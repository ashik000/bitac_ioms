<?php

use App\Http\Controllers\LineViewController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\StoreMachineDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Auth::loginUsingId(1);

Route::middleware(['auth:api', 'scope:admin,manager'])->group(function () {
    Route::get('stationProducts/all',['uses'=>'StationProductController@all']);
    Route::get('stationShifts/all',['uses'=>'StationShiftController@all']);
    Route::get('stationOperators/findByStationId', ['uses' => 'StationOperatorController@findStationOperatorByStationId']);
    Route::get('stationTeams/findByStationId', ['uses' => 'StationTeamController@findStationTeamByStationId']);
    Route::post('assignDowntimeReason', ['uses'=>'DowntimeReasonController@assignDowntimeReason']);
    Route::post('uploadHourlyScrapCount', ['uses'=>'ScrapController@uploadHourlyScrapCount']);
    Route::post('removeDowntimeReason', ['uses'=>'DowntimeReasonController@removeDowntimeReason']);
    Route::resource('shifts', 'ShiftController');
    Route::resource('stations', 'StationController');
    Route::resource('products', 'ProductController');
    Route::get('productsByGroupId/{id}','ProductController@productsByGroupId');
    Route::resource('downtimeReasons', 'DowntimeReasonController');
    Route::resource('downtimeReasonGroups','DowntimeReasonGroupController');
    Route::get('downtimeReasonsByGroupId/{id}','DowntimeController@downtimeReasonsByGroupId');
    Route::get('stationsByGroupId/{id}','StationController@stationsByGroupId');
    Route::resource('productGroups','ProductGroupController');
    Route::resource('stationGroups','StationGroupController');
    Route::resource('stationProducts','StationProductController');
    Route::resource('stationShifts','StationShiftController');
    Route::resource('stationOperators','StationOperatorController');
    Route::resource('stationTeams','StationTeamController');
    Route::resource('scraps','ScrapController');
    Route::resource('operators','OperatorController');
    Route::get('lineview', ['as' => 'lineview.graphdata', 'uses' => 'LineViewController@lineviewData']);
    Route::get('topDowntimeReasons', 'LineViewController@topDowntimeReasons');
    Route::get('topOperatorDowntimes', 'LineViewController@topOperatorDowntimes');
    Route::get('report',['uses'=>'ReportController@index']);
    Route::get('getHourlyProducedAndScrapedCountOfADay',['uses'=>'ReportController@getHourlyProducedAndScrapedCountOfADay']);
    Route::get('getDowntimeSummary',['uses'=>'DowntimeController@getDowntimeSummary']);
    Route::get('downtimeReport',['uses'=>'ReportController@getDowntimeReport']);
    Route::get('testMyFunctions',['uses'=>'DowntimeController@testMyFunctions']);
    Route::get('getOEETableReportByStation',['uses'=>'ReportController@getOEETableReportByStation']);
    Route::get('getOEETableReportByStationProduct',['uses'=>'ReportController@getOEETableReportByStationProduct']);
    Route::get('getOEETableReportByStationShift',['uses'=>'ReportController@getOEETableReportByStationShift']);
    Route::get('getOEETableReportByStationOperator',['uses'=>'ReportController@getOEETableReportByStationOperator']);
    Route::get('getOEETableReportByStationTeam',['uses'=>'ReportController@getOEETableReportByStationTeam']);
    Route::get('getOEETableReportByStationExcel',['uses'=>'ReportController@getOEETableReportByStationExcel']);
    Route::get('getOEETableReportByStationProductExcel',['uses'=>'ReportController@getOEETableReportByStationProductExcel']);
    Route::get('getOEETableReportByStationShiftExcel',['uses'=>'ReportController@getOEETableReportByStationShiftExcel']);
    Route::get('getOEETableReportByStationOperatorExcel',['uses'=>'ReportController@getOEETableReportByStationOperatorExcel']);
    Route::get('getOEETableReportByStationTeamExcel',['uses'=>'ReportController@getOEETableReportByStationTeamExcel']);
    Route::get('report/downtime/by/station',['uses'=>'DowntimeReportController@getDowntimeTableReportByStation']);
    Route::get('report/downtime/by/product',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationProduct']);
    Route::get('report/downtime/by/shift',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationShift']);
    Route::get('report/downtime/by/operator',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationOperator']);
    Route::get('report/downtime/by/team',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationTeam']);
    Route::get('report/downtime/by/station/excel',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationExcel']);
    Route::get('report/downtime/by/product/excel',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationProductExcel']);
    Route::get('report/downtime/by/shift/excel',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationShiftExcel']);
    Route::get('report/downtime/by/operator/excel',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationOperatorExcel']);
    Route::get('report/downtime/by/team/excel',['uses'=>'DowntimeReportController@getDowntimeTableReportByStationTeamExcel']);
    Route::get('lineviewStationShift','LineViewController@getLineViewStationShifts');
    Route::get('getOperatorName','LineViewController@getOperatorOfStation');
    Route::get('getTeamName','LineViewController@getTeamOfStation');
    Route::post('storeLineviewDefects','LineViewController@storeLineviewDefects');
    Route::post('assignOperatorToStation','StationOperatorController@assignOperatorToStation');
    Route::post('assignProductToStation','StationProductController@assignProductToStation');
    Route::get('stationProductsByStationId','ProductController@stationProductsByStationId');
    Route::get('team','TeamController@index');
    Route::get('team/operators','TeamController@getOperatorsOfTeam');
    Route::post('team','TeamController@store');
    Route::post('teamUpdate/{id}','TeamController@update');
    Route::post('teamDelete/{id}','TeamController@destroy');
    Route::post('team/operators','TeamController@updateTeamOperators');
    Route::post('team/operators/delete','TeamController@destroyTeamOperators');
    Route::get('scada-api', 'ReportController@scada');
    Route::get('dashboard-summary', 'ReportController@getDashboardSummary');
    Route::get('dashboard', 'DashboardController@getMachineStatus');
    Route::get('machiningData', 'MachiningController@index');
    Route::get('getMachiningDataExcel', 'MachiningController@getMachiningDataExcel');
    Route::get('getMachineStatus', 'DashboardController@getMachineStatus');
    Route::get('getMachineOEE', 'DashboardController@getMachineOEE');
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', ['uses' => 'Auth\LoginController@logout']);
});
Route::post('login', ['uses' => 'Auth\LoginController@login']);


Route::post('storeEventFile', 'LineViewController@storeEventFile');
Route::get('getDummyScrap', 'ScrapController@getDummyScrap');
Route::get('testScrapX', 'ScrapController@testScrap');

//Route::post('storeMachineData', 'StoreMachineDataController@store');
Route::post('storeMachineData', [StoreMachineDataController::class, 'store']);


