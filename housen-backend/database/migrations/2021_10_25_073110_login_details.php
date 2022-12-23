<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LoginDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LoginDetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AgentId',50)->nullable();
            $table->string('IpAddress',100)->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('LoginDetails')->insert(
            [[
                'AgentId' => 1,
                'IpAddress' => "127:0:0:1",
                'created_at' =>'2021-10-22 18:53:46'
            ],[
                'AgentId' => 1,
                'IpAddress' => "127:0:0:1",
                'created_at' =>'2021-10-24 13:53:46'
            ],[
                'AgentId' => 1,
                'IpAddress' => "127:0:0:1",
                'created_at' =>'2021-10-25 14:53:46'
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
        //
        Schema::dropIfExists('LoginDetails');
    }
}
