<?php


namespace App\Http\Controllers\Device\V1;


use App\Data\Models\Device;
use App\Data\Models\Packet;
use App\Data\Repositories\DeviceRepository;
use App\Devices\InovaceDevice;
use App\Devices\ParsedProductionLogPacket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Log;

class DeviceController
{

    /** @var InovaceDevice */
    protected $deviceManager;

    /** @var DeviceRepository */
    protected $deviceRepository;

    public function __construct(
        InovaceDevice $deviceManager,
        DeviceRepository $deviceRepository
    )
    {
        $this->deviceManager = $deviceManager;
        $this->deviceRepository = $deviceRepository;
    }

    public function postLogs(Request $request)
    {
        $req = $request->getContent();
        $packet = new Packet();
        $deviceIdentifier = sprintf("%04u", unpack("Vdevice/", substr($req, 0, 4))['device']);
        $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);
        $packet->device_id = !empty($device)? $device->id : null;
        $packet->request = bin2hex($req);
        $packet->save();

        try {
            /** @var  ParsedProductionLogPacket $parsedProductionLogPacket */
            $parsedProductionLogPacket = $this->deviceManager->parseAndSaveProductionLogs($packet);
            $response = $this->deviceManager->buildStatusResponse(0, 0, $parsedProductionLogPacket->getCycleTime()/2, Carbon::now()->diffInSeconds($parsedProductionLogPacket->getTimestamp()));
            $packet->status = $parsedProductionLogPacket->getExceptionStackTrace();
            $parsedProductionLogPacket->throwIfException();
            return $this->sendResponse($packet, $response, 200);
        } catch (Exception $ex) {
            Log::error($ex);
            $response = $this->deviceManager->buildStatusResponse(0, 0, 10, 10);
            return $this->sendResponse($packet, $response, 200);
        }
    }

    private function sendResponse($packet, $resp, $statusCode = 200)
    {
        $packet->response = bin2hex($resp);
        $packet->save();
        return response($resp, $status = $statusCode)
            ->withHeaders([
                'Content-Type'     => 'application/octet-stream',
                'Content-Encoding' => 'identity;q=0',
            ]);
    }
}
