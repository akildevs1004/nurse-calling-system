<?php

namespace App\Console\Commands;

use App\Http\Controllers\DeviceController;
use App\Models\Company;
use App\Models\Device;
use Illuminate\Console\Command;

// use Illuminate\Support\Facades\Log as Logger;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\NotifyIfLogsDoesNotGenerate;

class CheckDeviceHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:check_device_health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Device Health';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        echo   Device::where("company_id", ">", 0)->update(["status_id" => 2]);
    }

    public function checkSDKServerStatus($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $httpCode;
    }
}
