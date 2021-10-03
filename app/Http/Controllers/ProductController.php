<?php

namespace App\Http\Controllers;

use App\Data\Models\Product;
use App\Data\Models\Station;
use App\Data\Models\StationProduct;
use App\Data\Repositories\ProductRepository;
use App\Exceptions\NotFoundException;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProductController extends Controller
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ProductCollection
     */
    public function index(){
        return new ProductCollection(Product::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductCreateRequest $request
     * @return ProductCollection
     */
    public function store(ProductCreateRequest $request)
    {
        $checkStore = $this->productRepository->storeProduct($request);
        if(!$checkStore) throw new BadRequestException();
        $products = $this->productRepository->fetchAllProductsByGroup($request['product_group_id'], $orderBy = 'asc');
        return new ProductCollection($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return ProductResource
     */
    public function show($id)
    {
        return new ProductResource(Product::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return ProductCollection
     */
    public function update(Request $request, $id)
    {
        $checkUpdate = $this->productRepository->updateProduct($request, $id);
        if(!$checkUpdate) throw new BadRequestException();
        $products = $this->productRepository->fetchAllProductsByGroup($request['product_group_id'], $orderBy = 'asc');
        return new ProductCollection($products);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return ProductCollection
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $stationProduct = StationProduct::where('product_id', $product->id)->first();
        if(!empty($stationProduct)) throw new UnauthorizedException();
        $checkDelete = $this->productRepository->deleteProduct($id);
        if(!$checkDelete) throw new BadRequestException();
        $products = $this->productRepository->fetchAllProductsByGroup($product['product_group_id'], $orderBy = 'asc');
        return new ProductCollection($products);
    }

    /**
     * get list of downtime reasons by group id
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return ProductCollection
     */
    public function productsByGroupId($id)
    {
        $products = Product::where('product_group_id', $id)->get();
        return new ProductCollection($products);
    }

    public function stationProductsByStationId(Request $request) {
        $stationId = $request->get('station_id');
        $station = Station::where('id', $stationId)->first();
        if(empty($station)) throw new NotFoundException();
        $products = $station->products()->get();
        return $products;
    }
}
