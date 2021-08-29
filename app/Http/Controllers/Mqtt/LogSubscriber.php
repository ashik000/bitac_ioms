<?php

namespace App\Http\Controllers\Mqtt;

use Illuminate\Log\Logger;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MQTTClient;

class LogSubscriber
{

    public function subscribe() {
        $mqttConnection = MqttConnection::connect('IOMS Subscribe Logs');

        $mqttConnection->subscribe('php-mqtt/client/test', function ($topic, $message) {
            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
        }, 1);
        $mqttConnection->loop(true);
    }
}
