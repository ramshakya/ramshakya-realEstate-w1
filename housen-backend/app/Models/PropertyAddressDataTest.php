<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAddressDataTest extends Model
{
    use HasFactory;
    protected $table = "PropertyAddressDataTest";
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
