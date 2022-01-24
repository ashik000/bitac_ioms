<?php

namespace App\Http\Controllers\API;

use App\Data\Models\StationProduct;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\PacketRepository;
use App\Data\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreMachineDataController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;
    protected $productRepository;

    public function __construct(
        DeviceRepository $deviceRepository,
        MachineStatusRepository $machineStatusRepository,
        PacketRepository $packetRepository,
        ProductRepository $productRepository
    ) {
        $this->deviceRepository = $deviceRepository;
        $this->machineStatusRepository = $machineStatusRepository;
        $this->packetRepository = $packetRepository;
        $this->productRepository = $productRepository;
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
                'production_counter1' => $dataRow['production_counter1'] ?? null,
                'production_counter2' => $dataRow['production_counter2'] ?? null,
                'power_status' => $dataRow['power_status'] ?? null,
                'produced_at' => $dataRow['produced_at'] ?? null
            ];

            $result = $this->machineStatusRepository->storeMachineStatus($payload);

            if (!$result) {
                response()->json(['error' => true, 'message' => 'Could not store data'], 500);
            }
            else{
                $product = $this->productRepository->findProductByName($dataRow['program_name']);
                if(!empty($product) && $station['station_id'] > 0)
                {
                    Log::debug($product);
                    $stationProduct = $this->productRepository->findStationProductByStationIdAndProductId($station['station_id'], $product->id);
                    if(!empty($stationProduct) && empty($stationProduct['start_time']))
                    {
                        Log::debug('StationProduct found');
                        DB::transaction(function () use ($stationProduct) {
                            StationProduct::where('id', $stationProduct->id)
                                ->update([
                                    'start_time' => now()
                                ]);
                            StationProduct::where('station_id', $stationProduct->station_id)
                                ->where('id', '<>', $stationProduct->id)
                                ->update([
                                    'start_time' => null
                                ]);
                        });
                        Log::debug('StationProduct UPDATED');
                    }
                    else{
                        Log::debug('StationProduct not found or already running!');
                    }
                }
                else{
                    Log::debug('Product not found!');
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data store successful'], 200);
    }
}
