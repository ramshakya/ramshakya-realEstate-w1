<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LeadProfiles', function (Blueprint $table) {
            $table->text('Reqsent');
            $table->string('ABAReqSent')->default('No');
            $table->text('LeadAccptYorn');
            $table->text('BetaUser');
            $table->string('AgentActive',50)->default('No');
            $table->string('ListOfficeName',50);
            $table->text('ListAOR');
            $table->string('mls_no',20);
            $table->string('MlsName',40);
            $table->string('AgentOfficeMlsId',30);
            $table->string('ListAgentFullName');
            $table->text('CustomPropertyType');
            $table->text('ZipCodes');
            $table->text('ZipLockFlags');
            $table->text('ZipRequested');
            $table->text('Citys');
            $table->text('Tags');
            $table->text('Languages');
            $table->string('ListAgentMlsId',30);
            $table->text('IsKWAgent');
            $table->bigIncrements('id');
            $table->string('AgentType',50);
            $table->text('SellerLeads');
            $table->text('AssignedLA_ID');
            $table->string('IsAlsoListAgent',50)->default('No');
            $table->text('Carrier');
            $table->string('CarrierId',30)->nullable()->default('NULL');
            $table->string('ListOfficePhone',30);
            $table->string('password',100);
            $table->string('ListAgentDirectPhone',30);
            $table->string('FormattedPhone',40)->nullable()->default('NULL');
            $table->string('UnformattedPhone',40)->nullable()->default('NULL');
            $table->string('ListAgentEmail');
            $table->text('CMDLinx');
            $table->string('CMDLinxPropmpt',30)->default('Yes');
            $table->text('Gsuite');
            $table->text('Creds');
            $table->string('AgentHeadshot',30)->default('N');
            $table->text('AgentHeadshotUrl');
            $table->text('MLSNumbers');
            $table->text('MLSNumbersBuy');
            $table->text('MLSNumbersList');
            $table->text('Addresses');
            $table->text('BuyCount');
            $table->text('ListSold');
            $table->text('MaxBuyPrice');
            $table->text('MaxSoldPrice');
            $table->text('FullMailingAddress');
            $table->text('SpecializationRequested');
            $table->string('Specialization0',50);
            $table->string('Min0',40);
            $table->string('Max0',40);
            $table->string('Specialization1',40);
            $table->string('Min1',40);
            $table->string('Max1',40);
            $table->string('Specialization2',40);
            $table->string('Min2',40);
            $table->string('Max2',40);
            $table->string('Specialization3',40);
            $table->string('Min3',40);
            $table->string('Max3',40);
            $table->string('Specialization4',40);
            $table->string('Min4',40);
            $table->string('Max4',40);
            $table->string('Specialization5',40);
            $table->string('Min5',40);
            $table->string('Max5',40);
            $table->string('Specialization6',40);
            $table->string('Min6',40);
            $table->string('Max6',40);
            $table->text('Specialization7');
            $table->text('Min7');
            $table->text('Max7');
            $table->text('Specialization8');
            $table->text('Min8');
            $table->text('Max8');
            $table->tinyInteger('QueueFlag')->default('0');
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->datetime('ABAReqSentTime')->nullable();
            $table->string('CampReqSent')->default('No');
            $table->text('LeadAccptsent');
            $table->string('ABA')->default('No');
            $table->text('HasIDXSITE');
            $table->string('ImportedBy',50)->nullable()->default('NULL');
            $table->string('BoardIdentifier');
            $table->text('IDXOfficename');
            $table->text('LeadAccptReply');
            $table->text('State');
            $table->text('City');
            $table->text('ABAStatus');
            $table->string('ABAReplyRec')->default('No');
            $table->string('ActionLabel',50)->nullable()->default('NULL');
            $table->string('Source',100)->nullable()->default('NULL');
            $table->text('AccessToken');
            $table->text('RefreshToken');
            $table->string('GAuthIp');
            $table->datetime('GtokenAddedAt')->nullable();
            $table->datetime('GtokenUpdatedAt')->nullable();
            $table->text('ResetLink');
            $table->text('ResetCode');
            $table->datetime('ResetLinkTime');
            $table->text('EscrowPhone');
            $table->text('EscrowEmail');
            $table->text('EscrowFax');
            $table->text('EscrowAgent');
            $table->text('EscrowAddress');
            $table->text('County');
            $table->text('AgentState');
            $table->string('DarkMode',100)->nullable()->default('NULL');
            $table->text('CompactMode');
            $table->text('LeadListMode');
            $table->text('PushToken');
            $table->text('EnableDesktopNotification');
            $table->text('DesktopPushToken');
            $table->text('EnableNotification');
            $table->string('EnableSmsAlert',100)->nullable()->default('NULL');
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
        Schema::dropIfExists('LeadProfiles');
    }
}
