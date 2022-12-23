<?php

namespace App\Http\Controllers\campaign;

use App\Http\Controllers\Controller;
use App\Models\SqlModel\AlertsLog;
use Illuminate\Http\Request;
use App\Models\SqlModel\Campaign\TemplatesModel;

use App\Models\SqlModel\Campaign\CampaignModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SqlModel\lead\LeadsModel;
class CampaignController extends Controller
{
    //Campaign
    public function campaign() {
        $data["pageTitle"] = "Email Campaign";
        $data['campaign']= CampaignModel::where('agent_table','BrockerAgents')->get();
        if($data['campaign']){
            foreach ($data['campaign'] as &$k){
                $agent=json_decode($k->agent_ids);

                $k->agents=[];
            }
        }

        // return $data;
        return view('campaign.campaign',$data);
    }
    // Create Campaign
    public function createCampaign($id=null) {
        $data['campaign']=CampaignModel::where('id',$id)->first();
        if($data['campaign'])
        {
            $data['boards']=[];//AssignmentModel::where('mls_no',$data['campaign']['mls_no'])->groupBy('ListAOR')->get('ListAOR');
            $data['type']=[];//AssignmentModel::where('mls_no',$data['campaign']['mls_no'])->whereIn('ListAOR', $data['boards'])->groupBy('AgentLeadType')->get('AgentLeadType');
            $data['office']=[];//AssignmentModel::selectRaw('count(*) as total,ListOfficeMlsId,ListOfficeName')->where('mls_no',$data['campaign']['mls_no'])->where('AgentLeadType',$data['campaign']['agent_type'])->whereIn('ListAOR', $data['boards'])->groupBy('ListOfficeMlsId')->orderBy('total','DESC')->get();
            $data['agent']=[];//AssignmentModel::selectRaw('ListAgentFullName,ListAgentMlsId,ListOfficeMlsId')->where('mls_no',$data['campaign']['mls_no'])->where('AgentLeadType',$data['campaign']['agent_type'])->whereIn('ListAOR', $data['boards'])->whereIn('ListOfficeMlsId',json_decode($data['campaign']['office_ids']))->groupBy('ListAgentMlsId')->get();

             // return $data['office'];
        }
        $data["pageTitle"] = "Create Email Campaign";

        $data['mls']=collect(config('mls_config.mls'))->all();
//        $data['mls']= array (array("id"=>1,"mls"=>"MFRMLS"),array("id"=>2,"mls"=>"SEF"),array("id"=>3,"mls"=>"RAGFL"),array("id"=>4,"mls"=>"RAPB"));
        $data['template'] = TemplatesModel::where('type','email')->get();
        return view('campaign.createCampaign',$data);
    }
    public function Leadcampaign()
    {
        $data["pageTitle"] = "Lead Campaign";
        $data['campaign']= CampaignModel::where('agent_table','Lead')->get();;
        if($data['campaign']){
            foreach ($data['campaign'] as &$k){
               // $agent=json_decode($k->agent_ids);
                $leads=json_decode($k->lead_ids);
                // AssignmentModel::whereIn('ListAgentMlsId')
                if(isset($agent) && !empty($agent)) {
                    $k->agents = LeadsModel::select("id", "ListAgentFullName", "ListAgentEmail")->whereIn('id', $agent)->get();
                }elseif(isset($leads) && !empty($leads)) {
                    $k->agents = LeadsModel::select("id", "ListAgentFullName", "ListAgentEmail")->whereIn('id', $leads)->get();
                }
                // return $k->agents;
            }
        }
        return view('campaign.lead.campaign',$data);
    }
    public function createLeadcampaigns($id=null){
        $data['campaign']=CampaignModel::where('id',$id)->first();
        if($data['campaign'])
        {
            $data['boards']=[];//AssignmentModel::where('mls_no',$data['campaign']['mls_no'])->groupBy('ListAOR')->get('ListAOR');
//            $data['type']=[];AssignmentModel::where('mls_no',$data['campaign']['mls_no'])->whereIn('ListAOR', $data['boards'])->groupBy('AgentLeadType')->get('AgentLeadType');
            $data['office']=LeadsModel::selectRaw('count(*) as total,PropType, LeadType')->where('LeadType',$data['campaign']['agent_type'])->whereNotNull('ListAgentFullName')->where('ListAgentFullName','!=',"")->groupBy('PropType')->orderBy('total','DESC')->get();
//            $data['office']=AssignmentModel::selectRaw('count(*) as total,ListOfficeMlsId,ListOfficeName')->where('mls_no',$data['campaign']['mls_no'])->where('AgentLeadType',$data['campaign']['agent_type'])->whereIn('ListAOR', $data['boards'])->groupBy('ListOfficeMlsId')->orderBy('total','DESC')->get();
            $propType=json_decode($data['campaign']['office_ids']);
//            return $propType;
            $data['agent']=LeadsModel::selectRaw('ListAgentFullName, id')->whereNotNull('ListAgentFullName')->where('ListAgentFullName','!=',"")->where('LeadType',$data['campaign']['agent_type'])->groupBy('id')->get();
            // return $data['office'];
        }
        $data["pageTitle"] = "Create Email Campaign";
        $data['type']=LeadsModel::groupBy('LeadType')->whereNotNull('LeadType')->where('LeadType','!=',"")->get('LeadType');
//    return $data['type'];
        $data['mls']=collect(config('mls_config.mls'))->all();
//        $data['mls']= array (array("id"=>1,"mls"=>"MFRMLS"),array("id"=>2,"mls"=>"SEF"),array("id"=>3,"mls"=>"RAGFL"),array("id"=>4,"mls"=>"RAPB"));
        $data['template'] = TemplatesModel::where('type','email')->get();
        return view('campaign.lead.createCampaign',$data);
    }
    //
    public function template() {
        $data["pageTitle"] = "Email Templates";
        $data['templates']= TemplatesModel::all();
        return view('campaign.templates',$data);
    }
    // Add/Edit template page
    public function createTemplate($id=null) {
        $data["pageTitle"] = "Create Email Template";
        if($id){
            $data['template']=TemplatesModel::where('id',$id)->first();
        }
        // return $data;
        return view('campaign.createTemplate',$data);
    }
    // Add template
    public function addTemplate(Request $request){
        $form_data= $request->all();
        $id=0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }

