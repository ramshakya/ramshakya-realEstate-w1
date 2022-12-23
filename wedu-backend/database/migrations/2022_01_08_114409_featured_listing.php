<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FeaturedListing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('featured_listings', function (Blueprint $table) {
            $table->id();
            $table->string('PropertyId')->nullable();
            $table->string('AgentId')->nullable();
            $table->integer('StatusId')->default(1);
            $table->string('ListingId')->nullable();
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
        Schema::dropIfExists('featured_listings');
    }
}
