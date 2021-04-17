<?php

/*
|--------------------------------------------------------------------------
| Device Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Device routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "device" middleware group. Enjoy building your Device!
|
*/

Route::group([], function () {
    Route::post('logs', ['as' => 'logs', 'uses' => 'DeviceController@postLogs']);
});
