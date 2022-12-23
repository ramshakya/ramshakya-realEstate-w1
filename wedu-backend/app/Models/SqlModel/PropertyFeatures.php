<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyFeatures extends Model
{
    use HasFactory;
    protected $table = "PropertyFeatures";
    protected $fillable = [
        "PropertyId",
        "FeaturesId"
    ];
}
