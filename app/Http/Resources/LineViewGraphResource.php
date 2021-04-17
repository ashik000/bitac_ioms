<?php

namespace App\Http\Resources;

use App\Data\Models\Product;
use App\Data\Models\StationProduct;
use function foo\func;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Log;

class LineViewGraphResource extends JsonResource
{
    /**
     * @var Collection
     */
    protected $products;

    /**
     * @var Collection
     */
    protected $logs;

    /**
     * @var Collection
     */
    protected $downtimes;

    /**
     * @var Collection
     */
    protected $slows;

    /**
     * @var Collection
     */
    protected $scraps;

    /**
     * @var Collection
     */
    protected $productIdToStationProductsMap;

    public function __construct( $products, $logs, $downtimes, $slows, $scraps, $productIdToStationProductsMap)
    {
        $this->products                      = $products;
        $this->logs                          = $logs;
        $this->downtimes                     = $downtimes;
        $this->slows                         = $slows;
        $this->scraps                        = $scraps;
        $this->productIdToStationProductsMap = $productIdToStationProductsMap;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $productProductionMap = $this->products->mapWithKeys(function (Product $product) {
            return [
                $product->id => [
                    'product_name' => $product->name,
                    'scrapped'     => 0,
                    'produced'     => 0,
                ],
            ];
        })->toArray();


        $hours = $this->logs->keys()->merge($this->downtimes->keys())->merge($this->slows->keys());

        $minHour = $hours->min();
        $maxHour = $hours->max();

        $formattedLineView = collect(range($maxHour, $minHour, -1))->map(function ($hour) use (&$productProductionMap) {

            /**
             * @var $productionLogs Collection
             * @var $slowLogs Collection
             * @var $downtimes Collection
             */
            $productionLogs = $this->logs->get($hour, collect());
            $slowLogs  = $this->slows->get($hour, collect());
            $downtimes = $this->downtimes->get($hour, collect());

            $totalStopTime    = $downtimes->sum('duration');
            $plannedStopTime = $downtimes->filter(function($val, $key){
                return isset($val['reason']) && $val->reason->type==='planned';
            })->sum('duration');
            $unplannedStopTime = $totalStopTime - $plannedStopTime;

//            $slowTimes = $slowLogs->groupBy('product_id')
//                                  ->map(function ($items) {
//                                      return $items->sum('duration');
//                                  });

            $scrapsOfThisHour = $this->scraps->get($hour, collect());
            $scrapsOfThisHourGroupedByProductId = $scrapsOfThisHour->groupBy('product_id');
            $scrapCountOfThisHour = 0;
            foreach ($scrapsOfThisHourGroupedByProductId as $productId => $scrap) {
                $productProductionMap[$productId]['scrapped'] += $scrap[0]->value;
                $scrapCountOfThisHour += $scrap[0]->value;
            }

            $productionLogGroupedByProductId = $productionLogs->groupBy('product_id');

            $nominalTimesByProductId = $productionLogGroupedByProductId->map(function ($logs, $productId) {
                /** @var StationProduct $stationProduct */
                $stationProduct = $this->productIdToStationProductsMap->get($productId);
                return $logs->sum(function ($log) use (&$stationProduct) {
                    return $log->cycle_interval >= ($stationProduct->cycle_time + $stationProduct->cycle_timeout) ? ($stationProduct->cycle_time + $stationProduct->cycle_timeout) : $log->cycle_interval;
                });
            });

            $producedByProductId = $productionLogGroupedByProductId->map(function ($logs) {
                return $logs->sum('units_per_signal');
            });


            foreach ($producedByProductId as $productId => $production) {
                $productProductionMap[$productId]['produced'] += $production;
            }

            $producedMap = $this->products->map(function (Product $product) use ($nominalTimesByProductId, $unplannedStopTime) {
                $nominal = $nominalTimesByProductId->get($product->id, 0);
//                $slow    = $slowTimes->get($product->id, 0);

                $total = $nominal;

                $expected = floor(($total + $unplannedStopTime) / $product->meta->cycle_time);
                return [
                    'product'        => $product,
                    'available_time' => $total,
                    'expected'       => $expected,
                    'performance'    => $product->meta->performance_threshold,
                ];
            });

            ['available_time' => $totalAvailableTime, 'total_expected' => $totalExpected, 'threshold' => $threshold]
                = $producedMap->reduce(function ($carry, $current) {
                return [
                    'available_time' => $carry['available_time'] + $current['available_time'],
                    'total_expected' => $carry['total_expected'] + $current['expected'],
                    'threshold'      => $carry['threshold'] + ($current['performance'] * $current['available_time']),
                ];
            }, [
                'available_time' => 0,
                'total_expected' => 0,
                'threshold'      => 0,
            ]);

//            $threshold /= $totalAvailableTime;
            $threshold = 0;

            return [
                'hour'                  => $hour,
                'available_time'        => $totalAvailableTime,
                'produced'              => $productionLogs->sum('units_per_signal'),
                'expected'              => $totalExpected,
                'performance_threshold' => $threshold,
                'scrapped'              => $scrapCountOfThisHour,
                'data'                  => collect()
                    ->merge(SlowProductionLineData::collection($slowLogs))
                    ->merge(DowntimeLineData::collection($downtimes))
                    ->merge(ProductionLogLineData::collection($productionLogs))
                    ->toArray(),
            ];
        });
        return [
            'products' => $productProductionMap,
            'logs'     => $formattedLineView->toArray(),
        ];
    }
}
