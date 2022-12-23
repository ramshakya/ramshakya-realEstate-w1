<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AgentId');
            $table->text('PageName')->nullable();
            $table->text('PageUrl')->nullable();
            $table->text('Content')->nullable();
            $table->text('MetaTitle')->nullable();
            $table->text('MetaTags')->nullable();
            $table->text('MetaDescription')->nullable();
            $table->timestamps();
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
