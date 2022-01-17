<?php

namespace App\Http\Controllers;

use App\Data\Models\ProductionLog;
use App\Data\Models\Report;
use App\Data\Models\Downtime;
use App\Data\Models\Scrap;
use App\Data\Models\Shift;
use App\Data\Models\Station;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ReportRepository;
use App\Data\Repositories\ScrapRepository;
use App\Exports\ExcelDataExport;
use App\Http\Requests\HourlyProductionFetchRequest;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Log;
use Excel;
class ReportController extends Controller

    {

    /**
     * @var ReportRepository
     */
    private $reportRepository;

    private $productionLogRepository;
    private $scrapRepository;

    private $headers_with_duration = ['Time Duration', "Availability", "Quality", "Performance", "OEE"];
    private $headers_without_criteria = ['Station Name', 'Station Group', "Availability", "Quality", "Performance", "OEE"];

    public function __construct(ReportRepository $reportRepository,
                                ProductionLogRepository $productionLogRepository, ScrapRepository $scrapRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->productionLogRepository = $productionLogRepository;
        $this->scrapRepository = $scrapRepository;
    }

    public function index(Request $request)
    {
        $type  = $request->get('type');
        $start = $request->get('start');
        $end   = $request->get('end');

        $start = CarbonImmutable::parse($start);
        $end   = CarbonImmutable::parse($end);

        $stationId = @$request->get('station_id');
        $productId = null;
        $shiftId = null;
        $operatorId = null;
        $stationProductId = @$request->get('station_product_id');
        $stationOperatorId = @$request->get('station_operator_id');
        $stationShiftId = @$request->get('station_shift_id');

        $reportType = @$request->get('report_type');

        if (!empty($stationProductId)) {
            $stationProduct = StationProduct::find($stationProductId);
            $stationId = $stationProduct->station_id;
            $productId = $stationProduct->product_id;
        }

        if (!empty($stationOperatorId)) {
            $stationOperator = StationOperator::find($stationOperatorId);
            $stationId = $stationOperator->station_id;
            $operatorId = $stationOperator->operator_id;
        }

        if (!empty($stationShiftId)) {
            $stationShift = StationShift::find($stationShiftId);
            $stationId = $stationShift->station_id;
            $shiftId = $stationShift->shift_id;
        }

//        $operatorId = $request->get('operator_id');
//        $shiftId   = $request->get('shift_id');

        $query = Report::where('tag', $type);

        $groupedBy     = ['generated_at'];
        $availableBase = 3600;

        $title = '';
        switch ($type) {
            case 'hourly':
                $title = 'Hourly report';
                $query->whereBetween('generated_at', [$start->startOfDay(), $end->endOfDay()]);
                break;

            case 'daily':
                $title         = 'Daily report';
                $availableBase = 24 * 3600;
                $query->whereBetween('generated_at', [$start->startOfDay(), $end->endOfDay()]);
                break;

            case 'weekly':
                $title         = 'Weekly Report';
                $availableBase = 24 * 7 * 3600;
                $query->whereBetween('generated_at', [$start->startOfWeek(Carbon::SATURDAY), $end->endOfWeek(Carbon::FRIDAY)]);
                break;

            case 'monthly':
                $title         = 'Monthly Report';
                $availableBase = -1;
//                $availableBase = Carbon::parse($end)->endOfMonth()->diffInSeconds(Carbon::parse($start)->startOfMonth());
                $query->whereBetween('generated_at', [$start->startOfMonth(), $end->endOfMonth()]);
                break;
        }

        $query->when($stationId, function ($q, $stationId) use (&$groupedBy) {
            $groupedBy[] = 'station_id';
            return $q->where('station_id', $stationId);
        });

        $query->when($productId, function ($q, $productId) use (&$groupedBy) {
            $groupedBy[] = 'product_id';

            return $q->where('product_id', $productId);
        });

        $query->when($shiftId, function ($q, $shiftId) use (&$groupedBy) {
            $groupedBy[] = 'shift_id';
            return $q->where('shift_id', $shiftId);
        });

        $query->when($operatorId, function ($q, $operatorId) use (&$groupedBy) {
            $groupedBy[] = 'operator_id';
            return $q->where('operator_id', $operatorId);
        });

        $query->groupBy($groupedBy);
        $query->orderBy('generated_at');
        $query->select([
            'generated_at',
            DB::raw('SUM(produced) as produced'),
            DB::raw('SUM(scraped) as scraped'),
            DB::raw('SUM(expected) as expected'),
            DB::raw('SUM(available) as available'),
            DB::raw('SUM(planned_downtime) as planned_downtime'),
        ]);

        $reports = $query->get();

        $reports = $reports->reduce(function ($carry, $item) use ($availableBase, $type, $reportType) {

            if ($availableBase == -1)
            {
                $availableBase = Carbon::parse($item->generated_at)->endOfMonth()->diffInSeconds(Carbon::parse($item->generated_at)->startOfMonth());
            }

            $performance  = $item->produced ? ($item->produced / $item->expected) : 0;
//            $quality      = 1; //$item->produced ? ($item->scrapped / $item->produced) : 0;
            $quality      = $item->produced ? (($item->produced-$item->scraped) / $item->produced) : 0;

            if ($reportType == 'oee')
            {
                $availability = $item->available ? ($item->available / ($availableBase - $item->planned_downtime)) : 0;
            }
            else
            {
                $availability = $item->available ? ($item->available / ($availableBase)) : 0;
            }

            $carry['produced'][]         =(int) $item->produced;
            $carry['scraped'][]          =(int) $item->scraped;
            $carry['expected'][]         =(int) $item->expected;
            $carry['available'][]        =(int) $item->available;
            $carry['planned_downtime'][] =(int) $item->planned_downtime;


            $carry['labels'][]       = $this->getLabel($item->generated_at, $type);
            $carry['generated_at'][] = $item->generated_at;
            $carry['performance'][]  = $performance * 100;
            $carry['quality'][]      = $quality * 100;
            $carry['availability'][] = $availability * 100;
            $carry['oee'][]          = $performance * $quality * $availability * 100;

            return $carry;
        }, [
            'labels'       => [],
            'generated_at' => [],
            'performance'  => [],
            'quality'      => [],
            'availability' => [],
            'oee'          => [],
        ]);

        return response()->json(
            [
                'title'   => $title,
                'dataset' => $reports,
            ]
        );
    }

    private function getLabel($generated_at, $type)
    {
        return $this->reportRepository->getLabel($generated_at, $type);
    }

    public function getDowntimeReport(Request $request)
    {
        $downtimeResult = $this->reportRepository->getDowntimeReportv2($request);
        return response()->json([
            'title' => 'Downtime Report',
            'dataset' => $downtimeResult
        ], 200);
    }

    public function getHourlyProducedAndScrapedCountOfADay(HourlyProductionFetchRequest $request)
    {
        return $this->reportRepository->getHourlyProducedAndScrapedCountOfADay($request);
    }

    public function getOEETableReportByStation(Request $request)
    {
        return $this->reportRepository->getOEETableReportByStation($request);
    }



    public function getOEETableReportByStationProduct(Request $request)
    {
        return $this->reportRepository->getOEETableReportByStationProduct($request);
    }



    public function getOEETableReportByStationShift(Request $request)
    {
        return $this->reportRepository->getOEETableReportByStationShift($request);
    }


    public function getOEETableReportByStationOperator(Request $request)
    {
        return $this->reportRepository->getOEETableReportByStationOperator($request);
    }


    public function getOEETableReportByStationTeam(Request $request)
    {
        return $this->reportRepository->getOEETableReportByStationTeam($request);
    }

    public function getOEETableReportByStationExcel(Request $request)
    {
        $data = $this->reportRepository->getOEETableReportByStation($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationId', []);
        $headers = $this->getHeaders($request, 'stationId', []);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Station Report.xlsx');
    }

    public function getOEETableReportByStationProductExcel(Request $request)
    {
        $data = $this->reportRepository->getOEETableReportByStationProduct($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationProductId', ['product_name','product_group_name']);
        $headers = $this->getHeaders($request, 'stationProductId', ['Product', 'Product Group']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Product Report.xlsx');
    }

    public function getOEETableReportByStationShiftExcel(Request $request)
    {
        $data =  $this->reportRepository->getOEETableReportByStationShift($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationShiftId', ['shift_name']);
        $headers = $this->getHeaders($request, 'stationShiftId', ['Shift']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Shift Report.xlsx');

    }

    public function getOEETableReportByStationOperatorExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = $this->reportRepository->getOEETableReportByStationOperator($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationOperatorId',['operator_name']);
        $headers = $this->getHeaders($request, 'stationOperatorId', ['Operator']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Operator Report.xlsx');
    }


    public function getOEETableReportByStationTeamExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $data = $this->reportRepository->getOEETableReportByStationTeam($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationTeamId',['name']);
        $headers = $this->getHeaders($request, 'stationTeamId', ['Team']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Team Report.xlsx');
    }

    public function getHeaders(Request $request, $key_to_check_for, $items_to_add): array
    {
        if($request->get($key_to_check_for)){
            $headers = $this->headers_with_duration;
        }else{
            $headers = $this->headers_without_criteria;
            foreach (array_reverse($items_to_add) as $item){
                array_unshift($headers, $item);
            }
        }
        return $headers;
    }

    public function getFormattedDataForExcel($data,Request $request, $keyToCheckFor, $keysToSelect): array
    {
        $excel_data = [];
        Log::debug($request->get($keyToCheckFor));
        if($request->get($keyToCheckFor)){
            foreach ($data as $datum){
                $excel_data[] = [
                    "time_duration" => $datum['time_duration'],
                    "availability"  => number_format($datum['availability'] * 100, 2) . "%",
                    "quality"       => number_format($datum['quality'] * 100, 2) . "%",
                    "performance"   => number_format($datum['performance'] * 100, 2) . "%",
                    "oee"           => number_format($datum['oee'] * 100, 2) . "%",
                ];
            }
        }else{
            foreach ($data as $datum){
                $row = [];
                foreach ($keysToSelect as $key){
                    $row[$key] = $datum[$key];
                }
                $row["station_name"]       = $datum['station_name'];
                $row["station_group_name"] = $datum['station_group_name'];
                $row["availability"]       = number_format($datum['availability'] * 100, 2) . "%";
                $row["quality"]            = number_format($datum['quality'] * 100, 2) . "%";
                $row["performance"]        = number_format($datum['performance'] * 100, 2) . "%";
                $row["oee"]                = number_format($datum['oee'] * 100, 2) . "%";
                $excel_data[] = $row;
            }
        }
        return $excel_data;
    }

    public function scada(Request $request)
    {
        $filter = $request->filter ?? "last_hour";

        switch ($filter) {
            case "last_hour":
                $start_of_day = now()->modify('-1 hour');
                $end_of_day = now();
                break;
            case "current_shift":
                $time = now();
                $shifts = Shift::all();
                $start_of_day = null;
                $end_of_day = null;
                foreach ($shifts as $shift){
                    $shift_start_time = Carbon::parse($shift->start_time);
                    $shift_end_time   = Carbon::parse($shift->end_time);
                    if ($time->between($shift_start_time, $shift_end_time)) {
                        $start_of_day = $shift_start_time;
                        $end_of_day   = $shift_end_time;
                        break;
                    }
                }
                break;
            case "last_shift":
                echo "Your favorite color is green!";
                break;
            case "last_month":
                $start_of_day = now()->modify('-1 day')->startOfDay();
                $end_of_day = now()->modify('-31 day')->endOfDay();
                break;
            case "custom":
                $data = $request->all();
                $date_range = json_decode($data['date_range'], true);
                $start_of_day = $date_range['start'];
                $end_of_day = $date_range['end'];
                break;
            default:
                $start_of_day = now()->startOfDay();
                $end_of_day = now()->endOfDay();
        }

        $station_ids = Station::all()->pluck('id');
        $reports = Report::whereIn('station_id', $station_ids)
            ->whereBetween('generated_at', [$start_of_day, $end_of_day])
            ->groupBy('station_id')
            ->select([
                'station_id',
                DB::raw('SUM(produced) as produced'),
                DB::raw('SUM(scraped) as scraped'),
                DB::raw('SUM(expected) as expected'),
                DB::raw('SUM(available) as available'),
                DB::raw('SUM(planned_downtime) as planned_downtime'),
            ])->get()->keyBy('station_id');
//        $available_base = now()->diffInSeconds(now()->startOf('day'));
        $available_base = 24*3600;
        $reports = $station_ids->reduce(function ($carry, $stationId) use ($available_base, $reports) {
            $row = [];
            $item = $reports->get($stationId);
            if (empty($item)) return $carry;
//            error_log(print_r($item, true));
            $row['performance'] = $item['produced'] ? ($item['produced'] / $item['expected']) : 0;
            $row['quality'] = $item['produced'] ? (($item['produced'] - $item['scraped']) / $item['produced']) : 0;
            $row['availability'] = $item['available'] ? ($item['available'] / ($available_base - $item['planned_downtime'])) : 0;
            $row['oee'] = $row['performance'] * $row['quality'] * $row['availability'] * 100;
            $row['performance'] = number_format($row['performance'] * 100, 2);
            $row['availability'] = number_format($row['availability'] * 100, 2);
            $row['quality'] = number_format($row['quality'] * 100, 2);
            $row['oee'] = number_format($row['oee'], 2);
            $row['station_id'] = $stationId;
            $carry[$stationId] = $row;
            return $carry;
        }, []);
        $reports = collect($reports)->sortBy(function ($value, $key) {
            return $key;
        });
        return $reports;
    }

    public function getDashboardSummary(Request $request)
    {
        $startTime = now()->startOfHour();
        $endTime = now()->endOfHour();

        $allStations = Station::with('products')->get();
        $allStationIds = $allStations->pluck('id');
        $stationsById = $allStations->keyBy('id');

        $allDowntimes = $this->productionLogRepository->fetchDowntimes([
            'between'    => [
                'start' => $startTime,
                'end'   => $endTime,
            ],
        ])->groupBy(function ($log) {
                return $log->station_id;
            });

        $allProductionLogs = $this->productionLogRepository->fetchProductionLogs([
            'between'    => [
                'start' => $startTime,
                'end'   => $endTime,
            ],
        ])->groupBy(function ($log) {
            return $log->station_id;
        });


        $allScraps = $this->scrapRepository->fetchScrapsBetweenADateRangeOfAllStations($startTime->toImmutable(), $endTime->toImmutable())->groupBy('station_id');

        $reports = $allStationIds->reduce(function ($carry, $stationId) use($allProductionLogs, $allDowntimes, $allScraps, $stationsById) {
            $productionLogs = $allProductionLogs->get($stationId);
            $station = $stationsById->get($stationId);

            $downtimes = $allDowntimes->get($stationId);

            $nominalTime = empty($productionLogs)? 0 : $productionLogs->sum(function ($log) {
                return $log->cycle_interval >= ($log->cycle_time + $log->cycle_timeout) ? ($log->cycle_time + $log->cycle_timeout) : $log->cycle_interval;
            });
            $totalStopTime    = empty($downtimes)? 0 : $downtimes->sum('duration');
            $plannedStopTime = empty($downtimes)? 0 : $downtimes->filter(function($val, $key){
                return isset($val['reason']) && $val->reason->type==='planned';
            })->sum('duration');
            $unplannedStopTime = $totalStopTime - $plannedStopTime;

            $expected = empty($productionLogs)? 0 : floor(($nominalTime  + $unplannedStopTime)/ $productionLogs[0]->cycle_time);
            $produced = empty($productionLogs)? 0 : $productionLogs->sum('units_per_signal');

            $producedTillNow = 0;
            $logsPerMinute = empty($productionLogs)? [] : $productionLogs->groupBy(function ($log) {
                return Carbon::parse($log->produced_at)->minute;
            });
            $logsPerMinute = collect($logsPerMinute);
            $speed = collect(range(0, 59))->reduce(function ($carry, $minute) use($producedTillNow, $logsPerMinute) {
                $logs = $logsPerMinute->get($minute) ?? null;
                $producedTillNow = empty($logs)? $producedTillNow : $producedTillNow + $logs->sum('units_per_signal');
                $carry[$minute] = $minute == 0? $producedTillNow : $producedTillNow/$minute;
                return $carry;
            }, []);

            $scraps   = $allScraps->get($stationId, collect());

            $scraped = $scraps->sum('value');

            $performance  = $produced ? ($produced / $expected) : 0;
            $quality      = $produced ? (($produced - $scraped) / $produced) : 0;
            $availability = $nominalTime ? ($nominalTime / (3600 - $plannedStopTime)) : 0;
            $oeeNumber = $performance * $quality * $availability * 100;

            $currentProduct = $station->products->sortByDesc('start_time')->first();

            $carry[$stationId] = [
                'stationName'  => $station->name,
                'productName'  => empty($currentProduct)? '' : $currentProduct->name,
                'stationId'    => $stationId,
                'speed'        => $speed,
                'labels'       => range(0, 59),
                'performance'  => number_format($performance * 100, 0),
                'availability' => number_format($availability * 100, 2),
                'quality'      => number_format($quality * 100, 2),
                'oee'          => number_format($oeeNumber, 0),
                'color'        => empty($productionLogs)? 'black' : ($oeeNumber < $station->oee_threshold? 'red' : 'green')
            ];

            return $carry;
        }, []);

        return response()->json($reports, 200);
    }

    public function getSixSigmaQuality(Request $request)
    {
        $data = $request->all();
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        $allStations = Station::all();
        $allStationIds = $allStations->pluck('id');
        $stationsById = $allStations->keyBy('id');


        $allProductionLogs = $this->productionLogRepository->fetchProductionLogs([
            'between'    => [
                'start' => $startTime,
                'end'   => $endTime,
            ],
        ])->groupBy(function ($log) {
            return $log->station_id;
        });


        $allScraps = $this->scrapRepository->fetchScrapsBetweenADateRangeOfAllStations($startTime->toImmutable(), $endTime->toImmutable())->groupBy('station_id');

        $reports = $allStationIds->reduce(function ($carry, $stationId) use($allProductionLogs, $allScraps, $stationsById) {
            $productionLogs = $allProductionLogs->get($stationId);
            $station = $stationsById->get($stationId);



            $produced = empty($productionLogs)? 0 : $productionLogs->sum('units_per_signal');

            $scraps   = $allScraps->get($stationId, collect());

            $scraped = $scraps->sum('value');

            $quality      = $produced ? (($produced - $scraped) / $produced) : 0;


            $carry[$stationId] = [
                'stationName'  => $station->name,
                'stationId'    => $stationId,
                'quality'      => $quality * 100,
            ];

            return $carry;
        }, []);

        return response()->json($reports, 200);
    }
}
