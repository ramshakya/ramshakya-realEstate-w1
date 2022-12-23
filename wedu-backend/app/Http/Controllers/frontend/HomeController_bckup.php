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
                $response_data = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->orderBy('Dom', 'asc')->whereNotNull('ImageUrl')->limit(4)->get();
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
            $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag', 'TopBanner')
                ->where("AdminId", $request->agentId)
                ->first();
            $seo = Pages::select('MetaTitle', 'MetaDescription', 'MetaTags','Setting')->where('PageName', $PageName)
                ->where("AgentId", $request->agentId)
                ->first();
            $response['websetting'] = $websetting;
            if ($request->PageName == "Property details") {
                $seo->MetaDescription = "";
            }
            if($seo && $seo->Setting!=''){
                $response['pageSetting'] = json_decode($seo->Setting);
                // $response['pageSetting'] = ((array)$response['pageSetting']);
                $arrangeSection =  json_decode($response['pageSetting']->ArrangeSection);
                    $arrangeSections = [];
                    foreach ($arrangeSection[0] as $key => $value) {
                         $arrangeSections[] =  $value->value;
                    }

                unset($response['pageSetting']->ArrangeSection);
                $response['arrangeSections'] = $arrangeSections;
                // if(array_key_exists('ArrangeSection',$response['pageSetting'])){
                //     return gettype($response['pageSetting']);
                //     $arrangeSection =  json_decode($response['pageSetting']->ArrangeSection);
                //     $arrangeSections = [];
                //     foreach ($arrangeSection[0] as $key => $value) {
                //          $arrangeSections[] =  $value->value;
                //     }
                //     unset($response['pageSetting']->ArrangeSection);
                //     $response['arrangeSections'] = $arrangeSections;
                // }
                

                    unset($response['pageSetting']->ArrangeSection);
                    $response['arrangeSections'] = $arrangeSections;
                }

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
            $msg = "<h1>Hi SuperAdmin</h1>
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
            sendEmail("SMTP", env('FROM_EMAIL_DEV'), $adminEmail, $superAdminEmail, env('TEST_EMAIL'), $subject, $msg, "UserLogin - Signup");
            $response = ['success' => 'Enquiry Submitted'];
        } else {
            $response = ['errors' => 'Something went wrong!'];
        }
        return $response;
    }

    public function getProperties(Request $request)
    {
        //
        $requestData = $request->all();
        $featuredListings = [];
        $recentListings = [];
        $propertyList = [];
        $tmp_data = [];
        $agentId = $request->agentId;
        if (isset($requestData["featuredListing"]) && $requestData["featuredListing"]) {
            // $response_data = FeaturedListing::with('getProperty')->where("AgentId", $agentId)->limit(4)->get();
            $ids = FeaturedListing::where("AgentId", $request->AgentId)->pluck("ListingId")->toArray();
            $response_data = RetsPropertyData::whereIn("ListingId", $ids)->orWhere("City", PropertyConstants::FEATURED_CITY)->where('ListPrice', '>', PropertyConstants::FEATURED_PRICE)->orderBy("Dom", "ASC")->limit(4)->get();
            foreach ($response_data as $datum) {
                $datum = collect($datum)->all();
                // $datum = collect($datum["get_property"])->only(PropertyConstants::SELECT_DATA)->all();
                $datum["isOpenHouse"] = 0;
                //$datum["ListPrice"] = number_format($datum["ListPrice"]);
                $tmp_data[] = $datum;
            }
            $featuredListings = $tmp_data;
        }
        $ids = array();
        if (isset($requestData["recentListing"]) && $requestData["recentListing"]) {
            $recentListings = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->orderBy('Dom', 'asc')->whereNotNull('ImageUrl')->limit(4)->get();
            foreach ($recentListings as $key => $value) {
                $ids[] = $value->id;
            }
        }
        //
        if (isset($requestData["propertyList"]) && $requestData["propertyList"]) {
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
            $query->orderBy('Timestamp_sql', 'desc');
            $query->limit($limit);
            $propertyList = $query->get();
        }
        $response = [
            "recentListing" => $recentListings,
            "featuredListing" => $featuredListings,
            "propertyListing"  => $propertyList
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
            $query1 = User::where('type', $data['agent_id'])->first();
            if ($query1) {
                $subject = "Wedu.ca Home Valuation";
                $message = "<p>imformation of the user who is interested into home valuation , details listed below<p>";
                foreach ($data as $key => $value) {
                    $message .= '<p>' . $key . ' : ' . $value . '</p>';
                }
                $getSuperAdmin = getSuperAdmin();
                $adminEmail = getAdmin($request->agent_id);
                $sent_token_url = sendEmail("SMTP", "server@mail.com", $adminEmail, $getSuperAdmin, env('TEST_EMAIL'), $subject, $message, "HomeController->SubmitHomeValue");
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
            $city = CityData::where('AgentId', $agentId)->limit($limit)->get();
            $AllSubtype = array();
            if (!isset($form_data['onlyCity'])) {
                if (count($city) > 0) {
                    foreach ($city as $key => $value) {
                        $cityname = $value->CityName;
                        $community = RetsPropertyData::distinct('Community')->where('City', $cityname)->limit(4)->get('Community');
                        $AllSubtype[$cityname] = $community;
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
                $sent_token_url = sendEmail("SMTP", "server@mail.com", $adminEmail, $getSuperAdmin, env('TEST_EMAIL'), $subject, $message, "HomeController->SendListingRequest");
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
}
