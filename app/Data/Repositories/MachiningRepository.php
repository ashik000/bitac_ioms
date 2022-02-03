<?php


namespace App\Data\Repositories;

use App\Data\Models\MachineStatus;
use App\Data\Models\Stations;

class MachiningRepository
{
    public function fetchAllMachiningData($start_of_day, $end_of_day)
    {
        // get all data from machine_status table, join with station_id with stations table, where produced_at = $produced_at, produced_at can be null
        $machiningData = MachineStatus::select('machine_status.*', 'stations.name as station_name')
            ->join('stations', 'machine_status.station_id', '=', 'stations.id')
            ->whereBetween('machine_status.produced_at', [$start_of_day, $end_of_day])
            ->get();

        return $machiningData;
    }
}
