<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoLogosToBuilderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('builder', function (Blueprint $table) {
            //
            $table->text('SecondLogo')->nullable();
            $table->text('ThirdLogo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('builder', function (Blueprint $table) {
            $table->dropColumn('SecondLogo');
            $table->dropColumn('ThirdLogo');
        });
    }
}
