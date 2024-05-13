<?php

namespace App\Data\Repositories;

use App\Data\Models\MachineStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class MachineStatusRepository
{
    public function storeMachineStatus($request)
    {
        $machineStatus = new MachineStatus();
        $machineStatus['station_id'] = $request['station_id'];
        $machineStatus['spindle_speed'] = $request['spindle_speed'];
        $machineStatus['feed_rate'] = $request['feed_rate'];
        $machineStatus['machine_uptime'] = $request['machine_uptime'];
        $machineStatus['alarm_code'] = $request['alarm_code'];
        $machineStatus['alarm_info'] = $request['alarm_info'];
        $machineStatus['program_number'] = $request['program_number'];
        $machineStatus['program_name'] = $request['program_name'];
        $machineStatus['cycle_time'] = $request['cycle_time'];
        $machineStatus['production_counter1'] = $request['production_counter1'];
        $machineStatus['production_counter2'] = $request['production_counter2'];
        $machineStatus['power_status'] = $request['power_status'];
        $machineStatus['spindle_speed_active'] = $request['spindle_speed_active'];
        $machineStatus['feed_rate_active'] = $request['feed_rate_active'];
        $machineStatus['machining_mode'] = $request['machining_mode'];
        $machineStatus['tool_life'] = $request['tool_life'];
        $machineStatus['tool_number'] = $request['tool_number'];
        $machineStatus['load_on_table'] = $request['load_on_table'];
        $machineStatus['produced_at'] = $request['produced_at'];
        $machineStatus['synced_at'] = now();
        $check = $machineStatus->save();

        if ($check) {
            return true;
        }

        return false;
    }

    public function findLatestMachineStatusByStationId($stationId)
    {
        $query = MachineStatus::query();
        return $query
            ->where('station_id', '=', (int)$stationId)
            ->orderBy('produced_at', 'DESC')
            ->first();
    }
    public function findLatestAllMachineStatus($startTime, $endTime)
    {
        $query = MachineStatus::query();
        $query
            ->select('machine_status.*', 'stations.name as station_name')
            ->join('stations', 'machine_status.station_id', '=', 'stations.id')
            ->whereBetween('machine_status.produced_at', [$startTime, $endTime])
            ->orderBy('machine_status.produced_at', 'DESC');

        $query->when($filter['station_id'] ?? null, function (EloquentBuilder $q, $stationId) {
            $q->where('machine_status.station_id', '=', $stationId);
        });

        return $query->get();
    }

}
