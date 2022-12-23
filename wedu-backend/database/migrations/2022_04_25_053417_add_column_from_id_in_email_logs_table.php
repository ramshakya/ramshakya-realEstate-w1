<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFromIdInEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('EmailLogs', function (Blueprint $table) {
            //
            $table->tinyInteger('FromId')->default(0)->comment('CAMPAIGNS=1 SIGNUP=2 ENQUIRY=3 SCHEDULESHOWING=4 FORGETPASSWORD=5 SAVEDSEARCH=6 HOMEVALUE=7 RETSEMAILS=8');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('EmailLogs', function (Blueprint $table) {
            //
            $table->dropColumn('FromId');
        });
    }
}
