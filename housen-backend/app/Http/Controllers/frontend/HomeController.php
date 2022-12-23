<?php

namespace App\Http\Controllers\frontend;

use App\Constants\PropertyConstants;
use App\Http\Controllers\agent\BlogController;
use App\Http\Controllers\Controller;
use App\Models\Enquiries;
use App\Models\ProvinceTbl;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\MarketStats;
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
use Carbon\Carbon;
use App\Models\StatsData;
use DateTime;
use App\Models\RetsPropertyDataImage;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\PreConstruction;
use App\Models\SqlModel\Builder;
use Illuminate\Support\Facades\Storage;


class HomeController extends Controller
{
    //

     public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function getBlog()
    {
        $data = [];
        $blogs = BlogModel::orderBy('id', 'desc')->limit(3)->get();
        foreach ($blogs as $key => $value) {
            $value->MainImg = env('APP_URL') . $value->MainImg;
        }
        $data["blogData"] = $blogs;
        return response($blogs, 200);
    }
    public function GetBlogsSlugs(Request $request)
    {
        // GetBlogs
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
            foreach ($blogs as $key => $value) {
                $value->MainImg = env('APP_URL') . $value->MainImg;
            }
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
            "user_id" => $request->user_id,
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
            "user_id" => "required",
            "UserLocation" => "required",
        ]);

        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            $response["message"] = "All fields are required !";
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            if (isset($request->agentId) && $request->agentId !="") {
                $data['agent_id'] =  $request->agentId;
            }
            $res = Enquiries::insert($data);
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
            $zap = ZapierSender($arr,$request->agentId);
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
                $limit = 4;
                if (isset($request->housenProject)) {
                    $limit = 12;
                }

                $ids = FeaturedListing::where("AgentId", $request->AgentId)->limit($limit)->pluck("ListingId")->toArray();
                $city = null;
                $propertySubType = null;
                $minPrice = null;
                $maxPrice = null;
                if (isset($request->housenProject)) {
                    if ($request->userId != null) {
                        $query = LeadsModel::select('Preference')->where('id', $request->userId)->first();
                        if ($query) {
                            if ($query->Preference !== null) {
                                $per = json_decode($query->Preference);
                                $per->city = explode(",", $per->city);
                                $per->preportySubType = explode(",", $per->preportySubType);
                                $city = array_filter($per->city);
                                $propertySubType = array_filter($per->preportySubType);
                                $minPrice = $per->minPrice;
                                $maxPrice = $per->maxPrice;
                            }
                        }
                    }
                }
                $query1 = RetsPropertyData::whereIn("ListingId", $ids);
                if ($maxPrice != null) {

                    $query1 = $query1->where('ListPrice', '<=', (int)$maxPrice);
                }
                if ($minPrice != null) {

                    $query1 = $query1->where('ListPrice', '>=', (int)$minPrice);
                }
                if ($city != null && count($city) > 0) {

                    $query1 = $query1->whereIn('City', $city);
                }
                if ($propertySubType != null && count($propertySubType) > 0) {

                    $query1 = $query1->whereIn('PropertySubType', $propertySubType);
                }
                $response_data1 = $query1->orderBy("Dom", "ASC")->get();
                $images = [];

                foreach ($response_data1 as $datum) {
                    // $datum = collect($datum)->all();
                    // $datum = collect($datum["get_property"])->only(PropertyConstants::SELECT_DATA)->all();
                    // $datum["isOpenHouse"] = 0;
                    // //$datum["ListPrice"] = number_format($datum["ListPrice"]);
                    // $tmp_data[] = $datum;

                    if (isset($request->housenProject)) {
                        $listing_id = $datum['ListingId'];
                        $images[$listing_id] = RetsPropertyDataImage::select('s3_image_url')->where('listingID', $listing_id)->limit(5)->get();
                    }
                }

                if (isset($request->housenProject)) {
                    $response_data['property'] = $response_data1;
                    $response_data['images'] = $images;
                } else {
                    $response_data = $response_data1;
                }
            } catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }
        }
        return response($response_data, 200);
    }
    //
    public function getSoldListings(Request $request)
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
                $PropertyType = "Residential";
                $preDefineCity = [
                    "Brampton",
                    "Mississauga",
                    "Toronto",
                    "Vaughan",
                    "Milton",
                    "Oakville",
                    "Burlington",
                    "Ajax",
                    "Whitby",
                    "Pickering",
                    "Markham",
                    "Richmond Hill",
                    "Newmarket"
                ];
                $PredefinedPropertySubType = [
                    "Detached",
                    "Semi-Detached",
                    "Att/Row/Townhouse",
                    "Condo Townhouse",
                    "Condo Apt"
                ];
                $minPrice = 450000;
                $maxPrice = 2500000;
                $city = null;
                $propertySubType = null;
                if (isset($request->housenProject)) {
                    if ($request->userId != null) {
                        $query = LeadsModel::select('Preference')->where('id', $request->userId)->first();
                        if ($query) {
                            if ($query->Preference !== null) {
                                $per = json_decode($query->Preference);
                                $per->city = explode(",", $per->city);
                                $per->preportySubType = explode(",", $per->preportySubType);
                                $city = array_filter($per->city);
                                $propertySubType = array_filter($per->preportySubType);
                                $minPrice = $per->minPrice;
                                $maxPrice = $per->maxPrice;
                            }
                        }
                    }
                }

                // $IpAddress = \Request::getClientIp(true);
                // // third party api to get city name by ip address
                // $geopluginURL = 'http://www.geoplugin.net/php.gp?ip=' . $IpAddress;
                // $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
                // /*Get City name by return array*/
                // $cityByIp = $addrDetailsArr['geoplugin_city'];


                // $query1 = RetsPropertyData::select(PropertyConstants::SELECT_DATA);
                $query1 = RetsPropertyDataPurged::select(PropertyConstants::SELECT_DATA);

                if ($maxPrice != null) {

                    $query1 = $query1->where('ListPrice', '<=', (int)$maxPrice);
                }
                if ($minPrice != null) {

                    $query1 = $query1->where('ListPrice', '>=', (int)$minPrice);
                }
                if ($city != null && count($city) > 0) {

                    $query1 = $query1->whereIn('City', $city);
                } else {
                    $query1 = $query1->whereIn('City', $preDefineCity);
                }

                if ($propertySubType != null && count($propertySubType) > 0) {

                    $query1 = $query1->whereIn('PropertySubType', $propertySubType);
                } else {
                    $query1 = $query1->whereIn('PropertySubType', $PredefinedPropertySubType);
                }
                $limit = 6;
                // This is commented because inserted time is taking too much time (comment by sagar verma)
                //$data = $query1->orderBy('inserted_time', 'desc')->limit($limit)->get();
                $data = $query1->where('LastStatus', "Sld")->where("PropertyType","Condos")->orderBy('Sp_date', 'desc')->limit($limit)->get();
                $images = [];
                if (isset($request->housenProject)) {
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $listing_id =  $value->ListingId;
                            //$images[$listing_id] = RetsPropertyDataImage::select('s3_image_url')->where('listingID', $listing_id)->limit(5)->get();
                            $img = \App\Models\RetsPropertyDataImagesSold::select('image_urls')->where('listingID', $listing_id)->get();
                if(collect($img)->all() != []){
                    $img = json_decode($img[0]["image_urls"]);
                    $tmp_img = [];
                    foreach($img as &$si){
                        $k["s3_image_url"] = $si;
                        $tmp_img[] = $k;
                    }
                    $images[$listing_id] = $tmp_img;
                }else{
                    $images[$listing_id] = [];
                }
                        }
                    }
                    $response_data['property'] = $data;
                    $final_props = [];
                    foreach ($response_data['property'] as &$property) {
                        $property = collect($property)->all();
                        $final_props[] = getDom($property);
                    }
                    $response_data['property'] = $final_props;
                    $response_data['images'] = $images;
                } else {
                    $response_data = $data;
                }
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
                // $IpAddress = \Request::getClientIp(true);
                // // third party api to get city name by ip address
                // $geopluginURL = 'http://www.geoplugin.net/php.gp?ip=' . $IpAddress;
                // $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
                // /*Get City name by return array*/
                // $cityByIp = $addrDetailsArr['geoplugin_city'];
                $PropertyType = "Residential";
                $preDefineCity = [
                    "Brampton",
                    "Mississauga",
                    "Toronto",
                    "Vaughan",
                    "Milton",
                    "Oakville",
                    "Burlington",
                    "Ajax",
                    "Whitby",
                    "Pickering",
                    "Markham",
                    "Richmond Hill",
                    "Newmarket"
                ];
                $PredefinedPropertySubType = [
                    "Detached",
                    "Semi-Detached",
                    "Att/Row/Townhouse",
                    "Condo Townhouse",
                    "Condo Apt"
                ];
                $minPrice = 450000;
                $maxPrice = 2500000;
                $city = null;
                $propertySubType = null;
                $Dom = 1;
                if (isset($request->housenProject)) {
                    if ($request->userId != null) {
                        $query = LeadsModel::select('Preference')->where('id', $request->userId)->first();
                        if ($query) {
                            if ($query->Preference !== null) {
                                $per = json_decode($query->Preference);
                                $per->city = explode(",", $per->city);
                                $per->preportySubType = explode(",", $per->preportySubType);
                                $city = array_filter($per->city);
                                $propertySubType = array_filter($per->preportySubType);
                                $minPrice = $per->minPrice;
                                $maxPrice = $per->maxPrice;
                            }
                        }
                    }
                }

                $query1 = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Status', 'A');

                if ($maxPrice != null) {

                    $query1 = $query1->where('ListPrice', '<=', (int)$maxPrice);
                }
                if ($minPrice != null) {

                    $query1 = $query1->where('ListPrice', '>=', (int)$minPrice);
                }
                if ($city != null && count($city) > 0) {

                    $query1 = $query1->whereIn('City', $city);
                } else {
                    $query1 = $query1->whereIn('City', $preDefineCity);
                }

                if ($propertySubType != null && count($propertySubType) > 0) {

                    $query1 = $query1->whereIn('PropertySubType', $propertySubType);
                } else {
                    $query1 = $query1->whereIn('PropertySubType', $PredefinedPropertySubType);
                }
                $limit = 18;

                // $data = $query1->where('Dom', '<=', (float)$Dom)->whereNotNull('ImageUrl')->orderBy('inserted_time', 'desc')->limit($limit)->get();
                $data = $query1->where('Dom', '<', (float)$Dom)->where('PropertyStatus','Sale')->whereNotNull('ImageUrl')->orderBy('inserted_time', 'desc')->limit($limit)->get();
                $images = [];
                if (isset($request->housenProject)) {
                    if ($data) {
                        foreach ($data as $key => $value) {
                            $listing_id =  $value->ListingId;
                            $images[$listing_id] = RetsPropertyDataImage::select('s3_image_url')->where('listingID', $listing_id)->limit(5)->get();
                        }
                    }
                    $response_data['property'] = $data;
                    $final_props = [];
                    foreach ($response_data['property'] as &$property) {
                        $property = collect($property)->all();
                        $final_props[] = getDom($property);
                    }
                    $response_data['property'] = $final_props;
                    $response_data['images'] = $images;
                } else {
                    $response_data = $data;
                }
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
            $websetting = Websetting::select('WebsiteName', 'WebsiteTitle','bodyscriptTag', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag', 'TopBanner', 'FbAppId', 'GoogleClientId', 'OfficeName')
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
                $top_banner = json_decode($seo->Setting);
                if (isset($top_banner->TopBanner) && $top_banner->TopBanner !== null) {
                    $topbanner = env("APP_URL") .$top_banner->TopBanner;
                    $response['pageSetting']->TopBanner = $topbanner;
                }
                if (isset($top_banner->CommunityBanner) && $top_banner->CommunityBanner !== null) {
                    $communityBanner = env("APP_URL") . $top_banner->CommunityBanner;
                    $response['pageSetting']->CommunityBanner = $communityBanner;
                }
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
        $validator = Validator::make($request->all(), [
            "Email" => "required",
            "Name" => "required",
            "Phone" => "required"
        ]);
        // return $form_data;
        $data = array(
            'name' => $request->Name,
            'email' => $request->Email,
            'phone' => $request->Phone,
            'message' => $request->Message,
            'property_url' => $request->Url,
            'page_from' => $request->Page,
            'agent_id' => $request->AgentId
        );
        $query = Enquiries::create($data);
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
            // $content1 = [$Name,$AgentEmail,'','','','','','','','','',env('APP_NAME'),'',env('HOUSENFRONTURL')];
            // $EmailContent = str_replace($content,$content1,$sent_message->content);
            // $EmailSubject = $sent_message->subject;
            // $subject = $EmailSubject;
            // $msg = $EmailContent;
            // sendEmail("SMTP", env('MAIL_FROM'), $adminEmail,$superAdminEmail , env('ALERT_CC_EMAIL_ID'), $subject, $msg, "UserLogin - enquiry", "", env('ENQUIRY'));
            sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $msg, "UserLogin - enquiry", "", env('ENQUIRY'));
            $response = ['success' => 'Enquiry Submitted'];
            $notification_data = [
                "ContactName" => $request->Name,
                "Email" => $request->Email,
                "Message" => $request->Message,
                "StatusId" => 0,
                "AgentId" => $request->AgentId,
                "subject" => $request->Name . ", " . env('CONTACTUS_NOTIFICATION_MSG'),
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
        $featured = ["C5279553", "C5326745", "C5366282", "C5371958"];
        $recant   = ["W5433937", "S5433840", "C5404985", "C5444050"];
        $listdata = ["N5437480", "E5174700", "E5174706", "N5475315"];
        if (isset($requestData["featuredListing"]) && $requestData["featuredListing"]) {
            $starttimeFeaturedListing = microtime(true);
            $ids = FeaturedListing::where("AgentId", $request->AgentId)->pluck("ListingId")->toArray();
            $response_data = RetsPropertyData::select(PropertyConstants::HOME_SELECT_DATA)->where('ListPrice', '>', PropertyConstants::FEATURED_PRICE)->where(function ($qu) use ($ids) {
                $qu->whereIn("ListingId", $ids)->orWhere("City", PropertyConstants::FEATURED_CITY);
            })->where(function ($q) {
                $q->where('ImageUrl', '!=', '')
                    ->orWhere('ImageUrl', '!=',  null);
            })->orderBy("Dom", "ASC")->limit(4)->get();
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
            $recentListings = RetsPropertyData::select(PropertyConstants::HOME_SELECT_DATA)->orderBy('Dom', 'asc')->whereNotNull('ImageUrl')->limit(4)->orderBy('updated_time', 'desc')->get();
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
            $PropertyType = $query->distinct('PropertyType')->whereIn('ListingId', $listdata)->select("PropertyType")->get();
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
        //
        $form_data = $request->all();
        $data['name'] = $form_data['name'];
        $data['email'] = $form_data['email'];
        $data['phone'] = $form_data['phone'];
        $data['agent_id'] = $form_data['agent_id'];
        $data['user_ip'] = $_SERVER['REMOTE_ADDR'];

        $data['pro_type'] = $form_data['home_type'];
        $data['bedrooms'] = $form_data['beds'];
        $data['bathrooms'] = $form_data['baths'];
        $data['Bsmt1_out'] = $form_data['basement'];
        $data['garage_type'] = $form_data['parking_type'];
        $data['message'] = "User Category : ".$form_data['user_category'].", User Type : ".$form_data['user_type'];
        $query = Enquiries::insert($data);
        if ($query) {
            $query1 = User::where('type', $data['agent_id'])->first();
            if ($query1) {
                $subject = "Housen.ca Home Valuation";
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
                // $content1 = [$Name,$AgentEmail,$AgentPhone,'','','','','','','','',env('APP_NAME'),'',env('HOUSENFRONTURL')];
                // $EmailContent = str_replace($content,$content1,$sent_message->content);
                // $EmailSubject = $sent_message->subject;
                // $subject = $EmailSubject;
                // $message = $EmailContent;
                $getSuperAdmin = getSuperAdmin();
                $adminEmail = getAdmin($request->agent_id);
                $sent_token_url = sendEmail("SMTP", env('MAIL_FROM'), $adminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $message, "HomeController->SubmitHomeValue", "", env('HOMEVALUE'));
                $notification_data = [
                    "ContactName" => $form_data['name'],
                    "Email" => $form_data['email'],
                    "Phone" => $form_data['phone'],
                    "Message" => "User Category : ".$form_data['user_category'].", User Type : ".$form_data['user_type'],
                    "StatusId" => 0,
                    "AgentId" => $form_data['agent_id'],
                    "subject" => $form_data['name'] . ", " . env('HOME_VALUATION_NOTIFICATION_MSG'),
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
    public function GetBlogs_bcp_23aug(Request $request)
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
                $query = $saveData = BlogModel::where('AdminId', $agentId)->where('Url', $blogUrl)->first();
                try {
                    if ($saveData->Clicks)
                        $saveData->Clicks = $saveData->Clicks + 1;
                    else
                        $saveData->Clicks = 1;
                    $saveData->save();
                    foreach ($query as $key => $value) {
                        $value->MainImg = env('APP_URL') . $value->MainImg;
                    }
                } catch (\Throwable $th) {
                    $query->MainImg = env('APP_URL') . $query->MainImg;
                }
                $data['blogDetail'] = $query;
                return $data;
            }
            $currentPage = $form_data['currentPage'];
            $limit = 9;
            $offset = $currentPage - 1;
            $start = ($offset * $limit);
            $query = BlogModel::where('AdminId', $agentId);
            $mostSearches = BlogModel::where('AdminId', $agentId)->orderBy('Clicks', 'desc')->limit(2)->get();
            $topPost = BlogModel::where('AdminId', $agentId)->orderBy('id', 'asc')->limit($limit)->get();
            try {
                foreach ($topPost as $key => $value) {
                    $value->MainImg = env('APP_URL') . $value->MainImg;
                }
                foreach ($mostSearches as $key => $value) {
                    $value->MainImg = env('APP_URL') . $value->MainImg;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
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
            $total = $query->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $query = $query->offset($start);
            $query = $query->limit($limit);
            $query = $query->orderBy('id', 'desc');
            $records = $query->get();
            foreach ($records as $key => $value) {
                $value->MainImg = env('APP_URL') . $value->MainImg;
            }
            $data['records'] = $records;
            $data['total'] = $total;
            $data['currentPage'] = $currentPage;
            $data['totalPages'] = $totalPages;
            $data['topPost'] = $topPost;
            $data['mostSearches'] = $mostSearches;
            return $data;
        }
    }
    public function GetBlogs(Request $request)
    {
        $limit = 9;
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
                $query = $saveData = BlogModel::where('AdminId', $agentId)->where('Url', $blogUrl)->first();
                try {
                    if ($saveData->Clicks)
                        $saveData->Clicks = $saveData->Clicks + 1;
                    else
                        $saveData->Clicks = 1;
                    $saveData->save();
                    foreach ($query as $key => $value) {
                        $value->MainImg = env('APP_URL') . $value->MainImg;
                    }
                } catch (\Throwable $th) {
                    $query->MainImg = env('APP_URL') . $query->MainImg;
                }
                $data['blogDetail'] = $query;
                return $data;
            }

            $currentPage = $form_data['currentPage'];

            $offset = $currentPage - 1;
            $start = ($offset * $limit);

            $query = BlogModel::where('AdminId', $agentId);
            $mostSearches = BlogModel::where('AdminId', $agentId)->orderBy('Clicks', 'desc')->limit(2)->get();
            $topPost = BlogModel::where('AdminId', $agentId)->orderBy('updated_at', 'desc')->limit($limit)->get();
            try {
                foreach ($topPost as $key => $value) {
                    $value->MainImg = env('APP_URL') . $value->MainImg;
                }
                foreach ($mostSearches as $key => $value) {
                    $value->MainImg = env('APP_URL') . $value->MainImg;
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
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
                $limit = 6;
                $query = $query->where('Url', '!=', $currentBlog);
            }

            $total = $query->count();
            $lastPage = $total % $limit;
            if ($lastPage == 0) {
                $totalPages = $total / $limit;
            } else {
                $totalPages = floor($total / $limit) + 1;
            }
            $query = $query->offset($start);
            $query = $query->limit($limit);
            $query = $query->orderBy('id', 'desc');
            $records = $query->get();
            foreach ($records as $key => $value) {
                $value->MainImg = env('APP_URL') . $value->MainImg;
            }
            $data['records'] = $records;
            $data['total'] = $total;
            $data['currentPage'] = $currentPage;
            $data['totalPages'] = $totalPages;
            $data['topPost'] = $topPost;
            $data['mostSearches'] = $mostSearches;
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
                foreach ($query as $key => $value) {
                    $value->MainImg = env('APP_URL') . $value->MainImg;
                }
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
        // $IpAddress = \Request::getClientIp(true);
        // // third party api to get city name by ip address
        // $geopluginURL='http://www.geoplugin.net/php.gp?ip='.$IpAddress;
        // $addrDetailsArr = unserialize(file_get_contents($geopluginURL));
        // /*Get City name by return array*/
        // $city = $addrDetailsArr['geoplugin_city'];
        //
        $offset = $currentPage - 1;
        $start = ($offset * $limit);
        $response_data = DB::table($table)
            ->leftJoin('retspropertydataimages', $table . '.Ml_num', '=', 'retspropertydataimages.listingID')
            ->select($table . '.id', $table . '.Addr', $table . '.Bath_tot', $table . '.Br', $table . '.Addr', $table . '.Municipality', $table . '.Sqft', $table . '.Lp_dol', $table . '.Type_own1_out', $table . '.SlugUrl', 'retspropertydataimages.s3_image_url');

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
            $countArr = array();
            if (!isset($form_data['onlyCity'])) {
                if (count($city) > 0) {
                    foreach ($city as $key => $value) {
                        $cityname = $value->CityName;
                        $community = RetsPropertyData::distinct('Community')->where('City', $cityname)->limit(4)->get('Community');
                        $count = RetsPropertyData::where('City', $cityname)->count();
                        $AllSubtype[$cityname] = $community;
                        $value->count = $count;
                        // $countArr[$cityname."Cnt"] = $count;
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
                $sent_token_url = sendEmail("SMTP", env('MAIL_FROM'), $adminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $message, "HomeController->SendListingRequest");
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
    // TRied With row Data
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
        $startDate = $c_date->modify("-12 month");
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

    public function marketStatsCitiesData(Request $request)
    {
        $cities = ProvinceTbl::select('Community', 'Municipality', 'Province', 'id')->where('Province', $request->province)->where('MunicipalityHeading', $request->municipality)->orderBy('id', 'asc')->get();
        $result = array(
            'city' => $cities,
        );
        return response($result, 200);
    }
    public function marketStatsFilterData(Request $request)
    {
        $field = $request->key;
        $field = "City";
        // $cities = getFiltersData($field);
        $municipality = ProvinceTbl::select('MunicipalityHeading', 'Province', 'id')->where('Province', $request->province)->distinct('MunicipalityHeading')->groupBy('MunicipalityHeading')->orderBy('id', 'asc')->get();
        $field = "Community";
        // $community = getFiltersData($field);

        $field = "PropertyType";
        $propertyType = getFiltersData($field);

        $result = array(
            // 'city' => $cities,
            // 'community' => $community,
            'propertyType' => $propertyType,
            'municipality' => $municipality,
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

        $queryArray = array();
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $propType   = $request->propertyType;
        $city       = $request->City;
        $community = $request->community;
        $propType   = $propType ? " AND PropertyType = '$propType'" : "";
        $city       =  $city ? " AND City = '$city'" : "";
        $community       =  $community ? " AND Community = '$community'" : "";
        $dtTime     =  $request->date ? $request->date : 6;
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $qry = "SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyDataPurged` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city  $community AND  Sp_dol > 0 GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ";
        $soldList = DB::select($qry);
        // "Status","A"
        $queryArray[] = $qry;
        $finalData = array(
            "totalSold" => $soldList,
            "queries" => $queryArray,
        );
        return response($finalData, 200);
    }
    // DONE

    public function UpdateMarketStats()
    {
        $propTypes="";
        $cities="";
        $communities="";
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $data = array();
        $sp_date = time();
        //$sp_date = strtotime('-1 month');
        $s_date = date("Y-m-01 00:00:00",$sp_date);
        $year = date("Y",$sp_date);
        $month = date("m",$sp_date);echo $month.$s_date;
        $delete = "DELETE FROM `market_stats` WHERE year = $year and month =  $month";
        $delete_run =  DB::select($delete);
        $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
        $active = DB::select($activeQuery);
        foreach ($active as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"activeCounts",
                "month"=>$months,
                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->activeCounts
            );
        }
        echo "inserted";
        MarketStats::insert($data);
        $data = array();
        $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Timestamp_sql >= '$s_date'  $propType $city  group by date "; // DESC
        $soldList = DB::select($soldQuery);
        foreach ($soldList as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"soldCounts",
                "month"=>$months,

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->soldCounts
            );
        }
        $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
        $finalPriceData = DB::select($newListQuery);
        foreach ($finalPriceData as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"NewListounts",
                "month"=>$months,
                "city"=>$cities,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListounts
            );
        }
        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
        $active = DB::select($avgDomQuery);
        foreach ($active as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"avgDom",
                "month"=>$months,

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->avgDom
            );
        }
        $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
        $soldList = DB::select($qry);
        foreach ($soldList as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"priceAvg",
                "month"=>$months,

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->priceAvg
            );
        }
        $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND Timestamp_sql >= '$s_date' $propType $city $community ";
        $soldData = DB::select($query);
        $data[] = array(
            "name"=>"soldCount",
            "month"=>date("m",$sp_date),

            "city"=>$cities,
            "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
            "propertyType"=>$propTypes,
            "community"=>$communities,
            "year"=>date("Y",$sp_date),
            "value"=>$soldData[0]->soldCount
        );
        $query2 = "SELECT count(*) as activeCount from RetsPropertyData where inserted_time >= '$s_date' $propType $city $community  and Status = 'A' ";
        $activeData = DB::select($query2);
        $data[] = array(
            "name"=>"activeCount",
            "month"=>date("m",$sp_date),

            "city"=>$cities,
            "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
            "propertyType"=>$propTypes,
            "community"=>$communities,
            "year"=>date("Y",$sp_date),
            "value"=>$activeData[0]->activeCount
        );
        $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoApt = DB::select($CoOp);
        foreach ($condoApt as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CoApt",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CoApt
            );
        }
        $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Detached",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Detached
            );
        }
        $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($SemidetachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"SemiDetached",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->SemiDetached
            );
        }
        $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"FreeholdTownhouse",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->FreeholdTownhouse
            );
        }
        $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CondoTownhouse",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CondoTownhouse
            );
        }
        $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoTownhouse = DB::select($condoTownhouseQuery);
        foreach ($condoTownhouse as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CondoApt",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CondoApt
            );
        }
        $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
        foreach ($attRowTwnHouse as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Link",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Link
            );
        }
        $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $others = DB::select($othersQuery);
        foreach ($others as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Duplex",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,

                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Duplex
            );
        }
        $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $others = DB::select($othersQuery);
        foreach ($others as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"VacantLand",
                "month"=>$months,

                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->VacantLand
            );
        }
        $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
        $totalLease = DB::select($totalLeaseQuery);
        foreach ($totalLease as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"totalLease",
                "month"=>$months,

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->totalLease
            );
        }
        $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
        $newListData = DB::select($newListQuery);
        foreach ($newListData as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"NewListCounts",
                "month"=>$months,

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListCounts
            );
        }
        $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
        $medianPrice = DB::select($priceMedianQuery);
        $data[] = array(
            "name"=>"median_val",
            "month"=>date("m",$sp_date),

            "city"=>$cities,
            "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
            "propertyType"=>$propTypes,
            "community"=>$communities,
            "year"=>date("Y",$sp_date),
            "value"=>$medianPrice[0]->median_val
        );
        echo "inserted";
        MarketStats::insert($data);
        $data = array();
        $all_prop = array(
            "CoApt"=>"Co-Op Apt" ,
            "Detached"=>"Detached",
            "SemiDetached"=>"Semi-Detached",
            "FreeholdTownhouse"=>"Freehold Townhouse",
            "CondoTownhouse"=>"Condo Townhouse",
            "CondoApt"=>"Condo Apt",
            "Link"=>"Link",
            "Duplex"=>"Duplex",
            "VacantLand"=>"Vacant Land",
        );
        foreach ($all_prop as $key => $value) {
            $propTypes =$value;
            $propType = $value ? " AND PropertySubType = '$value'" : "";
            $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
            $active = DB::select($activeQuery);
            foreach ($active as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"activeCounts",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->activeCounts
                );
            }
            $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Timestamp_sql >= '$s_date'  $propType $city  group by date "; // DESC
            $soldList = DB::select($soldQuery);
            foreach ($soldList as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"soldCounts",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->soldCounts
                );
            }
            $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
            $finalPriceData = DB::select($newListQuery);
            foreach ($finalPriceData as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"NewListounts",
                    "month"=>$months,
                    "city"=>$cities,

                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->NewListounts
                );
            }
            $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
            $active = DB::select($avgDomQuery);
            foreach ($active as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"avgDom",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->avgDom
                );
            }
            $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
            $soldList = DB::select($qry);
            foreach ($soldList as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"priceAvg",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->priceAvg
                );
            }
            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND Timestamp_sql >= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            $data[] = array(
                "name"=>"soldCount",
                "month"=>date("m",$sp_date),

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$sp_date),
                "value"=>$soldData[0]->soldCount
            );
            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where inserted_time >= '$s_date' $propType $city $community  and Status = 'A' ";
            $activeData = DB::select($query2);
            $data[] = array(
                "name"=>"activeCount",
                "month"=>date("m",$sp_date),

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$sp_date),
                "value"=>$activeData[0]->activeCount
            );
            $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $condoApt = DB::select($CoOp);
            foreach ($condoApt as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"CoApt",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->CoApt
                );
            }
            $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $detached = DB::select($detachedQuery);
            foreach ($detached as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"Detached",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->Detached
                );
            }
            $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $detached = DB::select($SemidetachedQuery);
            foreach ($detached as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"SemiDetached",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->SemiDetached
                );
            }
            $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $detached = DB::select($detachedQuery);
            foreach ($detached as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"FreeholdTownhouse",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->FreeholdTownhouse
                );
            }
            $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $detached = DB::select($detachedQuery);
            foreach ($detached as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"CondoTownhouse",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->CondoTownhouse
                );
            }
            $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $condoTownhouse = DB::select($condoTownhouseQuery);
            foreach ($condoTownhouse as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"CondoApt",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->CondoApt
                );
            }
            $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
            foreach ($attRowTwnHouse as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"Link",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->Link
                );
            }
            $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $others = DB::select($othersQuery);
            foreach ($others as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"Duplex",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->Duplex
                );
            }
            $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $others = DB::select($othersQuery);
            foreach ($others as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"VacantLand",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->VacantLand
                );
            }
            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
            $totalLease = DB::select($totalLeaseQuery);
            foreach ($totalLease as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"totalLease",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->totalLease
                );
            }
            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
            $newListData = DB::select($newListQuery);
            foreach ($newListData as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"NewListCounts",
                    "month"=>$months,

                    "city"=>$cities,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->NewListCounts
                );
            }
            $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
            $medianPrice = DB::select($priceMedianQuery);
            $data[] = array(
                "name"=>"median_val",
                "month"=>date("m",$sp_date),

                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$sp_date),
                "value"=>$medianPrice[0]->median_val
            );
            echo "inserted";
            MarketStats::insert($data);
            $data = array();
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->orderBy("City","ASC")->get();
            foreach ($All_city as $key => $value) {
                if (isset($value->City) && $value->City !="") {
                    $cities = $value->City;
                    $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                    $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                    $active = DB::select($activeQuery);
                    foreach ($active as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"activeCounts",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->activeCounts
                        );
                    }
                    $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Timestamp_sql >= '$s_date'  $propType $city  group by date "; // DESC
                    $soldList = DB::select($soldQuery);
                    foreach ($soldList as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"soldCounts",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->soldCounts
                        );
                    }
                    $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                    $finalPriceData = DB::select($newListQuery);
                    foreach ($finalPriceData as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"NewListounts",
                            "month"=>$months,
                            "city"=>$cities,

                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->NewListounts
                        );
                    }
                    $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                    $active = DB::select($avgDomQuery);
                    foreach ($active as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"avgDom",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->avgDom
                        );
                    }
                    $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                    $soldList = DB::select($qry);
                    foreach ($soldList as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"priceAvg",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->priceAvg
                        );
                    }
                    $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND Timestamp_sql >= '$s_date' $propType $city $community ";
                    $soldData = DB::select($query);
                    $data[] = array(
                        "name"=>"soldCount",
                        "month"=>date("m",$sp_date),

                        "city"=>$cities,
                        "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$sp_date),
                        "value"=>$soldData[0]->soldCount
                    );
                    $query2 = "SELECT count(*) as activeCount from RetsPropertyData where inserted_time >= '$s_date' $propType $city $community  and Status = 'A' ";
                    $activeData = DB::select($query2);
                    $data[] = array(
                        "name"=>"activeCount",
                        "month"=>date("m",$sp_date),

                        "city"=>$cities,
                        "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$sp_date),
                        "value"=>$activeData[0]->activeCount
                    );
                    $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $condoApt = DB::select($CoOp);
                    foreach ($condoApt as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"CoApt",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->CoApt
                        );
                    }
                    $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $detached = DB::select($detachedQuery);
                    foreach ($detached as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"Detached",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->Detached
                        );
                    }
                    $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $detached = DB::select($SemidetachedQuery);
                    foreach ($detached as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"SemiDetached",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->SemiDetached
                        );
                    }
                    $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $detached = DB::select($detachedQuery);
                    foreach ($detached as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"FreeholdTownhouse",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->FreeholdTownhouse
                        );
                    }
                    $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $detached = DB::select($detachedQuery);
                    foreach ($detached as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"CondoTownhouse",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->CondoTownhouse
                        );
                    }
                    $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $condoTownhouse = DB::select($condoTownhouseQuery);
                    foreach ($condoTownhouse as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"CondoApt",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->CondoApt
                        );
                    }
                    $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                    $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                    foreach ($attRowTwnHouse as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"Link",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->Link
                        );
                    }
                    $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                    $others = DB::select($othersQuery);
                    foreach ($others as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"Duplex",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->Duplex
                        );
                    }
                    $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                    $others = DB::select($othersQuery);
                    foreach ($others as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"VacantLand",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->VacantLand
                        );
                    }
                    $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                    $totalLease = DB::select($totalLeaseQuery);
                    foreach ($totalLease as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"totalLease",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->totalLease
                        );
                    }
                    $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                    $newListData = DB::select($newListQuery);
                    foreach ($newListData as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"NewListCounts",
                            "month"=>$months,

                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->NewListCounts
                        );
                    }
                    $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
                    $medianPrice = DB::select($priceMedianQuery);
                    $data[] = array(
                        "name"=>"median_val",
                        "month"=>date("m",$sp_date),

                        "city"=>$cities,
                        "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$sp_date),
                        "value"=>$medianPrice[0]->median_val
                    );
                    echo "inserted";
                    MarketStats::insert($data);
                    $data = array();
                    $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertySubType",$propTypes)->orderBy("Community","ASC")->get();
                    if (isset($All_community) && count($All_community) > 0){
                        foreach ($All_community as $key => $value) {
                            $communities = $value->Community;
                            $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                            $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                            $active = DB::select($activeQuery);
                            foreach ($active as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"activeCounts",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->activeCounts
                                );
                            }
                            $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Timestamp_sql >= '$s_date'  $propType $city  group by date "; // DESC
                            $soldList = DB::select($soldQuery);
                            foreach ($soldList as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"soldCounts",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->soldCounts
                                );
                            }
                            $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                            $finalPriceData = DB::select($newListQuery);
                            foreach ($finalPriceData as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"NewListounts",
                                    "month"=>$months,
                                    "city"=>$cities,

                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->NewListounts
                                );
                            }
                            $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                            $active = DB::select($avgDomQuery);
                            foreach ($active as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"avgDom",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->avgDom
                                );
                            }
                            $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                            $soldList = DB::select($qry);
                            foreach ($soldList as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"priceAvg",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->priceAvg
                                );
                            }
                            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND Timestamp_sql >= '$s_date' $propType $city $community ";
                            $soldData = DB::select($query);
                            $data[] = array(
                                "name"=>"soldCount",
                                "month"=>date("m",$sp_date),

                                "city"=>$cities,
                                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$sp_date),
                                "value"=>$soldData[0]->soldCount
                            );
                            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where inserted_time >= '$s_date' $propType $city $community  and Status = 'A' ";
                            $activeData = DB::select($query2);
                            $data[] = array(
                                "name"=>"activeCount",
                                "month"=>date("m",$sp_date),

                                "city"=>$cities,
                                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$sp_date),
                                "value"=>$activeData[0]->activeCount
                            );
                            $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $condoApt = DB::select($CoOp);
                            foreach ($condoApt as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"CoApt",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->CoApt
                                );
                            }
                            $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $detached = DB::select($detachedQuery);
                            foreach ($detached as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"Detached",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->Detached
                                );
                            }
                            $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $detached = DB::select($SemidetachedQuery);
                            foreach ($detached as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"SemiDetached",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->SemiDetached
                                );
                            }
                            $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $detached = DB::select($detachedQuery);
                            foreach ($detached as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"FreeholdTownhouse",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->FreeholdTownhouse
                                );
                            }
                            $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $detached = DB::select($detachedQuery);
                            foreach ($detached as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"CondoTownhouse",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->CondoTownhouse
                                );
                            }
                            $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $condoTownhouse = DB::select($condoTownhouseQuery);
                            foreach ($condoTownhouse as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"CondoApt",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->CondoApt
                                );
                            }
                            $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                            foreach ($attRowTwnHouse as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"Link",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->Link
                                );
                            }
                            $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date'  $city $community group by date   ORDER BY `date` ASC ";
                            $others = DB::select($othersQuery);
                            foreach ($others as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"Duplex",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->Duplex
                                );
                            }
                            $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                            $others = DB::select($othersQuery);
                            foreach ($others as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"VacantLand",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->VacantLand
                                );
                            }
                            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                            $totalLease = DB::select($totalLeaseQuery);
                            foreach ($totalLease as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"totalLease",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->totalLease
                                );
                            }
                            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                            $newListData = DB::select($newListQuery);
                            foreach ($newListData as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"NewListCounts",
                                    "month"=>$months,

                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->NewListCounts
                                );
                            }
                            $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
                            $medianPrice = DB::select($priceMedianQuery);
                            $data[] = array(
                                "name"=>"median_val",
                                "month"=>date("m",$sp_date),

                                "city"=>$cities,
                                "Timestamp"=>date("Y-m-d H:i:s",$sp_date),
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$sp_date),
                                "value"=>$medianPrice[0]->median_val
                            );
                            echo "inserted";
                            MarketStats::insert($data);
                            $data = array();
                        }
                    }

                }
            }
        }
    }

    public function Marketstates1st_city(Type $var = null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertySubType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $data = array();
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->orderBy("City","ASC")->get();
        foreach ($All_city as $key => $value) {
            if (isset($value->City) && $value->City !="") {
                $cities = $value->City;
                $city =  $cities ? ' AND City = "'.$cities.'"' : NULL;
                $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                $active = DB::select($activeQuery);
                foreach ($active as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"activeCounts",
                        "month"=>$months,
                        "city"=>$cities,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->activeCounts
                    );
                }
                $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Sp_date<>'0000-00-00' and Sp_date >= '$s_date'  $propType $city  group by date "; // DESC
                $soldList = DB::select($soldQuery);
                foreach ($soldList as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"soldCounts",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->soldCounts
                    );
                }
                $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                $finalPriceData = DB::select($newListQuery);
                foreach ($finalPriceData as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"NewListounts",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->NewListounts
                    );
                }
                $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                $active = DB::select($avgDomQuery);
                foreach ($active as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"avgDom",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->avgDom
                    );
                }
                $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                $soldList = DB::select($qry);
                foreach ($soldList as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"priceAvg",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->priceAvg
                    );
                }
                $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $condoApt = DB::select($CoOp);
                foreach ($condoApt as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"CoApt",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->CoApt
                    );
                }
                $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $detached = DB::select($detachedQuery);
                foreach ($detached as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"Detached",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->Detached
                    );
                }
                $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $detached = DB::select($SemidetachedQuery);
                foreach ($detached as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"SemiDetached",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->SemiDetached
                    );
                }
                $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $detached = DB::select($detachedQuery);
                foreach ($detached as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"FreeholdTownhouse",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->FreeholdTownhouse
                    );
                }
                $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $detached = DB::select($detachedQuery);
                foreach ($detached as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"CondoTownhouse",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->CondoTownhouse
                    );
                }
                $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $condoTownhouse = DB::select($condoTownhouseQuery);
                foreach ($condoTownhouse as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"CondoApt",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->CondoApt
                    );
                }
                $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                foreach ($attRowTwnHouse as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"Link",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->Link
                    );
                }
                $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $others = DB::select($othersQuery);
                foreach ($others as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"Duplex",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->Duplex
                    );
                }
                $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                $others = DB::select($othersQuery);
                foreach ($others as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"VacantLand",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->VacantLand
                    );
                }
                $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                $totalLease = DB::select($totalLeaseQuery);
                foreach ($totalLease as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"totalLease",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->totalLease
                    );
                }
                $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                $newListData = DB::select($newListQuery);
                foreach ($newListData as $key => $value) {
                    $month = strtotime("$value->date-05");
                    $months = date("m",$month);
                    $data[] = array(
                        "name"=>"NewListCounts",
                        "month"=>$months,
                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$month),
                        "value"=>$value->NewListCounts
                    );
                }
                MarketStats::insert($data);
                $data = array();

                $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertySubType",$propTypes)->orderBy("Community","ASC")->get();
                if (isset($All_community) && count($All_community) > 0){
                    foreach ($All_community as $key => $value) {
                        $communities = $value->Community;
                        $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                        $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                        $active = DB::select($activeQuery);
                        foreach ($active as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"activeCounts",
                                "month"=>$months,
                                "city"=>$cities,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->activeCounts
                            );
                        }
                        $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`inserted_time`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and inserted_time >= '$s_date'  $propType $city  group by date "; // DESC
                        $soldList = DB::select($soldQuery);
                        foreach ($soldList as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"soldCounts",
                                "month"=>$months,
                                "city"=>$cities,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->soldCounts
                            );
                        }
                        $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                        $finalPriceData = DB::select($newListQuery);
                        foreach ($finalPriceData as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"NewListounts",
                                "month"=>$months,
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->NewListounts
                            );
                        }
                        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                        $active = DB::select($avgDomQuery);
                        foreach ($active as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"avgDom",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->avgDom
                            );
                        }
                        $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                        $soldList = DB::select($qry);
                        foreach ($soldList as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"priceAvg",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->priceAvg
                            );
                        }
                        $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $condoApt = DB::select($CoOp);
                        foreach ($condoApt as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"CoApt",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->CoApt
                            );
                        }
                        $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $detached = DB::select($detachedQuery);
                        foreach ($detached as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"Detached",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->Detached
                            );
                        }
                        $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $detached = DB::select($SemidetachedQuery);
                        foreach ($detached as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"SemiDetached",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->SemiDetached
                            );
                        }
                        $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $detached = DB::select($detachedQuery);
                        foreach ($detached as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"FreeholdTownhouse",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->FreeholdTownhouse
                            );
                        }
                        $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $detached = DB::select($detachedQuery);
                        foreach ($detached as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"CondoTownhouse",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->CondoTownhouse
                            );
                        }
                        $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $condoTownhouse = DB::select($condoTownhouseQuery);
                        foreach ($condoTownhouse as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"CondoApt",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->CondoApt
                            );
                        }
                        $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                        foreach ($attRowTwnHouse as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"Link",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->Link
                            );
                        }
                        $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $others = DB::select($othersQuery);
                        foreach ($others as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"Duplex",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->Duplex
                            );
                        }
                        $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                        $others = DB::select($othersQuery);
                        foreach ($others as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"VacantLand",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->VacantLand
                            );
                        }
                        $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                        $totalLease = DB::select($totalLeaseQuery);
                        foreach ($totalLease as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"totalLease",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->totalLease
                            );
                        }
                        $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                        $newListData = DB::select($newListQuery);
                        foreach ($newListData as $key => $value) {
                            $month = strtotime("$value->date-05");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"NewListCounts",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->NewListCounts
                            );
                        }
                        MarketStats::insert($data);
                        $data = array();
                    }
                }

            }
        }
    }
    public function Marketstates1st(Type $var = null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertySubType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $data = array();
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $all_prop = array(
            "CoApt"=>"Co-Op Apt" ,
            "Detached"=>"Detached",
            "SemiDetached"=>"Semi-Detached",
            "FreeholdTownhouse"=>"Freehold Townhouse",
            "CondoTownhouse"=>"Condo Townhouse",
            "CondoApt"=>"Condo Apt",
            "Link"=>"Link",
            "Duplex"=>"Duplex",
            "VacantLand"=>"Vacant Land",
        );
        foreach ($all_prop as $key => $value) {
            $propTypes =$value;
            $key_name = $key;
            $propType = $value ? " AND PropertySubType = '$value'" : "";
            $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
            $active = DB::select($activeQuery);
            foreach ($active as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                "name"=>"activeCounts",
                "month"=>$months,
                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->activeCounts
            );
            }
            $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`inserted_time`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and inserted_time >= '$s_date'  $propType $city  group by date "; // DESC
            $soldList = DB::select($soldQuery);
            foreach ($soldList as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                "name"=>"soldCounts",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->soldCounts
            );
            }
            $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
            $finalPriceData = DB::select($newListQuery);
            foreach ($finalPriceData as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                "name"=>"NewListounts",
                "month"=>$months,
                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListounts
                 );
            }
            $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
            $active = DB::select($avgDomQuery);
            foreach ($active as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"avgDom",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->avgDom
                );
            }
            $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
            $soldList = DB::select($qry);
            foreach ($soldList as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"priceAvg",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->priceAvg
                );
            }
            $othersquery = "SELECT count(*) as '$key_name',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='$propTypes' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
            $others = DB::select($othersquery);
            foreach ($others as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>$key_name,
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->$key_name
                );
            }
            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
            $totalLease = DB::select($totalLeaseQuery);
            foreach ($totalLease as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"totalLease",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->totalLease
                );
            }
            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
            $newListData = DB::select($newListQuery);
            foreach ($newListData as $key => $value) {
                $month = strtotime("$value->date-05");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"NewListCounts",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->NewListCounts
                );
            }
            MarketStats::insert($data);
            $data = array();
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertySubType",$propTypes)->orderBy("City","ASC")->get();
            foreach ($All_city as $key => $value) {
                if (isset($value->City) && $value->City !="") {
                    $cities = $value->City;
                    $city =  $cities ? ' AND City = "'.$cities.'"' : NULL;
                    $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                    $active = DB::select($activeQuery);
                    foreach ($active as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"activeCounts",
                            "month"=>$months,
                            "city"=>$cities,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->activeCounts
                        );
                    }
                    $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`inserted_time`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and inserted_time >= '$s_date'  $propType $city  group by date "; // DESC
                    $soldList = DB::select($soldQuery);
                    foreach ($soldList as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"soldCounts",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->soldCounts
                        );
                    }
                    $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                    $finalPriceData = DB::select($newListQuery);
                    foreach ($finalPriceData as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"NewListounts",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->NewListounts
                        );
                    }
                    $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                    $active = DB::select($avgDomQuery);
                    foreach ($active as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"avgDom",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->avgDom
                        );
                    }
                    $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                    $soldList = DB::select($qry);
                    foreach ($soldList as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"priceAvg",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->priceAvg
                        );
                    }
                    $othersquery = "SELECT count(*) as '$key_name',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='$propTypes' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                    $others = DB::select($othersquery);
                    foreach ($others as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>$key_name,
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->$key_name
                        );
                    }
                    $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                    $totalLease = DB::select($totalLeaseQuery);
                    foreach ($totalLease as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"totalLease",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->totalLease
                        );
                    }
                    $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                    $newListData = DB::select($newListQuery);
                    foreach ($newListData as $key => $value) {
                        $month = strtotime("$value->date-05");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"NewListCounts",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->NewListCounts
                        );
                    }
                    MarketStats::insert($data);
                    $data = array();
                    $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertySubType",$propTypes)->orderBy("Community","ASC")->get();
                    if (isset($All_community) && count($All_community) > 0){
                        sleep(5);
                        foreach ($All_community as $key => $value) {
                            $communities = $value->Community;
                            $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                            $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
                            $active = DB::select($activeQuery);
                            foreach ($active as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"activeCounts",
                                    "month"=>$months,
                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->activeCounts
                                );
                            }
                            $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`inserted_time`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and inserted_time >= '$s_date'  $propType $city  group by date "; // DESC
                            $soldList = DB::select($soldQuery);
                            foreach ($soldList as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"soldCounts",
                                    "month"=>$months,
                                    "city"=>$cities,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->soldCounts
                                );
                            }
                            $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
                            $finalPriceData = DB::select($newListQuery);
                            foreach ($finalPriceData as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"NewListounts",
                                    "month"=>$months,
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->NewListounts
                                );
                            }
                            $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                            $active = DB::select($avgDomQuery);
                            foreach ($active as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"avgDom",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->avgDom
                                );
                            }
                            $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                            $soldList = DB::select($qry);
                            foreach ($soldList as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"priceAvg",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->priceAvg
                                );
                            }
                            $othersquery = "SELECT count(*) as '$key_name',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='$propTypes' and Timestamp_sql >= '$s_date' $city $community group by date   ORDER BY `date` ASC ";
                            $others = DB::select($othersquery);
                            foreach ($others as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>$key_name,
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->$key_name
                                );
                            }
                            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                            $totalLease = DB::select($totalLeaseQuery);
                            foreach ($totalLease as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"totalLease",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->totalLease
                                );
                            }
                            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                            $newListData = DB::select($newListQuery);
                            foreach ($newListData as $key => $value) {
                                $month = strtotime("$value->date-05");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"NewListCounts",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->NewListCounts
                                );
                            }
                            MarketStats::insert($data);
                            $data = array();
                        }
                    }

                }
            }
        }
    }
    public function Marketstates(Type $soldActive =null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertySubType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $data = array();
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $delete = "DELETE FROM `market_stats`";
        $delete_run =  DB::select($delete);

        $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' $community AND updated_time >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
        $active = DB::select($activeQuery);
        foreach ($active as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"activeCounts",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->activeCounts
            );
        }
        $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' $community and Sp_date<>'0000-00-00' and Sp_date >= '$s_date'  $propType $city  group by date "; // DESC
        $soldList = DB::select($soldQuery);
        foreach ($soldList as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"soldCounts",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->soldCounts
            );
        }
        $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $community $propType $city  GROUP BY  date";
        $finalPriceData = DB::select($newListQuery);
        foreach ($finalPriceData as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"NewListounts",
                "month"=>$months,
                "city"=>$cities,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListounts
            );
        }
        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
        $active = DB::select($avgDomQuery);
        foreach ($active as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"avgDom",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->avgDom
            );
        }
        $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
        $soldList = DB::select($qry);
        foreach ($soldList as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"priceAvg",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->priceAvg
            );
        }
        $CoOp = "SELECT count(*) as 'CoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Co-Op Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoApt = DB::select($CoOp);
        foreach ($condoApt as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CoApt",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CoApt
            );
        }
        $detachedQuery = "SELECT count(*) as Detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Detached",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Detached
            );
        }
        $SemidetachedQuery = "SELECT count(*) as SemiDetached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Semi-Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($SemidetachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"SemiDetached",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->SemiDetached
            );
        }
        $detachedQuery = "SELECT count(*) as 'FreeholdTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Freehold Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"FreeholdTownhouse",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->FreeholdTownhouse
            );
        }
        $detachedQuery = "SELECT count(*) as 'CondoTownhouse',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CondoTownhouse",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CondoTownhouse
            );
        }
        $condoTownhouseQuery = "SELECT count(*) as 'CondoApt',DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoTownhouse = DB::select($condoTownhouseQuery);
        foreach ($condoTownhouse as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"CondoApt",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->CondoApt
            );
        }
        $attRowTwnHouseQuery = "SELECT count(*) as Link,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Link' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
        foreach ($attRowTwnHouse as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Link",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Link
            );
        }
        $othersQuery = "SELECT count(*) as Duplex,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Duplex' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $others = DB::select($othersQuery);
        foreach ($others as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"Duplex",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->Duplex
            );
        }
        $othersQuery = "SELECT count(*) as VacantLand,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType ='Vacant Land' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $others = DB::select($othersQuery);
        foreach ($others as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"VacantLand",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->VacantLand
            );
        }
        $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
        $totalLease = DB::select($totalLeaseQuery);
        foreach ($totalLease as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"totalLease",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->totalLease
            );
        }
        $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
        $newListData = DB::select($newListQuery);
        foreach ($newListData as $key => $value) {
            $month = strtotime("$value->date-05");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"NewListCounts",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListCounts
            );
        }
        MarketStats::insert($data);
        $data = array();
        Echo "Script is looking fine";
    }
    public function Marketstate2nd(Type $var = null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        echo "<pre>";
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertySubType = '$propTypes'" : NULL;
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community   =  $communities ? " AND Community = '$communities'" : NULL;
        $dtTime = 24;
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $pr_date = "";
        for ($i = 1; $i <= $dtTime; $i++) {
            $j = (1+$i);
            $two_year = strtotime("-$i month");
            $last_month = strtotime("-$j month");
            $s_date = date('Y-m-01 00:00:00',$two_year);
            $prev_dates = date("Y-m-01 00:00:00",$last_month);
            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            $data[] = array(
                "name"=>"soldCount",
                "month"=>date("m",$two_year),
                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$two_year),
                "value"=>$soldData[0]->soldCount
            );
            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
            $activeData = DB::select($query2);
            $data[] = array(
                "name"=>"activeCount",
                "month"=>date("m",$two_year),
                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$two_year),
                "value"=>$activeData[0]->activeCount
            );
            MarketStats::insert($data);
            $data = array();
            $propType = $propTypes ? " AND PropertySubType = '$propTypes'" : NULL;
            $city =  $cities ? " AND City = '$cities'" : NULL;
            $community       =  $communities ? " AND Community = '$communities'" : NULL;
            $all_prop = array(
                "CoApt"=>"Co-Op Apt" ,
                "Detached"=>"Detached",
                "SemiDetached"=>"Semi-Detached",
                "FreeholdTownhouse"=>"Freehold Townhouse",
                "CondoTownhouse"=>"Condo Townhouse",
                "CondoApt"=>"Condo Apt",
                "Link"=>"Link",
                "Duplex"=>"Duplex",
                "VacantLand"=>"Vacant Land"
            );
            foreach ($all_prop as $key => $value) {
                $propTypes = $value;
                $propType = $value ? " AND PropertySubType = '$value'" : NULL;
                $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                $soldData = DB::select($query);
                $data[] = array(
                    "name"=>"soldCount",
                    "month"=>date("m",$two_year),
                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$two_year),
                    "value"=>$soldData[0]->soldCount
                );
                $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                $activeData = DB::select($query2);
                $data[] = array(
                    "name"=>"activeCount",
                    "month"=>date("m",$two_year),
                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$two_year),
                    "value"=>$activeData[0]->activeCount
                );
                MarketStats::insert($data);
                $data = array();
                $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertySubType",$propTypes)->orderBy("City","ASC")->get();
                foreach ($All_city as $key => $value) {
                    if (isset($value->City) && $value->City !="") {
                        $cities = $value->City;
                        $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                        $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                        $soldData = DB::select($query);
                        $data[] = array(
                            "name"=>"soldCount",
                            "month"=>date("m",$two_year),
                            "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$two_year),
                            "value"=>$soldData[0]->soldCount
                        );
                        $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                        $activeData = DB::select($query2);
                        $data[] = array(
                            "name"=>"activeCount",
                            "month"=>date("m",$two_year),
                            "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$two_year),
                            "value"=>$activeData[0]->activeCount
                        );
                        $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertySubType",$propTypes)->orderBy("Community","ASC")->get();
                        if (isset($All_community) && !empty($All_community)){
                            foreach ($All_community as $key => $value) {
                                $communities = $value->Community;
                                $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                                $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                                $soldData = DB::select($query);
                                $data[] = array(
                                    "name"=>"soldCount",
                                    "month"=>date("m",$two_year),
                                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$two_year),
                                    "value"=>$soldData[0]->soldCount
                                );
                                $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                                $activeData = DB::select($query2);
                                $data[] = array(
                                    "name"=>"activeCount",
                                    "month"=>date("m",$two_year),
                                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$two_year),
                                    "value"=>$activeData[0]->activeCount
                                );
                            }
                        }
                        MarketStats::insert($data);
                        $data = array();
                    }

                }
            }
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->orderBy("City","ASC")->get();
            foreach ($All_city as $key => $value) {
                if (isset($value->City) && $value->City !="") {
                    $cities = $value->City;
                    $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                    $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                    $soldData = DB::select($query);
                    $data[] = array(
                        "name"=>"soldCount",
                        "month"=>date("m",$two_year),
                        "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$two_year),
                        "value"=>$soldData[0]->soldCount
                    );
                    $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                    $activeData = DB::select($query2);
                    $data[] = array(
                        "name"=>"activeCount",
                        "month"=>date("m",$two_year),
                        "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                        "city"=>$cities,
                        "propertyType"=>$propTypes,
                        "community"=>$communities,
                        "year"=>date("Y",$two_year),
                        "value"=>$activeData[0]->activeCount
                    );
                    $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertyType",$propTypes)->orderBy("Community","ASC")->get();
                    if (isset($All_community) && !empty($All_community)){
                        foreach ($All_community as $key => $value) {
                            $communities = $value->Community;
                            $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                            $soldData = DB::select($query);
                            $data[] = array(
                                "name"=>"soldCount",
                                "month"=>date("m",$two_year),
                                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$two_year),
                                "value"=>$soldData[0]->soldCount
                            );
                            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                            $activeData = DB::select($query2);
                            $data[] = array(
                                "name"=>"activeCount",
                                "month"=>date("m",$two_year),
                                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$two_year),
                                "value"=>$activeData[0]->activeCount
                            );
                        }
                    }
                    MarketStats::insert($data);
                    $data = array();
                }

            }

        }
    }
    public function Marketstates2nd(Type $domAvgMedian = null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        echo "<pre>";
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : NULL;
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : NULL;
        $one_year = strtotime("-1 year");
        $data = array();
        $delete = "DELETE FROM `market_stats` WHERE name IN ('avgDom','priceAvg')";
        $delete_run =  DB::select($delete);
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
        $active = DB::select($avgDomQuery);
        foreach ($active as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"avgDom",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->avgDom
            );
        }
        $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
        $soldList = DB::select($qry);
        foreach ($soldList as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"priceAvg",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->priceAvg
            );
        }
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $all_prop = array(
            "Commercial"=>"Commercial" ,
            "Condos"=> "Condos",
            "Residential"=>"Residential",
        );
        foreach ($all_prop as $key => $value) {
            $propTypes =$value;
            $propType = $value ? " AND PropertyType = '$value'" : "";
            $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
            $active = DB::select($avgDomQuery);
            foreach ($active as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"avgDom",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->avgDom
                );
            }
            $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
            $soldList = DB::select($qry);

            foreach ($soldList as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"priceAvg",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->priceAvg
                );
            }
            MarketStats::insert($data);
            $data = array();
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertyType",$propTypes)->orderBy("City","ASC")->get();
            if (isset($All_city) && !empty($All_city)) {
                foreach ($All_city as $key => $value) {
                    if (isset($value->City) && $value->City !="") {
                        $cities = $value->City;
                        $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                        $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                        $active = DB::select($avgDomQuery);
                        foreach ($active as $key => $value) {
                            $month = strtotime("$value->date-01");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"avgDom",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->avgDom
                            );
                        }
                        $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                        $soldList = DB::select($qry);
                        foreach ($soldList as $key => $value) {
                            $month = strtotime("$value->date-01");
                            $months = date("m",$month);
                            $data[] = array(
                                "name"=>"priceAvg",
                                "month"=>$months,
                                "Timestamp"=>date("Y-m-d H:i:s",$month),
                                "city"=>$cities,
                                "propertyType"=>$propTypes,
                                "community"=>$communities,
                                "year"=>date("Y",$month),
                                "value"=>$value->priceAvg
                            );
                        }
                        $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->where("PropertyType",$propTypes)->distinct("Community")->orderBy("Community","ASC")->get();
                        if (isset($All_community) && !empty($All_community)){
                            foreach ($All_community as $key => $value) {
                                $communities = $value->Community;
                                $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                                $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date  ";
                                $active = DB::select($avgDomQuery);
                                foreach ($active as $key => $value) {
                                    $month = strtotime("$value->date-01");
                                    $months = date("m",$month);
                                    $data[] = array(
                                        "name"=>"avgDom",
                                        "month"=>$months,
                                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                                        "city"=>$cities,
                                        "propertyType"=>$propTypes,
                                        "community"=>$communities,
                                        "year"=>date("Y",$month),
                                        "value"=>$value->avgDom
                                    );
                                }
                                $qry = "SELECT AVG(ListPrice) as priceAvg ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Timestamp_sql >= '$s_date' $propType $city $community group by date";
                                $soldList = DB::select($qry);
                                foreach ($soldList as $key => $value) {
                                    $month = strtotime("$value->date-01");
                                    $months = date("m",$month);
                                    $data[] = array(
                                        "name"=>"priceAvg",
                                        "month"=>$months,
                                        "Timestamp"=>date("Y-m-d H:i:s",$month),
                                        "city"=>$cities,
                                        "propertyType"=>$propTypes,
                                        "community"=>$communities,
                                        "year"=>date("Y",$month),
                                        "value"=>$value->priceAvg
                                    );
                                }
                            }
                        }

                    }
                    MarketStats::insert($data);
                    $data = array();
                }
            }
        }
    }
    public function Marketstates3rd(Type $absorptionData=null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        echo "<pre>";
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : NULL;
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community   =  $communities ? " AND Community = '$communities'" : NULL;
        $dtTime = 24;
        $delete = "DELETE FROM `market_stats` WHERE name IN ('soldCount','activeCount')";
        $delete_run =  DB::select($delete);
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $pr_date = "";
        for ($i = 1; $i <= $dtTime; $i++) {
            $j = (1+$i);
            $two_year = strtotime("-$i month");
            $last_month = strtotime("-$j month");
            $s_date = date('Y-m-01 00:00:00',$two_year);
            $prev_dates = date("Y-m-01 00:00:00",$last_month);
            $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            $data[] = array(
                "name"=>"soldCount",
                "month"=>date("m",$two_year),
                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$two_year),
                "value"=>$soldData[0]->soldCount
            );
            $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
            $activeData = DB::select($query2);
            $data[] = array(
                "name"=>"activeCount",
                "month"=>date("m",$two_year),
                "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$two_year),
                "value"=>$activeData[0]->activeCount
            );
            MarketStats::insert($data);
            $data = array();
            $propType = $propTypes ? " AND PropertyType = '$propTypes'" : NULL;
            $city =  $cities ? " AND City = '$cities'" : NULL;
            $community       =  $communities ? " AND Community = '$communities'" : NULL;
            $all_prop = array(
                "Commercial"=>"Commercial" ,
                "Condos"=> "Condos",
                "Residential"=>"Residential",
            );
            foreach ($all_prop as $key => $value) {
                $propTypes = $value;
                $propType = $value ? " AND PropertyType = '$value'" : NULL;
                $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                $soldData = DB::select($query);
                $data[] = array(
                    "name"=>"soldCount",
                    "month"=>date("m",$two_year),
                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$two_year),
                    "value"=>$soldData[0]->soldCount
                );
                $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                $activeData = DB::select($query2);
                $data[] = array(
                    "name"=>"activeCount",
                    "month"=>date("m",$two_year),
                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$two_year),
                    "value"=>$activeData[0]->activeCount
                );
                MarketStats::insert($data);
                $data = array();
                $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertyType",$propTypes)->orderBy("City","ASC")->get();
                foreach ($All_city as $key => $value) {
                    if (isset($value->City) && $value->City !="") {
                        $cities = $value->City;
                        $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                        $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                        $soldData = DB::select($query);
                        $data[] = array(
                            "name"=>"soldCount",
                            "month"=>date("m",$two_year),
                            "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$two_year),
                            "value"=>$soldData[0]->soldCount
                        );
                        $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                        $activeData = DB::select($query2);
                        $data[] = array(
                            "name"=>"activeCount",
                            "month"=>date("m",$two_year),
                            "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$two_year),
                            "value"=>$activeData[0]->activeCount
                        );
                        $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->distinct("Community")->where("PropertyType",$propTypes)->orderBy("Community","ASC")->get();
                        if (isset($All_community) && !empty($All_community)){
                            foreach ($All_community as $key => $value) {
                                $communities = $value->Community;
                                $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                                $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' $propType $city $community ";
                                $soldData = DB::select($query);
                                $data[] = array(
                                    "name"=>"soldCount",
                                    "month"=>date("m",$two_year),
                                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$two_year),
                                    "value"=>$soldData[0]->soldCount
                                );
                                $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
                                $activeData = DB::select($query2);
                                $data[] = array(
                                    "name"=>"activeCount",
                                    "month"=>date("m",$two_year),
                                    "Timestamp"=>date("Y-m-d 00:00:00",$two_year),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$two_year),
                                    "value"=>$activeData[0]->activeCount
                                );
                            }
                        }
                        MarketStats::insert($data);
                        $data = array();
                        sleep(1);
                    }

                }
            }

        }
    }
    public function Marketstates4th(Type $propertyTypeDistribution=null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        echo "<pre>";
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : NULL;
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $data = array();
        $delete = "DELETE FROM `market_stats` WHERE name IN ('condoApt','detached','condoTownhouse','attRowTwnHouse','others')";
        $delete_run =  DB::select($delete);
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        $s_date = $s_date->format("Y-m-d H:i:s");

        $condoAptQuery = "SELECT count(*) as condoApt,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoApt = DB::select($condoAptQuery);
        foreach ($condoApt as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"condoApt",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->condoApt
            );
        }

        $detachedQuery = "SELECT count(*) as detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $detached = DB::select($detachedQuery);
        foreach ($detached as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"detached",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->detached
            );
        }

        $condoTownhouseQuery = "SELECT count(*) as condoTownhouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $condoTownhouse = DB::select($condoTownhouseQuery);
        foreach ($condoTownhouse as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"condoTownhouse",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->condoTownhouse
            );
        }
        $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Sp_date >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
        foreach ($attRowTwnHouse as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"attRowTwnHouse",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->attRowTwnHouse
            );
        }
        $othersQuery = "SELECT count(*) as others,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse')  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
        $others = DB::select($othersQuery);
        foreach ($others as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"others",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->others
            );
        }
        MarketStats::insert($data);
        $data = array();
        $all_prop = array(
            "Commercial"=>"Commercial" ,
            "Condos"=> "Condos",
            "Residential"=>"Residential",
        );
        foreach ($all_prop as $key => $value) {
            $propTypes =$value;
            $propType = $value ? " AND PropertyType = '$value'" : "";
            $condoAptQuery = "SELECT count(*) as condoApt,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $condoApt = DB::select($condoAptQuery);
            foreach ($condoApt as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"condoApt",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->condoApt
                );
            }

            $detachedQuery = "SELECT count(*) as detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $detached = DB::select($detachedQuery);
            foreach ($detached as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"detached",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->detached
                );
            }

            $condoTownhouseQuery = "SELECT count(*) as condoTownhouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $condoTownhouse = DB::select($condoTownhouseQuery);
            foreach ($condoTownhouse as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"condoTownhouse",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->condoTownhouse
                );
            }
            $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
            foreach ($attRowTwnHouse as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"attRowTwnHouse",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->attRowTwnHouse
                );
            }
            $othersQuery = "SELECT count(*) as others,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse')  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
            $others = DB::select($othersQuery);
            foreach ($others as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"others",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->others
                );
            }
            MarketStats::insert($data);
            $data = array();
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertyType",$propTypes)->orderBy("City","ASC")->get();
            foreach ($All_city as $key => $value) {
                if (isset($value->City) && $value->City !="") {
                    $cities = $value->City;
                    $city =  $cities ? ' AND City = "'.$cities.'"' : NULL;
                    $condoAptQuery = "SELECT count(*) as condoApt,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                    $condoApt = DB::select($condoAptQuery);
                    foreach ($condoApt as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"condoApt",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->condoApt
                        );
                    }

                    $detachedQuery = "SELECT count(*) as detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                    $detached = DB::select($detachedQuery);
                    foreach ($detached as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"detached",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->detached
                        );
                    }

                    $condoTownhouseQuery = "SELECT count(*) as condoTownhouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                    $condoTownhouse = DB::select($condoTownhouseQuery);
                    foreach ($condoTownhouse as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"condoTownhouse",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->condoTownhouse
                        );
                    }
                    $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                    $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                    foreach ($attRowTwnHouse as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"attRowTwnHouse",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->attRowTwnHouse
                        );
                    }
                    $othersQuery = "SELECT count(*) as others,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse') and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                    $others = DB::select($othersQuery);
                    foreach ($others as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"others",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->others
                        );
                    }
                    MarketStats::insert($data);
                    $data = array();
                    sleep(1);
                    $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->where("PropertyType",$propTypes)->distinct("Community")->orderBy("Community","ASC")->get();
                    if (isset($All_community) && !empty($All_community)){
                        foreach ($All_community as $key => $value) {
                            $communities = $value->Community;
                            $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                            $condoAptQuery = "SELECT count(*) as condoApt,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                            $condoApt = DB::select($condoAptQuery);
                            foreach ($condoApt as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"condoApt",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->condoApt
                                );
                            }

                            $detachedQuery = "SELECT count(*) as detached,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                            $detached = DB::select($detachedQuery);
                            foreach ($detached as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"detached",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->detached
                                );
                            }

                            $condoTownhouseQuery = "SELECT count(*) as condoTownhouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                            $condoTownhouse = DB::select($condoTownhouseQuery);
                            foreach ($condoTownhouse as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"condoTownhouse",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->condoTownhouse
                                );
                            }
                            $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                            $attRowTwnHouse = DB::select($attRowTwnHouseQuery);
                            foreach ($attRowTwnHouse as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"attRowTwnHouse",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->attRowTwnHouse
                                );
                            }
                            $othersQuery = "SELECT count(*) as others,DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse')  and Timestamp_sql >= '$s_date' $propType $city $community group by date   ORDER BY `date` ASC ";
                            $others = DB::select($othersQuery);
                            foreach ($others as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"others",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->others
                                );
                            }
                            MarketStats::insert($data);
                            $data = array();
                            sleep(1);
                        }
                    }

                }
            }
        }
    }
    public function Marketstates5th(Type $medianRental=null)
    {
        $propTypes = "";
        $dates = [];
        $soldListData = [];
        $activeListData = [];
        $newListData = [];
        echo "<pre>";
        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];
        $communities = "";
        $cities = "";
        $propType = $propTypes ? " AND PropertyType = '$propTypes'" : "";
        $city =  $cities ? " AND City = '$cities'" : "AND City = 'Toronto'";
        $community       =  $communities ? " AND Community = '$communities'" : "";
        $one_year = strtotime("-1 year");
        $data = array();
        $delete = "DELETE FROM `market_stats` WHERE name IN ('totalLease','NewListCounts','median_val')";
        $delete_run =  DB::select($delete);
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-24 month");
        echo $s_date = $s_date->format("Y-m-d H:i:s") ;
        $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
        $totalLease = DB::select($totalLeaseQuery);
        foreach ($totalLease as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"totalLease",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->totalLease
            );
        }
        $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
        $newListData = DB::select($newListQuery);
        foreach ($newListData as $key => $value) {
            $month = strtotime("$value->date-01");
            $months = date("m",$month);
            $data[] = array(
                "name"=>"NewListCounts",
                "month"=>$months,
                "Timestamp"=>date("Y-m-d H:i:s",$month),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$month),
                "value"=>$value->NewListCounts
            );
        }
        MarketStats::insert($data);
        $data = array();
        $dtTime =24;
        for ($i = 1; $i <= $dtTime; $i++) {
            $j = (1+$i);
            $two_year = strtotime("-$i month");
            $last_month = strtotime("-$j month");
            $s_date = date('Y-m-01 00:00:00',$two_year);
            $prev_dates = date("Y-m-01 00:00:00",$last_month);
            $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
            $medianPrice = DB::select($priceMedianQuery);
            $data[] = array(
                "name"=>"median_val",
                "month"=>date("m",$two_year),
                "Timestamp"=>date("Y-m-01 00:00:00",$two_year),
                "city"=>$cities,
                "propertyType"=>$propTypes,
                "community"=>$communities,
                "year"=>date("Y",$two_year),
                "value"=>$medianPrice[0]->median_val
            );
            $all_prop = array(
                "Commercial"=>"Commercial" ,
                "Condos"=> "Condos",
                "Residential"=>"Residential",
            );
            foreach ($all_prop as $key => $value) {
                $propTypes =$value;
                $propType = $value ? " AND PropertyType = '$value'" : "";
                $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
                $medianPrice = DB::select($priceMedianQuery);
                $data[] = array(
                    "name"=>"median_val",
                    "month"=>date("m",$two_year),
                    "Timestamp"=>date("Y-m-01 00:00:00",$two_year),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$two_year),
                    "value"=>$medianPrice[0]->median_val
                );
                MarketStats::insert($data);
                $data = array();
                $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertyType",$propTypes)->orderBy("City","ASC")->get();
                foreach ($All_city as $key => $value) {
                    if (isset($value->City) && $value->City !="") {
                        $cities = $value->City;
                        $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                        $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
                        $medianPrice = DB::select($priceMedianQuery);
                        $data[] = array(
                            "name"=>"median_val",
                            "month"=>date("m",$two_year),
                            "Timestamp"=>date("Y-m-01 00:00:00",$two_year),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$two_year),
                            "value"=>$medianPrice[0]->median_val
                        );
                        $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->where("PropertyType",$propTypes)->distinct("Community")->orderBy("Community","ASC")->get();
                        if (isset($All_community) && !empty($All_community)){
                            foreach ($All_community as $key => $value) {
                                $communities = $value->Community;
                                $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                                $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date , AVG(ListPrice) as median_val   FROM ( SELECT Timestamp_sql, ListPrice, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE ListPrice is NOT NULL AND PropertyStatus='Sale'  AND  Timestamp_sql >= '$prev_dates' AND  Timestamp_sql <= '$s_date' ORDER BY  `Timestamp_sql` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
                                $medianPrice = DB::select($priceMedianQuery);
                                $data[] = array(
                                    "name"=>"median_val",
                                    "month"=>date("m",$two_year),
                                    "Timestamp"=>date("Y-m-01 00:00:00",$two_year),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$two_year),
                                    "value"=>$medianPrice[0]->median_val
                                );
                            }
                        }
                        MarketStats::insert($data);
                        $data = array();
                        sleep(1);

                    }
                }
            }
        }
        $all_prop = array(
            "Commercial"=>"Commercial" ,
            "Condos"=> "Condos",
            "Residential"=>"Residential",
        );
        foreach ($all_prop as $key => $value) {
            $propTypes =$value;
            $propType = $value ? " AND PropertyType = '$value'" : "";
            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
            $totalLease = DB::select($totalLeaseQuery);
            foreach ($totalLease as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"totalLease",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->totalLease
                );
            }
            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
            $newListData = DB::select($newListQuery);
            foreach ($newListData as $key => $value) {
                $month = strtotime("$value->date-01");
                $months = date("m",$month);
                $data[] = array(
                    "name"=>"NewListCounts",
                    "month"=>$months,
                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                    "city"=>$cities,
                    "propertyType"=>$propTypes,
                    "community"=>$communities,
                    "year"=>date("Y",$month),
                    "value"=>$value->NewListCounts
                );
            }
            MarketStats::insert($data);
            $data = array();
            $All_city = DB::table("RetsPropertyData")->select("City")->distinct("City")->where("PropertyType",$propTypes)->orderBy("City","ASC")->get();
            foreach ($All_city as $key => $value) {
                if (isset($value->City) && $value->City !="") {
                    $cities = $value->City;
                    $city =  $cities ? ' AND City = "'.$cities.'"' : "AND City = 'Toronto'";
                    $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                    $totalLease = DB::select($totalLeaseQuery);
                    foreach ($totalLease as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"totalLease",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->totalLease
                        );
                    }
                    $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                    $newListData = DB::select($newListQuery);
                    foreach ($newListData as $key => $value) {
                        $month = strtotime("$value->date-01");
                        $months = date("m",$month);
                        $data[] = array(
                            "name"=>"NewListCounts",
                            "month"=>$months,
                            "Timestamp"=>date("Y-m-d H:i:s",$month),
                            "city"=>$cities,
                            "propertyType"=>$propTypes,
                            "community"=>$communities,
                            "year"=>date("Y",$month),
                            "value"=>$value->NewListCounts
                        );
                    }
                    $All_community = DB::table("RetsPropertyData")->select("Community")->where("City",$cities)->where("PropertyType",$propTypes)->distinct("Community")->orderBy("Community","ASC")->get();
                    if (isset($All_community) && !empty($All_community)){
                        foreach ($All_community as $key => $value) {
                            $communities = $value->Community;
                            $community       =  $communities ? ' AND Community = "'.$communities.'"' : "";
                            $totalLeaseQuery = "SELECT  count(ListPrice) as totalLease ,  DATE_FORMAT(`Timestamp_sql`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease'  and Timestamp_sql > '$s_date' $propType $city $community group by date  ";
                            $totalLease = DB::select($totalLeaseQuery);
                            foreach ($totalLease as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"totalLease",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->totalLease
                                );
                            }
                            $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(ListPrice) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
                            $newListData = DB::select($newListQuery);
                            foreach ($newListData as $key => $value) {
                                $month = strtotime("$value->date-01");
                                $months = date("m",$month);
                                $data[] = array(
                                    "name"=>"NewListCounts",
                                    "month"=>$months,
                                    "Timestamp"=>date("Y-m-d H:i:s",$month),
                                    "city"=>$cities,
                                    "propertyType"=>$propTypes,
                                    "community"=>$communities,
                                    "year"=>date("Y",$month),
                                    "value"=>$value->NewListCounts
                                );
                            }
                            MarketStats::insert($data);
                            $data = array();
                            sleep(1);
                        }
                    }

                }
            }
        }
    }
    public function soldActive(Request $request)
    {
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $dates = [];
        $queryArray = array();
        $soldListData = [];
        $activeListData = [];
        $newListData = [];

        $soldListDataTemp = [];
        $activeListDataTemp = [];
        $newListDataTemp = [];

        $propType   = $request->propertyType;
        $city       = $request->City;
        $community = $request->community;
        // $propType   = $propType ? " AND PropertySubType = '$propType'" : "";
        $propType   = $propType ? " AND PropertyType = '$propType'" : "AND PropertyType = ''";
        $city       =  $city ? " AND City = '$city'" : "";
        $community       =  $community ? " AND Community = '$community'" : "";
        $dtTime     =  $request->date ? $request->date : 6;
        $c_date = new DateTime();
        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        // $soldQuery = " SELECT  COUNT(*) as soldCounts ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where   PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date'  $propType $city $community  group by date "; // DESC
        // $soldList = DB::select($soldQuery);

        // $activeQuery = " SELECT  COUNT(*) as activeCounts ,  DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND updated_time >= '$s_date'  $propType $city $community  group by date   ORDER BY `date` ASC"; // DESC
        // $active = DB::select($activeQuery);

        // $newListQuery = "SELECT   DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListounts FROM `RetsPropertyData` WHERE inserted_time >= '$s_date' $propType $city $community GROUP BY  date";
        // $finalPriceData = DB::select($newListQuery);

        $soldQuery = "SELECT  value as soldCounts ,  CONCAT(market_stats.month, '-', market_stats.year) as date FROM `market_stats` where  name like 'soldCounts' $community and Timestamp >= '$s_date'  $propType $city  group by date ORDER BY date asc"; // DESC
        $soldList = DB::select($soldQuery);


        $activeQuery = " SELECT  value as activeCounts ,  CONCAT(market_stats.month, '-', market_stats.year) as date FROM `market_stats` where   name like 'activeCounts' $community AND Timestamp >= '$s_date'  $propType $city   group by date   ORDER BY `date` ASC"; // DESC
        $active = DB::select($activeQuery);

        $newListQuery = "SELECT  value as NewListounts ,  CONCAT(market_stats.month, '-', market_stats.year) as date FROM `market_stats` WHERE name like 'NewListounts' AND Timestamp >= '$s_date' $community $propType $city  GROUP BY  date  ORDER BY `date` ASC";
        $finalPriceData = DB::select($newListQuery);

        $queryArray['newListQuery'] = $newListQuery;
        $queryArray['activeQuery'] = $activeQuery;
        $queryArray['soldQuery'] = $soldQuery;

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
            "queries" => $queryArray,
        );
        return response($finalData, 200);
    }
    //done
    public function domAvgMedian(Request $request)
    {
        $queryArray = [];
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceDate = [];
        $dates = [];
        $priceDataTemp = [];
        $propType   = $request->propertyType;
        $city       = $request->City;
        $community = $request->community;
        $propType   = $propType ? " AND PropertyType = '$propType'" : "AND PropertyType = ''";
        $city       =  $city ? " AND City = '$city'" : "";
        $community       =  $community ? " AND Community = '$community'" : "";
        $dtTime     =  $request->date ? $request->date : 6;
        $c_date     = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $avgDom = array();
        // $avgDomQuery = "SELECT avg(Dom) as avgDom ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community group by date  ";
        // //  dd($avgDomQuery);
        // $avgDom = DB::select($avgDomQuery);
        // $finalPriceData = [];
        // $finalData = [];
        // $c_date = new DateTime();
        // $qry = "SELECT AVG(Sp_dol) as priceAvg ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community group by date";
        // $priceData = DB::select($qry);

        $avgDomQuery = "SELECT value as avgDom ,  DATE_FORMAT(`Timestamp`,'%Y-%m') as date FROM `market_stats` where name like 'avgDom' and Timestamp >= '$s_date' $propType $city $community group by date  ";
        //  dd($avgDomQuery);
        $avgDom = DB::select($avgDomQuery);
        $finalPriceData = [];
        $finalData = [];
        $c_date = new DateTime();
        $qry = "SELECT value as priceAvg ,  DATE_FORMAT(`Timestamp`,'%Y-%m') as date FROM `market_stats` where name like 'priceAvg' and Timestamp >= '$s_date' $propType $city $community group by date";
        $priceData = DB::select($qry);
        $queryArray['avgMedian'] = $qry;
        $queryArray['avgDomQuery'] = $avgDomQuery;

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
            "dom" => $avgDom,
            "queries" => $queryArray,
        );
        return response($finalData, 200);
    }
    // done
    public function medianRental(Request $request)
    {
        $queryArray = [];
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceMedianData = [];
        $totalLeaseTemp = [];
        $totalLeaseData = [];
        $dateData = [];
        $newListDataTemp = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        //$propType  = $propType ? " AND PropertySubType = '$propType'" : "";
        $propType  = $propType ? " AND PropertyType = '$propType'" : "AND PropertyType =''";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 12;
        $c_date = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");

        // $totalLeaseQuery = "SELECT  count(Sp_dol) as totalLease ,  DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease' and Sp_date<>'0000-00-00' and Sp_date > '$s_date' $propType $city $community group by date  ";
        // $totalLease = DB::select($totalLeaseQuery);

        // $newListQuery = "SELECT  DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(Sp_dol) as NewListCounts FROM `RetsPropertyData` WHERE inserted_time > '$s_date' $propType $city  GROUP BY  date";
        // $newListData = DB::select($newListQuery);
        $totalLeaseQuery = "SELECT  value as totalLease ,  DATE_FORMAT(`Timestamp`,'%Y-%m') as date FROM `market_stats` WHERE name like 'totalLease'  and Timestamp > '$s_date' $propType $city $community group by date  ";
        $totalLease = DB::select($totalLeaseQuery);
        $newListQuery = "SELECT  DATE_FORMAT(`Timestamp`,'%Y-%m') as date, value as NewListCounts FROM `market_stats` WHERE Timestamp > '$s_date' and name like 'NewListCounts' $propType $city  GROUP BY  date";
        $newListData = DB::select($newListQuery);
        $queryArray['newListDataQuery'] = $newListQuery;
        $queryArray['totalLeaseQuery'] = $totalLeaseQuery;

        // $priceMedianQuery = "SELECT   avg(Sp_dol) as priceAvg,    DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community  GROUP BY  date";
        // $medianPrice = DB::select($priceMedianQuery);

        // 2022-05-05 00:000
        // 2022-04-05 00:000\\
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $priceMedianQuery = "";
        for ($i = 0; $i <= $dtTime; $i++) {
            if ($i == 0) {
                $s_date = $curr_date->modify("-0 month");
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $s_date->format("Y-m-d H:i:s");
            } else {
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $prev_dates;
            }
            $prev_dates = $prev_date->format("Y-m-d H:i:s");
            $d_date = $prev_date->format("Y-m");
            // $priceMedianQuery = "SELECT DATE_FORMAT(`Sp_date`,'%Y-%m') as date , AVG(Sp_dol) as median_val   FROM ( SELECT Sp_date, Sp_dol, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum FROM RetsPropertyDataPurged, (SELECT @rownum:=0) r WHERE Sp_dol is NOT NULL AND PropertyStatus='Sale'  AND  Sp_date >= '$prev_dates' AND  Sp_date <= '$s_date' ORDER BY  `Sp_date` DESC ) as dd WHERE dd.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) )";
            // $medianPrice = DB::select($priceMedianQuery);
            // $d = $medianPrice[0];
            // $medians = intval(round($d->median_val));
            $month = date("m",strtotime($prev_dates));
            $month_n = date("m",strtotime($s_date));
            if ($month==$month_n) {
                continue;
            }
            $priceMedianQuery = "SELECT DATE_FORMAT(`Timestamp`,'%Y-%m') as date , value as median_val   FROM market_stats  WHERE value is NOT NULL and name like  'median_val'  AND  month >= '$month' AND  month <= '$month_n'";
            $medianPrice = DB::select($priceMedianQuery);
            // print_r($month_n);echo "==<br>";
            if($medianPrice){
                $d = $medianPrice[0];
                $medians = $d->median_val;
                $date = $d->date;
            }else{
                $medians = 0;
                $date =$d_date ;
            }
            if (!$medians) {
                $date = $d_date;
            }
            // if (!in_array($d->date, $dateData)) {
                $dateData[] = $date;
            // }
            $priceMedianData[$date] = $medians;
        }
        $queryArray['totalLeaseQuery'] = $priceMedianQuery;

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
            "queries" => $queryArray,
        );
        return response($finalData, 200);
    }

    public function propertyTypeDistribution(Request $request)
    {
        $periodType = $request->periodType ? $request->periodType : "monthly";
        $priceMedianData = [];
        $totalLeaseTemp = [];
        $totalLeaseData = [];
        $queryArray = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND propertyType = '$propType'" : " AND propertyType = ''";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 6;
        $dtTime =6;
        $c_date = new DateTime();
        $s_date = $c_date->modify("-$dtTime month");
        $s_date = $s_date->format("Y-m-d H:i:s");

        // $condoAptQuery = "SELECT count(*) as condoApt FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Apt' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        // $condoApt = DB::select($condoAptQuery);

        // $detachedQuery = "SELECT count(*) as detached FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Detached' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        // $detached = DB::select($detachedQuery);

        // $condoTownhouseQuery = "SELECT count(*) as condoTownhouse FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Condo Townhouse' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        // $condoTownhouse = DB::select($condoTownhouseQuery);

        // $attRowTwnHouseQuery = "SELECT count(*) as attRowTwnHouse FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType='Att/Row/Twnhouse' and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        // $attRowTwnHouse = DB::select($attRowTwnHouseQuery);

        // $othersQuery = "SELECT count(*) as others FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and PropertySubType NOT IN ('Att/Row/Twnhouse','Condo Apt','Detached','Condo Townhouse') and Sp_date<>'0000-00-00' and Sp_date >= '$s_date' $propType $city $community ";
        // $others = DB::select($othersQuery);
        $condoAptQuery = "SELECT sum(value) as condoApt FROM `market_stats` where name like 'condoApt'  and  Timestamp >= '$s_date' $propType $city $community ";
        $condoApt = DB::select($condoAptQuery);

        $detachedQuery = "SELECT sum(value) as detached FROM `market_stats` where name like 'detached' and  Timestamp >= '$s_date' $propType $city $community ";
        $detached = DB::select($detachedQuery);

        $condoTownhouseQuery = "SELECT sum(value) as condoTownhouse FROM `market_stats`  where name like 'condoTownhouse' and Timestamp >= '$s_date' $propType $city $community ";
        $condoTownhouse = DB::select($condoTownhouseQuery);

        $attRowTwnHouseQuery = "SELECT sum(value) as attRowTwnHouse FROM `market_stats` where name like 'FreeholdTownhouse' and Timestamp >= '$s_date' $propType $city $community ";
        $attRowTwnHouse = DB::select($attRowTwnHouseQuery);

        $othersQuery = "SELECT sum(value) as others FROM `market_stats`  where name Not In('FreeholdTownhouse','CondoTownhouse','Detached','condoApt') and Timestamp >= '$s_date' $propType $city $community ";
        $others = DB::select($othersQuery);

        $queryArray['condoAptQuery'] = $condoAptQuery;
        $queryArray['detachedQuery'] = $detachedQuery;
        $queryArray['condoTownhouseQuery'] = $condoTownhouseQuery;
        $queryArray['attRowTwnHouseQuery'] = $attRowTwnHouseQuery;
        $queryArray['othersQuery'] = $othersQuery;

        $finalData = array(
            "condoApt" => isset($condoApt[0]) ? $condoApt[0]->condoApt : 0,
            "detached" => isset($detached[0]) ? $detached[0]->detached : 0,
            "condoTownhouse" => isset($condoTownhouse[0]) ? $condoTownhouse[0]->condoTownhouse : 0,
            "attRowTwnHouse" => isset($attRowTwnHouse[0]) ? $attRowTwnHouse[0]->attRowTwnHouse : 0,
            "others" => isset($others[0]) ? $others[0]->others : 0,
            "queries" => $queryArray,
        );
        return response($finalData, 200);
    }
    public function absorptionData(Request $request)
    {
        $tempDate = [];
        $activeTemp = [];
        $soldTemp = [];
        $queryArray = [];
        $absorptionData = [];
        $propType   = $request->propertyType;
        $city  = $request->City;
        $community = $request->community;
        $propType  = $propType ? " AND propertyType = '$propType'" : " AND propertyType LIKE ''";
        // $propType  = $propType ? " AND PropertySubType = '$propType'" : "";
        $city  =  $city ? " AND City = '$city'" : "";
        $community = $community ? " AND Community = '$community'" : "";
        $dtTime =  $request->date ? $request->date : 12;
        $curr_date = new DateTime();
        $date_prev = new DateTime();
        $prev_dates = "";
        $pr_date = "";
        // for ($i = 1; $i <= $dtTime; $i++) {
        //     if ($i == 1) {
        //         $s_date = $curr_date->modify("-0 month");
        //         $prev_date = $date_prev->modify("-1 month");
        //         $d_date = $s_date->format("Y-m");
        //         $s_date = $s_date->format("Y-m-d H:i:s");
        //     } else {
        //         $prev_date = $date_prev->modify("-1 month");
        //         $s_date = $prev_dates;
        //         $d_date = $pr_date;
        //     }
        //     $prev_dates = $prev_date->format("Y-m-d H:i:s");
        //     $pr_date = $prev_date->format("Y-m");
        //     // $query = "SELECT  count(*) as soldCount FROM `RetsPropertyDataPurged` where PropertyStatus='Sale'  AND  Sp_date >= '$prev_dates' AND  Sp_date <= '$s_date' $propType $city $community ";
        //     // $soldData = DB::select($query);
        //     // // $query2 = "SELECT  count(*) as activeCount ,DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND inserted_time >= '$prev_dates' AND  inserted_time <= '$s_date' $propType $city $community ";
        //     // $query2 = "SELECT count(*) as activeCount from RetsPropertyData where   inserted_time > '$prev_dates'  AND  inserted_time < '$s_date' $propType $city $community  and Status = 'A' ";
        //     // $activeData = DB::select($query2);
        //     // $query = "SELECT  value as soldCount FROM `market_stats` where name like 'soldCount'  AND  created_at >= '$prev_dates' AND  created_at <= '$s_date' $propType $city $community ";
        //     // $soldData = DB::select($query);
        //     // $query2 = "SELECT value as activeCount from market_stats  where name like 'activeCount'  and created_at > '$prev_dates'  AND  created_at < '$s_date' $propType $city $community  ";
        //     // $activeData = DB::select($query2);
        //     // echo"$query2 <br/> <br/>";
        //     // $soldData = $soldData[0];
        //     // $activeData = $activeData[0];
        //     // $tempDate[] = $d_date;
        //     // $activeTemp[$d_date] = $activeData->activeCount ? $activeData->activeCount : 0;
        //     // $soldTemp[$d_date] = $soldData->soldCount ? $soldData->soldCount : 0;
        // }
        for ($i = 1; $i <= $dtTime; $i++) {
            if ($i == 1) {
                $s_date = $curr_date->modify("-0 month");
                $prev_date = $date_prev->modify("-1 month");
                $d_date = $s_date->format("Y-m");
                $s_date = $s_date->format("Y-m-1 00:00:00");
            } else {
                $prev_date = $date_prev->modify("-1 month");
                $s_date = $prev_dates;
                $d_date = $pr_date;
            }
            $prev_dates = $prev_date->format("Y-m-1 00:00:00");
            $pr_date = $prev_date->format("Y-m");
            if ($prev_dates==$s_date) {
                continue;
            }
            $query = "SELECT  value as soldCount FROM `market_stats` where name like 'soldCount'  AND  Timestamp >= '$prev_dates' AND  Timestamp <= '$s_date' $propType $city $community ";
            $soldData = DB::select($query);
            // $query2 = "SELECT  count(*) as activeCount ,DATE_FORMAT(`updated_time`,'%Y-%m') as date FROM `RetsPropertyData` where   Status = 'A' AND inserted_time >= '$prev_dates' AND  inserted_time <= '$s_date' $propType $city $community ";
            $query2 = "SELECT value as activeCount from market_stats  where name like 'activeCount'  and Timestamp > '$prev_dates'  AND  Timestamp < '$s_date' $propType $city $community  ";
            $activeData = DB::select($query2);
            // echo"$query2 <br/> <br/>";
            // $soldData = $soldData[0];
            // $activeData = $activeData[0];
            $tempDate[] = $d_date;
            // $activeTemp[$d_date] = $activeData ? $activeData : 0;
            // $soldTemp[$d_date] = $soldData ? $soldData : 0;
            if (count($activeData) > 0) {
                foreach($activeData as $key =>$value){
                    $activeTemp[$d_date] = $value->activeCount ? $value->activeCount : 0;
                    }
            }else {
                    $activeTemp[$d_date] = 0;
            }
            if (count($soldData) > 0) {
                    foreach($soldData as $key =>$value){
                        $soldTemp[$d_date] = $value->soldCount ? $value->soldCount : 0;
                    }
            }else {
                $soldTemp[$d_date] = 0;
            }
        }
        $queryArray['soldCountQuery'] = $query;
        $queryArray['activeCountQuery'] = $query2;
        foreach ($tempDate as $key => $value) {
            $active = $activeTemp[$value];
            $sold = $soldTemp[$value];
            $absorption = 0;
            // if ($active && $sold) {
            //     $absorption = ceil($sold * 100 / $active);
            // }
             if ($active !=0) {
                $absorption = ceil($sold * 100 / $active);
            }
            $absorptionData[] = round($absorption);
        }
        $res = array(
            "date" => $tempDate,
            "absorptionData" => $absorptionData,
            "queries" => $queryArray,
        );
        return response($res, 200);
    }

    public function GetPreferenceData()
    {
        /*$data['PropertySubType'] = RetsPropertyData::select('PropertySubType')->distinct('PropertySubType')->get();
        $data['City'] = RetsPropertyData::select('City')->distinct('City')->get();
        $data['PropertyType'] = RetsPropertyData::select('PropertyType')->distinct('PropertyType')->get();*/

        $data['PropertySubType'] = DB::select("SELECT DISTINCT(PropertyType) FROM `RetsPropertyData`");
        $data['City'] = DB::select("SELECT DISTINCT(City) FROM `RetsPropertyData`");
        $data['PropertyType'] = DB::select("SELECT DISTINCT(PropertyType) FROM `RetsPropertyData`");
        return response()->json($data);
    }
    public function testStats(Request $request)
    {
        $listingId = $request->ListingId;
         $type = $request->type;
         $rpd = RetsPropertyData::where("ListingId",$listingId)->first();
         $data = [
             "isRpd" => 0,
             "isSold" => 0,
             "ListingId" => $listingId
         ];
         if ($rpd) {
             $data["isRpd"] = 1;
         }
        /* $rpdSold = RetsPropertyDataPurged::where("ListingId",$listingId)->first();
        if ($rpdSold) {
            $data["isSold"] = 1;
        }*/
        return response($data);
        dd(1);
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
        //dd($finalPriceData, $data1);
        $res = array(
            "date" => $tempDate,
            "absorptionData" => $absorptionData
        );
        return response($res, 200);
    }
    public function statsDataStore()
    {
        $city = PropertyConstants::GTACITY;
        $c_date = new DateTime();
        $startDate = $c_date->modify("-24 month");
        $startDate = $startDate->format('Y-m-d');
        // $query_for_count = "SELECT count(*) as counts,MONTH(Sp_date) AS month,YEAR(Sp_date) AS year, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date>'$startDate' and City= '$city'  group by date";
        // $count_data = DB::select($query_for_count);
        // AVG, Total price for sale And Count
        echo "Starting.....\n\n\n";
        echo "Started  For Sale \n\n";

        $datePeriod = $c_date->format('Y-m-d H:i:s');
        $s_date = $c_date->modify("-12 month");
        $s_date = $s_date->format("Y-m-d H:i:s");
        $activeQuery = "SELECT  MONTH(inserted_time) AS month,YEAR(inserted_time) AS year, DATE_FORMAT(`inserted_time`,'%Y-%m') as date, count(*) as counts FROM `RetsPropertyData` WHERE inserted_time<>'0000-00-00' and inserted_time >= '$startDate' AND  City= '$city' AND  Status = 'A'  group by date ";
        // $q = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') and PropertyStatus='Sale' and City= '$city' AND  Status = 'A'  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        // $active = DB::select($activeQuery);
        $query = " SELECT avg(Sp_dol) AS average , avg(Dom) as avgDom , sum(Sp_dol) AS priceTotal,count(*) as counts , MONTH(Sp_date) AS month,YEAR(Sp_date) AS year, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Sale' and Sp_date<>'0000-00-00' and Sp_date >= '$startDate' and City= '$city'  group by date "; // DESC
        $StatsData = DB::select($query);
        foreach ($StatsData as $key => $value) {
            $saveData = array(
                "Type" => "Sale",
                "TotalPriceForSale" => $value->priceTotal,
                "AvgPrice" => intval(round($value->average)),
                "Count" => $value->counts,
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "AvgDom" => $value->avgDom,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $check = array(
                "Type" => "Sale",
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $res = StatsData::UpdateOrCreate($check, $saveData);
        }

        // Total price for Rent
        echo "Started For Rent/Lease \n\n";
        $query = " SELECT avg(Sp_dol) AS average  , avg(Dom) as avgDom, sum(Sp_dol) AS priceTotal,count(*) as counts , MONTH(Sp_date) AS month,YEAR(Sp_date) AS year, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where PropertyStatus='Lease' and Sp_date<>'0000-00-00' and Sp_date >= '$startDate' and City= '$city'  group by date "; // DESC
        $StatsData = DB::select($query);
        foreach ($StatsData as $key => $value) {
            $saveData = array(
                "Type" => "Lease",
                "TotalPriceForRent" => $value->priceTotal,
                "AvgPrice" => intval(round($value->average)),
                "Count" => $value->counts,
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "AvgDom" => $value->avgDom,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $check = array(
                "Type" => "Lease",
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $res = StatsData::UpdateOrCreate($check, $saveData);
        }
        // Total price for All
        echo "Started  For All \n\n";
        $query = " SELECT avg(Sp_dol) AS average , avg(Dom) as avgDom, sum(Sp_dol) AS priceTotal,count(*) as counts , MONTH(Sp_date) AS month,YEAR(Sp_date) AS year, DATE_FORMAT(`Sp_date`,'%Y-%m') as date FROM `RetsPropertyDataPurged` where  Sp_date<>'0000-00-00' and Sp_date >= '$startDate' and City= '$city'  group by date "; // DESC
        $StatsData = DB::select($query);
        foreach ($StatsData as $key => $value) {
            $saveData = array(
                "Type" => "All",
                "TotalPriceForAll" => $value->priceTotal,
                "AvgPrice" => intval(round($value->average)),
                "Count" => $value->counts,
                "AvgDom" => $value->avgDom,
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $check = array(
                "Type" => "All",
                "TimePeriod" => $value->date,
                "Year" => $value->year,
                "Month" => $value->month,
                "Date" => $value->date . "-00",
            );
            $res = StatsData::UpdateOrCreate($check, $saveData);
        }
        echo "Stored data ....";
    }
    public function getSoldByAgent(Request $request)
    {

        $form_data = $request->all();
        $response_data = [];
        if (isset($form_data['OfficeName'])) {
            $office_name = $form_data['OfficeName'];
            $offices = explode('"', $office_name);
            $currentPage = $form_data['currentPage'];
            $limit = $form_data['limit'];
            $offset = $currentPage - 1;
            $start = ($offset * $limit);

            $query1 = RetsPropertyDataPurged::select(PropertyConstants::SELECT_DATA);
            $query1 = $query1->whereIn('Rltr', $offices);
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
    public function PopularSearch(Request $request)
    {
        $form_data = $request->all();
        $response_data = [];

        $currentPage = $form_data['currentPage'];
        $limit = $form_data['limit'];
        $offset = $currentPage - 1;
        $start = ($offset * $limit);
        $subtype = ['Detached', 'Semi-Detached', 'Att/Row/Twnhouse', 'Condo Townhouse', 'Condo Apt'];
        $minPrice = 300000;
        $maxPrice = 7000000;
        if (isset($form_data['status']) && $form_data['status'] != "") {
            $status = $form_data['status'];
            if ($status == "Sold") {
                $query1 = RetsPropertyDataPurged::select(PropertyConstants::SELECT_DATA);
            } else {
                $query1 = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Status', 'A');
                if ($status == "Sale") {
                    $query1 = $query1->where('PropertyStatus', 'Sale');
                }
                if ($status == "Rent") {
                    $query1 = $query1->where('PropertyStatus', 'Lease');
                }
            }
        } else {
            $query1 = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Status', 'A');
        }
        if (isset($form_data['city']) && $form_data['city'] != "") {
            $city = $form_data['city'];
            $query1 = $query1->where('City', $city);
        }

        if (isset($form_data['propertytype']) && $form_data['propertytype'] != "") {
            $propertytype = $form_data['propertytype'];
            $query1 = $query1->where('PropertyType', $propertytype);
        }
        // if(isset($form_data['subtype']) && $form_data['subtype']!=""){
        //     $subtype = $form_data['subtype'];
        //     $query1 = $query1->where('PropertySubType',$subtype);

        // }
        $query1 = $query1->whereIn('PropertySubType', $subtype);
        $query1 = $query1->where('ListPrice', '>=', (int)$minPrice)->where('ListPrice', '<=', (int)$maxPrice);
        $total = $query1->count();
        $lastPage = $total % $limit;
        if ($lastPage == 0) {
            $totalPages = $total / $limit;
        } else {
            $totalPages = floor($total / $limit) + 1;
        }
        $query1 = $query1->offset($start);
        $query1 = $query1->limit($limit);
        $query1 = $query1->get();
        $response_data['totalRecord'] = $total;
        $response_data['result'] = $query1;
        $response_data['currentPage'] = $currentPage;
        $response_data['offset'] = $offset;
        $response_data['limit'] = $limit;

        return response($response_data, 200);
    }
    public function preConstruction(Request $request)
    {
        $form_data = $request->all();
        $response_data = [];
        $currentPage = $form_data['currentPage'];
        $limit = $form_data['limit'];
        $offset = $currentPage - 1;
        $start = ($offset * $limit);

        $query1 = PreConstruction::select("id", "BuildingName", "Address", "City", "BuildingStatus", "MediaImage", "Bedroom", "Bathroom", "SaleStatus", "SizeRange", "PriceRange", "BuildingType", "Community", "Slug", "Completion");
        $total = $query1->count();
        $lastPage = $total % $limit;
        if ($lastPage == 0) {
            $totalPages = $total / $limit;
        } else {
            $totalPages = floor($total / $limit) + 1;
        }
        if (isset($form_data['cityname'])) {
            $query1 = $query1->where("City", $form_data['cityname']);
        }
        $query1 = $query1->where("Status", "Active");
        $response_data['totalRecord'] = $total;
        if (isset($form_data['LatestOrComminSoon'])) {

            $query1 = $query1->where("LatestOrComminSoon", $form_data['LatestOrComminSoon']);
            $limit = 12;
            $response_data['totalRecord'] = 12;
        }
        $query1 = $query1->offset($start);
        $query1 = $query1->limit($limit);
        $query1 = $query1->orderBy('created_at', 'Desc');
        $query1 = $query1->get();
        $cities = PreConstruction::select("City")->distinct("City")->get();
        $response_data['result'] = $query1;
        $response_data['currentPage'] = $currentPage;
        $response_data['offset'] = $offset;
        $response_data['limit'] = $limit;
        $response_data['City'] = $cities;
        return response($response_data, 200);
    }
    public function preConstructionDetail(Request $request)
    {
        $form_data = $request->all();
        $slug =  $form_data['slug'];
        $detail = PreConstruction::where('Slug', $slug)->where("Status", "Active")->first();

        if ($detail) {
            $builderId = $detail->BuilderId;
            $BuilderDetail = Builder::where('id', $builderId)->where("Status", "Active")->first();
        }
        $data['details'] = $detail;
        $data['BuilderDetail'] = $BuilderDetail;
        return $data;
    }

    public function getWebSettings() {
        $path = env('HOUSENFRONTJSONPATH')."websetting.json";
        $content = json_decode(file_get_contents($path), true);
        return response($content,200);
    }

    public function getSearchData() {
        $path = env('HOUSENFRONTJSONPATH')."data.json";
        //$path = "https://housen.ca/json/data.json";
        $content = json_decode(file_get_contents($path), true);

        /*$content = Storage::disk('public')->get('data.json');

        $content = json_decode($content,true);*/
        return response($content,200);
    }

    public function testProperty(Request $request) {
         $listingId = $request->ListingId;
         $type = $request->type;
         $rpd = RetsPropertyData::where("ListingId",$listingId)->first();
         $data = [
             "isRpd" => 0,
             "isSold" => 0
         ];
         if ($rpd) {
             $data["isRpd"] = 1;
         }
         $rpdSold = RetsPropertyDataPurged::where("ListingId",$listingId)->first();
        if ($rpdSold) {
            $data["isSold"] = 1;
        }
        return response($data);
     }

}
// git check
