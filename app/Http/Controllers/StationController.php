<?php

namespace App\Http\Controllers;

use App\Data\Models\Station;
use App\Http\Requests\StationCreateRequest;
use App\Http\Resources\StationCollection;
use App\Http\Resources\StationResource;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::with('stationGroup')->get();

        return new StationCollection($stations);
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
     * @param StationCreateRequest $request
     * @return StationCollection
     */
    public function store(StationCreateRequest $request)
    {
        Station::insert($request->only(['name', 'description', 'station_group_id', 'oee_threshold']));
        return new StationCollection(Station::all());
    }

    public function show(Request $request)
    {
        $stationId = $request->route('station');
        $station   = Station::find($stationId);

        return new StationResource($station);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
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
    public function update(StationCreateRequest $request, $id)
    {
        $station                        = Station::find($id);
        $station->name                  = $request['name'];
        $station->description           = $request['description'];
        $station->station_group_id      = $request['station_group_id'];
        $station->oee_threshold = $request['oee_threshold'];
        $station->save();
        return new StationCollection(Station::all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return StationCollection
     */
    public function destroy($id)
    {
        $station = Station::find($id);
        $station->delete();
        return new StationCollection(Station::all());
    }

    public function stationsByGroupId($id)
    {
        $stations = Station::where('station_group_id', $id)->get();
        return new StationCollection($stations);
    }
}
