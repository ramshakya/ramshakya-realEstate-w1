<?php

namespace App\Models\SqlModel\agent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
class LeadAgentModel extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;
    protected $table = "LeadAgentProfile";
    protected $guard = 'agentprofile';
    protected $fillable = [
        'ReqSent',
        'ABAReqSent',
        'LeadAccptYorn',
        'BetaUser',
        'AgentActive',
        'ListOfficeName',
        'ListAOR',
        'mls_no',
        'MlsName',
        'AgentOfficeMlsId',
        'ListAgentFullName',
        'CustomPropertyType',
        'ZipCodes',
        'ZipLockFlags',
        'ZipRequested',
        'Citys',
        'Tags',
        'Languages',
        'ListAgentMlsId',
        'IsKWAgent',
        'id',
        'AgentType',
        'SellerLeads',
        'AssignedLAID',
        'IsAlsoListAgent',
        'Carrier',
        'CarrierId',
        'ListOfficePhone',
        'password',
        'ListAgentDirectPhone',
        'FormattedPhone',
        'UnformattedPhone',
        'ListAgentEmail',
        'CMDLinx',
        'CMDLinxPropmpt',
        'Gsuite',
        'Creds',
        'AgentHeadshot',
        'AgentHeadshotUrl',
        'MLSNumbers',
        'MLSNumbersBuy',
        'MLSNumbersList',
        'Addresses',
        'BuyCount',
        'ListSold',
        'MaxBuyPrice',
        'MaxSoldPrice',
        'FullMailingAddress',
        'SpecializationRequested',
        'Specialization0',
        'Min0',
        'Max0',
        'Specialization1',
        'Min1',
        'Max1',
        'Specialization2',
        'Min2',
        'Max2',
        'Specialization3',
        'Min3',
        'Max3',
        'Specialization4',
        'Min4',
        'Max4',
        'Specialization5',
        'Min5',
        'Max5',
        'Specialization6',
        'Min6',
        'Max6',
        'Specialization7',
        'Min7',
        'Max7',
        'Specialization8',
        'Min8',
        'Max8',
        'QueueFlag',
        'created_at',
        'updated_at',
        'ABAReqSentTime',
        'CampReqSent',
        'LeadAccptSent',
        'ABA',
        'HasIDXSITE',
        'ImportedBy',
        'BoardIdentifier',
        'IDXOfficeName',
        'LeadAccptReply',
        'State',
        'City',
        'ABAStatus',
        'ABAReplyRec',
        'ActionLabel',
        'Source',
        'AccessToken',
        'RefreshToken',
        'GAuthIp',
        'GtokenAddedAt',
        'GtokenUpdatedAt',
        'ResetLink',
        'ResetCode',
        'ResetLinkTime',
        'EscrowPhone',
        'EscrowEmail',
        'EscrowFax',
        'EscrowAgent',
        'EscrowAddress',
        'County',
        'AgentState',
        'DarkMode',
        'CompactMode',
        'LeadListMode',
        'PushToken',
        'EnableDesktopNotification',
        'DesktopPushToken',
        'EnableNotification',
        'EnableSmsAlert'
    ];
    protected $hidden = ['password', 'remember_token'];

    function get_userinfo($request){
        $data=LeadAgentModel::where('ListAgentEmail', $request->email)
            ->where('password', md5($request->password))
            ->first();
        return $data;
    }

    public function getLeadAgentData($previous_agent_cond,$inhouse_cond) {
        return DB::select(DB::raw("SELECT * from LeadAgentProfile where  (AgentType = 'In-House' OR AgentType = 'In-House/Zip'  ) and AgentActive='Yes' $previous_agent_cond and ( $inhouse_cond )"));
     }

    public function getDataByListingId($curr_mls_id, $record)
    {
        return LeadAgentModel::where("mls_no", $curr_mls_id)
            ->where("ListAgentMlsId", $record)
            ->get();
    }
    public function getDataByListAgentFullName($curr_mls_id, $record)
    {
        return LeadAgentModel::where("mls_no", $curr_mls_id)
            ->where("ListAgentFullName", $record)
            ->get();
    }
}
