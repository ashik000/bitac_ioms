<?php

namespace App\Http\Controllers\Mqtt;

use App\Http\Controllers\DeviceController;
use PhpMqtt\Client\Exceptions\MQTTClientException;

class LogSubscriber
{

    protected $deviceController;

    public function __construct(DeviceController $deviceController)
    {
        $this->deviceController = $deviceController;
    }

    public function subscribe() {
        $connected = false;
        while(!$connected) {
            sleep(5);
            try {
                $mqttClient = MqttConnection::connect('IOMS Subscribe Logs');

                $mqttClient->subscribe('ioms/dev/+/logs', function ($topic, $message) use($mqttClient) {
                    $this->deviceController->parseLogPackets($topic, $message, $mqttClient);
                }, 1);
                $mqttClient->loop(true);
            } catch (MQTTClientException $mqttClientException) {
                \Log::error($mqttClientException);
                $connected = false;
            }
        }
    }
}
