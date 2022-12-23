<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Blog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('Blogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('AdminId')->nullable();
            $table->string('Title',255)->nullable();
            $table->string('MetaTitle',255)->nullable();
            $table->string('MetaKeyword',255)->nullable();
            $table->text('MetaDesc')->nullable();
            $table->text('Url')->nullable();
            $table->string('Categories',255)->nullable();
            $table->text('MainImg')->nullable();
            $table->text('ImgTags')->nullable();
            $table->text('Content')->nullable();
            $table->text('BlogTags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('Blogs');
    }
}
