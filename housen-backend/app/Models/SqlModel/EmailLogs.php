<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLogs extends Model
{
    use HasFactory;
    protected $table = "EmailLogs";
    protected $primaryKey = "id";
    protected $fillable = [
        'FromEmail', 'ToEmail', 'ToCc', 'ToBcc', 'Subject', 'Content', 'Method', 'FromMethod', 'DeliveredTime', 'Description', 'IsSent', 'IsRead', 'meta', 'tags', 'status_id', 'deleted_at', 'created_at', 'updated_at','HashId','OpenedTime','LastSeen','SeenAt','FromId'
    ];

    public function insertAndGetId($request) {
        return EmailLogs::create($request)->id;
    }
}
