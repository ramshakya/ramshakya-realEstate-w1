<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Campaign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('campaign', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->nullable();
            $table->string('mls_no')->nullable();
            $table->text('office_ids')->nullable();
            $table->text('agent_ids')->nullable();
            $table->text('lead_ids')->nullable();
            $table->text('board_ids')->nullable();
            $table->string('agent_type')->nullable();
            $table->string('agent_table')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time', $precision = 0);
            $table->time('finish_time', $precision = 0);
            $table->string('send_interval')->nullable();
            $table->integer('limit')->default(0);
            $table->string('template')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->integer('completed')->default(0);
            $table->integer('run_lock')->default(0);
            $table->integer('killed')->default(0);
            $table->integer('paused')->default(0);
            $table->time('last_run_time', $precision = 0);
            $table->date('last_run_date')->nullable();
            $table->enum('status',['Completed','Killed','Paused','Deleted'])->nullable();
            $table->string('sent_agent_ids')->nullable();
            $table->string('sent_office_ids')->nullable();
            $table->string('sent_lead_ids')->nullable();
            $table->dateTime('camp_created_time', $precision = 0);
            $table->dateTime('camp_start_time',$precision = 0);
            $table->dateTime('camp_finished_time', $precision = 0);
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
        Schema::dropIfExists('campaign');
    }
}
