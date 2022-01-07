<?php

namespace App\Http\Controllers\API\V1;

use App\Data\Models\MachineStatus;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\PacketRepository;
use App\Data\Repositories\OperatorRepository;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ShiftRepository;
use App\Data\Repositories\StationTeamRepository;
use App\Http\Controllers\Controller;

class storeMachineDataController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;

    public function __construct(DeviceRepository $deviceRepository, MachineStatusRepository $machineStatusRepository, PacketRepository $packetRepository)
    {
        $this->deviceRepository = $deviceRepository;
        $this->machineStatusRepository = $machineStatusRepository;
        $this->packetRepository = $packetRepository;
    }

    public function store(Request $request)
    {
        $machineData = $request->all();

        if(empty($machineData)) return response()->json(['error' => TRUE, 'message' => 'Empty body'], 500);

        $factoryName = $machineData['factory_name'] ?? null;
        $date = $machineData['date'];

        if (empty($machineData) || empty($date)) return response()->json(['error' => TRUE, 'message' => 'Empty data'], 400);

        $allMachineData = [];
        foreach ($machineData['data'] as $dataRow)
        {
            $stationName = $dataRow['machine_name'];
            $machine_id = $dataRow['machine_id'];
            $deviceIdentifier = $dataRow['device_id'];
            $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);
            $devicePortToDeviceStationMap = $this->deviceRepository->findAllDeviceStationsOfADevice($device->id)->mapWithKeys(function ($deviceStation) {
                return [$deviceStation['port'] + 1 => $deviceStation]; // 1 is added to port
            });

            $station = $devicePortToDeviceStationMap->get($machine_id+1);
            $newData     = $dataRow['data'];
            if(empty($newData) || empty($station)) return response()->json(['error' => TRUE, 'message' => 'Machine not found'], 404);

            foreach ($newData as $row)
            {
                $payload = [
                    'station_id' => $station->id,
                    'spindle_speed' => $row['spindle_speed'] ?? null,
                    'feed_rate' => $row['feed_rate'] ?? null,
                    'machine_uptime' => $row['machine_uptime'] ?? null,
                    'alarm_code' => $row['alarm_code'] ?? null,
                    'alarm_info' => $row['alarm_info'] ?? null,
                    'program_number' => $row['program_number'] ?? null,
                    'program_name' => $row['program_name'] ?? null,
                    'cycle_time' => $row['cycle_time'] ?? null,
                    'counter_number' => $row['counter_number'] ?? null,
                    'power_status' => $row['power_status'] ?? null,
                    'produced_at' => $row['produced_at'] ?? null
                ];

                error_log($payload['produced_at']);

                $result = $this->machineStatusRepository->storeData($payload);
                if(!$result) {
                    response()->json(['error' => true, 'message' => 'Could not store data'], 500);
                }
            }
        }

        return response()->json(['success' => TRUE, 'message' => 'Data store successful'], 200);
    }
}
