<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Jenssegers\Mongodb\Eloquent\Model;
// use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;
class RetsPropertyDataImagesSold extends Model
{
    use HasFactory;
    protected $table = "RetsPropertyDataImagesSold";
    protected $fillable = [
        "listingID",
        "image_urls"
    ];
}
