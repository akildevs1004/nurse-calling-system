<?php

use App\Http\Controllers\Alarm\Api\ApiAlarmControlController;
use App\Http\Controllers\Alarm\DeviceSensorLogsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnouncementsCategoriesController;
use App\Models\Alarm\DeviceSensorLogs;
use Illuminate\Support\Facades\Route;


Route::post('alarm_device_status', [ApiAlarmControlController::class, 'LogDeviceStatus']);

Route::get('alarm_dashboard_update_responsetime', [ApiAlarmControlController::class, 'updateAlarmResponseTime']);
Route::post('alarm_device_health', [ApiAlarmControlController::class, 'updateDeviceHealth']);


Route::get('alarm_dashboard_get_temparature_latest', [DeviceSensorLogsController::class, 'getDeviceLatestTemperature']);
Route::get('alarm_dashboard_get_hourly_data', [DeviceSensorLogsController::class, 'getDeviceTodayHourlyAlarmsRequest']);
Route::get('alarm_dashboard_get_monthly_data', [DeviceSensorLogsController::class, 'getDeviceTodayMonthlyAlarms']);
Route::get('alarm_dashboard_get_categories_data', [DeviceSensorLogsController::class, 'getDeviceCategoryAlarms']);
Route::get('alarm_dashboard_get_devices_data', [DeviceSensorLogsController::class, 'getDeviceDeviceAlarms']);
Route::get('alarm_dashboard_get_statistics', [DeviceSensorLogsController::class, 'getDeviceAlarmsStatics']);
Route::get('alarm_dashboard_get_humidity_hourly_data', [DeviceSensorLogsController::class, 'getDeviceTodayHourlyHumidity']);
Route::get('alarm_device_logs', [DeviceSensorLogsController::class, 'getDeliveLogs']);
Route::get('alarm_reports', [DeviceSensorLogsController::class, 'getAlarmReports']);



Route::get('delete_alarm_device_logs', [DeviceSensorLogsController::class, 'deleteOneMonthOldLogs']);
