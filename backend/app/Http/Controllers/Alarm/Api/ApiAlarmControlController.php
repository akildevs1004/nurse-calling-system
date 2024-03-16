<?php

namespace App\Http\Controllers\Alarm\Api;

use App\Console\Commands\SendWhatsappNotification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsappController;
use App\Mail\DbBackupMail;
use App\Mail\EmailContentDefault;
use App\Mail\ReportNotificationMail;
use App\Models\Alarm\DeviceSensorLogs;
use Illuminate\Http\Request;
use App\Models\Community\AttendanceLog;
use App\Models\Company;
use App\Models\Device;
use App\Models\DevicesAlarmLogs;
use App\Models\DevicesCategories;
use App\Models\ReportNotification;
use App\Models\ReportNotificationLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;
use Illuminate\View\View;

class ApiAlarmControlController extends Controller
{
    public function LogDeviceStatus(Request $request)
    {

        $alarm_status = -1;
        $battery = 100;
        Storage::append("logs/alarm/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode($request->all()));

        $device_serial_number = $request->serialNumber;
        if ($request->filled("alarm_status")) {
            $alarm_status = $request->alarm_status;
        }


        if ($request->filled("battery")) {
            $battery = $request->battery;
        }

        $alarm_status = 1; //always 1 

        if ($device_serial_number != '')
            return   $this->readStatus($device_serial_number, $alarm_status, $battery);
        else
            return $this->response('Device Information is not available', null, false);
    }

    public function updateAlarmResponseTime()
    {
        $devicesList = Device::get();
        $log[] = $this->updateDuration($devicesList);
        $log[] = $this->updateAlarmStartDatetime($devicesList);
        $log[] =   $this->updateAlarmEndDatetime($devicesList);

        return $log;
    }
    public function updateAlarmEndDatetime($devicesList)
    {

        foreach ($devicesList as $key => $device) {

            $alarmData =  DevicesAlarmLogs::where("serial_number", $device['serial_number'])
                ->where("alarm_end_datetime", null)
                ->first();
            if (isset($alarmData["id"])) {

                $logs = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                    ->where("company_id", '>', 0)
                    //->where("time_gap_seconds", '<', 30)

                    ->orderBy("log_time", "DESC")
                    ->first();


                if (isset($logs['log_time'])) {

                    $currentDateTime = date("Y-m-d H:i:s");;
                    $logTime = $logs['log_time'];

                    $datetime1 = new DateTime($currentDateTime);
                    $datetime2 = new DateTime($logTime);

                    $interval = $datetime1->diff($datetime2);
                    $secondsDifference = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);
                    if ($secondsDifference > 30) {






                        $datetime1 = new DateTime($logs['log_time']);
                        $datetime2 = new DateTime($alarmData["alarm_start_datetime"]);

                        $interval = $datetime1->diff($datetime2);

                        $minutesDifference = $interval->i + ($interval->h * 60) + ($interval->days * 1440); // i represents the minutes part of the interval


                        DevicesAlarmLogs::where("id",  $alarmData["id"])
                            ->update([
                                "alarm_end_datetime" => $logs['log_time'],
                                "response_minutes" => $minutesDifference
                            ]);

                        DeviceSensorLogs::where("serial_number", $logs['serial_number'])
                            ->where("company_id", '>', 0)
                            ->where("log_time", '<=', $logs['log_time'])
                            ->where("verified", false)->update(["verified" => true]);
                        $data = [
                            "alarm_status" => 0,
                            "alarm_end_datetime" => $logs['log_time'],
                        ];

                        Device::where("serial_number", $logs['serial_number'])->update($data);
                    }
                }
            }
        }
    }
    public function updateAlarmStartDatetime($devicesList)
    {

        $counter = 0;
        $previousLog = [];
        $currentLog = [];
        foreach ($devicesList as $key => $device) {

            $logs = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                ->where("company_id", '>', 0)
                ->where("verified", false)
                ->where("time_gap_seconds", '>=', 30)
                ->orderBy("log_time", "ASC")
                ->first();


            if (isset($logs['log_time'])) {

                $data = [
                    "company_id" => $logs['company_id'],
                    "serial_number" => $logs['serial_number'],
                    "alarm_start_datetime" => $logs['log_time'],
                ];

                DevicesAlarmLogs::create($data);
                DeviceSensorLogs::where("serial_number", $logs['serial_number'])
                    ->where("company_id", '>', 0)
                    ->where("verified", false)->update(["verified" => true]);
                $data = [
                    "alarm_status" => 1,
                    "alarm_start_datetime" => $logs['log_time'],
                    "alarm_end_datetime" => null
                ];

                Device::where("serial_number", $logs['serial_number'])->update($data);
            }
        }
    }
    public function updateDuration($devicesList)
    {

        foreach ($devicesList as $key => $device) {


            $data = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                ->where("time_gap_seconds", null)
                ->orderBy("log_time", "asc")
                ->get();

            for ($i = 0; $i < count($data); $i++) {

                $currentLog = $data[$i];
                $nextLog = isset($data[$i + 1]) ? $data[$i + 1] : false;

                if ($nextLog) {

                    $latestLogTime = $currentLog['log_time'];
                    $nextLogTime = $nextLog['log_time'];

                    $datetime1 = new DateTime($nextLogTime);
                    $datetime2 = new DateTime($latestLogTime);

                    $interval = $datetime1->diff($datetime2);
                    $secondsDifference = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);

                    //return [$secondsDifference, $latestLogTime, $nextLogTime];
                    DeviceSensorLogs::where('id', $nextLog['id'])->update(["time_gap_seconds" => $secondsDifference]);
                }
            }
        }
    }
    public function readStatus($device_serial_number, $alarm_status, $battery)
    {
        //   try {
        $insertedId = 0;

        $log_time = date('Y-m-d H:i:s');

        $PreviousRecord = null;

        if ($device_serial_number != '') {


            $logs["serial_number"] = $device_serial_number;

            $logs["alarm_status"] = $alarm_status;

            $logs["battery"] = $battery; //== 1 ? 0 : 1;



            $logs["log_time"] = $log_time;
            try {
                $insertedId = DeviceSensorLogs::create($logs);
            } catch (\Exception $e) {
            }
            $deviceModel = Device::where("serial_number", $device_serial_number);

            if (count($deviceModel->clone()->get()) == 0) {
                return $this->response('Device Information is not available', null, false);
            }

            $row = [];



            // $row["alarm_status"] = $alarm_status;
            // $row["battery_level"] = $battery;

            //battery
            if ($battery == 0) {

                $exist = $deviceModel->clone()->where("battery_level",   0)->exists();



                if (!$exist) {
                    $message[] =  $this->SendWhatsappNotification("Batery Level is 0",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time);

                    $row = [];
                    $row["battery_level"] = $battery;

                    $deviceModel->clone()->where("battery_level", '!=', 0)->update($row);
                }
            } else if ($battery > 0) {
                $row = [];
                $row["battery_level"] = $battery;

                $deviceModel->clone()->update($row);
            }
            //alarm_status
            if ($alarm_status == 1) {

                $exist = $deviceModel->clone()->where("alarm_status",   1)->exists();
                if (!$exist) {
                    $message[] =  $this->SendWhatsappNotification("Alarm Triggered ",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time);



                    $row = [];
                    $row["alarm_status"] = $alarm_status;
                    $row["alarm_start_datetime"] = $log_time;
                    $row["alarm_end_datetime"] = null;
                    $deviceModel->clone()->update($row);
                }
            } else if ($alarm_status == 0) {

                $exist = $deviceModel->clone()->where("alarm_status",   0)->exists();
                if (!$exist) {

                    $message[] =  $this->SendWhatsappNotification("Alarm Stopped ",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time);
                    $row = [];
                    $row["alarm_status"] = $alarm_status;
                    $row["alarm_end_datetime"] = $log_time;

                    $deviceModel->clone()->where("alarm_status", 1)->update($row);
                }

                // //avg response 

                // if ($PreviousRecord) {
                //     if ($PreviousRecord['alarm_status'] == 1) {

                //         $previousLogTime = $PreviousRecord['log_time'];
                //         $Time = date("Y-m-d H:i:s");

                //         $datetime1 = new DateTime($Time);
                //         $datetime2 = new DateTime($previousLogTime);

                //         $interval = $datetime1->diff($datetime2);
                //         $minutesDifference = $interval->i; // i represents the minutes part of the interval

                //         DeviceSensorLogs::where("id", $insertedId->id)->update(["response_minutes" => $minutesDifference]);
                //     }
                // }




                //$insertedId
            }


            //return [$logs, $row];

            // Device::where("serial_number", $device_serial_number)
            //     ->update($row);



            return $this->response('Successfully Updated', null, true);
        }
        // } catch (\Exception $e) {
        //     Storage::append("logs/alarm_error/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode($request->all()) . ' \n' . $e->getMessage());

        //     return  $e->getMessage();
        // }

        return $this->response('Data error', null, false);
    }
    // public function LogDeviceStatus(Request $request)
    // {


    //     //   try {
    //     Storage::append("logs/alarm/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode($request->all()));
    //     $insertedId = 0;
    //     $temparature = -1;
    //     $humidity = -1;
    //     $alarm_status = -1;
    //     $battery = 100;

    //     $log_time = date('Y-m-d H:i:s');

    //     $max_temparature = 30;
    //     $max_humidity = 50;

    //     try {

    //         $json = file_get_contents(Storage::path('alarm_rules.json'));
    //         $json_data = json_decode($json, true);

    //         $max_temparature = $json_data['max_temparature'];
    //         $max_humidity =  $json_data['max_humidity'];
    //     } catch (\Exception $e) {
    //     }

    //     $device_serial_number = $request->serialNumber;
    //     $PreviousRecord = null;

    //     if ($device_serial_number != '') {


    //         $PreviousRecord = DeviceSensorLogs::where("serial_number", $device_serial_number)

    //             ->orderBy("log_time", "DESC")
    //             ->first();




    //         if ($request->filled("alarm_status")) {
    //             $alarm_status = $request->alarm_status;
    //         }

    //         if ($request->filled("battery")) {
    //             $battery = $request->battery;
    //         }
    //         if ($PreviousRecord) {
    //             if ($alarm_status == 0 && $PreviousRecord['alarm_status'] == 1) {

    //                 $previousLogTime = $PreviousRecord['log_time'];
    //                 $Time = date("Y-m-d H:i:s");

    //                 $datetime1 = new DateTime($Time);
    //                 $datetime2 = new DateTime($previousLogTime);

    //                 $interval = $datetime1->diff($datetime2);
    //                 $minutesDifference = $interval->i; // i represents the minutes part of the interval

    //                 if ($minutesDifference <= 1) {
    //                     $alarm_status = 1;
    //                 }
    //             }
    //         }




    //         if ($temparature == "NaN") {
    //             $temparature = 0;
    //         }
    //         if ($humidity == "NaN") {
    //             $humidity = 0;
    //         }


    //         $logs["serial_number"] = $device_serial_number;
    //         $logs["temparature"] = $temparature;
    //         $logs["alarm_status"] = $alarm_status;
    //         $logs["humidity"] = $humidity;
    //         $logs["battery"] = $battery; //== 1 ? 0 : 1;



    //         $logs["log_time"] = $log_time;
    //         try {
    //             $insertedId = DeviceSensorLogs::create($logs);
    //         } catch (\Exception $e) {
    //         }
    //         $deviceModel = Device::where("serial_number", $device_serial_number);

    //         if (count($deviceModel->clone()->get()) == 0) {
    //             return $this->response('Device Information is not available', null, false);
    //         }

    //         $row = [];

    //         if ($temparature >= $max_temparature) {
    //             $row["temparature_alarm_status"] = 1;
    //             $row["temparature_alarm_start_datetime"] = $log_time;
    //         }
    //         if ($humidity >= $max_humidity) {
    //             $row["humidity_alarm_status"] = 1;
    //             $row["humidity_alarm_start_datetime"] = $log_time;
    //         }


    //         // $row["alarm_status"] = $alarm_status;
    //         // $row["battery_level"] = $battery;

    //         //battery
    //         if ($battery == 0) {

    //             $exist = $deviceModel->clone()->where("battery_level",   0)->exists();

    //             // $currentDateTime = Carbon::now();
    //             // $fiveMinutesAgo = $currentDateTime->copy()->subMinutes(5);

    //             // return  $logs = DeviceSensorLogs::where("serial_number", $device_serial_number)
    //             //     ->whereDate('log_time', '<=', $fiveMinutesAgo->toDateString())
    //             //     ->orWhere(function ($query) use ($fiveMinutesAgo) {
    //             //         $query->whereDate('log_time', $fiveMinutesAgo->toDateString())
    //             //             ->whereTime('log_time', '<=', $fiveMinutesAgo->toTimeString());
    //             //     })
    //             //     ->get();

    //             if (!$exist) {
    //                 $message[] =  $this->SendWhatsappNotification("Batery Level is 0",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time);

    //                 $row = [];
    //                 $row["battery_level"] = $battery;

    //                 $deviceModel->clone()->where("battery_level", '!=', 0)->update($row);
    //             }
    //         } else if ($battery > 0) {
    //             $row = [];
    //             $row["battery_level"] = $battery;

    //             $deviceModel->clone()->update($row);
    //         }
    //         //alarm_status
    //         if ($alarm_status == 1) {

    //             $exist = $deviceModel->clone()->where("battery_level",   1)->exists();
    //             if (!$exist) {
    //                 $message[] =  $this->SendWhatsappNotification("fire Detection",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time);



    //                 $row = [];
    //                 $row["alarm_status"] = $alarm_status;
    //                 $row["alarm_start_datetime"] = $log_time;
    //                 $deviceModel->clone()->update($row);
    //             }
    //         } else if ($alarm_status == 0) {

    //             $exist = $deviceModel->clone()->where("alarm_status",   0)->exists();
    //             if (!$exist) {
    //                 $row = [];
    //                 $row["alarm_status"] = $alarm_status;
    //                 $row["alarm_end_datetime"] = $log_time;

    //                 $deviceModel->clone()->where("alarm_status", 1)->update($row);
    //             }

    //             //avg response 

    //             if ($PreviousRecord) {
    //                 if ($PreviousRecord['alarm_status'] == 1) {

    //                     $previousLogTime = $PreviousRecord['log_time'];
    //                     $Time = date("Y-m-d H:i:s");

    //                     $datetime1 = new DateTime($Time);
    //                     $datetime2 = new DateTime($previousLogTime);

    //                     $interval = $datetime1->diff($datetime2);
    //                     $minutesDifference = $interval->i; // i represents the minutes part of the interval

    //                     DeviceSensorLogs::where("id", $insertedId->id)->update(["response_minutes" => $minutesDifference]);
    //                 }
    //             }




    //             //$insertedId
    //         }


    //         //return [$logs, $row];

    //         // Device::where("serial_number", $device_serial_number)
    //         //     ->update($row);



    //         return $this->response('Successfully Updated', null, true);
    //     }
    //     // } catch (\Exception $e) {
    //     //     Storage::append("logs/alarm_error/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode($request->all()) . ' \n' . $e->getMessage());

    //     //     return  $e->getMessage();
    //     // }

    //     return $this->response('Data error', null, false);
    // }

    public function SendWhatsappNotification($issue, $room_name, $model1, $date)
    {

        $company_id = $model1->company_id;
        $branch_id = $model1->branch_id;

        $reports = ReportNotification::where("company_id", $model1->company_id)->where("branch_id", $model1->branch_id)->get();

        foreach ($reports as $model) {
            $id = $model["id"];

            $script_name = "ReportNotificationCrons";

            // $date = date("Y-m-d H:i:s");

            // try {

            $model = ReportNotification::with(["managers.branch",  "company.company_mail_content"])->where("id", $id)


                ->with("managers", function ($query) use ($company_id, $branch_id) {
                    $query->where("company_id", $company_id);
                    $query->where("branch_id", $branch_id);
                })

                ->first();

            if ($model)
                if (in_array("Email", $model->mediums)) {



                    foreach ($model->managers as $key => $value) {

                        $branch_name = $value->branch->branch_name;

                        $body_content1 = "📊 *{$issue} Notification <br/>";

                        $body_content1 = " Hello, {$value->name} <br/>";
                        $body_content1 .= " Company:  {$model->company->name}<br/>";
                        $body_content1 .= "This is Notifing you about {$issue} status <br/>";
                        $body_content1 .= "Date:  $date<br/>";
                        $body_content1 .= "Room Name: {$room_name}<br/>";
                        $body_content1 .= "Branch: {$branch_name}<br/><br/><br/><br/>";
                        $body_content1 .= "*Xtreme Guard*<br/>";

                        $data = [
                            'subject' => "{$issue} Notification - {$date}",
                            'body' => $body_content1,
                        ];



                        $body_content1 = new EmailContentDefault($data);

                        if ($value->email != '') {
                            Mail::to($value->email)
                                ->send($body_content1);


                            $data = ["company_id" => $value->company_id, "branch_id" => $value->branch_id, "notification_id" => $value->notification_id, "notification_manager_id" => $value->id, "email" => $value->email];



                            ReportNotificationLogs::create($data);
                        }
                    }
                } else {
                    echo "[" . $date . "] Cron: $script_name. No emails are configured";
                }

            //wahtsapp with attachments
            if (in_array("Whatsapp", $model->mediums)) {

                foreach ($model->managers as $key => $manager) {

                    if ($manager->whatsapp_number != '') {


                        $body_content1 = "📊 *{$issue} Notification* 📊\n\n";

                        $body_content1 = "*Hello, {$manager->name}*\n\n";
                        $body_content1 .= "*Company:  {$model->company->name}*\n\n";
                        $body_content1 .= "This is Notifing you about {$issue} status \n\n";
                        $body_content1 .= "Date:  $date\n\n";
                        $body_content1 .= "Room Name:  {$room_name}\n\n";
                        $body_content1 .= "Branch:  {$room_name}\n\n";
                        $body_content1 .= "Branch:  {$branch_name}\n\n";
                        $body_content1 .= "*Xtreme Guard*\n";




                        if (count($model->company->company_whatsapp_content))
                            $body_content1 .= $model->company->company_whatsapp_content[0]->content;

                        (new WhatsappController())->sendWhatsappNotification($model->company, $body_content1, $manager->whatsapp_number, []);

                        $data = [
                            "company_id" => $model->company->id,
                            "branch_id" => $manager->branch_id,
                            "notification_id" => $manager->notification_id,
                            "notification_manager_id" => $manager->id,
                            "whatsapp_number" => $manager->whatsapp_number
                        ];

                        ReportNotificationLogs::create($data);
                    }
                }
            }
        }
    }
}
