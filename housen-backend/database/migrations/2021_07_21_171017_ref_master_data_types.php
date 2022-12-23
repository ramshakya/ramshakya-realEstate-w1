<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefMasterDataTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_master_data_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',50)->nullable();
            $table->string('name',50)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_as_css',100)->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->integer('status_id')->unsigned()->default('1')->index();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
        });

        \Illuminate\Support\Facades\DB::table('ref_master_data_types')->insert(
            [[
                'name' => 'Gender',
                'description' => "This is for the gender",
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
        Schema::dropIfExists('ref_master_data_types');
    }
}
