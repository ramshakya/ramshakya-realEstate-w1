<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PropertiesCronLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PropertiesCronLog', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CronFileName',50);
            $table->string('PropertyClass',50);
            $table->text('RetsQuery');
            $table->datetime('CronStartTime');
            $table->datetime('CronEndTime');
            $table->datetime('PropertiesDownloadStartTime');
            $table->datetime('PropertiesDownloadEndTime');
            $table->integer('PropertiesCountFromMls')->default(0);
            $table->integer('PropertiesCountActualDownloaded')->default(0);
            $table->string('PropertyInserted',50);
            $table->string('PropertyUpdated',50);
            $table->string('StepsCompleted',50)->default(0);
            $table->tinyInteger('ForceStop')->default(0);
            $table->integer('mls_no');
            $table->tinyInteger('Success')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('PropertiesCronLog');
    }
}
