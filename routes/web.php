<?php

use App\Http\Controllers\IotController;

Route::get('/', [IotController::class, 'index']);
Route::post('/control', [IotController::class, 'sendControl']);
