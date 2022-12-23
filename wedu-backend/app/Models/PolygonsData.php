<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolygonsData extends Model
{
    //
    use HasFactory;
    protected $table = "PolygonsData";
    protected $fillable = [
        'cityName',
        'cityPolygons',
        'areasName',
        'areasPolygons',
    ];
}
