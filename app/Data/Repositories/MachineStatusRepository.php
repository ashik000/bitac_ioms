<?php

namespace App\Data\Repositories;

use App\Data\Models\MachineStatus;

class MachineStatusRepository
{
    public function storeData($request)
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
        $machineStatus['counter_number'] = $request['counter_number'];
        $machineStatus['power_status'] = $request['power_status'];
        $machineStatus['program_name'] = $request['program_name'];
        $machineStatus['produced_at'] = $request['produced_at'];
        $machineStatus['synced_at'] = now();
        $check = $machineStatus->save();

        if ($check) {
            return true;
        }

        return false;
    }

}
