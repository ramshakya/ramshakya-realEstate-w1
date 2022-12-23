<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('ApiRequestLogs', function (Blueprint $table) {
            $table->id();
            $table->string("url")->nullable();
            $table->text("params")->nullable();
            $table->text("callFrom")->nullable();
            $table->text("domain")->nullable();
            $table->text("ip")->nullable();
            $table->string("status")->nullable();
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
        Schema::dropIfExists('ApiRequestLogs');
    }
}
