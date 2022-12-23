<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatsData extends Model
{
    use HasFactory;
    protected $table = "StatsData";
    protected $fillable = [
        "AvgPrice",
        "Count",
        "Type",
        "TimePeriod",
        "Date",
        "Month",
        "Year",
        "TotalPriceForAll",
        "TotalPriceForSale",
        "TotalPriceForRent",
        "AvgDom",
    ];
}
