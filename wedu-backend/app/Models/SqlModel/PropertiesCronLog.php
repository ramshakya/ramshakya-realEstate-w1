<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertiesCronLog extends Model
{
    use HasFactory;
    protected $table = "PropertiesCronLog";
    public $timestamps = false;
    public $fillable = [
        'id', 'CronFileName', 'PropertyClass', 'RetsQuery', 'CronStartTime', 'CronEndTime', 'PropertiesDownloadStartTime', 'PropertiesDownloadEndTime', 'PropertiesCountFromMls', 'PropertiesCountActualDownloaded', 'PropertyInserted', 'PropertyUpdated', 'StepsCompleted', 'ForceStop', 'mls_no', 'Success'
    ];
}
