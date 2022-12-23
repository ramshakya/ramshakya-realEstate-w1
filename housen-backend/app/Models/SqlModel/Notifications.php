<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $table = "Notifications";
    protected $fillable = [
            "id",
            "ContactName",
            "Email",
            "Phone",
            "AgentId",
            "Message",
            "Url",
            "Ip",
            "PageFrom" ,
            "BestTimeToCall",
            "PurchasePrice" ,
            "StatusId",
            "PropertyId",
            "LeadId",
            "ScheduleAShowing",
            "PropertyAddress",
            "createdAt",
            "updatedAt",
            "subject",
    ];
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
}
?>
