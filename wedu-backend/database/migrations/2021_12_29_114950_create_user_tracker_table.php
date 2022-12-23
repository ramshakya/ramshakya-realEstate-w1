<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //ALTER TABLE `UserTracker` ADD `FilteredData` TEXT NULL AFTER `AgentId`;
        Schema::create('user_tracker', function (Blueprint $table) {
            $table->id();
            $table->string('UserId')->nullable();
            $table->text('PageUrl')->nullable();
            $table->text('IpAddress')->nullable();
            $table->text('InTime')->nullable();
            $table->text('StayTime')->nullable();
            $table->text('PropertyId')->nullable();
            $table->text('AgentId')->nullable();
            $table->text('FilteredData')->nullable();
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
        Schema::dropIfExists('user_tracker');
    }
}
