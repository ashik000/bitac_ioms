<?php

namespace App\Http\Controllers;

use App\Data\Models\ProductionLog;
use App\Data\Models\Report;
use App\Data\Models\Downtime;
use App\Data\Models\Scrap;
use App\Data\Models\Station;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\ReportRepository;
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

    private $headers_with_duration = ['Time Duration', "Availability", "Quality", "Performance", "OEE"];
    private $headers_without_criteria = ['Station Name', 'Station Group', "Availability", "Quality", "Performance", "OEE"];

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
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

        $reports = $reports->reduce(function ($carry, $item) use ($availableBase, $type) {

            if ($availableBase == -1)
            {
                $availableBase = Carbon::parse($item->generated_at)->endOfMonth()->diffInSeconds(Carbon::parse($item->generated_at)->startOfMonth());
            }

            $performance  = $item->produced ? ($item->produced / $item->expected) : 0;
//            $quality      = 1; //$item->produced ? ($item->scrapped / $item->produced) : 0;
            $quality      = $item->produced ? (($item->produced-$item->scraped) / $item->produced) : 0;
            $availability = $item->available ? ($item->available / ($availableBase - $item->planned_downtime)) : 0;

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
        $start_of_day = Carbon::now()->startOf('day');
        $end_of_day = Carbon::now()->endOf('day');
        $station_ids = Station::all()->pluck('id');
        $reports = Report::whereIn('station_id', $station_ids)
            ->whereBetween('generated_at', [$start_of_day, $end_of_day])
            ->groupBy('station_id')
            ->select([
                DB::raw('SUM(produced) as produced'),
                DB::raw('SUM(scraped) as scraped'),
                DB::raw('SUM(expected) as expected'),
                DB::raw('SUM(available) as available'),
                DB::raw('SUM(planned_downtime) as planned_downtime'),
            ])->get();
        $available_base = now()->diffInSeconds(now()->startOf('day'));
        $reports = $reports->reduce(function ($carry, $item) use ($available_base) {
            $row = [];
            $row['performance'] = $item->produced ? ($item->produced / $item->expected) : 0;
            $row['quality'] = $item->produced ? (($item->produced - $item->scraped) / $item->produced) : 0;
            $row['availability'] = $item->available ? ($item->available / ($available_base - $item->planned_downtime)) : 0;
            $row['oee'] = $row['performance'] * $row['quality'] * $row['availability'] * 100;
            $carry[$item->station_id] = $row;
            return $carry;
        }, []);
        return $reports;
    }
}
