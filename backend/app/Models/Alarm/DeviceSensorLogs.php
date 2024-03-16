<?php

namespace App\Models\Alarm;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Device;
use App\Models\DeviceStatus;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSensorLogs extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function device()
    {
        return $this->belongsTo(Device::class, "serial_number", "serial_number")->withDefault(["name" => "Manual", "serial_number" => "Manual"]);
    }
}
