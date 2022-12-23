<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterEminitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_emenities', function (Blueprint $table) {
            $table->id();
            $table->text('Name')->nullable();
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
            $table->text('Url')->nullable();
            $table->text('IpAddress')->nullable();
            $table->text('Page')->nullable();
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
        Schema::dropIfExists('master_emenities');
    }
}
