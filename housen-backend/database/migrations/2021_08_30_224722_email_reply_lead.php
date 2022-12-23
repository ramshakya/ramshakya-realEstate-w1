<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmailReplyLead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('EmailReplyLead', function (Blueprint $table) {
            $table->bigIncrements('id',11);
            $table->string('FromName')->nullable()->default('NULL');
            $table->string('FromEmail')->nullable()->default('NULL');
            $table->string('ToName')->nullable()->default('NULL');
            $table->string('ToEmail')->nullable()->default('NULL');
            $table->string('OriginalEmail')->nullable()->default('NULL');
            $table->integer('Timestamp')->nullable();
            $table->string('Date',50)->nullable()->default('NULL');
            $table->text('Subject');
            $table->string('MessagePart',400)->nullable()->default('NULL');
            $table->tinyInteger('IsRead')->default('0');
            $table->string('ABADraft',50)->nullable()->default('NULL');
            $table->datetime('SyncAt');
            $table->string('LeadEmail')->nullable()->default('NULL');
            $table->string('LeadEmail2')->nullable()->default('NULL');
            $table->text('Address');
            $table->text('EmailContent');
            $table->text('WithoutHtmlEmailContent');
            $table->text('Reply');
            $table->string('LeadName')->nullable()->default('NULL');
            $table->string('LeadPhone')->nullable()->default('NULL');
            $table->string('LeadDate')->nullable()->default('NULL');
            $table->string('Beds',30)->nullable()->default('NULL');
            $table->string('Baths',30)->nullable()->default('NULL');
            $table->string('Model',100)->nullable()->default('NULL');
            $table->string('Unit',100)->nullable()->default('NULL');
            $table->string('Price',40)->nullable()->default('NULL');
            $table->string('MoveInDate')->nullable()->default('NULL');
            $table->text('Comments');
            $table->text('property_link');
            $table->string('AddressWithUnit',300)->nullable()->default('NULL');
            $table->string('Address1',300)->nullable()->default('NULL');
            $table->string('Address2',300)->nullable()->default('NULL');
            $table->string('City')->nullable()->default('NULL');
            $table->string('Zipcode',30)->nullable()->default('NULL');
            $table->string('State',100)->nullable()->default('NULL');
            $table->string('MatchingMlsid',50)->nullable()->default('NULL');
            $table->text('AdditionalProperties');
            $table->text('StreetNumber');
            $table->text('StreetDirPrefix');
            $table->text('StreetName');
            $table->text('StreetSuffix');
            $table->text('UnitNumber');
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
        Schema::dropIfExists('EmailReplyLead');
    }
}
