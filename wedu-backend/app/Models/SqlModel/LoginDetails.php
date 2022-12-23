<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginDetails extends Model
{
    use HasFactory;
    protected $table = "LoginDetails";
    protected $fillable = [
        'id',
        'AgentId',
        'IpAddress',
        'created_at',
        'updated_at'
    ];
}
