<?php

namespace App\Models\SqlModel;

use App\Http\Controllers\agent\events\EventController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "TableSchedules";
    protected $fillable = [
        'id',
        'AdminId',
        'LeadId',
        'mls_id',
        'PropertyType',
        'Subject',
        'ScheduleStartTime',
        'ScheduleEndTime',
        'Created',
        'Modified',
        'Status',
        'CreatedBy',
        'IsViewed',
        'EventColor',
        'StartDate',
        'Slots',
        'StartTime',
        'EndTime',
        'Description',
        'Name',
        'Email',
        'Phone'
    ];

    public function events() {
        return $this->belongsTo(Events::class,"EventColor");
    }
}
