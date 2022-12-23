<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinceTbl extends Model
{
    use HasFactory;
    protected $table = "ProvinceTbl";
    protected $fillable = [
        "Province",
        "Municipality",
        "MunicipalityHeading",
        "Ids",
        "Community"
    ];
}
