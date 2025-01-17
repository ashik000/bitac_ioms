<?php

namespace App\Http\Controllers;

use App\Data\Models\Packet;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\PacketRepository;
use App\Devices\InovaceDevice;
use App\Http\Controllers\Mqtt\InovaceMqttClient;
use App\Jobs\ParseAndSaveIomsLogPacket;
use Illuminate\Support\Facades\Log;

class DeviceController
{

    protected $deviceRepository;
    protected $packetRepository;
    protected $inovaceDevice;

    public function __construct(DeviceRepository $deviceRepository,
                                PacketRepository $packetRepository,
                                InovaceDevice $inovaceDevice)
    {
        $this->deviceRepository = $deviceRepository;
        $this->packetRepository = $packetRepository;
        $this->inovaceDevice = $inovaceDevice;
    }

    /**
     * @param $topic
     * @param $message
     * @param InovaceMqttClient $mqttClient
     */
    public function parseAndSaveLogPackets($topic, $message, $mqttClient)
    {
        $mqttClient->sendPublishAcknowledgementAfterProcessing();
        $deviceIdentifier = $this->getDeviceIdentifierFromTopic($topic);
        $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);
        $packet = $this->packetRepository->savePacketFromDevice($device, bin2hex($message));
        Log::debug('Recevied packet ' . bin2hex($message));
        error_log('Recevied packet ' . bin2hex($message));
        try {
            $this->inovaceDevice->parseLogPacketAndSave($device, $packet);
        } catch (\Exception $ex) {
            error_log('Exception occurred');
            Log::error($ex);
        }
    }

    private function getDeviceIdentifierFromTopic(string $topic)
    {
        $topicArray = explode('/', $topic);
        if(sizeof($topicArray) < 3) return '';
        return $topicArray[2];
    }
}
