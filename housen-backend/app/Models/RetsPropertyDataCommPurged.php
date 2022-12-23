<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetsPropertyDataCommPurged extends Model
{
    use HasFactory;


    protected $table = "RetsPropertyDataCommPurged";
    protected $fillable = ["id", "Timestamp_sql", "Area", "Community", "Cert_lvl", "Energy_cert", "Oh_date1", "Oh_date2", "Oh_date3", "Oh_to1", "Green_pis", "Oh_to2", "Oh_to3", "Oh_from1", "Oh_from2", "Oh_from3", "Net_inc", "Oa_area", "Occ", "Off_areacd", "Oper_exp", "Orig_dol", "Orig_lp_cd", "Other", "Out_storg", "Outof_area", "Parcel_id", "Park_spcs", "Perc_bldg", "Perc_rent", "Prop_type", "Rail", "Retail_a", "Retail_ac", "Com_chgs", "Com_cn_fee", "County", "Crane", "Cross_st", "Days_open", "Dba", "Depth", "Disp_addr", "Dom", "Elevator", "Employees", "Exp_actest", "Extras", "Fin_stmnt", "Franchise", "Freestandg", "Front_ft", "Gar_type", "Gross_inc", "Heat_exp", "Heating", "Hours_open", "Hydro_exp", "Ind_area", "Ind_areacd", "Insur", "Inventory", "Irreg", "Legal_desc", "Llbo", "Lot_code", "Lotsz_code", "Lp_code", "Lp_dol", "Maint", "Mgmt", "Minrenttrm", "Ml_num", "Mmap_col", "Mmap_page", "Mmap_row", "Handi_equipped", "Municipality_district", "Municipality_code", "Municipality", "Pix_updt", "A_c", "Ad_text", "Addr", "Amps", "Apt_num", "Area_infl1_out", "Area_infl2_out", "Ass_year", "Bath_tot", "Bay_size1", "Bay_size1_in", "Bay_size2", "Bay_size2_in", "Bsmt1_out", "Bus_type", "Ceil_ht", "Ceil_ht_in", "Chattels", "Rltr", "S_r", "Seats", "Sewer", "Shpdrsdlhtft", "Shpdrsdlhtin", "Shpdrsdlnu", "Shpdrsdlwdft", "Shpdrsdlwdin", "Shpdrsdmhtft", "Shpdrsdmhtin", "Shpdrsdmnu", "Shpdrsdmwdft", "Shpdrsdmwdin", "Shpdrsglhtft", "Shpdrsglhtin", "Shpdrsglnu", "Shpdrsglwdft", "Shpdrsglwdin", "Shpdrstlhtft", "Shpdrstlhtin", "Shpdrstlnu", "Shpdrstlwdft", "Shpdrstlwdin", "Soil_test", "Sprinklers", "St", "St_dir", "St_num", "St_sfx", "Status", "Survey", "Taxes", "Taxes_exp", "Terms", "Tot_area", "Tot_areacd", "Tour_url", "Community_code", "Area_code", "Trlr_pk_spt", "Tv", "Type_own_srch", "Type_own1_out", "Type_taxes", "Uffi", "Utilities", "Vac_perc", "Vend_pis", "Volts", "Vtour_updt", "Water", "Water_exp", "Wtr_suptyp", "Yr", "Yr_built", "Yr_exp", "Zip", "Zoning", "Poss_date", "Oh_date4", "Oh_Link1", "Oh_Link2", "Oh_Link3", "Oh_Type1", "Oh_Type2", "Oh_Type3", "Oh_date5", "Oh_date6", "Oh_from4", "Oh_from5", "Oh_from6", "Oh_Link4", "Oh_Link5", "Oh_Link6", "Oh_to4", "Oh_to5", "Oh_to6", "Oh_Type4", "Oh_Type5", "Oh_Type6", "processed", "property_insert_time", "property_last_updated", "image_downloaded", "image_download_tried", "image_downloaded_time", "mls_no", "created_at", "updated_at", "geocode", "geocodeTried", "Latitude", "Longitude", "SlugUrl","Vow_exclusive","Reimport","FromIdxImport","IsSold"];

    public function propertiesImages()
    {
        return $this->hasMany(RetsPropertyDataImage::class, 'listingID', 'Ml_num');
    }
}
