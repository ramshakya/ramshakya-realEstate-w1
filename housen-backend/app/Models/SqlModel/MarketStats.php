<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketStats extends Model
{
    use HasFactory;
    protected $table = "market_stats";
    protected $fillable = [
        "id",
        "name",
        "month",
        "city",
        "Timestamp",
        "propertyType",
        "community",
        "year",
        "value",
        "created_at"
    ];
}
