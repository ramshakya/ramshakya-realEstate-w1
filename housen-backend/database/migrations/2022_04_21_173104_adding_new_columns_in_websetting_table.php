<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingNewColumnsInWebsettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Websetting', function (Blueprint $table) {
            $table->text('GoogleClientId');
            $table->text('FbAppId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Websetting', function (Blueprint $table) {
            $table->dropColumn('GoogleClientId');
            $table->dropColumn('FbAppId');
          });
    }
}
