<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearchFilter extends Model
{
    use HasFactory;
    // SavedSearchFilter
    // ALTER TABLE SavedSearchFilter ADD features text NULL
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
        "emailHash",
        "subscribe"
    ];
    protected $casts = [
        "propertySubType" => "array",
        "features" => "array",
        "Bsmt1Out" => "array",
    ];
}
