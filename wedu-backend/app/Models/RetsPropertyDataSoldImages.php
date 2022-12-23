<?php

namespace App\Models;

// /var/www/html/Current-projects/wedu.ca/weduBackend/wedu/app/Models/RetsPropertyDataSoldImages.php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Jenssegers\Mongodb\Eloquent\Model;
//use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class RetsPropertyDataSoldImages extends Model
{
    use HasFactory;
//     protected $connection = "mongodb";
//     protected $table = "RetsPropertyDataImagesSold";
    protected $table = "RetsPropertyDataSoldImagesSql";
    protected $fillable = [
        "mls_no",
        "listingID",
        "image_path",
        "s3_image_url",
        "image_name",
        "downloaded_time",
        "is_uploaded_by_agent",
        "updated_time",
        "image_last_tried_time",
    ];
}
