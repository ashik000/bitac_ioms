<?php

namespace App\Http\Controllers\Mqtt;

use App\Http\Controllers\DeviceController;
use PhpMqtt\Client\Exceptions\MQTTClientException;

class LogSubscriber
{

    private const LOG_SUBSCRIPTION_TOPIC = 'ioms/dev/+/logs';
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
                $mqttClient = MqttConnection::connect('IOMS Subscribe Logs', false);
                \Log::debug('Subscribed to ' . self::LOG_SUBSCRIPTION_TOPIC);
                error_log('Subscribed to ' . self::LOG_SUBSCRIPTION_TOPIC);
                $mqttClient->subscribe(self::LOG_SUBSCRIPTION_TOPIC, function ($topic, $message) use($mqttClient) {
                    $this->deviceController->parseAndSaveLogPackets($topic, $message, $mqttClient);
                }, 1);
                $mqttClient->loop(true);
            } catch (MQTTClientException $mqttClientException) {
                \Log::error($mqttClientException);
                $connected = false;
            } catch (\Exception $exception) {
                \Log::error($exception);
            }
        }
    }
}
