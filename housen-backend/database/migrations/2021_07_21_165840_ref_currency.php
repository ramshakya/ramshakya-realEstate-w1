<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->string('description',100)->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->integer('status_id')->unsigned()->default('1')->index();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
        });
        \Illuminate\Support\Facades\DB::table('ref_currency')->insert(
            [[
                'name' => 'INR',
                'description' => "This is for the Indian Currency",
                'status_id' => 1
            ],[
                'name' => 'USD',
                'description' => "This is for the us dollars",
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
        Schema::dropIfExists('ref_currency');
    }
}
