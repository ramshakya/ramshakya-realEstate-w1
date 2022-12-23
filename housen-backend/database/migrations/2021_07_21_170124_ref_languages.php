<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefLanguages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->string('description',100)->nullable();
            $table->string('code',100)->nullable();
            $table->string('icon_as_css',100)->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->integer('status_id')->unsigned()->default('1')->index();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
        });
        \Illuminate\Support\Facades\DB::table('ref_languages')->insert(
            [[
                'name' => 'English',
                'description' => "This is for the English Language",
                'status_id' => 1
            ],[
                'name' => 'Hindi',
                'description' => "This is for the Hindi Language",
                'status_id' => 1
            ]]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_languages');
    }
}
