<?php

namespace App\Models\SqlModel;

use App\Models\RetsPropertyDataImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataPurged extends Model
{
    use HasFactory;
    protected $table = "RetsPropertyDataPurged";
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
        "Sp_date",
        "FromIdxImport",
        "IsSold",
        "IsMatched",
        "Price",
        "LastStatus",
        "Style",
        "ContractDate",
        "Address",
        "ExpiredDate"
    ];

    public function get_data($request_data)
    {
        $type = "";
        if (isset($request_data['type'])) {
            $type = $request_data['type'];
        }
        $query = RetsPropertyDataPurged::where('PropertyType', $type);
        if (isset($request_data['search'])) {
            $search = $request_data['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('StandardAddress', 'like', '%' . $search . '%')
                    ->orWhere('ListOfficeMlsId', 'like', '%' . $search . '%')
                    ->orWhere('ListAgentMlsId', 'like', '%' . $search . '%');
            });
        }
        if (isset($request_data['cities'])) {
            $cities = $request_data['cities'];
            $query->whereIn('City', $cities);
        }
        $data['property'] = $query->get();
        return $data;
    }

    public function getListResult1($property_id)
    {
        return RetsPropertyDataPurged::select('FullMailingAddress', 'BathroomsFull', 'BedroomsTotal', 'CustomPropertyType')->where("ListingId", $property_id)->get();
    }
    private function allcountries()
    {
        $data['countries'] = RetsPropertyDataPurged::distinct('County')->get('County');
        return $data;
    }

    public function getListingResult($listingId)
    {
        return RetsPropertyDataPurged::where("ListingId", $listingId)->get();
    }
    public function ImageUrl()
    {
        return $this->hasOne(RetsPropertyDataImage::class, 'listingID', 'ListingId');
    }
}
