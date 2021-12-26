<?php

namespace App\Data\Repositories;

use App\Data\Models\Station;
use Illuminate\Support\Facades\DB;

class StationTeamRepository
{


    public function getStationIdToTeamMap()
    {
        return Station::query()
                        ->join('station_teams', 'stations.id', '=', 'station_teams.station_id')
                        ->join('teams', 'station_teams.team_id', '=', 'teams.id')
                        ->whereNull('station_teams.deleted_at')
                        ->select(DB::raw('stations.id as sid, teams.*'))
                        ->get()
                        ->keyBy('sid');
    }

}
