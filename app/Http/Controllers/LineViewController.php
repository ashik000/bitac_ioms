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
use Illuminate\Http\Request;

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
}
