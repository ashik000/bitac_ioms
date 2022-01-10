<?php

namespace App\Http\Controllers\API;

use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\PacketRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreMachineDataController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;

    public function __construct(
        DeviceRepository $deviceRepository,
        MachineStatusRepository $machineStatusRepository,
        PacketRepository $packetRepository
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->machineStatusRepository = $machineStatusRepository;
        $this->packetRepository = $packetRepository;
    }

    public function store(Request $request)
    {
        $machineData = $request->all();
        Log::debug($machineData);
        if (empty($machineData)) {
            return response()->json(['error' => true, 'message' => 'Empty body'], 500);
        }

        $factoryName = $machineData['factory_name'] ?? null;
        $date = $machineData['date'];

        if (empty($machineData['data']) || empty($date)) {
            return response()->json(['error' => true, 'message' => 'Empty data'], 400);
        }

        foreach ($machineData['data'] as $dataRow) {
            $stationName = $dataRow['machine_name'];
            $machine_id = $dataRow['machine_id'];
            $deviceIdentifier = $dataRow['device_id'];
            $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);
            $devicePortToDeviceStationMap = $this->deviceRepository->findAllDeviceStationsOfADevice($device->id)->mapWithKeys(function (
                $deviceStation
            ) {
                return [$deviceStation['port'] => $deviceStation]; // 1 is added to port coz key=0 not possible
            });

            $station = $devicePortToDeviceStationMap->get($machine_id);

            if (empty($station)) {
                return response()->json(['error' => true, 'message' => 'Machine not found'], 404);
            }

            $payload = [
                'station_id' => $station['station_id'],
                'spindle_speed' => $dataRow['spindle_speed'] ?? null,
                'feed_rate' => $dataRow['feed_rate'] ?? null,
                'machine_uptime' => $dataRow['machine_uptime'] ?? null,
                'alarm_code' => $dataRow['alarm_code'] ?? null,
                'alarm_info' => $dataRow['alarm_info'] ?? null,
                'program_number' => $dataRow['program_number'] ?? null,
                'program_name' => $dataRow['program_name'] ?? null,
                'cycle_time' => $dataRow['cycle_time'] ?? null,
                'counter_number' => $dataRow['counter_number'] ?? null,
                'power_status' => $dataRow['power_status'] ?? null,
                'produced_at' => $dataRow['produced_at'] ?? null
            ];

            error_log($payload['produced_at']);
            $result = $this->machineStatusRepository->storeMachineStatus($payload);

            if (!$result) {
                response()->json(['error' => true, 'message' => 'Could not store data'], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Data store successful'], 200);
    }
}
