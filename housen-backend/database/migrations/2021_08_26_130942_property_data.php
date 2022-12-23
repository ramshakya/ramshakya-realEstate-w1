<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PropertyData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('PropertyData', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('StreetDirPrefix',50)->nullable();
            $table->string('CountryRegion',50)->nullable();
            $table->string('OtherParking',50)->nullable();
            $table->string('BuildingName',50)->nullable();
            $table->text('CoBuyerAgentStateLicense');
            $table->text('PropertyType');
            $table->text('MlsStatus');
            $table->text('PropertySubTypeAdditional');
            $table->text('StreetNumber');
            $table->text('ParkingTotal');
            $table->text('BuyerAgentStateLicense');
            $table->text('PropertySubType');
            $table->text('BathroomsFull');
            $table->text('LotSizeAcres');
            $table->text('SubdivisionName');
            $table->text('InternetAddressDisplayYN');
            $table->text('StateRegion');
            $table->text('BathroomsPartial');
            $table->text('PreviousListPrice');
            $table->text('StreetNumberNumeric');
            $table->text('PostalCodePlus4');
            $table->text('BuildingAreaSource');
            $table->text('BathroomsOneQuarter');
            $table->text('BuilderModel');
            $table->text('CoListAgentMlsId');
            $table->text('BedroomsPossible');
            $table->string('ListingId',30)->default('');
            $table->text('BathroomsTotalInteger');
            $table->text('BuildingAreaUnits');
            $table->text('City');
            $table->text('ListAgentNameSuffix');
            $table->text('BuildingAreaTotal');
            $table->text('BedroomsTotal');
            $table->text('CoListAgentDirectPhone');
            $table->text('Longitude');
            $table->text('PublicRemarks');
            $table->text('PostalCity');
            $table->text('CoListOfficeName');
            $table->text('ListOfficeName');
            $table->text('Latitude');
            $table->text('ListPrice');
            $table->text('CoListOfficeMlsId');
            $table->text('StateOrProvince');
            $table->text('BathroomsThreeQuarter');
            $table->text('MainLevelBathrooms');
            $table->text('CoBuyerAgentMlsId');
            $table->text('StreetSuffix');
            $table->text('ListAgentMlsId');
            $table->text('CoListAgentNameSuffix');
            $table->text('CoBuyerOfficeMlsId');
            $table->text('ListAgentNamePrefix');
            $table->text('Country');
            $table->text('UnitNumber');
            $table->text('ListOfficeMlsId');
            $table->text('ListAgentDirectPhone');
            $table->text('BathroomsHalf');
            $table->text('ListAgentStateLicense');
            $table->text('StreetName');
            $table->text('MainLevelBedrooms');
            $table->text('CityRegion');
            $table->text('BuyerAgentMlsId');
            $table->text('PostalCode');
            $table->text('BuyerOfficeMlsId');
            $table->text('LotSizeSquareFeet');
            $table->text('UnparsedAddress');
            $table->datetime('InsertedTime')->nullable();
            $table->datetime('UpdatedTime')->nullable();
            $table->tinyInteger('ImageDownloaded')->default('0');
            $table->datetime('ImageDownloadedTime')->nullable();
            $table->integer('ImageDownloadTried')->default('0');
            $table->tinyInteger('ImageAwsSync')->default('0');
            $table->text('Heating');
            $table->text('Cooling');
            $table->text('PoolFeatures');
            $table->text('ClosePrice');
            $table->text('LotSizeArea');
            $table->text('WaterfrontYN');
            $table->text('PoolPrivateYN');
            $table->integer('mls_no')->default('1');
            $table->text('ListingKeyNumeric');
            $table->text('Furnished');
            $table->text('Address');
            $table->text('FullAddress');
            $table->text('SimpleAddress');
            $table->text('LivingArea');
            $table->text('PrivateRemarks');
            $table->text('ImagesUrls');
            $table->text('County');
            $table->text('ListAgentFormattedName');
            $table->text('ListAgentFullName');
            $table->text('ListAgentEmail');
            $table->text('YearBuilt');
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
        Schema::dropIfExists('PropertyData');
    }
}
