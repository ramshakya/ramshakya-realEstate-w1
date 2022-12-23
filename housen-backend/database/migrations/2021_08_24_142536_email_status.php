<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('email_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('FromEmail',50)->nullable();
            $table->string('ToEmail',50)->nullable();
            $table->string('ToCc',50)->nullable();
            $table->string('ToBcc',50)->nullable();
            $table->string('Subject',50)->nullable();
            $table->text('Content')->nullable();
            $table->string('Method',50)->nullable();
            $table->string('FromMethod',400)->nullable();
            $table->string('DeliveredTime',400)->nullable();
            $table->string('Description',100)->nullable();
            $table->boolean('IsSent')->default(0)->nullable();
            $table->boolean('IsRead')->default(0)->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->integer('status_id')->unsigned()->default('1')->index();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('status_id')->references('id')->on('ref_statuses');
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
        Schema::dropIfExists('email_logs');
    }
}
