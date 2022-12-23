<?php
namespace App\Models\SqlModel\lead;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
class Lead extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['Source', 'PropType', 'LeadType', 'Prospect', 'Status', 'MlsStatus', 'PreviousStatus', 'RefProcessed', 'StatusProcessed', 'created_at', 'id', 'AssignedAgentName', 'Price', 'mls_id', 'Address', 'Unit', 'Beds', 'Baths', 'AdditionalProperties', 'AdditionalPropertiesText', 'City', 'State', 'Zipcode', 'County', 'ContactName', 'Email', 'EmailStatus', 'Phone', 'FormattedPhone', 'UnformattedPhone', 'PhoneDNCStatus', 'PhoneLabel', 'PhoneType', 'AddTenantName', 'AddTenantPhone', 'AddTenantEmail', 'AddOwnerName', 'AddOwnerPhone', 'AddOwnerEmail', 'AssgnAgentPhone', 'AssgnAgentEmail', 'AssgnAgentOffice', 'ListAgentFullName', 'ListAgentEmail', 'ListAgentDirectPhone', 'ShowingInstruction', 'TotalBeds', 'Message', 'ShowingRequest', 'Offers', 'MoveInDate', 'CreditScore', 'Income', 'JobTitle', 'Employer', 'EmployedSince', 'PastJobs', 'ReasonForMoving', 'Pets', 'Smoker', 'LeaseLength', 'Parking', 'DesiredNeighbourhood', 'FurnishedInfo', 'IpAddress', 'HaveRealtor', 'AssignmentDateTime', 'updated_at', 'CreatedSyncAt', 'ChgStatusAt', 'GcontactID', 'AgentGContactId', 'UpdatSyncAt', 'Date', 'RefProcessedTried', 'ReferralAgent', 'AssignedAgent', 'GeoSource', 'FullAddress', 'NeedsUpdate', 'AgentupdateAt', 'Synced', 'ContactCmdUrl', 'ContractDate', 'ContractOccupancy', 'ContractPrice', 'ContractTerm', 'ContractValue', 'ContractGCI', 'ContractComType', 'ContractComPer', 'ContractRefSplit', 'ContractRefAmt', 'ContractAddress', 'UnitNum', 'ContractAddressNounit', 'ContractCity', 'AgentNotes', 'RejectedReason', 'LostReason', 'Prc', 'ReassgnReason', 'StatusHistory', 'AllOtherOwnerAndTenant', 'CoLeads', 'Tags', 'CampaignTags', 'PeopleContactId', 'AgentPeopleContactId', 'FirstCallDate', 'HotProspectPoints', 'Rating', 'TotalCalls', 'Phone2', 'Phone2DNCStatus', 'Phone2Label', 'Phone2Type', 'Phone3', 'Phone3DNCStatus', 'Phone3Label', 'Phone3Type', 'Phone4', 'Phone4DNCStatus', 'Phone4Label', 'Phone4Type', 'Phone5', 'Phone5DNCStatus', 'Phone5Label', 'Phone5Type', 'ExpiredDate', 'CaseNumber', 'JudgementAmount', 'JudgementDate', 'JudgementSaleDate', 'PrimaryPlaintiff', 'Folder', 'Motivation','Password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
