<?php

namespace App\Http\Controllers;

use App\Data\Models\ProductionLog;
use App\Data\Models\Report;
use App\Data\Models\Downtime;
use App\Data\Models\Scrap;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\ReportRepository;
use App\Http\Requests\HourlyProductionFetchRequest;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Log;

class ReportController extends Controller
{
    /**
     * @var ReportRepository
     */
    private $reportRepository;

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
}
