<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Propertyview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('PropertyView', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('LeadId')->default(0);
            $table->integer('PropertyId')->nullable();
            $table->text('PropertyUrl')->nullable();
            $table->text('ListingId')->nullable();
            $table->text('IPaddress')->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('PropertyView')->insert(
            [[
                'LeadId' => 13581,
                'PropertyId' => 1,
                'PropertyUrl' => "localhost:8000/api/v1/property/C4833856",
                'ListingId' => "C4833856",
                "IPaddress" => "127:0:0:1"
            ],[
                'LeadId' => 13582,
                'PropertyId' => 2,
                'PropertyUrl' => "localhost:8000/api/v1/property/C4833856",
                'ListingId' => "C4850716",
                "IPaddress" => "127:0:0:1"
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
        Schema::dropIfExists('PropertyView');
    }
}
