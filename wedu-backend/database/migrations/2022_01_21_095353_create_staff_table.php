<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('FullName')->nullable();
            $table->string('FirstName')->nullable();
            $table->string('LastName')->nullable();
            $table->string('Email')->nullable();
            $table->string('AgentId')->nullable();
            $table->string('Gender')->nullable();
            $table->string('Phone')->nullable();
            $table->string('UserId')->nullable();
            $table->string('DeletedAt')->nullable();
            $table->string('ImageUrl')->nullable();
            $table->string('ProjectId')->nullable();
            $table->string('RoleId')->nullable();
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
        Schema::dropIfExists('staff');
    }
}
