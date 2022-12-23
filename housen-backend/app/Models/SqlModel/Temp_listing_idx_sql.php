<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temp_listing_idx_sql extends Model
{
    use HasFactory;
    protected $table = "TempListingIdx";
    protected $fillable = [
        "Ml_num",
        "Status"
    ];
}
