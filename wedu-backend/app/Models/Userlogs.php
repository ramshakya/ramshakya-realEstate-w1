<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userlogs extends Model
{
    use HasFactory;
    protected $table="UserLogs";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'UserId',
        'LoginTime',
        'LogoutTime',
        'IpAddress'
    ];
}
