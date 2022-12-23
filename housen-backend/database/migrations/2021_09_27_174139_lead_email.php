<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LeadEmail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('LeadId')->nullable();
            $table->integer('AgentId')->nullable();
            $table->string('Subject',300)->nullable();
            $table->text('Message')->nullable();
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
        Schema::dropIfExists('LeadEmail');
    }
}
