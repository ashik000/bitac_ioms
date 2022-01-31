<?php

namespace App\Http\Controllers\API;

use App\Console\Commands\MailAlert;
use App\Data\Models\StationProduct;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\PacketRepository;
use App\Data\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
//use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

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

            $this->AlarmCheck($stationName, $dataRow['alarm_info'], $station['station_id']);
            $result = $this->machineStatusRepository->storeMachineStatus($payload);

            if (!$result) {
                response()->json(['error' => true, 'message' => 'Could not store data'], 500);
            }
            else{
                $this->AutoProductSelection($dataRow, $station);
            }
        }

        return response()->json(['success' => true, 'message' => 'Data store successful'], 200);
    }

    public function AutoProductSelection($dataRow, $station){
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

    public function AlarmCheck($machineName, $alarmInfo, $stationId){
        if($alarmInfo != 'NULL' && $alarmInfo != 'NO ACTIVE ALARMS')
        {
            Log::debug('alarm detected');
            $lastStatus = $this->machineStatusRepository->findLatestMachineStatusByStationId($stationId);
            if($lastStatus['alarm_info'] == 'NULL' || $lastStatus['alarm_info'] == 'NO ACTIVE ALARMS')
            {
                Log::debug('alarm validation success');
                $mailBody = [
                    'machine_name'=>$machineName,
                    'alarm_info'=>$alarmInfo
                ];
                $this->GenerateAlarmMail($mailBody);
                Log::debug('alarm sent');
            }
        }
    }

    public function GenerateAlarmMail($mailBody)
    {
        $mail =  PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "cncshop.bitacdhaka@gmail.com";
        $mail->Password   = "k%uGR@8xpRZkjqA3";
        $mail->IsHTML(true);
        $mail->AddAddress("ashik.inovace@gmail.com", "Ashik");
        $mail->AddAddress("arifahmed.bitac@gmail.com", "Arif Ahmed");
        $mail->AddAddress("mhasan0925@gmail.com", "M Hasan");
        $mail->AddAddress("pulakkantiroy09@gmail.com", "Pulak Roy");
        $mail->AddAddress("omaryusuf778106@gmail.com", "Omar Yusuf");
        $mail->SetFrom("cncshop.bitacdhaka@gmail.com", "BITAC CNC Shop");
        //$mail->AddReplyTo("ashik.inovace@gmail.com", "Ashik");
        //$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
        $mail->Subject = "Alarm | IOMS";
        //$content = "Test Body";
        $machineName = $mailBody['machine_name'];
        $alarmInfo = $mailBody['alarm_info'];
        $content = "<b>Machine: </b>".$machineName."<br><b>Alarm: </b>".$alarmInfo;
        $mail->MsgHTML($content);
        $this->SendMail($mail);
    }

    public function SendMail($mail){
        try{
            if($mail->Send()) {
                Log::debug("Email sent successfully");
            } else {
                Log::debug("Error while sending Email");
            }
        }
        catch(Exception $e){
            Log::debug($e->getMessage());
        }
    }
}
