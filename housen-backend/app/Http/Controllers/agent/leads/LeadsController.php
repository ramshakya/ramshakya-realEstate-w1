<?php

namespace App\Http\Controllers\agent\leads;

use App\Constants\PropertyConstants;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\SqlModel\lead\LeadsModel;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\Validator;
// use App\Models\RetsPropertyDataPurged;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\SqlModel\LeadNotesModel;
use App\Models\SqlModel\Campaign\TemplatesModel;
use App\Models\RetsPropertyDataImage;
use App\Models\SqlModel\AlertsLog;
use App\Models\SqlModel\FavouriteProperties;
use App\Models\SqlModel\LoginDetails;
use App\Models\UserTracker;
use Carbon\Carbon;

class LeadsController extends Controller
{
    //
    public $retsPropertyData;
    public $LeadsModel;
    private $pdf;

    // public $AssignmentModel;
    public function __construct()
    {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql") {
            $this->LeadsModel = new LeadsModel();
            $this->retsPropertyData = new \App\Models\RetsPropertyData();

            $this->PropertyData = new \App\Models\RetsPropertyData();
            $this->PurgeDataModel = new RetsPropertyDataPurged();
        } else {

        }
    }

    public function index()
    {

        $data["pageTitle"] = "Leads";
        $data['status'] = config('mls_config.leads_status');
        $data['leads'] = $this->LeadsModel::selectRaw('count(*) as total, status')->groupBy('status')->get();
        // return $data;
        return view('agent.leads.leads', $data);
    }
    public function leads_rating(Request $request)
    {
        $result='';
        $getdata = $request->all();
        $r_value=0;
        $min_rating = LeadsModel::select('id','Rating')->where('id',$getdata['userid'])->get();
        if ($min_rating[0]->Rating !=0 && isset($getdata['userrate']) !=0) {
            if (isset($getdata['userrate']) && $getdata['userid']) {
                $data['Rating'] = $getdata['userrate'];
                $update = LeadsModel::where('id',$getdata['userid'])->update($data);
            }
            for ($i=1; $i <= $getdata['userrate']; $i++) {$r_value++;
            $result .='<span class="star_rated star_size" onclick="ratestar('. $getdata['userid'].','.$r_value.')">&#x2605;</span>';
            };
            $norating = 5 - $getdata['userrate'];
            if ($norating != 0) {
            for ($i=0; $i < $norating ; $i++) {$r_value++;
                $result .='<span class="star_size" onclick="ratestar('. $getdata['userid'].','.$r_value.')">&#x2605;</span>';
            }
            }
        }else{
            if ($getdata['userrate'] == 1 || $getdata['userrate'] == 0) {
                $result ='
                <span class="star_size star_rated" onclick="ratestar('. $getdata['userid'].',1)">&#x2605;</span> 
                <span class="star_size" onclick="ratestar('. $getdata['userid'].',2)">&#x2605;</span> 
                <span class="star_size" onclick="ratestar('. $getdata['userid'].',3)">&#x2605;</span> 
                <span class="star_size" onclick="ratestar('. $getdata['userid'].',4)">&#x2605;</span> 
                <span class="star_size" onclick="ratestar('. $getdata['userid'].',5)">&#x2605;</span>';
            }else{
                for ($i=1; $i <= $getdata['userrate']; $i++) {$r_value++;
                    $result .='<span class="star_rated star_size" onclick="ratestar('. $getdata['userid'].','.$r_value.')">&#x2605;</span>';
                };
                $norating = 5 - $getdata['userrate'];
                if ($norating != 0) {
                    for ($i=0; $i < $norating ; $i++) {$r_value++;
                        $result .='<span class="star_size" onclick="ratestar('. $getdata['userid'].','.$r_value.')">&#x2605;</span>';
                    }
                }
                $data['Rating'] = $getdata['userrate'];
                $update = LeadsModel::where('id',$getdata['userid'])->update($data);
            }
        }
        return $result;

    }
    public function LeadData(Request $request)
    {
        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        // Total records
        //        $totalRecords = LeadsModel::select('count(*) as allcount');
        $query = LeadsModel::select('Email', 'ContactName', 'AssignedAgentName', 'Phone', 'id', 'Address', 'Status', 'created_at', 'AdditionalProperties', 'TotalBeds', 'Beds', 'Baths', 'Price')->where('id', '!=', '')->orderBy('created_at','desc');
        if (isset($getdata['searchdata'])) {
            $search = $getdata['searchdata'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('ContactName', 'like', '%' . $search . '%')
                    ->orWhere('AssignedAgentName', 'like', '%' . $search . '%')
                    ->orWhere('Email', 'like', '%' . $search . '%')
                    ->orWhere('Phone', 'like', '%' . $search . '%');
            });
        }
        if (isset($getdata['statusdata'])) {
            $status = $getdata['statusdata'];
            $query = $query->whereIn('status', $status);
        }
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();

        $query = $query->orderBy($columnName, $columnSortOrder);
        $query = $query->skip($start);
        $query = $query->take($rowperpage);
        $records = $query->get(['Email', 'ContactName', 'AssignedAgentName', 'Phone', 'id', 'Address', 'Status', 'created_at', 'AdditionalProperties', 'TotalBeds', 'Beds', 'Baths', 'Price']);
        $data_arr = array();
        $srno = intval($start) + 1;
        foreach ($records as $record) {
            $Email = $record->Email;
            $ContactName = $record->ContactName;
            $AssignedAgentName = $record->AssignedAgentName;
            $Address = $record->Address;
            $Status = $record->Status;
            $created_at = $record->created_at;
            $id = $record->id;
            $Phone = $record->Phone;
            //            'id','Address','Status','created_at','AdditionalProperties','TotalBeds','Beds','Baths','Price'
            //            $id = $record->id;
            //            $username = $record->username;
            //            $name = $record->name;
            //            $email = $record->email;
            $action = '<a title="View Lead" href="'.url('agent/leadview/' . $record->id).'" class="float-left ">
            <i class="fa fa-eye"></i>
            </a>&nbsp;&nbsp;
            <a title="Edit Lead" href="'.url('agent/leadview/' . $record->id).'#Editing" class="">
            <i class="fa fa-edit"></i>
            </a>&nbsp;&nbsp;
            <a title="Activity Lead" href="'.url('agent/leadview/' . $record->id).'#Activity">
            <i class="fa fa-tasks" aria-hidden="true"></i>
            </a>&nbsp;&nbsp;
            <a title="Saved Search" href="'.url('agent/leadview/' . $record->id).'#savedSearch">
            <i class="fas fa-save" aria-hidden="true"></i>
            </a>&nbsp;&nbsp;
            <a title="Favourite Properties" href="'.url('agent/leadview/' . $record->id).'#favProperty">
            <i class="fa fa-heart" aria-hidden="true"></i>
            </a>&nbsp;&nbsp;
            <a title="View Email" href="'.url('agent/leadview/' . $record->id).'#eMail">
            <i class="fa fa-envelope" aria-hidden="true"></i>
            </a>&nbsp;&nbsp;
            <a href="#"
                onclick="get_delete_value('.$record->id.')"
                data-toggle="modal" data-target="#delete_data"
                title="Delete" class="text-danger">
                <i class="fa fa-trash"></i>
            </a>
            <br/>';
            $r_value=0;
            $rating='';
            $rating_value = LeadsModel::select('Rating')->where('id',$record->id)->get();
            // dd($rating_value[0]->Rating);
            if ($rating_value[0]->Rating == '' || count($rating_value) == 0) {
                $rating = '<div class="rating_section star"  id="starrating_'. $record->id.'">
                            <span class="star_size" onclick="ratestar('. $record->id.',1)">&#x2605;</span> 
                            <span class="star_size" onclick="ratestar('. $record->id.',2)">&#x2605;</span> 
                            <span class="star_size" onclick="ratestar('. $record->id.',3)">&#x2605;</span> 
                            <span class="star_size" onclick="ratestar('. $record->id.',4)">&#x2605;</span> 
                            <span class="star_size" onclick="ratestar('. $record->id.',5)">&#x2605;</span> 
                        </div> ';
            }else{
                $rate = '<div class="rating_section star"  id="starrating_'. $record->id.'">';
                $rateclose = '</div>';
                for ($i=0; $i < $rating_value[0]->Rating ; $i++) {$r_value++;
                    $rating .=' <span class="star_rated star_size" onclick="ratestar('. $record->id.','.$r_value.')">&#x2605;</span>';
                }
                $norating = 5 - $rating_value[0]->Rating;
                if ($norating != 0) {
                  for ($j=1; $j <= $norating ; $j++) {$r_value++;
                    $rating .='<span class="star_size" onclick="ratestar('. $record->id.','.$r_value.')">&#x2605;</span>';
                  }
                }
                $rating = $rate.$rating.$rateclose;
            }
            $data_arr[] = array(
                "id" => $srno,
                "Email" => $record->Email,
                "ContactName" => '<a href="' . url('agent/leadview/' . $record->id) . '">' . $record->ContactName . '</a>',
                "AssignedAgentName" => $record->AssignedAgentName,
                "Address" => $record->Address,
                "Status" => $record->Status,
                "Phone" => $record->Phone,
                "created_at" => $record->created_at,
                "action" =>$action.$rating,
            );
            $srno++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }
    public function LeadView($id = null)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Lead View";
        $data["lead"] = $this->LeadsModel::where('id', $id)->first();
        $data['templates'] = TemplatesModel::all();
        $data['AgentId'] = Auth()->user()->id;
        $data['notes'] = LeadNotesModel::where('LeadId', $id)->get();
        $data["leademail"] = AlertsLog::where('userId', $id)->get();
        $data['propertyView'] = UserTracker::where('UserId', $id)->get();
        $data['Allagent'] =[];
        //        return $data['Allagent'];
        //        $FavouriteProperties=FavouriteProperties::where('LeadId',$id)->OrderBy('id','Desc')->get();
        //        if(isset($FavouriteProperties) && !empty($FavouriteProperties))
        //        {
        //            foreach ($FavouriteProperties as &$p){
        //                $record1=$this->retsPropertyData::where("ListingId",$p->ListingId)->first(['ListPrice','StandardAddress']);
        //                $record=RetsPropertyDataImage::where('ListingId',$p->ListingId)->first();
        //                $p["ListPrice"]=$record1->ListPrice;
        //                $p["UnparsedAddress"]=$record1->StandardAddress;
        //                if(isset($record) && !empty($record)) {
        //                    $p['Media']=$record->s3_image_url;
        //                }else{
        //                    $p['Media']= "";
        //                }
        ////            return $p["Property"];
        //            }
        //        }
        //        $data['FavouriteProperties']=$FavouriteProperties;
        //        if(isset($data["lead"]['AssignedAgent']) && !empty($data["lead"]['AssignedAgent'])) {
        //            $data['LoginDetails'] = LoginDetails::where('AgentId', $data["lead"]['AssignedAgent'])->OrderBy('id','Desc')->get();
        //        }
        $data['LeadsEmail'] = AlertsLog::where('userId', $id)->get();
        //        $data["PageView"]=PageView::where('LeadId',$id)->get();
        //        return $data['PageView'];
        $images = [];

        //        foreach ($data["propertyView"] as &$p){
        //            $record1=$this->retsPropertyData::where("ListingId",$p->ListingId)->first(['ListPrice','StandardAddress']);
        //            $record=RetsPropertyDataImage::where('ListingId',$p->ListingId)->first();
        //            $p["ListPrice"]=$record1->ListPrice;
        //            $p["UnparsedAddress"]=$record1->StandardAddress;
        //            if(isset($record) && !empty($record)) {
        //                $p['Media']=$record->s3_image_url;
        //            }else{
        //                $p['Media']= "";
        //            }
        ////            return $p["Property"];
        //        }

        //        return $data["propertyView"];
        $data['id'] = $id;
        // return $data;
        return view('agent.leads.leadview', $data);
    }

    public function getLeads(Request $request)
    {
        $getdata = $request->all();
        $data['request'] = $getdata;
        // $type='';

        $query = $this->LeadsModel::where('id', '!=', '');
        if (isset($getdata['search'])) {
            $search = $getdata['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('ContactName', 'like', '%' . $search . '%')
                    ->orWhere('AssignedAgentName', 'like', '%' . $search . '%')
                    ->orWhere('Email', 'like', '%' . $search . '%')
                    ->orWhere('Phone', 'like', '%' . $search . '%');
            });
        }
        if (isset($getdata['status'])) {
            $status = $getdata['status'];
            $query = $query->whereIn('status', $status);
        }
        $data['leads'] = $query->orderBy('id', 'ASC')
            ->limit(50)
            ->get();
        foreach ($data['leads'] as &$key) {
            if ($key['AdditionalProperties']) {

                $addr = $key['AdditionalProperties'];
                $addr1 = explode(',', $addr);
                if (isset($addr1[1])) {
                } else {
                    $addr1[1] = '';
                }
                $key->property = $this->retsPropertyData::where('id', '!=', '');
                /*$key->property=$key->property->where(function($q) use ($addr1) {
                    $q->orwhere('UnparsedAddress',$addr1[0])
                        ->orWhere('ListOfficeMlsId',$addr1[1]);
                });*/
                //$key->property=$key->property->get();
                //$key->addr=$addr1;
            }
            $key->additional_properties = [];
        }
        return response(collect($data)->all(), 200);
    }
    public function getAllLeads(Request $request)
    {
        $getdata = $request->all();
        $token = $request->token;

        $searchFlag = $request->searchFlag;
        $searchId = $request->searchId;
        $statusvalarrfilter = $request->statusval;
        //$leadtypegetfilter = $this->input->get('leadtypeval');
        $leadtypegetarrt_filter = $request->leadtypeval;
        $minvarfilter = $request->minvar;
        $maxvarfilter = $request->maxvar;
        $zipcfilter_val = $request->zipcfilt_val;

        $propertytype_filt = $request->propertytype_filt;
        $motivation_filt = $request->motivation_filt;
        $mlsstatus_filt = $request->mlsstatus_filt;
        $dncstatus_filt = $request->dncstatus_filt;
        $state_filt = $request->state_filt;
        $counties_filt = $request->counties_filt;
        $city_filt = $request->city_filt;
        $keyword_filt = $request->keyword_filt;
        $selectkeyword_filt = $request->selectkeyword_filt;

        $ApiKeyData = env('ApiKey');
        $page    = isset($request->start_offset) ? $request->start_offset : 0;
        $limit  = 10;
        $rowvalimit    = isset($request->limit) ? $request->limit : $limit;
        $limit = intval($rowvalimit);
        $start_offset = 0;
        if ($page > 0) {
            $start_offset = intval(($page - 1) * $limit);
        }
        // return $token;
        // $type='';
        if (isset($token) && !empty($token)) {
            $user = json_decode(base64_decode($token));

            $user->id = $user->login_user_id;
            //            return $user;
            if ($user->id) {
                $leadlistmode = $user->LeadlistMode;
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $logintype = 'agent';
                    $sort_order = 'ASC';
                    $sort_by    = isset($request->sort_by) ? $request->sort_by : '';
                    $lead_search    = isset($request->lead_search) ? $request->lead_search : '';
                    $search_category = isset($request->search_category) ? $request->search_category : '';

                    if ($sort_by != "") {
                        if ($request->sort_order != "") {
                            $sort_order = $request->sort_order;
                        }

                        // $sort_query = " ORDER BY $sort_by $sort_order ";
                        if ($sort_by == "Price") {
                            $sort_by = 'Price';
                            // $sort_query =orderBY($sort_by,$sort_order);
                            // $sort_by = 'Price';
                        } else {
                            // $sort_query = orderBY($sort_by,$sort_order);
                        }
                    } else {
                        $sort_by = 'updated_at';
                        $sort_order = 'ASC';
                        // $sort_query = orderBy('updated_at','ASC');
                    }

                    $query = LeadsModel::where('AssignedAgent', '' . $user->id . '');

                    if ($searchFlag == "true") {
                        if ($searchId != '' && !empty($searchId)) {
                            // $lead_cond = where('id',''.$searchId.'');
                            $query = $query->where('id', '' . $searchId . '');
                        } else {
                            if ($logintype == 'admin') {
                                if ($search_category != '' && $lead_search != '') {
                                    if ($search_category == 'AssignedAgentName') {
                                        $query = $query->where('AssignedAgentName', 'like', '%' . $lead_search . '%');
                                    } else if ($search_category == 'Name') {
                                        $query = $query->where('ContactName', 'like', '%' . $lead_search . '%');
                                    } else if ($search_category == 'Email') {
                                        $query = $query->where('Email', 'like', '%' . $lead_search . '%');
                                    } else if ($search_category == 'Phone') {
                                        $query = $query->where(function ($q) use ($lead_search) {
                                            $q->orWhere('Phone', $lead_search)
                                                ->orWhere('UnformattedPhone', $lead_search)
                                                ->orWhere('FormattedPhone', $lead_search);
                                        });
                                    }
                                }
                            } else {
                                if ($lead_search != '') {
                                    $search = $lead_search;
                                    $query = $query->where(function ($q) use ($search) {
                                        $q->where('ContactName', 'like', '%' . $search . '%')
                                            ->orWhere('AssignedAgentName', 'like', '%' . $search . '%')
                                            ->orWhere('Email', $search)
                                            ->orWhere('Phone', $search)
                                            ->orWhere('UnformattedPhone', $search)
                                            ->orWhere('FormattedPhone', $search);
                                    });
                                }
                            }
                        }
                    } else {
                        if ($logintype == 'admin' || $logintype == "admin") {
                            if ($search_category != '' && $lead_search != '') {
                                if ($search_category == 'AssignedAgentName') {
                                    $query = $query->where('AssignedAgentName', 'like', '%' . $lead_search . '%');
                                } else if ($search_category == 'Name') {
                                    $query = $query->where('ContactName', 'like', '%' . $lead_search . '%');
                                } else if ($search_category == 'Email') {
                                    $query = $query->where('Email', 'like', '%' . $lead_search . '%');
                                } else if ($search_category == 'Phone') {
                                    $query = $query->where(function ($q) use ($lead_search) {
                                        $q->orWhere('Phone', $lead_search)
                                            ->orWhere('UnformattedPhone', $lead_search)
                                            ->orWhere('FormattedPhone', $lead_search);
                                    });
                                }
                            }
                        } else {
                            if ($lead_search != '') {
                                $search = $lead_search;
                                $query = $query->where(function ($q) use ($search) {
                                    $q->where('ContactName', 'like', '%' . $search . '%')
                                        ->orWhere('AssignedAgentName', 'like', '%' . $search . '%')
                                        ->orWhere('Email', $search)
                                        ->orWhere('Phone', $search)
                                        ->orWhere('UnformattedPhone', $search)
                                        ->orWhere('FormattedPhone', $search);
                                });
                            }
                        }
                    }
                    // Filter
                    if ($propertytype_filt != "" && $propertytype_filt != NULL && $propertytype_filt != 'null') {
                        // $propertytype_filt_string = "'" . str_replace(",", "','", $propertytype_filt) . "'";
                        // $lead_cond .= " AND ( PropType IN ($propertytype_filt_string))  ";
                        $propertytype_filt_string = explode(",", $propertytype_filt);
                        $query = $query->whereIn('PropType', $propertytype_filt_string);
                    }
                    if ($motivation_filt != "" && $motivation_filt != NULL && $motivation_filt != 'null') {

                        $motivationstr = $motivation_filt;
                        $motivationstr = rtrim($motivationstr, ',');
                        $motivationstr = ltrim($motivationstr, ',');
                        $motivationarr = explode(',', $motivationstr);
                        $chk = 'AND';
                        if ($key = array_search('FSBO', $motivationarr) !== false) {
                            $motivation_filt_string = "'FSBO','FRBO','FRBO'";
                            // $lead_cond .= " $chk ( Folder IN ($motivation_filt_string))  ";
                            $motivation_filt_string = explode(",", $motivation_filt_string);
                            $query = $query->whereIn('Folder', $motivation_filt_string);
                            // $chk = 'OR';
                        }
                        if ($key = array_search('XPRD', $motivationarr) !== false) {
                            $motivation_filt_string = "'OffMarket','Off Market','offmarket'";
                            $motivation_filt_string = explode(",", $motivation_filt_string);
                            // $lead_cond .= " $chk ( Folder IN ($motivation_filt_string))  ";
                            $query = $query->orWhereIn('Folder', $motivation_filt_string);
                        }
                        if ($key = array_search('Latepropertytaxes', $motivationarr) !== false) {
                            $motivation_filt_string = "'Dlqnt_Tax'";
                            // $lead_cond .= " $chk ( Motivation IN ($motivation_filt_string))  ";
                            $motivation_filt_string = explode(",", $motivation_filt_string);
                            $query = $query->WhereIn('Motivation', $motivation_filt_string);
                            // $chk = 'OR';
                        }
                        if ($key = array_search('Preforeclosure', $motivationarr) !== false) {
                            $motivation_filt_string = "'OffMarket','Off Market','offmarket'";
                            $motivation_filt_string = explode(",", $motivation_filt_string);
                            // $lead_cond .= " $chk  JudgementDate IS NULL";
                            $query = $query->orWhere('JudgementDate', '!=', NULL);
                            // $chk = 'OR';
                        }
                        if ($key = array_search('Foreclosure', $motivationarr) !== false) {
                            $motivation_filt_string = "'OffMarket','Off Market','offmarket'";
                            // $lead_cond .= " $chk  JudgementDate IS NOT NULL";
                            $motivation_filt_string = explode(",", $motivation_filt_string);
                            $query = $query->orWhere('JudgementDate', '!=', NULL);
                        }
                    }
                    if ($mlsstatus_filt != "" && $mlsstatus_filt != NULL && $mlsstatus_filt != 'null') {
                        $mlsstatus__filt_string = "'" . str_replace(",", "','", $mlsstatus_filt) . "'";
                        // $lead_cond .= " AND ( MlsStatus IN ($mlsstatus__filt_string))  ";
                        $mlsstatus__filt_string = explode(",", $mlsstatus__filt_string);
                        $query = $query->WhereIn('MlsStatus', $mlsstatus__filt_string);
                    }
                    if ($dncstatus_filt != "" && $dncstatus_filt != NULL && $dncstatus_filt != 'null') {
                        $dncstatus_filt_string = "'" . str_replace(",", "','", $dncstatus_filt) . "'";
                        // $lead_cond .= " AND ( PhoneDNCStatus IN ($dncstatus_filt_string))  ";
                        $dncstatus_filt_string = explode(",", $dncstatus_filt_string);
                        $query = $query->WhereIn('PhoneDNCStatus', $dncstatus_filt_string);
                    }
                    if ($state_filt != "" && $state_filt != NULL && $state_filt != 'null') {
                        $state_filt_string = "'" . str_replace(",", "','", $state_filt) . "'";
                        // $lead_cond .= " AND ( L.state IN ($state_filt_string))  ";
                        $state_filt_string = explode(",", $state_filt_string);
                        $query = $query->WhereIn('State', $state_filt_string);
                    }
                    if ($counties_filt != "" && $counties_filt != NULL && $counties_filt != 'null') {
                        // $counties_filt_string = "'" . str_replace(",", "','", $counties_filt) . "'";
                        // $lead_cond .= " AND ( L.County IN ($counties_filt_string))  ";
                        $counties_filt_string = explode(",", $counties_filt);
                        $query = $query->WhereIn('County', $counties_filt_string);
                    }
                    if ($city_filt != "" && $city_filt != NULL && $city_filt != 'null') {
                        $city_filt_string = explode(",", $city_filt);
                        // $lead_cond .= " AND ( L.city IN ($city_filt_string))  ";
                        $query = $query->WhereIn('City', $city_filt_string);
                    }
                    if ($statusvalarrfilter != "" && $statusvalarrfilter != NULL && $statusvalarrfilter != 'null') {
                        // $statusvalarrfilte_string = "'" . str_replace(",", "','", $statusvalarrfilter) . "'";
                        // $lead_cond .= " AND ( status IN ($statusvalarrfilte_string))  ";
                        $statusvalarrfilte_string = explode(',', $statusvalarrfilter);
                        $query = $query->WhereIn('Status', $statusvalarrfilte_string);
                    }
                    if ($leadtypegetarrt_filter != '' && $leadtypegetarrt_filter != NULL && $leadtypegetarrt_filter != 'null') {
                        //   $leadtypget_string = "'" . str_replace(",", "','", $leadtypegetarrt_filter) . "'";
                        //   $lead_cond .= " AND ( LeadType IN ($leadtypget_string))  ";
                        $leadstr = $leadtypegetarrt_filter;
                        $leadstr = rtrim($leadstr, ',');
                        $leadstr = ltrim($leadstr, ',');
                        $leadarr = explode(',', $leadstr);

                        $leadarr1 = [];
                        if ($key = array_search('LandloardOM', $leadarr) !== false) {
                            $key = array_search('LandloardOM', $leadarr);
                            $leadarr1[] = "Landloard";
                            unset($leadarr[$key]);
                        }
                        if ($key = array_search('SellerOM', $leadarr) !== false) {
                            $key = array_search('SellerOM', $leadarr);
                            $leadarr1[] = "Seller";
                            unset($leadarr[$key]);
                        }

                        if (count($leadarr) > 0) {
                            // $leadtype_text = implode("','",$leadarr);
                            //$leadtype_text = "'" .$leadtype_text."'";
                            // $lead_cond .= " AND ( LeadType IN ('" .$leadtype_text."'))  ";
                            $query = $query->WhereIn('LeadType', $leadtype_text);
                        }
                        if (count($leadarr1) > 0) {
                            if (count($leadarr) > 0) {
                                $chk = 'OR';
                            } else {
                                $chk = 'AND';
                            }
                            // $leadtype_text1 = implode("','",$leadarr1);
                            //$leadtype_text1 = "'" .$leadtype_text."'";
                            // $lead_cond .= " $chk ( LeadType IN ('" .$leadtype_text1."')) AND L.Source = 'offmarket' ";
                            $query = $query->orWhereIn('LeadType', $leadtype_text1);
                            $query = $query->where('Source', 'offmarket');
                        }
                    }
                    if ($zipcfilter_val != '' && $zipcfilter_val != 'null' && $zipcfilter_val != NULL) {
                        // $lead_cond .= " AND ( zipcode IN ($zipcfilter_val) ) ";
                        $zipcfilter_val = explode(',', $zipcfilter_val);
                        $query = $query->WhereIn('Zipcode', $zipcfilter_val);
                    }
                    if ($minvarfilter != "" && $maxvarfilter != "") {
                        // $lead_cond .= " AND ( Price between '$minvarfilter' and '$maxvarfilter') ";
                        $query = $query->whereBetween('Price', [$minvarfilter, $maxvarfilter]);
                    }
                    if ($keyword_filt != '' && $keyword_filt != 'null' && $keyword_filt != NULL && $selectkeyword_filt != '' && $selectkeyword_filt != 'null' && $selectkeyword_filt != NULL) {
                        $chk = 'AND';
                        //$keyword_filt = trim($keyword_filt);
                        if ($selectkeyword_filt == "Like") {
                            $query = $query->where(function ($q) use ($keyword_filt) {
                                $q->where('Source', $keyword_filt)
                                    ->orWhere('PropType', $keyword_filt)
                                    ->orWhere('LeadType', $keyword_filt)
                                    ->orWhere('Status', $keyword_filt)
                                    ->orWhere('MlsStatus', $keyword_filt)
                                    ->orWhere('Price', $keyword_filt)
                                    ->orWhere('mls_id', $keyword_filt)
                                    ->orWhere('Address', $keyword_filt)
                                    ->orWhere('Email', $keyword_filt)
                                    ->orWhere('ListAgentEmail', $keyword_filt)
                                    ->orWhere('Phone', $keyword_filt)
                                    ->orWhere('UnformattedPhone', $keyword_filt)
                                    ->orWhere('FormattedPhone', $keyword_filt);
                            });
                            //$chk = 'OR';
                        }
                        if ($selectkeyword_filt == "NotLike") {

                            $query = $query->where(function ($q) use ($keyword_filt) {
                                $q->where('Source', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('PropType', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('LeadType', $keyword_filt)
                                    ->orWhere('Status', $keyword_filt)
                                    ->orWhere('MlsStatus', $keyword_filt)
                                    ->orWhere('Price', $keyword_filt)
                                    ->orWhere('mls_id', $keyword_filt)
                                    ->orWhere('Address', $keyword_filt)
                                    ->orWhere('Email', $keyword_filt)
                                    ->orWhere('ListAgentEmail', $keyword_filt)
                                    ->orWhere('Phone', $keyword_filt)
                                    ->orWhere('UnformattedPhone', $keyword_filt)
                                    ->orWhere('FormattedPhone', $keyword_filt);
                            });
                            //$chk = 'OR';
                        }
                        if ($selectkeyword_filt == "Contains") {

                            $query = $query->where(function ($q) use ($keyword_filt) {
                                $q->where('Source', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('PropType', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('LeadType', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('Status', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('MlsStatus', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('Price', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('mls_id', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('Address', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('Email', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('ListAgentEmail', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('Phone', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('UnformattedPhone', 'like', '%' . $keyword_filt . '%')
                                    ->orWhere('FormattedPhone', 'like', '%' . $keyword_filt . '%');
                            });
                            //$chk = 'OR';
                        }
                        if ($selectkeyword_filt == "NotContains") {

                            $query = $query->where(function ($q) use ($keyword_filt) {
                                $q->where('Source', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('PropType', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('LeadType', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('Status', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('MlsStatus', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('Price', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('mls_id', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('Address', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('Email', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('ListAgentEmail', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('Phone', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('UnformattedPhone', 'not like', '%' . $keyword_filt . '%')
                                    ->orWhere('FormattedPhone', 'not like', '%' . $keyword_filt . '%');
                            });
                            //$chk = 'OR';
                        }
                    }

                    if (isset($getdata['status'])) {
                        $status = $getdata['status'];
                        $query = $query->whereIn('Status', $status);
                    }
                    $data['leads'] = $query->orderBy($sort_by, $sort_order)->limit($limit, $start_offset)->get(['ContactName', 'AssignedAgentName', 'Email', 'Phone', 'id', 'Address', 'Status', 'created_at', 'AdditionalProperties', 'TotalBeds', 'Beds as LeadBed', 'Baths as LeadBaths', 'Price', 'mls_id', 'MlsStatus', 'ContractPrice', 'FormattedPhone', 'Phone', 'PhoneDNCStatus', 'Phone2', 'Phone2DNCStatus', 'Phone3', 'Phone3DNCStatus', 'Phone4', 'Phone4DNCStatus', 'Phone5', 'Phone5DNCStatus', 'Emailstatus', 'AgentNotes', 'LeadType as LeadTypeValget', 'Source', 'ChgStatusAt', 'AssignmentDateTime', 'LeaseLength as Length', 'AssignedAgent', 'Message', 'MoveInDate', 'LeaseLength', 'HaveRealtor', 'CreditScore', 'Parking', 'Pets', 'updated_at']);
                    $data['total'] = $query->count();
                    $data['start_offset'] = $start_offset;
                    $data['limit'] = $limit;
                    foreach ($data['leads'] as &$key) {
                        $key->AssignmentDateTime = strtotime($key->AssignmentDateTime);
                        $key->assignmenttime = date('jS F Y h:i A', $key->AssignmentDateTime);
                        $key->updatedstatus_date = date('m/d/Y h:i A', strtotime($key->updated_at));
                        $key->BuildingName = '';
                        $key->PublicRemark = '';
                        $key->ListAgentFullName = '';
                        $key->ListOfficeName = '';
                        $key->ListAgentDirectPhone = '';
                        $key->CoListAgentEmail = '';
                        $key->YearBuilt  = '';
                        $key->LivingArea = '';
                        // $key->listagentinfo = '';
                        $key->Furnished = '';
                        $key->PublicRemark = '';
                        $key->leadtype = '';
                        $key->extra_info = '';
                        $key->leadtypevalget = '';
                        $key->Address2 = '';
                        $key->MlsStatusList = '';
                        $key->TableName = "";
                        $key->ImagesUrls = '/agent/images/blinx%20concept_v2.jpg';

                        if ($key->mls_id) {
                            $all_l_text = $key->mls_id;
                            // $all_l_text = implode("','",$al_l);
                            // $all_l_text = "'".$all_l_text."'";
                            // return $all_l_text;
                            $property = $this->retsPropertyData::where('ListingId', $all_l_text)->first(['ImagesUrls', 'PublicRemarks', 'ListOfficeName', 'MlsStatus', 'ListAgentFullName', 'ListAgentDirectPhone', 'ListAgentEmail', 'YearBuilt', 'PrivateRemarks', 'SubdivisionName', 'BuildingName', 'LivingArea', 'Furnished', 'PrivateRemarks']);
                            // return $all_l_text;
                            if ($property) {
                                // return $property;
                                $key->BuildingName = $property->BuildingName;
                                $key->PublicRemark = $property->PublicRemarks;
                                $key->ListAgentFullName = $property->ListAgentFullName;
                                $key->ListOfficeName = $property->ListOfficeName;
                                $key->ListAgentDirectPhone = $property->ListAgentDirectPhone;
                                $key->CoListAgentEmail = $property->BuildingName;
                                $key->YearBuilt  = $property->YearBuilt;
                                $key->LivingArea = $property->LivingArea;
                                $key->Furnished = $property->Furnished;
                                // $key->PublicRemark = $property;
                                $key->leadtype = $property->LivingArea;
                                $key->TableName = "PropertyData";
                                $key->ImagesUrls = '/blinx%20concept_v2.jpg';
                                $public = ['PublicRemarks' => $property->PublicRemarks, 'ListAgentFullName' => $key->ListAgentFullName, 'ListAgentDirectPhone' => $key->ListAgentDirectPhone, 'ListAgentEmail' => $key->ListAgentEmail, 'ListOfficeName' => $key->ListOfficeName, 'ShowingInstruction' => '', 'BrokerRemark' => ''];
                                $key->PublicRemark = $public;
                            }
                            // $key->addr=$addr1;
                        }
                        if ($key->AssignedAgent) {
                            $AssignedAgent = $key->AssignedAgent;
                            // return $AssignedAgent;
                            $agent = [];

                            $key->agent = $agent->first(['DarkMode', 'CompactMode', 'LeadlistMode', 'ListAgentEmail', 'ListAgentFullName', 'ListAgentMlsId', 'ListOfficePhone']);
                        }
                        $AssignedAgent = intval($key->AssignedAgent);
                        $key->listagentinfo = [];
                        $extra_info = ['message' => $key->message, 'move_in_date' => $key->move_in_date, 'HaveRealtor' => $key->HaveRealtor, 'lease_length' => $key->lease_length, 'credit_score' => $key->credit_score, 'parking' => $key->parking, 'pets' => $key->pets];
                        $key->extra_info = $extra_info;
                        $Phones = ['FormattedPhone' => $key->FormattedPhone, 'Phone' => $key->PhonePhone2, 'PhoneDNCStatus' => $key->PhoneDNCStatus, 'Phone2' => $key->Phone2, 'Phone2DNCStatus' => $key->Phone2DNCStatus, 'Phone3' => $key->Phone3, 'Phone3DNCStatus' => $key->Phone3DNCStatus, 'Phone4' => $key->Phone4, 'Phone4DNCStatus' => $key->Phone4DNCStatus, 'Phone5' => $key->Phone5, 'Phone5DNCStatus' => $key->Phone5DNCStatus];

                        $key->Phone = $Phones;

                        if ($key->Address != '') {
                            $properaddrmap = '';
                            $Addressget = $key->Address;
                            $addrestringAfter = str_replace(" ", "+", $Addressget);
                            $Addresscity = $key->city;
                            $AddresscityAfter = str_replace(" ", "+", $Addresscity);
                            $properaddrmap .= '' . $addrestringAfter . ',+' . $AddresscityAfter . ',+' . $key->state . ',+' . $key->zipcode . '';

                            $Address2 = ['properaddrmap' => $properaddrmap, 'Address' => $key->Address, 'city' => $key->city, 'state' => $key->state, 'zipcode' => $key->zipcode];
                            $key->Address2 = $Address2;
                        }
                        unset($key->FormattedPhone);
                        unset($key->PhonePhone2);
                        unset($key->PhoneDNCStatus);
                        unset($key->Phone2);
                        unset($key->Phone2DNCStatus);
                        unset($key->Phone3);
                        unset($key->Phone3DNCStatus);
                        unset($key->Phone4);
                        unset($key->Phone4DNCStatus);
                        unset($key->Phone5);
                        unset($key->Phone5DNCStatus);
                        unset($key->message);
                        unset($key->move_in_date);
                        unset($key->HaveRealtor);
                        unset($key->lease_length);
                        unset($key->credit_score);
                        unset($key->parking);
                        unset($key->pets);
                    }
                    $data['str'] = "";
                    $data['DarkMode'] = $user->DarkMode;
                    $data['CompactMode'] = $user->CompactMode;
                    $data['LeadlistMode'] = $user->LeadlistMode;
                } else {
                    $data = array('success' => false, 'is_login' => true, 'msg' => 'Please pass correct api key.');
                }
                // }

            } else {
                $data = array('sucess' => false, 'is_login' => false, 'msg' => 'invalid user token');
            }
        } else {
            $data = array('sucess' => false, 'is_login' => false, 'msg' => 'invalid user token');
        }
        return $data;
    }

    // All Api data
    public function AllApiData(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey) ? $request->apikey : '';
        // return $request->apikey;
        if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
            $data = array();
            $data['counties'] = $this->allcountries();
            $data['states'] = $this->allstates();
            $data['active_agents'] = $this->allactiveagents();
            $data['all_leadstatus'] = $this->allleadstatus();
            $data['all_leadtype'] = $this->allleadtype();
            $data['all_mlsstatus'] = $this->allmlsstatus();
            $data['success'] = true;
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct api key.');
        }
        return $data;
    }

    private function allcountries()
    {
        $result = $this->retsPropertyData::distinct('County')->get(['County']);
        $data = array();
        if ($result) {
            foreach ($result as $counties) {
                $data[] = $counties['county'];
            }
        }
        return $data;
    }
    private function allstates()
    {
        $result = PostalMasterModel::where('state', '!=', NULL)->where('state', '!=', '')->distinct('state')->get(['state', 'st_abb']);
        $data = [];
        if ($result) {
            foreach ($result as $state) {
                $data1['state_abbr'] = $state['st_abb'];
                $data1['state'] = $state['state'];
                $data[] = $data1;
            }
        }
        return $data;
    }
    private function allactiveagents()
    {
        $all_results = [];
        $data = [];
        foreach ($all_results as $agent) {
            $data1['agent_id'] = $agent['id'];
            $data1['name'] = $agent['ListAgentFullName'];
            $data[] = $data1;
        }
        return $data;
    }
    private function allleadstatus()
    {
        $data = array();
        $data = config('mls_config.leads_status');
        return $data;
    }
    private function allleadtype()
    {
        $data = config('mls_config.all_lead_types');
        return $data;
    }
    private function allmlsstatus()
    {
        $all_results = $this->LeadsModel::where('MlsStatus', '!=', NULL)->where('MlsStatus', '!=', '')->distinct('MlsStatus')->get(['mlsStatus']);
        $data = array();
        foreach ($all_results as $mlsStatus) {
            $data[] = $mlsStatus['mlsStatus'];
        }
        return $data;
    }
    public function GetLeadResultsGet(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        $AgentId = $request->agentid;
        // return $request->token;
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));

            if ($token->login_user_id) {

                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $agent_id = isset($request->AgentId) ? $request->AgentId : '';
                    $sort_order = 'ASC';
                    $onlyname   = isset($request->onlyname) ? $request->onlyname : 0;
                    $agentid    = isset($request->agentid) ? $request->agentid : '';
                    $logintype  = (isset($token->logintype) && $token->logintype != '') ? $token->logintype : 'agent';
                    $agent_cond = '';

                    $array = array();
                    // $leads_result=array();
                    $term  = $request->q;
                    if ($logintype == 'admin' && $onlyname == 0) {
                        $leads_data = $this->LeadsModel::where('AssignedAgentName', 'like', '%' . $term . '%')->distinct('AssignedAgentName');
                        if ($agentid != '' && $logintype == 'agent') {
                            // $leads_data   = array();
                            $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                        }
                        $leads_result = $leads_data->get(['AssignedAgentName', 'id']);
                        // return $lead_result;
                        if ($leads_result) {
                            foreach ($leads_result as $lead) {
                                $agent_name = $lead->AssignedAgentName;
                                $array[] = array('id' => $lead->id, 'label' => $agent_name, 'category' => 'AssignedAgentName');
                            }
                        }
                    }
                    if ($onlyname == 1) {
                        $leads_data = $this->LeadsModel::where('ContactName', 'like', '%' . $term . '%');
                        if ($agentid != '' && $logintype == 'agent') {
                            // $leads_data   = array();
                            $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                        }
                        $leads_result = $leads_data->get(['id', 'ContactName', 'Email', 'Phone', 'FormattedPhone']);
                    } else {
                        $leads_data = $this->LeadsModel::where('ContactName', 'like', '%' . $term . '%');
                        if ($agentid != '' && $logintype == 'agent') {
                            // $leads_data   = array();
                            $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                        }
                        $leads_result = $leads_data->get(['id', 'ContactName', 'Email', 'Phone', 'FormattedPhone']);
                    }
                    if ($leads_result) {
                        foreach ($leads_result as $lead) {
                            $contact_name = $lead->ContactName;
                            $array[] = array('id' => $lead->id, 'label' => $contact_name, 'category' => 'Name', 'email' => $lead->Email, 'phone' => $lead->FormattedPhone);
                        }
                    }
                    if (isset($onlyname) && $onlyname == 0) {
                        $leads_data = $this->LeadsModel::where('Email', 'like', '%' . $term . '%');
                        if ($agentid != '' && $logintype == 'agent') {
                            // $leads_data   = array();
                            $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                        }
                        $leads_result = $leads_data->get(['id', 'ContactName', 'Email', 'Phone', 'FormattedPhone']);
                        if ($leads_result) {
                            foreach ($leads_result as $lead) {
                                $email = $lead->Email;
                                $array[] = array('id' => $lead->id, 'label' => $email, 'category' => 'Email');
                            }
                            // return $leads_result;
                        }
                        $leads_data = $this->LeadsModel::where('UnFormattedPhone', 'like', '%' . $term . '%');
                        if ($agentid != '' && $logintype == 'agent') {
                            // $leads_data   = array();
                            $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                        }
                        $leads_result = $leads_data->get(['id', 'ContactName', 'Email', 'Phone', 'FormattedPhone', 'UnFormattedPhone']);
                        if (count($leads_result) > 0) {
                            if ($leads_result) {
                                foreach ($leads_result as $lead) {
                                    $phone = $lead->FormattedPhone;
                                    $array[] = array('id' => $lead->id, 'label' => $phone, 'category' => 'Phone');
                                }
                            }
                        } else {
                            $leads_data = $this->LeadsModel::where('Phone', 'like', '%' . $term . '%');
                            if ($agentid != '' && $logintype == 'agent') {
                                // $leads_data   = array();
                                $leads_data   = $leads_data->where('AssignedAgent', $agentid);
                            }
                            $leads_result = $leads_data->get(['id', 'ContactName', 'Email', 'Phone', 'FormattedPhone', 'UnFormattedPhone']);
                            if ($leads_result) {
                                foreach ($leads_result as $lead) {
                                    $phone = $lead->FormattedPhone;
                                    $array[] = array('id' => $lead->id, 'label' => $phone, 'category' => 'Phone');
                                }
                            }
                        }
                    }
                    return $array;
                } else {
                    $data = array('success' => false, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'msg' => 'Please pass correct api key.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct token.');
        }

        return $data;
    }

    // Leads info update
    public function LeadsInfoUpdate(Request $request)
    {
        //$agentid = $request->agent_id;
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {

                    $apikey = $request->apikey;
                    $leadtbl_id = $request->leadtbl_id;
                    $leadtbl_name = $request->leadtbl_name;
                    $leadphone = $request->leadtbl_phone;
                    $leadphone2 = $request->leadtbl_phone2;
                    $leadphone3 = $request->leadtbl_phone3;
                    $leadphone4 = $request->leadtbl_phone4;
                    $leadphone5 = $request->leadtbl_phone5;
                    $leademail = $request->leadtbl_email;
                    $leadtbl_agentnotes = $request->leadtbl_agentnotes;

                    $PhoneDNCStatus = $request->PhoneDNCStatus;
                    $Phone2DNCStatus = $request->Phone2DNCStatus;
                    $Phone3DNCStatus = $request->Phone3DNCStatus;
                    $Phone4DNCStatus = $request->Phone4DNCStatus;
                    $Phone5DNCStatus = $request->Phone5DNCStatus;

                    // if (isset($tokenresult['sucess']) && $tokenresult['sucess'] == false) {
                    //     $data = array('sucess' => false, 'is_login' => false, 'msg' => $tokenresult['message']);
                    // } else {
                    $apikey = isset($_REQUEST['apikey']) ? $_REQUEST['apikey'] : '';
                    $phonearr = [];
                    if (isset($apikey) && !empty($apikey) && $apikey == 'broker3112linux117') {
                        if (isset($leadtbl_name) && $leadtbl_name != '') {
                            $datasend['contact_name'] = trim($leadtbl_name);
                        }
                        if (isset($leademail) && $leademail != '') {
                            $datasend['email'] = trim($leademail);
                        }
                        if (isset($leadtbl_agentnotes) && $leadtbl_agentnotes != '') {
                            $datasend['agent_notes'] = $leadtbl_agentnotes;
                        }
                        if (isset($leadphone) && $leadphone != '') {
                            $phone_nos = $leadphone;
                            $phone_nos = str_replace(' ', '', $phone_nos);
                            $phone_nos = str_replace('(', '', $phone_nos);
                            $phone_nos = str_replace(')', '', $phone_nos);
                            $phone_nos = str_replace('+1', '', $phone_nos);
                            $phone_nos = str_replace('-', '', $phone_nos);
                            $phone_nos = ltrim(trim($phone_nos), '1');
                            $phone_nos = trim($phone_nos);

                            $formattedphone = preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $leadphone);
                            $datasend['Phone'] = trim($leadphone);
                            $datasend['FormattedPhone'] = trim($formattedphone);
                            $datasend['UnFormattedPhone'] = $phone_nos;
                        }
                        $datasend['Phone2'] = trim($leadphone2);
                        $datasend['Phone3'] = trim($leadphone3);
                        $datasend['Phone4'] = trim($leadphone4);
                        $datasend['Phone5'] = trim($leadphone5);
                        $datasend['PhoneDNCStatus'] = $PhoneDNCStatus;
                        $datasend['Phone2DNCStatus'] = $Phone2DNCStatus;
                        $datasend['Phone3DNCStatus'] = $Phone3DNCStatus;
                        $datasend['Phone4DNCStatus'] = $Phone4DNCStatus;
                        $datasend['Phone5DNCStatus'] = $Phone5DNCStatus;
                        $sql = '';
                        if (!empty($datasend) && count($datasend) > 0) {
                            $sql = $this->LeadsModel::where('id', '' . $leadtbl_id . '')->update($datasend);
                        }
                        $str = 'Information has been Updated Successfully!';
                        $data['leads_updateinfo'] = $str;
                        $data['qryfunctlead']  = $datasend;
                        $data['success'] = true;
                        $data['is_login'] = true;
                    } else {
                        $data = array('success' => false, 'msg' => 'Please pass correct api key.');
                    }

                    // }
                } else {
                    $data = array('success' => false, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    // Start phoneCarrier Api
    public function AllPhoneCarrier(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey) ? $request->apikey : '';
        if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
            // $query = "SELECT * from PhoneCarrier";
            // $result = $this->db->query( $query )->result_array();
            $result = PhoneCarrier::all();
            $data['phonecarriers'] = array();
            if ($result) {
                #foreach($result as $zip){
                #$data['phonecarriers'][] = $zip['zipcode'] ;
                #}
                $data['phonecarriers'] = $result;
            }
            $data['success'] = true;
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct api key.');
        }
        return $data;
    }
    // PropertyImages
    public function PropertyImages(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $leadmlsid = $request->leadmlsid;
                    $agent_id = $token->login_user_id;

                    //$list_row = PropertyData::where('ListingId', $leadmlsid)->first('ImagesUrls');
                    $list_row = RetsPropertyData::where('ListingId', $leadmlsid)->first('ImageUrl');
                    //$list_row_purge = PurgeDataModel::where('ListingId', $leadmlsid)->first('ImagesUrls');
                    $list_row_purge = [];
                    // return $list_row;
                    // $se = "SELECT images_urls from rets_property_data where ListingId ='$leadmlsid' ";
                    // $list_row = $this->db->query($se);

                    // $se_purge = "SELECT images_urls from rets_purged_data where ListingId ='$leadmlsid' ";
                    // $list_row_purge = $this->db->query($se_purge);
                    $result = false;
                    if ($list_row) {
                        $result = $list_row;
                    } else if ($list_row_purge) {
                        $result = $list_row_purge;
                    }
                    // return $result;
                    //$result = $query->row_array();
                    $imgelist = $result->ImagesUrls;
                    $string_parts = explode(",", $imgelist);
                    $data = [];
                    foreach ($string_parts as $key => $value) {
                        // $data["imageshtml"][]= '<div><img data-toggle="modal" data-target="#slic_full_screen" id="imgslic_fulscrn" class="img-responsive" src="'.$value.'"></div>';
                        $data["images"][] = $value;
                    }
                } else {
                    $data = array('sucess' => false, 'is_login' => true, 'msg' => "please pas correct apikey");
                }
            } else {
                $data = array('success' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    // Status Leads Contract
    public function StatusLeadContract(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $data = StatusLeadContract($request);
                    $data['success'] = true;
                    $data['is_login'] = true;
                } else {
                    $data = array('success' => false, 'is_login' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    public function StatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "leadagentid" => "required",
            "statsget" => "required",
            "apikey" => "required",
            "customaddress3" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        $leadagentid =  $request->leadagentid;
        $statusval =  $request->statsget;
        //$agentid = $request->agent_id;
        $apikey = $request->apikey;
        $contract_date =  $request->contract_date;
        $contract_occupancy =  $request->contract_occupancy;
        $contrct_price =  $request->contrct_price;
        $contract_term =  $request->contract_term;
        $contract_GCI =  $request->contract_GCI;
        $contract_address_full =  $request->customaddress3;
        $contract_addresses = explode(',', $contract_address_full);
        $contract_address = $contract_addresses[0];
        $contract_ref_split = $request->contract_ref_split;
        $contract_ref_amt = $request->contract_ref_amt;
        $contract_com_type = $request->contract_com_type;
        $contract_com_per = $request->contract_com_per;
        $agentnotes  = $request->agentnotes;
        $unit_num  = $request->unit_num;
        $contract_value = $request->contract_value;
        $contract_city =  $request->contract_city;
        $lost_reason =  $request->lost_reason;
        $rejected_reason =  $request->rejected_reason;
        $reassgn_reason = $request->reassgn_reason;
        $contract_mls  = $request->contractmls_no;
        $prp_type = $request->prp_type;
        $zipcode = $request->zipcode;
        $city = $request->city;
        $streetno = $request->streetno;

        $BulkActionStatus = isset($request->BulkActionStatus) ? $request->BulkActionStatus : '';
        $BulkLeadIds = isset($request->BulkLeadIds) ? $request->BulkLeadIds : '';
        $BulkLeadIds = trim($BulkLeadIds, ',');
        $leadids = "";
        if ($BulkLeadIds != "" && !empty($BulkLeadIds) && $BulkLeadIds != null && $BulkActionStatus == 'true') {
            $leadids = explode(",", $BulkLeadIds);
            if (in_array($leadagentid, $leadids)) {
            } else {
                array_push($leadids, $leadagentid);
            }
        } else {
            $leadids = array($leadagentid);
        }
        //print_r($leadids);
        $updatedate = date("Y-m-d H:i:s");
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $datasend['status'] = $statusval;
                    //if($statusval=='Under Contract'){
                    $mainleadid = $leadagentid;
                    foreach ($leadids as $leadsId) {
                        $leadagentid = $leadsId;
                        if ($mainleadid == $leadsId) {
                            if (isset($contrct_price) && $contrct_price != '') {
                                $datasend['contract_price'] = $contrct_price;
                            }
                            if (isset($contract_date) && $contract_date != '') {
                                $datasend['contract_date'] = $contract_date;
                            }
                            if (isset($contract_occupancy) && $contract_occupancy != '') {
                                $datasend['contract_occupancy'] = $contract_occupancy;
                            }
                            if (isset($contract_term) && $contract_term != '') {
                                $datasend['contract_term'] = $contract_term;
                            }
                            if (isset($contract_GCI) && $contract_GCI != '') {
                                $datasend['contract_GCI'] = $this->setDbValue($contract_GCI);
                            }
                            if (isset($contract_address) && $contract_address != '') {
                                $datasend['contract_address'] = $contract_address;
                            }
                            if (isset($contract_city) && $contract_city != '') {
                                $datasend['contract_city'] = $contract_city;
                            }
                            if (isset($contract_ref_split) && $contract_ref_split != '') {
                                $datasend['contract_ref_split'] = $contract_ref_split;
                            }
                            if (isset($contract_value) && $contract_value != '') {
                                $datasend['contract_value'] = $this->setDbValue($contract_value);
                            }
                            if (isset($contract_ref_amt) && $contract_ref_amt != '') {
                                $datasend['contract_ref_amt'] = $this->setDbValue($contract_ref_amt);
                            }
                            if (isset($contract_com_type) && $contract_com_type != '') {
                                $datasend['contract_com_type'] = $contract_com_type;
                            }
                            if (isset($contract_com_per) && $contract_com_per != '') {
                                $datasend['contract_com_per'] = $contract_com_per;
                            }
                            if (isset($unit_num) && $unit_num != '') {
                                $datasend['unit_num'] = $unit_num;
                            }
                        }


                        //}
                        if (isset($agentnotes) && $agentnotes != '') {
                            $datasend['agent_notes'] = $agentnotes;
                        }
                        if ($statusval == 'Reassignment Request') {
                            $datasend['reassgn_reason'] = $reassgn_reason;
                        }

                        if ($statusval == 'Lost') {
                            $datasend['lost_reason'] = $lost_reason;
                        }
                        if ($statusval == 'Rejected') {
                            $datasend['rejected_reason'] = $rejected_reason;
                        }
                        if ($statusval == 'Under Contract' || $statusval == 'Closing-In-Process' ||  $statusval == 'Closed' || $statusval == 'Compliance') {
                            $datasend['status_processed'] = 'N';
                        }
                        $datasend['updated_at'] = $updatedate;
                        $leadagentid = intval($leadagentid);
                        $this->LeadsModel::where('id', $leadagentid)->update($datasend);
                        // $this->db->where('id',$leadagentid);
                        // $this->db->update('Leads',$datasend);
                        $str = 'Status has been Updated Successfully!';
                        $date503 = date('m/d/y g:i A',  strtotime($updatedate));
                        $data['leads_content'] = $str;
                        $data['status_datechnage'] = $date503;
                        $data['success'] = true;

                        if ($statusval == 'Under Contract' || $statusval == 'Closing-In-Process' ||  $statusval == 'Closed') {

                            /*$city = "Miami";
                            $zipcode = "33127";
                            $prp_type = "RLSE";
                            $unitno = '345';
                            $streetno = '240';*/
                            //$contract_mls = '';
                            if (isset($unit_num) && trim($unit_num) != '') {
                                $contract_address = preg_replace('/\s+/', ' ', $contract_address);
                                $contract_address = trim($contract_address) . " " . $unit_num;
                            }
                            $contract_address = trim($contract_address);
                            //echo "fsdf".$contract_address;
                            /*$mls_search = $this->getmatching_listing($contract_address,$city,$zipcode,$prp_type,$streetno,$unit_num);
                            if(isset($mls_search['data']) && isset($mls_search['data']['MLSNum'])) {
                                $contract_mls = $mls_search['data']['MLSNum'];
                            }*/
                            $sql = $this->LeadsModel::where('id', $leadagentid)->where('address', $contract_address)->orWhere('mls_id', $contract_mls)->first();
                            //                            $sql ="SELECT address,mls_id FROM Leads where (address='$contract_address' or mls_id='$contract_mls') and id= $leadagentid";
                            $address_data = $sql;
                            if (count($address_data) == 0) {
                                //echo "Address Changed";
                                alterleadmainaddress($leadagentid, $contract_address, $contract_mls, $statusval);
                            }
                            //alterleadmainaddress($leadagentid,$contract_address,$contract_mls,$statusval);
                        }

                        if ($statusval == 'Reassignment Request') {
                            // So it will added to another Agent's Contact List
                            $updateq = $this->LeadsModel::where('id', $leadagentid)->update(['AgentGContactId' => NULL]);
                            //                            $updateq = "UPDATE `Leads` SET `AgentGContactId` = NULL WHERE id = $leadagentid";
                            //                            $rup = $this->db->query($updateq);
                            // Update Lead Contacts id and Send alert email and Do assignment
                            //$this->reassignAgent($leadagentid);
                            //                            $this->load->helper('common_helper');
                            //                            $result = auto_lead_assignmentcall($leadagentid,0,0,1,1);
                        }
                    }

                    $data['is_login'] = true;
                } else {
                    $data = array('success' => false, 'is_login' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'is_login' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'is_login' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    private function setDbValue($value)
    {
        return preg_replace("/[^0-9\.]/", '', $value);
    }
    public function GenratePdf(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                //                return $token;
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    //                require_once(FCPATH.'application/util/genrateapivariable.php');
                    $getvar = new GenerateApiVariable($request, $token);
                    $Offers = $getvar->Offers;
                    // return $getvar->dbdata;
                    $contract_log_id = addpdfdata($getvar->dbdata, $getvar->ContractLogId);
                    // return $contract_log_id;
                    // $AddOtherOwnerAndTenant = array();
                    // $AddOtherOwnerAndTenant['AdditionalOwnerName']  = explode(',', $otherOwnerNames);
                    // $AddOtherOwnerAndTenant['AdditionalOwnerPhone']  = explode(',', $otherOwnerPhones);
                    // $AddOtherOwnerAndTenant['AdditionalOwnerEmail']  = explode(',', $otherOwnerEmails);
                    // $AddOtherOwnerAndTenant['AdditionalTenantName']  = explode(',', $otherTenantNames);
                    // $AddOtherOwnerAndTenant['AdditionalTenantEmail']  = explode(',', $otherOwnerEmails);
                    // $AddOtherOwnerAndTenant['AdditionalTenantPhone']  = explode(',', $otherTenantPhones);

                    $AdditionalOwnerName = explode(',', $getvar->AdditionalOwnerName);
                    $AdditionalOwnerPhone = explode(',', $getvar->AdditionalOwnerPhone);
                    $AdditionalOwnerEmail = explode(',', $getvar->AdditionalOwnerEmail);
                    $AdditionalTenantName = explode(',', $getvar->AdditionalTenantName);
                    $AdditionalTenantEmail  = explode(',', $getvar->AdditionalTenantEmail);
                    $AdditionalTenantPhone  = explode(',', $getvar->AdditionalTenantPhone);
                    foreach ($AdditionalOwnerName as $key => $value) {
                        $arr = [];
                        $arr["OwnerName"] = $value;
                        $arr["OwnerPhone"] = $AdditionalOwnerPhone[$key];
                        $arr["OwnerEmail"] = $AdditionalOwnerEmail[$key];
                        $arr["TenantName"] = $AdditionalTenantName[$key];
                        $arr["TenantEmail"] = $AdditionalTenantEmail[$key];
                        $arr["TenantPhone"] = $AdditionalTenantPhone[$key];
                        $AddOtherOwnerAndTenant[] = $arr;
                    }
                    // return $AddOtherOwnerAndTenant;
                    if ($getvar->proptype != '') {
                        if ($getvar->proptype == 'RLSE' || $getvar->proptype == 'COML') {
                            $lead_type_name  = 'Tenant';
                        } else if ($getvar->proptype == 'RESI' || $getvar->proptype == 'RINC' || $getvar->proptype == 'COMM' || $getvar->proptype == 'BZOP' || $getvar->proptype == 'LAND') {
                            $lead_type_name = 'Buyer';
                        } else if ($getvar->proptype == 'RLSE_List' || $getvar->proptype == 'COML_List') {
                            $lead_type_name  = 'Landlord';
                        } else if ($getvar->proptype == 'RESI_List' || $getvar->proptype == 'RINC_List' || $getvar->proptype == 'COMM_List' || $getvar->proptype == 'BZOP_List' || $getvar->proptype == 'LAND_List') {
                            $lead_type_name  = 'Seller';
                        }
                    } else {
                        $lead_type_name = "";
                    }
                    if ($getvar->proptype != '') {
                        if (($getvar->proptype == 'RESI' || $getvar->proptype == 'RINC' || $getvar->proptype == 'COMM' || $getvar->proptype == 'BZOP' || $getvar->proptype == 'LAND') && $getvar->FormType == "Listings" && $Offers == "Offers_ForSale") {
                            $lead_type_name = 'Seller';
                        } else if (($getvar->proptype == 'RESI' || $getvar->proptype == 'RINC' || $getvar->proptype == 'COMM' || $getvar->proptype == 'BZOP' || $getvar->proptype == 'LAND') && $getvar->FormType == "Offers" && $Offers == "Offers_ForSale") {
                            $lead_type_name = 'Buyer';
                        } else if (($getvar->proptype == 'RLSE' || $getvar->proptype == 'COML') && $getvar->FormType == "Listings" && $getvar->Offers == "Offers_ForRent") {
                            $lead_type_name  = 'Landlord';
                        } else if (($getvar->proptype == 'RLSE' || $getvar->proptype == 'COML') && ($getvar->RentFormType == "ForRent_ListingDocs" || $getvar->RentFormType == "ForSale_BuyerDocs")) {
                            $lead_type_name  = 'Landlord';
                        } else if ($getvar->proptype == 'RLSE' && $Offers == "Offers_ForRent") {
                            $lead_type_name  = 'Tenant';
                        }
                    } else {
                        $lead_type_name = "";
                    }
                    /*echo " FormType - ".$getvar->FormType;
                echo " RentFormType - ".$getvar->RentFormType;
                echo " Offers - ".$Offers;
                echo $lead_type_name;
                exit;*/
                    $leadId = $getvar->leadId;
                    if (!empty($getvar->leadId)) {
                        $queryLead = $this->LeadsModel::where('id', $getvar->leadId)->first();
                        // $sql="select * from Leads where  id = $leadId";
                        // $queryLead = $this->db->query($sql);
                        if ($queryLead) {
                            $queryLeadResult = $queryLead;
                            if ($queryLeadResult->contract_GCI != null && !empty($queryLeadResult->contract_GCI)) {
                                $addContractvalue = array();
                                $addContractvalue['ContractPrice'] = $this->setDbValue($getvar->OfferPrice);
                                $addContractvalue['ContractValue'] = $this->setDbValue($getvar->contractvaluerlse);
                                $addContractvalue['ContractGCI'] = $this->setDbValue($getvar->ListingCommissionAmountBDocs);
                                $addContractvalue['ContractComType'] = $getvar->comtyperlse;
                                $addContractvalue['ContractComPer'] = $getvar->ListingCommissionBDocs;
                                $addContractvalue['updated_at'] = date("Y-m-d H:i:s");
                                $leadId = intval($leadId);
                                $this->LeadsModel::where('id', $leadId)->update($addContractvalue);
                                // $this->db->where('id',$leadId);
                                // $this->db->update('Leads',$addContractvalue);
                            }
                        }


                        if ($getvar->ContractLogId != "" && !empty($getvar->ContractLogId) && $getvar->ContractLogId != null) {
                            $ContractLogIdd = $getvar->ContractLogId;
                        } else {
                            $leadId = intval($leadId);
                            $ContractLogId = ContractLogModel::where('LeadId', $leadid)->first(['id']);
                            // $sql = "select id from contractlog where leadid = $leadId";
                            // $ContractLogId = $this->db->query($sql)->row_array();
                            $ContractLogIdd = $ContractLogId->id;
                        }

                        $data = array();
                        $data["ContractLogId"] = $ContractLogIdd;
                        $data["AllOtherOwnerAndTenant"] = $AddOtherOwnerAndTenant;
                        // $arr = json_encode($data);
                        $arr = $data;
                        $datasend = array();
                        $datasend['AllOtherOwnerAndTenant'] = $arr;
                        $datasend['CoLeads'] = $arr;
                        $datasend['LeadType'] = $lead_type_name;
                        $datasend['status'] = $getvar->statusval;
                        if (isset($getvar->proptype) && $getvar->proptype != '') {
                            $datasend['PropType'] = $getvar->proptype;
                        }
                        $datasend['updated_at'] = date("Y-m-d H:i:s");
                        // $this->db->where('id',$leadId);
                        // $this->db->update('Leads',$datasend);
                        $this->LeadsModel::where('id', $leadId)->update($datasend);
                        $contract_address = trim($getvar->Address);
                        if (!empty($contract_address) && $contract_address != '') {
                            $shortAdress = explode(',', $contract_address);
                            $contract_address = $shortAdress[0];
                        }
                        // return $datasend;
                        $contract_mls     = trim($getvar->OfferMlsid);
                        if ($contract_mls != "" && $contract_mls != null && !empty($contract_mls)) {
                            // $sql ="SELECT address,mls_id FROM Leads where (address='$contract_address' or mls_id='$contract_mls') and id= $leadId";
                            $sql = $this->LeadsModel::where('id', $leadId)->first(['Address', 'mls_id']);
                        } else {
                            // $sql ="SELECT address,mls_id FROM Leads where (address='$contract_address' ) and id= $leadId";
                            $sql = $this->LeadsModel::where('id', $leadId)->where('Address', $contract_address)->first(['Address', 'mls_id']);
                        }
                        $address_data = $sql;
                        $newStateOrProvince = $getvar->StateOrProvince;
                        if ($getvar->StateOrProvince == '') {
                            $newStateOrProvince = $State;
                        }
                        //                    if(empty($address_data)) {
                        //                        alterleadmainaddress($leadId,$contract_address,$contract_mls,$getvar->statusval,$getvar->City,$newStateOrProvince,$getvar->ZipCode);
                        //                    }
                    }
                    $contract_address = trim($getvar->Address);
                    if (!empty($contract_address) && $contract_address != '') {
                        $shortAdress = explode(',', $contract_address);
                        $contract_address = $shortAdress[0];
                    }
                    if (!empty($getvar->onlyaddress) && $getvar->onlyaddress != '') {
                        $onlyaddress = $getvar->onlyaddress;
                    } else {
                        $onlyaddress = " ";
                    }
                    /*if($getvar->proptype!=''){
                    if($getvar->proptype=='RLSE' || $getvar->proptype=='COML'){
                        $lead_type_name  = 'Tenant';
                    } else if( $getvar->proptype=='RESI' || $getvar->proptype=='RINC' || $getvar->proptype=='COMM' || $getvar->proptype=='BZOP' || $getvar->proptype=='LAND' ){
                        $lead_type_name = 'Buyer';
                    } else if( $getvar->proptype=='RLSE_List' || $getvar->proptype=='COML_List' ){
                        $lead_type_name  = 'Landloard';
                    } else if( $getvar->proptype=='RESI_List' || $getvar->proptype=='RINC_List' || $getvar->proptype=='COMM_List' || $getvar->proptype=='BZOP_List' || $getvar->proptype=='LAND_List' ){
                        $lead_type_name  = 'Seller';
                    }
                }else{
                    $lead_type_name ="";
                }
                 if($getvar->proptype!=''){
                    if($getvar->proptype=='RLSE' && $Offers=="Offers_ForRent"){
                        $lead_type_name  = 'Tenant';
                    } else if($getvar->proptype=='RLSE' || $getvar->proptype=='COMML' && $getvar->RentFormType=="ForRent_ListingDocs"){
                        $lead_type_name  = 'Landloard';
                    }else if( $getvar->proptype=='RESI' || $getvar->proptype=='RINC' || $getvar->proptype=='COMM' || $getvar->proptype=='BZOP' || $getvar->proptype=='LAND' && $getvar->RentFormType=="Offers_ForSale" ){
                        $lead_type_name = 'Buyer';
                    } else if( $getvar->proptype=='RESI' || $getvar->proptype=='RINC' || $getvar->proptype=='COMM' || $getvar->proptype=='BZOP' || $getvar->proptype=='LAND'  && $getvar->RentFormType=="ForSale_ListingDocs"){
                        $lead_type_name  = 'Seller';
                    }
                }else{
                    $lead_type_name ="";
                } */

                    $LeadTenantName = "";
                    $LeadTenantEmail = "";
                    $LeadTenantPhone = "";
                    if ($getvar->RentFormType == "ForSale_BuyerDocs") {
                        $LeadTenantName = $getvar->BuyerNameBdocs;
                        $LeadTenantEmail = $getvar->BuyerEmailBdocs;
                        $LeadTenantPhone = $getvar->BuyerPhoneBdocs;
                    }
                    if ($getvar->FormType == "Offers" && $getvar->Offers == "Offers_ForRent") {
                        $LeadTenantName = $getvar->Tenant;
                        $LeadTenantEmail = $getvar->TenantEmail;
                        $LeadTenantPhone = $getvar->TenantPhone;
                    }
                    if ($getvar->FormType == "Listings") {
                        $LeadTenantName = $getvar->AgentNameLdocs;
                        $LeadTenantPhone = $getvar->AgentPhoneLdocs;
                        $LeadTenantEmail = $getvar->AgentEmailLdocs;
                    }
                    if ($getvar->StateOrProvince == "" || empty($getvar->StateOrProvince) || $getvar->StateOrProvince == null) {
                        $getvar->StateOrProvince = $getvar->State;
                    }

                    $addLeadData = array(
                        "source" => "from contract form",
                        "contact_name" => trim($LeadTenantName),
                        "price" =>    $this->setDbValue($getvar->OfferPrice),
                        "mls_id" =>   $getvar->OfferMlsid,
                        "address" =>  $onlyaddress,
                        "email"  =>   trim($LeadTenantEmail),
                        "phone"  =>   trim($LeadTenantPhone),
                        "FormattedPhone" => trim($LeadTenantPhone),
                        "unformattedphone" =>  trim(GetIntFromString($LeadTenantPhone)),
                        "unit"  =>    $getvar->Unit,
                        "Beds"  =>    $getvar->Bedrooms,
                        "Baths"  =>    $getvar->Baths,
                        "total_beds"  =>    $getvar->Bedrooms,
                        "city"  =>    $getvar->City,
                        "state"  =>   $getvar->StateOrProvince,
                        "zipcode" =>  $getvar->ZipCode,
                        "PropType"  =>   $getvar->proptype,
                        "LeadType" =>  $lead_type_name,
                        "FullAddress" => $getvar->Address,
                        "ShowingInstruction" => $getvar->ShowingRequirements,
                        "ListAgentFullName" => $getvar->ListingBrokerNameBdocs,
                        "ListAgentDirectPhone" => $getvar->ListingBrokerPhoneBdocs,
                        "ListAgentEmail" => $getvar->ListingBrokerEmailBdocs,
                        "status" => "Make an Offer",
                        "Date"   =>  date('m/d/y g:i A'),
                        "updated_at" => date('Y-m-d H:i:s'),
                        "created_at" => date('Y-m-d H:i:s'),
                    );

                    if ($LeadTenantName != "" && !empty($LeadTenantName) && $LeadTenantName != null) {
                        $lead_id = $this->addContractDataInLead($LeadTenantEmail, $LeadTenantPhone, $getvar->agentid, $addLeadData, $leadId);
                        if ($lead_id != null && $lead_id != "") {
                            $updateLeadId = array("leadid" => $lead_id);
                            $cid = addpdfdata($updateLeadId, $contract_log_id);
                        }
                    }
                    $data['leadid'] = $leadId;
                    $data['ContractLogId'] = $getvar->ContractLogId;
                    $data['Result'] = array();
                    //                require_once(FCPATH.'application/libraries/pdf/fpdf/fpdf.php');
                    //                require_once(FCPATH.'application/libraries/pdf/Fpdi.php');
                    //                include FCPATH.'application/libraries/pdf/vendor/autoload.php';
                    //                $pdf = new \setasign\Fpdi\Fpdi();

                    $pdf = new Fpdi();
                    if ($getvar->pdfdata['AdditionalFormAction'] == 'SeparatePDF') {
                        //                    return $getvar->pdfdata;
                        $data['Result']['Main']    = pdfConvert($contract_log_id, $getvar->pdfdata, $pdf, 'MAYANK', 'No');
                        $AdditionalFormMulti = explode(",", $getvar->pdfdata['AdditionalForm']);
                        //                    return $data['Result']['Main'];
                        //                    exit;
                        //  if($pdfdata['PropertyType']=="Residential"){
                        // if($pdfdata['PropertyType']=="Residential" && isset($pdfdata['AdditionalForm']) && in_array("RentalDeposit", $AdditionalFormMulti) && $pdfdata['RentFormType']!="ForSale_BuyerDocs"){
                        //     $pdf1 = new \setasign\Fpdi\Fpdi();
                        //     $data['Result']['Rental']  = pdfConvert($contract_log_id,$pdfdata,$pdf1,'Rental','Yes');
                        // }else{
                        //     $data['Result']['Rental'] = "";
                        // }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("LeadInfoPamphlet", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] != "ForRent_CTL") {
                            $pdf2 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['Paint']   = pdfConvert($contract_log_id, $pdfdata, $pdf2, 'Paint', 'Yes');
                        } else {
                            $data['Result']['Paint'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("CondominiumRider", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf3 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['CondominiumRider']   = pdfConvert($contract_log_id, $pdfdata, $pdf3, 'CondominiumRider', 'Yes');
                        } else {
                            $data['Result']['CondominiumRider'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("HomeownersAssociation", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf4 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['HomeownersAssociation']   = pdfConvert($contract_log_id, $pdfdata, $pdf4, 'HomeownersAssociation', 'Yes');
                        } else {
                            $data['Result']['HomeownersAssociation'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SellerFinancing", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs" || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SellerFinancing", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf5 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['SellerFinancing']   = pdfConvert($contract_log_id, $pdfdata, $pdf5, 'SellerFinancing', 'Yes');
                        } else {
                            $data['Result']['SellerFinancing'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("MortgageAssumption", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs" || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("MortgageAssumption", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf6 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['MortgageAssumption']   = pdfConvert($contract_log_id, $pdfdata, $pdf6, 'MortgageAssumption', 'Yes');
                        } else {
                            $data['Result']['MortgageAssumption'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("FhaVaFinancing", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf7 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['FhaVaFinancing']   = pdfConvert($contract_log_id, $pdfdata, $pdf7, 'FhaVaFinancing', 'Yes');
                        } else {
                            $data['Result']['FhaVaFinancing'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("AppraisalContingency", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf8 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['AppraisalContingency']   = pdfConvert($contract_log_id, $pdfdata, $pdf8, 'AppraisalContingency', 'Yes');
                        } else {
                            $data['Result']['AppraisalContingency'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("ShortSale", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs" || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("ShortSale", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf9 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['ShortSale']   = pdfConvert($contract_log_id, $pdfdata, $pdf9, 'ShortSale', 'Yes');
                        } else {
                            $data['Result']['ShortSale'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("HomeOwnersFlood", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf10 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['HomeOwnersFlood']   = pdfConvert($contract_log_id, $pdfdata, $pdf10, 'HomeOwnersFlood', 'Yes');
                        } else {
                            $data['Result']['HomeOwnersFlood'] = "";
                        }

                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("InterestBearing", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf11 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['InterestBearing']   = pdfConvert($contract_log_id, $pdfdata, $pdf11, 'InterestBearing', 'Yes');
                        } else {
                            $data['Result']['InterestBearing'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("AsIsRider", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf12 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['AsIsRider']   = pdfConvert($contract_log_id, $pdfdata, $pdf12, 'AsIsRider', 'Yes');
                        } else {
                            $data['Result']['AsIsRider'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("RightToInspect", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs" || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("RightToInspect", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf13 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['RightToInspect']   = pdfConvert($contract_log_id, $pdfdata, $pdf13, 'RightToInspect', 'Yes');
                        } else {
                            $data['Result']['RightToInspect'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("DefectiveDrywall", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf14 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['DefectiveDrywall']   = pdfConvert($contract_log_id, $pdfdata, $pdf14, 'DefectiveDrywall', 'Yes');
                        } else {
                            $data['Result']['DefectiveDrywall'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("CoastalConstructionControl", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs"  || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("CoastalConstructionControl", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf15 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['CoastalConstructionControl']   = pdfConvert($contract_log_id, $pdfdata, $pdf15, 'CoastalConstructionControl', 'Yes');
                        } else {
                            $data['Result']['CoastalConstructionControl'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("InsulationDisclosure", $AdditionalFormMulti)  && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf16 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['InsulationDisclosure']   = pdfConvert($contract_log_id, $pdfdata, $pdf16, 'InsulationDisclosure', 'Yes');
                        } else {
                            $data['Result']['InsulationDisclosure'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("LeadPaintDisclosure", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf17 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['LeadPaintDisclosure']   = pdfConvert($contract_log_id, $pdfdata, $pdf17, 'LeadPaintDisclosure', 'Yes');
                        } else {
                            $data['Result']['LeadPaintDisclosure'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("HousingOlderPersons", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf18 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['HousingOlderPersons']   = pdfConvert($contract_log_id, $pdfdata, $pdf18, 'HousingOlderPersons', 'Yes');
                        } else {
                            $data['Result']['HousingOlderPersons'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("Rezoning", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf19 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['Rezoning']   = pdfConvert($contract_log_id, $pdfdata, $pdf19, 'Rezoning', 'Yes');
                        } else {
                            $data['Result']['Rezoning'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("LeasePurchase", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf20 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['LeasePurchase']   = pdfConvert($contract_log_id, $pdfdata, $pdf20, 'LeasePurchase', 'Yes');
                        } else {
                            $data['Result']['LeasePurchase'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("PreClosingOccupancy", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf21 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['PreClosingOccupancy']   = pdfConvert($contract_log_id, $pdfdata, $pdf21, 'PreClosingOccupancy', 'Yes');
                        } else {
                            $data['Result']['PreClosingOccupancy'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("PostClosingOccupancy", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf22 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['PostClosingOccupancy']   = pdfConvert($contract_log_id, $pdfdata, $pdf22, 'PostClosingOccupancy', 'Yes');
                        } else {
                            $data['Result']['PostClosingOccupancy'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SaleBuyerProperty", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf23 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['SaleBuyerProperty']   = pdfConvert($contract_log_id, $pdfdata, $pdf23, 'SaleBuyerProperty', 'Yes');
                        } else {
                            $data['Result']['SaleBuyerProperty'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("BackUpContract", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf24 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['BackUpContract']   = pdfConvert($contract_log_id, $pdf24, 'BackUpContract', 'Yes');
                        } else {
                            $data['Result']['BackUpContract'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("KickOutClause", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf25 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['KickOutClause']   = pdfConvert($contract_log_id, $pdfdata, $pdf25, 'KickOutClause', 'Yes');
                        } else {
                            $data['Result']['KickOutClause'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SellerAttorneyApproval", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs"  || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SellerAttorneyApproval", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf26 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['SellerAttorneyApproval']   = pdfConvert($contract_log_id, $pdfdata, $pdf26, 'SellerAttorneyApproval', 'Yes');
                        } else {
                            $data['Result']['SellerAttorneyApproval'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("BuyerAttorneyApproval", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs" || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("BuyerAttorneyApproval", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf28 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['BuyerAttorneyApproval']   = pdfConvert($contract_log_id, $pdfdata, $pdf28, 'BuyerAttorneyApproval', 'Yes');
                        } else {
                            $data['Result']['BuyerAttorneyApproval'] = "";
                        }



                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("AddendumToContract", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf29 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['AddendumToContract']   = pdfConvert($contract_log_id, $pdfdata, $pdf29, 'AddendumToContract', 'Yes');
                        } else {
                            $data['Result']['AddendumToContract'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("ShortSaleAddendum", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf30 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['ShortSaleAddendum']   = pdfConvert($contract_log_id, $pdfdata, $pdf30, 'ShortSaleAddendum', 'Yes');
                        } else {
                            $data['Result']['ShortSaleAddendum'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("LicenseeDisclosureRider", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf31 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['LicenseeDisclosureRider']   = pdfConvert($contract_log_id, $pdfdata, $pdf31, 'LicenseeDisclosureRider', 'Yes');
                        } else {
                            $data['Result']['LicenseeDisclosureRider'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("BindingArbitration", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs"  || $getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("BindingArbitration", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf32 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['BindingArbitration']   = pdfConvert($contract_log_id, $pdfdata, $pdf32, 'BindingArbitration', 'Yes');
                        } else {
                            $data['Result']['BindingArbitration'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SpecialTaxingDistrict", $AdditionalFormMulti) && $getvar->pdfdata['RentFormType'] == "ForSale_BuyerDocs") {
                            $pdf33 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['SpecialTaxingDistrict']   = pdfConvert($contract_log_id, $pdfdata, $pdf33, 'SpecialTaxingDistrict', 'Yes');
                        } else {
                            $data['Result']['SpecialTaxingDistrict'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("ModifyListingAgreement", $AdditionalFormMulti)) {
                            if ($getvar->pdfdata['RentFormType'] == "ForSale_ListingDocs" || $getvar->pdfdata['RentFormType'] == "ForRent_ListingDocs") {
                                $pdf34 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['ModifyListingAgreement']   = pdfConvert($contract_log_id, $pdfdata, $pdf34, 'ModifyListingAgreement', 'Yes');
                            } else {
                                $data['Result']['ModifyListingAgreement'] = "";
                            }
                        } else {
                            $data['Result']['ModifyListingAgreement'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("LBPListing", $AdditionalFormMulti)) {
                            if ($getvar->pdfdata['RentFormType'] == "ForSale_ListingDocs" || $getvar->pdfdata['RentFormType'] == "ForRent_ListingDocs") {
                                $pdf35 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['LBPListing']   = pdfConvert($contract_log_id, $pdfdata, $pdf35, 'LBPListing', 'Yes');
                            } else {
                                $data['Result']['LBPListing'] = "";
                            }
                        } else {
                            $data['Result']['LBPListing'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("CovidInPerson", $AdditionalFormMulti)) {
                            if ($getvar->pdfdata['RentFormType'] == "ForSale_ListingDocs" || $getvar->pdfdata['RentFormType'] == "ForRent_ListingDocs") {
                                $pdf36 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['CovidInPerson']   = pdfConvert($contract_log_id, $pdfdata, $pdf36, 'CovidInPerson', 'Yes');
                            } else {
                                $data['Result']['CovidInPerson'] = "";
                            }
                        } else {
                            $data['Result']['CovidInPerson'] = "";
                        }

                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("CovidExtensionTime", $AdditionalFormMulti)) {
                            if ($getvar->pdfdata['FormType'] == "Offers" && $getvar->pdfdata['Offers'] == "Offers_ForSale") {
                                $pdf38 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['CovidExtensionTime']   = pdfConvert($contract_log_id, $pdfdata, $pdf38, 'CovidExtensionTime', 'Yes');
                            } else {
                                $data['Result']['CovidExtensionTime'] = "";
                            }
                        } else {
                            $data['Result']['CovidExtensionTime'] = "";
                        }

                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("ShortSaleERSA", $AdditionalFormMulti)) {
                            if ($getvar->pdfdata['RentFormType'] == "ForSale_ListingDocs" || $getvar->pdfdata['RentFormType'] == "ForRent_ListingDocs") {
                                $pdf37 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['ShortSaleERSA']   = pdfConvert($contract_log_id, $pdfdata, $pdf37, 'ShortSaleERSA', 'Yes');
                            } else {
                                $data['Result']['ShortSaleERSA'] = "";
                            }
                        } else {
                            $data['Result']['ShortSaleERSA'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Commercial" && isset($getvar->pdfdata['AdditionalForm']) && in_array("SectionExchange", $AdditionalFormMulti) && isset($getvar->pdfdata['FormType']) && $getvar->pdfdata['FormType'] == 'CommercialContract_Comm' && $getvar->pdfdata['Offers'] == 'Offers_ForSale_Comm') {
                            $pdf38 = new \setasign\Fpdi\Fpdi();
                            $data['Result']['SectionExchange']   = pdfConvert($contract_log_id, $pdfdata, $pdf38, 'SectionExchange', 'Yes');
                        } else {
                            $data['Result']['SectionExchange'] = "";
                        }
                        if ($getvar->pdfdata['PropertyType'] == "Residential" && isset($getvar->pdfdata['AdditionalForm']) && in_array("RentalDisbursment", $AdditionalFormMulti) && $getvar->pdfdata['FormType'] == "Compliance") {
                            if ($getvar->pdfdata['RentFormType'] == "ForRent_LeaseCondoMulti" || $getvar->pdfdata['RentFormType'] == "ForRent_LeaseSFH") {
                                $pdf39 = new \setasign\Fpdi\Fpdi();
                                $data['Result']['RentalDisbursment']  = pdfConvert($contract_log_id, $pdfdata, $pdf39, 'RentalDisbursment', 'Yes');
                            } else {
                                $data['Result']['RentalDisbursment'] = "";
                            }
                        } else {
                            $data['Result']['RentalDisbursment'] = "";
                        }
                        //  }//end  Residential addenda
                        $data['SeparatePdf'] = true;
                    } else {
                        $data['SeparatePdf'] = false;
                        $data['Result']['Main'] = pdfConvert($contract_log_id, $getvar->pdfdata, $pdf, 'All', 'No');
                        $data['Result']['Rental'] = "";
                        $data['Result']['Paint'] = "";
                        $data['Result']['CondominiumRider'] = "";
                        $data['Result']['HomeownersAssociation'] = "";
                        $data['Result']['SellerFinancing'] = "";
                        $data['Result']['MortgageAssumption'] = "";
                        $data['Result']['FhaVaFinancing'] = "";
                        $data['Result']['AppraisalContingency'] = "";
                        $data['Result']['ShortSale'] = "";
                        $data['Result']['HomeOwnersFlood'] = "";
                        $data['Result']['InterestBearing'] = "";
                        $data['Result']['AsIsRider'] = "";
                        $data['Result']['RightToInspect'] = "";
                        $data['Result']['DefectiveDrywall'] = "";
                        $data['Result']['CoastalConstructionControl'] = "";
                        $data['Result']['InsulationDisclosure'] = "";
                        $data['Result']['LeadPaintDisclosure'] = "";
                        $data['Result']['HousingOlderPersons'] = "";
                        $data['Result']['Rezoning'] = "";
                        $data['Result']['LeasePurchase'] = "";
                        $data['Result']['PreClosingOccupancy'] = "";
                        $data['Result']['PostClosingOccupancy'] = "";
                        $data['Result']['SaleBuyerProperty'] = "";
                        $data['Result']['BackUpContract'] = "";
                        $data['Result']['KickOutClause'] = "";
                        $data['Result']['SellerAttorneyApproval'] = "";
                        $data['Result']['BuyerAttorneyApproval'] = "";
                        $data['Result']['AddendumToContract'] = "";
                        $data['Result']['ShortSaleAddendum'] = "";
                        $data['Result']['LicenseeDisclosureRider'] = "";
                        $data['Result']['BindingArbitration'] = "";
                        $data['Result']['SpecialTaxingDistrict'] = "";
                        $data['Result']['ModifyListingAgreement'] = "";
                        $data['Result']['LBPListing'] = "";
                        $data['Result']['CovidInPerson'] = "";
                        $data['Result']['ShortSaleERSA'] = "";
                        $data['Result']['CovidExtensionTime'] = "";
                        $data['Result']['SectionExchange'] = "";
                        $data['Result']['RentalDisbursment'] = "";
                    }

                    //print_r($pdfdata);
                    $data['allOwnerName'] = $getvar->esginAllOwnerName;
                    $data['allOwnerEmail'] = $getvar->esginAllOwnerEmail;
                    $data['allTenantName'] = $getvar->esginAllTenantName;
                    $data['allTenantEmail'] = $getvar->esginAllTenantEmail;
                    $data['PropertyType'] = $getvar->pdfdata['PropertyType'];
                    $data['isLogin'] = true;
                    $data['success'] = true;
                } else {
                    $data = array('success' => false, "isLogin" => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, "isLogin" => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, "isLogin" => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    private function addContractDataInLead($email, $phone, $agent_id, $data, $leadId)
    {

        // $lead_query = "SELECT * from Leads where email ='$email' ";
        // $lead_result  = $this->db->query( $lead_query );
        $lead_result = $this->LeadsModel::where('Email', $email)->first();
        $lead_result2 = $this->LeadsModel::where('Phone', $phone)->orWhere('UnformattedPhone', $phone)->first();
        // $lead_query2 = "SELECT * from Leads where phone ='$phone' OR unformattedphone = '$phone'";
        // $lead_result2  = $this->db->query( $lead_query2 );
        if ((isset($phone) && $phone != '' && $lead_result2) || (isset($email) && $email != '' && $lead_result)) {
            return $leadId;
        } else {
            $insert_id = $this->LeadsModel::Create($data)->_id;
            // $this->db->insert('Leads',$data);
            // $insert_id = $this->db->insert_id();
            // $this->load->helper('common_helper');
            auto_lead_assignmentcall($insert_id, $agent_id, 1, 0, 0);
            return  $insert_id;
        }
    }
    // Add Lead Api
    public function AddLeadApi(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $data = array();
                    // return $token;
                    $apikey = $request->apikey;
                    // $tknval = $request->tknvalueget;
                    // $tokenresult = $this->check_token($tknval);
                    $loginagentid = $request->loginagentid;

                    $PropType       = $request->PropertyType_Al;
                    $LeadType             = $request->ContactType_Al;

                    $contact_name       = $request->contact_name;
                    $mls_id             = $request->mls_id;
                    $phone              = $request->phone;
                    $county       = $request->county;
                    $email              = $request->email;
                    $total_beds         = $request->total_beds;
                    $Baths_Al           = $request->Baths_Al;
                    $address            = $request->address;
                    $onlyaddress_Al            = $request->onlyaddress_Al;
                    $city               = $request->city;
                    $state              = $request->state;
                    $zipcode            = $request->zipcode;
                    $price              = $request->price;
                    $message            = $request->message;
                    $unit               = $request->unit;
                    $showing_request    = $request->showing_request;
                    $offers             = $request->offers;
                    $move_in_date       = $request->move_in_date;
                    $credit_score       = $request->credit_score;
                    $income             = $request->income;
                    $job_title          = $request->job_title;
                    $employer           = $request->employer;
                    $employed_since     = $request->employed_since;
                    $past_jobs          = $request->past_jobs;
                    $reason_for_moving  = $request->reason_for_moving;
                    $additional_properties  = $request->additional_properties;
                    $pets           = $request->pets;
                    $lease_length           = $request->lease_length;
                    $parking                = $request->parking;
                    $desired_neighbourhood  = $request->desired_neighbourhood;
                    $furnished_info         = $request->furnished_info;
                    $haverealtor            = $request->haverealtor;

                    $phone_nos = str_replace(' ', '', $phone);
                    $phone_nos = str_replace('(', '', $phone_nos);
                    $phone_nos = str_replace(')', '', $phone_nos);
                    $phone_nos = str_replace('-', '', $phone_nos);
                    $phone_nos = ltrim(trim($phone_nos), '1');
                    $phone_nos = trim($phone_nos);

                    $user_data = array();
                    $user_data['source']              = "from header";
                    $user_data['LeadType']              = $LeadType;
                    $user_data['PropType']              = $PropType;
                    $user_data['ContactName']          = trim($contact_name);
                    $user_data['mls_id']                = $mls_id;
                    $user_data['Phone']                 = trim($phone);
                    $user_data['County']          = trim($county);
                    $user_data['FormattedPhone']        = trim($phone);
                    $user_data['UnFormattedphone']      = trim($phone_nos);
                    $user_data['Email']                 = trim($email);
                    $user_data['TotalBeds']            = $total_beds;
                    $user_data['Beds']            =       $total_beds;
                    $user_data['Baths']                 = $Baths_Al;
                    $user_data['Address']               = $onlyaddress_Al;
                    $user_data['city']                  = $city;
                    $user_data['State']                 = $state;
                    $user_data['ZipCode']               = $zipcode;
                    $user_data['Price']                 = $price;
                    $user_data['Unit']                  = $unit;
                    $user_data['Message']               = $message;
                    $user_data['ShowingRequest']       = $showing_request;
                    $user_data['Offers']                = $offers;
                    $user_data['MoveInDate']          = $move_in_date;
                    $user_data['CreditScore']          = $credit_score;
                    $user_data['Income']                = $income;
                    $user_data['JobTitle']             = $job_title;
                    $user_data['Employer']              = $employer;
                    $user_data['EmployedSince']        = $employed_since;
                    $user_data['PastJobs']             = $past_jobs;
                    $user_data['ReasonForMoving']     = $reason_for_moving;
                    $user_data['Pets']                  = $pets;
                    $user_data['LeaseLength']          = $lease_length;
                    $user_data['Parking']               = $parking;
                    $user_data['AdditionalProperties'] = $additional_properties;
                    $user_data['DesiredNeighbourhood'] = $desired_neighbourhood;
                    $user_data['FurnishedInfo']        = $furnished_info;
                    $user_data['HaveRealtor']           = $haverealtor;
                    // $user_data['ip_address']            = $request->ip_address();
                    $user_data['created_at']            = date('Y-m-d H:i:s');
                    $user_data['updated_at']            = date('Y-m-d H:i:s');
                    $user_data['Status'] = 'Captured';

                    $user_id     = $this->LeadsModel::create($user_data)->_id;
                    // $user_id    = $this->db->insert_id() ;
                    if ($user_id) {
                        $data['msg'] = "Lead Added Successfully";
                        $data['id'] = $user_id;
                        $data['success'] = true;
                        $data['isLogin'] = true;
                        // $this->load->helper('common_helper');
                        // auto_lead_assignmentcall($user_id,$loginagentid,1,0,0);
                    } else {
                        $data['msg'] = "Some error occurred ,Please try again later.";
                        $data['success'] = false;
                    }
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    public function BugsReportPost(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    if ($request->file('ReportDocument') != null) {
                        $imageName = time() . '.' . $request->ReportDocument->extension();
                        $Report = $request->Report;
                        $data['Report'] = $request->Report;
                        $extension = $request->ReportDocument->extension();
                        //                        $name = $request->file('ReportDocument')->getClientOriginalName();
                        $url = $request->ReportDocument->storeAs('/public/img', $imageName);
                        $data['ReportDocument'] = storage_path($url);
                        //                        return $data;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        BugsReport::create($data);
                        if ($data['Report'] != "") {
                            $email = "msuman1610@gmail.com";
                            $subject = "Bugs Report";
                            $message = "<p>$Report</p>";
                            $attach = $data['ReportDocument'];
                            //                            $this->sendBugReportEmail($email,$subject,$message,$attach);
                            $data['msg'] = "Report Added Successfully";
                            $data['success'] = true;
                        }
                        //                        return $file;
                    }
                    $data['isLogin'] = true;
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    public function saveSuggestionReportpost(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    if ($request->file('SuggestionDocument') != null) {
                        $imageName = time() . '.' . $request->SuggestionDocument->extension();
                        //                        $data['Report'] = $request->Report;
                        $extension = $request->SuggestionDocument->extension();
                        //                        $name = $request->file('ReportDocument')->getClientOriginalName();
                        $url = $request->SuggestionDocument->storeAs('/public/img', $imageName);
                        $Suggestion = $request->Suggestion;
                        $data['Suggestion'] = $request->Suggestion;
                        $data['SuggestionDocument'] = storage_path($url);
                        //                        return $data;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        SuggestionlistModel::create($data);
                        if ($data['Suggestion'] != "") {
                            $email = "msuman1610@gmail.com";
                            $subject = "Bugs Report";
                            $message = "<p>$Suggestion</p>";
                            $attach = $data['SuggestionDocument'];
                            //                            $this->sendBugReportEmail($email,$subject,$message,$attach);
                            $data['msg'] = "Suggestion Added Successfully";
                            $data['success'] = true;
                        }
                    }
                    $data['isLogin'] = true;
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    //    Delete Lead Api
    public function DeleteleadsPost(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $logintype  = (isset($token->logintype) && $token->logintype != '') ? $token->logintype : 'agent';
                    //  echo "logintype".$logintype ;
                    $BulkActionStatus = isset($request->BulkActionStatus) ? $request->BulkActionStatus : '';
                    $BulkLeadIds = isset($request->BulkLeadIds) ? $request->BulkLeadIds : '';
                    $BulkLeadIds = rtrim($BulkLeadIds, ',');
                    $leadids = "";
                    if ($BulkLeadIds != "" && !empty($BulkLeadIds) && $BulkLeadIds != null && $BulkActionStatus == 'true') {
                        $leadids = explode(",", $BulkLeadIds);
                    }
                    //                    return $leadids;
                    // print_r( $leadids);
                    if ($logintype == 'admin') {
                        if ($leadids != "" && !empty($leadids) && $leadids != null) {
                            foreach ($leadids as $leadsids) {
                                $lead_id = $leadsids;
                                if ($lead_id > 0) {
                                    $select = $this->LeadsModel::where('id', $lead_id)->first();
                                    if ($select) {
                                        //                                        $query=$this->LeadsModel::where('id',$lead_id)->delete();
                                    }
                                    //                                    }
                                }
                            }
                        }
                        $data = array('success' => true, 'isLogin' => true, 'msg' => 'Leads Deleted Successfully');
                    } else {
                        $data = array('success' => false, 'isLogin' => true, 'msg' => 'Only Admin is allowed.');
                    }
                    //                    $data['isLogin'] = true;
                    //                    $data['success'] = true;
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    public function AddNotes(Request $request)
    {
        $id = 0;
        $form_data['AgentId'] = $request->AgentId;
        $form_data['LeadId'] = $request->LeadId;
        $form_data['Type'] = $request->type;
        $form_data['Notes'] = $request->Notes;
        $unit_id = LeadNotesModel::updateOrCreate(['id' => $id], $form_data);
        $message = $request->type . " Feed Added Successfully";
        if ($unit_id) {
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
    public function UpdateLeadAgent(Request $request)
    {
        $id = 0;
        //        return $request->all();
        if (isset($request->id) && !empty($request->id)) {
            $id = $request->id;
        }
        if (isset($request->FirstName) && !empty($request->FirstName)) {
            $form_data['ContactName'] = $request->FirstName . ' ' . $request->LastName;
        }
        if (isset($request->Email) && !empty($request->Email)) {
            $form_data['Email'] = $request->Email;
        }
        if (isset($request->Phone) && !empty($request->Phone)) {
            $form_data['Phone'] = $request->Phone;
        }
        if (isset($request->AssgnAgentOffice) && !empty($request->AssgnAgentOffice)) {
            $form_data['AssgnAgentOffice'] = $request->AssgnAgentOffice;
        }
        if (isset($request->City) && !empty($request->City)) {
            $form_data['City'] = $request->City;
        }
        if (isset($request->Status) && !empty($request->Status)) {
            $form_data['Status'] = $request->Status;
        }
        $unit_id = LeadsModel::where('id', $id)->update($form_data);
        //        return $unit_id;
        $message = " Updates Successfully!";
        if ($unit_id) {
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
    public function ChangeAgent(Request $request)
    {
        if (isset($request->LeadId) && !empty($request->LeadId)) {
            $id = $request->LeadId;
        }
        if (isset($request->AssignedAgent) && !empty($request->AssignedAgent)) {
            $form_data['AssignedAgent'] = $request->AssignedAgent;
            $agent = [];
            if (isset($agent) && !empty($agent)) {
                $form_data['AssignedAgentName'] = $agent->ListAgentFullName;
                $form_data['AssgnAgentEmail'] = $agent->ListAgentEmail;
                $form_data['AssgnAgentPhone'] = $agent->ListAgentDirectPhone;
                $form_data['AssgnAgentOffice'] = $agent->ListOfficeName;
            }
        } else {
            return response()->json([
                'error' => true,
                'data' => $form_data,
                'message' => 'Please select Agent',
            ]);
        }
        $unit_id = LeadsModel::where('id', $id)->update($form_data);
        //        return $unit_id;
        $message = " Updates Successfully!";
        if ($unit_id) {
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
    public function AddMail(Request $request)
    {
        $id = 0;
        if (isset($request->userId) && !empty($request->userId)) {
            $form_data = $request->all();
        }
        $unit_id = AlertsLog::updateOrCreate(['id' => $id], $form_data);
        $sendEmail = sendEmail("SMTP", env('MAIL_FROM'), $request->Email, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $request->Subject, $request->Message, "From leads","","");
        //        return $unit_id;
        $message = " Created Successfully!";
        if ($unit_id) {
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
    public function GetEmailTemp(Request $request)
    {
        $id = $request->id;
        $email = TemplatesModel::where('id', $id)->first();
        if (isset($email->content) && !empty($email->content)) {
            return $email->content;
        } else {
            return " ";
        }
    }
    public function propertyDetails(Request $request)
    {

        $draw = $request->get('draw');
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        $qts = "'";
        // where("UserId", $request->LeadId)->
        $propertyView = UserTracker::where("UserId", $request->LeadId)->where("PageUrl", $request->slug)->orderBy('id', 'desc')->get(); //pluck("UserId");
        $totalRecords = count($propertyView);
        $totalRecordswithFilter = count($propertyView);
        foreach ($propertyView as $record) {
            $filtered = json_decode($record->FilteredData, true);
            $query=\App\Models\RetsPropertyData::query();
            $query->select(PropertyConstants::PROPERTY_STATS_DATA);
            $prop= $query->where('SlugUrl', $filtered['slug'])->with('propertiesImges:s3_image_url,listingID')->first();

            // $prop = getProperties($filtered['slug']);
            $img = $prop->ImageUrl;
            if ($img) {
                $img = $img->s3_image_url;
            } else {
                $img = "/assets/agent/images/no-imag.jpg";
            }
            $data_arr[] = array(
                "Media" =>'<a href="'.$record->PageUrl.'" target="_blank" title="'.$record->PageUrl.'">'. '<img  class=" img-fluid" src="' . $img . '" style="height:80px;"></a>',
                "UnparsedAddress" => $prop->StandardAddress,
                "ListPrice" => formatDollars($prop->ListPrice),
                "Date" => $record->InTime
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        echo json_encode($response);
        //        return $data_arr;
    }
    public function PropertiesViewed(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        // for last 7 days
        // $date = strtotime("-7 day");
        // $lastsevendays = date('Y-m-d H:i:s', $date); ->where('created_at','>=',$lastsevendays)
        //if days is added
        $query = UserTracker::select('PropertyUrl','UserId','PageUrl');
        if (isset($getdata['LeadId'])) {
            $status = $getdata['LeadId'];
            $query = $query->where('UserId', $getdata['LeadId']);
        }
        $propertyView = $query->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL').'propertydetails/' . '%' )->groupby('PageUrl');
        $totalRecords = count($propertyView->get());
        $totalRecordswithFilter = count($propertyView->get());
        $propertyView = $propertyView->skip($start)->take($rowperpage)->get();
        foreach ($propertyView as $p) {
            $trim = ltrim($p->PageUrl,env('HOUSENFRONTURL')."propertydetails/");

            $record = RetsPropertyData::where('SlugUrl',$trim)->first(['ListPrice', 'StandardAddress','ImageUrl']);
            $count = $Propertypage =UserTracker::where('UserId',$p->UserId)->where('PageUrl', 'like', '%' . $p->PageUrl . '%' )->groupBy('PageUrl')->count();
            $purgedproperty = RetsPropertyDataPurged::select('ListingId', 'ImageUrl', 'StandardAddress', 'ListPrice',)->where('SlugUrl', $trim)->first();
            $qts = "'";
            if (isset($record)) {
                if (isset($record->ImageUrl) !='') {
                    $Imageurl = $record->ImageUrl;
                }else{
                    $Imageurl ="/assets/agent/images/no-imag.jpg" ;
                }
                $data_arr[] = array(
                    // 'Media' => '<a href="' . $p->PropertyUrl . '" target="_blank"><img  width="100" src="' . $Imageurl . '"/></a>',
                    "Media" => '<img  class=" img-fluid" src="' . $Imageurl . '" style="height:50px;" onclick="propertyView(' . $qts . $p->PropertyUrl . $qts . ',' . $qts . $p->IpAddress . $qts . ')">',
                    "StandardAddress" => $record->StandardAddress,
                    "ListPrice" => $record->ListPrice,
                    "Total View" => $count
                );
            }else{
                if(isset($purgedproperty)) {
                    if ($purgedproperty->ImageUrl != '') {
                        $Imageurl = $purgedproperty->ImageUrl;
                    } else {
                        $Imageurl = "/assets/agent/images/no-imag.jpg";
                    }
                    $data_arr[] = array(
                        'Media' => '<a href="' . $p->PropertyUrl . '" target="_blank"><img  width="100" src="' . $Imageurl . '"/></a>',
                        'StandardAddress' => $purgedproperty->StandardAddress,
                        'ListPrice' => number_format($purgedproperty->ListPrice),
                        'Total View' => $count,
                    );
                }
            }
            if(empty($record) && empty($purgedproperty)){
                $data_arr[] = array(
                    "Media" => '<a href="' . $p->PropertyUrl . '" target="_blank"><img  class=" img-fluid" src="' . "/assets/agent/images/no-imag.jpg" . '" style="height:50px;"></a>',
                    "StandardAddress" => 'Address not found',
                    "ListPrice" => 'Price not found',
                    "Total View" => 'Count not found',
                );
            }
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        //        return $data_arr;
    }
    public function PageViewed(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        $query = UserTracker::select('count(*) as allcount');
        if (isset($getdata['UserId'])) {
            $status = $getdata['UserId'];
            $query = $query->where('UserId', $getdata['LeadId']);
        }
        $propertyView = UserTracker::where('UserId', $id)->select('PageUrl',DB::raw('COUNT(PageUrl) as count'))
        ->groupBy('PageUrl')
        ->orderBy('count')
        ->get();
        $totalRecords = $propertyView->count();
        $totalRecordswithFilter = $propertyView->count();
        // foreach ($propertyView as &$p) {
            //     $record1 =\App\Models\RetsPropertyData::where("ListingId", $p->LeadId)->first();
            //     $record = RetsPropertyDataImage::where('ListingId', $p->LeadId)->first();
            //     $p["ListPrice"] = $record1->ListPrice;
            //     $p["UnparsedAddress"] = $record1->StandardAddress;
            //     if (isset($record) && !empty($record)) {
                //         $p['Media'] = $record->s3_image_url;
                //     } else {
                    //         $p['Media'] = "";
                    //     }
                    //     //            return $p["Property"];
                    // }
        foreach ($propertyView as $record) {
                $address =  UserTracker::where('UserId', $id)->where('PageUrl',$record->PageUrl)->select('IpAddress',DB::raw('COUNT(IpAddress) as count'))
                ->groupBy('IpAddress')
                ->get('IpAddress');
                $time =  UserTracker::where('UserId', $id)->where('PageUrl',$record->PageUrl)->select('created_at')
                ->get('created_at');
                foreach ($time as $key => $v) {
                    # code...
                }
                foreach ($address as $key => $value) {
                    # code...
                }
            $data_arr[] = array(
                "PageUrl" => $record->PageUrl,
                "IpAddress" => $value->IpAddress,
                "created_at" => date('F j, Y, g:i a', strtotime($v->created_at)),
                "count" =>$record->count,
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        echo json_encode($response);
        //        return $data_arr;
    }
    public function LoginDetail(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        $data["lead"] = LeadsModel::where('id', $getdata['LeadId'])->first();
        $query = LoginDetails::select('count(*) as allcount');
        if (isset($getdata['LeadId'])) {
            $status = $getdata['LeadId'];
            $query = $query->where('AgentId', $id);
        }
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $propertyView = LoginDetails::where('AgentId', $id)->get();
        $i = 1;
        foreach ($propertyView as $record) {
            $data_arr[] = array(
                "Sr" => $i,
                "IpAddress" => $record->IpAddress,
                "created_at" => date('F j, Y, g:i a', strtotime($record->created_at))
            );
            $i++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        //        return $data_arr;
    }
    public function FavPropperty(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        $query = FavouriteProperties::select('count(*) as allcount');
        if (isset($getdata['LeadId'])) {
            $status = $getdata['LeadId'];
            $query = $query->where('LeadId', $getdata['LeadId']);
        }
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $propertyView = FavouriteProperties::where('LeadId', $id)->get();
        foreach ($propertyView as &$p) {
            $record1 = $this->retsPropertyData::where("ListingId", $p->ListingId)->first(['ListPrice', 'StandardAddress']);
            if($record1)
            {
                $record = RetsPropertyData::where('ListingId', $p->ListingId)->first();
                $p["ListPrice"] = $record1->ListPrice;
                $p["UnparsedAddress"] = $record1->StandardAddress;
                if (isset($record) && !empty($record)) {
                    $p['Media'] = $record->ImageUrl;
                } else {
                    $p['Media'] = "";
                }
                //            return $p["Property"];
            }
           if (isset($p["UnparsedAddress"])) {
            $p["UnparsedAddress"] = $record1->StandardAddress;
            }
            if (isset($record) && !empty($record)) {
                $p['Media'] = $record->s3_image_url;
            } else {
                $p['Media'] = "";
            }
            //            return $p["Property"];
        }
        foreach ($propertyView as $record) {
            $data_arr[] = array(
                "Media" => '<img class=" img-fluid" src="' . $record->Media . '" style="height:50px;">',
                "UnparsedAddress" => $record->UnparsedAddress,
                "ListPrice" => $record->ListPrice
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );


        echo json_encode($response);
        //        return $data_arr;
    }
    public function LeadsAll(){
        $newTime = strtotime('-1 minutes');
        $currentdate = date('Y-m-d H:i:s', $newTime);
        $data['Onlineusers'] = UserTracker::distinct('IpAddress')->where('StayTime','>',$currentdate)->count();
        $data['Onlineleads'] = UserTracker::distinct('UserId')->where('UserId','!=',null)->where('StayTime','>',$currentdate)->count();
        $data['allleads'] = LeadsModel::count();
        $data['captured_leads'] = $this->LeadsModel::where('Status','Captured')->count();
        $data['registered_leads'] = $this->LeadsModel::where('Status','')->count();
        $total= $data['captured_leads'] + $data['registered_leads'];
        $data['bar_captured'] = intval($data['captured_leads']/$total*100);

        return $data;

    }
    public function allListing(){
        $data['listing'] = RetsPropertyData::distinct('id')->count();
        $data['Soldlisting'] = RetsPropertyDataPurged::distinct('id')->count();
        $total =$data['listing'] + $data['Soldlisting'] ;
        $data['listing_bar'] = intval($data['listing']/$total*100)."%";
        $data['Soldlisting_bar'] = intval($data['Soldlisting']/$total*100)."%";

        return $data;

    }
}
