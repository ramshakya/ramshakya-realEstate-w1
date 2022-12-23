<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SoldPropertiesCronLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SoldPropertiesCronLog', function (Blueprint $table) {
            $table->bigIncrements('id',11);
            $table->string('CronFileName',50)->nullable();
            $table->string('PropertyClass',50)->nullable();
            $table->text('RetsQuery');
            $table->datetime('CronStartTime');
            $table->datetime('CronEndTime');
            $table->datetime('PropertiesDownloadStartTime');
            $table->datetime('PropertiesDownloadEndTime');
            $table->integer('PropertiesCountFromMls')->default(0);
            $table->integer('PropertiesCountActualDownloaded')->default(0);
            $table->string('PropertyInserted',50)->nullable();
            $table->string('PropertyUpdated',50)->nullable();
            $table->string('StepsCompleted',50)->default('0');
            $table->integer('mls_no')->nullable();
            $table->tinyInteger('Success')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('SoldPropertiesCronLog');
    }
}
