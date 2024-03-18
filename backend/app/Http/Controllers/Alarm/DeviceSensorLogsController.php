<?php

namespace App\Http\Controllers\Alarm;

use App\Http\Controllers\Controller;
use App\Models\Alarm\DeviceSensorLogs as AlarmDeviceSensorLogs;
use App\Models\Company;
use App\Models\Device;
use App\Models\DevicesAlarmLogs;
use App\Models\DevicesCategories;
use App\Models\DeviceSensorLogs;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as Logger;
use Ramsey\Uuid\Type\Integer;

class DeviceSensorLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function getAlarmReports(Request $request)
    {
        $model = DevicesAlarmLogs::with(["device.category"])->where("company_id", $request->company_id)
            ->where("company_id", $request->company_id);

        if ($request->filled("device_serial_number")) {
            $model->where("serial_number", $request->device_serial_number);
        }
        if ($request->filled("from_date")) {
            $model->whereDate("alarm_start_datetime", '>=', $request->from_date);
        }
        if ($request->filled("to_date")) {
            $model->whereDate("alarm_end_datetime", '<=', $request->to_date);
        }

        $model->Orwhere("alarm_end_datetime", null);
        // if ($request->filled("filter_alarm_status")) {

        //     $model->where(function ($query) use ($request) {
        //         $query->where("alarm_status", $request->filter_alarm_status);
        //     });
        // }
        // if ($request->filled("filter_battery")) {

        //     $model->where(function ($query) use ($request) {
        //         $query->where("battery", '>=', $request->filter_battery);
        //     });
        // }


        // { name: `Smoke  Only`, value: `1` },
        // { name: `Water  Only`, value: `2` },
        // { name: `Door  Only`, value: `3` },
        // { name: `C Power  Only`, value: `4` },

        $model->when(
            $request->filled('sortBy'),
            function ($q) use ($request) {
                $sortDesc = $request->input('sortDesc'); {
                    $q->orderBy($request->sortBy . "", $sortDesc == 'true' ? 'desc' : 'asc'); {
                    }
                }
            }
        );

        if (!$request->sortBy) {
            $model->orderBy('alarm_start_datetime', 'DESC');
        }



        return    $model->paginate($request->per_page);
    }

    public function getDeliveLogs(Request $request)
    {
        $model = AlarmDeviceSensorLogs::with(["device.category"])->where("company_id", $request->company_id)
            ->where("company_id", $request->company_id);

        if ($request->filled("device_serial_number")) {
            $model->where("serial_number", $request->device_serial_number);
        }
        if ($request->filled("from_date")) {
            $model->where("log_time", '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->filled("to_date")) {
            $model->where("log_time", '<=', $request->to_date . ' 23:59:59');
        }
        if ($request->filled("filter_alarm_status")) {

            $model->where(function ($query) use ($request) {
                $query->where("alarm_status", $request->filter_alarm_status);
            });
        }
        if ($request->filled("filter_battery")) {

            $model->where(function ($query) use ($request) {
                $query->where("battery", '>=', $request->filter_battery);
            });
        }


        // { name: `Smoke  Only`, value: `1` },
        // { name: `Water  Only`, value: `2` },
        // { name: `Door  Only`, value: `3` },
        // { name: `C Power  Only`, value: `4` },

        $model->when(
            $request->filled('sortBy'),
            function ($q) use ($request) {
                $sortDesc = $request->input('sortDesc'); {
                    $q->orderBy($request->sortBy . "", $sortDesc == 'true' ? 'desc' : 'asc'); {
                    }
                }
            }
        );

        if (!$request->sortBy) {
            $model->orderBy('log_time', 'DESC');
        }



        return    $model->paginate($request->per_page);
    }
    public function getDeviceTodayHourlyAlarmsRequest(Request $request)
    {

        $date = date('Y-m-d');
        if ($request->filled("from_date")) {
            $date = $request->from_date;
        }
        $HouryData = $this->getDeviceTodayHourlyAlarms($request->company_id, $request->device_serial_number, $date);

        return [

            "houry_data" => $HouryData
        ];
    }
    public function getDeviceTodayHourlyHumidity(Request $request)
    {

        $date = date('Y-m-d');
        if ($request->filled("from_date")) {
            $date = $request->from_date;
        }
        $HouryData = $this->getHumidityHourlyData($request->company_id, $request->device_serial_number, $date);

        return [

            "houry_data" => $HouryData
        ];
    }
    public function getDeviceLatestTemperature(Request $request)
    {
        $temperature_latest = '--';
        $temperature_date_time = '--';
        $temperature_min = '--';
        $temperature_max = '--';
        $temperature_min_date_time = '--';
        $temperature_max_date_time = '--';
        $fire_alarm_start_datetime = '--';
        $smoke_open_start_datetime = '--';
        $water_alarm_start_datetime = '--';
        $power_alarm_start_datetime = '--';
        $door_open_start_datetime = '--';

        $temparature_alarm_status = '--';
        $fire_alarm_status = '--';
        $smoke_alarm_status = '--';
        $water_alarm_status = '--';
        $power_alarm_status = '--';




        $humidity_latest = '--';
        $humidity_date_time = '--';
        $humidity_min = '--';
        $humidity_max = '--';
        $humidity_min_date_time = '--';
        $humidity_max_date_time = '--';



        $date = date('Y-m-d');


        $model = AlarmDeviceSensorLogs::where("company_id", $request->company_id)
            // ->where("serial_number", $request->device_serial_number)
            ->where("serial_number", $request->device_serial_number)

            ->where("temparature", '>', 0)
            ->where("temparature",  "!=", "NaN")



            ->whereDate("log_time", $date)
            ->orderBy("log_time", "DESC")
            ->first();


        if ($model) {
            $temperature_latest = $model->temparature;
            $temperature_date_time = $model->log_time;

            $humidity_latest = $model->humidity;
            $humidity_date_time = $model->log_time;
        }

        //----
        $model =   AlarmDeviceSensorLogs::where("company_id", $request->company_id)
            ->where("serial_number", $request->device_serial_number)
            ->whereDate("log_time", $date);
        $temperature  = $model->clone()->where(
            'temparature',
            '=',
            AlarmDeviceSensorLogs::where("company_id", $request->company_id)
                ->where("serial_number", $request->device_serial_number)
                ->where("temparature", "!=", "0.0")
                ->where("temparature", "!=", "NaN")
                ->whereDate("log_time", $date)->min('temparature')
        )->first();
        if ($temperature) {
            $temperature_min = $temperature->temparature;
            $temperature_min_date_time = $temperature->log_time;
        }
        //----------------
        $temperature =  $temperature  = $model->clone()->where(
            'temparature',
            '=',
            AlarmDeviceSensorLogs::where("company_id", $request->company_id)
                ->where("serial_number", $request->device_serial_number)
                ->where("temparature", "!=", "0.0")
                ->where("temparature", "!=", "NaN")
                ->whereDate("log_time", $date)->max('temparature')
        )->first();
        if ($temperature) {
            $temperature_max = $temperature->temparature;
            $temperature_max_date_time = $temperature->log_time;
        }

        //-------Humidity 
        //----
        $model =   AlarmDeviceSensorLogs::where("company_id", $request->company_id)
            ->where("serial_number", $request->device_serial_number)
            ->where("humidity", '>', 0)
            ->where("humidity", "!=", "NaN")
            ->whereDate("log_time", $date);
        $humidity  = $model->clone()->where(
            'humidity',
            '=',
            AlarmDeviceSensorLogs::where("company_id", $request->company_id)
                ->where("serial_number", $request->device_serial_number)
                ->where("humidity", "!=", "0.0")
                ->where("humidity", "!=", "NaN")
                ->whereDate("log_time", $date)->min('humidity')
        )->first();
        if ($humidity) {
            $humidity_min = $humidity->humidity;
            $humidity_min_date_time = $humidity->log_time;
        }
        //----------------
        $humidity =  $humidity  = $model->clone()->where(
            'humidity',
            '=',
            AlarmDeviceSensorLogs::where("company_id", $request->company_id)
                ->where("serial_number", $request->device_serial_number)
                ->where("humidity", "!=", "0.0")
                ->where("humidity", "!=", "NaN")
                ->whereDate("log_time", $date)->max('humidity')
        )->first();
        if ($humidity) {
            $humidity_max = $humidity->humidity;
            $humidity_max_date_time = $humidity->log_time;
        }


        //last alarm 

        $DeviceArray = Device::where("company_id", $request->company_id)
            ->where("serial_number", $request->device_serial_number)
            ->get();
        $device = [];
        if (isset($DeviceArray[0])) {
            $fire_alarm_start_datetime = $DeviceArray[0]->fire_alarm_start_datetime;
            $device = $DeviceArray[0];
        }
        return [
            "temperature_latest" =>   $temperature_latest,
            "temperature_date_time" => $temperature_date_time,
            "temperature_min" => $temperature_min,
            "temperature_max" => $temperature_max,
            "temperature_min_date_time" => $temperature_min_date_time,
            "temperature_max_date_time" => $temperature_max_date_time,



            "humidity_latest" =>   $humidity_latest,
            "humidity_date_time" => $humidity_date_time,
            "humidity_min" => $humidity_min,
            "humidity_max" => $humidity_max,
            "humidity_min_date_time" => $humidity_min_date_time,
            "humidity_max_date_time" => $humidity_max_date_time,
            "device" => $device,


        ];
    }
    public function getHumidityHourlyData($company_id, $device_serial_number, $date)
    {
        $finalarray = [];

        for ($i = 0; $i < 24; $i++) {

            $j = $i;

            $j = $i <= 9 ? "0" . $i : $i;

            //$date = date('Y-m-d'); //, strtotime(date('Y-m-d') . '-' . $i . ' days'));
            $model = AlarmDeviceSensorLogs::where('company_id', $company_id)
                ->where("serial_number", $device_serial_number)
                ->where("humidity", "!=", "NaN")
                ->where('log_time', '>=', $date . ' ' . $j . ':00:00')
                ->where('log_time', '<=', $date  . ' ' . $j . ':59:59')
                ->avg("humidity");

            $finalarray[] = [
                "date" => $date,
                "hour" => $i,
                "count" => $model == null ? 0 : round((int)$model, 2),

            ];
        }


        return  $finalarray;
    }
    public function getDeviceTodayHourlyAlarms($company_id, $device_serial_number, $date)
    {
        $finalarray = [];

        for ($i = 0; $i < 24; $i++) {

            $j = $i;

            $j = $i <= 9 ? "0" . $i : $i;

            // $date = date('Y-m-d'); //, strtotime(date('Y-m-d') . '-' . $i . ' days'));
            $model = DevicesAlarmLogs::where('company_id', $company_id)


                ->where('alarm_start_datetime', '>=', $date . ' ' . $j . ':00:00')
                ->where('alarm_start_datetime', '<=', $date  . ' ' . $j . ':59:59')
                ->count();

            $modelBattery = AlarmDeviceSensorLogs::where('company_id', $company_id)

                ->where('log_time', '>=', $date . ' ' . $j . ':00:00')
                ->where('log_time', '<=', $date  . ' ' . $j . ':59:59')
                ->where('battery', '<=', 10)->count();

            $finalarray[] = [
                "date" => $date,
                "hour" => $i,
                "count" => $model,
                "batteryCount" => $modelBattery,




            ];
        }


        return  $finalarray;
    }

    public function getDeviceDeviceAlarms(Request $request)
    {



        $logs = DevicesAlarmLogs::with(["device.category"])
            ->where('company_id', $request->company_id)
            ->where('alarm_start_datetime', '>=', $request->date_from . ' 00:00:00')
            ->where('alarm_start_datetime', '<=', $request->date_to . ' 23:59:59')

            ->get();

        // Group logs by category name
        $countByDevice = $logs->groupBy('device.name')
            ->map(function ($group) {
                return $group->count();
            });

        // Fetch all categories
        $Devices = Device::get();

        // Initialize data array
        $data = [];

        // Loop through categories and populate data array with counts
        foreach ($Devices as $device) {
            $categoryName = $device->name;
            $count = $countByDevice->has($categoryName) ? $countByDevice[$categoryName] : 0;
            $data[] = ["category" => $categoryName, "count" => $count];
        }
        usort($data, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        return  $data;
    }

    public function getDeviceCategoryAlarms(Request $request)
    {



        $logs = DevicesAlarmLogs::with(["device.category"])
            ->where('company_id', $request->company_id)
            ->where('alarm_start_datetime', '>=', $request->date_from . ' 00:00:00')
            ->where('alarm_start_datetime', '<=', $request->date_to . ' 23:59:59')

            ->get();

        // Group logs by category name
        $countByCategory = $logs->groupBy('device.category.name')
            ->map(function ($group) {
                return $group->count();
            });

        // Fetch all categories
        $categories = DevicesCategories::get();

        // Initialize data array
        $data = [];

        // Loop through categories and populate data array with counts
        foreach ($categories as $category) {
            $categoryName = $category->name;
            $count = $countByCategory->has($categoryName) ? $countByCategory[$categoryName] : 0;
            $data[] = ["category" => $categoryName, "count" => $count];
        }
        usort($data, function ($a, $b) {
            return $b['count'] - $a['count'];
        });
        return  $data;
    }
    public function getDeviceAlarmsStatics(Request $request)
    {
        $finalarray = [];
        $dateStrings = [];




        $alarmCount = DevicesAlarmLogs::where('company_id', $request->company_id)
            ->where('alarm_start_datetime', '>=', $request->date_from . ' 00:00:00')
            ->where('alarm_start_datetime', '<=', $request->date_to  .  ' 23:59:59')
            ->count();

        $batteryCount = AlarmDeviceSensorLogs::where('company_id', $request->company_id)
            ->where('log_time', '>=', $request->date_from . ' 00:00:00')
            ->where('log_time', '<=', $request->date_to  .  ' 23:59:59')
            ->where('battery', '<=', 10)->count();


        $model = DevicesAlarmLogs::where('company_id', $request->company_id)
            ->where('alarm_start_datetime', '>=', $request->date_from . ' 00:00:00')
            ->where('alarm_start_datetime', '<=', $request->date_to  .  ' 23:59:59')
            ->where('alarm_end_datetime',  "!=", null);
        $avgResponse = $model->clone()
            ->avg("response_minutes");

        $fastestResponse = $model->clone()
            ->min("response_minutes");
        $slowestResponse = $model->clone()
            ->max("response_minutes");

        $finalarray  = [
            "totalCount" => $batteryCount + $alarmCount,
            "alarmCount" => $alarmCount,
            "batteryCount" => $batteryCount,
            "avgResponse" => round($avgResponse, 0),
            "fastestResponse" => $fastestResponse,
            "slowestResponse" => $slowestResponse,


        ];



        return  $finalarray;
    }
    public function getDeviceTodayMonthlyAlarms(Request $request)
    {
        $finalarray = [];
        $dateStrings = [];
        if ($request->has("date_from") && $request->has("date_to")) {
            // Usage example:
            $startDate = new DateTime($request->date_from); // Replace with your start date
            $endDate = new DateTime($request->date_to);   // Replace with your end date

            $dateStrings = $this->createDateRangeArray($startDate, $endDate);
        }

        foreach ($dateStrings as $key => $value) {

            $date = $value;

            // $date = date('Y-m-d'); //, strtotime(date('Y-m-d') . '-' . $i . ' days'));
            $model = AlarmDeviceSensorLogs::where('company_id', $request->company_id)

                ->where('log_time', '>=', $date . ' 00:00:00')
                ->where('log_time', '<=', $date  .  ' 23:59:59')
                ->where('alarm_status', 1)->count();

            $modelBattery = AlarmDeviceSensorLogs::where('company_id', $request->company_id)


                ->where('log_time', '>=', $date . ' 00:00:00')
                ->where('log_time', '<=', $date  .  ' 23:59:59')
                ->where('battery', '<=', 10)->count();

            $finalarray[] = [
                "date" => $date,
                "count" => $model,
                "batteryCount" => $modelBattery,




            ];
        }


        return  $finalarray;
    }
    function createDateRangeArray($startDate, $endDate)
    {
        $dateStrings = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            $dateStrings[] = $currentDate->format('Y-m-d'); // Change the format as needed
            $currentDate->modify('+1 day');
        }

        return $dateStrings;
    }
    public function UpdateCompanyIds()
    {
        $date = date("Y-m-d H:i:s");

        $model = AlarmDeviceSensorLogs::query();
        $model->distinct('serial_number');
        $model->where("company_id", 0);

        $model->whereHas('device', function ($query) {
            $query->where('company_id', '!=', 0);
        });

        $model->take(100);
        $model->with("device");
        $rows = $model->get(["serial_number"]);

        if (count($rows) == 0) {
            return "[" . $date . "] Cron: UpdateCompanyIds. No new record found while updating company ids for device.\n";
        }

        $i = 0;

        foreach ($rows as $arr) {

            try {
                $i++;
                AlarmDeviceSensorLogs::where("serial_number", $arr["serial_number"])->update([
                    "company_id" => $arr["device"]["company_id"] ?? 0,

                ]);
            } catch (\Throwable $th) {
                Logger::channel("custom")->error('Cron: UpdateCompanyIds. Error Details: ' . $th);

                $data = [
                    'title' => 'Quick action required',
                    'body' => $th,
                ];

                return "[" . $date . "] Cron: UpdateCompanyIds. Error occured while updating company ids.\n";
            }
        }

        return "[" . $date . "] Cron: UpdateCompanyIds. $i Logs has been merged with Company IDS.\n"; //."Details: " . json_encode($result) . ".\n";

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeviceSensorLogs  $deviceSensorLogs
     * @return \Illuminate\Http\Response
     */
    public function show(AlarmDeviceSensorLogs $deviceSensorLogs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeviceSensorLogs  $deviceSensorLogs
     * @return \Illuminate\Http\Response
     */
    public function edit(AlarmDeviceSensorLogs $deviceSensorLogs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeviceSensorLogs  $deviceSensorLogs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AlarmDeviceSensorLogs $deviceSensorLogs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeviceSensorLogs  $deviceSensorLogs
     * @return \Illuminate\Http\Response
     */
    public function destroy(AlarmDeviceSensorLogs $deviceSensorLogs)
    {
        //
    }

    public function deleteOldLogs()
    {
        try {

            //delete duplicate 5 days before old logs 
            $this->deleteOld5DaysLogs();
        } catch (\Exception $e) {
        }
        try {
            $this->deleteOneMonthOldLogs();
        } catch (\Exception $e) {
        }
    }
    public function deleteOneMonthOldLogs()
    {

        //Fetch 30minutes logs and keep one record for every 30 minutes with alarm
        //Deleting records which has no alarm vaue 

        $date =  date("Y-m-d", strtotime('-30 days'));
        $startTime = new DateTime($date . "00:00:00"); // Current date and time
        $endTime = new DateTime($date . "23:59:59");; // Display for the next 24 hours

        $interval = new DateInterval('PT30M'); // 30 minutes interval
        $period = new DatePeriod($startTime, $interval, $endTime);

        $companies = Company::get();


        $finalDuplicateIds = [];
        foreach ($companies as $company) {

            foreach ($period as $dt) {
                $filter_from_date = $dt->format('Y-m-d H:i:s');

                $filter_to_datetime = $dt;
                $filter_to_datetime = $filter_to_datetime->modify('+30 minutes'); // Add 5 minutes to the current date and time
                $filter_to_datetime = $filter_to_datetime->format('Y-m-d H:i:s');


                $logs = AlarmDeviceSensorLogs::where("company_id", $company->id)
                    ->where("water_leakage", 0)
                    ->where("power_failure", 0)
                    ->where("door_status", 0)
                    ->where("smoke_alarm", 0)
                    ->where("fire_alarm", 0)
                    ->where("log_time", ">=",  $filter_from_date)
                    ->where("log_time", "<",  $filter_to_datetime);

                $deleteIds = $logs->get()->pluck('id')->toArray();

                array_shift($deleteIds);


                $finalDuplicateIds = array_merge($finalDuplicateIds, $deleteIds);
            }
        }


        if (count($finalDuplicateIds))
            AlarmDeviceSensorLogs::whereIn("id", $finalDuplicateIds)->delete();

        return $finalDuplicateIds;
    }
    public function deleteOld5DaysLogs()
    {

        //delete duplicate 5 days before old logs 

        $date = date("Y-m-d", strtotime('-5 days'));
        $return = [];


        $companies = Company::get();

        $finalDuplicateIds = [];
        foreach ($companies as $company) {

            $logs = AlarmDeviceSensorLogs::where("company_id", $company->id)

                ->where("water_leakage", 0)
                ->where("power_failure", 0)
                ->where("door_status", 0)
                ->where("smoke_alarm", 0)
                ->where("fire_alarm", 0)
                ->where("log_time", ">=",  $date . ' 00:00:00')
                ->where("log_time", "<",  $date . ' 23:59:59');

            $logs = $logs->get();


            $uniqueCombinations = [];
            $duplicateCombinations = [];

            foreach ($logs as $log) {

                $key = $log['serial_number'] . '_' . date("Y-m-d H:i", strtotime($log['log_time'])) . '-' . $log['smoke_status'] . '_' . $log['water_leakage'] . '_' . $log['power_failure'] . '_' . $log['door_status'];
                if (isset($uniqueCombinations[$key])) {
                    $duplicateCombinations[] =   $log['id'];
                } else {

                    $uniqueCombinations[$key] =  $log['id'];
                }
            }

            $finalDuplicateIds = array_merge($finalDuplicateIds, $duplicateCombinations);

            // }
        }

        if (count($finalDuplicateIds))
            AlarmDeviceSensorLogs::whereIn("id", $finalDuplicateIds)->delete();

        return  $finalDuplicateIds;
    }
}
