<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingNewColumnInUsertracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserTracker', function (Blueprint $table) {
            //
            $table->text('PropertyUrl')->nullable();
            $table->text('ListingId')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('UserTracker', function (Blueprint $table) {
            //
            $table->dropColumn('PropertyUrl');
        });
    }
}
