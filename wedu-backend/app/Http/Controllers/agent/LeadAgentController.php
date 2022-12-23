<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SqlModel\agent\LeadAgentModel;
use App\Models\SqlModel\PostalMasterModel;
use App\Models\SqlModel\Notifications;
use App\Models\SqlModel\PhoneCarrier;
use App\Models\SqlModel\lead\LeadsModel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class LeadAgentController extends Controller
{

     public $LeadAgentModel;
    // public $AssignmentModel;
        public function __construct() {
            $db = env('RUNNING_DB_INFO');
            if ($db == "sql"){
                $this->LeadAgentModel = new LeadAgentModel();
                $this->LeadsModel = new LeadsModel();
            }else{
//                $this->LeadsModel = new \App\Models\MongoModel\LeadsModel();
//                $this->LeadAgentModel = new \App\Models\MongoModel\LeadAgentModel();
            }
        }
    //
    public function index(){
        // $data=[];
        $data["pageTitle"] = "Agents List";
        // $data['agents'] = LeadAgentModel::select('AgentType', LeadAgentModel::raw('count(*) as total'))
        // ->groupBy('AgentType')
        // ->get();
        $data['agents']= LeadAgentModel::selectRaw('count(*) as total, AgentType')->groupBy('AgentType')->get();
        $data['total']= $this->LeadAgentModel::groupBy('AgentType')->count();
        // $data['total'] =$data['agents']->count();
        // print_r($data);
        // exit;
        return view('agent.leadAgent.index',$data);
    }
    // Property data filter
    public function getData(Request $request) {
        $getdata = $request->all();
        $data['request']=$getdata;
        $type='';

        $query=$this->LeadAgentModel::where('id','!=','');
        if(isset($getdata['search'])){
            $search=$getdata['search'];
            $query=$query->where(function($q) use ($search) {
                $q->where('ListAgentFullName', 'like', '%' . $search . '%')
                ->orWhere('ListAgentDirectPhone', 'like', '%' . $search . '%')
                ->orWhere('ListAgentEmail', 'like', '%' . $search . '%');
            });
        }
        if(isset($getdata['type'])){
            $type=$getdata['type'];
            // $query=$query->whereIn('AgentType',$type);
        }
        $data['property']=$query->orderBy('id','ASC')->get(['AgentType','ListAgentFullName','ListAgentDirectPhone','ListAgentEmail','id','AgentActive']);
        return $data;
    }
    public function getAgent($id=null){
        $data["pageTitle"] = "Agents Details";
        $data['all_spc'] = config("mls_config.all_spc");
        $data['agent']= LeadAgentModel::where('id',$id)->first();
        //$data['state']= PostalMasterModel::distinct('state')->get(['state']);
        // return $data;
        return view('agent.leadAgent.profile',$data);
    }
    public function getCounty(Request $request){
        $getdata = $request->all();
        $data['request']=$getdata;
        $query="";
        if(isset($getdata['state'])){
            $state=$getdata['state'];
            $query=$query->where('state',$state);
        }
        $data['county']=$query->distinct('county')->get(['county']);
        return $data;
    }
   /* public function getCity(Request $request){
        $getdata = $request->all();
        $data['request']=$getdata;
        $query=PostalMasterModel::where('id','!=','');
        if(isset($getdata['county'])){
            $county=$getdata['county'];
            $query=$query->where('county',$county);
        }
        $data['city']=$query->distinct('city')->get(['city']);
        return $data;
    }*/
    //
    /*public function getZip(Request $request){
        $getdata = $request->all();
        $data['request']=$getdata;
        $query=PostalMasterModel::where('id','!=','');
        if(isset($getdata['state'])){
            $state=$getdata['state'];
            $query=$query->where('state',$state);
        }
        if(isset($getdata['county'])){
            $county=$getdata['county'];
            $query=$query->where('county',$county);
        }
        if(isset($getdata['city'])){
            $city=$getdata['city'];
            $query=$query->where('city',$city);
        }
        $data['zip']=$query->distinct('zipcode')->get(['zipcode']);
        return $data;
    }*/
    public function getlapGraphData(Request $request){
        $type=$request['type'];
        $days=$request['days'];
        // return $request;
        $data['mls']=collect(config('mls_config.mls'))->all();
        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        $date = \Carbon\Carbon::today()->subDays($days);

        $data['chart_day'] = LeadAgentModel::selectRaw('count(id) as total, Date('.$type.') as udate,mls_no')->where($type,'>=',$date)->groupBy('mls_no')->groupBy('udate')->orderBy('udate','ASC')->get();
        // $data['chart_day'] = $this->LeadAgentModel::selectRaw('count(id) as total, Date('.$type.') as udate,mls_no')->where($type,'>=',$date)->groupBy('mls_no')->groupBy('udate')->orderBy('udate','ASC')->get();
        $final=[];
        $date=[];
        foreach($data['chart_day'] as $val)
        {
            $dir=array('day'=>'', 'mls1'=>0, 'mls2'=>0 , 'mls3'=>0,'mls4'=>0);
            if(in_array($val->udate,$date)){
                $arr=array_search($val->udate,$date);
                if($val->mls_no==1){
                    $dir['day']=$val->udate;
                    $dir['mls1']=$val->total;
                    $final[$arr]['mls1']=$val->total;
                }
                if($val->mls_no==2){
                    $dir['day']=$val->udate;
                   $dir['mls2']=$val->total;
                    $final[$arr]['mls2']=$val->total;
                }
                if($val->mls_no==3){
                    $dir['day']=$val->udate;
                    $dir['mls3']=$val->total;
                    $final[$arr]['mls3']=$val->total;
                }
                if($val->mls_no==4){
                    $dir['day']=$val->udate;
                    $dir['mls4']=$val->total;
                    $final[$arr]['mls4']=$val->total;
                }
            }else{
            if($val->mls_no==1){
                $dir['day']=$val->udate;
                $dir['mls1']=$val->total;
            }
            if($val->mls_no==2){
                $dir['day']=$val->udate;
               $dir['mls2']=$val->total;
            }
            if($val->mls_no==3){
                $dir['day']=$val->udate;
                $dir['mls3']=$val->total;
            }
            if($val->mls_no==4){
                $dir['day']=$val->udate;
                $dir['mls4']=$val->total;
            }
            $date[]=$val->udate;
            $final[]=$dir;
        }
        }
        $data['final']=json_encode($final);
        $mlsName=[];
        foreach($data['mls'] as $mls){
             $mlsName[]=$mls['mls'];
        }
        $data['mls']=$mlsName;
        return $data;
    }
    public function getagentinfo(Request $request){
        $ApiKeyData = env('ApiKey');
        $AgentId=$request->AgentId;
        if( isset($request->token) && !empty($request->token) ){
            $token= json_decode(base64_decode($request->token));
            // return $user;
            if($token->login_user_id){

            $apikey    = isset($request->apikey)?$request->apikey:'';
            if( isset($apikey) && !empty($apikey) && $apikey==$ApiKeyData){
                    $agent_id = isset($request->AgentId)?$request->AgentId:'';
                    // return $user->login_user_id;
                    $logintype  = (isset($token->logintype) && $token->logintype!='' )?$token->logintype:'agent';
                    if($logintype=='admin'){
                        //ListAgentFullName,ListAgentDirectPhone,ListAgentEmail,ListOfficeName,ZipCodes,AgentType,
                        $agentinfo=LeadAgentModel::where('id',''.$AgentId.'')->first(['ListAgentFullName','AgentHeadshotUrl','Min0','Min1','Min2','Min3','Min4','Min5','Min6','Min7','Max0','Max1','Max2','Max3','Max4','Max5','Max6','Max7','FormattedPhone','ListAgentEmail','AgentType','ListAOR','ListOfficeName','ListOfficePhone','AgentOfficeMlsId','ZipCodes']);

                        if( isset($agentinfo['AgentHeadshotUrl']) && $agentinfo['AgentHeadshotUrl']!='' ){
                            $agentinfo['agent_image'] = '<center><img height="150" style="max-width:80%;" src="https://dev.brokerlinx.com/uploads/'.$agentinfo['agent_headshot_url'].'"  alt=""  /></center>';
                              $agentinfo['agent_image_url'] =  "https://dev.brokerlinx.com/uploads/".$agentinfo['agent_headshot_url'];
                        } else {
                            $agentinfo['agent_image'] = '<center><img height="150" style="max-width:80%;" src="https://brokerlinx.com/crm/uploads/default_profile.png" class="imgmarksign"  alt="" markno="noimage"  /></center>';
                              $agentinfo['agent_image_url'] =  "https://brokerlinx.com/crm/uploads/default_profile.png";

                        }
                        $data = array('success'=>true,'agentinfo'=>$agentinfo,'AgentId'=>$AgentId);
                    } else {
                        $data = array('success'=>false,'msg'=>'Only Admin is allowed.');
                    }
                } else {
                    $data = array('success'=>false,'msg'=>'Please pass correct api key.');
                }
            }else{
                $data = array('success'=>false,'msg'=>'Please pass correct api key.');
            }
            }

            return $data;
        }

    // Agent Signup Api
   /* public function AgentSignup(Request $request){
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey)?$request->apikey:'';
        if( isset($apikey) && !empty($apikey) && $apikey==$ApiKeyData){
            try {
                // $userIp = $this->input->ip_address();
                $license_no          = $request->license_no;
                $office_name         = $request->office_name;
                $agent_name          = $request->agent_name;
                $phone               = $request->phone;
                $email               = $request->email;
                $mls_no              = $request->mls_no;
                $MlsName             = $request->MlsName;
                $password            = $request->password;
                $specializations     = $request->specializations;
                $zip_codes           = $request->zip_codes;
                $office_id           = $request->office_id;
                $board               = $request->board;
                $Carrier             = $request->Carrier;
                $OtherCarrier        = $request->OtherCarrier;
                $only_office_name    = $request->only_office_name;
                $ListOfficePhone     = $request->ListOfficePhone;
                $user_data = array();
                $user_data['Carrier']                   = $Carrier ;

                if(isset($Carrier) && $Carrier== '-1'){
                    // return $user_data;
                    $query=PhoneCarrier::where('name',$OtherCarrier)->first();
                    // $query = $this->db->get_where('PhoneCarrier', array('name' => $OtherCarrier) );
                    // $query = $this->db->get_where('PhoneCarrier', array('name' => $OtherCarrier) );
                    if ($query) {
                        $row = $query;
                        $user_data['CarrierId'] = $row->id ;
                    } else {
                        $new_carrier = array();
                        $new_carrier['Name'] = $OtherCarrier;
                        $new_carrier['created_at'] = date('Y-m-d H:i:s');
                        // $result     = $this->db->insert('PhoneCarrier',$new_carrier);
                         $carrier_id    = PhoneCarrier::Create($new_carrier);
                        $user_data['CarrierId'] = $carrier_id ;
                        if($carrier_id){
                            //$this->superadminnotification( 'New Phone Carrier Added','New Phone Carrier Added by Name - '.$OtherCarrier);
                        }
                    }
                } else {
                    $query=PhoneCarrier::where('SmsEmail',$Carrier)->first();
                    $query2=PhoneCarrier::where('Name',$Carrier)->first();
                    // $query = $this->db->get_where('PhoneCarrier', array('sms_email' => $Carrier) );
                    // $query2 = $this->db->get_where('PhoneCarrier', array('name' => $Carrier) );
                    // return $query;
                    if ($query) {
                        $user_data['CarrierId'] = $query->id ;
                    } else if($query2) {
                        $user_data['CarrierId'] = $query2->id ;
                        $user_data['Carrier'] = $query2->SmsEmail ;
                    }
                    // return $user_data;
                }
                if( isset($only_office_name) && $only_office_name!='' ){
                    $office_name = $only_office_name ;
                }

                $err = 0;
                $err_msg = '';
                if( !isset($office_name) || trim($office_name)=='' ){
                    $err = 1;
                    $err_msg .= " Office Name, ";
                }
                if( !isset($ListOfficePhone) || trim($ListOfficePhone)=='' ){
                    $err = 1;
                    $err_msg .= " Office Phone, ";
                }
                if( !isset($agent_name) || trim($agent_name)=='' ){
                    $err = 1;
                    $err_msg .= " Agent Name, ";
                }
                if( !isset($license_no) || trim($license_no)=='' ){
                    $err = 1;
                    $err_msg .= " License No, ";
                }
                if( !isset($email) || trim($email)=='' ){
                    $err = 1;
                    $err_msg .= " Email, ";
                }
                if( !isset($office_id) || trim($office_id)=='' ){
                    $err = 1;
                    $err_msg .= " Office Id, ";
                }
                if( !isset($mls_no) || trim($mls_no)=='' ){
                    $err = 1;
                    $err_msg .= " mls no, ";
                }
                if( !isset($Carrier) || trim($Carrier)=='' ){
                    $err = 1;
                    $err_msg .= " Carrier, ";
                }
                if( !isset($specializations) || trim($specializations)=='' ){
                    $err = 1;
                    $err_msg .= " Specializations, ";
                }

                if( !isset($zip_codes) || trim($zip_codes)=='' ){
                    $err = 1;
                    $err_msg .= " Zip Codes, ";
                } else {
                    $zip_codes = str_ireplace(' ','',$zip_codes);
                }

                if($err==1){
                    throw new Exception($err_msg.' are required');
                }

                $user_data['IDXOfficeName']            = $office_name ;
                $user_data['HasIDXSITE']               = 'No';
                $user_data['ListOfficePhone']           = $ListOfficePhone;
                $user_data['ListAgentFullName']         = $agent_name  ;
                $user_data['ListOfficeName']            = $office_name  ;
                $user_data['ListAgentMlsId']            = $license_no ;
                $user_data['ListAgentEmail']            = $email  ;
                $user_data['AgentOfficeMlsId']          = $office_id ;
                $user_data['mls_no']                    = $mls_no  ;
                $user_data['MlsName']                   = $MlsName ;
                #$user_data['Carrier']                   = $Carrier ;
                $user_data['BoardIdentifier']           = $board  ;
                $user_data['ListAOR']                   = $board ;
                $user_data['ListAgentDirectPhone']      = $phone  ;
                $user_data['SpecializationRequested']   = $specializations;
                $all_types = explode(',',$specializations);
                foreach($all_types as $p_type){
                    if($p_type=='RLSE'){
                        $user_data['Specialization1'] = 'RLSE';
                        $user_data['Min1'] = 1;
                        $user_data['Max1'] = 99999999;
                    } else if($p_type=='RESI'){
                        $user_data['Specialization0'] = 'RESI';
                        $user_data['Min0'] = 1;
                        $user_data['Max0'] = 99999999;
                    } else if($p_type=='RINC'){
                        $user_data['Specialization2'] = 'RINC';
                        $user_data['Min2'] = 1;
                        $user_data['Max2'] = 99999999;
                    } else if($p_type=='LAND'){
                        $user_data['Specialization3'] = 'LAND';
                        $user_data['Min3'] = 1;
                        $user_data['Max3'] = 99999999;
                    } else if($p_type=='BZOP'){
                        $user_data['Specialization4'] = 'BZOP';
                        $user_data['Min4'] = 1;
                        $user_data['Max4'] = 99999999;
                    } else if($p_type=='COMM'){
                        $user_data['Specialization5'] = 'COMM';
                        $user_data['Min5'] = 1;
                        $user_data['Max5'] = 99999999;
                    } else if($p_type=='COML'){
                        $user_data['Specialization6'] = 'COML';
                        $user_data['Min6'] = 1;
                        $user_data['Max6'] = 99999999;
                    } else if($p_type=='RESI_List'){
                        $user_data['Specialization7'] = 'RESI_List';
                        $user_data['Min7'] = 1;
                        $user_data['Max7'] = 99999999;
                    }
                }

                $zips = "";
                // $protected_zipcodes = $this->config->item('protected_zipcodes');
                $protected_zipcodes = config('mls_config.protected_zipcodes');
                if ( isset($zip_codes) && $zip_codes!='' ) {
                  $curr_zips = explode(',',$zip_codes) ;
                  $zip_diff = array_diff($curr_zips,$protected_zipcodes);
                  $zips = implode(',',$zip_diff);
                }
                $user_data['ZipRequested']              = $zips ;
                $user_data['created_at']                = date('Y-m-d H:i:s') ;
                $user_data['updated_at']                = date('Y-m-d H:i:s') ;
                $user_data['AgentType']                 = 'Zip' ;
                $IsKWAgent = 'No';
                if( strpos( strtolower($office_name),'keller') !==false  ){
                    $user_data['IsKWAgent']                 = 'Yes';
                    $IsKWAgent  = 'Yes';
                } else {
                    $user_data['IsKWAgent']                 = 'No';
                }

                $user_data['password']                  = md5($password) ;
                $insert = 0;
                if( isset($office_name) && isset($agent_name) && $agent_name!='' && $office_name !='' ){
                    // $agent_query = $this->db->get_where('LeadAgentProfile',array('ListOfficeName'=>$office_name,'ListAgentFullName'=>$agent_name )) ;
                    $agent_query =$this->LeadAgentModel::where('ListOfficeName',$office_name)->where('ListAgentFullName',$agent_name)->first();
                    if(!empty($agent_query)){
                        // return $agent_query;
                        $insert = 1 ;
                        $curr_rows = $agent_query;
                        $curr_id = $curr_rows->id;
                        if( isset($curr_rows->CustomPropertyType) && $curr_rows->CustomPropertyType!='' ){
                            $all_type = explode(",", $curr_rows->CustomPropertyType);
                            $all_type[] = $specializations ;
                        } else {
                        }
                        if( isset($curr_rows->MlsName) && $curr_rows->MlsName!='' ){
                            unset($user_data['MlsName']);
                        }
                        if( isset($curr_rows->HasIDX_SITE) && $curr_rows->HasIDX_SITE!='' ){
                            unset($user_data['HasIDX_SITE']);
                        }
                        if( isset($curr_rows->IsKWAgent) && $curr_rows->IsKWAgent!='' ){
                            unset($user_data['IsKWAgent']);
                        }
                        if( isset($curr_rows->AgentType) && $curr_rows->AgentType!='' ){
                            unset($user_data['AgentType']);
                        }
                        if( isset($curr_rows->IDX_officename) && $curr_rows->IDX_officename!='' ){
                            unset($user_data['IDX_officename']);
                        }
                        if( isset($curr_rows->BoardIdentifier) && $curr_rows->BoardIdentifier!='' ){
                            unset($user_data['BoardIdentifier']);
                        }
                        if( isset($curr_rows->ListAOR) && $curr_rows->ListAOR!='' ){
                            unset($user_data['ListAOR']);
                        }
                        // $opr = ' UPDATED';
                        // $prop_cond = array('id' => $curr_id );
                        // $user_id = $curr_id ;
                        #$this->db->where($prop_cond);
                        #$result = $this->db->update('LeadAgentProfile', $user_data);
                    }
                }
                #print_r($user_data);
                if($insert==0){
                    $opr = ' INSERTED';
                    $user_data['importedby'] = 'mobile_api';
                    $user_data['reqsent'] = 'No';
                    $user_id     = $this->LeadAgentModel::Create($user_data);
                    // $user_id    = $this->db->insert_id() ;
                }
                $message    = "<br><span> Agent Name : $agent_name </span>";
                $message    .= "<br><span> Agent Email : $email </span>";
                $message    .= "<br><span> Agent Office : $office_name </span>";
                $message    .= "<br><span> Agent Mlsid : $license_no </span>";
                $zips = ( isset($zip_codes) && $zip_codes!='' )?$zip_codes:'' ;
                $spcl = ( isset($specializations) && $specializations!='' )?$specializations:'' ;
                $message    .= "<br><span> Carrier : ".$user_data['Carrier'] ." </span>";
                $message    .= "<br><span> ZipCodes : ".$zips."</span>";
                $message    .= "<br><span> Specializations : ".$spcl."</span>";
                $message    .= "<br><span> IsKWAgent : ".$IsKWAgent."</span>";
                //$this->superadminnotification( 'New Agent Signup -'.$opr,$message);

                if($user_id){
                    $data = array('success'=>true,'msg'=>'Signup Successfull ,Please login.');
                } else {
                    $data = array('success'=>false,'msg'=>'Some error occurred ,Please try again later.' );
                }
            } catch (Exception $e) {
                $data = array('success'=>false,'msg'=>$e->getMessage() );
            }
        } else {
            $data = array('success'=>false,'msg'=>'Please pass correct api key.');
        }
        return $data;
    }*/
    // Agent Profile Setting
    public function ProfileSettings(Request $request){
        //$agentidget =  $this->input->get('agentid');
        $ApiKeyData = env('ApiKey');
        if( isset($request->token) && !empty($request->token) ){
            $token= json_decode(base64_decode($request->token));
            if($token->login_user_id){
                $apikey    = isset($request->apikey)?$request->apikey:'';
                if( isset($apikey) && !empty($apikey) && $apikey==$ApiKeyData){
                    $agent_id = $request->agentid;
                    $query=LeadAgentModel::where('id',$agent_id)->first(['id', 'ListAgentEmail', 'ListAgentDirectPhone','FormattedPhone','Carrier', 'Gsuite', 'AgentActive', 'ListOfficeName', 'ZipCodes', 'County', 'AgentState', 'EscrowAgent', 'EscrowAddress', 'EscrowPhone', 'EscrowEmail', 'EscrowFax','DarkMode','CompactMode','LeadlistMode','EnableNotification','PushToken']);
                    //add escrow_agent,escrow_address,escrow_phone,escrow_email,escrow_fax
                   // $this->db->where('id',$agent_id);
                   // $this->db->from('LeadAgentProfile');
                   // $query  = $this->db->get();
                   $res = $query;
                   // return $query;
                   $changestatus = '';
                   if($res->AgentActive=='Yes'){
                       $changestatus =['No','Pause','Resume'];
                   }
                   if($res->AgentActive=='Pause'){
                    $changestatus =['Yes','No','Resume'];

                   }
                   if($res->AgentActive=='Resume'){
                       $changestatus .=['Yes','No','Pause'];

                   }
                   //$data['agntdirectphone'] =   $res['ListAgentDirectPhone'];
                   $data['agntdirectphone'] =   $res->FormattedPhone;
                   $data['agntemail'] =   $res->ListAgentEmail;
                   $data['agntcarrier'] =   $res->Carrier;
                   $data['agntgsuit'] =   $res->Gsuite;
                   $data['agentstatus'] =   $res->AgentActive;
                   $data['agntofficename'] = $res->ListOfficeName;
                   $data['assignedzipcode'] = $res->ZipCodes;
                   $data['state'] = $res->AgentState;
                   $data['county'] = $res->County;
                   $data['escrowagnt'] = $res->EscrowAgent;
                   $data['escrowaddress'] = $res->EscrowAddress;
                   $data['escrowphone'] = $res->EscrowPhone;
                   $data['escrowemail'] = $res->EscrowEmail;
                   $data['escrowfax'] = $res->EscrowFax;
                   $data['DarkMode'] = $res->DarkMode;
                   $data['CompactMode'] = $res->CompactMode;
                   $data['LeadlistMode'] = $res->LeadlistMode;
                   $data['EnableNotification'] = $res->EnableNotification;
                   $data['PushToken'] = $res->PushToken;
                   $data['agentchangestatus'] =  $changestatus;
                   $data['success'] = true;
                   $data['is_login'] = true;
                }else {
                   $data = array('success'=>false,'msg'=>'Please pass correct api key.');
               }
            }else{
                $data = array('success'=>false,'msg'=>'Please pass correct token.');
            }

        }else{
            $data = array('success'=>false,'msg'=>'Please pass correct token.');
        }
           return $data;
    }
    // Profile update
    public function ProfileUpdate(Request $request){
        // $this->load->helper(array('form'));
        // $upload_path = './uploads/';
        // $config['upload_path']   = $upload_path;
        // $config['allowed_types'] = 'gif|jpeg|jpg|png';
        // //$config['max_size'] = '200';
        // $config['remove_spaces'] = TRUE;
        // $config['encrypt_name'] = TRUE;
        $agentimage = '';
        // $imgerror = '';
        // $this->load->library('upload', $config);
        // if(!($this->upload->do_upload('agntimg')))  {
        //     $imgerror = $this->upload->display_errors();
        // } else {
        //     $uploadData = $this->upload->data();
        //     $filename = $uploadData['file_name'];
        //     $agentimage = $this->upload->data('file_name');
        // }
        $datasend=[];
        $apikey = $request->apikey;
        if($request->agent_id){
            $agent_id = $request->agent_id;
        }
        if($request->tknvalueget){
            $tknval = $request->tknvalueget;
        }
        if($request->phone){
            $agntdirectphone = $request->phone;
        }
        if($request->email){
            $agntdirectemail = $request->email;
        }
        //$agntcarrier = $request->carrier;
        if($request->gsuitget){
            $agntgsuite = $request->gsuitget;
        }
        if($request->agntoffice){
            $agntoffice = $request->agntoffice;
        }
        if($request->assgnzipscodes){
            $assgnzipscodes = $request->assgnzipscodes;
        }
        if($request->status){
            $agntactvstatus = $request->status;
        }
        if($request->phncarrierval){
            $phncartxt = $request->phncarrierval;
        }
        if($request->phncarrierid){
            $phncarid = $request->phncarrierid;
        }
        if($request->othcarr){
            $othrcrtxt = $request->othcarr;
        }
        if($request->county){
            $county = $request->county;
        }
        if($request->state){
            $state = $request->state;
        }
        if($request->escrowagnt){
            $escrowagnt  = $request->escrowagnt;
        }
        if($request->escrowaddress){
            $escrowaddress  = $request->escrowaddress;
        }
        if($request->escrowphone){
            $escrowphone  = $request->escrowphone;
        }
        if($request->escrowemail){
            $escrowemail  = $request->escrowemail;
        }
        if($request->escrowfax){
            $escrowfax  = $request->escrowfax;
        }
        if($request->DarkMode){
            $DarkMode  = $request->DarkMode;
        }
        if($request->EnableNotification){
            $EnableNotification  = $request->EnableNotification;
        }
        if($request->PushToken){
            $PushToken = $request->PushToken;
        }
        if($request->CompactMode){
            $CompactMode = $request->CompactMode;
        }
        if($request->LeadlistMode){
            $LeadlistMode = $request->LeadlistMode;
        }
        // $tokenresult = $this->check_token($tknval);
        $ApiKeyData = env('ApiKey');
        if( isset($request->token) && !empty($request->token) ){
            $token= json_decode(base64_decode($request->token));
            if($token->login_user_id){
                $apikey    = isset($request->apikey)?$request->apikey:'';
                if( isset($apikey) && !empty($apikey) && $apikey==$ApiKeyData){
                if(!empty($othrcrtxt)){
                    $phncartxt  = $othrcrtxt ;
                    $phncarid = '';
                }
                $agent_id=intval($agent_id);
                $res=$this->LeadAgentModel::where('id',$agent_id)->first(['id', 'AgentActive','AgentHeadshotUrl','Gsuite']);
                // return $res;
                // $this->db->select("id, AgentActive,agent_headshot_url,gsuite");
                // $this->db->from('LeadAgentProfile');
                // $this->db->where('id',$agent_id);
                // $query  = $this->db->get();
                // $res = $query->row_array();
                //$res['AgentActive'];
                $pre_email = $res->Gsuite;
                $changed = 'No';
                if(isset($agntgsuite) && $agntgsuite!='' && $pre_email!=$agntgsuite){
                    $changed = 'Yes';
                }
                if(isset($agntactvstatus) && $agntactvstatus==''){
                    $agntactvstatus = $res->AgentActive;
                }
                if(isset($agntdirectphone))
                {
                $FormattedPhone = $agntdirectphone;
                $UnFormattedPhone = str_replace(' ','',$FormattedPhone);
                $UnFormattedPhone = str_replace('(','',$UnFormattedPhone);
                $UnFormattedPhone = str_replace(')','',$UnFormattedPhone);
                $UnFormattedPhone = str_replace('-','',$UnFormattedPhone);
                $UnFormattedPhone = ltrim(trim($UnFormattedPhone),'1');
                $UnFormattedPhone = trim($UnFormattedPhone);
                $FormattedPhone = preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $FormattedPhone);
            }
                if(isset($agntdirectphone)){
                    $datasend['ListAgentDirectPhone']=$agntdirectphone;
                }
                if(isset($UnformattedPhone)){
                    $datasend['UnformattedPhone']=$UnformattedPhone;
                }
                if(isset($FormattedPhone)){
                    $datasend['FormattedPhone']=$FormattedPhone;
                }
                if(isset($agntdirectemail)){
                    $datasend['ListAgentEmail']=$agntdirectemail;
                }
                if(isset($phncartxt)){
                    $datasend['Carrier']=$phncartxt;
                }
                if(isset($phncarid)){
                    $datasend['CarrierId']=$phncarid;
                }
                if(isset($agntactvstatus)){
                    $datasend['AgentActive']=$agntactvstatus;
                }
                if(isset($agntoffice)){
                    $datasend['ListOfficeName']=$agntoffice;
                }
                if(isset($assgnzipscodes)){
                    $datasend['ZipCodes']=$assgnzipscodes;
                }
                if(isset($county)){
                    $datasend['County']=$county;
                }
                if(isset($state)){
                    $datasend['AgentState']=$state;
                }
                if(isset($escrowagnt)){
                    $datasend['EscrowAgent']=$escrowagnt;
                }
                if(isset($escrowaddress)){
                    $datasend['EscrowAddress']=$escrowaddress;
                }
                if(isset($escrowphone)){
                    $datasend['EscrowPhone']=$escrowphone;
                }
                if(isset($escrowemail)){
                    $datasend['EscrowEmail']=$escrowemail;
                }
                if(isset($escrowfax)){
                    $datasend['EscrowFax']=$escrowfax;
                }
                if(isset($DarkMode)){
                    $datasend['DarkMode']=$DarkMode;
                }
                if(isset($EnableNotification)){
                    $datasend['EnableNotification']=$EnableNotification;
                }
                if(isset($PushToken)){
                    $datasend['PushToken']=$PushToken;
                }
                if(isset($CompactMode)){
                    $datasend['CompactMode']=$CompactMode;
                }
                if(isset($LeadlistMode)){
                    $datasend['LeadlistMode']=$LeadlistMode;
                }

                if(isset($agentimage) && $agentimage!='' &&  !empty($agentimage)){
                    //$agentimage =  $res['agent_headshot_url'];
                    $datasend['AgentHeadshotUrl'] = $agentimage;
                    $datasend['AgentHeadshot'] = 'Y';
                }
                $sql=$this->LeadAgentModel::where('id',$agent_id)->update($datasend);
                // $sql = $this->db->last_query();
                //print_r($_POST);
                $str = 'Information has been Updated Successfully!';

                $data['leads_content_upd'] = $str;
                // $data['qryfunct']  = $sql;
                $data['changed_gsuite'] = $changed;
                $data['success'] = true;
                // $data['imgerror'] = $agentimage.'--'.$imgerror;
            } else {
                $data = array('success'=>false,'msg'=>'Please pass correct api key.');
            }
        }else{
            $data = array('success'=>false,'msg'=>'Please pass correct token.');
        }

        }else{
            $data = array('success'=>false,'isLogin' => false,'msg'=>'Please pass correct token.');
        }
       return $data;
    }
