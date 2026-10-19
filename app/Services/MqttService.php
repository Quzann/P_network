<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function publish($topic, $message)
    {
        $server   = env('MQTT_BROKER_HOST', 'broker.hivemq.com');
        $port     = env('MQTT_BROKER_PORT', 1883);
        $clientId = 'LaravelApp_' . uniqid();

        $mqtt = new MqttClient($server, $port, $clientId);
        $settings = (new ConnectionSettings())->setKeepAliveInterval(60);

        try {
            $mqtt->connect($settings, true);
            $mqtt->publish($topic, $message, 0);
            $mqtt->disconnect();
            return true;
        } catch (\Exception $e) {
            \Log::error("[MQTT Error] Gagal publish: " . $e->getMessage());
            return false;
        }
    }
}