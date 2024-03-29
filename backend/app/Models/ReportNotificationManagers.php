<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportNotificationManagers extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'report_notifications_manager';
    public function branch()
    {
        return $this->belongsTo(CompanyBranch::class, "branch_id", "id");
    }
}