//    Update Agent
    public function UpdateAgent(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $lead_id = isset($request->lead_id)?$request->lead_id:'';
                    $agent_id = isset($request->agent_id)?$request->agent_id:'';
                    $assign_agent_id = isset($request->assign_agent_id)?$request->assign_agent_id:'';
                    $zipcodes = isset($request->agn_zipcodes)?$request->agn_zipcodes:'';
                    $BulkActionStatus = isset($request->BulkActionStatus)?$request->BulkActionStatus:'';
                    $BulkLeadIds = isset($request->BulkLeadIds)?$request->BulkLeadIds:'';
                    $BulkLeadIds = trim($BulkLeadIds,',');
                    $leadids = array();
                    if($BulkLeadIds!="" && !empty($BulkLeadIds) && $BulkLeadIds!=null && $BulkActionStatus=='true'){
                        $leadids = explode(",",$BulkLeadIds);
                        if (in_array($lead_id,$leadids)){
                        }
                        else{
                            array_push($leadids,$lead_id);
                        }
                    }else{
                        $leadids = array($lead_id);
                    }
                    //print_r($leadids);
                    $logintype  = (isset($token->logintype) && $token->logintype!='' )?$token->logintype:'agent';
                    if($logintype!='admin'){
                        $mainleadid = $lead_id;
                        foreach($leadids as $leadsids){
                            $lead_id = $leadsids;
                            if($lead_id>0){
                                $select=$this->LeadsModel::where('id',$lead_id)->where('AssignedAgent',$assign_agent_id)->first();
//                                $select = "SELECT * from Leads where id=$lead_id and AssignedAgent = '$assign_agent_id'";
                                $res_same = $select;
                                if( $res_same){
                                } else {
//                                    $this->load->helper('common_helper');
//                                    $result = auto_lead_assignmentcall($lead_id,$assign_agent_id,1,0,1);
                                    $data = array('success'=>true,'isLogin' => true,'msg'=>'Agent is assigned Successfully.');
                                }
                            }
                            if($mainleadid == $leadsids){
                                if($lead_id>0 && $agent_id>0) {
                                    /*$upload_path = './uploads/';
                                    $config['upload_path']   = $upload_path;
                                    $config['allowed_types'] = 'gif|jpeg|jpg|png';
                                    $config['remove_spaces'] = TRUE;
                                    $config['encrypt_name'] = TRUE;
                                    $agentimage = '';
                                    $imgerror = '';
                                    $this->load->library('upload', $config);
                                    if(!($this->upload->do_upload('agentimage')))  {
                                        $imgerror = $this->upload->display_errors();
                                    } else {
                                        $uploadData = $this->upload->data();
                                        $filename = $uploadData['file_name'];
                                        $agentimage = $this->upload->data('file_name');
                                    }*/
                                    if(  isset($zipcodes) && $zipcodes!=''){
                                        $upd['ZipCode']=$zipcodes;
                                        $db = env('RUNNING_DB_INFO');
                                        if ($db == "sql") {
                                            $select = $this->LeadAgentModel::where('id', $agent_id)->update($upd);
                                        }else{
                                            $select = $this->LeadAgentModel::where('_id', $agent_id)->update($upd);
                                        }
//                                        $up_query = "UPDATE LeadAgentProfile SET ZipCodes='$zipcodes' where id = $agent_id ";
//                                        $this->db->query($up_query);
                                    }

                                    $user_data = array();
                                    /*if($agentimage!='' &&  !empty($agentimage)){
                                        $user_data['agent_headshot_url'] = $agentimage;
                                        $user_data['agent_headshot'] = 'Y';
                                    }*/
                                    if( isset($request->min1) && isset($request->max1) && $request->min1>0 && $request->max1>0  ){
                                        $user_data['Specialization1'] = 'RLSE';
                                        $user_data['min1'] = $request->min1;
                                        $user_data['max1'] = $request->max1;
                                    }
                                    if( isset($request->min0) && isset($request->max0) && $request->min0>0 && $request->max0>0  ){
                                        $user_data['Specialization0'] = 'RESI';
                                        $user_data['min0'] = $request->min0;
                                        $user_data['max0'] = $request->max0;
                                    }
                                    if( isset($request->min2) && isset($request->max2) && $request->min2>0 && $request->max2>0  ){
                                        $user_data['Specialization2'] = 'RINC';
                                        $user_data['min2'] = $request->min2;
                                        $user_data['max2'] = $request->max2;
                                    }
                                    if( isset($request->min3) && isset($request->max3) && $request->min3>0 && $request->max3>0  ){
                                        $user_data['Specialization3'] = 'LAND';
                                        $user_data['min3'] = $request->min3;
                                        $user_data['max3'] = $request->max3;
                                    }
                                    if( isset($request->min4) && isset($request->max4) && $request->min4>0 && $request->max4>0  ){
                                        $user_data['Specialization4'] = 'BZOP';
                                        $user_data['min4'] = $request->min4;
                                        $user_data['max4'] = $request->max4;
                                    }
                                    if( isset($request->min5) && isset($request->max5) && $request->min5>0 && $request->max5>0  ){
                                        $user_data['Specialization5'] = 'COMM';
                                        $user_data['min5'] = $request->min5;
                                        $user_data['max5'] = $request->max5;
                                    }
                                    if( isset($request->min6) && isset($request->max6) && $request->min6>0 && $request->max6>0  ){
                                        $user_data['Specialization6'] = 'COML';
                                        $user_data['min6'] = $request->min6;
                                        $user_data['max6'] = $request->max6;
                                    }
                                    if( isset($request->min7) && isset($request->max7) && $request->min7>0 && $request->max7>0  ){
                                        $user_data['Specialization7'] = 'RESI_List';
                                        $user_data['min7'] = $request->min7;
                                        $user_data['max7'] = $request->max7;
                                    }
                                    //print_r($user_data);
                                    if(count($user_data)>0){
//                                        $this->db->where('id',$agent_id);
//                                        $this->db->update('LeadAgentProfile',$user_data);
                                        $db = env('RUNNING_DB_INFO');
                                        if ($db == "sql") {
                                            $upd = $this->LeadAgentModel::where('id', $agent_id)->update($user_data);
                                        }else{
                                            $upd = $this->LeadAgentModel::where('_id', $agent_id)->update($user_data);
                                        }
                                    }
                                    $data = array('success'=>true,'isLogin' => true,'msg'=>'Info updated successfully');

                                } else {
                                    if( ($agent_id=='' || $agent_id=='undefined' ) && $assign_agent_id>0  ){

                                    } else {
                                        $data = array('success'=>false,'isLogin' => true,'msg'=>'Please pass correct agent or lead');
                                    }
                                }
                            }
                        }
                    } else {
                        $data = array('success'=>false,'isLogin' => true,'msg'=>'Only Admin can reassign Agent.');
                    }
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        }
    }
}
