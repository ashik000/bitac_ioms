<?php


namespace App\Data\Repositories;

use App\Data\Models\MachineStatus;
use App\Data\Models\Stations;

class MachiningRepository
{
    public function fetchAllMachiningData($startTime, $endTime)
    {
        $machiningData = MachineStatus::select('machine_status.*', 'stations.name as station_name')
            ->join('stations', 'machine_status.station_id', '=', 'stations.id')
            ->whereBetween('machine_status.produced_at', [$startTime, $endTime])
            ->orderBy('machine_status.produced_at', 'desc')
            ->paginate(100);

        return $machiningData;
    }
}
