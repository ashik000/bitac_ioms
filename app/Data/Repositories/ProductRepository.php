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
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;

class ProductRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public function findProductsOfStation(int $stationId)
    {
        $station = Station::find($stationId);
        $products = $station->products;

//        Log::debug($products);

        foreach ($products as $key => $res) {
//            Log::debug('test');
//            Log::debug($res->meta->start_time);
            if (empty($res->meta->start_time)) {
                unset($products[$key]);
            }
        }
//        Log::debug($products);

        return $products;
    }

    public function findStationProductAndProductByStationId(int $stationId)
    {
        $stationProduct = StationProduct::where('station_id', '=', $stationId)->first();
        if (empty($stationProduct)) return null;

        $product = Product::where('id', '=', $stationProduct->product_id)->first();

        return [$stationProduct, $product];
    }

    public function findAllStationProductsKeyByStationId() {
        return StationProduct::whereNotNull('start_time')->get()->keyBy('station_id');
    }

    public function findAllStationProductsOfAStationKeyByProductId($stationId) {
        return StationProduct::where('station_id', '=', $stationId)->get()->keyBy('product_id');
    }

    public function findAllProductsKeyById() {
        return Product::all()->keyBy('id');
    }

    public function fetchAllProductsByGroup($productGroupId, $orderBy) {
        return Product::where('product_group_id', $productGroupId)->orderBy('name', $orderBy)->get();
    }

    public function storeProduct($request) {
        $product = new Product();
        $product['name']             = $request['name'];
        $product['code']             = $request['code'];
        $product['unit']             = $request['unit'];
        $product['product_group_id'] = $request['product_group_id'];
        $check = $product->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function updateProduct($request, $id) {
        $product = Product::find($id);
        $product['name']             = $request['name'];
        $product['code']             = $request['code'];
        $product['unit']             = $request['unit'];
        $product['product_group_id'] = $request['product_group_id'];
        $check = $product->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function deleteProduct($id) {
        $product = Product::find($id);
        $check = $product->delete();
        if ($check) {
            return true;
        }
        return false;
    }

}
