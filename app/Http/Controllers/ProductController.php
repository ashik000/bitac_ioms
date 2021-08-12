<?php

namespace App\Http\Controllers;

use App\Data\Models\Product;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
        $product = new Product();
        $product['name']             = $request['name'];
        $product['code']             = $request['code'];
        $product['unit']             = $request['unit'];
        $product['product_group_id'] = $request['product_group_id'];
        $product->save();

        $products = Product::where('product_group_id', $product['product_group_id'])->get();
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
        $product = Product::find($id);
        $product['name']             = $request['name'];
        $product['code']             = $request['code'];
        $product['unit']             = $request['unit'];
        $product['product_group_id'] = $request['product_group_id'];
        $product->save();

        $products = Product::where('product_group_id', $product['product_group_id'])->get();
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
        $product->delete();
        $products = Product::where('product_group_id', $product['product_group_id'])->get();
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
}
