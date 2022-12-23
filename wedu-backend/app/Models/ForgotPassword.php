<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    use HasFactory;
    protected $table = "ForgotPassword";
    protected $fillable = [
        "id",
        "UserId",
        "Token",
        "TimeLimit",
        "OTP",
        "Email"
    ];
}
