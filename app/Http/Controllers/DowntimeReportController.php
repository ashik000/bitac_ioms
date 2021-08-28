<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\DowntimeReportRepository;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DowntimeReportController extends Controller
{
    /**
     * @var DowntimeReportRepository
     */
    private $downtimeReportRepository;

    public function __construct(DowntimeReportRepository $downtimeReportRepository)
    {
        $this->downtimeReportRepository = $downtimeReportRepository;
    }

    public function getDowntimeTableReportByStation(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStation($request);
    }

    public function getDowntimeTableReportByStationProduct(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationProduct($request);
    }

    public function getDowntimeTableReportByStationShift(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationShift($request);
    }

    public function getDowntimeTableReportByStationOperator(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationOperator($request);
    }

    public function testDowntimeReportByStation()
    {
        $stationId = 1;
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');

        if (is_null($stationId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return response()->json($result, 200);
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationId)
//                ->groupBy([DB::raw('DATE(downtimes.start_time)'), 'downtime_reasons.type'])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id', 'downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
//                if(is_null($row['reason_id'])){
//                    $row['name'] = 'Uncommented';
//                    $row['reason_group_name'] = ' - ';
//                    $row['type'] = ' - ';
//                }
//                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
//            return $result;
            return response()->json($result, 200);
        }

    }

    public function testDowntimeReportByStationProduct()
    {
        $stationProductId = null;
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');

        if (is_null($stationProductId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationProduct = StationProduct::find($stationProductId);
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationProduct->station_id)
                ->where('product_id', '=', $stationProduct->product_id)
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
//                if(is_null($row['reason_id'])){
//                    $row['name'] = 'Uncommented';
//                    $row['reason_group_name'] = ' - ';
//                    $row['type'] = ' - ';
//                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }

    }

    public function testDowntimeReportByStationShift()
    {
        $stationShiftId = null;
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');

        if (is_null($stationShiftId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','stations.station_group_id','stations.name','station_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationShift = StationShift::find($stationShiftId);
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationShift->station_id)
                ->where('shift_id', '=', $stationShift->shift_id)
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as reason_name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
//                    $row['name'] = 'Uncommented';
//                    $row['reason_group_name'] = ' - ';
//                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }

    public function testDowntimeReportByStationOperator()
    {
        $stationOperatorId = null;
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');

        if (is_null($stationOperatorId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->join('operators', 'operators.id', '=', 'downtimes.operator_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
//                ->groupBy('operator_id','station_id','station_group_id','stations.name','station_groups.name','operators.first_name','operators.last_name')
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','station_id','station_group_id','stations.name','station_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'station_id',
//                    'operator_id',
                    'station_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('station_groups.name as station_group_name'),
//                    DB::raw('CONCAT(operators.first_name, operators.last_name) as operator_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationOperator = StationOperator::find($stationOperatorId);
            if(is_null($stationOperator)) return [];
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationOperator->station_id)
                ->where('operator_id', '=', $stationOperator->operator_id)
//                ->groupBy(['downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as name'),
                    'reason_group_id',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
//                    $row['name'] = 'Uncommented';
//                    $row['reason_group_name'] = ' - ';
//                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }

    public function gettestDowntimeReport()
    {
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');
//        $start = CarbonImmutable::parse($request->get('start'));
//        $end = CarbonImmutable::parse($request->get('end'));

        $stationId = null;
//        $stationId = @$request->get('station_id');
        $productId = null;
        $shiftId = null;
        $operatorId = null;

        $stationOperatorStartTime = null;
        $stationOperatorEndTime = null;
//        $stationProductId = @$request->get('station_product_id');
//        $stationShiftId = @$request->get('station_shift_id');
//        $stationOperatorId = @$request->get('station_operator_id');
//
        $stationProductId = null;
        $stationShiftId = null;
        $stationOperatorId = null;

        if(!empty($stationProductId)){
            $stationProduct = StationProduct::find($stationProductId);
            $productId = $stationProduct->product_id;
            $stationId = $stationProduct->station_id;
        }
        if(!empty($stationShiftId)){
            $stationShift = StationShift::find($stationShiftId);
            $stationId = $stationShift->station_id;
            $shiftId = $stationShift->shift_id;
        }
        if(!empty($stationOperatorId)){
            $stationOperator = StationOperator::find($stationOperatorId);
            $operatorId = $stationOperator->operator_id;
            $stationId = $stationOperator->station_id;
            $stationOperatorStartTime = $stationOperator->start_time;
            $stationOperatorEndTime = $stationOperator->end_time;
        }

        $downtimeQuery = Downtime::query();
        $downtimeQuery->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
            ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
            ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
            ->when($stationId, function ($q,$sId) {
                $q->where( 'production_logs.station_id', '=', $sId);
            })
            ->when($productId, function ($q,$pId) {
                $q->where( 'production_logs.product_id', '=', $pId);
            })
            ->when($shiftId,function ($q,$shId){
                $q->where('downtimes.shift_id',$shId);
            })
            ->when($operatorId,function ($q,$opId){
                $q->where('downtimes.operator_id',$opId);
            })
            ->whereBetween('downtimes.start_time', [
                $start->startOfDay(),
                $end->endOfDay()
            ])
            ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name'])
            ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'ASC')
            ->select([
                'reason_id',
                DB::raw('downtime_reasons.name as reason_name'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(downtimes.duration) as duration'),
                DB::raw('DATE(downtimes.start_time) as date'),
                DB::raw('downtime_reasons.type as reason_type'),
            ]);


        $downtimeResult = $downtimeQuery->get()->reduce(function ($carry, $item) {
            if(empty($carry[$item->date])) $carry[$item->date] = [
                'planned_duration' => 0,
                'unplanned_duration' => 0,
                'reasons' => []
            ];
            $carry[$item->date]['planned_duration'] += $item->reason_type == 'planned' ? $item->duration : 0;
            $carry[$item->date]['unplanned_duration'] += $item->reason_type == 'unplanned' ? $item->duration : 0;
            $carry[$item->date]['reasons'][$item->reason_name] = $item->duration;
            return $carry;
        }, [

        ]);

        return $downtimeResult;
    }


}
