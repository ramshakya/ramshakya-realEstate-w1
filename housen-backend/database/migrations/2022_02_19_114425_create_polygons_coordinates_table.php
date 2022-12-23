<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolygonsCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polygons_coordinates', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('enterId')->nullable();
            $table->text('cityName')->nullable();
            $table->text('cityPolygons')->nullable();
            $table->text('areasName')->nullable();
            $table->text('areasPolygons')->nullable();
            $table->text('fullJson')->nullable();
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
        Schema::dropIfExists('polygons_coordinates');
    }
}
