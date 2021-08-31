<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\ProductionLog;
use App\Data\Models\Scrap;
use App\Data\Models\SlowProduction;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ProductRepository;
use App\Data\Repositories\ScrapRepository;
use App\Http\Resources\LineViewGraphResource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineViewController extends Controller
{
    public function lineviewData(Request $request, ProductRepository $productsRepository, ProductionLogRepository $productionLogRepository, ScrapRepository $scrapRepository)
    {
        $stationId = $request->input('station_id');
        $date      = $request->input('date');

        $date = Carbon::parse($date)->toImmutable();

        $products = $productsRepository->findProductsOfStation($stationId);
        $productIdToStationProductsMap = $productsRepository->findAllStationProductsOfAStationKeyByProductId($stationId);

        $productionLogs = $productionLogRepository->fetchProductionLogs([
            'station_id' => $stationId,
            'between'    => [
                'start' => $date->startOfDay(),
                'end'   => $date->endOfDay(),
            ],
        ])->groupBy(function (ProductionLog $log) {
            return Carbon::parse($log->produced_at)->hour;
        });

        $downtimes = $productionLogRepository->fetchDowntimes([
            'station_id' => $stationId,
            'between'    => [
                'start' => $date->startOfDay(),
                'end'   => $date->endOfDay(),
            ],
        ])->load(['reason.downtimeReasonGroup'])
         ->groupBy(function (Downtime $log) {
             return Carbon::parse($log->start_time)->hour;
         });

        $slowLogs = $productionLogRepository->fetchSlowLogs([
            'station_id' => $stationId,
            'between'    => [
                'start' => $date->startOfDay(),
                'end'   => $date->endOfDay(),
            ],
        ])->groupBy(function (SlowProduction $log) {
            return Carbon::parse($log->start_time)->hour;
        });

        $scraps = $scrapRepository->fetchScrapsOfADate($stationId, $date)->groupBy('hour');
        return new LineViewGraphResource($products, $productionLogs, $downtimes, $slowLogs, $scraps, $productIdToStationProductsMap);
    }

    public function topDowntimeReasons(Request $request)
    {
//        $start = CarbonImmutable::parse($request->get('start'));
//        $end = CarbonImmutable::parse($request->get('end'));

        $start = '2019-09-24';
        $end = '2019-09-30';

        $query = Downtime::query();
        $query->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
            ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
            ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
//            ->whereBetween('downtimes.start_time', [
//                $start->startOfDay(),
//                $end->endOfDay()
//            ])
            ->whereBetween('downtimes.start_time', [
                $start,
                $end
            ])
            ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','downtimes.reason_id','downtime_reasons.name'])
            ->orderBy(DB::raw('SUM(downtimes.duration)'), 'DESC')
            ->select([
                'reason_id',
                DB::raw('downtime_reasons.name as reason_name'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(downtimes.duration) as duration'),
                DB::raw('DATE(downtimes.start_time) as date'),
            ]);

        $result = $query->take(5)->get();

        foreach ($result as &$row) {
            $seconds = $row['duration'];
            $minutes = floor($seconds / 60);
            $hours = floor($minutes / 60).'hrs '.($minutes - floor($minutes / 60) * 60).'mins';
            $row['duration'] = $hours;
        }

        return $result;
    }

    public function topOperatorDowntimeReasons(Request $request)
    {
//        $start = CarbonImmutable::parse($request->get('start'));
//        $end = CarbonImmutable::parse($request->get('end'));

        $start = '2019-09-24';
        $end = '2019-09-30';

        $result = Downtime::query()
            ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
            ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
            ->join('operators', 'operators.id', '=', 'downtimes.operator_id')
//            ->whereBetween('downtimes.start_time', [$start->startOfDay(), $end->endOfDay()])
            ->whereBetween('downtimes.start_time', [$start, $end])
            ->groupBy('operator_id','station_id','station_group_id','stations.name','station_groups.name','operators.first_name','operators.last_name')
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
        foreach ($result as &$row) {
            $row['stop_percent'] = $row['duration'] / $totalSum;
            $row['operator_name'] = $row['operator_first_name'] . ' ' . $row['operator_last_name'];
//            $row['duration'] = CarbonInterval::seconds($row['duration'])->cascade()->forHumans();
            $seconds = $row['duration'];
            $minutes = floor($seconds / 60);
            $hours = floor($minutes / 60).'hrs '.($minutes - floor($minutes / 60) * 60).'mins';
            $row['duration'] = $hours;
            unset($row['operator_first_name']);
            unset($row['operator_last_name']);
        }
        return $result;
    }
}
