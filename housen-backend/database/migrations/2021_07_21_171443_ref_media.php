<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50);
            $table->string('title',50)->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->string('mime_type',100)->nullable();
            $table->string('size',100)->nullable();
            $table->text('hash')->nullable();
            $table->string('can_be_purged')->nullable();
            $table->string('purged_after_date')->nullable();
            $table->string('path')->nullable();
            $table->integer('status_id')->unsigned()->default(1)->index();
            $table->string('aws_bucket')->nullable();
            $table->string('aws_region')->nullable();
            $table->json('driver_details')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
        });
        \Illuminate\Support\Facades\DB::table('ref_media')->insert(
            [[
                'name' => 'Test Image',
                'path' => '/storage/profile_pic.jepg',
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
        Schema::dropIfExists('ref_media');
    }
}
