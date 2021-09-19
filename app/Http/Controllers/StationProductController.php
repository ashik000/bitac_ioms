<?php

namespace App\Http\Controllers;

use App\Data\Models\StationProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $allStationProducts = StationProduct::all();
        $allStationProducts->map(function ($stationProduct) {
            $stationProduct->station_name = $stationProduct->station->name;
            $stationProduct->product_name = $stationProduct->product->name;
            $stationProduct->station_group_id = $stationProduct->station->stationGroup->id;
            $stationProduct->station_group_name = $stationProduct->station->stationGroup->name;
            $stationProduct->product_group_id = $stationProduct->product->productGroup->id;
            $stationProduct->product_group_name = $stationProduct->product->productGroup->name;
        });
        return response()->json($allStationProducts->makeHidden(['deleted_at', 'created_at', 'updated_at',
            'station', 'product', 'cycle_time', 'cycle_unit', 'cycle_timeout', 'units_per_signal', 'performance_threshold']),
            200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function all(Request $request){
        $stationProducts = StationProduct::where('station_id',$request->input('station_id'))->get()->load('product');
        return response()->json($stationProducts,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $stationProduct = StationProduct::where('station_id',$data['station_id'])->where('product_id',$data['product_id'])->first();
        if(empty($stationProduct)){
            $stationProduct = new StationProduct();
        }
        $stationProduct->product_id = $data['product_id'];
        $stationProduct->station_id = $data['station_id'];
        $stationProduct->cycle_time = $data['cycle_time'];
        $stationProduct->cycle_unit = $data['cycle_unit'];
        $stationProduct->cycle_timeout = $data['cycle_timeout'];
        $stationProduct->units_per_signal = $data['units_per_signal'];
//        $stationProduct->deleted_at = null;
        $stationProduct->performance_threshold = $data['performance_threshold'];
        $stationProduct->save();
        $stationProducts = StationProduct::where('station_id',$data['station_id'])->get()->load('product');
        return response()->json($stationProducts,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $stationProduct = StationProduct::find($id);
        $stationId = $stationProduct->station_id;
        $stationProduct->delete();
        $stationProducts = StationProduct::where('station_id',$stationId)->get()->load('product');
        return response()->json($stationProducts,200);
    }

    public function assignProductToStation(Request $request)
    {
        $data = $request->all();
        $stationProduct = StationProduct::where('station_id',$data['station_id'])->where('product_id',$data['product_id'])->first();

        if (!empty($stationProduct)) {
            // when product found on the db
            if (!empty($stationProduct['start_time'])) {
                // no update because already selected
                return response()->json('success1',200);
            }
            else {
                // assign start_time and set null to others
                DB::beginTransaction();

                $update_check = DB::table('station_products')
                    ->where('id', $stationProduct['id'])
                    ->update(['start_time' => now()]);

                if ($update_check) {
                    // make other station products null
                    $other_check = DB::table('station_products')
                        ->where('station_id', $data['station_id'])
                        ->where('id',  '<>', $stationProduct['id'])
                        ->update(['start_time' => null]);

                    if ($other_check) {
                        DB::commit();
                        // return success msg
                        return response()->json('success2',200);
                    }
//                    else {
//                        DB::rollback();
//                        // return error msg
//                        return response()->json('error 3',400);
//                    }
                } else {
                    DB::rollback();
                    // return error msg
                    return response()->json('error 2',400);
                }
            }
        } else {
            // product not found
            return response()->json('error 1',400);
        }
    }

}
