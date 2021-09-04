<?php

namespace App\Http\Controllers;

use App\Data\Models\Packet;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\PacketRepository;
use App\Http\Controllers\Mqtt\InovaceMqttClient;
use App\Jobs\ParseAndSaveIomsLogPacket;

class DeviceController
{

    protected $deviceRepository;
    protected $packetRepository;

    public function __construct(DeviceRepository $deviceRepository,
                                PacketRepository $packetRepository)
    {
        $this->deviceRepository = $deviceRepository;
        $this->packetRepository = $packetRepository;
    }

    /**
     * @param $topic
     * @param $message
     * @param InovaceMqttClient $mqttClient
     */
    public function parseAndSaveLogPackets($topic, $message, $mqttClient)
    {
        $deviceIdentifier = $this->getDeviceIdentifierFromTopic($topic);
        $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);
        $packet = $this->packetRepository->savePacketFromDevice($device, bin2hex($message));
        dispatch(new ParseAndSaveIomsLogPacket($device, $packet));
        $mqttClient->sendPublishAcknowledgementAfterProcessing();
    }

    private function getDeviceIdentifierFromTopic(string $topic)
    {
        $topicArray = explode('/', $topic);
        if(sizeof($topicArray) < 3) return '';
        return $topicArray[2];
    }
}
