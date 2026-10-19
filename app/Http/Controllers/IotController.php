<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MqttService;
use Kreait\Firebase\Factory;

class IotController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function index()
    {
        return view('iot-dashboard');
    }

    public function sendControl(Request $request)
    {
        $command = $request->input('command');
        $topic = env('MQTT_TOPIC_CONTROL', 'iot/kelompok2/control');

        // 1. Kirim perintah via MQTT ke Hardware
        $sent = $this->mqttService->publish($topic, $command);

        if ($sent) {
            // 2. Catat log kontrol ke Firebase Realtime Database
            $firebasePath = storage_path('app/firebase/utss-bfa6d-firebase-adminsdk-fbsvc-73c5ec9d0b.json');
            $database = (new Factory)->withServiceAccount($firebasePath)->createDatabase();
            
            $database->getReference('control_logs')->push([
                'command' => $command,
                'timestamp' => ['.sv' => 'timestamp']
            ]);

            return response()->json(['status' => 'success', 'message' => 'Perintah terkirim']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengirim perintah'], 500);
    }
}