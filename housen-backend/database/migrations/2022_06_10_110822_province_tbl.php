<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProvinceTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('ProvinceTbl', function (Blueprint $table) {
            $table->id();
            $table->integer("Ids")->nullable();
            $table->string("Province","100")->nullable();
            $table->string("Municipality","255")->nullable();
            $table->string("MunicipalityHeading","255")->nullable();
            $table->text("Community")->nullable();
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
        Schema::dropIfExists('ProvinceTbl');
    }
}
