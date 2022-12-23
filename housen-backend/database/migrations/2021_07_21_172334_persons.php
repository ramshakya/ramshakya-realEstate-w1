<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Persons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('status_id')->unsigned()->default(1)->index();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
        });
        \Illuminate\Support\Facades\DB::table('persons')->insert(
            [[
                'name' => 'Super Admin',
                'status_id' => 1
            ], [
                'name' => 'Admin',
                'status_id' => 1
            ], [
                'name' => 'User',
                'status_id' => 1
            ]]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
