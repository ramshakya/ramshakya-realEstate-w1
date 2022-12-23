<?php

namespace App\Models\SqlModel;

use App\Models\RetsPropertyData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RetsPropertyDataSql extends Model
{
    use HasFactory;

    protected $table = "RetsPropertyData";
    public $timestamps = false;
    protected $fillable = [
        "Den_fr",
        "Disp_addr",
        "Dom",
        "Dt_sus",
        "Dt_ter",
        "Timestamp_sql",
        "City",
        "Community",
        "Municipality_code",
        "Cert_lvl",
        "Energy_cert",
        "Oh_dt_stamp",
        "Oh_to1",
        "Oh_to2",
        "Oh_to3",
        "Green_pis",
        "Oh_from1",
        "Oh_from2",
        "Oh_from3",
        "A_c",
        "Prop_feat4_out",
        "Prop_feat5_out",
        "Prop_feat6_out",
        "Rm9_len",
        "Rm9_wth",
        "Prop_feat3_out",
        "PublicRemarks",
        "Addl_mo_fee",
        "StandardAddress",
        "All_inc",
        "Condo_corp",
        "Condo_exp",
        "Constr1_out",
        "Constr2_out",
        "Corp_num",
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
        "Level10",
        "Orig_dol",
        "Outof_area",
        "Parcel_id",
        "Park_chgs",
        "Park_desig",
        "Park_desig_2",
        "Park_fac",
        "Park_lgl_desc1",
        "Park_lgl_desc2",
        "Park_spc1",
        "Park_spc2",
        "Park_spcs",
        "Patio_ter",
        "Perc_dif",
        "Pets",
        "Pr_lsc",
        "Prkg_inc",
        "Prop_feat1_out",
        "Prop_feat2_out",
        "Prop_mgmt",
        "Pvt_ent",
        "Retirement",
        "Rltr",
        "Rm1_dc1_out",
        "Rm1_dc2_out",
        "Rm1_dc3_out",
        "Rm1_len",
        "Rm1_out",
        "Rm1_wth",
        "Rm10_dc1_out",
        "Rm10_dc2_out",
        "Rm10_dc3_out",
        "Rm10_len",
        "Rm10_out",
        "Rm10_wth",
        "Rm11_dc1_out",
        "Rm11_dc2_out",
        "Rm11_dc3_out",
        "Rm11_len",
        "Rm11_out",
        "Rm11_wth",
        "Rm12_dc1_out",
        "Rm12_dc2_out",
        "Rm12_dc3_out",
        "Rm12_len",
        "Rm12_out",
        "Rm12_wth",
        "Rm2_dc1_out",
        "Rm2_dc2_out",
        "Rm2_dc3_out",
        "Rm2_len",
        "Rm2_out",
        "Rm2_wth",
        "Rm3_dc1_out",
        "Rm3_dc2_out",
        "Rm3_dc3_out",
        "Rm3_len",
        "Rm3_out",
        "Rm3_wth",
        "Rm4_dc1_out",
        "Rm4_dc2_out",
        "Rm4_dc3_out",
        "Rm4_len",
        "Rm4_out",
        "Rm4_wth",
        "Rm5_dc1_out",
        "Rm5_dc2_out",
        "Rm5_dc3_out",
        "Rm5_len",
        "Rm5_out",
        "Rm5_wth",
        "Rm6_dc1_out",
        "Rm6_dc2_out",
        "Rm6_dc3_out",
        "Rm6_len",
        "Rm6_out",
        "Rm6_wth",
        "Rm7_dc1_out",
        "Rm7_dc2_out",
        "Rm7_dc3_out",
        "Rm7_len",
        "Rm7_out",
        "Rm7_wth",
        "Rm8_dc1_out",
        "Rm8_dc2_out",
        "Rm8_dc3_out",
        "Rm8_len",
        "Rm8_out",
        "Rm8_wth",
        "Rm9_dc1_out",
        "Rm9_dc2_out",
        "Rm9_dc3_out",
        "Rm9_out",
        "Rms",
        "Rooms_plus",
        "MlsStatus",
        "Sp_dol",
        "Sqft",
        "StreetName",
        "StreetDirPrefix",
        "Stories",
        "VirtualTourURLBranded",
        "Community_code",
        "Area_code",
        "PropertySubType",
        "Municipality",
        "Oh_date1",
        "PostalCode",
        "BedroomsTotal",
        "Bsmt1_out",
        "Bsmt2_out",
        "ListPrice",
        "ListingId",
        "Status",
        "Pool",
        "BathroomsFull",
        "PropertyType",
        "inserted_time",
        "updated_time",
        "StreetNumber",
        "UnitNumber",
        "Latitude",
        "Longitude",
        "SubdivisionName",
        "YearBuilt",
        "ListAgentFullName",
        "ListAgentEmail",
        "ListAgentDirectPhone",
        "ListAgentMlsId",
        "CustomAddress",
        "LivingArea"
    ];

    public function getListingResult($listingId){
        return RetsPropertyDataSql::where("ListingId",$listingId)->get();
    }

    public function getPostalCode() {
        return RetsPropertyDataSql::select("PostalCode")->distinct()->get();
    }

    public function getPostalCodeForDistinct() {
        return DB::select("SELECT DISTINCT(PostalCode) FROM `RetsPropertyData` where PostalCode is not Null and PostalCode <>'' and PostalCode not like '%+%' and PostalCode not like '%-%' and length(PostalCode)=5 ");
    }

    public function getStateOrProvince() {
        return DB::select("SELECT DISTINCT(StateOrProvince) FROM `RetsPropertyData` where StateOrProvince is not Null and StateOrProvince <>'' and length(StateOrProvince)>1  and StateOrProvince REGEXP '[a-zA-Z]' and StateOrProvince not like 'st' and StateOrProvince not like 'rd' and StateOrProvince not like 'DR' ");
    }

    public function getLowerCity() {
        return DB::select("SELECT DISTINCT(LOWER(City)) as City FROM `RetsPropertyData` where City is not Null and City <>'' and City <>'Florida' and City like '% %' order by LENGTH(City) DESC");
    }

    public function getDistinctCity() {
        return DB::select("SELECT DISTINCT(City) FROM `RetsPropertyData` where City is not Null and City <>'' and City <>'Florida' order by LENGTH(City) DESC ");
    }

    public function getDistinctCity2($exl_str) {
        return DB::select("SELECT DISTINCT(City) FROM `RetsPropertyData` where City is not Null and City <>'' and City not in($exl_str) order by LENGTH(City) DESC ");
    }
    public function getDistinctCity3() {
        return DB::select("SELECT DISTINCT(City) FROM `RetsPropertyData` where City is not Null and City <>'' order by LENGTH(City) DESC ");
    }
    public function get_data($request_data){
        $type = "";
        if(isset($request_data['type'])){
            $type=$request_data['type'];
        }
        $query=RetsPropertyData::where('ClassName',$type);
        if(isset($request_data['search'])){
            $search=$request_data['search'];
            /*$query=$query->where(function($q) use ($search) {
                $q->where('UnparsedAddress', 'like', '%' . $search . '%')
                    ->orWhere('ListOfficeMlsId', 'like', '%' . $search . '%')
                    ->orWhere('ListAgentMlsId', 'like', '%' . $search . '%');
            });*/
        }
        if(isset($request_data['cities']))
        {
            $cities=$request_data['cities'];
            $query->whereIn('City',$cities);
        }
        //$data['property']=RetsPropertyData::where("ClassName",$type);
        $data['property']=$query->get();
        return $data;
    }

}
