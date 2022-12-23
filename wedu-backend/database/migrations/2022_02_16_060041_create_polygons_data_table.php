<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolygonsDataTable extends Migration
{
    //
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PolygonsData', function (Blueprint $table) {
            $table->id();
            $table->text('cityName')->nullable();
            $table->text('cityPolygons')->nullable();
            $table->text('areasName')->nullable();
            $table->text('areasPolygons')->nullable();
            $table->timestamps();
        });
    }

    //
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PolygonsData');
    }
}
