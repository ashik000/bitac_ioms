<?php

namespace App\Http\Controllers\Mqtt;

use Monolog\Logger;
use PhpMqtt\Client\MQTTClient;
use Illuminate\Support\Facades\Log;

class MqttConnection
{

    public function __construct()
    {
    }

    /**
     * Connect to the MQTT broker and return the connection object
     * @param string $clientId
     * @return MQTTClient $mqttClient
     */
    public static function connect(string $clientId)
    {
        $server   = config('app.mqtt_broker_url');
        $port     = config('app.mqtt_broker_port');
        $username = config('app.mqtt_broker_username');
        $password = config('app.mqtt_broker_password');

        $mqtt = new InovaceMqttClient($server, $port, $clientId, null, null, new Logger('mqtt-logger'));
        $mqtt->connect($username, $password, null, true);
        return $mqtt;
    }
}
