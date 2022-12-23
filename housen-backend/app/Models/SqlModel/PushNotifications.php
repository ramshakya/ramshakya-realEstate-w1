<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotifications extends Model
{
    use HasFactory;
    protected $table = "PushNotifications";
    public $timestamps = false;
    protected $fillable = ['id', 'LoginUserId', 'LeadId', 'Token', 'Title', 'Message', 'EnableNotification', 'SendStatus', 'CreatedAt', 'UpdatedAt'];
}
