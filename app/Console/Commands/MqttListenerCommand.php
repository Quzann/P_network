<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Kreait\Firebase\Factory;

class MqttListenerCommand extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Mendengarkan data sensor dari MQTT broker dan menyimpannya ke Firebase';

    public function handle()
    {
        $server   = env('MQTT_BROKER_HOST', 'broker.hivemq.com');
        $port     = env('MQTT_BROKER_PORT', 1883);
        $clientId = 'LaravelListener_' . uniqid();
        $topic    = env('MQTT_TOPIC_SENSOR', 'iot/kelompok2/sensor');

        // Inisialisasi Firebase Admin SDK
        $firebasePath = storage_path('app/firebase/utss-bfa6d-firebase-adminsdk-fbsvc-73c5ec9d0b.json');
        $factory = (new Factory)->withServiceAccount($firebasePath);
        $database = $factory->createDatabase();

        $mqtt = new MqttClient($server, $port, $clientId);
        $settings = (new ConnectionSettings())->setKeepAliveInterval(60);

        try {
            $mqtt->connect($settings, true);
            $this->info("Berhasil terhubung ke MQTT Broker & mendengarkan topik: {$topic}");

            $mqtt->subscribe($topic, function (string $topic, string $message) use ($database) {
                $this->info("[MQTT IN] [{$topic}] -> {$message}");

                // Parsing JSON dari sensor dan perbarui data live_data di Firebase Realtime Database
                $data = json_decode($message, true);
                if ($data) {
                    $database->getReference('live_data')->update($data);
                    $this->info("[Firebase RTDB] Data live_data berhasil diperbarui.");
                }
            }, 0);

            $mqtt->loop();
        } catch (\Exception $e) {
            $this->error("[MQTT Error]: " . $e->getMessage());
        }
    }
}