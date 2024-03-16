<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicesAlarmLogs extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function device()
    {
        return $this->belongsTo(Device::class, "serial_number", "serial_number")->withDefault(["name" => "Manual", "serial_number" => "Manual"]);
    }
}
