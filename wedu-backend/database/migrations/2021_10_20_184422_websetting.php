<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Websetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('Websetting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('AdminId')->unsigned()->index();
            $table->string('WebsiteName',255)->nullable();
            $table->string('WebsiteTitle',255)->nullable();
            $table->string('PhoneNo',20)->nullable();
            $table->string('WebsiteEmail',255)->nullable();
            $table->string('FromEmail',255)->nullable();
            $table->string('EmailPassword',255)->nullable();
            $table->string('GoogleAnalyticsCode',255)->nullable();
            $table->string('FacebookPixelCode',255)->nullable();
            $table->string('MapApiKey',255)->nullable();
            $table->string('FrontSiteTheme',255)->nullable();
            $table->string('WebsiteAddress',255)->nullable();
            $table->string('UploadLogo',255)->nullable();
            $table->string('LogoAltTag',255)->nullable();
            $table->string('Favicon',255)->nullable();
            $table->text('ScriptTag')->nullable();
            $table->timestamps();
            $table->foreign('AdminId')->references('id')->on('users');
            $table->string('YelpKey')->nullable();
            $table->string('YelpClientId')->nullable();
            $table->string('WebsiteColor')->nullable();
            $table->string('WebsiteMapColor')->nullable();
            $table->string('GoogleMapApiKey')->nullable();
            $table->string('HoodQApiKey')->nullable();
            $table->string('WalkScoreApiKey')->nullable();
            $table->string('FavIconAltTag')->nullable();
            $table->string('FacebookUrl')->nullable();
            $table->string('TwitterUrl')->nullable();
            $table->string('LinkedinUrl')->nullable();
            $table->string('InstagramUrl')->nullable();
            $table->string('YoutubeUrl')->nullable();
            $table->string('TopBanner')->nullable();
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
        Schema::dropIfExists('Websetting');
    }
}
