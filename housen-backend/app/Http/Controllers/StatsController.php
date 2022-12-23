<?php

namespace App\Http\Controllers;

use App\Constants\PropertyConstants;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\MostSearchedCities;
use App\Models\SqlModel\Websetting;
use App\Models\UserTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\RetsPropertyDataPurged;

class StatsController extends Controller
{
    private $dateTime = array();
    private $pageUrl = array();

    public function search_city(Request $request)
    {
        $usertype = 'agent';
        $agentId = Auth::user()->id;
        return view("agent.stats.city", compact('usertype', 'agentId'));
    }
    public function getCityData(Request $request)
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
        $agentId = $getdata['agentId'];

        $CityStats = UserTracker::select('PageUrl', 'created_at')->where("AgentId", $agentId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'city' . '%')->groupBy('PageUrl');
        if (isset($getdata['CitiesLastDays'])) {
            $date = strtotime("-" . $getdata['CitiesLastDays'] . "day");
            $last = date('Y-m-d H:i:s', $date);
            $CityStats = $CityStats->where('created_at', '>=', $last);
        } else {
            $date = strtotime("-7 day");
            $last = date('Y-m-d H:i:s', $date);
            $CityStats =  $CityStats->where('created_at', '>=', $last);
        }
        if (isset($getdata['DateTo'])) {
            if (isset($getdata['DateTo'])) {
                if (isset($getdata['DateFrom'])) {
                    $getdata['DateFrom'];
                } else {
                    $getdata['DateFrom'] = date("Y-m-d");
                }
                $CityStats = $CityStats->whereDate('created_at', '<=', $getdata['DateTo'])->whereDate('created_at', '>=', $getdata['DateFrom']);
            }
        }


        $totalRecords = count($CityStats->get());
        $totalRecordswithFilter = count($CityStats->get());

        $records = $CityStats->skip($start)->take($rowperpage)->orderby('created_at', 'desc')->get();

