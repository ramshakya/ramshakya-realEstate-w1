<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataResi extends Model
{
    use HasFactory;
    protected $table = "RetsPropertyDataResi";
    protected $fillable = ["StandardAddress","Comp_pts","Cond","Constr1_out","Constr2_out","County","Cross_st","Den_fr","Depth","Disp_addr","Dom","Drive","Dt_sus","Dt_ter","Elec","Elevator","Extras","Farm_agri","Fpl_num","Front_ft","Fuel","Furnished","Gar_spaces","Gar_type","Gas","Heat_inc","Heating","Hydro_inc","Input_date","Internet","Orig_dol","Oth_struc1_out","Oth_struc2_out","Outof_area","Parcel_id","Park_chgs","Park_spcs","Pay_freq","Perc_dif","Pool","Pr_lsc","Prkg_inc","Prop_feat1_out","Prop_feat2_out","Prop_feat3_out","Prop_feat4_out","Prop_feat5_out","Prop_feat6_out","Rm3_len","Rm3_out","Rm3_wth","Rm4_dc1_out","Rm4_dc2_out","Rm4_dc3_out","Rm4_len","Rm4_out","Rm4_wth","Rm5_dc1_out","Rm5_dc2_out","Rm5_dc3_out","Rm5_len","Rm5_out","Rm5_wth","Rm6_dc1_out","Rm6_dc2_out","Rm6_dc3_out","Rm6_len","Rm6_out","Rm6_wth","Rm7_dc1_out","Rm7_dc2_out","Rm7_dc3_out","Rm7_len","Rm7_out","Rm7_wth","Rm8_dc1_out","Rm8_dc2_out","Rm8_dc3_out","Rm8_len","Rm8_out","Rm8_wth","Rm9_dc1_out","Rm9_dc2_out","Rm9_dc3_out","Rm9_len","Rm9_out","Rm9_wth","Rms","Rooms_plus","Uffi","Unavail_dt","Util_cable","Util_tel","Vend_pis","Vtour_updt","Water","Water_inc","Waterfront","Wcloset_p1","Wcloset_p2","Wcloset_p3","Wcloset_p4","Wcloset_p5","Wcloset_t1","Wcloset_t1lvl","Wcloset_t2","Wcloset_t2lvl","Wcloset_t3","Wcloset_t3lvl","Wcloset_t4","Wcloset_t4lvl","Wcloset_t5","Wcloset_t5lvl","Wtr_suptyp","Xd","Xdtd","Yr","Yr_built","Zip","Zoning","Timestamp_sql","Municipality_code","Area","Community","Cert_lvl","Energy_cert","Handi_equipped","Oh_to1","Oh_to2","Oh_to3","Oh_from1","Oh_from2","Oh_from3","A_c","Acres","Ad_text","Addl_mo_fee","Addr","All_inc","Apt_num","Ass_year","Bath_tot","Br","Br_plus","Bsmt1_out","Bsmt2_out","Cable","Cac_inc","Cd","Central_vac","Cndsold_xd","Com_coopb","Comel_inc","Irreg","Kit_plus","Laundry","Laundry_lev","Ld","Lease","Lease_term","Legal_desc","Level1","Level10","Level11","Level12","Level2","Level3","Level4","Level5","Level6","Level7","Level8","Level9","Lot_fr_inc","Lotsz_code","Lp_dol","Lsc","Lse_terms","Ml_num","Mmap_col","Mmap_page","Mmap_row","Num_kit","Occ","Prop_mgmt","Pvt_ent","Retirement","Rltr","Rm1_dc1_out","Rm1_dc2_out","Rm1_dc3_out","Rm1_len","Rm1_out","Rm1_wth","Rm10_dc1_out","Rm10_dc2_out","Rm10_dc3_out","Rm10_len","Rm10_out","Rm10_wth","Rm11_dc1_out","Rm11_dc2_out","Rm11_dc3_out","Rm11_len","Rm11_out","Rm11_wth","Rm12_dc1_out","Rm12_dc2_out","Rm12_dc3_out","Rm12_len","Rm12_out","Rm12_wth","Rm2_dc1_out","Rm2_dc2_out","Rm2_dc3_out","Rm2_len","Rm2_out","Rm2_wth","Rm3_dc1_out","Rm3_dc2_out","Rm3_dc3_out","S_r","Sewer","Sp_dol","Spec_des1_out","Spec_des2_out","Spec_des3_out","Spec_des4_out","Spec_des5_out","Spec_des6_out","Sqft","St","St_dir","St_num","St_sfx","Status","Style","Taxes","Td","Tour_url","Community_code","Area_code","Tv","Type_own_srch","Type_own1_out","Municipality_district","Municipality","Pix_updt","Oh_date1","Oh_date2","Oh_date3","Oh_dt_stamp","Green_pis","Poss_date","Water_body","Water_type","Oh_Link1","Oh_Link2","Oh_Link3","Oh_Type1","Oh_Type2","Oh_Type3","Water_front","Access_prop1","Access_prop2","Water_feat1","Water_feat2","Water_feat3","Water_feat4","Water_feat5","Shoreline1","Shoreline2","Shore_allow","Shoreline_exp","Alt_power1","Alt_power2","Easement_rest1","Easement_rest2","Easement_rest3","Easement_rest4","Rural_svc1","Rural_svc2","Rural_svc3","Rural_svc4","Rural_svc5","Water_acc_bldg1","Water_acc_bldg2","Water_del_feat1","Water_del_feat2","Sewage1","Sewage2","Potl","Tot_park_spcs","Link_yn","Link_Comment","Oh_date4","Oh_date5","Oh_date6","Oh_from4","Oh_from5","Oh_from6","Oh_Link4","Oh_Link5","Oh_Link6","Oh_to4","Oh_to5","Oh_to6","Oh_Type4","Oh_Type5","Oh_Type6","processed","property_insert_time","property_last_updated","image_downloaded","image_download_tried","image_downloaded_time","mls_no","created_at","updated_at","geocode","geocodeTried","Latitude","Longitude","RoomsDescription","SlugUrl","Vow_exclusive","Reimport","IsSold"];

    /**
     *
     */
    public function propertiesImages()
    {
        return $this->hasMany(RetsPropertyDataImage::class, 'listingID', 'Ml_num');
    }
    /**
     *
     */
    public function latLog()
    {
        return $this->hasOne(RetsPropertyData::class,'ListingId','Ml_num');
    }
}
