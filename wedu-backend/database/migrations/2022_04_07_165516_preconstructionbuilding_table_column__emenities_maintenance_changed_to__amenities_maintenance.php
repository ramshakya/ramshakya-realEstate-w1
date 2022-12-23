<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PreconstructionbuildingTableColumnEmenitiesMaintenanceChangedToAmenitiesMaintenance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PreConstructionBuilding', function (Blueprint $table) {
            Schema::table('PreConstructionBuilding', function (Blueprint $table) {
                $table->renameColumn('EmenitiesMaintenance', 'AmenitiesMaintenance');
            });
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
            Schema::table('PreConstructionBuilding', function (Blueprint $table) {
                $table->renameColumn('AmenitiesMaintenance', 'EmenitiesMaintenance');
            });
        });
    }
}
