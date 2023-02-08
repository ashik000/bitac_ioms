<?php

namespace App\Http\Controllers\API;

use App\Data\Models\StationProduct;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\PacketRepository;
use App\Data\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
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

        if (empty($machineData)) {
            return response()->json(['error' => true, 'message' => 'Empty body'], 500);
        }

        $date = $machineData['date'];

        if (empty($machineData['data']) || empty($date)) {
            return response()->json(['error' => true, 'message' => 'Empty data'], 400);
        }

        foreach ($machineData['data'] as $dataRow)
        {
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
                'feed_rate' => $dataRow['feed_rate'] ?? '0.0',
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

            $this->alarmCheck($stationName, $dataRow['alarm_info'], $station['station_id']);
            $result = $this->machineStatusRepository->storeMachineStatus($payload);

            if (!$result) {
                response()->json(['error' => true, 'message' => 'Could not store data'], 500);
            }
            else{
                $this->autoProductSelection($dataRow, $station);
            }
        }

        return response()->json(['success' => true, 'message' => 'Data store successful'], 200);
    }

    public function autoProductSelection($dataRow, $station)
    {
        $product = $this->productRepository->findProductByName($dataRow['program_name']);
        if(!empty($product) && $station['station_id'] > 0)
        {
            $stationProduct = $this->productRepository->findStationProductByStationIdAndProductId($station['station_id'], $product->id);
            if(!empty($stationProduct) && empty($stationProduct['start_time']))
            {
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
            }
        }
    }

    public function alarmCheck($machineName, $alarmInfo, $stationId)
    {
        if($alarmInfo != 'NULL' && $alarmInfo != 'NO ACTIVE ALARMS')
        {
            $lastStatus = $this->machineStatusRepository->findLatestMachineStatusByStationId($stationId);
            if($lastStatus['alarm_info'] == 'NULL' || $lastStatus['alarm_info'] == 'NO ACTIVE ALARMS')
            {
                $mailBody = [
                    'machine_name'=>$machineName,
                    'alarm_info'=>$alarmInfo
                ];

                $toEmails = [
                    'emails' => [
                        'arifahmed.bitac@gmail.com',
                        'mhasan0925@gmail.com',
                        'pulakkantiroy09@gmail.com',
                        'omaryusuf778106@gmail.com',
                        'salauddin06@yahoo.com',
                        'ae_cncdhaka@bitac.gov.bd',
                        'ashik@inovacetech.com'
                    ],
                    'names' => [
                        'Arif Ahmed',
                        'M Hasan',
                        'Pulak Roy',
                        'Omar Yusuf',
                        'Salauddin',
                        'AE CNC Dhaka',
                        'Ashik Inovace'
                    ]
                ];

                $mailController = new MailController();
                $mailController->generateAlarmMail($mailBody, $toEmails);
            }
        }
    }




}
