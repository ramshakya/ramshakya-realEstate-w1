<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RetsAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('PropertyAddressData', function (Blueprint $table) {
            $table->id();
            $table->string("ListingId","100")->nullable();
            $table->string("Address","255")->nullable();
            $table->string("City","255")->nullable();
            $table->string("Area","255")->nullable();
            $table->string("ZipCode","255")->nullable();
            $table->string("County","255")->nullable();
            $table->string("Status","255")->nullable();
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
        Schema::dropIfExists('PropertyAddressData');
    }
}
