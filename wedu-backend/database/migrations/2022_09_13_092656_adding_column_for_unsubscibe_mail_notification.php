<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingColumnForUnsubscibeMailNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('SavedSearchFilter', function (Blueprint $table) {
            $table->text('subscribe')->default(1);
            $table->text('emailHash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('SavedSearchFilter', function (Blueprint $table) {
            $table->dropColumn('subscribe');
            $table->dropColumn('emailHash');
        });
    }
}