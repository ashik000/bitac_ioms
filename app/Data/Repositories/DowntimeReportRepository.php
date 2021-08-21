<?php

namespace App\Data\Repositories;

use App\Data\Models\Downtime;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DowntimeReportRepository
{
    public function getDowntimeTableReportByStation($request)
    {
        $stationId = $request->get('stationId');
        $start = CarbonImmutable::parse($request->get('start'));
        $end = CarbonImmutable::parse($request->get('end'));

        if (is_null($stationId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy(['station_id','station_group_id','station_name','stations.station_group_id','station_group_name'])
                ->select([
                    'station_id',
                    'station_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('stations.station_group_id as station_group_id'),
                    DB::raw('station_groups.name as station_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration')
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationId)
                ->groupBy(['downtimes.reason_id','downtime_reasons.name','reason_group_id','type','reason_group_name'])
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as name'),
                    'reason_group_id',
                    'type',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
                    $row['name'] = 'Uncommented';
                    $row['reason_group_name'] = ' - ';
                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }

    public function getDowntimeTableReportByStationProduct($request)
    {
        $stationProductId = $request->get('stationProductId');
        $start = CarbonImmutable::parse($request->get('start'));
        $end = CarbonImmutable::parse($request->get('end'));

        if (is_null($stationProductId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('products', 'products.id', '=', 'production_logs.product_id')
                ->leftjoin('product_groups', 'product_groups.id', '=', 'products.product_group_id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy(['product_id','station_id','station_group_id','product_group_id','station_groups.name','stations.name','products.name','product_groups.name'])
                ->select([
                    'station_id',
                    'product_id',
                    'station_group_id',
                    'product_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('station_groups.name as station_group_name'),
                    DB::raw('products.name as product_name'),
                    DB::raw('product_groups.name as product_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration')
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
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->leftJoin('downtime_reason_groups', 'downtime_reasons.reason_group_id', '=', 'downtime_reason_groups.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationProduct->station_id)
                ->where('product_id', '=', $stationProduct->product_id)
                ->groupBy(['downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as name'),
                    'reason_group_id',
                    'type',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
                    $row['name'] = 'Uncommented';
                    $row['reason_group_name'] = ' - ';
                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }

    public function getDowntimeTableReportByStationShift($request)
    {
        $stationShiftId = $request->get('stationShiftId');
        $start = CarbonImmutable::parse($request->get('start'));
        $end = CarbonImmutable::parse($request->get('end'));

        if (is_null($stationShiftId)) {
            $result = Downtime::query()
                ->join('shifts', 'shifts.id', '=', 'downtimes.shift_id')
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy('shift_id','station_id','stations.station_group_id','stations.name','station_groups.name','shifts.name')
                ->select([
                    'shift_id',
                    'station_id',
                    'station_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('station_groups.name as station_group_name'),
                    DB::raw('shifts.name as shift_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration')
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
                ->groupBy(['downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as name'),
                    'reason_group_id',
                    'type',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
                    $row['name'] = 'Uncommented';
                    $row['reason_group_name'] = ' - ';
                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }

    public function getDowntimeTableReportByStationOperator($request)
    {
        $stationOperatorId = $request->get('stationOperatorId');
        $start = CarbonImmutable::parse($request->get('start'));
        $end = CarbonImmutable::parse($request->get('end'));

        if (is_null($stationOperatorId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->join('operators', 'operators.id', '=', 'downtimes.operator_id')
                ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy('operator_id','station_id','station_group_id','stations.name','station_groups.name','operators.first_name','operators.last_name')
                ->select([
                    'station_id',
                    'operator_id',
                    'station_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('station_groups.name as station_group_name'),
                    DB::raw('CONCAT(operators.first_name, operators.last_name) as operator_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration')
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
                ->groupBy(['downtimes.reason_id','downtime_reasons.name','downtime_reasons.reason_group_id','downtime_reasons.type','downtime_reason_groups.name'])
                ->select([
                    'reason_id',
                    DB::raw('downtime_reasons.name as name'),
                    'reason_group_id',
                    'type',
                    DB::raw('downtime_reason_groups.name as reason_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                if(is_null($row['reason_id'])){
                    $row['name'] = 'Uncommented';
                    $row['reason_group_name'] = ' - ';
                    $row['type'] = ' - ';
                }
                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return $result;
        }
    }
}
