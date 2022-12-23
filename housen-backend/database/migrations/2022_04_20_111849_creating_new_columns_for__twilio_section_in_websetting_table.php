<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatingNewColumnsForTwilioSectionInWebsettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Websetting', function (Blueprint $table) {
            $table->text('TwilioSID');
            $table->text('TwilioNumber');
            $table->text('TwilioToken');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Websetting');
    }
}
