<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CampaignLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('camp_id');
            $table->integer('template_id');
            $table->string('camp_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->string('to_email')->nullable();
            $table->timestamp('sent_time')->nullable();
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
        Schema::dropIfExists('campaign_logs');
    }
}
