<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToMasterEmenities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_emenities', function (Blueprint $table) {
            //
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_emenities', function (Blueprint $table) {
            //
            $table->dropColumn('Status');
        });
    }
}
