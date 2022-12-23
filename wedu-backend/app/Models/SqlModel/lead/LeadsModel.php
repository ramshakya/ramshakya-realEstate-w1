<?php

namespace App\Models\SqlModel\lead;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class LeadsModel extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "Leads";
    protected $gaurd = "leads";
    public $timestamps = false;
    protected $fillable = ['Source', 'PropType', 'LeadType', 'Prospect', 'Status', 'MlsStatus', 'PreviousStatus', 'RefProcessed', 'StatusProcessed', 'created_at', 'id', 'AssignedAgentName', 'Price', 'mls_id', 'Address', 'Unit', 'Beds', 'Baths', 'AdditionalProperties', 'AdditionalPropertiesText', 'City', 'State', 'Zipcode', 'County', 'ContactName', 'Email', 'EmailStatus', 'Phone', 'FormattedPhone', 'UnformattedPhone', 'PhoneDNCStatus', 'PhoneLabel', 'PhoneType', 'AddTenantName', 'AddTenantPhone', 'AddTenantEmail', 'AddOwnerName', 'AddOwnerPhone', 'AddOwnerEmail', 'AssgnAgentPhone', 'AssgnAgentEmail', 'AssgnAgentOffice', 'ListAgentFullName', 'ListAgentEmail', 'ListAgentDirectPhone', 'ShowingInstruction', 'TotalBeds', 'Message', 'ShowingRequest', 'Offers', 'MoveInDate', 'CreditScore', 'Income', 'JobTitle', 'Employer', 'EmployedSince', 'PastJobs', 'ReasonForMoving', 'Pets', 'Smoker', 'LeaseLength', 'Parking', 'DesiredNeighbourhood', 'FurnishedInfo', 'IpAddress', 'HaveRealtor', 'AssignmentDateTime', 'updated_at', 'CreatedSyncAt', 'ChgStatusAt', 'GcontactID', 'AgentGContactId', 'UpdatSyncAt', 'Date', 'RefProcessedTried', 'ReferralAgent', 'AssignedAgent', 'GeoSource', 'FullAddress', 'NeedsUpdate', 'AgentupdateAt', 'Synced', 'ContactCmdUrl', 'ContractDate', 'ContractOccupancy', 'ContractPrice', 'ContractTerm', 'ContractValue', 'ContractGCI', 'ContractComType', 'ContractComPer', 'ContractRefSplit', 'ContractRefAmt', 'ContractAddress', 'UnitNum', 'ContractAddressNounit', 'ContractCity', 'AgentNotes', 'RejectedReason', 'LostReason', 'Prc', 'ReassgnReason', 'StatusHistory', 'AllOtherOwnerAndTenant', 'CoLeads', 'Tags', 'CampaignTags', 'PeopleContactId', 'AgentPeopleContactId', 'FirstCallDate', 'HotProspectPoints', 'Rating', 'TotalCalls', 'Phone2', 'Phone2DNCStatus', 'Phone2Label', 'Phone2Type', 'Phone3', 'Phone3DNCStatus', 'Phone3Label', 'Phone3Type', 'Phone4', 'Phone4DNCStatus', 'Phone4Label', 'Phone4Type', 'Phone5', 'Phone5DNCStatus', 'Phone5Label', 'Phone5Type', 'ExpiredDate', 'CaseNumber', 'JudgementAmount', 'JudgementDate', 'JudgementSaleDate', 'PrimaryPlaintiff', 'Folder', 'Motivation','Password','Seen'
    ];
    // public function agent()
    // {
    //     return $this->belongsTo('App\Models\sqlModel\agent\LeadAgentModel', 'id');
    // }

    public function updateData($_id, $data)
    {
        LeadsModel::where("id", $_id)->update($data);
    }

    public static function getLead($_id)
    {
        return LeadsModel::where("id", $_id)->get();
    }

    public function getAssignedAgent($lead_id)
    {
        return LeadsModel::where("id", $lead_id)
            ->where("AssignedAgent", "")
            ->orWhere("AssignedAgent", NULL)
            ->orWhere("AssignedAgent", "SA")->get();
    }

    public function getLeadforcefullassignment($lead_id)
    {
        return LeadsModel::where("id", $lead_id)->get();
    }

    public function getAssignedAgentElse($lead_id)
    {
        return LeadsModel::where("id", $lead_id)
            ->where("AssignedAgent", "")
            ->orWhere("AssignedAgent", NULL)
            ->get();
    }

    public function getMaxAssignedAgent($curr_agentsid)
    {
        $results = LeadsModel::select("AssignedAgent")
            ->whereIn("AssignedAgent", $curr_agentsid)
            //->groupBy("AssignedAgent")
            ->orderBy("id", 'DESC')
            ->get();
        $results = collect($results)->groupBy("AssignedAgent")->map(function ($item) {
            return collect($item)->first();
        })->all();

        return $results;
    }

    public function insertData($requet)
    {
        $id = LeadsModel::create($requet)->id;
        return $id;
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'Password',
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
    function get_userinfo($request){
         $data=LeadsModel::where('Email', $request->Email)
         ->where('Password', md5($request->Password))
         ->first();
         
         return $data;
      }
}
