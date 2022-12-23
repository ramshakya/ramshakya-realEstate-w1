<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadsEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LeadsEmail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('LeadId');
            $table->integer('AgentId')->default(0);
            $table->string('Email',255)->nullable();;
            $table->text('Subject')->nullable();;
            $table->text('Template')->nullable();;
            $table->text('Message')->nullable();;
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
        Schema::dropIfExists('LeadsEmail');
    }
}
