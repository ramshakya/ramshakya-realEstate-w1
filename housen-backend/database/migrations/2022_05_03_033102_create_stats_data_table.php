<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('StatsData', function (Blueprint $table) {
            $table->id();
            $table->integer("AvgPrice")->nullable();
            $table->integer("AvgDom")->nullable();
            $table->integer("Count")->nullable();
            $table->string("Type")->nullable();
            $table->string("TimePeriod")->nullable();
            $table->integer("TotalPriceForSale")->nullable();
            $table->integer("TotalPriceForRent")->nullable();
            $table->integer("TotalPriceForAll")->nullable();
            $table->date("Date")->nullable();
            $table->integer("Month")->nullable();
            $table->integer("Year")->nullable();
            $table->timestamps();
        });
    }

    /**Totalprice for all, totalprice for sale total price for rent

     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists('stats_data');
    }
}

//
//
