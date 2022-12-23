<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\RetsPropertyDataPurged;

class SavedSearchFilter extends Model
{
    use HasFactory;
    // SavedSearchFilter
    //
    // ALTER TABLE SavedSearchFilter ADD watchListings text NULL

    protected $table = "SavedSearchFilter";
    protected $fillable = [
        "userId",
        "filterName",
        "frequency",
        "emailAlert",
        "textAlert",
        "subClass",
        "bedsTotal",
        "bathsFull",
        "noOfStories",
        "style",
        "GarType",
        "lotSizeAreaMax",
        "lotSizeAreaMin",
        "textSearch",
        "city",
        "countyName",
        "priceMin",
        "priceMax",
        "sqftMin",
        "sqftMax",
        "lotMin",
        "lotMax",
        "yearBuiltMin",
        "yearBuiltMax",
        "schoolsInput",
        "dom",
        "keywords",
        "shape",
        "currPathQuery",
        "currBounds",
        "radius",
        "latitude",
        "longitude",
        "enabled",
        "createdAt",
        "currPath",
        "typeOwn1Out",
        "AC",
        "Bsmt1Out",
        "status",
        "className",
        "agentId",
        "propertySubType",
        "multiplePropType",
        "openHouse",
        "Sqft",
        "features",
        "watchListings",
        "ListingId",
        "updated_at",
        "emailHash",
        "subscribe"
    ];
    protected $casts = [
        "propertySubType" => "array",
        "features" => "array",
        "Bsmt1Out" => "array",
    ];

    public function propertyList(){
        return $this->hasMany(RetsPropertyData::class, 'ListingId', 'ListingId');
    }
    public function propertyListSold(){
        return $this->hasMany(RetsPropertyDataPurged::class, 'ListingId', 'ListingId');
    }


}
