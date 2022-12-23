<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangingTablenameMasteremenitiesToMasteramenities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
    {
        // Schema::table('master_amenities', function (Blueprint $table) {
        //     //
        // });
        Schema::rename('master_emenities', 'master_amenities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('master_amenities', function (Blueprint $table) {
        //     //
        // });
        Schema::dropIfExists('master_emenities');
    }
}
