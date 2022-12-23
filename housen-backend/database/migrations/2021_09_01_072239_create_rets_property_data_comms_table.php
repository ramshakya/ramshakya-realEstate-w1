<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetsPropertyDataCommsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rets_property_data_comm', function (Blueprint $table) {
            $table->id();
            $table->date('Timestamp_sql')->nullable();
            $table->string('Area', 40)->nullable();
            $table->string('Community', 44)->nullable();
            $table->string('Cert_lvl', 25)->nullable();
            $table->string('Energy_cert', 50)->nullable();
            $table->date('Oh_date1')->nullable();
            $table->date('Oh_date2')->nullable();
            $table->date('Oh_date3')->nullable();
            $table->string('Oh_to1', 8)->nullable();
            $table->string('Green_pis', 50)->nullable();
            $table->string('Oh_to2', 8)->nullable();
            $table->string('Oh_to3', 8)->nullable();
            $table->string('Oh_from1', 8)->nullable();
            $table->string('Oh_from2', 8)->nullable();
            $table->string('Oh_from3', 8)->nullable();
            $table->decimal('Net_inc');
            $table->decimal('Oa_area');
            $table->string('Occ', 14)->nullable();
            $table->string('Off_areacd', 50)->nullable();
            $table->decimal('Oper_exp');
            $table->decimal('Orig_dol');
            $table->string('Orig_lp_cd', 50)->nullable();
            $table->decimal('Other');
            $table->string('Out_storg', 50)->nullable();
            $table->string('Outof_area', 16)->nullable();
            $table->string('Parcel_id', 9)->nullable();
            $table->decimal('Park_spcs');
            $table->decimal('Perc_bldg');
            $table->decimal('Perc_rent');
            $table->string('Prop_type', 50)->nullable();
            $table->string('Rail', 50)->nullable();
            $table->decimal('Retail_a');
            $table->string('Retail_ac', 50)->nullable();
            $table->decimal('Com_chgs');
            $table->decimal('Com_cn_fee');
            $table->string('County', 16)->nullable();
            $table->string('Crane', 50)->nullable();
            $table->string('Cross_st', 30)->nullable();
            $table->string('Days_open', 50)->nullable();
            $table->string('Dba', 37)->nullable();
            $table->decimal('Depth');
            $table->string('Disp_addr', 1)->nullable();
            $table->decimal('Dom');
            $table->string('Elevator', 50)->nullable();
            $table->decimal('Employees');
            $table->string('Exp_actest', 50)->nullable();
            $table->string('Extras', 240)->nullable();
            $table->string('Fin_stmnt', 50)->nullable();
            $table->string('Franchise', 50)->nullable();
            $table->string('Freestandg', 50)->nullable();
            $table->decimal('Front_ft');
            $table->string('Gar_type', 50)->nullable();
            $table->decimal('Gross_inc');
            $table->decimal('Heat_exp');
            $table->string('Heating', 50)->nullable();
            $table->string('Hours_open', 7)->nullable();
            $table->decimal('Hydro_exp');
            $table->decimal('Ind_area');
            $table->string('Ind_areacd', 50)->nullable();
            $table->decimal('Insur');
            $table->decimal('Inventory');
            $table->string('Irreg', 40)->nullable();
            $table->string('Legal_desc', 50)->nullable();
            $table->string('Llbo', 50)->nullable();
            $table->string('Lot_code', 50)->nullable();
            $table->string('Lotsz_code', 50)->nullable();
            $table->string('Lp_code', 50)->nullable();
            $table->decimal('Lp_dol');
            $table->decimal('Maint');
            $table->decimal('Mgmt');
            $table->decimal('Minrenttrm');
            $table->string('Ml_num', 8)->nullable();
            $table->decimal('Mmap_col');
            $table->decimal('Mmap_page');
            $table->string('Mmap_row', 1)->nullable();
            $table->string('Handi_equipped', 50)->nullable();
            $table->string('Municipality_district', 44)->nullable();
            $table->string('Municipality_code', 50)->nullable();
            $table->string('Municipality', 40)->nullable();
            $table->date('Pix_updt')->nullable();
            $table->string('A_c', 50)->nullable();
            $table->text('Ad_text');
            $table->string('Addr', 35)->nullable();
            $table->decimal('Amps');
            $table->string('Apt_num', 7)->nullable();
            $table->string('Area_infl1_out', 50)->nullable();
            $table->string('Area_infl2_out', 50)->nullable();
            $table->decimal('Ass_year');
            $table->decimal('Bath_tot');
            $table->decimal('Bay_size1');
            $table->decimal('Bay_size1_in');
            $table->decimal('Bay_size2');
            $table->decimal('Bay_size2_in');
            $table->string('Bsmt1_out', 50)->nullable();
            $table->string('Bus_type', 50)->nullable();
            $table->decimal('Ceil_ht');
            $table->decimal('Ceil_ht_in');
            $table->string('Chattels', 50)->nullable();
            $table->text('Rltr');
            $table->string('S_r', 50)->nullable();
            $table->decimal('Seats');
            $table->string('Sewer', 50)->nullable();
            $table->decimal('Shpdrsdlhtft');
            $table->decimal('Shpdrsdlhtin');
            $table->decimal('Shpdrsdlnu');
            $table->decimal('Shpdrsdlwdft');
            $table->decimal('Shpdrsdlwdin');
            $table->decimal('Shpdrsdmhtft');
            $table->decimal('Shpdrsdmhtin');
            $table->decimal('Shpdrsdmnu');
            $table->decimal('Shpdrsdmwdft');
            $table->decimal('Shpdrsdmwdin');
            $table->decimal('Shpdrsglhtft');
            $table->decimal('Shpdrsglhtin');
            $table->decimal('Shpdrsglnu');
            $table->decimal('Shpdrsglwdft');
            $table->decimal('Shpdrsglwdin');
            $table->decimal('Shpdrstlhtft');
            $table->decimal('Shpdrstlhtin');
            $table->decimal('Shpdrstlnu');
            $table->decimal('Shpdrstlwdft');
            $table->decimal('Shpdrstlwdin');
            $table->string('Soil_test', 50)->nullable();
            $table->string('Sprinklers', 50)->nullable();
            $table->string('St', 20)->nullable();
            $table->string('St_dir', 50)->nullable();
            $table->string('St_num', 7)->nullable();
            $table->string('St_sfx', 50)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('Survey', 50)->nullable();
            $table->decimal('Taxes');
            $table->decimal('Taxes_exp');
            $table->decimal('Terms');
            $table->decimal('Tot_area');
            $table->string('Tot_areacd', 50)->nullable();
            $table->string('Tour_url', 100)->nullable();
            $table->string('Community_code', 50)->nullable();
            $table->string('Area_code', 50)->nullable();
            $table->decimal('Trlr_pk_spt');
            $table->decimal('Tv');
            $table->text('Type_own_srch');
            $table->string('Type_own1_out', 50)->nullable();
            $table->string('Type_taxes', 50)->nullable();
            $table->string('Uffi', 50)->nullable();
            $table->string('Utilities', 50)->nullable();
            $table->decimal('Vac_perc');
            $table->string('Vend_pis', 50)->nullable();
            $table->decimal('Volts');
            $table->date('Vtour_updt')->nullable();
            $table->string('Water', 50)->nullable();
            $table->decimal('Water_exp');
            $table->string('Wtr_suptyp', 50)->nullable();
            $table->decimal('Yr')->nullable();
            $table->string('Yr_built', 50)->nullable();
            $table->decimal('Yr_exp');
            $table->string('Zip', 7)->nullable();
            $table->string('Zoning', 40)->nullable();
            $table->date('Poss_date')->nullable();
            $table->date('Oh_date4')->nullable();
            $table->text('Oh_Link1');
            $table->text('Oh_Link2');
            $table->text('Oh_Link3');
            $table->string('Oh_Type1', 50)->nullable();
            $table->string('Oh_Type2', 50)->nullable();
            $table->string('Oh_Type3', 50)->nullable();
            $table->date('Oh_date5')->nullable();
            $table->date('Oh_date6')->nullable();
            $table->string('Oh_from4', 8)->nullable();
            $table->string('Oh_from5', 8)->nullable();
            $table->string('Oh_from6', 8)->nullable();
            $table->text('Oh_Link4')->nullable();
            $table->text('Oh_Link5')->nullable();
            $table->text('Oh_Link6')->nullable();
            $table->string('Oh_to4', 8)->nullable();
            $table->string('Oh_to5', 8)->nullable();
            $table->string('Oh_to6', 8)->nullable();
            $table->string('Oh_Type4', 50)->nullable();
            $table->string('Oh_Type5', 50)->nullable();
            $table->string('Oh_Type6', 50)->nullable();
            $table->tinyInteger('processed')->nullable();
            $table->datetime('property_insert_time')->nullable();
            $table->datetime('property_last_updated')->nullable();
            $table->tinyInteger('image_downloaded')->nullable();
            $table->integer('image_download_tried')->nullable();
            $table->datetime('image_downloaded_time')->nullable();
            $table->integer('mls_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rets_property_data_comm');
    }
}
