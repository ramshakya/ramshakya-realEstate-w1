<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePredefinedPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('predefined_page', function (Blueprint $table) {
            $table->id();
            $table->string('AgentId');
            $table->text('PageName')->nullable();
            $table->text('PageUrl')->nullable();
            $table->text('MetaTitle')->nullable();
            $table->text('MetaTags')->nullable();
            $table->text('MetaDescription')->nullable();
            $table->text('MlsStatus')->nullable();
            $table->text('Area')->nullable();
            $table->text('Bathrooms')->nullable();
            $table->text('Bedrooms')->nullable();
            $table->text('City')->nullable();
            $table->text('MinPrice')->nullable();
            $table->text('MaxPrice')->nullable();
            $table->text('PropertyType')->nullable();
            $table->text('SqftRange')->nullable();
            $table->text('ZipCode')->nullable();
            $table->enum('Status',['Active','Inactive','Deleted'])->nullable();
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
        Schema::dropIfExists('predefined_page');
    }
}
