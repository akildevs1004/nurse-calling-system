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
use App\Models\Alarm\DeviceSensorLogs;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class AlarmDeleteOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:delete_old_logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete_old_logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new DeviceSensorLogsController)->deleteOldLogs();
    }
}
