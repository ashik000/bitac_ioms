<?php


namespace App\Data\Repositories;


use App\Data\Models\Product;
use App\Data\Models\Station;
use App\Data\Models\StationProduct;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;

class ProductRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public function findProductsOfStation(int $stationId)
    {
        $station = Station::find($stationId);

        if (empty($station)) {
            return null;
        }

        return $station->products;
    }

    public function findStationProductAndProductByStationId(int $stationId)
    {
        $stationProduct = StationProduct::where('station_id', '=', $stationId)->first();
        if (empty($stationProduct)) return null;

        $product = Product::where('id', '=', $stationProduct->product_id)->first();

        return [$stationProduct, $product];
    }

    public function findAllStationProductsKeyByStationId() {
        return StationProduct::all()->keyBy('station_id');
    }

    public function findAllStationProductsOfAStationKeyByProductId($stationId) {
        return StationProduct::where('station_id', '=', $stationId)->get()->keyBy('product_id');
    }

    public function findAllProductsKeyById() {
        return Product::all()->keyBy('id');
    }

}
