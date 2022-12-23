<?php

namespace App\Http\Controllers\frontend\propertiesListings;

use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataCommPurged;
use App\Models\RetsPropertyDataCondoPurged;
use App\Models\RetsPropertyDataImagesSold_new;
use App\Models\RetsPropertyDataResiPurged;
use App\Models\SqlModel\FeaturesMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use App\Models\RetsPropertyData;
use App\Constants\PropertyConstants;
use App\Models\PolygonsData;
use App\Models\RetsPropertyDataComm;
use App\Models\SqlModel\SavedSearchFilter;
use App\Models\SqlModel\Websetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\SqlModel\Pages;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\RetsPropertyDataPurged;
use App\Models\RetsPropertyDataImagesSold;
use Illuminate\Support\Facades\Validator;

class PropertiesController extends Controller
{

    public function propertiesList()
    {
        $response['dataList'] = [];
        $response['PropertyType'] = [];
        try {
            $limit = PropertyConstants::HOME_PAGE_LIMIT;
            $query = RetsPropertyData::query();
            $PropertyType = $query->distinct('PropertyType')->select("PropertyType")->get();
            $query->select(PropertyConstants::SELECT_DATA);
            $query->where('PropertyStatus', 'Sale');
            $query->where('PropertyType', 'Residential');
            $query->orderBy('Timestamp_sql', 'desc');
            $query->limit($limit);
            $result = $query->get();
            $response['dataList'] = $result;
            $response['PropertyType'] = $PropertyType;
        } catch (QueryException $exception) {
            return response(['errors' => $exception->errorInfo]);
        }
        return response($response, 200);
    }

