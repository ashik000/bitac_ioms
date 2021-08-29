<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Mqtt\InovaceMqttClient;

class DeviceController
{


    /**
     * @param $topic
     * @param $message
     * @param InovaceMqttClient $mqttClient
     */
    public function parseLogPackets($topic, $message, $mqttClient)
    {
        \Log::debug($topic);
        \Log::debug(bin2hex($message));
        $mqttClient->sendPublishAcknowledgementAfterProcessing();
    }
}
