<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactEnquiryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ContactEnquiry', function (Blueprint $table) {
            $table->id();
            $table->string('AgentId')->nullable();
            $table->text('Name')->nullable();
            $table->text('Email')->nullable();
            $table->text('Phone')->nullable();
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
        Schema::dropIfExists('ContactEnquiry');
    }
}
