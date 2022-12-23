<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableForStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_stats', function (Blueprint $table) {
            $table->id();
            $table->string("name",255)->nullable();
            $table->string("month",255)->nullable();
            $table->string("city",255)->nullable();
            $table->string("area",255)->nullable();
            $table->string("propertyType",255)->nullable();
            $table->string("community",255)->nullable();
            $table->integer("year")->nullable();
            $table->integer("value")->nullable();
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
        Schema::dropIfExists('market_stats');
    }
}
