<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Leads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('Leads', function (Blueprint $table) {
            $table->string('Source',500)->nullable()->default('NULL');
            $table->text('PropType');
            $table->text('LeadType');
            $table->text('Prospect');
            $table->string('Status',50);
            $table->text('MlsStatus');
            $table->text('PreviousStatus');
            $table->text('RefProcessed');
            $table->text('StatusProcessed');
            $table->datetime('created_at');
            $table->bigIncrements('id');
            $table->string('AssignedAgentName')->default('Not Assigned');
            $table->string('Price',30);
            $table->string('mls_id',40)->nullable()->default('NULL');
            $table->text('Address');
            $table->string('Unit',20);
            $table->string('Beds',10)->nullable()->default('NULL');
            $table->string('Baths',10)->nullable()->default('NULL');
            $table->string('Additional_properties',400);
            $table->text('AdditionalPropertiesText');
            $table->string('City');
            $table->string('State');
            $table->string('Zipcode',10);
            $table->text('County');
            $table->string('ContactName')->nullable()->default('NULL');
            $table->string('Email')->nullable()->default('NULL');
            $table->text('EmailStatus');
            $table->string('Phone',15);
            $table->string('FormattedPhone',20)->nullable()->default('NULL');
            $table->string('UnformattedPhone',50)->nullable()->default('NULL');
            $table->text('PhoneDNCStatus');
            $table->text('PhoneLabel');
            $table->text('PhoneType');
            $table->text('AddTenantName');
            $table->text('AddTenantPhone');
            $table->text('AddTenantEmail');
            $table->text('AddOwnerName');
            $table->text('AddOwnerPhone');
            $table->text('AddOwnerEmail');
            $table->text('AssgnAgentPhone');
            $table->text('AssgnAgentEmail');
            $table->text('AssgnAgentOffice');
            $table->string('ListAgentFullName',300)->nullable()->default('NULL');
            $table->string('ListAgentEmail')->nullable()->default('NULL');
            $table->string('ListAgentDirectPhone',30)->nullable()->default('NULL');
            $table->text('ShowingInstruction');
            $table->integer('TotalBeds');
            $table->text('Message');
            $table->text('ShowingRequest');
            $table->string('Offers');
            $table->string('MoveInDate',50);
            $table->string('CreditScore',10);
            $table->string('Income',30);
            $table->string('JobTitle',50);
            $table->string('Employer');
            $table->string('EmployedSince',50);
            $table->text('PastJobs');
            $table->text('ReasonForMoving');
            $table->string('Pets',250);
            $table->text('Smoker');
            $table->string('LeaseLength',50);
            $table->string('Parking',200);
            $table->string('DesiredNeighbourhood',200);
            $table->string('FurnishedInfo',200);
            $table->string('IpAddress',50);
            $table->string('HaveRealtor',20);
            $table->datetime('AssignmentDateTime');
            $table->datetime('updated_at');
            $table->datetime('CreatedSyncAt');
            $table->datetime('ChgStatusAt')->nullable();
            $table->text('GcontactID');
            $table->text('AgentGContactId');
            $table->datetime('UpdatSyncAt');
            $table->text('Date');
            $table->integer('RefProcessedTried')->default('0');
            $table->string('ReferralAgent',10)->nullable()->default('NULL');
            $table->string('AssignedAgent')->nullable()->default('NULL');
            $table->string('GeoSource',50)->nullable()->default('NULL');
            $table->text('FullAddress');
            $table->text('NeedsUpdate');
            $table->date('AgentupdateAt')->nullable();
            $table->text('Synced');
            $table->text('ContactCmdUrl');
            $table->date('ContractDate')->nullable();
            $table->date('ContractOccupancy')->nullable();
            $table->text('ContractPrice');
            $table->text('ContractTerm');
            $table->text('ContractValue');
            $table->text('ContractGCI');
            $table->text('ContractComType');
            $table->text('ContractComPer');
            $table->text('ContractRefSplit');
            $table->text('ContractRefAmt');
            $table->text('ContractAddress');
            $table->text('UnitNum');
            $table->text('ContractAddressNounit');
            $table->text('ContractCity');
            $table->text('AgentNotes');
            $table->text('RejectedReason');
            $table->text('LostReason');
            $table->tinyInteger('Prc')->default('0');
            $table->text('ReassgnReason');
            $table->text('StatusHistory');
            $table->text('AllOtherOwnerAndTenant');
            $table->text('CoLeads');
            $table->text('Tags');
            $table->text('CampaignTags');
            $table->text('PeopleContactId');
            $table->text('AgentPeopleContactId');
            $table->text('FirstCallDate');
            $table->text('HotProspectPoints');
            $table->text('Rating');
            $table->text('TotalCalls');
            $table->text('Phone2');
            $table->text('Phone2DNCStatus');
            $table->text('Phone2Label');
            $table->text('Phone2Type');
            $table->text('Phone3');
            $table->text('Phone3DNCStatus');
            $table->text('Phone3Label');
            $table->text('Phone3Type');
            $table->text('Phone4');
            $table->text('Phone4DNCStatus');
            $table->text('Phone4Label');
            $table->text('Phone4Type');
            $table->text('Phone5');
            $table->text('Phone5DNCStatus');
            $table->text('Phone5Label');
            $table->text('Phone5Type');
            $table->text('ExpiredDate');
            $table->text('CaseNumber');
            $table->text('JudgementAmount');
            $table->text('JudgementDate');
            $table->text('JudgementSaleDate');
            $table->text('PrimaryPlaintiff');
            $table->text('Folder');
            $table->text('Motivation');
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
        Schema::dropIfExists('Leads');
    }
}