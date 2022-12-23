<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsertrackerTableIntimeAndStaytimeDatatypeChanged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserTracker', function (Blueprint $table) {
            $table->dateTime('InTime')->change();
            $table->dateTime('StayTime')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('UserTracker');
    }
}