         $unit_id = TemplatesModel::updateOrCreate(['id' => $id], $form_data);
        if($unit_id){
            if($id==0){
                $message='Template added successfully !';
            }else{
                $message='Template updated successfully !';
            }
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
    // add or edit campaign
    public function addCampaign(Request $request){
        $form_data= $request->all();

        $id=0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        if(isset($form_data['office_ids']) && !empty($form_data['office_ids'])) {
            $form_data['office_ids'] = json_encode($form_data['office_ids']);
        }
        if(isset($form_data['board_ids']) && !empty($form_data['board_ids']))
        {
            $form_data['board_ids']=json_encode($form_data['board_ids']);
        }
        if(isset($form_data['agent_ids']) && !empty($form_data['agent_ids'])) {
            $form_data['agent_ids'] = json_encode($form_data['agent_ids']);
        }
        if(isset($form_data['lead_ids']) && !empty($form_data['lead_ids'])) {
            $form_data['lead_ids'] = json_encode($form_data['lead_ids']);
        }
//        $form_data['agent_ids']=json_encode($form_data['agent_ids']);
        // $form_data['office_ids']=json_encode($form_data['office_ids']);
         $unit_id = CampaignModel::updateOrCreate(['id' => $id], $form_data);
        if($unit_id){
            if($id==0){
                $message='Campaign added successfully !';
            }else{
                $message='Campaign updated successfully !';
            }
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
                'id' =>$unit_id,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
    public function getTemplate(Request $request){
        $form_data=$request->all();
        $id=$form_data['id'];
        $data=TemplatesModel::where('id',$id)->first();
        return $data;
    }
    // Get board data
    public function getBoard(Request $request){
        $form_data = $request->all();
        $data['board']=0;//AssignmentModel::selectRaw('count(*) as total,id, ListAOR')->where('mls_no',$form_data['msl_no'])->groupBy('ListAOR')->get();
        return $data;
    }
    // Get Agent type
    public function getAgentType(Request $request)
    {
        $form_data = $request->all();
        // return $form_data;
        $boardid=0;
        if(isset($form_data['boards'])){
            $boardid=$form_data['boards'];
            $data['board']=0;//AssignmentModel::selectRaw('count(*) as total,id, AgentLeadType')->where('mls_no',$form_data['msl_no'])->whereIn('ListAOR', $boardid)->groupBy('AgentLeadType')->get();
        }else{
            $data['board']='';
        }

        return $data;
    }
//    Get getleadDataCamp
    public function getleadDataCamp(Request $request)
    {
        $form_data = $request->all();
        if(isset($form_data['agenttype'])){
            $data['board']=LeadsModel::selectRaw('count(*) as total,PropType, LeadType')->where('LeadType',$form_data['agenttype'])->groupBy('PropType')->orderBy('total','DESC')->get();
        }else{
            $data['board']='';
        }
        return $data;
    }
    // Get Agent office
    public function getAgentoffice(Request $request)
    {
        $form_data = $request->all();
        if(isset($form_data['agenttype'])){
            $data['board']=0;//AssignmentModel::selectRaw('count(*) as total,ListOfficeMlsId, ListOfficeName,ListAgentMlsId')->where('mls_no',$form_data['msl_no'])->where('AgentLeadType',$form_data['agenttype'])->whereIn('ListAOR', $form_data['boards'])   ->groupBy('ListOfficeMlsId')->orderBy('total','DESC')->get();
        }else{
            $data['board']='';
        }
        return $data;
    }
    //    getLeads
    public function getLeads(Request $request)
    {
        $form_data = $request->all();
        if(isset($form_data['agenttype'])){
            $data['board']=LeadsModel::selectRaw('count(*) as total,PropType, LeadType,ListAgentFullName, id')->whereNotNull('ListAgentFullName')->where('ListAgentFullName','!=',"")->where('LeadType',$form_data['agenttype'])->groupBy('id')->orderBy('total','DESC')->get();
        }else{
            $data['board']='';
        }
//        if(isset($form_data['office'])){
//            $data['board']=LeadsModel::selectRaw('count(*) as total,ListAgentFullName, id')->whereNotNull('ListAgentFullName')->where('ListAgentFullName','!=',"")->where('LeadType',$form_data['agenttype'])->whereIn('PropType',$form_data['office'])->groupBy('id')->get();
//        }else{
//            $data['board']='';
//        }
        return $data;
    }
    // Get Agent data
    public function getAgent(Request $request)
    {
        $form_data = $request->all();

        if(isset($form_data['office'])){
            $data['board']=0;//AssignmentModel::selectRaw('count(*) as total,ListAgentMlsId, ListOfficeMlsId,ListAgentFullName')->where('mls_no',$form_data['msl_no'])->where('AgentLeadType',$form_data['agenttype'])->whereIn('ListOfficeMlsId',$form_data['office'])->whereIn('ListAOR', $form_data['boards'])->groupBy('ListAgentMlsId')->get();
        }else{
            $data['board']='';
        }
        return $data;
    }
    public function run_campaign(){
            $curr_hour = (INT)date('H');
            $curr_time = date('H:i:s');
            $curr_date = date('Y-m-d');
            $cron_start_time = time();
            // return $curr_time;
            // exit ;
            $camp_table_name = 'campaign';

            // echo "<pre>";
            // $query  = "SELECT * from $camp_table_name where completed = 0 and run_lock = 0 and killed=0 and paused=0 and start_time <= $curr_hour and finish_time > $curr_hour and start_date <= $curr_date";
            $query = CampaignModel::where("completed", "=", 0)->where("run_lock", "=", 0)->where("killed", "=", 0)->where("paused", "=", 0)->where("start_time", "<=", $curr_hour)->where("finish_time", ">", $curr_hour)->where("start_date", "<=", $curr_date)->get();
            // return $query;
            $replaces = config("mls_config.replaces");
            $strip_parse = implode('\b|\b',$replaces);
            $strip_parse = str_ireplace("/", "\/",$strip_parse );
            $strip_parse = '\b'.$strip_parse .'\b' ;

            //echo $query  = "SELECT * from $camp_table_name where id=51";
            // $result = $this->db->query($query);
            if(!empty($query->all())){
                $query=collect($query)->first();
                $camp_row  = collect($query)->all();
                  // return $camp_row;
                $camp_id   = $camp_row['id'];
                $camp_name = $camp_row['campaign_name'];
                $interval  = $camp_row['send_interval'];
                $email_subject = $camp_row['subject'];
                $email_content_temp = $camp_row['content'];
                $start_time = $camp_row['start_time'];
                $template_id = $camp_row['template'];
                $finish_time = $camp_row['finish_time'];
                $campaign_start_time = $camp_row['camp_start_time'];
                if( isset($camp_start_time) && $camp_start_time!='' && $camp_start_time!='0000-00-00 00:00:00'){
                    // Means crons was already started
                } else {
                    // Set cron start time
                    // $this->db->where('id' , $camp_id);
                    // $res1 = $this->db->update($camp_table_name , array('campaign_start_time'=>$curr_time ) );
                    // $res1 = CampaignModel::update(['id' => $camp_id], array('camp_start_time'=>$curr_time ));
                    $res1 =CampaignModel::where("id",$camp_id)->update(['camp_start_time'=>$curr_time]);
                }

                // Set Lock for this campaign
                // $this->db->where('id' , $camp_id);
                // $res1 = $this->db->update($camp_table_name , array('run_lock'=>1 ) );
                $res1 = CampaignModel::where('id',$camp_id)->update(['run_lock'=>1 ]);
                $agents_lists = $camp_row['agent_ids'];
                $agents=json_decode($agents_lists);
                // $agents = explode(',',$agents_lists);
                // return $agents_lists;
                // Check how many agents email already sent
                // $agent_check_query = "SELECT agent_id FROM  campaign_emails_logs where campaign_id  = $camp_id " ;
                $agent_check_query=AlertsLog::where('camp_id',$camp_id)->get('agent_id');

                // $result_check = $this->db->query( $agent_check_query );
                if( $agent_check_query){
                    // $check_array = $result_check->result_array();
                     $curr_agent_list = array();
                    foreach($agent_check_query as $check_agent){
                        $curr_agent_list[] = $check_agent['agent_id'] ;
                    }
                    $agents = array_diff($agents, $curr_agent_list);
                    $total_agents_count = count($agents);//."Counts";
                    if($total_agents_count==0){
                        // $this->db->where('id' , $camp_id);
                        // $res1 = $this->db->update($camp_table_name , array('completed'=>1,'campaign_finished_time'=>date('Y-m-d H:i:s') ) );
                        $res1= CampaignModel::where('id' , $camp_id)->update(['completed'=>1,'camp_finished_time'=>date('Y-m-d H:i:s')]);
                    }
                }

                //print_r($agents);
                //exit;

                if(!empty($agents) && count($agents)>0){
                    foreach($agents as $agent){
                        // Check current hour everytime
                        $curr_hour_agent = (INT)date('H');
                        $agent_id = $agent ;

                        // $querykl  = "SELECT * from $camp_table_name where id = $camp_id and ( paused = 1  or killed = 1 ) ";
                        // $resultkl = $this->db->query($querykl);

                        $querykl = CampaignModel::where('id',$camp_id)->Where("paused", "=", 1)->orWhere("killed", "=", 1)->get();
                        // return $querykl;
                        if(collect($querykl)->count() > 0){
                            echo  "Campaign is killed or paused ";
                            Log::error('Campaign is killed or paused '.$camp_id.'   '.date('Y-m-d H:i:s') );
                            break;
                        }

                        if( $curr_hour_agent > $finish_time || $curr_hour_agent < $start_time ){
                            echo  "Time not matched Now,, Remaining Part will be covered next day";
                            Log::error('Time not matched Now,, Remaining Part will be covered next day '.$camp_id.'   '.date('Y-m-d H:i:s') );
                            break;
                        }
                        $curr_instant_time = time();
                        if(  (int)($curr_instant_time-$cron_start_time) >1800 ){
                            echo "Cron is running more then 30 Minutes , Remaining Part will be covered in next cron";
                            Log::error('Cron is running more then 30 Minutes , Remaining Part will be covered in next cron '.$camp_id.'   '.date('Y-m-d H:i:s') );
                            break;
                        }
                        // if($template_id==20 || $template_id==23 || $template_id==44 || $template_id==27 || $template_id==32 || $template_id==33 || $template_id==43 ){
                            // $query_agent = "SELECT ListAgentEmail,ListAgentFullName,ListAgentDirectPhone from BrokerAgents where ListAgentMlsId = '$agent_id' ";
                        $query_agent=0;//AssignmentModel::where('ListAgentMlsId',$agent_id)->first();
                        // return $query_agent;
                        // } else if( $template_id==22 || $template_id==25 ){
                        //     $query_agent = "SELECT ListAgentEmail,ListAgentFullName,ListAgentDirectPhone from LeadAgentProfile where ListAgentMlsId = '$agent_id' ";
                        // } else {
                        //     $query_agent = "SELECT ListAgentEmail,ListAgentFullName,ListAgentDirectPhone from LeadAgentProfile where ListAgentMlsId = '$agent_id' ";
                        // }
                        //echo "<br>new ".$query_agent."<br>";
                        // $agent_result  = $this->db->query( $query_agent );
                        if(collect($query_agent)->count()){
                            $agent_row    = collect($query_agent)->all();;
                            $agent_email  = $agent_row['ListAgentEmail'];
                            $agent_name   = $agent_row['ListAgentFullName'];
                            $AgentPhone   = $agent_row['ListAgentDirectPhone'];
                            $office_name   = $agent_row['ListOfficeName'];
                            $OfficeStreet   = $agent_row['ListAgentDirectPhone'];
                            $OfficeCity   = $agent_row['City'];
                            $OfficeState   = $agent_row['ListAgentDirectPhone'];
                            $OfficeZip   = $agent_row['PostalCode'];
                            $SiteName   = $agent_row['ListAgentDirectPhone'];
                            $SiteUrl   = $agent_row['ListAgentDirectPhone'];
                            //continue ;
                            $email_content = $email_content_temp ;
                            $email_content   = str_replace('{Agent}',$agent_name,$email_content);
                            $email_content   = str_replace('{AgentEmail}',$agent_email,$email_content);
                            $email_content   = str_replace('{AgentPhone}',$AgentPhone,$email_content);
                            $email_content   = str_replace('{OfficeName}',$office_name,$email_content);
                            $email_content   = str_replace('{OfficeStreet}',$OfficeStreet,$email_content);
                            $email_content   = str_replace('{OfficeCity}',$OfficeCity,$email_content);
                            $email_content   = str_replace('{OfficeState}',$OfficeState,$email_content);
                            $email_content   = str_replace('{OfficeZip}',$OfficeZip,$email_content);
                            $email_content   = str_replace('{SiteName}',$SiteName,$email_content);
                            $email_content   = str_replace('{SiteUrl}',$SiteUrl,$email_content);
                            // return $email_content;
                            //$email_content   = str_replace('admin/enableaba','admin/enableaba/'.$agent_id ,$email_content);
                            // $email_content   = str_replace('admin/disableaba','admin/disableaba/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/enableaba_in-house','admin/enableaba_in_house/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/enableaba_co-broke','admin/enableaba_co_broke/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/agentactive_No','admin/agentactive_no/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/agentactive_Yes','admin/agentactive_yes/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/carrier/','admin/carrier/'.$agent_id.'/' ,$email_content);
                            // $email_content   = str_replace('admin/enableaba_inhousezip','admin/enableaba_in_house_zip/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/enableleadsacceptence','admin/enableleadsacceptence/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/disableleadsacceptence','admin/disableleadsacceptence/'.$agent_id.'/'.$template_id ,$email_content);
                            // $email_content   = str_replace('admin/toggleaba','admin/enableaba/'.$agent_id.'/'.$template_id ,$email_content);
                            //$config['charset'] = 'utf-8';
                            //$config['wordwrap'] = TRUE;
                            //$config['mailtype'] = 'html';
                            //echo "<br>".$agent_email ;
                            //echo $email_content ;
                            //exit;


                            // $config = Array(
                            //     'smtp_timeout'=>'30',
                            //     'protocol' => 'smtp',
                            //     'smtp_host' => 'ssl://smtp.googlemail.com',//'ssl://smtp.gmail.com',
                            //     'smtp_port' => 465,
                            //     'smtp_user' => $this->sentemail ,// 'brokerlinxshowings@gmail.com',
                            //     'smtp_pass' => $this->sentemail_password ,//'mrabroker305$',
                            //     'mailtype' => 'html',
                            //     'wordwrap' => TRUE,
                            //    // 'smtp_crypto' => 'ssl'
                            // );
                            // //$this->load->library('email', $config);

                            // $this->load->library('email',$config);
                            // $this->email->set_newline("\r\n");
                            // $this->email->from($this->sentemail , 'BrokerLinx');
                            // $this->email->to( $agent_email ); //$agent_email
                            // $this->email->bcc( $this->superadminemail );
                            // //$this->email->cc( ALERT_CC_EMAIL_ID );
                            // $this->email->subject( $email_subject );
                            // $this->email->message( $email_content );
                            // //$this->email->send();
                            // if (!$this->email->send()){
                            //     //show_error($this->email->print_debugger());
                            // } else {
                            //     //echo 'Your e-mail has been sent!';
                            // }


                            $curr_sent_time = date('Y-m-d H:i:s');
                            // Set Agent Camp Req Sent = 1;
                            // if($template_id==20 || $template_id==23 || $template_id==44 || $template_id==27 || $template_id==32 || $template_id==33 || $template_id==43){
                                //$update_query = "UPDATE BrokerAgents  set  ABAReqSent = 'Yes', ABAReqSentTime='".$curr_sent_time."' where ListAgentMlsId = '$agent_id' ";
                                $agent_name = preg_replace("/\b".$strip_parse."\b/",'',$agent_name);
                                $agent_name = trim($agent_name);
                                // if($template_id==43){
                                //   $update_query = 'UPDATE BrokerAgents  set  ABA2ndAtp = "Yes", 2nd_date="'.$curr_sent_time.'" where ListAgentEmail="'.$agent_email.'" AND ListAgentFullName like "%'.$agent_name.'%" ' ;

                                // } else {
                                    // $update_query = 'UPDATE BrokerAgents  set  ABAReqSent = "Yes", ABAReqSentTime="'.$curr_sent_time.'" where ListAgentEmail="'.$agent_email.'" AND ListAgentFullName like "%'.$agent_name.'%"';
                                // }
                            // } else {
                            //    $update_query = "UPDATE LeadAgentProfile set  ABAReqSent = 'Yes' , ABAReqSentTime='".$curr_sent_time."' where ListAgentMlsId = '$agent_id' ";
                            // }
                            // exit();
                            //echo $update_query ;
                            // $res_up = $this->db->query($update_query);

                            $camp_cron_data = array();
                            $camp_cron_data['agent_id']      = $agent_id ;
                            $camp_cron_data['camp_id']   = $camp_id ;
                            $camp_cron_data['camp_name'] = $camp_name ;
                            $camp_cron_data['subject'] = $email_subject ;
                            $camp_cron_data['emailContent'] = $email_content ;
                            $camp_cron_data['toEmail']      = $agent_email ;
                            $camp_cron_data['template_id']   = $template_id ;
                            $camp_cron_data['sentAt']     = date('Y-m-d H:i:s') ;

                            $res= AlertsLog::Create($camp_cron_data);
                            // if($template_id==43){
                            //     $camp_cron_data['2nd_attempt']   = 1 ;
                            // }
                            // $this->db->insert('campaign_emails_logs',$camp_cron_data);
                        }
                        // exit();
                        // $this->db->where('id' , $camp_id);
                        $res1 = CampaignModel::where('id' , $camp_id)->update(['last_run_time'=>date('Y-m-d H:i:s'),'last_run_date'=>date('Y-m-d')]);
                        // If need to send email then make sleep after email
                        sleep($interval);
                    }
                }

                // Unlock the cron
                // $this->db->where('id' , $camp_id);
                $res1 = CampaignModel::where('id' , $camp_id)->update(['run_lock'=>0 ]);
                // $res1 = $this->db->update($camp_table_name , array('run_lock'=>0 ) );

                // Set finished if sent to all agents


                echo "Got Camp";
            } else {
                echo "No Camp";
            }
        }
    public function DeleteTemplate(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $id = $data['id'];
            $res = TemplatesModel::where('id', $id)->delete();
            CampaignModel::where('template', $id)->delete();
            if ($res) {
                echo "true";
            } else {
                echo "false";
            }
        }
    }
    public function DeleteLead(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $id = $data['id'];
//            $this->authorize('delete',ProjectModel::class);
            $res= CampaignModel::where("id",$id)->delete();
            // return redirect('project');
            if ($res) {
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'message' => 'Campaign Deleted !',
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'data' => $data,
                    'message' => 'Something Wents Wrong !',
                ]);
            }
        }


    }
}
