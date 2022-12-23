<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeildsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone_number', 50);
            $table->bigInteger('profile_image_id')->unsigned()->index()->nullable();
            $table->string('last_login')->nullable();
            $table->string('domain')->nullable();
            $table->string('alt_address')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('alt_email')->nullable();
            $table->string('identification_document_type_id')->nullable();
            $table->string('identification_document_number')->nullable();
            $table->string('identification_document_issue_authority')->nullable();
            $table->string('age')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('social_mobile')->nullable();
            $table->string('fb_id')->nullable();
            $table->string('fb_friends_list')->nullable();
            $table->integer('is_email_verified')->default(0);
            $table->integer('gender_id')->unsigned()->index()->nullable();
            $table->integer('country_id')->unsigned()->index()->nullable();
            $table->integer('person_id')->unsigned()->index();
            $table->integer('status_id')->unsigned()->default(1)->index();
            $table->foreign('profile_image_id')->references('id')->on('ref_media');
            $table->foreign('status_id')->references('id')->on('ref_statuses');
            $table->foreign('gender_id')->references('id')->on('ref_master_data');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('person_id')->references('id')->on('persons');
        });

        \Illuminate\Support\Facades\DB::table('users')->insert(
            [[
                'first_name' => 'Super',
                'name' => 'Super Admin',
                'last_name' => 'Admin',
                'phone_number' => '0987564321',
                'email' => 'superadmin@gmail.com',
                'password' => '$2y$10$L4B2ZWMm8WyuotR9EwhZzeuXmHjkGlE1o48qubc7.UmBLeAYLfH6S',
                'profile_image_id' => 1,
                'gender_id' => 1,
                'country_id' => 1,
                'person_id' => 1,
                'type' => 1,
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
        Schema::table('users', function (Blueprint $table) {
            //1. Drop foreign constraints
            $table->dropForeign(['profile_image_id', 'status_id', 'gender_id', 'country_id', 'person_id']);
            // 2. Drop the column
            $table->dropColumn('store_id', 'first_name', 'last_name', 'phone_number', 'last_login',
                'domain', 'alt_address', 'alt_phone', 'alt_email', 'identification_document_type_id', 'identification_document_number',
                'identification_document_issue_authority', 'age', 'date_of_birth', 'social_mobile', 'fb_id', 'fb_friends_list', 'is_email_verified');
        });
    }
}