    // TODO::DELETE
    public function propertiesSearchMongo(Request $request)
    {

        $propType = $request->propType;
        $payload = $params = $request->all();
        if (isset($params['features'])) {
            if ($params['features'] == 'Central_vac') {
                $params['Central_vac'] = "Y";
            } elseif ($params['features'] == 'Elevator') {
                $params['Elevator'] = "Y";
            } elseif ($params['features'] == 'Central Air') {
                $params['A_c'] = "Central Air";
            } elseif ($params['features'] == 'Den_fr') {
                $params['Den_fr'] = "Y";
            } elseif ($params['features'] == 'Gar') {
                $params['Gar'] = "Y";
            } elseif ($params['features'] == 'Extras') {
                $params['Extras'] = "Gym";
            } elseif ($params['features'] == 'Park_spcs') {
                $params['Park_spcs'] = "Y";
            } elseif ($params['features'] == 'Pool') {
                $params['Pool'] = "Y";
            } elseif ($params['features'] == 'prop_feature') {
                $params['prop_feature'] = "Ravine";
            } elseif ($params['features'] == 'Waterfront') {
                $params['Waterfront'] = "Y";
            } elseif ($params['features'] == 'A_c') {
                $params['A_c'] = "Y";
            }
        }
        unset($params['features']);
        $searchFilter = array();
        $filteredData = array();
        $orFilter = array();
        $array_data = array();
        $textSearchFilter = '';
        $offset = 0;
        $curr_page = 0;
        $limit = PropertyConstants::LIMIT;
        if (isset($payload['curr_page']) && $payload['curr_page'] != '' && $payload['curr_page'] > 0) {
            $curr_page = (int)$payload['curr_page'];
            $offset = (($curr_page - 1) >= 0) ? ($curr_page - 1) * $limit : 0; //*$limit ;
        }
        if (isset($params['sort_by']) && !empty($params['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $params['sort_by'];
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
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'inserted_time';
            $orderBy = 'Desc';
        }

        $response['allparams'] = $params;
        unset($params['sort_by']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['text_search']) && !empty($payload['text_search'])) {
            unset($params['shape']);
            unset($params['curr_shape']);
            unset($params['curr_bounds']);
            unset($params['radius']);
            unset($params['curr_path']);
            unset($params['bounds']);
            unset($params['center_lat']);
            unset($params['center_lng']);
            unset($params['curr_bounds']);
            unset($params['curr_page']);
            unset($params['curr_path_query']);
            unset($params['curr_radius']);
        } else {

            if (isset($payload['shape']) && $payload['shape'] != '') {
                $dis_sel = "";
                $dis_cond = "";
                $shape = $payload['shape'];
                if ($payload['shape'] == 'circle') {
                    $center_lat = $payload['center_lat'];
                    $center_lng = $payload['center_lng'];
                    $radius = $payload['radius'];
                    $dis_sel = " , ( 6371 * acos ( cos ( radians(" . $center_lat . ") ) * cos( radians( Latitude ) ) * cos( radians( Longitude ) - radians($center_lng) ) + sin ( radians(" . $center_lat . ") ) * sin( radians( Latitude ) )  ) ) AS distance ";
                    if (isset($payload['radius']) && $payload['radius'] > 0) {
                        $rdinkm = $payload['radius'] / 1000;
                        $dis_cond = " distance < " . $rdinkm;
                    } else {
                        $dis_cond = " distance < 5 ";
                    }
                } else if ($payload['shape'] == 'polygon') {
                    $temp_query = rawurldecode($payload['curr_path_query']);
                    $path_query = rtrim($temp_query, ",");
                    $dis_cond = "ST_WITHIN(point(latitude,longitude), ST_GeomFromText('POLYGON((" . $path_query . "))') )";
                } else if ($payload['shape'] == 'rectangle') {
                    if (isset($payload['curr_bounds']) && $payload['curr_bounds'] != '') {
                        $allbound = explode('###', $payload['curr_bounds']);

                        $p1 = explode(',', $allbound[0]);
                        $p2 = explode(',', $allbound[1]);
                        $a = (float)$p1[1];
                        $b = (float)$p1[0];
                        $c = (float)$p2[1];
                        $d = (float)$p2[0];

                        $condition1 = $a > $c ? "Latitude > $c AND Latitude < $a" : "Latitude > $a AND Latitude < $c";
                        $condition2 = $b > $d ? "Longitude > $d AND Longitude < $b" : "longitude > $b AND Longitude < $d";
                        $dis_cond = "( $condition1 ) AND ( $condition2 )";
                    }
                }
                unset($params['shape']);
                unset($params['curr_shape']);
                unset($params['curr_bounds']);
                unset($params['radius']);
                unset($params['curr_path']);
                unset($params['bounds']);
                unset($params['center_lat']);
                unset($params['center_lng']);
                unset($params['curr_bounds']);
                unset($params['curr_page']);
                unset($params['curr_path_query']);
                unset($params['curr_radius']);
            }
        }
        unset($params['curr_bounds']);
        unset($params['curr_page']);
        unset($params['sort_by']);
        foreach ($params as $searchKey => $searchValue) {
            if (($searchValue) === '') {
                continue;
            }
            switch ($searchKey) {
                case 'text_search':
                    $textSearchFilter = $searchValue;
                    break;
                case 'price_max':
                    $searchFilter['price_max'] = $searchValue;
                    break;
                case 'price_min':
                    $searchFilter['price_min'] = $searchValue;
                    break;
                case 'S_r':
                    $array_data['S_r'] = $searchValue;
                    break;
                case 'Type_own1_out':
                    $array_data['Type_own1_out'] = $searchValue;
                    break;
                case 'Gar':
                    $searchFilter['Gar'] = $searchValue;
                    // $searchFilter['Gar'] = $searchValue;
                    break;

                case 'Extras':
                    $textSearchFilter = $searchValue;
                    break;

                case 'Park_spcs':
                    $searchFilter['Park_spcs'] = $searchValue;
                    break;

                case 'Pool':
                    $searchFilter['Pool'] = $searchValue;
                    break;

                case 'prop_feature':
                    $orFilter['Prop_feat1_out'] = $searchValue;
                    $orFilter['Prop_feat2_out'] = $searchValue;
                    $orFilter['Prop_feat3_out'] = $searchValue;
                    $orFilter['Prop_feat4_out'] = $searchValue;
                    $orFilter['Prop_feat5_out'] = $searchValue;
                    $orFilter['Prop_feat6_out'] = $searchValue;
                    break;

                case 'Waterfront':
                    $searchFilter['Waterfront'] = $searchValue;
                    break;

                case 'Bsmt1_out':
                    $array_data['Bsmt1_out'] = $searchValue;
                    break;

                case 'sqft_min':
                    $searchFilter["Sqft"] = $searchValue;
                    break;

                case 'sqft_max':
                    $searchFilter["Sqft"] = $searchValue;
                    break;


                default:
                    $searchFilter[$searchKey] = $searchValue;
            }
        }
        unset($params['price_min']);
        unset($params['price_max']);
        if (empty($textSearchFilter)) {
            $filteredData = get_search_result_mongo(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "Main"
            );
            $mapData = get_search_result_mongo(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "MAP",

            );
        } else {

            $filteredData = get_search_result_mongo(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "Main"
            );
            $mapData = get_search_result_mongo(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "MAP"
            );
        }
        $total_properties = $filteredData['total'];
        $resutl_properties = $filteredData['result'];
        $final_result = getFormatedDataMongo($resutl_properties);
        // $response['search_query'] = $lastsearch_query;
        //$pagination = getPaginationString($curr_page, $total_properties, $limit, 2, "", "");
        //$response['pagination'] = $pagination;
        $response['alldata'] = $final_result;
        $response['total'] = $total_properties;
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        $response['mapdata'] = $mapData["result"];
        //$response['mapdata'] = getFormatedDataMongo($mapData['result']);
        return response($response, 200);
    }

    public function propertiesDetailsOld(Request $request)
    {
        $table = "";
        // try {
        $metDescString = "";
        $data['similar'] = ["sale" => [], 'rent' => []];
        $roomData = array();
        $roomsData = array();
        // SlugUrl
        $rpdQuery = RetsPropertyData::query();
        $rpdQuery->select("ListingId");
        $rpdQuery->where('SlugUrl', $request->SlugUrl);
        $retsPropData = $rpdQuery->first();
        $res = array();
        $imgs = [];
        if (RetsPropertyDataResi::where('SlugUrl', $request->SlugUrl)->exists()) {
            DB::enableQueryLog();
            $query = RetsPropertyDataResi::query();
            $table = "Residential";
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            if ($retsPropData->ListingId) {
                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            if ($res) {
                $res->Addr = $res->StandardAddress;
                $res->PropertyType = $table;
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
            }
        }
        if (empty($res)) {
            if (RetsPropertyDataCondo::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Condo";
                $query = RetsPropertyDataCondo::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                if ($retsPropData->ListingId) {
                    $query->where('Ml_num', $retsPropData->ListingId);
                } else {
                    $query->where('SlugUrl', $request->SlugUrl);
                }
                $res = $query->with('propertiesImages')->first();
                if ($res) {
                    if ($res->propertiesImages != []) {
                        $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                            return $data->s3_image_url;
                        });
                    }
                    $res->PropertyType = $table;
                    $res->Addr = $res->StandardAddress;
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
                }
            }
        }
        if (empty($res)) {
            if (RetsPropertyDataComm::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Commercial";
                $query = RetsPropertyDataComm::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                if ($retsPropData->ListingId) {

                    $query->where('Ml_num', $retsPropData->ListingId);
                } else {
                    $query->where('SlugUrl', $request->SlugUrl);
                }
                $res = $query->with('propertiesImages')->first();
                if ($res) {
                    if ($res->propertiesImages != []) {
                        $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                            return $data->s3_image_url;
                        });
                    }
                    $res->PropertyType = $table;
                    $res->Addr = $res->StandardAddress;
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
                }
            }
        }
        // resi purgedC5237966
        if (empty($res)) {
            if (RetsPropertyDataResiPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Residential Purged";
                $query = RetsPropertyDataResiPurged::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
                $query->where('SlugUrl', $request->SlugUrl);
                $res = $query->with('propertiesImages')->first();
                // if ($res->propertiesImages != []) {
                //     $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                //         return $data->s3_image_url;
                //     });
                // }
                if ($res) {
                    $res->PropertyType = $table;
                    $res->Addr = $res->StandardAddress;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }
        //comm purged
        if (empty($res)) {
            if (RetsPropertyDataCommPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Commercial Purged";
                $query = RetsPropertyDataCommPurged::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                $query->where('SlugUrl', $request->SlugUrl);
                $res = $query->with('propertiesImages')->first();
                // if ($res->propertiesImages != []) {
                //     $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                //         return $data->s3_image_url;
                //     });
                // }
                if ($res) {
                    $res->PropertyType = $table;
                    $res->Addr = $res->StandardAddress;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }
        // condo purged
        if (empty($res)) {
            if (RetsPropertyDataCondoPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Condo Purged";
                $query = RetsPropertyDataCondoPurged::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                $query->where('SlugUrl', $request->SlugUrl);
                $res = $query->with('propertiesImages')->first();
                if ($res) {
                    $res->PropertyType = $table;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }
        if (!empty($res)) {
            $roomsData = json_decode($res->RoomsDescription, true);
            $res->Addr = $res->StandardAddress;
        }
        $flag = 1;
        $preLevel = "";
        for ($i = 1; $i <= 12; $i++) {
            if (isset($roomsData['Rm' . $i . '_len']) && $roomsData['Rm' . $i . '_len'] != "" && isset($roomsData['Rm' . $i . '_wth']) && $roomsData['Rm' . $i . '_wth'] != "" && isset($roomsData['Rm' . $i . '_out']) && $roomsData['Rm' . $i . '_out'] != "") {
                if ($flag == 1) {
                    $obj = array(
                        'isHeading' => true,
                        'name' => "Name",
                        'size' => "Size",
                        'features' => "Features",
                        'level' => "Level",
                    );
                    array_push($roomData, $obj);
                    $flag++;
                }
                $level = $roomsData['Level' . $i];
                if ($level == $preLevel) {
                    $level = "";
                } else {
                    $preLevel = $level;
                }
                $name = $roomsData['Rm' . $i . '_out'];
                $wth = $roomsData['Rm' . $i . '_wth'];
                $len = $roomsData['Rm' . $i . '_len'];
                $tempDec1 = $roomsData['Rm' . $i . '_dc' . 1 . '_out'];
                $tempDec2 = $roomsData['Rm' . $i . '_dc' . 2 . '_out'];
                $tempDec3 = $roomsData['Rm' . $i . '_dc' . 3 . '_out'];

                $dec1 = isset($tempDec1) ? $tempDec1 . " , " : "";
                $dec2 = isset($tempDec2) ? $tempDec2 . " , " : "";
                $dec3 = isset($tempDec3) ? $tempDec3 . " , " : "";
                $descFull = $dec1 . $dec2 . $dec3;
                $descFull = preg_replace("/,+/", ",", $descFull);
                $descFull = rtrim($descFull, ' ,');
                $obj = array(
                    'name' => $name,
                    'sizeInMt' => $len . " X " . $wth,
                    'sizeInFt' => round(($len * 3.28084), 2) . " X " . round(($wth * 3.28084), 2),
                    'desc' => $descFull,
                    "levels" => $level
                );
                array_push($roomData, $obj);
            }
        }
        // $res->RoomsDescription = $roomData;
        // $res->properties_imagesesssss	=$imgs;
        if ($res != []) {
            //$res["Dom"] = getActualDom($res["Timestamp_sql"]);
            $res["PropertyStatus"] = $res["S_r"];
            $res["ContractDate"] = $res["Ld"];
            $res["Sp_date"] = $res["Cd"];
            $res = getDom($res);
            if (isset($res["Sp_dol"]) && $res["Sp_dol"] > 0) {
                $res["Lp_dol"] = $res["Sp_dol"];
            }
        }
        $res["RoomsDescription"] = $roomData;
        $res["properties_images"] = [];
        $data['details'] = $res;
        $data["metaDesc"] = $metDescString;
        $data["table"] = $table;
        return response($data, 200);
        // } catch (\Throwable $th) {
        //     $res["RoomsDescription"] =[];
        //     $res["properties_images"] = [];
        //     $data['details'] = $res;
        //     $data["metaDesc"] = [];
        //     $data["table"] = $table;
        //     $data["Error"] = $th;
        //     return response($data, 500);
        // }
    }

    public function propertiesDetails(Request $request)
    {
        $table = "";
        $status = "";
        $classType = "";
        $soldListingId = "";
        // try {
        $metDescString = "";
        $data['similar'] = ["sale" => [], 'rent' => []];
        $roomData = array();
        $roomsData = array();
        // SlugUrl
        $rpdQuery = RetsPropertyData::query();
        $rpdQuery->select(["ListingId", "Status", "PropertyType"]);
        $rpdQuery->where('SlugUrl', $request->SlugUrl);
        $retsPropData = $rpdQuery->first();
        if (collect($retsPropData)->count() > 0) {
            $status = $retsPropData->Status;
            $classType = $retsPropData->PropertyType;
        } else {
            $data = RetsPropertyDataPurged::select(["ListingId", "Status", "PropertyType"])->where("SlugUrl", $request->SlugUrl)->first();
            if (collect($data)->count() > 0) {
                $status = $data->Status;
                $classType = $data->PropertyType;
                $soldListingId = $data->ListingId;
            }
        }
        if ($classType == "" && $status == "") {
            $data['details'] = [];
            $data["metaDesc"] = "";
            $data["table"] = "";
            $data["message"] = "No properties were found";
            return response($data);
        }
        $res = array();
        $imgs = [];
        if ($classType == "Residential" && $status == "A") {
            DB::enableQueryLog();
            $query = RetsPropertyDataResi::query();
            $table = "Residential";
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            if ($retsPropData->ListingId) {
                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
        } elseif ($classType == "Commercial" && $status == "A") {
            $table = "Commercial";
            $query = RetsPropertyDataComm::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
            if ($retsPropData->ListingId) {

                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            /*if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }*/
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
        } elseif ($classType == "Condos" && $status == "A") {
            $table = "Condo";
            $query = RetsPropertyDataCondo::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
            if ($retsPropData->ListingId) {
                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            /*if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }*/
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
        } elseif ($classType == "Residential" && $status == "U") {
            $table = "Residential Purged";
            $query = RetsPropertyDataResiPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            //$query->where('SlugUrl', $request->SlugUrl);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->first();
            $res->PropertyType = $table;
            $res->S_r = "Closed";
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $timestamp_sql = strtotime($res->property_insert_time);
            $saleDate = date('F jS Y', $timestamp_sql);
            $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
            $res = makeLabel($res);
            $res->S_r = $res->LastStatusButton;
            try {
                $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
            } catch (\Throwable $th) {
            }
            $res->propertiesImages = $imgs;
            $res->properties_images = $imgs;
        } elseif ($classType == "Commercial" && $status == "U") {
            $table = "Commercial Purged";
            $query = RetsPropertyDataCommPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
            //$query->where('SlugUrl', $request->SlugUrl);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->first();
            $res->PropertyType = $table;
            $res = makeLabel($res);
            $res->S_r = $res->LastStatusButton;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $timestamp_sql = strtotime($res->property_insert_time);
            $saleDate = date('F jS Y', $timestamp_sql);
            $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
            try {
                $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
            } catch (\Throwable $th) {
            }
            $res->propertiesImages = $imgs;
            $res->properties_images = $imgs;
        } else {
            $table = "Condo Purged";
            $query = RetsPropertyDataCondoPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
            //$query->where('SlugUrl', $request->SlugUrl);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->first();
            $res->PropertyType = $table;
            $res = makeLabel($res);
            $res->S_r = $res->LastStatusButton;
            //$res->S_r = "Closed";
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $timestamp_sql = strtotime($res->property_insert_time);
            $saleDate = date('F jS Y', $timestamp_sql);
            $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
            try {
                $imgs = RetsPropertyDataImagesSold::select('s3_image_url')->where("listingID", $res->Ml_num)->get();
            } catch (\Throwable $th) {
            }
            $res->propertiesImages = $imgs;
            $res->properties_images = $imgs;
        }
        if (!empty($res)) {
            $roomsData = json_decode($res->RoomsDescription, true);
        }
        $flag = 1;
        $preLevel = "";
        for ($i = 1; $i <= 12; $i++) {
            if (isset($roomsData['Rm' . $i . '_len']) && $roomsData['Rm' . $i . '_len'] != "" && isset($roomsData['Rm' . $i . '_wth']) && $roomsData['Rm' . $i . '_wth'] != "" && isset($roomsData['Rm' . $i . '_out']) && $roomsData['Rm' . $i . '_out'] != "") {
                if ($flag == 1) {
                    $obj = array(
                        'isHeading' => true,
                        'name' => "Name",
                        'size' => "Size",
                        'features' => "Features",
                        'level' => "Level",
                    );
                    array_push($roomData, $obj);
                    $flag++;
                }
                $level = $roomsData['Level' . $i];
                if ($level == $preLevel) {
                    $level = "";
                } else {
                    $preLevel = $level;
                }
                $name = $roomsData['Rm' . $i . '_out'];
                $wth = $roomsData['Rm' . $i . '_wth'];
                $len = $roomsData['Rm' . $i . '_len'];
                $tempDec1 = $roomsData['Rm' . $i . '_dc' . 1 . '_out'];
                $tempDec2 = $roomsData['Rm' . $i . '_dc' . 2 . '_out'];
                $tempDec3 = $roomsData['Rm' . $i . '_dc' . 3 . '_out'];

                $dec1 = isset($tempDec1) ? $tempDec1 . " , " : "";
                $dec2 = isset($tempDec2) ? $tempDec2 . " , " : "";
                $dec3 = isset($tempDec3) ? $tempDec3 . " , " : "";
                $descFull = $dec1 . $dec2 . $dec3;
                $descFull = preg_replace("/,+/", ",", $descFull);
                $descFull = rtrim($descFull, ' ,');
                $obj = array(
                    'name' => $name,
                    'sizeInMt' => $len . " X " . $wth,
                    'sizeInFt' => round(($len * 3.28084), 2) . " X " . round(($wth * 3.28084), 2),
                    'desc' => $descFull,
                    "levels" => $level
                );
                array_push($roomData, $obj);
            }
        }

        if ($res != []) {
            $res["PropertyStatus"] = $res["S_r"];
            $res["ContractDate"] = $res["Ld"];
            $res["Sp_date"] = $res["Cd"];
            $res = getDom($res);
            if (isset($res["Sp_dol"]) && $res["Sp_dol"] > 0) {
                $res["Lp_dol"] = $res["Sp_dol"];
            }
        }
        $res["RoomsDescription"] = $roomData;
        //$res["properties_images"] = [];
        $data['details'] = $res;
        $data["metaDesc"] = $metDescString;
        $data["table"] = $table;
        return response($data, 200);
    }

    public function propertiesDetailsRecon(Request $request)
    {
        /*$select_value = implode(",",PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
        $sql_query = "SELECT $select_value from RetsPropertyDataCondoPurged where SlugUrl = '".$request->SlugUrl."'";
        $res  = DB::select($sql_query);
	$data["res"] = $res;
        return response($data,200);*/

        $table = "";
        // try {
        $metDescString = "";
        $data['similar'] = ["sale" => [], 'rent' => []];
        $roomData = array();
        $roomsData = array();
        // SlugUrl
        $rpdQuery = RetsPropertyData::query();
        $rpdQuery->select("ListingId");
        $rpdQuery->where('SlugUrl', $request->SlugUrl);
        $retsPropData = $rpdQuery->first();
        $res = array();
        $imgs = [];
        // step 1 for sql query for common tables


        if ($request->classType == "Residential" && $request->status == "A") {
            if (RetsPropertyDataResi::where('SlugUrl', $request->SlugUrl)->exists()) {
                DB::enableQueryLog();
                $query = RetsPropertyDataResi::query();
                $table = "Residential";
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
                if ($retsPropData->ListingId) {
                    $query->where('Ml_num', $retsPropData->ListingId);
                } else {
                    $query->where('SlugUrl', $request->SlugUrl);
                }
                $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
                $res->PropertyType = $table;
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
            }
        }
        if ($request->classType == "Commercial" && $request->status == "A") {
            if (empty($res)) {
                if (RetsPropertyDataCondo::where('SlugUrl', $request->SlugUrl)->exists()) {
                    $table = "Condo";
                    $query = RetsPropertyDataCondo::query();
                    $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                    if ($retsPropData->ListingId) {
                        $query->where('Ml_num', $retsPropData->ListingId);
                    } else {
                        $query->where('SlugUrl', $request->SlugUrl);
                    }
                    $res = $query->with('propertiesImages')->first();
                    if ($res->propertiesImages != []) {
                        $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                            return $data->s3_image_url;
                        });
                    }
                    $res->PropertyType = $table;
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
                }
            }
        }
        if ($request->classType == "Condos" && $request->status == "A") {
            if (empty($res)) {
                if (RetsPropertyDataComm::where('SlugUrl', $request->SlugUrl)->exists()) {
                    $table = "Commercial";
                    $query = RetsPropertyDataComm::query();
                    $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                    if ($retsPropData->ListingId) {

                        $query->where('Ml_num', $retsPropData->ListingId);
                    } else {
                        $query->where('SlugUrl', $request->SlugUrl);
                    }
                    $res = $query->with('propertiesImages')->first();
                    if ($res->propertiesImages != []) {
                        $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                            return $data->s3_image_url;
                        });
                    }
                    $res->PropertyType = $table;
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $metDescString = "Wedu provides the latest listings of condos, apartments, and houses for Buy throughout Canada. View our comprehensive Buy listings today!";
                }
            }
        }
        // resi purgedC5237966
        if ($request->classType == "Residential" && $request->status == "U") {
            if (empty($res)) {
                if (RetsPropertyDataResiPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                    $table = "Residential Purged";
                    //$query = RetsPropertyDataResiPurged::query();
                    //$query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
                    //$query->where('SlugUrl', $request->SlugUrl);
                    //$res = $query->first();
                    $select_value = implode(",", PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
                    $sql_query = "SELECT $select_value from RetsPropertyDataResiPurged where SlugUrl = '" . $request->SlugUrl . "'";
                    $res = DB::select($sql_query);
                    $res = collect($res)->first();
                    //$res = collect($res)->all();
                    $res->PropertyType = $table;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold_new::select('image_urls')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }
        if ($request->classType == "Commercial" && $request->status == "U") {
            //comm purged
            if (empty($res)) {
                if (RetsPropertyDataCommPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                    $table = "Commercial Purged";
                    //$query = RetsPropertyDataCommPurged::query();
                    //$query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                    //$query->where('SlugUrl', $request->SlugUrl);
                    //$res = $query->first();

                    $select_value = implode(",", PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
                    $sql_query = "SELECT $select_value from RetsPropertyDataCommPurged where SlugUrl = '" . $request->SlugUrl . "'";
                    $res = DB::select($sql_query);
                    $res = collect($res)->first();

                    // if ($res->propertiesImages != []) {
                    //     $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    //         return $data->s3_image_url;
                    //     });
                    // }
                    $res->PropertyType = $table;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold_new::select('image_urls')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }
        if ($request->classType == "Condos" && $request->status == "U") {
            // condo purged
            if (empty($res)) {
                if (RetsPropertyDataCondoPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                    $table = "Condo Purged";
                    /*$query = RetsPropertyDataCondoPurged::query();
                    $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                    $query->where('SlugUrl', $request->SlugUrl);
                    $res = $query->first();*/

                    $select_value = implode(",", PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
                    $sql_query = "SELECT $select_value from RetsPropertyDataCondoPurged where SlugUrl = '" . $request->SlugUrl . "'";
                    $res = DB::select($sql_query);
                    $res = collect($res)->first();

                    $res->PropertyType = $table;
                    $res->S_r = "Closed";
                    $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                    $timestamp_sql = strtotime($res->property_insert_time);
                    $saleDate = date('F jS Y', $timestamp_sql);
                    $metDescString = "Home taken off the market on " . $saleDate . ". View homes for sale near  " . $res->Addr . ", or learn if this home sold today!";
                    try {
                        $imgs = RetsPropertyDataImagesSold_new::select('image_urls')->where("listingID", $res->Ml_num)->get();
                    } catch (\Throwable $th) {
                    }
                    $res->propertiesImages = $imgs;
                }
            }
        }

        if (!empty($res)) {
            $roomsData = json_decode($res->RoomsDescription, true);
        }
        //dd($res);
        $flag = 1;
        $preLevel = "";
        for ($i = 1; $i <= 12; $i++) {
            if (isset($roomsData['Rm' . $i . '_len']) && $roomsData['Rm' . $i . '_len'] != "" && isset($roomsData['Rm' . $i . '_wth']) && $roomsData['Rm' . $i . '_wth'] != "" && isset($roomsData['Rm' . $i . '_out']) && $roomsData['Rm' . $i . '_out'] != "") {
                if ($flag == 1) {
                    $obj = array(
                        'isHeading' => true,
                        'name' => "Name",
                        'size' => "Size",
                        'features' => "Features",
                        'level' => "Level",
                    );
                    array_push($roomData, $obj);
                    $flag++;
                }
                $level = $roomsData['Level' . $i];
                if ($level == $preLevel) {
                    $level = "";
                } else {
                    $preLevel = $level;
                }
                $name = $roomsData['Rm' . $i . '_out'];
                $wth = $roomsData['Rm' . $i . '_wth'];
                $len = $roomsData['Rm' . $i . '_len'];
                $tempDec1 = $roomsData['Rm' . $i . '_dc' . 1 . '_out'];
                $tempDec2 = $roomsData['Rm' . $i . '_dc' . 2 . '_out'];
                $tempDec3 = $roomsData['Rm' . $i . '_dc' . 3 . '_out'];

                $dec1 = isset($tempDec1) ? $tempDec1 . " , " : "";
                $dec2 = isset($tempDec2) ? $tempDec2 . " , " : "";
                $dec3 = isset($tempDec3) ? $tempDec3 . " , " : "";
                $descFull = $dec1 . $dec2 . $dec3;
                $descFull = preg_replace("/,+/", ",", $descFull);
                $descFull = rtrim($descFull, ' ,');
                $obj = array(
                    'name' => $name,
                    'sizeInMt' => $len . " X " . $wth,
                    'sizeInFt' => round(($len * 3.28084), 2) . " X " . round(($wth * 3.28084), 2),
                    'desc' => $descFull,
                    "levels" => $level
                );
                array_push($roomData, $obj);
            }
        }
        // $res->RoomsDescription = $roomData;
        // $res->properties_imagesesssss	=$imgs;
        if ($res != []) {
            $res->Dom = getActualDom($res->Timestamp_sql);
            if (isset($res->Sp_dol) && $res->Sp_dol > 0) {
                $res->Lp_dol = $res->Sp_dol;
            }
        }
        $res->RoomsDescription = $roomData;
        $data['details'] = $res;
        $data["metaDesc"] = $metDescString;
        $data["table"] = $table;
        return response($data, 200);
    }


    /**
     * Get Similar Rent Data for Details Page
     * @param request
     * @return Json Response
     */
    public function similarProperty(Request $request)
    {
        // page setting data
        $pageSetting = Pages::select('Setting')->where('PageName', 'property details')
            ->first();
        $MinPrice = null;
        $MaxPrice = null;
        $city = null;
        $community = null;
        if ($pageSetting->Setting != null) {
            $pageSetting = json_decode($pageSetting->Setting);
            if ($pageSetting->priceSection != null) {
                $percent = ($pageSetting->priceSection * 10) / 100;
                $MinPrice = $pageSetting->priceSection - $percent;
                $MaxPrice = $pageSetting->priceSection + $percent;
            }
            if ($pageSetting->citySection != null && $pageSetting->citySection == 1) {
                $city = $request->Area;
            }
            if ($pageSetting->areaSection != null && $pageSetting->areaSection == 1) {
                $community = $request->Community;
            }
        }
        $city = $request->Area;
        $community = $request->Community;
        DB::enableQueryLog();
        $sale = getSimilar($community, "sale", $MinPrice, $MaxPrice, $city);
        $q = DB::getQueryLog();

        $rent = getSimilar($community, "rent", $MinPrice, $MaxPrice, $city);
        if ($sale['result'] != []) {
            $sale['result'] = collect($sale['result'])->map(function ($item) {
                $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                return $item;
            });
        }
        if ($rent['result'] != []) {
            $rent['result'] = collect($rent['result'])->map(function ($item) {
                $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                return $item;
            });
        }
        $data['similar'] = [
            "sale" => $sale['result'],
            "rent" => $rent['result'],
            'query' => $q,
        ];
        return response($data, 200);
    }

    /**
     * Get Similar Sale Properties for Details Page
     * @param request
     * @return Json Response
     */
    public function similarSaleProperty(Request $request)
    {
        // page setting data
        $pageSetting = Pages::select('Setting')->where('PageName', 'property details')
            ->first();
        $MinPrice = null;
        $MaxPrice = null;
        $city = null;
        $community = null;
        if ($pageSetting->Setting != null) {
            $pageSetting = json_decode($pageSetting->Setting);
            if ($pageSetting->priceSection != null) {
                $percent = ($pageSetting->priceSection * 10) / 100;
                $MinPrice = $pageSetting->priceSection - $percent;
                $MaxPrice = $pageSetting->priceSection + $percent;
            }
            if ($pageSetting->citySection != null && $pageSetting->citySection == 1) {
                $city = $request->Area;
            }
            if ($pageSetting->areaSection != null && $pageSetting->areaSection == 1) {
                $community = $request->Community;
            }
        }
        // $city = $request->Area;
        $community = $request->Community;
        $PropertySubType = $request->PropertySubType;
        $Ml_num = $request->Ml_num;
        DB::enableQueryLog();
        $sale = getSimilar($community, "Sale", $MinPrice, $MaxPrice, $city, $PropertySubType, $Ml_num);
        if ($sale['result'] != []) {
            $sale['result'] = collect($sale['result'])->map(function ($item) {
                //$item->Dom = getActualDom($item->Timestamp_sql);
                $item = collect($item)->all();
                $item = getDom($item);
                return $item;
            });
        }
        $q = DB::getQueryLog();
        $data['similarSale'] = [
            "sale" => $sale['result'],
            'query' => $q,
        ];
        return response($data, 200);
    }

    /**
     * Get Similar Rent Properties for Details Page
     * @param request
     * @return Json Response
     */
    public function similarRentProperty(Request $request)
    {
        // page setting data
        $pageSetting = Pages::select('Setting')->where('PageName', 'property details')
            ->first();
        $MinPrice = null;
        $MaxPrice = null;
        $city = null;
        $community = null;
        if ($pageSetting->Setting != null) {
            $pageSetting = json_decode($pageSetting->Setting);
            if ($pageSetting->priceSection != null) {
                $percent = ($pageSetting->priceSection * 10) / 100;
                $MinPrice = $pageSetting->priceSection - $percent;
                $MaxPrice = $pageSetting->priceSection + $percent;
            }
            if ($pageSetting->citySection != null && $pageSetting->citySection == 1) {
                $city = $request->Area;
            }
            if ($pageSetting->areaSection != null && $pageSetting->areaSection == 1) {
                $community = $request->Community;
            }
        }
        // $city = $request->Area;
        $community = $request->Community;
        $Ml_num = $request->Ml_num;
        $PropertySubType = $request->PropertySubType;
        DB::enableQueryLog();
        $rent = getSimilar($community, "rent", $MinPrice, $MaxPrice, $city, $PropertySubType, $Ml_num);
        $q = DB::getQueryLog();
        if ($rent['result'] != []) {
            $rent['result'] = collect($rent['result'])->map(function ($item) {
                //$item->Dom = getActualDom($item->Timestamp_sql);
                $item = collect($item)->all();
                $item = getDom($item);
                return $item;
            });
        }
        $data['similarRent'] = [
            "rent" => $rent['result'],
            'query' => $q,
        ];
        return response($data, 200);
    }

    /**
     * Sold Data for Details Page
     * @param request
     * @return Json Response
     */
    public function soldData(Request $request)
    {
        $select_value = PropertyConstants::SELECT_DATA;
        unset($select_value['id']);
        $select_value = implode(",", $select_value);
        $city = $request->Area;
        $community = $request->Community;
        $Ml_num = $request->Ml_num;
        $PropertySubType = $request->PropertySubType;
        if ($community) {
            $community = " and `Community` = '$community'";
        }
        if ($city) {
            $city = " and `City` = '$city'";
        }
        if ($PropertySubType) {
            $PropertySubType = " and `PropertySubType` = '$PropertySubType'";
        }

        if ($Ml_num) {
            $Ml_num = " and `ListingId` != '$Ml_num'";
        }
        $sql_query = "SELECT $select_value from RetsPropertyDataPurged where `Sp_dol` > 0 and `ImageUrl` is not null $community $PropertySubType $Ml_num $city order by `id` desc limit 4";
        DB::enableQueryLog();
        $res = DB::select($sql_query);
        if (count($res) <= 3) {
            $sql_query = "SELECT $select_value from RetsPropertyDataPurged where `Sp_dol` > 0 and `ImageUrl` is not null $community  $Ml_num $city order by `id` desc limit 4";
            DB::enableQueryLog();
            $res = DB::select($sql_query);
            if (count($res) <= 3) {
                $sql_query = "SELECT $select_value from RetsPropertyDataPurged where `ImageUrl` is not null $community  $Ml_num $city order by `id` desc limit 4";
                DB::enableQueryLog();
                $res = DB::select($sql_query);
            }
        }
        $sold = $res;
        $purgedQry = DB::getQueryLog();
        if ($sold != []) {
            /* $sold = collect($sold)->map(function ($item) {
                 $item->Dom = getActualDom($item->Timestamp_sql);
                 if ($item->Sp_dol) {
                     $item->ListPrice = $item->Sp_dol;
                 }
                 return $item;
             }); */
            $sold = collect($sold)->map(function ($item) {
                $item = collect($item)->all();
                $item = getDom($item);
                if ($item["Sp_dol"]) {
                    $item["ListPrice"] = $item["Sp_dol"];
                }
                return $item;
            });
        }
        $data['soldData'] = [
            'sold' => $sold,
            'query' => $purgedQry,
        ];
        return response($data, 200);
    }

    public function getAutoSearchResults(Request $request)
    {
        $text =  $request->all();
        $isSoldSearch = false;
        $suggesstionArr = array();
        $validator = Validator::make($request->all(), [
            "type" => "required",
            "query" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            if ($text["query"] == trim($text["query"]) && str_contains($text["query"], ' ')) {
                if (strlen($text["query"]) >= 5) {
                    $isSoldSearch = true;
                }
            }
            if (isset($text['query']) && $text['query'] == "default") {
                $result = get_default_auto_sugesstion();
                $Communities = $result["Community"];
                $cities = $result["City"];
                $flag = true;
                foreach ($cities as $city) {
                    if ($flag) {
                        $res = array('isHeading' => true, 'text' => 'City', 'value' => $city['City'], 'category' => 'Cities', 'group' => 'City');
                        array_push($suggesstionArr, $res);
                        $flag = false;
                    }
                    $res = array("text" => $city['City'], 'value' => $city['City'], 'category' => 'Cities', 'group' => 'City');
                    array_push($suggesstionArr, $res);
                    // $res = array('label' => $city['City'], 'category' => 'Cities');
                }
                $flag = true;
                foreach ($Communities as $Community) {
                    if ($flag) {
                        $res = array('isHeading' => true, 'text' => 'Neighborhood', 'value' => $Community['Community'], 'category' => 'Community', 'group' => 'Community');
                        array_push($suggesstionArr, $res);
                        $flag = false;
                    }
                    $res = array("text" => $Community['Community'], 'value' => $Community['Community'], 'category' => 'Community', 'group' => 'Community');
                    array_push($suggesstionArr, $res);

                    // $res = array('label' => $Community['Community'], 'category' => 'Community');
                    // array_push($suggesstionArr, $res);
                }
            } else {
                if ($text["type"] == "address") {
                    $addressSearch = get_auto_sugesstion('StandardAddress', $text['query'], $isSoldSearch);
                    $flag = true;
                    foreach ($addressSearch as $addr) {
                        if ($flag) {
                            $res = array('isHeading' => true, 'text' => 'StandardAddress', 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress', 'group' => 'StandardAddress');
                            array_push($suggesstionArr, $res);
                            $flag = false;
                        }
                        $res = array("text" => $addr['StandardAddress'], 'ListingId' => $addr['ListingId'], 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress', 'group' => 'StandardAddress');
                        array_push($suggesstionArr, $res);
                    }
                }
                if ($text["type"] == "listingId") {
                    $isSoldSearch = true;
                    $listingId = get_auto_sugesstion('ListingId', $text['query'], $isSoldSearch);
                    $flag = true;
                    foreach ($listingId as $listedid) {

                        if ($flag) {
                            $res = array('isHeading' => true, 'text' => 'ListingId', 'value' => $listedid['ListingId'], 'category' => 'ListingId', 'group' => 'ListingId');
                            array_push($suggesstionArr, $res);
                            $flag = false;
                        }
                        $res = array("text" => $listedid['ListingId'], 'value' => $listedid['ListingId'], 'category' => 'ListingId', 'group' => 'ListingId');
                        array_push($suggesstionArr, $res);
                    }
                }
                if ($text["type"] == "city") {
                    $cities = get_auto_sugesstion('City', $text['query'], $isSoldSearch);
                    $flag = true;
                    foreach ($cities as $city) {
                        if ($flag) {
                            $res = array('isHeading' => true, 'text' => 'City', 'value' => $city['City'], 'category' => 'Cities', 'group' => 'City');
                            array_push($suggesstionArr, $res);
                            $flag = false;
                        }
                        $res = array("text" => $city['City'], 'value' => $city['City'], 'category' => 'Cities', 'group' => 'City');
                        array_push($suggesstionArr, $res);
                    }
                }

                if ($text["type"] == "community") {
                    $Communities = get_auto_sugesstion('Community', $text['query'], $isSoldSearch);
                    $flag = true;
                    foreach ($Communities as $Community) {
                        if ($flag) {
                            $res = array('isHeading' => true, 'text' => 'Neighborhood', 'value' => $Community['Community'], 'category' => 'Neighborhood', 'group' => 'Community');
                            array_push($suggesstionArr, $res);
                            $flag = false;
                        }
                        $res = array("text" => $Community['Community'], 'value' => $Community['Community'], 'category' => 'Neighborhood', 'group' => 'Community');
                        array_push($suggesstionArr, $res);
                    }
                }
            }
        }
        return response($suggesstionArr, 200);
    }

    public function getAutoSearchResultsback(Request $request)
    {
        $text = $request->all();
        // $text["query"] = isset($text['query']) ? $text['query'] : "1";
        $isSoldSearch = false;
        if ($text["query"] == trim($text["query"]) && str_contains($text["query"], ' ')) {
            if (strlen($text["query"]) >= 5) {
                $isSoldSearch = true;
            }
        }
        $suggesstionArr = array();
        if (isset($text['query']) && $text['query'] == "default") {
            $result = get_default_auto_sugesstion();
            $Communities = $result["Community"];
            $cities = $result["City"];
            $flag = true;
            foreach ($cities as $city) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'City', 'value' => $city['City'], 'category' => 'Cities');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $city['City'], 'value' => $city['City'], 'category' => 'Cities');
                array_push($suggesstionArr, $res);
                // $res = array('label' => $city['City'], 'category' => 'Cities');
            }
            $flag = true;
            foreach ($Communities as $Community) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'Community', 'value' => $Community['Community'], 'category' => 'Community');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $Community['Community'], 'value' => $Community['Community'], 'category' => 'Community');
                array_push($suggesstionArr, $res);

                // $res = array('label' => $Community['Community'], 'category' => 'Community');
                // array_push($suggesstionArr, $res);
            }
        } else {
            $addressSearch = get_auto_sugesstion('StandardAddress', $text['query'], $isSoldSearch);
            $listingId = get_auto_sugesstion('ListingId', $text['query'], $isSoldSearch);
            $municipality = []; //get_auto_sugesstion('Area', $text['query'], $isSoldSearch);
            $cities = get_auto_sugesstion('City', $text['query'], $isSoldSearch);
            $Communities = []; //get_auto_sugesstion('Community', $text['query'],$isSoldSearch);
            $Countries = []; // get_auto_sugesstion('County', $text['query'], $isSoldSearch);
            // $Zipes = get_auto_sugesstion('PostalCode', $text['query']);
            $flag = true;
            foreach ($listingId as $listedid) {

                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'ListingId', 'value' => $listedid['ListingId'], 'category' => 'ListingId');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $listedid['ListingId'], 'value' => $listedid['ListingId'], 'category' => 'ListingId');
                array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($cities as $city) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'City', 'value' => $city['City'], 'category' => 'Cities');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $city['City'], 'value' => $city['City'], 'category' => 'Cities');
                array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($Communities as $Community) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'Community', 'value' => $Community['Community'], 'category' => 'Community');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $Community['Community'], 'value' => $Community['Community'], 'category' => 'Community');
                array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($Countries as $Country) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'County', 'value' => $Country['County'], 'category' => 'Countries');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $Country['County'], 'value' => $Country['County'], 'category' => 'Countries');
                array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($municipality as $data) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'Municipality', 'value' => $data['Municipality'], 'category' => 'Municipality');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $data['Municipality'], 'value' => $data['Municipality'], 'category' => 'Municipality');
                array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($addressSearch as $addr) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'StandardAddress', 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $addr['StandardAddress'], 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress');
                array_push($suggesstionArr, $res);
            }
        }

        return response($suggesstionArr, 200);
    }

    public function filterData(Request $request)
    {
        $queryData = RetsPropertyData::query();
        $type = $queryData->where("PropertyType", "!=", "")->distinct('PropertyType')->get('PropertyType');
        $subtype = $queryData->where("PropertySubType", "!=", "")->distinct('PropertySubType')->get('PropertySubType');
        $basement = $queryData->where("Bsmt1_out", "!=", "")->distinct('Bsmt1_out')->get('Bsmt1_out');
        $featuresData = FeaturesMaster::limit(20)->get();
        $price = getPriceList();
        $propertyType = [];
        $propertySubType = [];
        $format_basement = [];
        $basements = [];
        $features = [];
        // this is for data build
        $temp_type_data = [];
        $temp_sub_data = [];
        foreach ($featuresData as $key => $value) {
            if ($value['Features'] != '') {
                $obj = [
                    "text" => $value->Features,
                    "value" => $value->id,
                ];
                array_push($features, $obj);
            }
        }
        foreach ($basement as $key => $value) {
            if ($value['Bsmt1_out'] != '') {
                $basements[] = $value['Bsmt1_out'];
                $obj = [
                    "text" => $value->Bsmt1_out,
                    "value" => $value->Bsmt1_out,
                ];
                array_push($format_basement, $obj);
            }
        }
        if ($request->is_search) {
            foreach ($type as $key => $value) {
                $obj = [
                    "text" => $value->PropertyType,
                    "value" => $value->PropertyType,
                ];
                array_push($propertyType, $obj);
            }
            foreach ($subtype as $key => $value) {
                $obj = [
                    "text" => $value->PropertySubType,
                    "value" => $value->PropertySubType,
                ];
                array_push($propertySubType, $obj);
            }
            $response['property_type'] = $propertyType;
            $response['subtype'] = $propertySubType;
            $response['basement'] = $format_basement;
            $response['features'] = $features;
        } else {
            $response['property_type'] = $type;
            $response['subtype'] = $subtype;
            $response['basement'] = $basement;
            $response['features'] = $features;
        }
        $response['price'] = $price;
        // $response['Sqft'] = $Sqft;
        return response($response, 200);
    }

    public function propertiesSearch(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        $isDefault = false;
        if (
            $payload["text_search"] == ""
            && $payload["propertyType"] == "" && !count($payload["propertySubType"]) && $payload["price_min"] == "" &&
            $payload["price_max"] == "" && $payload["beds"] == "" && $payload["baths"] == "" && $payload["status"] == ""
            && $payload["sort_by"] == "" && !count($payload["basement"]) && !count($payload["features"]) &&
            $payload["Sqft"] == "" && $payload["shape"] == "" && $payload["curr_path_query"] == "" && $payload["City"] == ""
        ) {
            $params["curr_path_query"] = "43.866933327462874 -80.30768976442968,43.095997607180095 -80.30768976442968,43.11859110764115 -79.09732565052512,43.891718326524796 -79.14890366674238,43.866933327462874 -80.30768976442968";
            $payload["curr_path_query"] = "43.866933327462874 -80.30768976442968,43.095997607180095 -80.30768976442968,43.11859110764115 -79.09732565052512,43.891718326524796 -79.14890366674238,43.866933327462874 -80.30768976442968";
            $payload['shape'] = "polygon";
            $params['shape'] = "polygon";
            $isDefault = true;
        } else {
        }
        if (isset($params["text_search"])) {
            $cityText = $params["text_search"];
        }
        if ($payload['City'] == "") {
            unset($payload['City']);
            unset($params['City']);
        } else {
            $cityText = $payload['City'];
        }
        $searchFilter = array();
        $filteredData = array();
        $orFilter = array();
        $array_data = array();
        $textSearchFilter = '';
        $offset = 0;
        $curr_page = 0;
        $limit = PropertyConstants::LIMIT;
        if (isset($payload['curr_page']) && $payload['curr_page'] != '' && $payload['curr_page'] > 0) {
            $curr_page = (int)$payload['curr_page'];
            $offset = (($curr_page - 1) >= 0) ? ($curr_page - 1) * $limit : 0; //*$limit ;
        }
        if (isset($params['sort_by']) && !empty($params['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $params['sort_by'];
            if ($sortData === 'price_high') {
                $field = 'ListPrice';
                $orderBy = 'Desc';
            } else if ($sortData === 'price_low') {
                $field = "ListPrice";
                $orderBy = 'Asc';
            } else if ($sortData === 'dom_high') {
                $field = 'Dom';
                $orderBy = 'Desc';
            } else if ($sortData === 'dom_low') {
                $field = 'Dom';
                $orderBy = 'Asc';
            }
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'Dom';
            $orderBy = 'Asc';
        }
        $response['allparams'] = $params;
        unset($params['sort_by']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['text_search']) && !empty($payload['text_search'])) {
            unset($params['shape']);
            unset($params['curr_shape']);
            unset($params['curr_bounds']);
            unset($params['radius']);
            unset($params['curr_path']);
            unset($params['bounds']);
            unset($params['center_lat']);
            unset($params['center_lng']);
            unset($params['curr_bounds']);
            unset($params['curr_page']);
            unset($params['curr_path_query']);
            unset($params['curr_radius']);
        } else {
            if (isset($payload['shape']) && $payload['shape'] != '') {
                $dis_sel = "";
                $dis_cond = "";
                $shape = $payload['shape'];
                if ($payload['shape'] == 'circle') {
                    $center_lat = $payload['center_lat'];
                    $center_lng = $payload['center_lng'];
                    $radius = $payload['radius'];
                    $dis_sel = " , ( 6371 * acos ( cos ( radians(" . $center_lat . ") ) * cos( radians( Latitude ) ) * cos( radians( Longitude ) - radians($center_lng) ) + sin ( radians(" . $center_lat . ") ) * sin( radians( Latitude ) )  ) ) AS distance ";
                    if (isset($payload['radius']) && $payload['radius'] > 0) {
                        $rdinkm = $payload['radius'] / 1000;
                        $dis_cond = " distance < " . $rdinkm;
                    } else {
                        $dis_cond = " distance < 5 ";
                    }
                } else if ($payload['shape'] == 'polygon') {
                    $temp_query = rawurldecode($payload['curr_path_query']);
                    $path_query = rtrim($temp_query, ",");
                    $dis_cond = "ST_WITHIN(point(Latitude,Longitude), ST_GeomFromText('POLYGON((" . $path_query . "))') )";
                } else if ($payload['shape'] == 'rectangle') {
                    if (isset($payload['curr_bounds']) && $payload['curr_bounds'] != '') {
                        $isDefault = true;
                        $allbound = explode('###', $payload['curr_bounds']);
                        $p1 = explode(',', $allbound[0]);
                        $p2 = explode(',', $allbound[1]);
                        $a = (float)$p1[1];
                        $b = (float)$p1[0];
                        $c = (float)$p2[1];
                        $d = (float)$p2[0];
                        $condition1 = $a > $c ? "Latitude > $c AND Latitude < $a" : "Latitude > $a AND Latitude < $c";
                        $condition2 = $b > $d ? "Longitude > $d AND Longitude < $b" : "longitude > $b AND Longitude < $d";
                        $dis_cond = "( $condition1 ) AND ( $condition2 )";
                    }
                }
                unset($params['shape']);
                unset($params['curr_shape']);
                unset($params['curr_bounds']);
                unset($params['radius']);
                unset($params['curr_path']);
                unset($params['bounds']);
                unset($params['center_lat']);
                unset($params['center_lng']);
                unset($params['curr_bounds']);
                unset($params['curr_page']);
                unset($params['curr_path_query']);
                unset($params['curr_radius']);
            }
        }
        unset($params['curr_bounds']);
        unset($params['curr_page']);
        unset($params['sort_by']);
        foreach ($params as $searchKey => $searchValue) {
            if (($searchValue) === '') {
                continue;
            }
            switch ($searchKey) {
                case 'BedroomsTotal':
                    $searchFilter['BedroomsTotal'] = $searchValue;
                    break;
                case 'BathroomsFull':
                    $searchFilter['BathroomsFull'] = $searchValue;
                    break;
                case 'text_search':
                    $textSearchFilter = $searchValue;
                    $cityText = $searchValue;
                    break;
                case 'price_max':
                    $searchFilter['price_max'] = $searchValue;
                    break;
                case 'price_min':
                    $searchFilter['price_min'] = $searchValue;
                    break;
                case 'S_r':
                    $array_data['S_r'] = $searchValue;
                    break;
                case 'Type_own1_out':
                    $array_data['Type_own1_out'] = $searchValue;
                    break;
                case 'Gar':
                    $searchFilter['Gar'] = $searchValue;
                    break;

                case 'Extras':
                    $textSearchFilter = $searchValue;
                    break;

                case 'Park_spcs':
                    $searchFilter['Park_spcs'] = $searchValue;
                    break;

                case 'Pool':
                    $searchFilter['Pool'] = $searchValue;
                    break;

                case 'prop_feature':
                    $orFilter['Prop_feat1_out'] = $searchValue;
                    $orFilter['Prop_feat2_out'] = $searchValue;
                    $orFilter['Prop_feat3_out'] = $searchValue;
                    $orFilter['Prop_feat4_out'] = $searchValue;
                    $orFilter['Prop_feat5_out'] = $searchValue;
                    $orFilter['Prop_feat6_out'] = $searchValue;
                    break;

                case 'Waterfront':
                    $searchFilter['Waterfront'] = $searchValue;
                    break;

                case 'Bsmt1_out':
                    $array_data['Bsmt1_out'] = $searchValue;
                    break;
                case 'propertyType':
                    $searchFilter['PropertyType'] = $searchValue;
                    break;
                case 'propertySubType':
                    $searchFilter['PropertySubType'] = $searchValue;
                    break;
                default:
                    $searchFilter[$searchKey] = $searchValue;
            }
        }
        unset($params['price_min']);
        unset($params['price_max']);
        unset($params['status']);
        $startTiming = date("H:i:s");

        if (empty($textSearchFilter)) {
            $mapData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT_SQL,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "map",
                $isDefault
            );
            $filteredData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "main",
                $isDefault
            );
        } else {
            $filteredData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "main",
                $isDefault
            );
            $mapData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT_SQL,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "map",
                $isDefault
            );
        }
        $endTiming = date("H:i:s");
        $txt = "";
        if (isset($request->beds) && $request->beds != "") {
            $txt .= $request->beds . " Beds ";
        }
        if (isset($request->baths) && $request->baths != "") {
            if ($request->beds != "") {
                $txt .= "and " . $request->baths . " Baths ";
            } else {
                $txt .= $request->baths . " Baths ";
            }
        }
        if (isset($request->propertyType) && $request->propertyType != "") {
            $txt .= $request->propertyType . " ";
        }
        $txt .= "Properties ";
        if (isset($request->status) && $request->status != "") {
            $txt .= "For " . $request->status;
        }
        if (isset($request->text_search) && $request->text_search != "") {
            $txt .= " in " . $request->text_search;
        }
        $total_properties = $filteredData['total'];
        $countInWords = " " . $total_properties . " Listings";
        if ($total_properties == 0) {
            $txt = "Oops! sorry No exact matches Found";
        }
        if (isset($filteredData['total_temp']) && $filteredData['total_temp'] !== 0) {
            $total_properties = $filteredData['total_temp'];
        }
        $cityData = [];
        $areaData = [];
        $cityData = PolygonsData::where("cityName", $cityText)->where("cityPolygons", "<>", "")->first();
        $areaData = PolygonsData::where("areasName", $cityText)->where("areasPolygons", "<>", "")->first();
        $response["areaData"] = $areaData;
        $response["textShow"] = $txt;
        $response["cityData"] = $cityData;
        $response['alldata'] = $filteredData['result'];
        $response['total'] = $total_properties;
        $response['countInWords'] = $countInWords;
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        $response['mapdata'] = $mapData['result'];
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
        return response($response, 200);
    }

    public function propertiesSearchProperty(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        if (isset($params['features'])) {
            if ($params['features'] == 'Central_vac') {
                $params['Central_vac'] = "Y";
            } elseif ($params['features'] == 'Elevator') {
                $params['Elevator'] = "Y";
            } elseif ($params['features'] == 'Central Air') {
                $params['A_c'] = "Central Air";
            } elseif ($params['features'] == 'Den_fr') {
                $params['Den_fr'] = "Y";
            } elseif ($params['features'] == 'Gar') {
                $params['Gar'] = "Y";
            } elseif ($params['features'] == 'Extras') {
                $params['Extras'] = "Gym";
            } elseif ($params['features'] == 'Park_spcs') {
                $params['Park_spcs'] = "Y";
            } elseif ($params['features'] == 'Pool') {
                $params['Pool'] = "Y";
            } elseif ($params['features'] == 'prop_feature') {
                $params['prop_feature'] = "Ravine";
            } elseif ($params['features'] == 'Waterfront') {
                $params['Waterfront'] = "Y";
            } elseif ($params['features'] == 'A_c') {
                $params['A_c'] = "Y";
            }
        }
        unset($params['features']);
        $searchFilter = array();
        $filteredData = array();
        $orFilter = array();
        $array_data = array();
        $textSearchFilter = '';
        $offset = 0;
        $curr_page = 0;
        $limit = PropertyConstants::LIMIT;
        if (isset($payload['curr_page']) && $payload['curr_page'] != '' && $payload['curr_page'] > 0) {
            $curr_page = (int)$payload['curr_page'];
            $offset = (($curr_page - 1) >= 0) ? ($curr_page - 1) * $limit : 0; //*$limit ;
        }
        if (isset($params['sort_by']) && !empty($params['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $params['sort_by'];
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
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'inserted_time';
            $orderBy = 'Desc';
        }
        $response['allparams'] = $params;
        unset($params['sort_by']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['text_search']) && !empty($payload['text_search'])) {
            unset($params['shape']);
            unset($params['curr_shape']);
            unset($params['curr_bounds']);
            unset($params['radius']);
            unset($params['curr_path']);
            unset($params['bounds']);
            unset($params['center_lat']);
            unset($params['center_lng']);
            unset($params['curr_bounds']);
            unset($params['curr_page']);
            unset($params['curr_path_query']);
            unset($params['curr_radius']);
        } else {
            if (isset($payload['shape']) && $payload['shape'] != '') {
                $dis_sel = "";
                $dis_cond = "";
                $shape = $payload['shape'];
                if ($payload['shape'] == 'circle') {
                    $center_lat = $payload['center_lat'];
                    $center_lng = $payload['center_lng'];
                    $radius = $payload['radius'];
                    $dis_sel = " , ( 6371 * acos ( cos ( radians(" . $center_lat . ") ) * cos( radians( Latitude ) ) * cos( radians( Longitude ) - radians($center_lng) ) + sin ( radians(" . $center_lat . ") ) * sin( radians( Latitude ) )  ) ) AS distance ";
                    if (isset($payload['radius']) && $payload['radius'] > 0) {
                        $rdinkm = $payload['radius'] / 1000;
                        $dis_cond = " distance < " . $rdinkm;
                    } else {
                        $dis_cond = " distance < 5 ";
                    }
                } else if ($payload['shape'] == 'polygon') {
                    $temp_query = rawurldecode($payload['curr_path_query']);
                    $path_query = rtrim($temp_query, ",");
                    $dis_cond = "ST_WITHIN(point(Latitude,Longitude), ST_GeomFromText('POLYGON((" . $path_query . "))') )";
                } else if ($payload['shape'] == 'rectangle') {
                    if (isset($payload['curr_bounds']) && $payload['curr_bounds'] != '') {
                        $allbound = explode('###', $payload['curr_bounds']);

                        $p1 = explode(',', $allbound[0]);
                        $p2 = explode(',', $allbound[1]);
                        $a = (float)$p1[1];
                        $b = (float)$p1[0];
                        $c = (float)$p2[1];
                        $d = (float)$p2[0];

                        $condition1 = $a > $c ? "Latitude > $c AND Latitude < $a" : "Latitude > $a AND Latitude < $c";
                        $condition2 = $b > $d ? "Longitude > $d AND Longitude < $b" : "longitude > $b AND Longitude < $d";
                        $dis_cond = "( $condition1 ) AND ( $condition2 )";
                    }
                }
                unset($params['shape']);
                unset($params['curr_shape']);
                unset($params['curr_bounds']);
                unset($params['radius']);
                unset($params['curr_path']);
                unset($params['bounds']);
                unset($params['center_lat']);
                unset($params['center_lng']);
                unset($params['curr_bounds']);
                unset($params['curr_page']);
                unset($params['curr_path_query']);
                unset($params['curr_radius']);
            }
        }
        unset($params['curr_bounds']);
        unset($params['curr_page']);
        unset($params['sort_by']);
        foreach ($params as $searchKey => $searchValue) {
            if (($searchValue) === '') {
                continue;
            }
            switch ($searchKey) {
                case 'BedroomsTotal':
                    $searchFilter['BedroomsTotal'] = $searchValue;
                    break;
                case 'BathroomsFull':
                    $searchFilter['BathroomsFull'] = $searchValue;
                    break;
                case 'text_search':
                    $textSearchFilter = $searchValue;
                    break;
                case 'price_max':
                    $searchFilter['price_max'] = $searchValue;
                    break;
                case 'price_min':
                    $searchFilter['price_min'] = $searchValue;
                    break;
                case 'S_r':
                    $array_data['S_r'] = $searchValue;
                    break;
                case 'Type_own1_out':
                    $array_data['Type_own1_out'] = $searchValue;
                    break;
                case 'Gar':
                    $searchFilter['Gar'] = $searchValue;
                    break;

                case 'Extras':
                    $textSearchFilter = $searchValue;
                    break;

                case 'Park_spcs':
                    $searchFilter['Park_spcs'] = $searchValue;
                    break;

                case 'Pool':
                    $searchFilter['Pool'] = $searchValue;
                    break;

                case 'prop_feature':
                    $orFilter['Prop_feat1_out'] = $searchValue;
                    $orFilter['Prop_feat2_out'] = $searchValue;
                    $orFilter['Prop_feat3_out'] = $searchValue;
                    $orFilter['Prop_feat4_out'] = $searchValue;
                    $orFilter['Prop_feat5_out'] = $searchValue;
                    $orFilter['Prop_feat6_out'] = $searchValue;
                    break;

                case 'Waterfront':
                    $searchFilter['Waterfront'] = $searchValue;
                    break;

                case 'Bsmt1_out':
                    $array_data['Bsmt1_out'] = $searchValue;
                    break;
                case 'propertyType':
                    $searchFilter['PropertyType'] = $searchValue;
                    break;
                case 'propertySubType':
                    $searchFilter['PropertySubType'] = $searchValue;
                    break;
                default:
                    $searchFilter[$searchKey] = $searchValue;
            }
        }
        unset($params['price_min']);
        unset($params['price_max']);
        unset($params['status']);
        if (empty($textSearchFilter)) {
            $filteredData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "main"
            );
        } else {
            $filteredData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::PAGE_DATA_LIMIT,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "main"
            );
        }
        $txt = "";
        if (isset($request->beds) && $request->beds != "") {
            $txt .= $request->beds . " Beds ";
        }
        if (isset($request->baths) && $request->baths != "") {
            if ($request->beds != "") {
                $txt .= "and " . $request->baths . " Baths ";
            } else {
                $txt .= $request->baths . " Baths ";
            }
        }
        if (isset($request->propertyType) && $request->propertyType != "") {
            $txt .= $request->propertyType . " ";
        }
        $txt .= "Properties ";
        if (isset($request->status) && $request->status != "") {
            $txt .= "For " . $request->status;
        }
        if (isset($request->text_search) && $request->text_search != "") {
            $txt .= " in " . $request->text_search;
        }
        $total_properties = $filteredData['total'];
        $countInWords = " " . $total_properties . " Listings";
        if ($total_properties == 0) {
            $txt = "Oops! sorry No exact matches Found";
        }
        $response["textShow"] = $txt;
        $response['alldata'] = $filteredData['result'];
        $response['total'] = $total_properties;
        $response['countInWords'] = $countInWords;
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        return response($response, 200);
    }

    public function propertiesSearchMap(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        if (isset($params['features'])) {
            if ($params['features'] == 'Central_vac') {
                $params['Central_vac'] = "Y";
            } elseif ($params['features'] == 'Elevator') {
                $params['Elevator'] = "Y";
            } elseif ($params['features'] == 'Central Air') {
                $params['A_c'] = "Central Air";
            } elseif ($params['features'] == 'Den_fr') {
                $params['Den_fr'] = "Y";
            } elseif ($params['features'] == 'Gar') {
                $params['Gar'] = "Y";
            } elseif ($params['features'] == 'Extras') {
                $params['Extras'] = "Gym";
            } elseif ($params['features'] == 'Park_spcs') {
                $params['Park_spcs'] = "Y";
            } elseif ($params['features'] == 'Pool') {
                $params['Pool'] = "Y";
            } elseif ($params['features'] == 'prop_feature') {
                $params['prop_feature'] = "Ravine";
            } elseif ($params['features'] == 'Waterfront') {
                $params['Waterfront'] = "Y";
            } elseif ($params['features'] == 'A_c') {
                $params['A_c'] = "Y";
            }
        }
        unset($params['features']);
        $searchFilter = array();
        $filteredData = array();
        $orFilter = array();
        $array_data = array();
        $textSearchFilter = '';
        $offset = 0;
        $curr_page = 0;
        $limit = PropertyConstants::LIMIT;
        if (isset($payload['curr_page']) && $payload['curr_page'] != '' && $payload['curr_page'] > 0) {
            $curr_page = (int)$payload['curr_page'];
            $offset = (($curr_page - 1) >= 0) ? ($curr_page - 1) * $limit : 0; //*$limit ;
        }
        if (isset($params['sort_by']) && !empty($params['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $params['sort_by'];
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
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'inserted_time';
            $orderBy = 'Desc';
        }
        unset($params['sort_by']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['text_search']) && !empty($payload['text_search'])) {
            unset($params['shape']);
            unset($params['curr_shape']);
            unset($params['curr_bounds']);
            unset($params['radius']);
            unset($params['curr_path']);
            unset($params['bounds']);
            unset($params['center_lat']);
            unset($params['center_lng']);
            unset($params['curr_bounds']);
            unset($params['curr_page']);
            unset($params['curr_path_query']);
            unset($params['curr_radius']);
        } else {
            if (isset($payload['shape']) && $payload['shape'] != '') {
                $dis_sel = "";
                $dis_cond = "";
                $shape = $payload['shape'];
                if ($payload['shape'] == 'circle') {
                    $center_lat = $payload['center_lat'];
                    $center_lng = $payload['center_lng'];
                    $radius = $payload['radius'];
                    $dis_sel = " , ( 6371 * acos ( cos ( radians(" . $center_lat . ") ) * cos( radians( Latitude ) ) * cos( radians( Longitude ) - radians($center_lng) ) + sin ( radians(" . $center_lat . ") ) * sin( radians( Latitude ) )  ) ) AS distance ";
                    if (isset($payload['radius']) && $payload['radius'] > 0) {
                        $rdinkm = $payload['radius'] / 1000;
                        $dis_cond = " distance < " . $rdinkm;
                    } else {
                        $dis_cond = " distance < 5 ";
                    }
                } else if ($payload['shape'] == 'polygon') {
                    $temp_query = rawurldecode($payload['curr_path_query']);
                    $path_query = rtrim($temp_query, ",");
                    $dis_cond = "ST_WITHIN(point(Latitude,Longitude), ST_GeomFromText('POLYGON((" . $path_query . "))') )";
                } else if ($payload['shape'] == 'rectangle') {
                    if (isset($payload['curr_bounds']) && $payload['curr_bounds'] != '') {
                        $allbound = explode('###', $payload['curr_bounds']);

                        $p1 = explode(',', $allbound[0]);
                        $p2 = explode(',', $allbound[1]);
                        $a = (float)$p1[1];
                        $b = (float)$p1[0];
                        $c = (float)$p2[1];
                        $d = (float)$p2[0];

                        $condition1 = $a > $c ? "Latitude > $c AND Latitude < $a" : "Latitude > $a AND Latitude < $c";
                        $condition2 = $b > $d ? "Longitude > $d AND Longitude < $b" : "longitude > $b AND Longitude < $d";
                        $dis_cond = "( $condition1 ) AND ( $condition2 )";
                    }
                }
                unset($params['shape']);
                unset($params['curr_shape']);
                unset($params['curr_bounds']);
                unset($params['radius']);
                unset($params['curr_path']);
                unset($params['bounds']);
                unset($params['center_lat']);
                unset($params['center_lng']);
                unset($params['curr_bounds']);
                unset($params['curr_page']);
                unset($params['curr_path_query']);
                unset($params['curr_radius']);
            }
        }
        unset($params['curr_bounds']);
        unset($params['curr_page']);
        unset($params['sort_by']);
        foreach ($params as $searchKey => $searchValue) {
            if (($searchValue) === '') {
                continue;
            }
            switch ($searchKey) {
                case 'BedroomsTotal':
                    $searchFilter['BedroomsTotal'] = $searchValue;
                    break;
                case 'BathroomsFull':
                    $searchFilter['BathroomsFull'] = $searchValue;
                    break;
                case 'text_search':
                    $textSearchFilter = $searchValue;
                    break;
                case 'price_max':
                    $searchFilter['price_max'] = $searchValue;
                    break;
                case 'price_min':
                    $searchFilter['price_min'] = $searchValue;
                    break;
                case 'S_r':
                    $array_data['S_r'] = $searchValue;
                    break;
                case 'Type_own1_out':
                    $array_data['Type_own1_out'] = $searchValue;
                    break;
                case 'Gar':
                    $searchFilter['Gar'] = $searchValue;
                    break;

                case 'Extras':
                    $textSearchFilter = $searchValue;
                    break;

                case 'Park_spcs':
                    $searchFilter['Park_spcs'] = $searchValue;
                    break;

                case 'Pool':
                    $searchFilter['Pool'] = $searchValue;
                    break;

                case 'prop_feature':
                    $orFilter['Prop_feat1_out'] = $searchValue;
                    $orFilter['Prop_feat2_out'] = $searchValue;
                    $orFilter['Prop_feat3_out'] = $searchValue;
                    $orFilter['Prop_feat4_out'] = $searchValue;
                    $orFilter['Prop_feat5_out'] = $searchValue;
                    $orFilter['Prop_feat6_out'] = $searchValue;
                    break;

                case 'Waterfront':
                    $searchFilter['Waterfront'] = $searchValue;
                    break;

                case 'Bsmt1_out':
                    $array_data['Bsmt1_out'] = $searchValue;
                    break;
                case 'propertyType':
                    $searchFilter['PropertyType'] = $searchValue;
                    break;
                case 'propertySubType':
                    $searchFilter['PropertySubType'] = $searchValue;
                    break;
                default:
                    $searchFilter[$searchKey] = $searchValue;
            }
        }
        unset($params['price_min']);
        unset($params['price_max']);
        unset($params['status']);
        if (empty($textSearchFilter)) {
            $mapData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT_SQL,
                $field,
                $orderBy,
                '',
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "map"
            );
        } else {
            $mapData = get_search_result(
                $searchFilter,
                $offset,
                PropertyConstants::MAP_PAGE_DATA_LIMIT_SQL,
                $field,
                $orderBy,
                $textSearchFilter,
                $dis_cond,
                $dis_sel,
                $shape,
                $array_data,
                $orFilter,
                $type = "map"
            );
        }
        $response['mapdata'] = $mapData['result'];
        return response($response, 200);
    }

    public function getDataFromYelp(Request $request)
    {
        $postData = $request->all();
        $websetting = Websetting::select('WebsiteName', 'YelpKey', 'YelpClientId', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag')
            ->where("AdminId", $request->agentId)
            ->first();
        $key = $websetting->YelpKey;
        $data = yelp_data($key, $postData["latitude"], $postData["longitude"], $postData["type"]);
        return response($data, 200);
    }

    public function shareEmail(Request $request)
    {
        $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'FavIconAltTag')
            ->where("AdminId", $request->agentId)
            ->first();
        $retsPropData = getProperties("", $request->property_mls_no);
        $propData = array(
            "name" => $request->name,
            "property_url" => $request->property_url,
            "propertyDetails" => $request->details,
            "websetting" => $websetting,
        );
        $addr = isset($request->details['Addr']) ? $request->details['Addr'] : "";
        $subject = $request->name . " has shared a property  #MLS - " . $request->property_mls_no . " - " . $addr;
        // $emails = explode(',', $request->emails,true);
        $template = getTemplate($propData);
        $emails = $request->emails;
        array_push($emails, "ram@peregrine-it.com");
        sendEmail("SMTP", $request->email, $emails, "mukesh@peregrine-it.com", "sagar@peregrine-it.com", $subject, $template, "", null);
        $data = array(
            "status" => 200,
            "message" => "Successful",
        );
        return response($data, 200);
    }

    public function saveSearch(Request $request)
    {
        $query = SavedSearchFilter::query();
        $filterData = $request->filtersData;
        $filterData = json_decode($filterData, true);
        $data = array(
            "textSearch" => isset($filterData['text_search']) ? $filterData['text_search'] : "",
            "className" => isset($filterData['propertyType']) ? $filterData['propertyType'] : "",
            "priceMin" => isset($filterData['price_min']) ? $filterData['price_min'] : "",
            "priceMax" => isset($filterData['price_max']) ? $filterData['price_max'] : "",
            "bedsTotal" => isset($filterData['beds']) ? $filterData['beds'] : "",
            "bathsFull" => isset($filterData['baths']) ? $filterData['baths'] : "",
            "status" => isset($filterData['status']) ? $filterData['status'] : "",
            "features" => isset($filterData['features']) ? json_encode($filterData['features']) : "",
            "propertySubType" => isset($filterData['propertySubType']) ? json_encode($filterData['propertySubType']) : "",
            "Bsmt1Out" => isset($filterData['basement']) ? json_encode($filterData['basement']) : "",
            "openHouse" => isset($filterData['openhouse']) ? $filterData['openhouse'] : "",
            "dom" => isset($filterData['Dom']) ? $filterData['Dom'] : "",
            "Sqft" => isset($filterData['Sqft']) ? $filterData['Sqft'] : "",
            "shape" => isset($filterData['shape']) ? $filterData['shape'] : "",
            "currPathQuery" => isset($filterData['curr_path_query']) ? $filterData['curr_path_query'] : "",
            "city" => isset($filterData['City']) ? $filterData['City'] : "",
            "emailAlert" => 1,
            "textAlert" => 0,
            "frequency" => $request->frequency,
            "filterName" => $request->searchName,
            "userId" => $request->userId,
            "agentId" => $request->agentId

        );
        $res = $query->insert($data);
        $data = array(
            "status" => 200,
            "message" => "Successful",
        );
        return response($data, 200);
    }


    public function markerInfo(Request $request)
    {
        $ReqFilters = [];
        if ($request->status) {
            if ($request->status == "Sold") {
                $status = "U";
            } elseif ($request->status == "Rented") {
                $status = "U";
            } elseif ($request->status == "Sale") {
                $status = "A";
            } elseif ($request->status == "Lease") {
                $status = "A";
            }
        }
        if ($status == "U") {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            $query->where("ListingId", $request->id);
            $res = $query->first();
        } else {
            $query = RetsPropertyData::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            $query->where("ListingId", $request->id);
            $res = $query->first();
        }
        if ($res != []) {
            $res = collect($res)->all();
            $res = getDom($res);
        }
        $data = array(
            "status" => 200,
            "message" => "Successful",
            "data" => $res,
        );
        return response($data, 200);
    }



    public function markerInfo_old(Request $request)
    {
        DB::enableQueryLog();
        $isSoldSearch = false;
        $ReqFilters = [];
        $ReqFilters["ListingId"] = $request->id;

        if ($request->status) {
            if ($request->status == "Sold") {
                $ReqFilters['PropertyStatus'] = "Sale";
                $ReqFilters["Status"] = "U";
                $isSoldSearch = true;
            }
            if ($request->status == "Rented") {
                $ReqFilters['PropertyStatus'] = "Lease";
                $ReqFilters["Status"] = "U";
                $isSoldSearch = true;
            }
            if ($request->status == "Sale") {
                $ReqFilters['PropertyStatus'] = "Sale";
                $ReqFilters["Status"] = "A";
            }
            if ($request->status == "Lease") {
                $ReqFilters['PropertyStatus'] = "Lease";
                $ReqFilters["Status"] = "A";
            }
        }
        if ($isSoldSearch) {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            foreach ($ReqFilters as $key => $value) {
                $query->where($key, $value);
            }
            $res = $query->first();
        } else {
            $query = RetsPropertyData::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            foreach ($ReqFilters as $key => $value) {
                $query->where($key, $value);
            }
            $res = $query->first();
        }

        unset($ReqFilters["Status"]);
        unset($ReqFilters["PropertyStatus"]);
        if (!$res) {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            foreach ($ReqFilters as $key => $value) {
                $query->where($key, $value);
            }
            $res = $query->first();
        }
        if (!$res) {
            $query = RetsPropertyData::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            foreach ($ReqFilters as $key => $value) {
                $query->where($key, $value);
            }
            $res = $query->first();
        }
        //$res->Dom = getActualDom($res->Timestamp_sql);
        if ($res != []) {
            $res = collect($res)->all();
            $res = getDom($res);
        }

        $data = array(
            "status" => 200,
            "message" => "Successful",
            "data" => $res,
            "queryLog" => DB::getQueryLog()
        );
        return response($data, 200);
    }

    public function listingHistory(Request $request)
    {
        $community = $request->Community;
        $Municipality = $request->Municipality;
        $PropertyStatus = $request->PropertyStatus;
        $PropertyType = $request->PropertyType;
        $standardAddress = $request->Addr;
        $months = 24;
        DB::enableQueryLog();
        $sold = RetsPropertyDataPurged::select(
            'id',
            'Sp_dol',
            "Orig_dol",
            "ListPrice",
            "ListingId",
            "Sp_date",
            "Timestamp_sql",
            "inserted_time",
            "updated_time",
            "Status",
            "PropertyStatus",
            "ContractDate",
            "LastStatus",
            "Sp_date",
            "ExpiredDate"
        )->where("StandardAddress", $standardAddress)
            ->distinct("ListingId")
            ->orderBy('ContractDate', 'desc')
            ->get();
        foreach ($sold as $key => $value) {
            if ($value["LastStatus"] == "Lsd" && $value["Status"] == "U") {
                if ($value["ContractDate"] != "") {
                    $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                    $value->sold_updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
                } else {
                    $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                    $value->sold_updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
                }
            } elseif ($value["Status"] == "U" && $value["LastStatus"] == "Sld") {
                if ($value["Sp_date"] != "") {
                    $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                    $value->sold_updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
                } else {
                    $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                    $value->sold_updated_time = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
                }
            } elseif ($value["PropertyStatus"] == "Lease" && $value["Status"] == "A") {
                if ($value["ContractDate"] != "") {
                    $value["Dom"] = getActualDom($value["ContractDate"]);
                } else {
                    $value["Dom"] = getActualDom($value["Timestamp_sql"]);
                }
            } elseif ($value["Status"] == "D" || $value["LastStatus"] == "Ter") {
                $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                $value->sold_updated_time = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
            } elseif ($value["Status"] == "U" && $value["LastStatus"] == "Exp") {
                $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                $value->sold_updated_time = Carbon::parse($value->ExpiredDate)->format('Y-m-d');
            } else {
                $value->inserted_time = Carbon::parse($value->ContractDate)->format('Y-m-d');
                $value->sold_updated_time = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
                //$value["Dom"] = getActualDom($value["Timestamp_sql"]);
            }

            $value->Sp_date = Carbon::parse($value->Sp_date)->format('Y-m-d');
            $value->Timestamp_sql = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
            //$value->inserted_time = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
            $value->updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
            //$value->sold_updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
        }
        $data = array(
            "listData" => $sold,
            'query' => DB::getQueryLog()
        );
        return response($data, 200);
    }
}
