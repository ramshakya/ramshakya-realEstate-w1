<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Adding4NewColumnsForPreConstructionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PreConstructionBuilding', function (Blueprint $table) {
            $table->text('Architects')->nullable();
            $table->text('Completion')->nullable();
            $table->text('Brochure')->nullable();
            $table->text('Feature_and_Finishes')->nullable();
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
            $table->dropColumn('Architects');
            $table->dropColumn('Completion');
            $table->dropColumn('Brochure');
            $table->dropColumn('Feature_and_Finishes');
        });
    }
}
