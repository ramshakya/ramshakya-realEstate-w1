<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RetsPropertyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('rets_property_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Den_fr', 50)->nullable();
            $table->string('Disp_addr', 1)->nullable();
            $table->decimal('Dom');
            $table->date('Dt_sus')->nullable();
            $table->date('Dt_ter')->nullable();
            $table->date('Timestamp_sql')->nullable();
            $table->string('Area', 40)->nullable();
            $table->string('Community', 44)->nullable();
            $table->string('Municipality_code', 50)->nullable();
            $table->string('Cert_lvl', 25)->nullable();
            $table->string('Energy_cert', 50)->nullable();
            $table->date('Oh_dt_stamp')->nullable();
            $table->string('Oh_to1', 8)->nullable();
            $table->string('Oh_to2', 8)->nullable();
            $table->string('Oh_to3', 8)->nullable();
            $table->string('Green_pis', 50)->nullable();
            $table->string('Oh_from1', 8)->nullable();
            $table->string('Oh_from2', 8)->nullable();
            $table->string('Oh_from3', 8)->nullable();
            $table->string('A_c', 50)->nullable();
            $table->string('Prop_feat4_out', 50)->nullable();
            $table->string('Prop_feat5_out', 50)->nullable();
            $table->string('Prop_feat6_out', 50)->nullable();
            $table->decimal('Rm9_len')->nullable();
            $table->decimal('Rm9_wth')->nullable();
            $table->string('Prop_feat3_out', 50)->nullable();
            $table->text('Ad_text')->nullable();
            $table->decimal('Addl_mo_fee');
            $table->string('Addr', 35)->nullable();
            $table->string('All_inc', 50)->nullable();
            $table->string('Condo_corp', 4)->nullable();
            $table->string('Condo_exp', 50)->nullable();
            $table->string('Constr1_out', 50)->nullable();
            $table->string('Constr2_out', 50)->nullable();
            $table->decimal('Corp_num');
            $table->string('County', 16)->nullable();
            $table->string('Cross_st', 30)->nullable();
            $table->string('Elevator', 50)->nullable();
            $table->string('Ens_lndry', 50)->nullable();
            $table->string('Extras', 240)->nullable();
            $table->string('Fpl_num', 50)->nullable();
            $table->string('Fuel', 50)->nullable();
            $table->string('Furnished', 50)->nullable();
            $table->decimal('Gar');
            $table->string('Gar_type', 50)->nullable();
            $table->string('Heat_inc', 50)->nullable();
            $table->string('Heating', 50)->nullable();
            $table->string('Laundry', 50)->nullable();
            $table->string('Laundry_lev', 50)->nullable();
            $table->date('Ld')->nullable();
            $table->string('Level1', 50)->nullable();
            $table->string('Level10', 50)->nullable();
            $table->decimal('Orig_dol');
            $table->string('Outof_area', 16)->nullable();
            $table->string('Parcel_id', 9)->nullable();
            $table->decimal('Park_chgs');
            $table->string('Park_desig', 50)->nullable();
            $table->string('Park_desig_2', 50)->nullable();
            $table->string('Park_fac', 50)->nullable();
            $table->string('Park_lgl_desc1', 15)->nullable();
            $table->string('Park_lgl_desc2', 15)->nullable();
            $table->string('Park_spc1', 4)->nullable();
            $table->string('Park_spc2', 4)->nullable();
            $table->decimal('Park_spcs');
            $table->string('Patio_ter', 50)->nullable();
            $table->decimal('Perc_dif');
            $table->string('Pets', 50)->nullable();
            $table->string('Pr_lsc', 50)->nullable();
            $table->string('Prkg_inc', 50)->nullable();
            $table->string('Prop_feat1_out', 50)->nullable();
            $table->string('Prop_feat2_out', 50)->nullable();
            $table->string('Prop_mgmt', 60)->nullable();
            $table->string('Pvt_ent', 50)->nullable();
            $table->string('Retirement', 50)->nullable();
            $table->text('Rltr');
            $table->string('Rm1_dc1_out', 50)->nullable();
            $table->string('Rm1_dc2_out', 50)->nullable();
            $table->string('Rm1_dc3_out', 50)->nullable();
            $table->decimal('Rm1_len');
            $table->string('Rm1_out', 50)->nullable();
            $table->decimal('Rm1_wth');
            $table->string('Rm10_dc1_out', 50)->nullable();
            $table->string('Rm10_dc2_out', 50)->nullable();
            $table->string('Rm10_dc3_out', 50)->nullable();
            $table->decimal('Rm10_len');
            $table->string('Rm10_out', 50)->nullable();
            $table->decimal('Rm10_wth');
            $table->string('Rm11_dc1_out', 50)->nullable();
            $table->string('Rm11_dc2_out', 50)->nullable();
            $table->string('Rm11_dc3_out', 50)->nullable();
            $table->decimal('Rm11_len');
            $table->string('Rm11_out', 50)->nullable();
            $table->decimal('Rm11_wth');
            $table->string('Rm12_dc1_out', 20)->nullable();
            $table->string('Rm12_dc2_out', 20)->nullable();
            $table->string('Rm12_dc3_out', 20)->nullable();
            $table->decimal('Rm12_len');
            $table->string('Rm12_out', 9)->nullable();
            $table->decimal('Rm12_wth');
            $table->string('Rm2_dc1_out', 50)->nullable();
            $table->string('Rm2_dc2_out', 50)->nullable();
            $table->string('Rm2_dc3_out', 50)->nullable();
            $table->decimal('Rm2_len');
            $table->string('Rm2_out', 50)->nullable();
            $table->decimal('Rm2_wth');
            $table->string('Rm3_dc1_out', 50)->nullable();
            $table->string('Rm3_dc2_out', 50)->nullable();
            $table->string('Rm3_dc3_out', 50)->nullable();
            $table->decimal('Rm3_len');
            $table->string('Rm3_out', 50)->nullable();
            $table->decimal('Rm3_wth');
            $table->string('Rm4_dc1_out', 50)->nullable();
            $table->string('Rm4_dc2_out', 50)->nullable();
            $table->string('Rm4_dc3_out', 50)->nullable();
            $table->decimal('Rm4_len');
            $table->string('Rm4_out', 50)->nullable();
            $table->decimal('Rm4_wth');
            $table->string('Rm5_dc1_out', 50)->nullable();
            $table->string('Rm5_dc2_out', 50)->nullable();
            $table->string('Rm5_dc3_out', 50)->nullable();
            $table->decimal('Rm5_len');
            $table->string('Rm5_out', 50)->nullable();
            $table->decimal('Rm5_wth');
            $table->string('Rm6_dc1_out', 50)->nullable();
            $table->string('Rm6_dc2_out', 50)->nullable();
            $table->string('Rm6_dc3_out', 50)->nullable();
            $table->decimal('Rm6_len');
            $table->string('Rm6_out', 50)->nullable();
            $table->decimal('Rm6_wth');
            $table->string('Rm7_dc1_out', 50)->nullable();
            $table->string('Rm7_dc2_out', 50)->nullable();
            $table->string('Rm7_dc3_out', 50)->nullable();
            $table->decimal('Rm7_len');
            $table->string('Rm7_out', 50)->nullable();
            $table->decimal('Rm7_wth');
            $table->string('Rm8_dc1_out', 50)->nullable();
            $table->string('Rm8_dc2_out', 50)->nullable();
            $table->string('Rm8_dc3_out', 50)->nullable();
            $table->decimal('Rm8_len');
            $table->string('Rm8_out', 50)->nullable();
            $table->decimal('Rm8_wth');
            $table->string('Rm9_dc1_out', 50)->nullable();
            $table->string('Rm9_dc2_out', 50)->nullable();
            $table->string('Rm9_dc3_out', 50)->nullable();
            $table->string('Rm9_out', 50)->nullable();
            $table->decimal('Rms');
            $table->decimal('Rooms_plus');
            $table->string('S_r', 50)->nullable();
            $table->decimal('Sp_dol');
            $table->string('Sqft', 50)->nullable();
            $table->string('St', 20)->nullable();
            $table->string('St_dir', 50)->nullable();
            $table->string('Stories', 3)->nullable();
            $table->string('Tour_url', 100)->nullable();
            $table->string('Community_code', 50)->nullable();
            $table->string('Area_code', 50)->nullable();
            $table->string('Type_own1_out', 50)->nullable();
            $table->string('Municipality', 40)->nullable();
            $table->date('Oh_date1')->nullable();
            $table->string('Zip', 7)->nullable();
            $table->decimal('Br');
            $table->string('Bsmt1_out', 50)->nullable();
            $table->string('Bsmt2_out', 50)->nullable();
            $table->decimal('ListPrice');
            $table->string('listingId', 8)->nullable();
            $table->string('Status', 8)->nullable();
            $table->string('Pool', 8)->nullable();
            $table->string('BathroomsFull', 8)->nullable();
            $table->string('ClassName', 50)->nullable();
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
        //
        Schema::dropIfExists('rets_property_data');
    }
}
