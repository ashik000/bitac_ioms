<?php

namespace App\Http\Controllers;

use App\Data\Models\ProductGroup;
use App\Http\Requests\ProductGroupCreateRequest;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ProductGroupCollection
     */
    public function index(){
        return response()->json(ProductGroup::all(),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *x
     * @param ProductGroupCreateRequest $request
     * @return ProductGroupCollection
     */
    public function store(ProductGroupCreateRequest $request){
        $product_group = new ProductGroup();
        $product_group['name'] = $request['name'];
        $product_group->save();
        return response()->json(ProductGroup::all(),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function show($id){
        return response()->json(ProductGroup::find($id),200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ProductGroupCollection
     */
    public function update(Request $request, $id)
    {
        $product_group = ProductGroup::find($id);
        $product_group['name'] = $request['name'];
        $product_group->save();
        return response()->json(ProductGroup::all(),200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return ProductGroupCollection
     */
    public function destroy($id){
        $product_group = ProductGroup::find($id);
        $product_group->delete();
        return response()->json(ProductGroup::all(),200);
    }
}
