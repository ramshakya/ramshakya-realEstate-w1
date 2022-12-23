<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('PushNotifications', function (Blueprint $table) {
            $table->bigIncrements('id', 11);
            $table->string('LoginUserId', 100)->nullable()->default('NULL');
            $table->text('LeadId');
            $table->text('Token');
            $table->text('Title');
            $table->text('Message');
            $table->string('EnableNotification', 100)->nullable()->default('NULL');
            $table->string('SendStatus', 100)->nullable()->default('NULL');
            $table->datetime('CreatedAt')->nullable();
            $table->datetime('UpdatedAt')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('PushNotifications');
    }
}
