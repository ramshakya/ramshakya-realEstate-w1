<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MostSearchedCities extends Model
{
    use HasFactory;
    protected $table = "MostSearchedCities";
    protected $fillable = [
        "Id",
        "CityName",
        "Count",
        "AgentId",
        "CreatedAt",
        "UpdatedAt",
    ];

}
