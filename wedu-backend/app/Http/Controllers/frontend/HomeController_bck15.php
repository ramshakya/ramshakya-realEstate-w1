<?php

namespace App\Http\Controllers\frontend;

use App\Constants\PropertyConstants;
use App\Http\Controllers\agent\BlogController;
use App\Http\Controllers\Controller;
use App\Models\Enquiries;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\BlogModel;
use App\Models\SqlModel\FeaturedListing;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SqlModel\Websetting;
use App\Models\SqlModel\Pages;
use App\Models\SqlModel\ContactEnquiry;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SqlModel\Staff;
use App\Models\SqlModel\BlogCategory;
use App\Models\SqlModel\CityData;
use App\Models\SqlModel\Testimonial;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\RetsPropertyDataPurged;
use App\Models\StatsData;
use DateTime;

class HomeController extends Controller
{
    //
    public function getBlog()
    {
        $data = [];
        $blogs = BlogModel::orderBy('id', 'desc')->limit(3)->get();
        $data["blogData"] = $blogs;
        return response($blogs, 200);
    }
    public function GetBlogsSlugs(Request $request)
    {
        $data = [];
        $form_data = $request->all();
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $agentId = $request->agentId;
            $blogs = BlogModel::select("Url")->where('AdminId', $agentId)->orderBy('id', 'desc')->get();
            $data["blogData"] = $blogs;
            return response($blogs, 200);
        }
    }
    public function saveContactForm(Request $request)
    {
        return response($request->all(), 200);
    }
    public function FeedbackForm(Request $request)
    {
        $data = array(
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "message" => $request->comments,
            "page_from" => $request->page_from,
            "user_id" => $request->user_id,
        );
        $validator = Validator::make($data, [
            //"AgentId" => "required",
            "name" => "required",
            "email" => "required",
            "phone" => "required",
            "message" => "required",
            "page_from" => "required",
            "user_id" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            $response["message"] = "All fields are required !";
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $res = Enquiries::insert($data);
            $data = array(
                "message" => "Submited Successfully !",
                "status" => self::SUCCESS_HTTP_RESPONSE_STATUS
            );
            return response($data, self::SUCCESS_HTTP_RESPONSE_STATUS);
        } else {
            return response("Error occured", self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }

    public function ContactUsForm(Request $request)
    {
        $data = array(
            "name" => $request->user_name,
            "email" => $request->user_email,
            "phone" => $request->user_phone,
            "message" => $request->comments,
            "page_from" => $request->page_from,
            "TimeLine" => $request->timeLine,
            "Queries" => $request->queryValue,
            //"user_id" => $request->user_id,
            "agentId" => $request->agentId,
            "UserLocation" => $request->user_location,
        );

        $validator = Validator::make($data, [
            //"AgentId" => "required",
            "name" => "required",
            "email" => "required",
            "phone" => "required",
            "message" => "required",
            "page_from" => "required",
            "TimeLine" => "required",
            "Queries" => "required",

            "UserLocation" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            $response["message"] = "All fields are required !";
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            unset($data['agentId']);
            $res = Enquiries::insert($data);
            $query1 = User::where('type',$request->agentId)->first();
            if ($query1) {
                $subject = "Wedu.ca Contact us";
                $message = "<p>Contact us<p>";
                foreach ($data as $key => $value) {
                    $message .= '<p>' . $key . ' : ' . $value . '</p>';
                }
                $getSuperAdmin = getSuperAdmin();
                $adminEmail = getAdmin($request->agentId);
                $adminEmail ="ram@peregrine-it.com";
                $sent_token_url = sendEmail("SMTP", $getSuperAdmin, $adminEmail, 'mukesh@peregrine-it.com', env('SUPERBBROKERFROM'), $subject, $message, "HomeController->SubmitHomeValue");
            }
            // send data to zapier
            $arr = array(
                'Subject' => 'New enquiry from contact us page',
                'Name' => $request->user_name,
                'Email' => $request->user_email,
                'Phone' => $request->user_phone,
                'Message' => $request->comments,
                'Page from' => $request->page_from,
                'TimeLine' => $request->timeLine,
                'Queries' => $request->queryValue,
                'User Location' => $request->user_location,
                'Date and time' => date("d/m/Y h:i:sa")
            );
            $zap = ZapierSender($arr);
            // end code
            $data = array(
                "message" => "Submited Successfully !",
                "status" => self::SUCCESS_HTTP_RESPONSE_STATUS
            );
            return response($data, self::SUCCESS_HTTP_RESPONSE_STATUS);
        } else {
            return response("Error occured", self::DB_ERROR_HTTP_RESPONSE_STATUS);
        }
    }
    public function featuredList(Request $request)
    {
        $requestData = $request->all();
        $tmp_data = [];
        $response_data['allData'] = [];
        $response_data['total'] = 0;
        $validator = Validator::make($request->all(), [
            //"AgentId" => "required",
        ]);
        $textSearchField = "";
        $currentPage = $requestData['curr_page'];
        $limit = 12;
        $offset = $currentPage - 1;
        $start = ($offset * $limit);
        if (isset($requestData['sort_by']) && !empty($requestData['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $requestData['sort_by'];
            if ($sortData === 'price_high') {
                $field = 'ListPrice';
                $orderBy = 'Desc';
            } else if ($sortData === 'price_low') {
                $field = "ListPrice";
                $orderBy = 'Asc';
            } else if ($sortData === 'dom_high') {
                $field = 'inserted_time';
                $orderBy = 'Desc';
            } else if ($sortData === 'dom_low') {
                $field = 'inserted_time';
                $orderBy = 'Asc';
            }
            unset($requestData['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'inserted_time';
            $orderBy = 'Desc';
        }
        if (isset($requestData['text_search']) && !empty($requestData['text_search'])) {
            $textSearchField = $requestData['text_search'];
        }

        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                $featuredQuery = FeaturedListing::query();
                $featuredQuery->where("AgentId", $request->AgentId);

                $featuredQuery->offset($start);
                $featuredQuery->limit($limit);
                $data = $featuredQuery->pluck('ListingId')->toArray();
                $query = RetsPropertyData::query();
                $query->select(PropertyConstants::SELECT_DATA);

                if (in_array($textSearchField, $data, true)) {
                    $query->where('ListingId', $textSearchField);
                    $query->orderBy($field, $orderBy);
                    $property = $query->get();
                    $property = collect($property)->all();
                    $property["isOpenHouse"] = 0;
                    $tmp_data[] = $property;
                    $total = 1;
                } else {
                    $query->whereIn('ListingId', $data);
                    if ($textSearchField) {
                        $query->where('City', $textSearchField);
                    }
                    $query->orderBy($field, $orderBy);
                    $total = $query->count();
                    $query->offset($start);
                    $query->limit($limit);
                    $tmp_data = $query->get();
                }
                $data = [];
                foreach ($data as $prop) {
                    $query = RetsPropertyData::query();
                    $query->select(PropertyConstants::SELECT_DATA);
                    if ($textSearchField === $prop) {
                        $query->where('ListingId', $prop);
                        $query->orderBy($field, $orderBy);
                        $property = $query->first();
                        $property = collect($property)->all();
                        $property["isOpenHouse"] = 0;
                        $tmp_data[] = $property;
                        $total = 1;
                    } else {
                        if ($textSearchField) {
                            $query->where('City', $textSearchField);
                        }
                        $query->where('ListingId', $prop);
                        $query->orderBy($field, $orderBy);
                        $property = $query->first();
                        $property = collect($property)->all();
                        $property["isOpenHouse"] = 0;
                        $tmp_data[] = $property;
                    }
                }
                $response_data['alldata'] = $tmp_data;
                $response_data['total'] = $total;
                $response_data['offset'] = $start;
                $response_data['limit'] = $limit;
                // $response_data = $tmp_data;
            } catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }
        }
        return response($response_data, 200);
    }

    public function getFeaturedListings(Request $request)
    {
        $response_data = [];
        $tmp_data = [];
        $validator = Validator::make($request->all(), [
            //"AgentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                // $response_data = FeaturedListing::with('getProperty')->where("AgentId", $request->AgentId)->limit(4)->get();
                // foreach ($response_data as $datum) {
                //     $datum = collect($datum)->all();
                //     $datum = collect($datum["get_property"])->only(PropertyConstants::SELECT_DATA)->all();
                //     $datum["isOpenHouse"] = 0;
                //     //$datum["ListPrice"] = number_format($datum["ListPrice"]);
                //     $tmp_data[] = $datum;
                // }
                // $response_data = $tmp_data;
                $ids = FeaturedListing::where("AgentId", $request->AgentId)->pluck("ListingId")->toArray();
                $response_data = RetsPropertyData::whereIn("ListingId", $ids)->orWhere("City", PropertyConstants::FEATURED_CITY)->where('ListPrice', '>', PropertyConstants::FEATURED_PRICE)->orderBy("Dom", "ASC")->limit(4)->get();
            } catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }
        }
        return response($response_data, 200);
    }

    public function getRecentListings(Request $request)
    {
        $response_data = [];
        $tmp_data = [];
        $validator = Validator::make($request->all(), [
            //"AgentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                $response_data = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->orderBy('updated_time', 'desc')->whereNotNull('ImageUrl')->limit(4)->get();
            } catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }
        }
        return response($response_data, 200);
    }
    public function webSettings(Request $request)
    {
        $response = [];
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $agentId = $request->agentId;
            $PageName = $request->all('PageName');
            $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag', 'TopBanner', 'FbAppId', 'GoogleClientId','OfficeName')
                ->where("AdminId", $request->agentId)
                ->first();
            $seo = Pages::select('MetaTitle', 'MetaDescription', 'MetaTags', 'Setting')->where('PageName', $PageName)
                ->where("AgentId", $request->agentId)
                ->first();
            $response['websetting'] = $websetting;
            if ($request->PageName == "Property details") {
                $seo->MetaDescription = "";
            }
            if ($seo && $seo->Setting != '') {
                $response['pageSetting'] = json_decode($seo->Setting);
                $arrangeSection =  json_decode($response['pageSetting']->ArrangeSection);
                $arrangeSections = [];
                foreach ($arrangeSection[0] as $key => $value) {
                    $arrangeSections[] =  $value->value;
                }
                unset($response['pageSetting']->ArrangeSection);
                $response['arrangeSections'] = $arrangeSections;
            }
            unset($seo['Setting']);
            $response['seo'] = $seo;
        }
        return response($response, 200);
    }
    public function ContactEnquiry(Request $request)
    {
        $form_data =  $request->all();
        $form_data['IpAddress'] = $_SERVER['REMOTE_ADDR'];
        // return $form_data;
        $query = ContactEnquiry::create($form_data);
        if ($query) {
            // send data to zapier
            $arr = array(
                'Subject' => 'New enquiry',
                'Name' => $request->Name,
                'Email' => $request->Email,
                'Message' => $request->Message,
                'Page Url' => $request->Url,
                'Date and time' => date("d/m/Y h:i:sa")
            );
            $zap = ZapierSender($arr);
            // end code
            $msg = "<h1>Hi Admin</h1>
                <p>1 new enquiry raised, please contact with this user</p>
                <table style='width:100%'>
            <tr>
                <th style='border:1px solid black'>Name</th>
                <th style='border:1px solid black'>Email</th>
                <th style='border:1px solid black'>Message</th>
                <th style='border:1px solid black'>URL</th>
                <th style='border:1px solid black'>Page</th>
            </tr>
            <tr>
                <td style='border:1px solid black'>$request->Name</td>
                <td style='border:1px solid black'>$request->Email</td>
                <td style='border:1px solid black'>$request->Message</td>
                <td style='border:1px solid black'>$request->Url</td>
                <td style='border:1px solid black'>$request->Page</td>
            </tr>

            </table>";
            $superAdminEmail = getSuperAdmin();
            $adminEmail = getAdmin($request->AgentId);
            $subject = "A new enquiry raised";
            // Code for sending dynamic email content
            // $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'Password reset' . '%' )->get();
            // foreach ($sent_content as $sent_msg => $sent_message) {

            // }
            // $Name = $request->Name;
            // $AgentEmail=$request->Email;
            // $AgentMessage = $request->Message;

            // // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

            // // }
            // $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{AgentName}','{AgentEmail}','{AgentPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{Resetlink}','{SiteUrl}'];
            // $content1 = [$Name,$AgentEmail,'','','','','','','','','',env('APP_NAME'),'',env('WEDUURL')];
            // $EmailContent = str_replace($content,$content1,$sent_message->content);
            // $EmailSubject = $sent_message->subject;
            // $subject = $EmailSubject;
            // $msg = $EmailContent;
            // sendEmail("SMTP", env('MAIL_FROM'), $adminEmail,$superAdminEmail , env('ALERT_CC_EMAIL_ID'), $subject, $msg, "UserLogin - enquiry", "", env('ENQUIRY'));
            sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail,env('ALERT_CC_EMAIL_ID') , env('ALERT_BCC_EMAIL_ID'), $subject, $msg, "UserLogin - enquiry", "", env('ENQUIRY'));
            $response = ['success' => 'Enquiry Submitted'];
            $notification_data = [
                "ContactName" => $request->Name,
                "Email" => $request->Email,
                "Message" => $request->Message,
                "StatusId" => 0,
                "AgentId" => $request->AgentId,
                "subject" => $request->Name . " " . env('CONTACTUS_NOTIFICATION_MSG'),
                "PageFrom" => $request->Page,
                "Url" => $request->Url
            ];
            saveNotificationData($notification_data);
        } else {
            $response = ['errors' => 'Something went wrong!'];
        }
        return $response;
    }

   public function getProperties(Request $request)
    {
        $requestData = $request->all();
        $featuredListings = [];
        $recentListings = [];
        $propertyList = [];
        $tmp_data = [];
        $agentId = $request->agentId;
        if (isset($requestData["featuredListing"]) && $requestData["featuredListing"]) {
            $starttimeFeaturedListing = microtime(true);
            $ids = FeaturedListing::where("AgentId", $request->AgentId)->pluck("ListingId")->toArray();
            $response_data = RetsPropertyData::select(PropertyConstants::HOME_SELECT_DATA)->where('ListPrice', '>', PropertyConstants::FEATURED_PRICE)->where(function ($qu) use ($ids) {
                $qu->whereIn("ListingId", $ids)->orWhere("City", PropertyConstants::FEATURED_CITY);
            })->where(function ($q) {
                $q->where('ImageUrl', '!=', '')
                    ->orWhere('ImageUrl', '!=',  null);
            })->orderBy("updated_time", "desc")->limit(4)->get();
            $featuredListings = $response_data;
            $endtimeFeaturedListing = microtime(true);
            $durationtimefeaturedListing = $endtimeFeaturedListing - $starttimeFeaturedListing;
        }
        $ids = array();
        //
        //$IpAddress = \Request::getClientIp(true);
        // third party api to get city name by ip address
        //$geopluginURL = 'http://www.geoplugin.net/php.gp?ip=' . $IpAddress;
        //$addrDetailsArr = unserialize(file_get_contents($geopluginURL));
        /*Get City name by return array*/
        //$city = $addrDetailsArr['geoplugin_city'];
        //
        if (isset($requestData["recentListing"]) && $requestData["recentListing"]) {
            $starttimeRecentListing = microtime(true);
            $recentListings = RetsPropertyData::select(PropertyConstants::HOME_SELECT_DATA)->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->limit(20)->orderBy('inserted_time', 'desc')->get();
            $endtimeRecentListing = microtime(true);
            $durationtimeRecentListing = $endtimeFeaturedListing - $starttimeFeaturedListing;
            /*foreach ($recentListings as $key => $value) {
                $ids[] = $value->id;
            }*/
        }
        //
        /*if (isset($requestData["propertyList"]) && $requestData["propertyList"]) {
            $limit = PropertyConstants::HOME_PAGE_LIMIT;
            $query = RetsPropertyData::query();
            $PropertyType = $query->distinct('PropertyType')->select("PropertyType")->get();
            $query->select(PropertyConstants::SELECT_DATA);
            $query->where('PropertyStatus', 'Sale');
            $query->where('PropertyType', 'Residential');
            $query->where(function ($q) {
                $q->where('ImageUrl', '!=', '')
                    ->orWhere('ImageUrl', '!=',  null);
            });
            if (count($ids) > 0) {
                $query->whereNotIn('id', $ids);
            }
            $query->orderBy('Time     $query->limit($limit);
            $propertyList = $query->get();
        }*/
        $starttimecount = microtime(true);
        $resiCountQuery = "SELECT count(*) as count from RetsPropertyData where  PropertyType = 'Residential' and Status = 'A'";
        $condosCountQuery = "SELECT count(*) as count from RetsPropertyData where  PropertyType = 'Condos' and Status = 'A'";
        $soldCountQuery = "SELECT count(ListingId) as count from RetsPropertyDataPurged";
        $resiCount =  DB::selectOne($resiCountQuery);
        $condosCount =  DB::selectOne($condosCountQuery);
        $soldCount =  DB::selectOne($soldCountQuery);
        $resiCount =  $resiCount->count;
        $condosCount =  $condosCount->count;
        $soldCount =  $soldCount->count;
        //dd($resiCount->count);
        /*$resiCount = RetsPropertyData::where("PropertyType", "Residential")->where("Status", "A")->count();
        $condosCount = RetsPropertyData::where("PropertyType", "Condos")->where("Status", "A")->count();
        $soldCount = RetsPropertyDataPurged::count();*/
        $endtimetimecount  = microtime(true);
        $durationtimecount = $endtimetimecount - $starttimecount;
        $response = [
            "recentListing" => $recentListings,
            "featuredListing" => $featuredListings,
            //"propertyListing"  => $propertyList,
            'resiCount' => $resiCount,
            'condosCount' => $condosCount,
            'soldCount' => $soldCount,
            'durationFeatured' => $durationtimefeaturedListing,
            'countTime' => $durationtimecount,
            'recentListingTime' => $durationtimeRecentListing,
        ];
        return $response = response($response, 200);
    }


    public function SubmitHomeValue(Request $request)
    {
        $form_data = $request->all();
        $data['name'] = $form_data['name'];
        $data['email'] = $form_data['email'];
        $data['phone'] = $form_data['phone'];
        $data['agent_id'] = $form_data['agent_id'];
        $data['message'] = $form_data['purpose'];
        $data['time'] = $form_data['time'];
        $data['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['property_size'] = $form_data['sqft'];
        $query = Enquiries::insert($data);
        if ($query) {
            // send data to zapier
            $arr = array(
                'Subject' => 'New enquiry from home valuation',
                'Name' => $request->name,
                'Email' => $request->email,
                'Phone' => $request->phone,
                'Message' => $request->purpose,
                'Property size' => $request->sqft . 'Sqft',
                'Date and time' => date("d/m/Y h:i:sa")
            );
            $agentId = $request->agent_id;
            $zap = ZapierSender($arr, $agentId);
            // end code

            $query1 = User::where('type', $data['agent_id'])->first();
            if ($query1) {
                $subject = "Wedu.ca Home Valuation";
                $message = "<p>imformation of the user who is interested into home valuation , details listed below<p>";
                foreach ($data as $key => $value) {
                    $message .= '<p>' . $key . ' : ' . $value . '</p>';
                }
                // Code for sending dynamic email content
                // $sent_content = TemplatesModel::select('subject','content')->where('name', 'like', '%' . 'Password reset' . '%' )->get();
                // foreach ($sent_content as $sent_msg => $sent_message) {

                // }
                // $Name = $request->name;
                // $AgentEmail=$request->email;
                // $AgentPhone = $request->phone;

                // // if (isset($Name) || isset($AgentEmail) || isset($AgentPhone) || isset($Office) || isset($Street) || isset($City) || isset($OfficeState) || isset($SiteUrl) || isset($OficeZip) || isset($SiteName)) {

                // // }
                // $content = ['{LeadName}','{LeadEmail}','{LeadPhone}','{AgentName}','{AgentEmail}','{AgentPhone}','{OfficeName}','+ val +','{OfficeCity}','{OfficeState}','{OfficeZip}','{SiteName}','{Resetlink}','{SiteUrl}'];
                // $content1 = [$Name,$AgentEmail,$AgentPhone,'','','','','','','','',env('APP_NAME'),'',env('WEDUURL')];
                // $EmailContent = str_replace($content,$content1,$sent_message->content);
                // $EmailSubject = $sent_message->subject;
                // $subject = $EmailSubject;
                // $message = $EmailContent;
                $getSuperAdmin = getSuperAdmin();
                $adminEmail = getAdmin($request->agent_id);
                $sent_token_url = sendEmail("SMTP", env('MAIL_FROM'), $adminEmail, $getSuperAdmin, env('ALERT_BCC_EMAIL_ID'), $subject, $message, "HomeController->SubmitHomeValue", "", env('HOMEVALUE'));
                $notification_data = [
                    "ContactName" => $form_data['name'],
                    "Email" => $form_data['email'],
                    "Phone" => $form_data['phone'],
                    "Message" => $form_data['purpose'],
                    "StatusId" => 0,
                    "AgentId" => $form_data['agent_id'],
                    "subject" => $form_data['name'] . " " . env('HOME_VALUATION_NOTIFICATION_MSG'),
                    "PageFrom" => "HomeValuation"
                ];
                saveNotificationData($notification_data);
            }
            return json_encode(["success" => "Query submitted"]);
        } else {
            return json_encode(["error" => "Something went wrong"]);
        }
    }
    public function GetStaffs(Request $request)
    {
        $form_data = $request->all();
        if (isset($form_data['getInfo'])) {
            $AgentId = $form_data['AgentId'];
            $staffId = $form_data['staffId'];
            $query = User::where('AdminId', $AgentId)->where('id', $staffId)->first();
            // $query = staff::where('AdminId', $AgentId)->where('id', $staffId)->first();
            $data['StaffDetail'] = $query;
            $id = $data['StaffDetail']->id;
            $simage = "";
            $simage = Staff::select('ImageUrl')->where('UserId', $id)->first();
            if ($simage) {
                $data["ImageUrl"] = $simage->ImageUrl;
            } else {
                $data["ImageUrl"] = null;
            }
            return $data;
        }
        $AgentId = $form_data['AgentId'];
        $currentPage = $form_data['currentPage'];

        $limit = 6;
        $offset = $currentPage - 1;
        $start = ($offset * $limit);

        $query = User::where('AdminId', $AgentId);
        if (isset($form_data['agentName'])) {
            $AgentName = $form_data['agentName'];
            $query = $query->where(function ($q) use ($AgentName) {
                $q->where('name', 'like', '%' . $AgentName . '%');
            });
        }
        $total = $query->count();
        $lastPage = $total % $limit;
        if ($lastPage == 0) {
            $totalPages = $total / $limit;
        } else {
            $totalPages = floor($total / $limit) + 1;
        }
        $totalPages;
        $query = $query->offset($start);
        $query = $query->limit($limit);
        $records = $query->get();
        $img = array();
        $simage = "";
        foreach ($records as $key => $value) {
            $id = $value->id;
            $simage = Staff::select('ImageUrl')->where('UserId', $id)->first();
            if ($simage) {
                $img[$id] = $simage->ImageUrl;
            } else {
                $img[$id] = null;
            }
        }

        $data['records'] = $records;
        $data['total'] = $total;
        $data['currentPage'] = $currentPage;
        $data['totalPages'] = $totalPages;
        $data['images'] = $img;
        return $data;
    }
    public function GetBlogs(Request $request)
    {
        $form_data = $request->all();
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $agentId = $form_data['agentId'];
            if (isset($form_data['blogUrl'])) {
                $data = [];
                $blogUrl = $form_data['blogUrl'];
                $query = BlogModel::where('AdminId', $agentId)->where('Url', $blogUrl)->first();
                $data['blogDetail'] = $query;
                return $data;
            }

            $currentPage = $form_data['currentPage'];
            $limit = 6;
            $offset = $currentPage - 1;
            $start = ($offset * $limit);

            $query = BlogModel::where('AdminId', $agentId);
            $topPost = BlogModel::where('AdminId', $agentId)->orderBy('id', 'asc')->limit(3)->get();
            if (isset($form_data['category'])) {
                $category = $form_data['category'];
                $catId = BlogCategory::where('name', $category)->select('id')->first();
                $newCat = $catId->id;
                $query = $query->where('categories', 'like', '%"' . $newCat . '"%');
            }
            if (isset($form_data['blogSearch'])) {

                $title = $form_data['blogSearch'];
                $query = $query->where('Title', $title);
            }
            if (isset($form_data['currentBlog'])) {
                $currentBlog = $form_data['currentBlog'];
                $query = $query->where('Url', '!=', $currentBlog);
            }

            $total_result = $query->count();
            $total = $query->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $totalPages;
            $query = $query->offset($start);
            $query = $query->limit($limit);
            $query = $query->orderBy('id', 'desc');
            $records = $query->get();
            $data['records'] = $records;
            $data['total'] = $total;
            $data['currentPage'] = $currentPage;
            $data['totalPages'] = $totalPages;
            $data['topPost'] = $topPost;
            return $data;
        }
    }
    public function GetBlogTitle(Request $request)
    {
        $form_data = $request->all();
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $agentId = $form_data['agentId'];
            if (isset($form_data['keyword_search'])) {
                $data = [];
                $keyword_search = $form_data['keyword_search'];
                $query = BlogModel::where('AdminId', $agentId)->where('Title', 'like', '%' . $keyword_search . '%')->get();
                $suggesstionArr = array();
                if ($query) {
                    foreach ($query as $key => $value) {
                        $res = array("text" => $value['Title'], 'value' => $value['Title']);
                        array_push($suggesstionArr, $res);
                    }
                }
                $data['suggesstionArr'] = $suggesstionArr;
                return $data;
            }
        }
    }
    public function GetBlogCategory(Request $request)
    {
        $query = BlogCategory::select('Name')->get();
        $suggesstionArr = array();
        if ($query) {
            foreach ($query as $key => $value) {
                $res = array("text" => $value['Name'], 'value' => $value['Name']);
                array_push($suggesstionArr, $res);
            }
        }
        $data['suggesstionArr'] = $suggesstionArr;
        return $data;
    }
    public function GetSoldProperty(Request $request)
    {
        $form_data = $request->all();
        $limit = $form_data['limit'];
        $table = $form_data['table'];
        $currentPage = $form_data['currentPage'];
        //
        $IpAddress = \Request::getClientIp(true);
        // third party api to get city name by ip address
        $geopluginURL = 'http://www.geoplugin.net/php.gp?ip=' . $IpAddress;
        $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
        /*Get City name by return array*/
        $city = $addrDetailsArr['geoplugin_city'];
        //
        $offset = $currentPage - 1;
        $start = ($offset * $limit);
        $response_data = DB::table($table)
            ->leftJoin('retspropertydataimages', $table . '.Ml_num', '=', 'retspropertydataimages.listingID')
            ->select($table . '.id', $table . '.Addr', $table . '.Bath_tot', $table . '.Br', $table . '.Addr', $table . '.Municipality', $table . '.Sqft', $table . '.Lp_dol', $table . '.Type_own1_out', $table . '.SlugUrl', 'retspropertydataimages.s3_image_url')
            ->where($table . '.Municipality', $city);
        $total = $response_data->count();
        $lastPage = $total % $limit;
        if ($lastPage == 0) {
            $totalPages = $total / $limit;
        } else {
            $totalPages = floor($total / $limit) + 1;
        }
        $query = $response_data->offset($start);
        $response_data = $response_data->limit($limit);
        $response_data = $response_data->get();
        $data['result'] = $response_data;
        $data['total_records'] = $total;
        $data['currentPage'] = $currentPage;
        $data['totalPages'] = $totalPages;
        return json_encode($data);
    }
    public function getCities(Request $request)
    {
        $form_data = $request->all();
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $agentId = $form_data['agentId'];
            $limit = $form_data['limit'];
            $city = CityData::where('AgentId', $agentId)->where('Featured', 1)->limit($limit)->get();
            $AllSubtype = array();
            if (!isset($form_data['onlyCity'])) {
                if (count($city) > 0) {
                    foreach ($city as $key => $value) {
                        $cityname = $value->CityName;
                        $community = RetsPropertyData::distinct('Community')->where('City', $cityname)->limit(4)->get('Community');
                        $count = RetsPropertyData::where('City', $cityname)->count();
                        $AllSubtype[$cityname] = $community;
                        $value->count = $count;
                    }
                    $data['community'] = $AllSubtype;
                }
            }
            $data['city'] = $city;
            return $data;
        }
    }

    public function SendListingRequest(Request $request)
    {
        $form_data = $request->all();
        $data['name'] = $form_data['name'];
        $data['email'] = $form_data['email'];
        $data['phone'] = $form_data['mobile'];
        $data['agent_id'] = $form_data['agent_id'];
        $data['message'] = $form_data['purpose'];
        $data['bedrooms'] = $form_data['beds'];
        $data['bathrooms'] = $form_data['baths'];
        $data['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $query = Enquiries::insert($data);
        if ($query) {
            $query1 = User::where('type', $data['agent_id'])->first();
            if ($query1) {
                $subject = "More listings request";
                $message = "<p>imformation of the user who is interested more listings to show<p>";
                foreach ($data as $key => $value) {
                    $message .= '<p>' . $key . ' : ' . $value . '</p>';
                }

                $getSuperAdmin = getSuperAdmin();
                $adminEmail = getAdmin($request->agent_id);
                $sent_token_url = sendEmail("SMTP", env('MAIL_FROM'), $adminEmail, $getSuperAdmin, env('ALERT_BCC_EMAIL_ID'), $subject, $message, "HomeController->SendListingRequest");
            }
            return json_encode(["success" => "Query submitted"]);
        } else {
            return json_encode(["error" => "Something went wrong"]);
        }
    }

    public function getTestimonials(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "agentId" => "required",
            "limit" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $form_data = $request->all();
            $agentId = $form_data['agentId'];
            $limit = $form_data['limit'];
            $data['result'] = Testimonial::where('AgentId', $agentId)->get();
            return json_encode($data);
        }
    }
    // TRied With row Data  TODO::DELETE
    public function homeStatss(Request $request)
    {
        $query = RetsPropertyDataPurged::query();
        $finalPriceData = [];
        $finalDateData = [];
        $finalData = [];
        $finalSoldData = [];
        $date = "";
        $c_date = new DateTime();
        // for ($mnth = 0; $mnth < 6; $mnth++) {
        $prices = array();
        $date1 = $c_date->format('Y-m-d H:i:s');
        $c_date->modify("-6 month");
        $date = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->format('Y-m');
        $response_data = $query->select(
            [
                DB::raw('YEAR(inserted_time) as year'),
                DB::raw('MONTH(inserted_time) as periodNumber '),
                DB::raw('COUNT(Sp_dol) as data')
            ]
        )
            ->where('City', PropertyConstants::GTACITY)
            ->whereBetween('inserted_time', [$date, $date1])
            ->groupBy('year')
            ->groupBy('periodNumber')
            ->orderBy('year', 'ASC')
            ->orderBy('periodNumber', 'desc')
            // ->limit(2)
            ->get();
        dd($response_data);
        // dd("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyDataPurged` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");

        foreach ($response_data as $key => $value) {
            $prices[] = (int)round($value->Sp_Dol);
        }
        if (count($prices)) {
            $finalSoldData[] = count($response_data);
            $finalPriceData[] = getMedian($prices);
            $finalDateData[] = $s_date;
        } else {
            $finalDateData[] = $s_date;
            $finalPriceData[] = 0;
        }
        // }
        $finalData = array(
            "date" => $finalDateData,
            "price" => $finalPriceData,
            "sold" => $finalSoldData
        );
        dd($finalData);
        return json_encode($finalData);

        // $query_for_count = "SELECT count(*) as counts, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date>'$startDate' and City= '$city'  group by date";
        // $count_response_data = DB::select($query_for_count);
        // $query_for_median = " SELECT Sp_dol, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >'$startDate' and City= '$city' ORDER BY `date`  DESC "; // DESC
        // $response_data = DB::select($query_for_median);
        foreach ($response_data as $key => $value) {
            if (array_key_exists($value->date, $tempPriceMedian)) {
                $lastPrice = $tempPriceMedian[$value->date];
                array_push($lastPrice, intval(round($value->Sp_dol)));
                $tempPriceMedian[$value->date] = $lastPrice;
            } else {
                $lastPrice[] = intval(round($value->Sp_dol));
                $tempPriceMedian[$value->date] = $lastPrice;
            }
        }
        foreach ($tempPriceMedian as $key => $priceMedian) {
            $finalPriceData[] = getMedian($priceMedian);
            if (!in_array($key, $finalDateData)) {
                $finalDateData[] = $key;
            }
        }
        foreach ($count_response_data as $key => $value) {
            $finalSoldData[] = $value->counts;
            if (!in_array($value->date, $finalDateData)) {
                $finalDateData[] = $value->date;
            }
        }
    }
    public function homeStats(Request $request)
    {
        $finalData = [];
        $c_date = new DateTime();
        $reqDate=$request->date?$request->date:12;
        $startDate = $c_date->modify("-$reqDate month");
        $startDate = $startDate->format('Y-m-d');
        $starttimeCount = microtime(true);
        $avgSoldPrice = [];
        $soldCount = [];
        $TimePeriod = [];
        // DB::enableQueryLog();
        $queries = StatsData::query();
        $queries->select("AvgPrice", "Count", "TimePeriod", "Date");
        $queries->where("Type", "Sale");
        $queries->where("Date", ">=", $startDate);
        $queries->orderBy('Date', 'ASC');
        $statsData = $queries->get();
        // dd(DB::getQueryLog());
        foreach ($statsData as $key => $d) {
            $avgSoldPrice[] = $d->AvgPrice;
            $soldCount[] = $d->Count;
            $TimePeriod[] = $d->TimePeriod;
        }
        $endttimeCount = microtime(true);
        $durationtimecount = $endttimeCount - $starttimeCount;
        // $durationtimeAvg = $endttimeAvg - $starttimeAvg;  // Ram
        $finalData = array(
            "date" => $TimePeriod,
            "price" => $avgSoldPrice,
            "sold" => $soldCount,
            "durationTimeCount" => $durationtimecount,
            // "durationtimeAvg" => $durationtimeAvg // Ram
            "durationtimeAvg" => $durationtimecount
        );
        return json_encode($finalData);
    }
    public function marketStatsFilterData(Request $request)
    {
        $field = $request->key;
        $field = "City";
        $cities = getFiltersData($field);
        $field = "Community";
        $community = getFiltersData($field);

        $field = "PropertyType";
        $propertyType = getFiltersData($field);
        $result = array(
            'city' => $cities,
            'community' => $community,
            'propertyType' => $propertyType,
        );
        return response($result, 200);
        // $listingId = get_auto_sugesstion('ListingId');
        // $municipality = get_auto_sugesstion('Municipality');
        // $cities = get_auto_sugesstion('City');
        // $Communities = get_auto_sugesstion('Community');
        // $Countries = get_auto_sugesstion('County', $text['query']);
    }
    public function totalSold(Request $request)
    {
        $query = RetsPropertyDataPurged::query();
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $propType = $request->propType;
        $city = $request->city;
        $propType = $propType ? " AND PropertyType = '$propType'" : "";
        $city =  $city ? " AND City = '$city'" : "";
        $dtTime =  $request->datePeriod ? $request->datePeriod : 6;
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $soldList = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyDataPurged` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city AND  Sp_dol > 0 GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        // "Status","A"
        $finalData = array(
            "totalSold" => $soldList
        );
        return response($finalData, 200);
    }

    // DONE
   /* public function soldActive(Request $request)
    	{
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $propType = $request->propType;
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];

        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];

        $city = $request->city;
        $propType = $propType ? " AND PropertyType = '$propType'" : "";
        $city =  $city ? " AND City = '$city'" : "AND City = 'Toronto'";
        $dtTime =  $request->datePeriod ? $request->datePeriod : 12;
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date'  $propType $city  group by date "; // DESC
        $soldList = DB::select($soldQuery);

        $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
        $active = DB::select($activeQuery);

        $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $propType $city  GROUP BY  date";
        $finalPriceData = DB::select($newListQuery);
        foreach ($soldList as $key => $value) {
            $dates[] = $value->date;
            $soldListDataTemp[$value->date] = $value->soldCounts;
        }
        foreach ($active as $key => $d) {
            if (!in_array($d->date, $dates)) {
                $dates[] = $d->date;
            }
            $activeListDataTemp[$d->date] = intval(round($d->activeCounts));
        }

        foreach ($finalPriceData as $key => $value) {
            if (!in_array($value->date, $dates)) {
                $dates[] = $value->date;
            }
            $newListDataTemp[$value->date] = intval(round($value->NewListounts));
        }
        foreach ($dates as $key => $dt) {
            if (array_key_exists($dt, $soldListDataTemp)) {
                $soldListData[] = $soldListDataTemp[$dt];
            } else {
                $soldListData[] = 0;
            }
            if (array_key_exists($dt, $activeListDataTemp)) {
                $activeListData[] = $activeListDataTemp[$dt];
            } else {
                $activeListData[] = 0;
            }
            if (array_key_exists($dt, $newListDataTemp)) {
                $newListData[] = $newListDataTemp[$dt];
            } else {
                $newListData[] = 0;
            }
        }
        $finalData = array(
            "dates" => $dates,
            "soldList" => $soldListData,
            "activeList" => $activeListData,
            "newList" => $newListData,
        );
        return response($finalData, 200);
    }*/
    //done
	 public function soldActive(Request $request)
    {
        $propType = $request->propType;
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];

        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $community = $request->community;
        $city = $request->City;
        $propType = $propType ? " AND PropertyType = '$propType'" : "";
        $city =  $city ? " AND City = '$city'" : "AND City = 'Toronto'";
        $dtTime =  $request->date ? $request->date : 12;
        $community       =  $community ? " AND Community = '$community'" : "";
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Sp_date<>'0000-00-00' and Sp_date >= '$s_date'  $propType $city  group by date "; // DESC
        $soldList = DB::select($soldQuery);

        $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
        $active = DB::select($activeQuery);

        $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
        $finalPriceData = DB::select($newListQuery);
        foreach ($soldList as $key => $value) {
            $dates[] = $value->date;
            $soldListDataTemp[$value->date] = $value->soldCounts;
        }
        foreach ($active as $key => $d) {
            if (!in_array($d->date, $dates)) {
                $dates[] = $d->date;
            }
            $activeListDataTemp[$d->date] = intval(round($d->activeCounts));
        }

        foreach ($finalPriceData as $key => $value) {
            if (!in_array($value->date, $dates)) {
                $dates[] = $value->date;
            }
            $newListDataTemp[$value->date] = intval(round($value->NewListounts));
        }
        foreach ($dates as $key => $dt) {
            if (array_key_exists($dt, $soldListDataTemp)) {
                $soldListData[] = $soldListDataTemp[$dt];
            } else {
                $soldListData[] = 0;
            }
            if (array_key_exists($dt, $activeListDataTemp)) {
                $activeListData[] = $activeListDataTemp[$dt];
            } else {
                $activeListData[] = 0;
            }
            if (array_key_exists($dt, $newListDataTemp)) {
                $newListData[] = $newListDataTemp[$dt];
            } else {
                $newListData[] = 0;
            }
        }
        $finalData = array(
            "dates" => $dates,
            "soldList" => $soldListData,
            "activeList" => $activeListData,
            "newList" => $newListData,
        );
        return response($finalData, 200);
    }

    public function domAvgMedian(Request $request)
    {
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceDate = [];
        $dates = [];
        $priceDataTemp = [];
        $propType   = $request->propertyType;
        $city       = $request->City;
        $community = $request->community;
        $propType   = $propType ? " AND PropertyType = '$propType'" : "";
        $city       =  $city ? " AND City = '$city'" : "";
        $community       =  $community ? " AND Community = '$community'" : "";
        $dtTime     =  $request->date ? $request->date : 6;
        $c_date     = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $avgDom = array();
        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community group by date  ";
        //  dd($avgDomQuery);
        $avgDom = DB::select($avgDomQuery);
        $finalPriceData = [];
        $finalData = [];
        $c_date = new DateTime();
        $qry = "SELECT AVG(Sp_dol) as priceAvg ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community group by date";
        $priceData = DB::select($qry);
        foreach ($priceData as $key => $value) {
            $priceDate[] = $dates[] = $value->date;
            $priceDataTemp[$value->date] = intval(round($value->priceAvg));
        }
        foreach ($avgDom as $key => $d) {
            if (!in_array($d->date, $dates)) {
                $dates[] = $d->date;
            }
            $avgDate[] = $d->date;
            $avgDataTemp[$d->date] = intval(round($d->avgDom));
        }
        $avgDom = [];
        foreach ($dates as $key => $dt) {
            if (array_key_exists($dt, $avgDataTemp)) {
                $avgDom[] = $avgDataTemp[$dt];
            } else {
                $avgDom[] = 0;
            }
            if (array_key_exists($dt, $priceDataTemp)) {
                $finalPriceData[] = $priceDataTemp[$dt];
            } else {
                $finalPriceData[] = 0;
            }
        }
        $finalData = array(
            "date" => $dates,
            "median" => $finalPriceData,
            "dom" => $avgDom
        );
        return response($finalData, 200);
    }
    // done
    public function medianRental(Request $request)
    {

        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceMedianData = [];
        $totalLeaseTemp = [];
        $totalLeaseData = [];
        $dateData = [];
        $newListDataTemp = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND PropertyType = '$propType'" : "";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 6;
        $c_date = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");

        $totalLeaseQuery = "SELECT  count(Sp_dol) as totalLease ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease' and Sp_date<>'0000-00-00' and Sp_date > '$s_date' $propType $city $community group by date  ";
        $totalLease = DB::select($totalLeaseQuery);

        $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
        $newListData = DB::select($newListQuery);


        // $priceMedianQuery = "SELECT   avg(Sp_dol) as priceAvg,    DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community  GROUP BY  date";
        // $medianPrice = DB::select($priceMedianQuery);

        // 2022-05-05 00:000
        // 2022-04-05 00:000\\
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        for ($i = 1; $i <= $dtTime; $i++) {
            if ($i == 1) {
                $s_date = $curr_date->modify("-0 month");
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $s_date->format("Y-m-d H:i:s");
            } else {
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $prev_dates;
            }
            $prev_dates = $prev_date->format("Y-m-d H:i:s");
            $d_date = $prev_date->format("Y-m");
            $priceMedianQuery = "SELECT DATE_FORMAT(`Sp_date`,'%Y-%m') as date , AVG(Sp_dol) as median_val   FROM ( SELECT Sp_date, Sp_dol, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE Sp_dol is NOT NULL AND PropertyStatus='Sale'  AND  Sp_date >= '$prev_dates' AND  Sp_date <= '$s_date' ORDER BY  `Sp_date` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
            $medianPrice = DB::select($priceMedianQuery);
            $d = $medianPrice[0];
            $medians = intval(round($d->median_val));
            $date = $d->date;
            if (!$medians) {
                $date = $d_date;
            }
            if (!in_array($d->date, $dateData)) {
                $dateData[] = $date;
            }
            $priceMedianData[$date] = $medians;
        }
        //count median

        // foreach ($medianPrice as $key => $d) {
        //     if (!in_array($d->date, $dateData)) {
        //         $dateData[] = $d->date;
        //     }
        //     $priceMedianData[$d->date] = intval(round($d->priceAvg));
        // }
        // end count median

        foreach ($totalLease as $key => $value) {
            if (!in_array($value->date, $dateData)) {
                $dateData[] = $value->date;
            }
            $totalLeaseTemp[$value->date] = intval(round($value->totalLease));
        }
        foreach ($newListData as $key => $value) {
            if (!in_array($value->date, $dateData)) {
                $dateData[] = $value->date;
            }
            $newListDataTemp[$value->date] = intval(round($value->NewListCounts));
        }
        rsort($dateData);
        $newListData = [];
        $medianPrice = [];
        foreach ($dateData as $key => $dt) {
            if (array_key_exists($dt, $totalLeaseTemp)) {
                $totalLeaseData[] = $totalLeaseTemp[$dt];
            } else {
                $totalLeaseData[] = 0;
            }
            if (array_key_exists($dt, $newListDataTemp)) {
                $newListData[] = $newListDataTemp[$dt];
            } else {
                $newListData[] = 0;
            }
            if (array_key_exists($dt, $priceMedianData)) {
                $medianPrice[] = $priceMedianData[$dt];
            } else {
                $medianPrice[] = 0;
            }
        }
        $finalData = array(
            "date" => $dateData,
            "median" => $medianPrice,
            "totalLease" => $totalLeaseData,
            "newList" => $newListData,
        );
        return response($finalData, 200);
    }

    public function propertyTypeDistribution(Request $request)
    {
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceMedianData = [];
        $totalLeaseTemp = [];
        $totalLeaseData = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND PropertyType = '$propType'" : "";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 6;
        $c_date = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");

        $condoAptQuery = "SELECT count(*) as condoApt FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        $condoApt = DB::select($condoAptQuery);

        $detachedQuery = "SELECT count(*) as detached FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        $detached = DB::select($detachedQuery);

        $condoTownhouseQuery = "SELECT count(*) as condoTownhouse FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        $condoTownhouse = DB::select($condoTownhouseQuery);

        $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);

        $othersQuery = "SELECT count(*) as others FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse') and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        $others = DB::select($othersQuery);

        $finalData = array(
            "condoApt" => isset($condoApt[0]) ? $condoApt[0]->condoApt : 0,
            "detached" => isset($detached[0]) ? $detached[0]->detached : 0,
            "condoTownhouse" => isset($condoTownhouse[0]) ? $condoTownhouse[0]->condoTownhouse : 0,
            "attRowTwnHouse" => isset($attRowTwnHouse[0]) ? $attRowTwnHouse[0]->attRowTwnHouse : 0,
            "others" => isset($others[0]) ? $others[0]->others : 0,
        );
        return response($finalData, 200);
    }
    public function absorptionData(Request $request)
    {
        $tempDate = [];
        $activeTemp = [];
        $soldTemp = [];
        $absorptionData = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND PropertyType = '$propType'" : "";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 6;
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $pr_date = "";
        for ($i = 1; $i <= $dtTime; $i++) {
            if ($i == 1) {
                $s_date = $curr_date->modify("-0 month");
                $prev_date = $date_prev->modify("-1 month");
                $d_date = $s_date->format("Y-m");
                $s_date = $s_date->format("Y-m-d H:i:s");
            } else {
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $prev_dates;
                $d_date = $pr_date;
            }
            $prev_dates = $prev_date->format("Y-m-d H:i:s");
            $pr_date = $prev_date->format("Y-m");
            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Sp_date >= '$prev_dates' AND  Sp_date <= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            // $query2 = "SELECT  count(*) as activeCount ,DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND inserted_time >= '$prev_dates' AND  inserted_time <= '$s_date' $propType $city $community ";
            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
            $activeData = DB::select($query2);
            // echo"$query2 <br/> <br/>";
            $soldData = $soldData[0];
            $activeData = $activeData[0];
            $tempDate[] = $d_date;
            $activeTemp[$d_date] = $activeData->activeCount ? $activeData->activeCount : 0;
            $soldTemp[$d_date] = $soldData->soldCount ? $soldData->soldCount : 0;
        }
        foreach ($tempDate as $key => $value) {
            $active = $activeTemp[$value];
            $sold = $soldTemp[$value];
            $absorption = 0;
            if ($active && $sold) {
                $absorption = ceil($sold * 100 / $active);
            }
            $absorptionData[] = round($absorption);
        }
        $res = array(
            "date"=>$tempDate,
            "absorptionData"=>$absorptionData
        );
        return response($res, 200);
    }

    public function GetPreferenceData(Request $request)
    {
        $data['PropertySubType'] = RetsPropertyData::select('PropertySubType')->distinct('PropertySubType')->get();
        $data['City'] = RetsPropertyData::select('City')->distinct('City')->get();
        $data['PropertyType'] = RetsPropertyData::select('PropertyType')->distinct('PropertyType')->get();
        return $data;
    }
    public function testStats(Request $request)
    {
        $tempDate = [];
        $activeTemp = [];
        $soldTemp = [];
        $absorptionData = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND PropertyType = '$propType'" : "";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 6;
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $pr_date = "";
        for ($i = 1; $i <= $dtTime; $i++) {
            if ($i == 1) {
                $s_date = $curr_date->modify("-0 month");
                $prev_date = $date_prev->modify("-1 month");
                $d_date = $s_date->format("Y-m");
                $s_date = $s_date->format("Y-m-d H:i:s");
            } else {
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $prev_dates;
                $d_date = $pr_date;
            }
            $prev_dates = $prev_date->format("Y-m-d H:i:s");
            $pr_date = $prev_date->format("Y-m");
            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Sp_date >= '$prev_dates' AND  Sp_date <= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            // $query2 = "SELECT  count(*) as activeCount ,DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND inserted_time >= '$prev_dates' AND  inserted_time <= '$s_date' $propType $city $community ";
            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
            $activeData = DB::select($query2);
            // echo"$query2 <br/> <br/>";
            $soldData = $soldData[0];
            $activeData = $activeData[0];
            $tempDate[] = $d_date;
            $activeTemp[$d_date] = $activeData->activeCount ? $activeData->activeCount : 0;
            $soldTemp[$d_date] = $soldData->soldCount ? $soldData->soldCount : 0;
        }
        foreach ($tempDate as $key => $value) {
            $active = $activeTemp[$value];
            $sold = $soldTemp[$value];
            $absorption = 0;
            if ($active && $sold) {
                $absorption = ceil($sold * 100 / $active);
            }
            $absorptionData[] = round($absorption);
        }
        $res = array(
            "date"=>$tempDate,
            "absorptionData"=>$absorptionData
        );
        return response($res, 200);
    }

    public function getSoldByAgent(Request $request)
    {

        $form_data = $request->all();
        $response_data=[];
        if(isset($form_data['OfficeName']))
        {
            $office_name = $form_data['OfficeName'];
            $offices = explode('"', $office_name);
            $currentPage = $form_data['currentPage'];
            $limit = $form_data['limit'];
            $offset = $currentPage - 1;
            $start = ($offset * $limit);

            $query1 = RetsPropertyDataPurged::select(PropertyConstants::SELECT_DATA);
            $query1 = $query1->whereIn('Rltr',$offices);
            $total = $query1->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $query1 = $query1->offset($start);
            $query1 = $query1->limit($limit);
            $query1 = $query1->orderBy('inserted_time', 'desc')->get();
            $response_data['totalRecord'] = $total;
            $response_data['result'] = $query1;
            $response_data['currentPage'] = $currentPage;
            $response_data['offset'] = $offset;
            $response_data['limit'] = $limit;
        }
        return response($response_data, 200);
    }
}
