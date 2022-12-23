<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetsPropertyDataSoldImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RetsPropertyDataSoldImagesSql', function (Blueprint $table) {
            $table->id();
            $table->integer('mls_no')->default('1');
            $table->string('listingID')->nullable()->default('NULL');
            $table->string('image_directory', 250);
            $table->string('image_path');
            $table->text('image_url')->nullable()->default('NULL');
            $table->text('s3_image_url')->nullable()->default('NULL');
            $table->string('image_name', 100);
            $table->datetime('downloaded_time');
            $table->tinyInteger('is_uploaded_by_agent')->default('0');
            $table->datetime('updated_time');
            $table->timestamp('image_last_tried_time')->nullable();
            $table->tinyInteger('is_download')->default('0');
            $table->tinyInteger('is_resized1')->default('0');
            $table->tinyInteger('is_resized2')->default('0');
            $table->tinyInteger('is_resized3')->default('0');
            $table->integer('sort_order_no')->nullable();
            $table->string('mls_order', 20)->nullable()->default('NULL');
            $table->integer('property_id')->nullable();
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
        Schema::dropIfExists('rets_property_data_sold_images');
    }
}
