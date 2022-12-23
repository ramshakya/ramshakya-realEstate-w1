<?php

namespace App\Models\SqlModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturesMaster extends Model
{
    use HasFactory;
    protected $table = "FeaturesMaster";
    protected $fillable = [
        "Type",
        "Features",
        "AdminId"
    ];
    public function PropertiesId(){
        return $this->hasOne(PropertyFeatures::class,'FeaturesId','id');
    }
}
