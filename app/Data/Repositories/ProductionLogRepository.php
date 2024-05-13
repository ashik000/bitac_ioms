<?php


namespace App\Data\Repositories;


use App\Data\Models\Downtime;
use App\Data\Models\ProductionLog;
use App\Data\Models\SlowProduction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class ProductionLogRepository
{

    public function findLastProductionLogByStationIdAndProductId(int $stationId, int $productId)
    {
        $log = ProductionLog::where('station_id', '=', $stationId)
                            ->where('product_id', '=', $productId)
                            ->orderBy('produced_at', 'desc')
                            ->first();

        return empty($log) ? null : $log;
    }

    public function findLastProductionLogByStationIdAndProductIdBeforeGivenTime(int $stationId, int $productId, Carbon $logTime)
    {
        $log = ProductionLog::where('station_id', '=', $stationId)
            ->where('product_id', '=', $productId)
            ->where('produced_at', '<=', $logTime)
            ->orderBy('produced_at', 'desc')
            ->first();

        return empty($log) ? null : $log;
    }
    public function findLatestProductionLogOfEachStation()
    {
        return ProductionLog::selectRaw('station_id, max(produced_at) as produced_at')
            ->groupBy('station_id')
            ->get()
            ->keyBy('station_id');
    }
    public function fetchDowntimeLogsOfStationBetween($stationId, $start, $end, $type)
    {
        $query = Downtime::join('production_logs', function (QueryBuilder $join) {
            $join->on('downtimes.production_log_id', 'production_logs.id');
        })->join('downtime_reasons', function (QueryBuilder $join) {
            $join->on('downtimes.reason_id', 'downtime_reasons.id');
        })->where('downtimes.start_time', '>=', $start->copy()->startOfHour())
                         ->where('downtimes.start_time', '<=', $end->copy()->endOfHour())
                         ->where('production_logs.station_id', '=', $stationId)
                         ->where('downtime_reasons.type', '=', $type)
                         ->select('downtimes.*', 'production_logs.product_id', 'downtime_reasons.*');

        return $query->get();
    }


    public function fetchProductionLogsOfStationBetween($stationId, $start, $end)
    {
        $query = ProductionLog::join('station_products', function (QueryBuilder $join) {
            $join->on('station_products.product_id', 'production_logs.product_id')
                 ->on('station_products.station_id', 'production_logs.station_id');
        })->select('production_logs.*', 'station_products.cycle_time', 'station_products.cycle_timeout', 'station_products.units_per_signal');

        $query->where('production_logs.produced_at', '>=', $start->copy()->startOfHour())
              ->where('production_logs.produced_at', '<=', $end->copy()->endOfHour());

        $query->when($stationId ?? null, function (EloquentBuilder $q, $stationId) {
            $q->where('production_logs.station_id', '=', $stationId);
        });

        return $query->get();
    }

    public function fetchSlowProductionLogsOfAStationBetweenHour($stationId, $start, $end)
    {
        $query = SlowProduction::join('production_logs', 'production_logs.id', '=', 'slow_productions.production_log_id')
                               ->select('slow_productions.*', 'production_logs.product_id');

        $query->where('slow_productions.start_time', '>=', $start->copy()->startOfHour())
              ->where('slow_productions.start_time', '<=', $end->copy()->endOfHour());;
        $query->where('production_logs.station_id', '=', $stationId);
        return $query->get();
    }

    public function fetchProductionLogs($filter = [])
    {
        $query = ProductionLog::join('station_products', function (QueryBuilder $join) {
            $join->on('station_products.product_id', 'production_logs.product_id')
                 ->on('station_products.station_id', 'production_logs.station_id');
        })->select('production_logs.*', 'station_products.cycle_time', 'station_products.cycle_timeout', 'station_products.units_per_signal');

        $query->when($filter['between'], function (EloquentBuilder $q, $between) {
            $q->where('production_logs.produced_at', '>=', $between['start'])
              ->where('production_logs.produced_at', '<=', $between['end']);
        });

        $query->when($filter['station_id'] ?? null, function (EloquentBuilder $q, $stationId) {
            $q->where('production_logs.station_id', '=', $stationId);
        });

        return $query->get();
    }


    public function fetchDowntimes($filter = [])
    {
        $query = Downtime::join('production_logs', 'production_logs.id', '=', 'downtimes.production_log_id')
                         ->select('downtimes.*', 'production_logs.station_id', 'production_logs.product_id');

        $query->when($filter['between'], function (EloquentBuilder $q, $between) {
            $q->where('downtimes.start_time', '>=', $between['start'])
              ->where('downtimes.start_time', '<=', $between['end']);
        });

        $query->when($filter['station_id'] ?? null, function (EloquentBuilder $q, $stationId) {
            $q->where('production_logs.station_id', '=', $stationId);
        });
        return $query->get()->load('reason.downtimeReasonGroup');
    }

    public function fetchSlowLogs($filter = [])
    {
        $query = SlowProduction::join('production_logs', 'production_logs.id', '=', 'slow_productions.production_log_id')
                               ->select('slow_productions.*', 'production_logs.station_id', 'production_logs.product_id');

        $query->when($filter['between'], function (EloquentBuilder $q, $between) {
            $q->where('slow_productions.start_time', '>=', $between['start'])
              ->where('slow_productions.start_time', '<=', $between['end']);
        });

        $query->when($filter['station_id'] ?? null, function (EloquentBuilder $q, $stationId) {
            $q->where('production_logs.station_id', '=', $stationId);
        });

        return $query->get();
    }

    public function fetchLastProductionLog()
    {
        return ProductionLog::orderBy('id', 'desc')->first();
    }

    public function fetchLastDowntime()
    {
        return Downtime::orderBy('id', 'desc')->first();
    }

    public function fetchLastSlowProduction()
    {
        return SlowProduction::orderBy('id', 'desc')->first();
    }

    public function saveNewProductionLog() {

    }

    public function fetchProductionLogCountOfHour($stationId, $productId, Carbon $time)
    {
        $query = ProductionLog::query();
        return $query->where('product_id', $productId)
                        ->where('station_id', $stationId)
                        ->whereBetween('produced_at', [$time->copy()->startOfHour(), $time->copy()->endOfHour()])
                        ->count();
    }
}
