<?php

namespace App\Http\Controllers;

use App\Data\Models\StationOperator;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Log;


class StationOperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $stationOperators = StationOperator::active()->get();
        $stationOperators->map(function ($stationOperator) {
            $stationOperator->operator_name = $stationOperator->operator->first_name . ' ' . $stationOperator->operator->last_name;
            $stationOperator->station_name = $stationOperator->station->name;
            $stationOperator->station_group_id = $stationOperator->station->stationGroup->id;
            $stationOperator->station_group_name = $stationOperator->station->stationGroup->name;
        });
        return response()->json($stationOperators->makeHidden(['deleted_at', 'created_at', 'updated_at', 'station', 'operator']), 200);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $stationId = $data['station_id'];
        $operatorId = $data['operator_id'];
        $startTime = $data['start_time'];
        $stationOperator = StationOperator::where('station_id', '=', $stationId)
            ->where('operator_id', '=', $operatorId)
            ->where('end_time', '=', null)->first();

        if (empty($stationOperator)) {
            $stationOperator = new StationOperator();
        }
        $stationOperator->station_id = $stationId;
        $stationOperator->operator_id = $operatorId;
        $stationOperator->start_time = !empty($startTime) ? Carbon::parse($startTime) : Carbon::now();
        $stationOperator->end_time = null;
        try {
            $stationOperator->save();
        } catch (Exception $ex) {
            Log::error($ex);
            return response()->json([
                'title' => 'error'
            ], 500);
        }
        return app(StationOperatorController::class)->findStationOperatorByStationId($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stationOperator = StationOperator::find($id);
        $stationOperator->end_time = Carbon::now();
        $stationOperator->save();
        return app(StationOperatorController::class)->index();
    }

    public function assignOperatorToStation(Request $request)
    {
        $data = $request->all();
        $stationId = $data['stationId'];
        $operatorId = $data['operatorId'];
        $startTime = date('Y-m-d H:i:s');
        $endTime = null;

        $stationOperator = StationOperator::where('station_id', '=', $stationId)
            ->where('operator_id', '=', $operatorId)->first();
        
        if ($stationOperator) {
            // remove previous operator
            $stationOperator->delete();
        }

        $newStationOperator = new StationOperator();

        $newStationOperator->station_id = $stationId;
        $newStationOperator->operator_id = $operatorId;
        $newStationOperator->start_time = Carbon::parse($startTime);
        $newStationOperator->end_time = $endTime;

        try {
            $newStationOperator->save();
            // return success response
            return TRUE;
        } catch (Exception $ex) {
            return FALSE;
        }
    }

}
