<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FavouriteProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('FavouriteProperties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('LeadId');
            $table->string('ListingId',50)->nullable();
            $table->integer('AgentId')->default(0);
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('FavouriteProperties')->insert(
            [[
                'LeadId' => 13581,
                'ListingId' => "C4833856",
                "AgentId" => 1
            ],[
                'LeadId' => 13582,
                'ListingId' => "C4850716",
                "AgentId" => 13186
            ],[
                'LeadId' => 13581,
                'ListingId' => "C4833856",
                "AgentId" => 1
            ]]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('FavouriteProperties');
    }
}
