<?php

namespace App\Models;

use App\Models\SqlModel\FeaturedListing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataTest extends Model
{
    use HasFactory;
    protected $table = "RetsPropertyDataTest";
    public $timestamps = false;
    protected $fillable = [
        "id",
        "Disp_addr",
        "Dom",
        "Timestamp_sql",
        "City",
        "Community",
        "Municipality_code",
        "A_c",
        "Prop_feat4_out",
        "Prop_feat5_out",
        "Prop_feat6_out",
        "Prop_feat3_out",
        "PublicRemarks",
        "Addl_mo_fee",
        "StandardAddress",
        "All_inc",
        "County",
        "Cross_st",
        "Elevator",
        "Ens_lndry",
        "Extras",
        "Fpl_num",
        "Fuel",
        "Furnished",
        "Gar",
        "Gar_type",
        "Heat_inc",
        "Heating",
        "Laundry",
        "Laundry_lev",
        "Ld",
        "Level1",
        "Orig_dol",
       "Prop_feat1_out",
       "Prop_feat2_out",
        "Retirement",
        "Rltr",
        "MlsStatus",
        "Sp_dol",
        "Sqft",
        "StreetName",
        "StreetDirPrefix",
        "Community_code",
        "Area_code",
        "PropertySubType",
        "Municipality",
        "PostalCode",
        "BedroomsTotal",
        "Bsmt1_out",
        "Bsmt2_out",
        "ListPrice",
        "ListingId",
        "Status",
        "Pool",
        "BathroomsFull",
        "inserted_time",
        "updated_time",
        "PropertyType",
        "StreetNumber",
        "UnitNumber",
        "Latitude",
        "Longitude",
        "YearBuilt",
        "geocode",
        "geocodeTried",
        "ImageUrl",
        "ShortPrice",
        "PropertyStatus",
        "SlugUrl",
        "SqftMin",
        "SqftMax",
        "SqftFlag",
        "Vow_exclusive",
        "Ad_text",
        "Thumbnail_downloaded",
        "Reimport",
        "IsSold"
    ];
    public function getListResult1($property_id)
    {
        return RetsPropertyData::select('FullMailingAddress', 'BathroomsFull', 'BedroomsTotal', 'CustomPropertyType')->where("ListingId", $property_id)->get();
    }
    public function isFeatured(){
        return $this->belongsTo(FeaturedListing::class, 'ListingId','ListingId');
    }
    /**
     *
     */
    public function propertiesImges()
    {
        return $this->hasMany(RetsPropertyDataImage::class, 'listingID', 'ListingId');
    }
}
