<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AgentId');
            $table->text('CityName');
            $table->text('Slug');
            $table->text('MetaTitle');
            $table->text('MetaTags');
            $table->text('MetaDescription');
            $table->text('Content');
            $table->text('Image');
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
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
        Schema::dropIfExists('city_data');
    }
}
