<?php

namespace App\Models\Sqlmodel\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignModel extends Model
{
    use HasFactory;
    protected $table = "Campaign";
    protected $fillable = [
    'campaign_name','mls_no','office_ids','agent_ids','lead_ids','board_ids','agent_type','agent_table','start_date','start_time','finish_time','send_interval','limit','template','subject','content','run_lock','last_run_time','last_run_date','status','sent_agent_ids','sent_office_ids','sent_lead_ids','camp_created_time','camp_start_time','camp_finished_time'
    ];
}
