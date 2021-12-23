<?php

namespace App\Http\Controllers;

use App\Data\Models\StationTeam;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Log;

class StationTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $stationTeams = StationTeam::active()->get();
        $stationTeams->map(function ($stationTeams)
        {
            $stationTeam->name               = $stationTeam->team->name;
            $stationTeam->station_name       = $stationTeam->station->name;
            $stationTeam->station_group_id   = $stationTeam->station->stationGroup->id;
            $stationTeam->station_group_name = $stationTeam->station->stationGroup->name;
        });
        return response()->json($stationTeams->makeHidden(['deleted_at', 'created_at', 'updated_at', 'station', 'team']), 200);
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
        $data            = $request->all();
        $stationId       = $data['station_id'];
        $teamId      = $data['team_id'];
        $startTime       = $data['start_time'] ?? null;
        $stationTeam = StationTeam::where('station_id', '=', $stationId)
            ->where('team_id', '=', $teamId)
            ->where('end_time', '=', null)->first();

        if (empty($stationTeam))
        {
            $stationTeam = new StationTeam();
        }
        $stationTeam->station_id  = $stationId;
        $stationTeam->team_id = $teamId;
        $stationTeam->start_time  = !empty($startTime) ? Carbon::parse($startTime) : now();
        $stationTeam->end_time    = null;
        try {
            $stationTeam->save();
        }
        catch (Exception $ex)
        {
            // Log::error($ex);
            return response()->json([
                'title' => 'error'
            ], 500);
        }
        return app(StationTeamController::class)->findStationTeamByStationId($request);
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
        $stationTeam           = StationTeam::find($id);
        $stationTeam->end_time = Carbon::now();
        $stationTeam->save();
        return app(StationTeamController::class)->index();
    }

    public function findStationTeamByStationId(Request $request)
    {
        $data = $request->all();
        $stationId = $data['station_id'];
        $stationTeams = StationTeam::active()->where('station_id', '=', $stationId)->get();
        $stationTeams->map(function ($stationTeam) {
            $stationTeam->name = $stationTeam->team->name;
            $stationTeam->team->makeHidden(['deleted_at', 'created_at', 'updated_at']);
            $stationTeam->station->makeHidden(['deleted_at', 'created_at', 'updated_at']);
        });
        return response()->json($stationTeams->makeHidden(['deleted_at', 'created_at', 'updated_at']), 200);
    }

    public function assignOperatorToStation(Request $request)
    {
        $data       = $request->all();
        $stationId  = $data['stationId'];
        $teamId = $data['teamId'];
        $startTime  = date('Y-m-d H:i:s');
        $endTime    = null;

        $stationTeam = StationTeam::where('station_id', '=', $stationId)
            ->where('team_id', '=', $teamId)->first();

        if ($stationTeam)
        {
            // remove previous team
            if ($stationTeam->team_id != $teamId)
            {
                $stationTeam->delete();
            }
            else
            {
                return TRUE;
            }
        }

        $newStationTeam = new StationTeam();

        $newStationTeam->station_id  = $stationId;
        $newStationTeam->team_id = $teamId;
        $newStationTeam->start_time  = Carbon::parse($startTime);
        $newStationTeam->end_time    = $endTime;

        try {
            $newStationTeam->save();
            // return success response
            return TRUE;
        }
        catch (Exception $ex)
        {
            return FALSE;
        }
    }

}
