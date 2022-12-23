<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreConstructionBuildingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_construction_building', function (Blueprint $table) {
            $table->id();
            $table->string('AgentId')->nullable();;
            $table->text('BuildingName')->nullable();
            $table->text('BuilderId')->nullable();
            $table->text('Address')->nullable();
            $table->text('Country')->nullable();
            $table->text('City')->nullable();
            $table->text('State')->nullable();
            $table->text('MainInterection')->nullable();
            $table->text('PostelCode')->nullable();
            $table->text('DemoF')->nullable();
            $table->text('Community')->nullable();
            $table->text('Demo')->nullable();
            $table->text('AddrInfo')->nullable();
            $table->text('BuildingType')->nullable();
            $table->text('BuildingStatus')->nullable();
            $table->text('SaleStatus')->nullable();
            $table->text('SizeRange')->nullable();
            $table->text('PriceRange')->nullable();
            $table->text('Storeys')->nullable();
            $table->text('Suites')->nullable();
            $table->text('Bedroom')->nullable();
            $table->text('Bathroom')->nullable();
            $table->text('Possession')->nullable();
            $table->text('Content')->nullable();
            $table->text('Adrrr55')->nullable();
            $table->text('Asasasas')->nullable();
            $table->text('MediaImage')->nullable();
            $table->text('VideoLink')->nullable();
            $table->text('Attechments')->nullable();
            $table->text('Emenities')->nullable();
            $table->text('EmenitiesMaintenance')->nullable();
            $table->text('Map')->nullable();
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
        Schema::dropIfExists('pre_construction_building');
    }
}
