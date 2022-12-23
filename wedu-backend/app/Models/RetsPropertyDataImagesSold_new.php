<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Jenssegers\Mongodb\Eloquent\Model;
//use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataImagesSold_new extends Model
{
    // /var/www/html/Current-projects/wedu.ca/weduBackend/wedu/
    use HasFactory;
    //protected $connection = "mongodb";
    protected $table = "RetsPropertyDataSoldImagesSql_old"; //"RetsPropertyDataImages";
    protected $casts = [
        'image_urls' => 'array',
    ];
   // protected $table = "RetsPropertyDataImages";
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
        "image_urls"
    ];
}
