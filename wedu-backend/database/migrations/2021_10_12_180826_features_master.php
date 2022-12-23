<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FeaturesMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('FeaturesMaster', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('Type',['General','Exterior Features','Interior Features','Community features','Basement features','Construction Features','Energy features','Security features','Lot features','Window/Door Features'])->default('General');
            $table->string('Features',255)->nullable();
            $table->integer('AdminId')->default(0);
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
        Schema::dropIfExists('FeaturesMaster');
    }
}
