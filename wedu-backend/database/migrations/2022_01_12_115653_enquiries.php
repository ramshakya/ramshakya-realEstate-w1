<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Enquiries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // php artisan migrate --path=database/migrations/2022_01_12_115653_enquiries.php
    public function up()
    {
        Schema::table('Enquiries', function (Blueprint $table) {
        $table->id();
        $table->string('name');
		$table->string('email');
		$table->string('phone');
		$table->string('best_time_to_call')->nullable();
		$table->string('purchase_price')->nullable();
		$table->string('down_payment')->nullable();
		$table->string('total_mortgage')->nullable();
		$table->string('page_from');
		$table->datetime('created_at')->default('current_timestamp');
		$table->integer('agent_id',11)->nullable();
		$table->tinyInteger('status_id',1)->nullable();
		$table->string('follow_up');
		$table->string('property_id')->nullable();
		$table->string('property_url')->nullable();
		$table->integer('user_id',11)->nullable();
		$table->string('message');
		$table->string('schedule_a_showing');
		$table->string('user_ip');
		$table->string('date');
		$table->string('time');
		$table->string('booking_start_time')->nullable();
		$table->string('booking_end_time')->nullable();
		$table->string('realtor',250)->nullable();
		$table->string('property_size');
		$table->string('home_style');
		$table->string('pro_type');
		$table->string('garage_type');
		$table->string('bedrooms');
		$table->string('bathrooms');
		$table->string('Bsmt1_out');
		$table->string('propertyaddress');
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
        Schema::dropIfExists('Enquiries');
    }
}
