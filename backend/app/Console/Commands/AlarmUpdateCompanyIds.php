<?php

namespace App\Console\Commands;

use App\Http\Controllers\Alarm\DeviceSensorLogsController;
use App\Http\Controllers\AlarmLogsController;
use App\Http\Controllers\CompanyController;
use App\Models\AttendanceLog;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyIfLogsDoesNotGenerate;
use Illuminate\Support\Facades\DB;

class AlarmUpdateCompanyIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:alarm_update_company_ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Alarm Company Ids';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo (new DeviceSensorLogsController)->updateCompanyIds();
    }
}
