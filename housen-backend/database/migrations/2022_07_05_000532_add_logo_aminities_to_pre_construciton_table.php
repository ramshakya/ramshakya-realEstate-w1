<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoAminitiesToPreConstrucitonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PreConstructionBuilding', function (Blueprint $table) {
            $table->text('BuildingLogo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('PreConstructionBuilding', function (Blueprint $table) {
            $table->dropColumn('BuildingLogo');
        });
    }
}
