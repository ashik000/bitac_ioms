<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\ProductionLog;
use App\Data\Models\Scrap;
use App\Data\Models\SlowProduction;
use App\Data\Models\StationOperator;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ProductRepository;
use App\Data\Repositories\ScrapRepository;
use App\Data\Repositories\ShiftRepository;
use App\Http\Resources\LineViewGraphResource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineViewController extends Controller
{
    public function lineviewData(Request $request, ProductRepository $productsRepository, ShiftRepository $shiftRepository, ProductionLogRepository $productionLogRepository, ScrapRepository $scrapRepository)
    {
        $stationId = $request->input('station_id');
        $shiftId   = $request->input('shift_id');
        $date      = $request->input('date');

        $dateX = $date;

        $date = Carbon::parse($date)->toImmutable();

        $products                      = $productsRepository->findProductsOfStation($stationId);
        $productIdToStationProductsMap = $productsRepository->findAllStationProductsOfAStationKeyByProductId($stationId);

        if (isset($shiftId) && isset($stationId))
        {
            $shiftDetails = $shiftRepository->fetchShiftDetails($stationId, $shiftId);
        }

        $start_time = isset($shiftDetails[0]->start_time) ? $dateX . ' ' . $shiftDetails[0]->start_time : $date->startOfDay();
        $end_time   = isset($shiftDetails[0]->end_time) ? $dateX . ' ' . $shiftDetails[0]->end_time : $date->endOfDay();

        $productionLogs = $productionLogRepository->fetchProductionLogs([
            'station_id' => $stationId,
//            'between'    => [
            //                'start' => $date->startOfDay(),
            //                'end'   => $date->endOfDay(),
            //            ],
            'between'    => [
                'start' => $start_time,
                'end'   => $end_time
            ]
        ])->groupBy(function (ProductionLog $log)
        {
            return Carbon::parse($log->produced_at)->hour;
        });

        $downtimes = $productionLogRepository->fetchDowntimes([
            'station_id' => $stationId,
            'between'    => [
                'start' => $start_time,
                'end'   => $end_time
            ]
        ])->load(['reason.downtimeReasonGroup'])
            ->groupBy(function (Downtime $log)
        {
                return Carbon::parse($log->start_time)->hour;
            });

        $slowLogs = $productionLogRepository->fetchSlowLogs([
            'station_id' => $stationId,
            'between'    => [
                'start' => $start_time,
                'end'   => $end_time
            ]
        ])->groupBy(function (SlowProduction $log)
        {
            return Carbon::parse($log->start_time)->hour;
        });

        $scraps = $scrapRepository->fetchScrapsOfADate($stationId, $date)->groupBy('hour');

        return new LineViewGraphResource($products, $productionLogs, $downtimes, $slowLogs, $scraps, $productIdToStationProductsMap);
    }

    public function getLineViewStationShifts(ShiftRepository $shiftRepository)
    {
        return $shiftRepository->findShiftsOfStation();
    }

    public function topDowntimeReasons(Request $request)
    {
        $start = CarbonImmutable::parse($request->get('start'));
        $end   = CarbonImmutable::parse($request->get('end'));

        $start = date('Y-m-d', strtotime($start));
        $end   = date('Y-m-d', strtotime($end));

        $query = Downtime::query();
        $query->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
            ->leftJoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
            ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
            ->whereBetween('downtimes.start_time', [
                $start,
                $end
            ])
            ->groupBy([DB::raw('DATE(downtimes.start_time)'), 'downtime_reasons.type', 'downtimes.reason_id', 'downtime_reasons.name'])
            ->orderBy(DB::raw('SUM(downtimes.duration)'), 'DESC')
            ->select([
                'reason_id',
                DB::raw('downtime_reasons.name as reason_name'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(downtimes.duration) as duration'),
                DB::raw('DATE(downtimes.start_time) as date')
            ]);

        $result = $query->take(5)->get();

        foreach ($result as &$row)
        {
            $seconds         = $row['duration'];
            $minutes         = floor($seconds / 60);
            $hours           = floor($minutes / 60) . 'hrs ' . ($minutes - floor($minutes / 60) * 60) . 'mins';
            $row['duration'] = $hours;
            if ($row['reason_id'] == null)
            {
                $row['reason_name'] = 'N/A';
                $row['reason_id']   = 0;
            }
        }

        return $result;
    }

    public function topOperatorDowntimes(Request $request)
    {
        $start = CarbonImmutable::parse($request->get('start'));
        $end   = CarbonImmutable::parse($request->get('end'));

        $start = date('Y-m-d', strtotime($start));
        $end   = date('Y-m-d', strtotime($end));

        //    $start = '2021-09-05';
        //    $end = '2021-09-11';

        $result = Downtime::query()
            ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
            ->leftJoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
            ->join('operators', 'operators.id', '=', 'downtimes.operator_id')
            ->whereBetween('downtimes.start_time', [$start, $end])
            ->groupBy('operator_id', 'station_id', 'station_group_id', 'stations.name', 'station_groups.name', 'operators.first_name', 'operators.last_name')
            ->orderBy(DB::raw('SUM(downtimes.duration)'), 'DESC')
            ->select([
                'station_id',
                'operator_id',
                'station_group_id',
                DB::raw('stations.name as station_name'),
                DB::raw('station_groups.name as station_group_name'),
                DB::raw('operators.first_name as operator_first_name'),
                DB::raw('operators.last_name as operator_last_name'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(duration) as duration')
            ])->take(5)->get();
        $totalSum = $result->sum('duration');
        foreach ($result as &$row)
        {
            $row['stop_percent']  = $row['duration'] / $totalSum;
            $row['operator_name'] = $row['operator_first_name'] . ' ' . $row['operator_last_name'];
            $seconds              = $row['duration'];
            $minutes              = floor($seconds / 60);
            $hours                = floor($minutes / 60) . 'hrs ' . ($minutes - floor($minutes / 60) * 60) . 'mins';
            $row['duration']      = $hours;
            unset($row['operator_first_name']);
            unset($row['operator_last_name']);
        }
        return $result;
    }

    public function getOperatorName(Request $request)
    {
        $stationId = $request->get('stationId');
        $date      = $request->get('date');

        $checkDate = Carbon::parse($date)->format('Y-m-d H:i:s');

        $result = StationOperator::query()
            ->leftJoin('stations', 'stations.id', '=', 'station_operators.station_id')
            ->leftJoin('operators', 'operators.id', '=', 'station_operators.operator_id')
            ->where([
                ['station_operators.station_id', '=', $stationId],
                ['station_operators.start_time', '<=', $checkDate]
                // ['station_operators.end_time', '>=', $checkDate]
            ])
            ->select([
                DB::raw('operators.id as operator_id'),
                DB::raw('operators.first_name as first_name'),
                DB::raw('operators.last_name as last_name')
            ])->distinct()->get();

        if (count($result) > 0)
        {
            return [
                'operatorId'   => $result[0]->operator_id,
                'operatorName' => $result[0]->first_name . ' ' . $result[0]->last_name
            ];
        }
        else
        {
            return [
                'operatorId'   => null,
                'operatorName' => 'N/A'
            ];
        }
    }

    public function storeLineviewDefects(Request $request)
    {
        $scrap = Scrap::query()
            ->where('scraps.station_id', '=', $request['stationId'])
            ->where('scraps.product_id', '=', $request['productId'])
            ->where('scraps.date', '=', $request['date'])
            ->where('scraps.hour', '=', $request['defectTime'])->first();

        if (empty($scrap))
        {
            $scrap = new Scrap();

            $scrap['value']       = $request['defectValue'];
            $scrap['date']        = $request['date'];
            $scrap['hour']        = $request['defectTime'];
            $scrap['station_id']  = $request['stationId'];
            $scrap['shift_id']    = $request['stationShiftId'];
            $scrap['product_id']  = $request['productId'];
            $scrap['operator_id'] = 1;
            $scrap['created_by']  = 1;

        }
        else
        {
            $scrap->value += $scrap['value'];
        }

        $check = $scrap->save();
        if ($check)
        {
            return true;
        }
        return false;
    }
}
