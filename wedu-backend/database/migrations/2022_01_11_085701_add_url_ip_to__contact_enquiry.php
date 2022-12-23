<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlIpToContactEnquiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ContactEnquiry', function (Blueprint $table) {
            $table->text('Url')->nullable();
            $table->text('IpAddress')->nullable();
            $table->text('Page')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ContactEnquiry', function (Blueprint $table) {
            //
            $table->dropColumn('Url');
            $table->dropColumn('IpAddress');
            $table->dropColumn('Page');

        });
    }
}
