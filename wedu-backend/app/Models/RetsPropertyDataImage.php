<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataImage extends Model
{
    use HasFactory;
    protected $table = "RetsPropertyDataImages";
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
