<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuilderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builder', function (Blueprint $table) {
            $table->id();
            $table->string('AgentId')->nullable();;
            $table->text('BuilderName')->nullable();
            $table->text('BuilderPhone')->nullable();
            $table->text('BuilderEmail')->nullable();
            $table->text('BuilderCountry')->nullable();
            $table->text('BuilderAddress')->nullable();
            $table->text('BuilderCity')->nullable();
            $table->text('BuilderState')->nullable();
            $table->text('BuilderPostalCode')->nullable();
            $table->text('BuilderDescription')->nullable();
            $table->text('Logo')->nullable();
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
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
        Schema::dropIfExists('builder');
    }
}
