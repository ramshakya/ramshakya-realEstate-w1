<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PropertyAddressDataMongo extends Eloquent
{
    use HasFactory;
    protected $connection = "mongodb";
    protected $table = "PropertyAddressDataMongo";
    protected $fillable = [
        "id",
        "ListingId",
        "StandardAddress",
        "City",
        "Area",
        "ZipCode",
        "County",
        "Status",
        "Community",
        "created_at",
        "updated_at"
    ];
}