        $srno = intval($start) + 1;
        foreach ($records as $record) {
            $trim = str_replace(env('HOUSENFRONTURL') . 'city/','', $record->PageUrl);

            DB::enableQueryLog();
            $Totalcount = UserTracker::where('PageUrl', 'like', '%' . $record->PageUrl . '%')->where('created_at', '>=', $last)->orderby('created_at', 'desc')->groupBy('PageUrl')->count();
            $data_arr[] = array(
                "id" => $srno,
                'CityName' => '<a href="' . $record->PageUrl . '" target="_blank">' . $trim . '</a>',
                "Count" => $Totalcount,
                "created_at" => date_format($record->created_at, 'Y-m-d H:i:s'),

            );
            $srno++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );
        return Response($response, 200);
    }

    public function getCityGraphData(Request $request)
    {
        $getdata = $request->all();
        $data['request'] = $getdata;
        $id = $request->LeadId;
        $data_arr = [];
        $agentId = $getdata['agentId'];
        $CityStats = UserTracker::select('PageUrl', 'created_at')->where("AgentId", $agentId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'city' . '%')->groupBy('PageUrl');
        if (isset($getdata['CitiesLastDays'])) {
            $date = strtotime("-" . $getdata['CitiesLastDays'] . "day");
            $last = date('Y-m-d H:i:s', $date);
            $CityStats = $CityStats->where('created_at', '>=', $last);
        } else {
            $date = strtotime("-7 day");
            $last = date('Y-m-d H:i:s', $date);
            $CityStats =  $CityStats->where('created_at', '>=', $last);
        }
        if (isset($getdata['DateTo'])) {
            if (isset($getdata['DateFrom'])) {
                $getdata['DateFrom'];
            } else {
                $getdata['DateFrom'] = date("Y-m-d");
            }
            $CityStats = $CityStats->whereDate('created_at', '<=', $getdata['DateTo'])->whereDate('created_at', '>=', $getdata['DateFrom']);
        }


        $totalRecords = count($CityStats->get());
        $totalRecordswithFilter = count($CityStats->get());
        $records = $CityStats->orderby('created_at', 'desc')->get();
        $srno =1;
        foreach ($records as $record) {
            $trim = str_replace(env('HOUSENFRONTURL') . 'city/','', $record->PageUrl);

            $Totalcount = UserTracker::where('PageUrl', 'like', '%' . $record->PageUrl . '%')->where('created_at', '>=', $last)->orderby('created_at', 'desc')->groupBy('PageUrl')->count();

            $data_arr[] = array(
                "id" =>$srno,
                "CityName" => $trim,
                "Count" => $Totalcount,
                "created_at" => date_format($record->created_at, 'Y-m-d H:i:s'), 

            );
            $srno++;
        }
        $response = array(
            "iTotalRecords" => $totalRecords,
            "aaData" => $data_arr,
        );
        return Response($response, 200);
    }
    public function getuserstats_filter(Request $request)
    {
        $getdata = $request->all();
        $agentId = $getdata['agentid'];
        $data_array = array();
        $users = UserTracker::where('AgentId',$agentId);
        if (isset($getdata['UserStatsdata'])) {
            $date = strtotime("-" . $getdata['UserStatsdata'] . "day");
            $last = date('Y-m-d H:i:s', $date);
            $users = $users->where('created_at', '>=', $last);
        } else {
            $date = strtotime("-7 day");
            $last = date('Y-m-d H:i:s', $date);
            $users =  $users->where('created_at', '>=', $last);
        }
        if (isset($getdata['DateTo'])) {
            if (isset($getdata['DateFrom'])) {
                $getdata['DateFrom'];
            } else {
                $getdata['DateFrom'] = date("Y-m-d");
            }
            $users = $users->whereDate('created_at', '<=', $getdata['DateTo'])->whereDate('created_at', '>=', $getdata['DateFrom']);
        }
        if (isset($getdata['userid']) !== '') {
            $users = $users->select("UserId");
        }
            $users = $users->distinct('UserId')->orderBy('created_at','desc')->get();
        
        foreach ($users as $key ) {
            $User = LeadsModel::getLead($key->UserId)->first();

            if (isset($User)) {
                $data_array[] = array(
                    'UserId' => $key->UserId,
                    'Username' =>$User->ContactName,
                );
            }else {
                $data_array[] = array(
                    'UserId' => $key->UserId,
                    'Username' => 'N/A',
                );
            }
        }
        $data['Users'] = $data_array;
        if ($getdata['userid']) {
            $userId = $getdata['userid'];
            $data['IpAddress'] = UserTracker::where('AgentId',$agentId)->distinct('IpAddress')->select('IpAddress')->where('UserId',$userId)->orderBy('created_at','desc')->get();
        }else{
            $data['IpAddress'] = UserTracker::where('AgentId',$agentId)->distinct('IpAddress')->select('IpAddress')->whereNull('UserId')->orderBy('created_at','desc')->where('created_at','>=',$last)->get();
        }
        return $data;

    }
    public function user_stats()
    {
        $usertype = 'agent';
        $agentId = Auth::user()->id;
        return view("agent.stats.userstats", compact('usertype', 'agentId'));
    }
    public function getUserStats(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $data_array = array();
        $getdata = $request->all();
        $agentId = $getdata['agentId'];
        $date = strtotime("-7 day");
        $last = date('Y-m-d H:i:s', $date);
        $Totalcount= UserTracker::where('AgentId',$agentId)->groupBy('IpAddress','UserId')->select("UserId", 'IpAddress')->orderBy('created_at','desc');
        if (isset($getdata['UserStatsdata'])) {
            $date = strtotime("-" . $getdata['UserStatsdata'] . "day");
            $last = date('Y-m-d H:i:s', $date);
            $Totalcount = $Totalcount->where('created_at', '>=', $last);
        }

        if (isset($getdata['DateTo'])) {
            if (isset($getdata['DateTo'])) {
                if (isset($getdata['DateFrom'])) {
                    $getdata['DateFrom'];
                } else {
                    $getdata['DateFrom'] = date("Y-m-d");
                }
                $Totalcount = $Totalcount->whereDate('created_at', '<=', $getdata['DateTo'])->whereDate('created_at', '>=', $getdata['DateFrom']);
            }
        }

        if(isset($getdata['IpAddress'])){
            $IpAddress=$getdata['IpAddress'];
            $Totalcount=$Totalcount->where('IpAddress','like','%'.$IpAddress.'%');
        }
        if(isset($getdata['UserId'])){
            $UserId=$getdata['UserId'];
            $Totalcount=$Totalcount->where('UserId',$UserId);
        }
        $totalRecords = count($Totalcount->get());
        $totalRecordswithFilter = count($Totalcount->get());
        $Statsdata = $Totalcount->skip($start)->take($rowperpage)->get();
        $records = $Statsdata;

        $srno = intval($start) + 1;
        foreach ($records as $key => $value) {
            if ($value->UserId != '') {
                $User = LeadsModel::getLead($value->UserId);
                foreach ($User as $k => $val) {
                }
                if (isset($val['ContactName'])) {
                    $Username = $val['ContactName'];
                } else {
                    $Username = 'N/A';
                }
                $Homepage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', env('HOUSENFRONTURL'))->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Propertypage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'propertydetails/' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Mappage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'Map' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Profilepage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'Profile' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Contactpage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'ContactUs' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Citypage = UserTracker::where('UserId', $value->UserId)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'city' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();

                $data_array[] = array(
                    'id' => $srno,
                    'UserId' => $Username,
                    'IpAddress' => $value->IpAddress,
                    'HomePage' =>  $Homepage,
                    'PropertyPage' => $Propertypage,
                    'MapPage' => $Mappage,
                    'ProfilePage' => $Profilepage,
                    'ContactPage' => $Contactpage,
                    'CityPage' => $Citypage,
                );
            } else {
                $Homepage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', env('HOUSENFRONTURL'))->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Propertypage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'propertydetails/' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                //  DB::enableQueryLog();
                $Mappage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'Map' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                // dd(DB::getQueryLog());
                $Profilepage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'Profile' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Contactpage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'ContactUs' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Citypage = UserTracker::where('IpAddress', $value->IpAddress)->where('PageUrl', 'like', '%' . env('HOUSENFRONTURL') . 'city/' . '%')->where('created_at', '>=', $last)->groupBy('PageUrl')->count();
                $Username = 'N/A';

                $data_array[] = array(
                    'id' => $srno,
                    'UserId' => $Username,
                    'IpAddress' => $value->IpAddress,
                    'HomePage' =>  $Homepage,
                    'PropertyPage' => $Propertypage,
                    'MapPage' => $Mappage,
                    'ProfilePage' => $Profilepage,
                    'ContactPage' => $Contactpage,
                    'CityPage' => $Citypage,
                );
            }

            $srno++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array,
        );

        return response($response, 200);
    }

    public function property_viewed(Request $request)
    {
        $usertype = 'agent';
        $agentId = Auth::user()->id;
        return view("agent.stats.viwedProperty", compact('usertype', 'agentId'));
    }
    public function getPropertyGraphData(Request $request)
    {
        $getdata = $request->all();
        $DateTo = $getdata['DateTo'];
        $DateFrom = $getdata['DateFrom'];
        $PropertiesLastDays = $getdata['PropertiesLastDays'];
        $agentId = $request->agentId;
        $query = UserTracker::select('PropertyUrl', 'created_at');
        $propertyViews = array();
        if (isset($DateTo)) {
            if (isset($DateTo)) {
                if (isset($DateFrom)) {
                    $DateFrom;
                } else {
                    $DateFrom = date("Y-m-d");
                }
                $query->where("AgentId", $agentId)->whereDate('created_at', '<=', $DateTo)->whereDate('created_at', '>=', $DateFrom);
            }
        }
        if (isset($PropertiesLastDays)) {
            $date = strtotime("-" . $PropertiesLastDays . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where("AgentId", $agentId)->where('created_at', '>=', $last);
        } else {
            $date = strtotime("-7 day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where("AgentId", $agentId)->where('created_at', '>=', $last);
        }
        $propertyView = $query->where('PropertyUrl', 'like', '%' . env('HOUSENFRONTURL') . 'propertydetails/' . '%')->orderby('created_at', 'desc')->groupBy('PropertyUrl')->get();
        foreach ($propertyView as $records) {
            $trim = trim($records->PropertyUrl, env('HOUSENFRONTURL') . "propertydetails/");
            $propertyViewcount =  UserTracker::select('PropertyUrl')->where('PropertyUrl', 'like', '%' . $records->PropertyUrl . '%')->where('created_at', '>=', $last)->groupBy('PropertyUrl')->count();
            $propertyViewed = RetsPropertyData::select('ListingId')->where('SlugUrl', 'like', '%'. $trim . '%')->first();
            if (isset($propertyViewed) > 0) {
                $propertyViews[] = array(
                    'Ml_num' => $propertyViewed->ListingId,
                    'count' => $propertyViewcount,
                );
            } else {
                $purgedproperty = RetsPropertyDataPurged::select('ListingId')->where('SlugUrl', 'like', '%'. $trim . '%')->first();
                if (isset($purgedproperty)) {
                    $propertyViews[] = array(
                        'Ml_num' => $purgedproperty->ListingId,
                        'count' => $propertyViewcount,
                    );
                }
            }
        }
        $TotalRecords = count($propertyViews);
        $TotalDisplayRecords = count($propertyViews);
        $response = array(
            "iTotalRecords" => $TotalRecords,
            "iTotalDisplayRecords" => $TotalDisplayRecords,
            'aaData' => $propertyViews,

        );

        return response($response, 200);
    }
    public function graphData(Request $request)
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
        $DateTo = $getdata['DateTo'];
        $DateFrom = $getdata['DateFrom'];
        $PropertiesLastDays = $getdata['PropertiesLastDays'];
        $agentId = $request->agentId;

        $query = UserTracker::select('PropertyUrl', 'created_at');
        $propertyViews = array();
        if (isset($DateTo)) {
            if (isset($DateTo)) {
                if (isset($DateFrom)) {
                    $DateFrom;
                } else {
                    $DateFrom = date("Y-m-d");
                }
                $query->where("AgentId", $agentId)->whereDate('created_at', '<=', $DateTo)->whereDate('created_at', '>=', $DateFrom);
            }
        }
        if (isset($PropertiesLastDays)) {
            $date = strtotime("-" . $PropertiesLastDays . "day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where("AgentId", $agentId)->where('created_at', '>=', $last);
        } else {
            $date = strtotime("-7 day");
            $last = date('Y-m-d H:i:s', $date);
            $query->where("AgentId", $agentId)->where('created_at', '>=', $last);
        }

        $propertycount = $query->where('PropertyUrl', 'like', '%' . env('HOUSENFRONTURL') . 'propertydetails/' . '%')->orderby('created_at', 'desc')->groupBy('PropertyUrl')->get();
        $propertyView = $query->skip($start)->take($rowperpage)->get();
        $srno = intval($start) + 1;
        foreach ($propertyView as $records) {
            $trim = ltrim($records->PropertyUrl, env('HOUSENFRONTURL') . "propertydetails/");
            $propertyViewcount =  UserTracker::select('PropertyUrl')->where('PropertyUrl', 'like', '%' . $records->PropertyUrl . '%')->groupBy('PropertyUrl')->where('created_at', '>=', $last)->count();
            $propertyViewed = RetsPropertyData::select('ListingId', 'ImageUrl', 'StandardAddress', 'ListPrice', 'SlugUrl')->where('SlugUrl','like', '%'. $trim . '%')->first();
            if (isset($propertyViewed) > 0) {
                if ($propertyViewed->ImageUrl != '') {
                    $Imageurl = $propertyViewed->ImageUrl;
                } else {
                    $Imageurl = "/assets/agent/images/no-imag.jpg";
                }
                $propertyViews[] = array(
                    'id' => $srno,
                    'ImageUrl' => '<a href="' . $records->PropertyUrl . '" target="_blank"><img  width="100" src="' . $Imageurl . '"/></a>',
                    'Addr' => $propertyViewed->StandardAddress,
                    'Lp_dol' => number_format($propertyViewed->ListPrice),
                    'Ml_num' => '<a href="' . $records->PropertyUrl . '" target="_blank">' . $propertyViewed->ListingId . '</a>',
                    'date' => date_format($records->created_at, 'Y-m-d H:i'),
                    'count' => $propertyViewcount,
                );
            } else {
                $purgedproperty = RetsPropertyDataPurged::select('ListingId', 'ImageUrl', 'StandardAddress', 'ListPrice',)->where('SlugUrl', 'like', '%'. $trim . '%')->first();
                if (isset($purgedproperty)) {
                    if ($purgedproperty->ImageUrl != '') {
                        $Imageurl = $purgedproperty->ImageUrl;
                    } else {
                        $Imageurl = "/assets/agent/images/no-imag.jpg";
                    }
                    $propertyViews[] = array(
                        'id' => $srno,
                        'ImageUrl' => '<a href="' . $records->PropertyUrl . '" target="_blank"><img  width="100" src="' . $Imageurl . '"/></a>',
                        'Ml_num' => '<a href="' . $records->PropertyUrl . '" target="_blank">' . $purgedproperty->ListingId . '</a>',
                        'Addr' => $purgedproperty->StandardAddress,
                        'Lp_dol' => number_format($purgedproperty->ListPrice),
                        'date' => date_format($records->created_at, 'Y-m-d H:i'),
                        'count' => $propertyViewcount,
                    );
                }
            }
            $srno++;
        }
        $TotalRecords = count($propertycount);
        $TotalDisplayRecords = count($propertycount);
        $response = array(
            "iTotalRecords" => $TotalRecords,
            "iTotalDisplayRecords" => $TotalDisplayRecords,
            'aaData' => $propertyViews,
            "draw" => intval($draw),

        );

        return response($response, 200);
    }

    private function getStatsData_old($agentId, $getProp = false, $PropertiesLastDays = '', $DateTo = '', $DateFrom = '')
    {
        $query = UserTracker::query();
        if (isset($PropertiesLastDays)) {
            $query->where("AgentId", $agentId)->where('created_at', '>=', \Carbon\Carbon::now()->subdays($PropertiesLastDays));
        } elseif (isset($DateTo)) {
            if (isset($DateTo)) {
                if (isset($DateFrom)) {
                    $DateFrom;
                } else {
                    $DateFrom = date("Y-m-d");
                }
                $query->where("AgentId", $agentId)->whereDate('created_at', '<=', $DateTo)->whereDate('created_at', '>=', $DateFrom);
            } else {
                $query->where("AgentId", $agentId)->where('created_at', '>=', \Carbon\Carbon::now()->subdays(7));
            }
        }
        $query->orderBy('id', 'DESC');
        $TotalRecords = $query->orderBy('id', 'DESC')->count();
        $TotalDisplayRecords = $query->orderBy('id', 'DESC')->count();
        $trackUsers = $query->get();
        $mostPropertyViews = array();
        $mostPagesOpened = array();
        $mostCitySearch = array();
        $mostSearches = array();
        $mostTypesSearch = array();
        $mostSubTypesSearch = array();
        $propertyViews = array();
        $prop = "";
        $tempPropertyViews = array();
        $tempSlug = array();
        $tempSlugs = array();
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
                        "inTime" => $value->InTime,
                        "stayTime" => $value->StayTime,
                        "IpAddress" => $value->IpAddress
                    );
                    $mostPagesOpened[$pageUrl] = $obj;
                }
            } else {
                $tempPagesOpened[] = $pageUrl;
                $obj = array(
                    "text" => $pageUrl,
                    "count" => 1,
                    "inTime" => $value->InTime,
                    "stayTime" => $value->StayTime,
                    "IpAddress" => $value->IpAddress
                );
                $mostPagesOpened[$pageUrl] = $obj;
            }
            if ($value->FilteredData) {
                $filteredData = $value->FilteredData;
                $filteredData = json_decode($filteredData, true);
                if (isset($filteredData['text_search']) && $filteredData['text_search'] !== "") {
                    // $text_search = $filteredData['text_search'];
                    // if (in_array($text_search, $tempSearches)) {
                    //     foreach ($mostSearches as $key => $page) {
                    //         unset($mostSearches[$text_search]);
                    //         $text = $page['text'];
                    //         $count = ++$page['count'];
                    //         $obj = array(
                    //             "text" => $text,
                    //             "count" => $count
                    //         );
                    //         $mostSearches[$text_search] = $obj;
                    //     }
                    // } else {
                    //     $tempSearches[] = $text_search;
                    //     $obj = array(
                    //         "text" => $text_search,
                    //         "count" => 1
                    //     );
                    //     $mostSearches[$text_search] = $obj;
                    // }
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
                }
                // if (isset($filteredData['propertySubType']) && $filteredData['propertySubType'] != "" && count($filteredData['propertySubType']) !== 0) { //adding  a new condition by siddharth
                //     // dd($filteredData['propertySubType']);

                //     $propertySubType = $filteredData['propertySubType'];
                //     if (in_array($propertySubType, $tempSubTypesSearch)) {
                //         // dd(in_array($propertySubType, $tempSubTypesSearch));
                //         foreach ($mostSubTypesSearch as $key => $page) {
                //             unset($mostSubTypesSearch[$propertySubType]);
                //             $text = $page['text'];
                //             $count = ++$page['count'];
                //             $obj = array(
                //                 "text" => $text,
                //                 "count" => $count
                //             );
                //             $mostSubTypesSearch[$propertySubType] = $obj;
                //         }
                //     } else {
                //         $tempSubTypesSearch[] = $propertySubType;
                //         // dd($tempSubTypesSearch[0]);
                //         $obj = array(
                //             "text" => $propertySubType,
                //             "count" => 1
                //         );
                //         $mostSubTypesSearch[$propertySubType] = $obj;
                //     }
                // }


                if ($getProp) {
                    if (isset($filteredData['slug']) && $filteredData['slug'] !== "") {
                        $tempSlug[]["slug"] = $filteredData['slug'];
                        $this->dateTime[$filteredData['slug']] = $value->InTime;
                        $this->pageUrl[$filteredData['slug']] = $pageUrl;
                        $tempSlugs[] = $filteredData['slug'];

                        // if (!in_array($filteredData['slug'], $tempSlug)) {
                        //     $prop = getProperties($filteredData['slug']);
                        //     $prop['date'] = $value->InTime;
                        //     unset($prop["Extras"]);
                        //     unset($prop["Ad_text"]);
                        //     unset($prop["RoomsDescription"]);
                        //     $mls = $prop->Ml_num;
                        //     if (in_array($mls, $tempPropertyViews)) {
                        //         unset($mostPropertyViews[$mls]);
                        //         foreach ($mostPropertyViews as $key => $page) {
                        //             $text = $page['text'];
                        //             $count = ++$page['count'];
                        //             $obj = array(
                        //                 "text" => $text,
                        //                 "count" => $count
                        //             );
                        //             $mostPropertyViews[$mls] = $obj;
                        //         }
                        //     } else {
                        //         $tempPropertyViews[] = $mls;
                        //         $obj = array(
                        //             "text" => $mls,
                        //             "count" => 1
                        //         );
                        //         $mostPropertyViews[$mls] = $obj;
                        //     }
                        //     if (count($propertyViews) <= 20) {
                        //         $propertyViews[] = $prop;
                        //     }
                        // }else{
                        // }
                    }
                }
            }
        }
        $collection = $tempSlug;
        $collection = collect($collection);
        $collections  = $collection->groupBy('slug')
            ->flatMap(function ($items) {
                $quantity = collect($items)->count();
                return $items->map(function ($item) use ($quantity) {
                    $item["count"] = $quantity;
                    $item["date"] = $this->dateTime[$item['slug']];
                    $item["pageUrl"] = $this->pageUrl[$item['slug']];

                    return $item;
                });
            })
            ->unique('slug')
            ->values()
            ->all();

        // $propData = RetsPropertyData::select(PropertyConstants::PROPERTY_STATS_DATA)->where('SlugUrl', $item['slug'])->first();
        // $item["mls"] = $propData->ListingId;
        foreach ($collections as $key => $collect) {
            if (count($mostPropertyViews) < 20) {
                $prop = getPropstats($collect['slug']);
                if (!$prop) {
                    continue;
                }
                $prop->date = $collect['date'];
                $prop->count = $collect['count'];
                $prop->pageUrl = $collect['pageUrl'];
                try {

                    $propertyViews[] = array(
                        'ImageUrl' => $prop->ImageUrl,
                        'Addr' => $prop->StandardAddress,
                        'Lp_dol' => $prop->Sp_dol,
                        'Ml_num' => $prop->ListingId,

                        'date' => $collect['date'],
                        'count' => $collect['count'],
                        'pageUrl' => $collect['pageUrl'],
                    );
                } catch (\Throwable $th) {
                }
                $collect['msl'] = $prop->Ml_num;
                $mostPropertyViews[] = $collect;
            }
        }
        // dd(count($propertyViews));
        return  array("mostPropertyViews" => $mostPropertyViews, "mostPagesOpened" => $mostPagesOpened, "mostSearches" => $mostSearches, "mostTypesSearch" => $mostTypesSearch, "mostSubTypesSearch" => $mostSubTypesSearch, 'propertyViews' => $propertyViews, "total" => count($mostPropertyViews),  "iTotalRecords" => $TotalRecords, "iTotalDisplayRecords" => $TotalDisplayRecords,);
    }

    public function testApi(){
        $str = '{"websetting":{"WebsiteName":"Wedu","WebsiteTitle":"wedu.ca","UploadLogo":"https:\/\/panel.wedu.ca\/storage\/1652333852.png","LogoAltTag":"Wedu","Favicon":"https:\/\/panel.wedu.ca\/storage\/1652270821.png","WebsiteEmail":"info@wedu.com","PhoneNo":"647-243-5349","WebsiteAddress":"AIMHOME REALTY INC.\r\nBROKERAGE,                            \r\n\r\n3601 HWY. 7 E UNIT 513\r\nMARKHAM,ONTARIO","FacebookUrl":"https:\/\/www.facebook.com\/","TwitterUrl":"https:\/\/twitter.com\/login","LinkedinUrl":"https:\/\/www.linkedin.com\/","InstagramUrl":"https:\/\/www.instagram.com\/?hl=en","YoutubeUrl":"https:\/\/www.youtube.com\/","WebsiteColor":"#c874b2","WebsiteMapColor":"#cb62af","GoogleMapApiKey":null,"HoodQApiKey":"95xVLjhNZ82RKeWqkU2gr8TfoOexrIYc4LU9SyKE","WalkScoreApiKey":"ad81fcdbf9d13dccb26f827251fb7a08","FavIconAltTag":"Wedu","ScriptTag":"<scrip>\r\nconsole.log(\"script tag\");\r\n<\/script>","TopBanner":null,"FbAppId":"319600363492618","GoogleClientId":"635000236410-183hivd8j45ib94iems5ht93plkqnn5m.apps.googleusercontent.com"},"pageSetting":{"topBannerSection":"show","recentSection":"show","citySection":"show","communityBannerSection":"show","testimonialSection":"show","contectFormSection":"show","htmlContent":null,"TopBanner":"https:\/\/panel.wedu.ca\/storage\/1652265919.jpg","CommunityBanner":"http:\/\/panel.wedu.ca\/storage\/62572982c3a5b.jpg","contentSection":"hide","blogSection":"hide","featuredSection":"hide","profileSection":"hide"},"arrangeSections":["bannerSection","featuredSection","recentSection","citySection","blogSection","communitySection","testimonialSection","contectFormSection","htmlContentSection","profileSection"],"seo":{"MetaTitle":"Homes for Sale & Real Estate Get Listings in Canada | Wedu","MetaDescription":"Wedu Is Your Best Choice for Real Estate Search in Canada. Find Homes for Sale, New Developments, Rental Homes, Real Estate Agents, and Property Insights.","MetaTags":"Homes for sale, real estate get listings"}}';
        $str = json_decode($str,true);
        return response($str,200);
    }
}
