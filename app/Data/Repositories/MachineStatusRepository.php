<?php

namespace App\Data\Repositories;

use App\Data\Models\MachineStatus;
use App\Data\Models\Station;
use Illuminate\Support\Facades\DB;

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
    public function findCurrentMachineStatusByStationId($stationId,$startTime,$endTime)
    {
        $query = MachineStatus::query();
        return $query
            ->where('station_id', '=', (int)$stationId)
            ->whereBetween('produced_at', [$startTime, $endTime])
            ->orderBy('produced_at', 'DESC')
            ->first();
    }
    public function findLatestAllMachineStatus($startTime,$endTime)
    {
        $query = Station::query();
        return $query
            ->select('stations.id as sid','stations.name as station_name', 'ms.*')
            ->leftJoin(DB::raw("(select * from machine_status as ms
                    where (ms.produced_at between '".$startTime."' and '".$endTime."') order by ms.produced_at desc limit 1) as ms"), 'ms.station_id', '=', 'stations.id')
            ->orderBy('stations.id')
            ->get()->keyBy('sid');
    }

}
