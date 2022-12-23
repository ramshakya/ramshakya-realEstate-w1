<?php

namespace App\Http\Controllers\agent;

use App\Constants\PropertyConstants;
use App\Http\Controllers\Controller;
use App\Models\PropertiesCronLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\agent\AssignmentModel;
use App\Models\SqlModel\lead\LeadsModel;


use App\Models\SqlModel\RetsPropertyDataSql;
use App\Models\SqlModel\EmailLogs;
use App\Models\SqlModel\Schedules;

use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\Country;
use App\Models\SqlModel\Notifications;

use App\Models\SqlModel\RefMasterData;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\SqlModel\Websetting;
use App\Models\UserTracker;
use Illuminate\Support\Facades\DB;
use App\Models\SqlModel\RetsPropertyDataPurged;
use App\Models\Enquiries;

class MainAgentController extends Controller
{
    public $LeadsModel;

    public $AssignmentModel;
    public $PropertyData;

    public function __construct()
    {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql") {
            $this->LeadsModel = new LeadsModel();

            $this->AssignmentModel = new AssignmentModel();
            $this->PropertyData = new RetsPropertyData();
        } else {
            /*$this->LeadsModel = new \App\Models\MongoModel\LeadsModel();

            $this->AssignmentModel = new \App\Models\MongoModel\BrokerAgents();
            $this->PropertyData = new \App\Models\MongoModel\RetsPropertyData();*/
        }
    }

    //
    public function index()
    {
        if (!is_null(Auth::user())) {
            return redirect('agent/dashboard');
        } else {
            return view('agent.login');
        }
    }

    public function dashboard()
    {
        $data["pageTitle"] = "Agent Dashboard";
        $data['read'] = EmailLogs::where('IsRead',1)->count();
        $data['sent'] = EmailLogs::where('IsSent',1)->count();

        $data['campaignssent'] = EmailLogs::where('IsSent',1)->where('FromId', '1')->count();
        $data['campaignsread'] = EmailLogs::where('IsRead',1)->where('FromId', '1' )->count();
        $data['signupsent'] = EmailLogs::where('IsSent',1)->where('FromId', '2' )->count();
        $data['signupread'] = EmailLogs::where('IsRead',1)->where('FromId', '2' )->count();
        $data['enquirysent'] = EmailLogs::where('IsSent',1)->where('FromId', '3')->count();
        $data['enquiryread'] = EmailLogs::where('IsRead',1)->where('FromId', '3')->count();

        return view('agent.dashboard', $data);
    }
    public function AllNotifications(){
        return view('agent.leads.notifications');

    }

    public function getlistingGraphData(Request $request)
    {
        $type = $request['type'];
        if ($type=='updated_time') {
            $type = 'updated_time';
        }else {
            $type = 'inserted_time';
        }
        $days = $request['days'];
        // return $request;
        //        $data['mls'] = collect(config('mls_config.mls'))->all();
        //        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        //        $date = \Carbon\Carbon::today()->subDays($days);
        $prop = RetsPropertyData::orderBy($type, 'DESC')->first([$type]);
        $udate = $prop[$type];
        //        return $udate;
        $date = date($udate, strtotime("-30 days"));
        $date = date('Y-m-d H:i:s', strtotime($udate . ' -30 days'));
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff = $now - $your_date;
        $days = round($datediff / (60 * 60 * 24));
        //            return $days;
        // return $request;
        $data['mls'] = collect(config('mls_config.mls'))->all();
        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        $date = \Carbon\Carbon::today()->subDays($days);
        $data['chart_day'] = RetsPropertyData::selectRaw('count(id) as total, Date(' . $type . ') as udate')->where($type, '>=', $date)->groupBy('udate')->orderBy('udate', 'ASC')->get();
        $final = [];
        $date = [];
        foreach ($data['chart_day'] as $val) {
            $val['mls_no'] = 1;
            $dir = array('day' => '', 'mls1' => 0, 'mls2' => 0, 'mls3' => 0, 'mls4' => 0);
            if (in_array($val->udate, $date)) {
                $arr = array_search($val->udate, $date);
                if ($val->mls_no == 1) {
                    $dir['day'] = $val->udate;
                    $dir['mls1'] = $val->total;
                    $final[$arr]['mls1'] = $val->total;
                }
                if ($val->mls_no == 2) {
                    $dir['day'] = $val->udate;
                    $dir['mls2'] = $val->total;
                    $final[$arr]['mls2'] = $val->total;
                }
                if ($val->mls_no == 3) {
                    $dir['day'] = $val->udate;
                    $dir['mls3'] = $val->total;
                    $final[$arr]['mls3'] = $val->total;
                }
                if ($val->mls_no == 4) {
                    $dir['day'] = $val->udate;
                    $dir['mls4'] = $val->total;
                    $final[$arr]['mls4'] = $val->total;
                }
            } else {
                if ($val->mls_no == 1) {
                    $dir['day'] = $val->udate;
                    $dir['mls1'] = $val->total;
                }
                if ($val->mls_no == 2) {
                    $dir['day'] = $val->udate;
                    $dir['mls2'] = $val->total;
                }
                if ($val->mls_no == 3) {
                    $dir['day'] = $val->udate;
                    $dir['mls3'] = $val->total;
                }
                if ($val->mls_no == 4) {
                    $dir['day'] = $val->udate;
                    $dir['mls4'] = $val->total;
                }
                $date[] = $val->udate;
                $final[] = $dir;
            }
        }
        $data['final'] = json_encode($final);

        $mlsName = [];
        foreach ($data['mls'] as $mls) {
            $mlsName[] = $mls['mls'];
        }
        $data['mls'] = $mlsName;
        return $data;
    }

    public function getPergeGraphData(Request $request)
    {
        $type = $request['type'];
        $days = $request['days'];
        // return $request;
        $prop = PropertiesCronLog::orderBy($type, 'DESC')->first([$type]);
        $udate = $prop[$type];
        //        return $udate;
        $date = date($udate, strtotime("-30 days"));
        $date = date('Y-m-d H:i:s', strtotime($udate . ' -30 days'));
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff = $now - $your_date;
        $days = round($datediff / (60 * 60 * 24));
        //            return $days;
        // return $request;
        $data['mls'] = collect(config('mls_config.mls'))->all();
        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        $date = \Carbon\Carbon::today()->subDays($days);

        $data['chart_day'] = PropertiesCronLog::selectRaw('count(id) as total, Date(' . $type . ') as udate,mls_no')->where($type, '>=', $date)->groupBy('mls_no')->groupBy('udate')->orderBy('udate', 'ASC')->get();
        $final = [];
        $date = [];
        foreach ($data['chart_day'] as $val) {
            $dir = array('day' => '', 'mls1' => 0, 'mls2' => 0, 'mls3' => 0, 'mls4' => 0);
            if (in_array($val->udate, $date)) {
                $arr = array_search($val->udate, $date);
                if ($val->mls_no == 1) {
                    $dir['day'] = $val->udate;
                    $dir['mls1'] = $val->total;
                    $final[$arr]['mls1'] = $val->total;
                }
                if ($val->mls_no == 2) {
                    $dir['day'] = $val->udate;
                    $dir['mls2'] = $val->total;
                    $final[$arr]['mls2'] = $val->total;
                }
                if ($val->mls_no == 3) {
                    $dir['day'] = $val->udate;
                    $dir['mls3'] = $val->total;
                    $final[$arr]['mls3'] = $val->total;
                }
                if ($val->mls_no == 4) {
                    $dir['day'] = $val->udate;
                    $dir['mls4'] = $val->total;
                    $final[$arr]['mls4'] = $val->total;
                }
            } else {
                if ($val->mls_no == 1) {
                    $dir['day'] = $val->udate;
                    $dir['mls1'] = $val->total;
                }
                if ($val->mls_no == 2) {
                    $dir['day'] = $val->udate;
                    $dir['mls2'] = $val->total;
                }
                if ($val->mls_no == 3) {
                    $dir['day'] = $val->udate;
                    $dir['mls3'] = $val->total;
                }
                if ($val->mls_no == 4) {
                    $dir['day'] = $val->udate;
                    $dir['mls4'] = $val->total;
                }
                $date[] = $val->udate;
                $final[] = $dir;
            }
        }
        $data['final'] = json_encode($final);

        $mlsName = [];
        foreach ($data['mls'] as $mls) {
            $mlsName[] = $mls['mls'];
        }
        $data['mls'] = $mlsName;
        return $data;
    }
    public function UpdateAccount()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | My Account";
        $id = auth()->user()->id;
        $data["genders"] = RefMasterData::where("type_id", self::TYPE_ID_FOR_GENDER)->get();
        $data['staff'] = User::where('id', $id)->first();
        $data["countries"] = Country::all();
        $data['userurl'] = 'agent';
        $data['usertype'] = 'agent';
        return view('agent.setting.MyAccount', $data);
    }
    public function Setting()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Website Setting";
        //        $id=auth()->user()->id;
        //        $data['AdminId']=auth()->user()->id;
        //        $data['id']=0;
        //        $user = Websetting::where('AdminId',$id)->first();
        //        $data['user']=$user;
        $id = auth()->user()->id;

        $data['id'] = 0;
        if (auth()->user()->person_id == '3') {
            $data['AdminId'] = auth()->user()->AdminId;
            //            $user = Websetting::where('AdminId',auth()->user()->AdminId)->first();
        } else {
            $data['AdminId'] = auth()->user()->id;
            //            $user = Websetting::where('AdminId',$id)->first();
        }
        $user = Websetting();
        $data['user'] = $user;
        if (isset($user->id) && !empty($user)) {
            $data['id'] = $user->id;
        }
        return view('agent.setting.setting', $data);
    }
    public function UpdSetting(Request $request)
    {
        $form_data = $request->all();
        $id = $form_data['userId'];
        $upd['AdminId'] = $form_data['AdminId'];
        $upd['WebsiteName'] = $form_data['WebsiteName'];
        $upd['WebsiteTitle'] = $form_data['WebsiteTitle'];
        $upd['PhoneNo'] = $form_data['PhoneNo'];
        $upd['WebsiteEmail'] = $form_data['WebsiteEmail'];
        $upd['FromEmail'] = $form_data['FromEmail'];
        $upd['EmailPassword'] = $form_data['EmailPassword'];
        $upd['GoogleAnalyticsCode'] = $form_data['GoogleAnalyticsCode'];
        $upd['FacebookPixelCode'] = $form_data['FacebookPixelCode'];
        $upd['MapApiKey'] = $form_data['MapApiKey'];
        $upd['FrontSiteTheme'] = $form_data['FrontSiteTheme'];
        $upd['WebsiteAddress'] = $form_data['WebsiteAddress'];
        $upd['LogoAltTag'] = $form_data['LogoAltTag'];
        $upd['WebsiteColor'] = $form_data['WebsiteColor'];
        $upd['WebsiteMapColor'] = $form_data['WebsiteMapColor'];
        $upd['GoogleMapApiKey'] = $form_data['GoogleMapApiKey'];
        $upd['HoodQApiKey'] = $form_data['HoodQApiKey'];
        $upd['WalkScoreApiKey'] = $form_data['WalkScoreApiKey'];
        $upd['FavIconAltTag'] = $form_data['FavIconAltTag'];
        $upd['FacebookUrl'] = $form_data['FacebookUrl'];
        $upd['TwitterUrl'] = $form_data['TwitterUrl'];
        $upd['LinkedinUrl'] = $form_data['LinkedinUrl'];
        $upd['InstagramUrl'] = $form_data['InstagramUrl'];
        $upd['YoutubeUrl'] = $form_data['YoutubeUrl'];
        $upd['ScriptTag'] = $form_data['ScriptTag'];
        $upd['bodyscriptTag'] = $form_data['bodyscriptTag'];
        
        $upd['ZapierSID'] = $form_data['ZapierSID'];
        $upd['ZapierToken'] = $form_data['ZapierToken'];
        $upd['WebhookUrl'] = $form_data['WebhookUrl'];
        $upd['TwilioToken'] = $form_data['TwilioToken'];
        $upd['TwilioNumber'] = $form_data['TwilioNumber'];
        $upd['TwilioSID'] = $form_data['TwilioSID'];

        $upd['YelpKey'] = $form_data['YelpKey'];
        $upd['YelpClientId'] = $form_data['YelpClientId'];

        $upd['FbAppId'] = $form_data['FbAppId'];
        $upd['GoogleClientId'] = $form_data['GoogleClientId'];
        $upd['OfficeName'] = $form_data['OfficeName'];

        if ($request->hasfile('UploadLogo')) {
            $file = $request->file('UploadLogo');
            //            $name = $file->getClientOriginalName();
            $name = time() . '.' . $request->UploadLogo->extension();
            $path = $request->file('UploadLogo')->storeAs('public/img',$name);
            $imgDat = url('storage/' . $name);
            $upd['UploadLogo'] = $imgDat;
        }
        if ($request->hasfile('DarkLogo')) {
            $file = $request->file('DarkLogo');
            //            $name = $file->getClientOriginalName();
            $name = time() . '.' . $request->DarkLogo->extension();
            $path = $request->file('DarkLogo')->storeAs('public/img',$name);
            $imgDat = url('storage/' . $name);
            $upd['DarkLogo'] = $imgDat;
        }
        if ($request->hasfile('Favicon')) {
            $file = $request->file('Favicon');
            $name = time() . '.' . $request->Favicon->extension();
            //            $name = $file->getClientOriginalName();
            $path = $request->file('Favicon')->storeAs('public/img',$name);
            $imgDat = url('storage/' . $name);
            $upd['Favicon'] = $imgDat;
        }
        if ($request->hasfile('TopBanner')) {
            $file = $request->file('TopBanner');
            $name = time() . '.' . $request->TopBanner->extension();
            //            $name = $file->getClientOriginalName();
            $path = $request->file('TopBanner')->storeAs('public/img',$name);
            $imgDat = url('storage/' . $name);
            $upd['TopBanner'] = $imgDat;
        }
        $unit_id = Websetting::updateOrCreate(['id' => $id], $upd);
        $message = 'Setting Information updated successfully !';
        return response()->json([
            'success' => true,
            'data' => $upd,
            'message' => $message,
        ]);
        //        $upd['Favicon']=$form_data['Favicon'];
    }
    public function ChangePass(Request $request)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Change Password";
        $id = auth()->user()->id;
        $data['id'] = $id;
        $data['user'] = User::where('id', $id)->first();
        $data['users'] = Auth::user();
        //        return $data;
        return view('agent.setting.ChangePass', $data);
    }
    public function changePassword(Request $request)
    {
        //        return $request->all();
        $validator = Validator::make($request->all(), [
            'OldPass' => 'required',
            'NewPass' => 'required|string|min:8',
            'ConfirmPass' => 'required',
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        $form_data = $request->all();
        $id = $form_data['id'];
        if ($request->NewPass != $request->ConfirmPass) {
            return response()->json([
                'error' => true,
                'data' => $form_data,
                'message' => 'Confirm passwords does not match!',
            ]);
        }

        $user = User::where('id', $id)->first();
        if (!Hash::check($request->OldPass, $user->password)) {
            return response()->json([
                'error' => true,
                'data' => $form_data,
                'message' => 'Current password does not match!',
            ]);
            //            return back()->with('error', 'Current password does not match!');
        }

        $user->password = Hash::make($request->NewPass);
        $user->save();
        return response()->json([
            'success' => true,
            'data' => $form_data,
            'message' => 'Password successfully changed!',
        ]);
        return back()->with('success', 'Password successfully changed!');
    }

    public function trackUsers(Request $request)
    {
        $query = UserTracker::query();
        $query->orderByDesc('id');
        $query->groupBy('IpAddress');
        $query->where("AgentId", Auth::user()->id);
        $trackUsers = $query->get();
        $usertype = 'agent';
        return view("agent.trackusers.index", compact('trackUsers', 'usertype'));
    }
    public function trackUserView(Request $request)
    {
        $query = UserTracker::query();
        $query->orderByDesc('id');
        $query->where("IpAddress", $request->id);
        $trackUsers = $query->get();
        $mostPropertyViews = array();
        $mostPagesOpened = array();
        $mostCitySearch = array();
        $mostSearches = array();
        $mostTypesSearch = array();
        $mostSubTypesSearch = array();
        $propertyViews=array();

        $tempPropertyViews = array();
        $tempPagesOpened = array();
        $tempCitySearch = array();
        $tempSearches = array();
        $tempTypesSearch = array();
        $tempSubTypesSearch = array();

        foreach ($trackUsers as $key => $value) {
            $pageUrl = $value->PageUrl;
            if (in_array($pageUrl, $tempPagesOpened)) {
                foreach ($mostPagesOpened as $key => $page) {
                    unset($mostPagesOpened[$pageUrl]);
                    $text = $page['text'];
                    $count = ++$page['count'];
                    $obj = array(
                        "text" => $text,
                        "count" => $count,
                        "inTime"=>$value->InTime,
                        "stayTime"=>$value->StayTime,
                        "IpAddress"=>$value->IpAddress
                    );
                    $mostPagesOpened[$pageUrl] = $obj;
                }
            } else {
                $tempPagesOpened[] = $pageUrl;
                $obj = array(
                    "text" => $pageUrl,
                    "count" => 1,
                    "inTime"=>$value->InTime,
                    "stayTime"=>$value->StayTime,
                    "IpAddress"=>$value->IpAddress
                );
                $mostPagesOpened[$pageUrl] = $obj;
            }
            if ($value->FilteredData) {
                $filteredData = $value->FilteredData;
                $filteredData = json_decode($filteredData, true);
                if (isset($filteredData['City']) && $filteredData['City'] !== "") {
                    $city = $filteredData['City'];
                    if (in_array($city, $tempCitySearch)) {
                        foreach ($mostCitySearch as $key => $page) {
                            unset($mostCitySearch[$city]);
                            $text = $page['text'];
                            $count = ++$page['count'];
                            $obj = array(
                                "text" => $text,
                                "count" => $count
                            );
                            $mostCitySearch[$city] = $obj;
                        }
                    } else {
                        $tempCitySearch[] = $city;
                        $obj = array(
                            "text" => $city,
                            "count" => 1
                        );
                        $mostCitySearch[$city] = $obj;
                    }
                }
                if (isset($filteredData['text_search']) && $filteredData['text_search'] !== "") {
                    $text_search = $filteredData['text_search'];
                    if (in_array($text_search, $tempSearches)) {
                        foreach ($mostSearches as $key => $page) {
                            unset($mostSearches[$text_search]);
                            $text = $page['text'];
                            $count = ++$page['count'];
                            $obj = array(
                                "text" => $text,
                                "count" => $count
                            );
                            $mostSearches[$text_search] = $obj;
                        }
                    } else {
                        $tempSearches[] = $text_search;
                        $obj = array(
                            "text" => $text_search,
                            "count" => 1
                        );
                        $mostSearches[$text_search] = $obj;
                    }
                    // $mostSearches[] = $filteredData['text_search'];
                }
                if (isset($filteredData['propertyType']) && $filteredData['propertyType'] !== "") {
                    $propertyType = $filteredData['propertyType'];
                    if (in_array($propertyType, $tempTypesSearch)) {
                        foreach ($mostTypesSearch as $key => $page) {
                            unset($mostTypesSearch[$propertyType]);
                            $text = $page['text'];
                            $count = ++$page['count'];
                            $obj = array(
                                "text" => $text,
                                "count" => $count
                            );
                            $mostTypesSearch[$propertyType] = $obj;
                        }
                    } else {
                        $tempTypesSearch[] = $propertyType;
                        $obj = array(
                            "text" => $propertyType,
                            "count" => 1
                        );
                        $mostTypesSearch[$propertyType] = $obj;
                    }
                    // $mostTypesSearch[] = $filteredData['propertyType'];
                }
                if (isset($filteredData['propertySubType']) && $filteredData['propertySubType'] !== "") {

                    $propertySubType = $filteredData['propertySubType'];
                    if (in_array($propertySubType, $tempSubTypesSearch)) {
                        foreach ($mostSubTypesSearch as $key => $page) {
                            unset($mostSubTypesSearch[$propertySubType]);
                            $text = $page['text'];
                            $count = ++$page['count'];
                            $obj = array(
                                "text" => $text,
                                "count" => $count
                            );
                            $mostSubTypesSearch[$propertySubType] = $obj;
                        }
                    } else {
                        $tempSubTypesSearch[] = $propertySubType;
                        $obj = array(
                            "text" => $propertySubType,
                            "count" => 1
                        );
                        $mostSubTypesSearch[$propertySubType] = $obj;
                    }

                    // $mostSubTypesSearch[] = $filteredData['propertySubType'];
                }
                if (isset($filteredData['slug']) && $filteredData['slug'] !== "") {
                    $prop = $this->getProperties($filteredData['slug']);
                    $mls = $prop->Ml_num;

                    if (in_array($mls, $tempPropertyViews)) {
                        foreach ($mostPropertyViews as $key => $page) {
                            unset($mostPropertyViews[$mls]);
                            $text = $page['text'];
                            $count = ++$page['count'];
                            $obj = array(
                                "text" => $text,
                                "count" => $count
                            );
                            $mostPropertyViews[$mls] = $obj;
                        }
                    } else {
                        $propertyViews[]=$prop;
                        $tempPropertyViews[] = $mls;
                        $obj = array(
                            "text" => $mls,
                            "count" => 1
                        );
                        $mostPropertyViews[$mls] = $obj;
                    }
                }
            }
        }
        $data = array("mostCitySearch" => $mostCitySearch, "mostPropertyViews" => $mostPropertyViews, "mostPagesOpened" => $mostPagesOpened, "mostSearches" => $mostSearches, "mostTypesSearch" => $mostTypesSearch, "mostSubTypesSearch" => $mostSubTypesSearch,'propertyViews'=>$propertyViews);
        $usertype = 'agent';
        return view("agent.trackusers.view", compact('trackUsers', 'usertype', 'data'));
    }

    private function getProperties($slug)
    {
        $table = "";
        $metDescString = "";

        if (RetsPropertyDataResi::where('SlugUrl', $slug)->exists()) {
            DB::enableQueryLog();
            $query = RetsPropertyDataResi::query();
            $table = "Residential";
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            $query->where('SlugUrl', $slug);
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            $res->PropertyType = $table;
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            return $res;
        }
        if (empty($res)) {
            if (RetsPropertyDataCondo::where('SlugUrl', $slug)->exists()) {
                $table = "Condo";
                $query = RetsPropertyDataCondo::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                $query->where('SlugUrl', $slug);
                $res = $query->with('propertiesImages')->first();
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                return $res;
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            }
        }
        if (empty($res)) {
            if (RetsPropertyDataComm::where('SlugUrl', $slug)->exists()) {
                $table = "Commercial";
                $query = RetsPropertyDataComm::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                $query->where('SlugUrl', $slug);
                $res = $query->with('propertiesImages')->first();
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                return $res;
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            }
        }
    }
    public function leaddataGraphData(Request $request){
        $form_data =  $request->all();
        $type = $form_data['type'];
        $days = $form_data['days'];
        $date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -'.$days.' days'));
        $query = LeadsModel::where('created_at','>',$date);
        if($type=="registered"){
            $query = $query->where('Status','!=','Captured');
        }
        $query = $query->get('created_at');
        $all_date = [];
        foreach ($query as $key => $value) {
            $all_date[] = date_format(date_create($value->created_at),"Y-m-d");

        }
        $leadCounts =  array_count_values($all_date);
        $final = [];
        $dir = [];
        foreach ($leadCounts as $key => $value) {
            $dir['y'] = $key;
            $dir['a'] = $value;
            $final[] = $dir;
        }
        return json_encode($final);
    }
    public function getNotifications(Request $request){
        
        $form_data =  $request->all();
        $agentId = $form_data['AgentId'];
        if(isset($form_data['dashboard'])){
            $newLeads = Notifications::select('ContactName','createdAt','Message','subject')->where('StatusId',0)->orderBy('createdAt','Desc')->get();
            if (count($newLeads) >0) {
               
                $msg="";
                foreach ($newLeads as $key => $value){
                    $profile = '<div class="inbox-item inbox-lead"><div class="inbox-item-img mr-0 text-center"><i class="fa fa-user" aria-hidden="true"></i></div>';
                    $leadname= '<h5 class="inbox-item-author mt-0 mb-1">'.ucwords(strtolower($value->ContactName)).'</h5>';
                    $leadstatus = '<p class="inbox-item-text mt-2 ml-2">'. ucfirst($value->subject).' </p>';
                    $leadmsg = '<p class="inbox-item-text ml-2"> &#x1F4E7; '.ucfirst(substr($value->Message,0,100)).' </p>';
                    $time =date("Y-m-d H:m:i",strtotime($value->createdAt));
                    $registered_time = '<p class="inbox-item-date mt-2 pt-1 pb-2">'.$time.'</p></div>';

                    $msg.= '<a href="/agent/Notifications">'.$profile.$leadname.$leadstatus.$leadmsg.$registered_time.' </a>';
                    // $msg.= '<p class="leads"><a href="#"><b>'.$value->ContactName.'</b> Registered as new lead </a></p>';
                }
                return $msg;
            }else {
                $newLeads = Notifications::select('ContactName','createdAt','Message','subject')->where('StatusId',1)->orderBy('createdAt','Desc')->take(5)->get();
                $msg="";
                foreach ($newLeads as $key => $value){
                    $profile = '<div class="inbox-item inbox-lead"><div class="inbox-item-img mr-0 text-center"><i class="fa fa-user" aria-hidden="true"></i></div>';
                    $leadname= '<h5 class="inbox-item-author mt-0 mb-1">'.ucwords(strtolower($value->ContactName)).'</h5>';
                    $leadstatus = '<p class="inbox-item-text mt-2 ml-2">'. ucfirst($value->subject).' </p>';
                    $leadmsg = '<p class="inbox-item-text ml-2"> &#x1F4E7; '.substr($value->Message,0,100).' </p>';
                    $time =date("Y-m-d H:m:i",strtotime($value->createdAt));
                    $registered_time = '<p class="inbox-item-date mt-2 pt-1 pb-1">'.$time.'</p></div>';

                    $msg.= '<a href="/agent/Notifications">'.$profile.$leadname.$leadstatus.$leadmsg.$registered_time.' </a>';
                    // $msg.= '<p class="leads"><a href="#"><b>'.$value->ContactName.'</b> Registered as new lead </a></p>';
                }
                return $msg;
            }

        }
        else
        {

        }
    }
    public function Notifications(Request $request){
        $form_data =  $request->all();
        $agentId = $form_data['AgentId'];
        if(isset($form_data['dashboard'])){
            $newLeads = Notifications::select('ContactName','createdAt','Message')->where('StatusId',0)->orderBy('createdAt','Desc')->get();
            $msg="";
            $new=0;
            foreach ($newLeads as $key => $value){
                $profile = '<div class="inbox-item inbox-lead pl-2"><div class="inbox-item-img mr-0 text-center"><i class="fa fa-user" aria-hidden="true"></i></div>';
                $leadname= '<h5 class="inbox-item-author mt-0 mb-1">'.ucfirst(strtolower($value->ContactName)).'</h5>';
                $leadmsg = '<p class="inbox-item-text ml-2">'.ucfirst(strtolower($value->Message)) .' </p>';
                $time =date("Y-m-d H:m:i",strtotime($value->createdAt));
                $registered_time = '<p class="inbox-item-date pt-2 m-1 pr-2">'.$time.'</p></div>';
                $msg.= '<a href="/agent/Notifications">'.$profile.$leadname.$leadmsg.$registered_time.' </a>';
                $new++;
                // $msg.= '<p class="leads"><a href="#"><b>'.$value->ContactName.'</b> Registered as new lead </a></p>';
            }
           if ($new>=10) {
            $data['total'] = '10+';
           }else {
            $data['total'] = $new;
           }
            // $data['total'] = $new;
            $data['msg']=$msg;
           return $data;
        }
        else
        {

        }
    }
    public function refreshOnlineLead(){
        $newTime = strtotime('-1 minutes');
        $currentdate = date('Y-m-d H:i:s', $newTime);
        $data['Onlineusers'] = UserTracker::distinct('IpAddress')->where('StayTime','>',$currentdate)->count();
        $data['Onlineleads'] = UserTracker::distinct('UserId')->where('UserId','!=',null)->where('StayTime','>',$currentdate)->count();

        return response($data,200);

    }
    public function email_logs()
    {
        $data['FromTo'] = EmailLogs::distinct('FromEmail')->orderBy('FromEmail', 'asc')->get(['FromEmail']);
        $data['ToEmail'] = EmailLogs::distinct('ToEmail')->orderBy('ToEmail', 'asc')->get(['ToEmail']);
        // $data['Subject'] = EmailLogs::distinct('Subject')->orderBy('Subject', 'asc')->get(['Subject']);
       
        return view("agent.email.email_logs",$data);
    }
    public function email_data(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $getdata = $request->all();
        $query= EmailLogs::select("FromEmail","ToEmail","Subject","OpenedTime","DeliveredTime","IsRead")->where('IsSent',1);
        if(isset($getdata['FromTo'])){
            $FromTo=$getdata['FromTo'];
            $query=$query->where('FromEmail',$FromTo);
        }
        if(isset($getdata['SentTo'])){
            $SentTo=$getdata['SentTo'];
            $query=$query->where('ToEmail',$SentTo);
        }
        if(isset($getdata['Subject'])){
            $Subject=$getdata['Subject'];
            $query=$query->where('Subject' ,'like', '%' . $Subject . '%');
        }
        if(isset($getdata['Lastdays'])){
            $Lastdays=$getdata['Lastdays'];
            $query=$query->where('created_at','>=',\Carbon\Carbon::now()->subdays($getdata['Lastdays']));
        }
        if (!isset($getdata['Lastdays']) && !isset($getdata['DateTo']) && !isset($getdata['DateFrom'])) {
            $date = strtotime("-7 days");
            $date = date("Y-m-d H:i:s",$date);
            $query=$query->where('created_at','>=',$date);
        }
        if(isset($getdata['DateTo'])){
            $DateTo=$getdata['DateTo'];
            if (isset($getdata['DateFrom'])) {
                $DateFrom=$getdata['DateFrom'];
            }else {
                $DateFrom = date("Y-m-d");
            }
            $query=$query->whereDate('created_at','<=',$DateTo)->whereDate('created_at','>=',$DateFrom);
        }
        if(isset($getdata['EmailType'])){
            $EmailType=$getdata['EmailType'];
            $query=$query->where('FromId',$EmailType);
        }
        //Code to remove where condition after count from Query 822 to 838
        $seencount= $query->where('IsRead',1)->count();
        $bindings = $query->getQuery()->bindings['where'];
        $wheres = $query->getQuery()->wheres;

        $whereKey = false;
        foreach ($wheres as $key => $where) {
            if ($where['column'] == "IsRead") {
                $whereKey = $key;
                break;
            }
        }
        if ($whereKey !== false) {
            unset($bindings[$whereKey]);
            unset($wheres[$whereKey]);
        }
        $query->getQuery()->wheres = $wheres;
        $query->getQuery()->bindings['where'] = $bindings;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        // Total records
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $i = 1;
        if ($columnName == "id") {
            $query = $query->orderBy('created_at','desc');
        }else {
            $query = $query->orderBy($columnName, $columnSortOrder);
        }
        $records = $query
        ->skip($start)
        ->take($rowperpage)
        ->get();

        $data_arr = array();
        $data_array = [];
        $srno = intval($start) + 1;
        foreach($records as $record){
            $data_arr['id'] = $srno;
            $data_arr['FromEmail'] = $record->FromEmail;
            $data_arr['ToEmail'] = $record->ToEmail;
            $data_arr['Subject'] = $record->Subject;
            $data_arr['OpenedTime'] = $record->OpenedTime;
            $data_arr['DeliveredTime'] = $record->DeliveredTime;
            if ($record->IsRead==1) {
                $data_arr['IsRead'] = '<span class="badge badge-success">Seen</span>';
            }else{
                $data_arr['IsRead'] = '<span class="badge badge-danger">Not Seen</span>';

            }
            $srno++;
            $data_array[]=$data_arr;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array,
            "readcount" => $seencount,
        );

        return response($response,200);
    }

    public function AllEnquiries()
    {
        return view("agent.enquiry.enquiries");
    }
    public function enquiries_data(Request $request)
    {
        $draw = $request->draw;
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
        $agentId = $request->get("agentid");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $DateTo = $getdata['dateto'];
        $DateFrom = $getdata['datefrom'];
        $LastDays = $getdata['days'];

        $query= Enquiries::select('name','email','phone','message','propertyaddress','created_at')->where("agent_id", $agentId);

        if (empty($LastDays) && empty($DateFrom) && empty($DateTo)) {
            $date = strtotime("-" . 7 . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where('created_at', '>=', $last);
        }
        if (isset($DateTo)) {
            if (isset($DateTo)) {
                if (isset($DateFrom)) {
                    $DateFrom;
                } else {
                    $DateFrom = date("Y-m-d");
                }
                $query->whereDate('created_at', '>=', $DateTo)->whereDate('created_at', '<=', $DateFrom);
            }
        }
        if (isset($LastDays)) {
            $date = strtotime("-" . $LastDays . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where('created_at', '>=', $last);
        }
        // Total records
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $i = 1;
        // Get records, also we have included search filter as well
        $records = $query
            ->orderBy('created_at','desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();
            $data_arr = array();
            $data_array = [];
            $srno = intval($start) + 1;
        foreach ($records as $record) {
            $data_arr['id'] = $srno;
            $data_arr['name'] = $record->name;
            $data_arr['email'] = $record->email;
            $data_arr['phone'] = $record->phone;
            $data_arr['message'] = $record->message;
            $data_arr['page_from'] = $record->propertyaddress;
            $data_arr['created_at'] = date_format($record->created_at,'Y-m-d H:i:s');
            $srno++;
            $data_array[]=$data_arr;  
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array,
        );

        // echo json_encode($response);
        return response($response, 200);
    }
    public function AllSchedules()
    {
        return view("agent.enquiry.schedules");
    }
    public function schedules_data(Request $request)
    {
        $draw = $request->draw;
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
        $agentId = $request->get("agentid");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $DateTo = $getdata['dateto'];
        $DateFrom = $getdata['datefrom'];
        $LastDays = $getdata['days'];

        $query= Schedules::select('Name','Email','Phone','ScheduleEndTime','Created','StartDate','ScheduleStartTime','Description','Status')->where("AdminId", $agentId);

        if (isset($DateTo)) {
            if (isset($DateTo)) {
                if (isset($DateFrom)) {
                    $DateFrom;
                } else {
                    $DateFrom = date('Y-m-d H:i:s');
                }
                $query->whereDate('Created', '>=', $DateTo)->whereDate('Created', '<=', $DateFrom);
            }
        }
        if (isset($LastDays)) {
            $date = strtotime("-" . $LastDays . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where('Created', '>=', $last);
        }
        if (empty($LastDays) && empty($DateFrom) && empty($DateTo)) {
            $date = strtotime("-" . 7 . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where('Created', '>=', $last);
        }
        // Total records
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $i = 1;
        // Get records, also we have included search filter as well
        $records = $query
            ->orderBy('Created','desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();
            $data_arr = array();
            $data_array = [];
            $srno = intval($start) + 1;
        foreach ($records as $record) {
            $data_arr['id'] = $srno;
            $data_arr['name'] = $record->Name;
            $data_arr['email'] = $record->Email;
            $data_arr['phone'] = $record->Phone;
            $data_arr['message'] = $record->Description;
            $data_arr['startTime'] = $record->ScheduleStartTime;
            $data_arr['endTime'] = $record->ScheduleEndTime;
            $data_arr['startDate'] = $record->StartDate;
            $data_arr['created'] = $record->Created;
            if ($record->Status == 0) {
                $status = '<span class="badge badge-primary">Pending</span>';
            }elseif ($record->Status == 1) {
                $status = '<span class="badge badge-success">Approved</span>';
            }elseif ($record->Status == 2) {
                $status = '<span class="badge badge-danger">Declined</span>';
            }else{
                $status = '<span class="badge badge-info">Rescheduled</span>';
            }
            $data_arr['page_from'] = $status;
            $srno++;
            $data_array[]=$data_arr;  
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array,
        );

        return response($response, 200);
    }
    public function showNotifications(Request $request) {
        $draw = $request->draw;
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
        $agentId = $request->get("agentid");
        $getdata = $request->all();
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value 
        $query= Notifications::select('ContactName','Email','Message','Url','createdAt','subject')->where('AgentId',$agentId);
        if (empty($getdata['LastDays']) && empty($getdata['DateFrom']) && empty($getdata['DateTo']) && empty($getdata['NotificationType'])) {
            $date = strtotime("-7 days");
            $last = date('Y-m-d H:i:s', $date);
            $query = $query->where('createdAt', '>=', $last);           
        }
        if (isset($getdata['LastDays'])) {
            $date = strtotime("-" . $getdata['LastDays'] . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query = $query->where('createdAt', '>=', $last);
        } 
        if (isset($getdata['NotificationType'])) {
            $query = $query->where('subject','like','%'.$getdata['NotificationType'].'%');
        }
        if (isset($getdata['DateTo'])) {
            if (isset($getdata['DateTo'])) {
                if (isset($getdata['DateFrom'])) {
                    $getdata['DateFrom'];
                } else {
                    $getdata['DateFrom'] = date("Y-m-d");
                }
                $query = $query->whereDate('createdAt', '<=', $getdata['DateTo'])->whereDate('createdAt', '>=', $getdata['DateFrom']);
            }
        }

        // Total records
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $i = 1;

        // Get records, also we have included search filter as well
        $records = $query
            ->orderBy('createdAt','desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();
            $data_arr = array();
            $data_array = [];
            $srno = intval($start) + 1;
        foreach ($records as $record) {
            $data_arr['id'] = $srno;
            $data_arr['ContactName'] = $record->ContactName;
            $data_arr['Email'] = $record->Email;
            $data_arr['Message'] = substr($record->Message,0,100);
            $data_arr['Url'] = $record->Url;
            $data_arr['createdAt'] = date_format($record->createdAt,"Y-m-d H:m:i");
            $data_arr['subject'] = $record->subject;
            $srno++;
            $data_array[]=$data_arr;  
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array,
        );

        // echo json_encode($response);
        return response($response, 200);
    }
}
