<?php

namespace App\Http\Controllers;

use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use Illuminate\Http\Request;
use Log;

class StationShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allStationShifts = StationShift::all();
        $allStationShifts->map(function ($stationShift) {
            $stationShift->station_name = $stationShift->station->name;
            $stationShift->product_name = $stationShift->shift->name;
            $stationShift->station_group_id = $stationShift->station->stationGroup->id;
            $stationShift->station_group_name = $stationShift->station->stationGroup->name;
            $stationShift->shift_name = $stationShift->shift->name;
            $stationShift->shift_id = $stationShift->shift->id;
        });
        return response()->json($allStationShifts->makeHidden(['deleted_at', 'created_at', 'updated_at',
            'station', 'shift', 'cycle_time', 'cycle_unit', 'cycle_timeout', 'units_per_signal', 'performance_threshold']),
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
        $stationShifts = StationShift::where('station_id',$request->input('station_id'))->get()->load('shift');
        return response()->json($stationShifts,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $stationShift = StationShift::where('station_id',$data['station_id'])->where('shift_id',$data['shift_id'])->first();
        if(empty($stationShift)){
            $stationShift = new StationShift();
        }
        $stationShift->shift_id = $data['shift_id'];
        $stationShift->station_id = $data['station_id'];
        $stationShift->schedule = $data['schedule'];
        $stationShift->save();
        $stationShifts = StationShift::where('station_id',$data['station_id'])->get()->load('shift');
        return response()->json($stationShifts,200);
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stationShift = StationShift::find($id);
        $stationId = $stationShift->station_id;
        $stationShift->delete();
        $stationShifts = StationShift::where('station_id',$stationId)->get()->load('shift');
        return response()->json($stationShifts,200);
    }
}
