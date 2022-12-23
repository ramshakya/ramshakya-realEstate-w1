<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PageView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('PageView', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('LeadId')->default(0);
            $table->integer('PageId')->nullable();
            $table->text('PageUrl')->nullable();
            $table->text('IPaddress')->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('PageView')->insert(
            [[
                'LeadId' => 13581,
                'PageId' => 1,
                'PageUrl' => "http://ec2-18-117-105-201.us-east-2.compute.amazonaws.com/agent/leadview/13611",
                "IPaddress" => "127:0:0:1"
            ],[
                'LeadId' => 13582,
                'PageId' => 1,
                'PageUrl' => "http://ec2-18-117-105-201.us-east-2.compute.amazonaws.com/agent/lead",
                "IPaddress" => "127:0:0:1"
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
        Schema::dropIfExists('PageView');
    }
}
