<?php

namespace App\Http\Controllers\Alarm\Api;

use App\Console\Commands\SendWhatsappNotification;
use App\Http\Controllers\Alarm\DeviceSensorLogsController;
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
        Storage::append("logs/nurse-calling-system/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode($request->all()));

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

        // (new DeviceSensorLogsController)->updateCompanyIds();

        //  $devicesList = Device::where("serial_number", "24000003")->get();
        $log[] = $this->updateDuration($devicesList);
        $log[] = $this->updateAlarmStartDatetime($devicesList);
        $log[] =   $this->updateAlarmEndDatetime($devicesList);

        return $log;
    }
    public function updateAlarmEndDatetime($devicesList)
    {
        $message = [];
        foreach ($devicesList as $key => $device) {

            $alarmData =  DevicesAlarmLogs::where("serial_number", $device['serial_number'])
                ->where("alarm_end_datetime", null)
                ->orderBy("alarm_start_datetime", "ASC")
                ->first();
            if (isset($alarmData["id"])) {


                $currentDateTime = date("Y-m-d H:i:s");

                $alarm_start_datetime = $alarmData['alarm_start_datetime'];
                //if new Log is available
                $logsNewAlarmInitiated = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                    ->where("company_id", '>', 0)
                    ->where("time_gap_seconds", "!=", null)
                    ->where("time_gap_seconds", '>', 30)
                    ->where("log_time", '>',  $alarmData['alarm_start_datetime'])
                    // ->where("log_time", '<=', date("Y-m-d H:i:s", strtotime("-30 seconds")))  //wait for 1 minute to close the Alram 
                    ->orderBy("log_time", "DESC")

                    ->first();

                $message["logsNewAlarmInitiated"] = $logsNewAlarmInitiated;


                $logs = null;
                if (isset($logsNewAlarmInitiated['log_time'])) {
                    $dateNewLogTime = $logsNewAlarmInitiated["log_time"];

                    $logs = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                        ->where("company_id", '>', 0)
                        ->where("time_gap_seconds", "!=", null)
                        ->where("log_time", '<',  $dateNewLogTime)
                        ->orderBy("log_time", "DESC")
                        ->first();


                    //$currentDateTime = $logs['log_time'];


                } else {
                    $logs = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                        ->where("company_id", '>', 0)
                        ->where("time_gap_seconds", "!=", null)
                        ->where("time_gap_seconds", '<', 30)
                        ->where("log_time", '>',  $alarmData['alarm_start_datetime'])
                        ->orderBy("log_time", "DESC")
                        ->first();
                }




                if (isset($logs['log_time'])) {
                    $message[" NewAlarmInitiated"] = $logs['log_time'];

                    $currentDateTime = date("Y-m-d H:i:s");;
                    // $logTime = $logs['log_time'];

                    $datetime1 = new DateTime($logs['log_time']);
                    $datetime2 = new DateTime($alarm_start_datetime);
                    //   return [$datetime1, $datetime2];
                    $interval = $datetime1->diff($datetime2);
                    $secondsDifference = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);
                    //if ($secondsDifference > 70 || $currentDateTime == $alarm_start_datetime)
                    { //as per cron job have to wait 1 minute

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
                            ->where("time_gap_seconds", "!=", null)
                            ->where("log_time", '<=', $logs['log_time'])
                            ->where("verified", false)->update(["verified" => true]);
                        $data = [
                            "alarm_status" => 0,
                            "alarm_end_datetime" => $logs['log_time'],
                        ];

                        Device::where("serial_number", $logs['serial_number'])->update($data);

                        $this->SendWhatsappNotification($device['name'] . " - Alarm Stopped ",   $device['name'],  $device, $logs['log_time'], true);

                        $message[" SendWhatsappNotification"] = "Notification Sent";
                    }
                }
            } else {
                $message[] = "No Open Alarms ";
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
                ->where("time_gap_seconds", "!=", null)
                ->where(function ($query) use ($key) {
                    $query->where("time_gap_seconds",  null);

                    $query->Orwhere("time_gap_seconds", '>=', 30);
                })

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
                    ->where("time_gap_seconds", "!=", null)
                    ->where("verified", false)->update(["verified" => true]);
                $data = [
                    "alarm_status" => 1,
                    "alarm_start_datetime" => $logs['log_time'],
                    "alarm_end_datetime" => null
                ];

                Device::where("serial_number", $logs['serial_number'])->update($data);

                $this->SendWhatsappNotification($device['name'] . " - Alarm Started ",   $device['name'],  $device, $logs['log_time'], true);
            }
        }
    }
    public function updateDuration($devicesList)
    {



        foreach ($devicesList as $key => $device) {


            $data = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                ->where("time_gap_seconds", null)
                ->where("company_id", '>', 0)
                ->where("log_time", '<=',  date("Y-m-d H:i:s", strtotime("-30 seconds")))
                ->orderBy("log_time", "DESC")
                ->get();

            for ($i = 0; $i < count($data); $i++) {

                $currentLog = $data[$i];
                //$previousLogTime = isset($data[$i + 1]) ? $data[$i + 1]['log_time'] : date("Y-m-d H:i:0", strtotime("-10 minutes"));
                //$previousLogTime = isset($data[$i + 1]) ? $data[$i + 1]['log_time'] : false;
                $previousLogTime = false;
                if (isset($data[$i + 1])) {
                    $previousLogTime =   $data[$i + 1]['log_time'];
                } else {
                    $fisrtRecord = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                        ->where("company_id", '>', 0)
                        ->orderBy("log_time", "ASC")
                        ->first();

                    if ($fisrtRecord["id"] == $currentLog["id"]) {
                        $previousLogTime = date("Y-m-d H:i:0", strtotime("-10 minutes"));
                    } else {

                        $previousRecord = DeviceSensorLogs::where("serial_number", $device['serial_number'])
                            ->where("company_id", '>', 0)
                            ->where("id", "<", $currentLog["id"])
                            ->orderBy("log_time", "DESC")
                            ->first();
                        $previousLogTime = $previousRecord["log_time"];
                    }
                }
                if ($previousLogTime) {

                    $latestLogTime = $currentLog['log_time'];
                    //$previousLogTime = $previousLog['log_time'];

                    $datetime1 = new DateTime($previousLogTime);
                    $datetime2 = new DateTime($latestLogTime);

                    $interval = $datetime1->diff($datetime2);
                    $secondsDifference = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);

                    //return [$secondsDifference, $latestLogTime, $nextLogTime];
                    DeviceSensorLogs::where('id', $currentLog['id'])->update(["time_gap_seconds" => $secondsDifference]);
                }
            }
        }
    }
    public function readStatus($device_serial_number, $alarm_status, $battery)
    {

        $message = [];
        //try {
        $insertedId = 0;

        $log_time = date('Y-m-d H:i:s');

        $PreviousRecord = null;

        if ($device_serial_number != '') {


            $logs["serial_number"] = $device_serial_number;

            $logs["alarm_status"] = $alarm_status;

            $logs["battery"] = $battery; //== 1 ? 0 : 1;



            $logs["log_time"] = $log_time;
            try {
                $insertedObj = DeviceSensorLogs::create($logs);


                // $first_log = DeviceSensorLogs::where("serial_number", $device_serial_number)->orderBy("log_time", "asc")->first();
                // if($first_log["id"]==$insertedId )
                // {

                // }
            } catch (\Exception $e) {
            }
            $deviceModel = Device::where("serial_number", $device_serial_number);

            if (count($deviceModel->clone()->get()) == 0) {
                return $this->response('Device Information is not available', null, false);
            } else {

                $company_id = $deviceModel->clone()->get()[0]['company_id'];

                DeviceSensorLogs::where("id", $insertedObj->id)->update(["company_id" => $company_id]);
            }

            $row = [];

            $deviceObj = $deviceModel->clone()->get()[0];


            //alarm_status
            $message[] = $alarm_status;

            if ($deviceObj) {
                if ($alarm_status == 1) {
                    $ignore15Minutes = false;



                    if ($deviceObj['alarm_status'] == 0) {

                        $ignore15Minutes = true;
                        $row = [];
                        $row["alarm_status"] = $alarm_status;
                        $row["alarm_start_datetime"] = $log_time;
                        $row["alarm_end_datetime"] = null;
                        $deviceModel->clone()->update($row);
                    }
                    $message["whatsapp_response"] =  $this->SendWhatsappNotification($deviceObj['name'] . " - Alarm Triggered ",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time, $ignore15Minutes);
                } else if ($alarm_status == 0) {


                    if ($deviceObj['alarm_status'] == 1) {
                        $ignore15Minutes = true;
                        $message["whatsapp_response"] = $this->SendWhatsappNotification($deviceObj['name'] . " - Alarm Stopped ",   $deviceModel->clone()->first()->name, $deviceModel->clone()->first(), $log_time,  $ignore15Minutes);
                        $row = [];
                        $row["alarm_status"] = $alarm_status;
                        $row["alarm_end_datetime"] = $log_time;

                        $deviceModel->clone()->where("alarm_status", 1)->update($row);
                    }
                }
            }
            // try {
            (new ApiAlarmControlController)->updateAlarmResponseTime();
            // } catch (\Exception $e) {
            // }
        }
        // } catch (\Exception $e) {
        //     Storage::append("logs/nurse-calling-system/api-requests-device-" . date('Y-m-d') . ".txt", date("Y-m-d H:i:s") .  " : "    . json_encode([$device_serial_number, $alarm_status, $battery]) . ' \n' . $e->getMessage());

        //     return  $e->getMessage();
        // }
        return $this->response('Successfully Updated', $message, true);
        return $this->response('Data error', null, false);
    }


    public function SendWhatsappNotification($issue, $room_name, $model1, $date, $ignore15Minutes)
    {

        $company_id = $model1->company_id;
        $branch_id = $model1->branch_id;

        //$reports = ReportNotification::where("company_id", $model1->company_id)->where("branch_id", $model1->branch_id)->get();
        $reports = ReportNotification::with(["managers.branch",  "company.company_mail_content"])


            ->with("managers", function ($query) use ($company_id, $branch_id) {
                $query->where("company_id", $company_id);
                $query->where("branch_id", $branch_id);
            })->get();
        foreach ($reports as $model) {

            $id = $model["id"];

            $script_name = "ReportNotificationCrons";



            if ($model)
                if (in_array("Email", $model->mediums)) {



                    foreach ($model->managers as $key => $value) {
                        $minutesDifference = 1000;

                        //wait 5 minutes to send notification
                        $notificationSentLogs = ReportNotificationLogs::where("notification_id", $value->notification_id)
                            ->where("notification_manager_id", $value->id)
                            ->where("email", $value->email)
                            ->orderBy("created_at", "DESC")->first();

                        if ($notificationSentLogs) {
                            $datetime1 = new DateTime(date("Y-m-d H:i"));
                            $datetime2 = new DateTime($notificationSentLogs["created_at"]);

                            $interval = $datetime1->diff($datetime2);
                            $minutesDifference =  $interval->i + ($interval->h * 60) + ($interval->days * 1440);
                        }


                        if ($minutesDifference >=   15 || $ignore15Minutes) { // 




                            $branch_name = $value->branch->branch_name;

                            $body_content1 = "📊 *{$issue} Notification <br/>";

                            $body_content1 .= " Hello, {$value->name} <br/>";
                            $body_content1 .= "Company:  {$model->company->name}<br/>";
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
                    }
                } else {
                    echo "[" . $date . "] Cron: $script_name. No emails are configured";
                }

            //wahtsapp with attachments
            if (in_array("Whatsapp", $model->mediums)) {

                foreach ($model->managers as $key => $manager) {
                    $minutesDifference = 1000; //minutes
                    //wait 5 minutes to send notification
                    $notificationSentLogs = ReportNotificationLogs::where("notification_id", $manager->notification_id)
                        ->where("notification_manager_id", $manager->id)
                        ->where("whatsapp_number", $manager->whatsapp_number)
                        ->orderBy("created_at", "DESC")->first();
                    $minutesDifference = 1000; //minutes
                    if ($notificationSentLogs) {
                        $datetime1 = new DateTime(date("Y-m-d H:i"));
                        $datetime2 = new DateTime($notificationSentLogs["created_at"]);
                        $interval = $datetime1->diff($datetime2);
                        $minutesDifference =  $interval->i + ($interval->h * 60) + ($interval->days * 1440);
                    }



                    if ($minutesDifference >=  15   || $ignore15Minutes) { // 


                        $branch_name = $manager->branch->branch_name;

                        if ($manager->whatsapp_number != '') {


                            $body_content1 = "📊 *{$issue} Notification* 📊\n\n";

                            $body_content1 .= "*Hello, {$manager->name}*\n\n";
                            $body_content1 .= "*Company:  {$model->company->name}*\n\n";
                            $body_content1 .= "*This is Notifing you about {$issue}*\n\n";
                            $body_content1 .= "Date:  $date\n";
                            $body_content1 .= "Room Name:  {$room_name}\n";
                            $body_content1 .= "Branch:  {$room_name}\n";
                            $body_content1 .= "Branch:  {$branch_name}\n";
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
}
