<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EmailTemplates', function (Blueprint $table) {
            $table->integer('id',11);
            $table->string('TemplateName',100);
            $table->text('Content');
            $table->text('Subject')->nullable()->default('NULL');
            $table->integer('Status')->default(1);
            $table->datetime('AddedTime');
            $table->datetime('UpdatedTime');
            $table->tinyInteger('Type')->default(0);

        });
    }

    public function down()
    {
        Schema::dropIfExists('EmailTemplates');
    }
}
