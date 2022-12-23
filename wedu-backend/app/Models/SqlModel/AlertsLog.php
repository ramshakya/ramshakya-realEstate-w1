<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertsLog extends Model
{
    use HasFactory;
    // SavedSearchFilter
    protected $table = "AlertsLog";
    public $timestamps = false;
    protected $fillable = [
        "alertId",
        "userId",
        "toEmail",
        "toPhone",
        "alertFreq",
        "emailContent",
        "smsContent",
        "sentAt",
        "agent_id",
        "camp_id",
        "template_id",
        "camp_name",
        "subject"
    ];

}
