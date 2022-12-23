<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temp_listing extends Model
{
    use HasFactory;
    protected $table = "Temp_listing";
    protected $fillable = [
        "Ml_num",
        "Status"
    ];
}
