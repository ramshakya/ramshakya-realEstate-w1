<?php

namespace App\Http\Controllers\frontend\propertiesListings;

use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataCommPurged;
use App\Models\RetsPropertyDataCondoPurged;
use App\Models\RetsPropertyDataResiPurged;
use App\Models\SqlModel\FeaturesMaster;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use App\Models\RetsPropertyData;
use App\Constants\PropertyConstants;
use App\Models\PolygonsData;
use App\Models\RetsPropertyDataComm;
use App\Models\SqlModel\SavedSearchFilter;
use App\Models\SqlModel\Websetting;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\SqlModel\Pages;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\RetsPropertyDataPurged;
use App\Models\RetsPropertyDataImagesSold;

use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isNull;

class PropertiesController extends Controller
{
    //

    /**
     *
     */
    public function changeSqft()
    {
        echo "\n started crons";
        $queryData = RetsPropertyData::query();
        $queryData->limit(10);
        $queryData->select('id', 'Sqft', 'SqftMax', 'SqftMin', 'sqftFlag');
        $queryData->where('Sqft', '!=', "");
        $queryData->whereNull('sqftFlag');
        // $queryData->where('sqftFlag',1);
        $queryData->limit(10000);
        $prop = $queryData->get();
        $flag = 0;
        echo "\n count" . count($prop);
        foreach ($prop as $key => $data) {
            $query = RetsPropertyData::query();
            echo "\n  ID =>" . $data['id'];
            $propArr = trim($data['Sqft']);
            $propArr = str_replace(" ", "", $propArr);
            $exp = array();
            $saveData = array();
            $SqftMax = 9999999;
            $SqftMin = 0;
            if (str_contains($propArr, '<')) {
                $exp = explode("<", $propArr);
                $flag = 1;
                echo "<br>";
                echo "<";
                echo "<br>";
            }
            if (str_contains($propArr, '-')) {
                $exp = explode("-", $propArr);
                $flag = 2;
                echo "<br>";
                echo "-";
                echo "<br>";
            }
            if (str_contains($propArr, '+')) {
                $exp = explode("+", $propArr);
                $flag = 3;
                echo "\n";
                echo "\n +";
                echo "\n";
            }
            if (count($exp) > 0) {
                $saveData["sqftFlag"] = 1;
                if ($flag == 1) {
                    if (isset($exp[0]) && $exp[0] !== "") {
                        $SqftMin =  $exp[0];
                    }
                    if (isset($exp[1]) && $exp[1] !== "") {
                        $SqftMax =  $exp[1];
                    }
                    $saveData["SqftMax"] = (int)$SqftMax;
                    $saveData["SqftMin"] = (int)$SqftMin;
                }

                if ($flag == 2) {
                    if (isset($exp[0]) && $exp[0] !== "") {
                        $SqftMin =  $exp[0];
                    }
                    if (isset($exp[1]) && $exp[1] !== "") {
                        $SqftMax =  $exp[1];
                    }
                    $saveData["SqftMax"] = (int)$SqftMax;
                    $saveData["SqftMin"] = (int)$SqftMin;
                }

                if ($flag == 3) {
                    if (isset($exp[0]) && $exp[0] !== "") {
                        $SqftMin =  $exp[0];
                    }
                    if (isset($exp[1]) && $exp[1] !== "") {
                        $SqftMax =  $exp[1];
                    }
                    $saveData["SqftMax"] = (int)$SqftMax;
                    $saveData["SqftMin"] = (int)$SqftMin;
                }
                $query->where('id', $data['id']);
                $res = $query->update($saveData);
                if ($res) {
                    echo "\n Updated Successfully .............";
                } else {
                    echo "\n Updated failed ...... ..";
                }
            }
        }
    }

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
    //advance
    public function propertiesSearchold(Request $request)
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
                default:
                    $searchFilter[$searchKey] = $searchValue;
            }
        }
        unset($params['price_min']);
        unset($params['price_max']);
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

            );
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
                ""
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
                ""
            );
        }
        $total_properties = $filteredData['total'];
        $resutl_properties = $filteredData['result'];
        $final_result = getFormatedData($resutl_properties);
        // $response['search_query'] = $lastsearch_query;
        // $pagination = getPaginationString($curr_page, $total_properties, $limit, 2, "", "");
        $pagination = "";
        $response['pagination'] = $pagination;
        $response['alldata'] = $final_result;
        $response['total'] = $total_properties;
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        //$response['mapdata'] = getFormatedData($mapData['result']);
        $response['mapdata'] = $mapData['result'];
        return response($response, 200);
    }
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

    /**
     *
     * Get Price Histories  for details page
     *
     */
    public function listingHistory_old(Request $request)
    {
        $community = $request->Community;
        $Municipality = $request->Municipality;
        $PropertyStatus = $request->PropertyStatus;
        $PropertyType = $request->PropertyType;
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
            "PropertyStatus"
        )
            ->where('Community', $community)
            ->where('Municipality', $Municipality)
            ->where('PropertyStatus', $PropertyStatus)
            ->where('PropertyType', $PropertyType)
            ->where('Sp_date', '!=', '')
            ->where('Sp_date', '>=', Carbon::now()->subMonths($months))
            ->orderBy('inserted_time', 'desc')
            ->limit(5)
            ->get();
        foreach ($sold as $key => $value) {
            $value->Sp_date = Carbon::parse($value->Sp_date)->format('Y-m-d');
            $value->Timestamp_sql = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
            $value->inserted_time = Carbon::parse($value->Timestamp_sql)->format('Y-m-d');
            $value->updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
            $value->sold_updated_time = Carbon::parse($value->Sp_date)->format('Y-m-d');
        }
        $data = array(
            "listData" => $sold,
            'query' => DB::getQueryLog()
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
        )->distinct()->where("StandardAddress", $standardAddress)->orderBy('ContractDate', 'desc')->get();



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

    public function propertiesDetails_old(Request $request)
    {
        $table = "";
        $metDescString = "";
        $data['similar'] = ["sale" => [], 'rent' => []];
        $roomData = array();
        $roomsData = array();
        // SlugUrl
        $LastStatus = "";
        $rpdQuery = RetsPropertyData::query();
        $rpdQuery->select("ListingId", "LastStatus");
        $rpdQuery->where('SlugUrl', $request->SlugUrl);
        $retsPropData = $rpdQuery->first();
        if ($retsPropData) {
            $LastStatus = $retsPropData->LastStatus;
        }
        $res = array();
        if (RetsPropertyDataResi::where('SlugUrl', $request->SlugUrl)->exists()) {
            DB::enableQueryLog();
            $query = RetsPropertyDataResi::query();
            $table = "Residential";
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            if (isset($retsPropData) && $retsPropData->ListingId) {
                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $request->SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            $res->PropertyType = $table;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $LastStatus = $res->Lsc;
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
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
                $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                $LastStatus = $res->Lsc;
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
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
                $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            }
        }
        // resi purged
        if (empty($res)) {
            if (RetsPropertyDataResiPurged::where('SlugUrl', $request->SlugUrl)->exists()) {
                $table = "Residential Purged";
                $query = RetsPropertyDataResiPurged::query();
                $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
                $query->where('SlugUrl', $request->SlugUrl);
                $res = $query->with('propertiesImages')->first();
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
                $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
                //$res->S_r = "Closed";
                $res->propertyStatus = "Closed";
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                $LastStatus = $res->Lsc;
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
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
                $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
                // $res->S_r = "Closed";

                // $res->Status = "D";

                $res->propertyStatus = "Closed";
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
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
                if ($res->propertiesImages != []) {
                    $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                        return $data->s3_image_url;
                    });
                }
                $res->PropertyType = $table;
                $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
                $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
                // $res->S_r = "Closed";
                $res->propertyStatus = "Closed";
                $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
                $LastStatus = $res->Lsc;
            }
        }
        if (!empty($res)) {
            $res->propertyUpdated = date('M d, Y', strtotime($res->property_last_updated));
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
                $descFull = $dec1 .  $dec2  . $dec3;
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
        if ($LastStatus === "Sld") {
            $res->Status = "U";
        }
        if ($LastStatus === "Ter") {
            $res->Status = "D";
        }
        if ($LastStatus === "New") {
            $res->Status = "A";
        }
        // Status
        // return $res;
        // $res->RoomsDescription = $roomData;
        $res["RoomsDescription"] = $roomData;
        $res["properties_images"] = [];
        $data['details'] = $res;
        $data["metaDesc"] = $metDescString;
        // end
        // $sold = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('City', $city)->where('Sp_Dol', '>', '0')->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->get();
        // $sold = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Sp_Dol', '>', '0')->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->limit(10)->get();
        // return $res;
        $data["table"] = $table;
        return response($data, 200);
    }

    public function propertiesDetails(Request $request)
    {
        $table = "";
        $metDescString = "";
        $data['similar'] = ["sale" => [], 'rent' => []];
        $roomData = array();
        $roomsData = array();
        // SlugUrl
        $LastStatus = "";
        $status = "";
        $classType = "";
        $soldListingId = "";
        $SlugUrl = $request->SlugUrl;
        $SlugUrl = str_replace('https://housen.ca/propertydetails//', '', $SlugUrl);
        $SlugUrl = str_replace('https://housen.ca/propertydetails/', '', $SlugUrl);
        $SlugUrl = str_replace('https://housen.ca//propertydetails/', '', $SlugUrl);
        $SlugUrl = str_replace('https:/housen.ca//', '', $SlugUrl);
        $rpdQuery = RetsPropertyData::query();
        $rpdQuery->select(["ListingId", "Status", "PropertyType", "LastStatus"]);
        $rpdQuery->where('SlugUrl', $SlugUrl);
        $retsPropData = $rpdQuery->first();
        if (collect($retsPropData)->count() > 0) {
            $status = $retsPropData->Status;
            $classType = $retsPropData->PropertyType;
        } else {
            $data = RetsPropertyDataPurged::select(["ListingId", "Status", "PropertyType", "LastStatus"])
                ->where("SlugUrl", $SlugUrl)->first();
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
            return  response($data);
        }
        if ($retsPropData) {
            $LastStatus = $retsPropData->LastStatus;
        }
        $res = array();
        $imgs = [];
        if ($classType == "Residential" && $status == "A") {
            DB::enableQueryLog();
            $query = RetsPropertyDataResi::query();
            $table = "Residential";
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            if (isset($retsPropData) && $retsPropData->ListingId) {
                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $LastStatus = $res->Lsc;
        } elseif ($classType == "Commercial" && $status == "A") {
            $table = "Commercial";
            $query = RetsPropertyDataComm::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
            if ($retsPropData->ListingId) {

                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->with('propertiesImages')->first();
            if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
        } elseif ($classType == "Condos" &&  $status == "A") {
            $table = "Condos";
            $query = RetsPropertyDataCondo::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
            if ($retsPropData->ListingId) {

                $query->where('Ml_num', $retsPropData->ListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->with('propertiesImages')->first();
            if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $LastStatus = $res->Lsc;
        } elseif ($classType == "Residential" && $status == "U") {
            $table = "Residential Purged";
            $query = RetsPropertyDataResiPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->first();
            /*if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }*/

            try {
                $imgs = RetsPropertyDataImagesSold::select('image_urls')->where("listingID", $res->Ml_num)->first();
                if (collect($imgs)->count() > 0) {
                    $image = $imgs->image_urls;
                    $image = json_decode($imgs->image_urls);
                    $imgs = collect($image)->map(function ($item) {
                        return ["s3_image_url" => $item];
                    })->all();
                }
            } catch (\Throwable $th) {
            }
            if ($imgs != null) {
                $res->propertiesImages = $imgs;
                $res->properties_images = $imgs;
            } else {
                $res->propertiesImages = [];
                $res->properties_images = [];
            }

            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            //$res->S_r = "Closed";
            $res->propertyStatus = "Closed";
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $LastStatus = $res->Lsc;
        } elseif ($classType == "Commercial" && $status == "U") {
            $table = "Commercial Purged";
            $query = RetsPropertyDataCommPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->first();
            /*if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }*/
            try {
                $imgs = RetsPropertyDataImagesSold::select('image_urls')->where("listingID", $res->Ml_num)->first();
                if (collect($imgs)->count() > 0) {
                    $image = $imgs->image_urls;
                    $image = json_decode($imgs->image_urls);
                    $imgs = collect($image)->map(function ($item) {
                        return ["s3_image_url" => $item];
                    })->all();
                }
            } catch (\Throwable $th) {
            }
            if ($imgs != null) {
                $res->propertiesImages = $imgs;
                $res->properties_images = $imgs;
            } else {
                $res->propertiesImages = [];
                $res->properties_images = [];
            }
            $res->PropertyType = $table;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            // $res->S_r = "Closed";

            // $res->Status = "D";

            $res->propertyStatus = "Closed";
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
        } elseif ($classType == "Condos" && $status == "U") {
            $table = "Condo Purged";
            $query = RetsPropertyDataCondoPurged::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
            if ($soldListingId != "") {
                $query->where('Ml_num', $soldListingId);
            } else {
                $query->where('SlugUrl', $SlugUrl);
            }
            $res = $query->first();
            /*if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }*/
            try {
                $imgs = RetsPropertyDataImagesSold::select('image_urls')->where("listingID", $res->Ml_num)->first();
                if (collect($imgs)->count() > 0) {
                    $image = $imgs->image_urls;
                    $image = json_decode($imgs->image_urls);
                    $imgs = collect($image)->map(function ($item) {
                        return ["s3_image_url" => $item];
                    })->all();
                }
            } catch (\Throwable $th) {
            }
            if ($imgs != null) {
                $res->propertiesImages = $imgs;
                $res->properties_images = $imgs;
            } else {
                $res->propertiesImages = [];
                $res->properties_images = [];
            }
            $res->PropertyType = $table;
            $res->Addr = $res->StandardAddress;
            $res->property_insert_time = date('Y-m-d', strtotime($res->property_insert_time));
            $res->property_last_updated = date('Y-m-d', strtotime($res->property_last_updated));
            // $res->S_r = "Closed";
            $res->propertyStatus = "Closed";
            $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
            $LastStatus = $res->Lsc;
        }

        if (!empty($res)) {
            $res->propertyUpdated = date('M d, Y', strtotime($res->property_last_updated));
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
                $descFull = $dec1 .  $dec2  . $dec3;
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
        if ($LastStatus === "Sld") {
            $res->Status = "U";
        }
        if ($LastStatus === "Ter") {
            $res->Status = "D";
        }
        if ($LastStatus === "New") {
            $res->Status = "A";
        }
        if ($res != []) {
            $res["PropertyStatus"] = $res["S_r"];
            $res["ContractDate"] = $res["Ld"];
            $res["Sp_date"] = $res["Cd"];
            $res = getDom($res);
            $todayDate = new \DateTime(); // For today/now, don't pass an arg.
            $todayDate->modify("-" . $res->Dom . " day");
            $desiredDate =  $todayDate->format("M d, Y");
            $res->propertyUpdated = $desiredDate;

            /*if ($res["S_r"] == "Sale" || $res["Status"] == "A") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Lease" || $res["Status"] == "A") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Sale" || $res["Status"] == "U") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Cd));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Sale" || $res["Status"] == "U") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Cd));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } else {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Timestamp_sql));
            }*/

            if ($res["S_r"] == "Sale" && $res["Status"] == "A") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Lease" && $res["Status"] == "A") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Sale" && $res["Status"] == "U") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Cd));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } elseif ($res["S_r"] == "Sale" && $res["Status"] == "U") {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Cd));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Cd));
            } else {
                $res->property_insert_time = date('Y-m-d', strtotime($res->Ld));
                $res->property_last_updated = date('Y-m-d', strtotime($res->Timestamp_sql));
                $res->sold_updated_time = date('Y-m-d', strtotime($res->Timestamp_sql));
            }
        }
        // Status
        // return $res;
        // $res->RoomsDescription = $roomData;
        $res["RoomsDescription"] = $roomData;
        //$res["properties_images"] = [];
        $data['details'] = $res;
        $data["metaDesc"] = $metDescString;
        // end
        // $sold = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('City', $city)->where('Sp_Dol', '>', '0')->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->get();
        // $sold = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Sp_Dol', '>', '0')->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->limit(10)->get();
        // return $res;
        $data["table"] = $table;
        return response($data, 200);
    }
    /**
     *
     * Get Sold,Rent,Sale Properties for details page
     *
     */

    public function similarProperty_bcp_23aug(Request $request)
    {
        // page setting data
        // $pageSetting = Pages::select('Setting')->where('PageName', 'property details')
        //     ->first();
        $MinPrice = null;
        $MaxPrice = null;
        $city = null;
        $community = null;
        $community = $request->Community;
        $city = $request->Area;
        $bath = (int)$request->Bath_tot;
        $bed =  (int)$request->Br;
        $garage = (int)$request->Gar_spaces;
        $Ml_num = $request->Ml_num;

        // if ($pageSetting->Setting != null) {
        //     $pageSetting = json_decode($pageSetting->Setting);
        //     if ($pageSetting->priceSection != null) {
        //         $percent = ($pageSetting->priceSection * 10) / 100;
        //         $MinPrice = $pageSetting->priceSection - $percent;
        //         $MaxPrice = $pageSetting->priceSection + $percent;
        //     }
        //     if ($pageSetting->citySection != null && $pageSetting->citySection == 1) {
        //         $city = $request->Area;
        //     }
        //     if ($pageSetting->areaSection != null && $pageSetting->areaSection == 1) {
        //         $community = $request->Community;
        //     }
        // }
        // $sold = RetsPropertyDataPurged::select(PropertyConstants::SELECT_SOLD_DATA)->orderBy('inserted_time', 'desc')->limit(10)->get();
        $sold = RetsPropertyDataPurged::select(PropertyConstants::SELECT_SOLD_DATA)->orderBy('inserted_time', 'desc')->whereNotNull('ImageUrl')->limit(10)->get();
        $propType = $request->Type === "Lease" ? 'rent' : 'sold';
        if ($request->Type === "sale") {
            $propType = "sale";
        }
        $sale = getSimilar($community, $propType, $Ml_num, $MinPrice, $MaxPrice, $city, $bath, $bed, $garage);

        // $rent = getSimilar($community, "rent", $Ml_num, $MinPrice, $MaxPrice, $city, $bath, $bed, $garage);

        $nearestSold = getNearest($Ml_num, "sold");
        $nearestRent = getNearest($Ml_num, "rent");
        $data['similar'] = [
            'sold' => $sold,
            "sale" => $sale['result'],
            "rent" => [],
            "nearest" => [
                "sold" => $nearestSold,
                "rent" => $nearestRent,
            ],
            // 'soldFlag' => $soldFlag,
            // 'query' => $q,
            // 'purgedQry' => $purgedQry,
        ];
        return response($data, 200);
    }
    public function similarProperty(Request $request)
    {
        // page setting data
        // $pageSetting = Pages::select('Setting')->where('PageName', 'property details')
        //     ->first();
        $dom = 120;
        $MinPrice = null;
        $MaxPrice = null;
        $city = null;
        $community = null;
        $garage = null;
        $bath = null;
        $community = $request->Community;
        $bed =  (int)$request->Br;
        $Ml_num = $request->Ml_num;
        $Lp_dol = $request->price;

        // $bath = (int)$request->Bath_tot;
        // $city = $request->Area;
        // $garage = (int)$request->Gar_spaces;

        // if ($pageSetting->Setting != null) {
        //     $pageSetting = json_decode($pageSetting->Setting);
        //     if ($pageSetting->priceSection != null) {
        //         $percent = ($pageSetting->priceSection * 10) / 100;
        //         $MinPrice = $pageSetting->priceSection - $percent;
        //         $MaxPrice = $pageSetting->priceSection + $percent;
        //     }
        //     if ($pageSetting->citySection != null && $pageSetting->citySection == 1) {
        //         $city = $request->Area;
        //     }
        //     if ($pageSetting->areaSection != null && $pageSetting->areaSection == 1) {
        //         $community = $request->Community;
        //     }
        // }
        // $sold = RetsPropertyDataPurged::select(PropertyConstants::SELECT_SOLD_DATA)->orderBy('inserted_time', 'desc')->limit(10)->get();
        /*$sold = RetsPropertyDataPurged::select(PropertyConstants::SELECT_SOLD_DATA)
            ->orderBy('inserted_time', 'desc')
            ->whereNotNull('ImageUrl')
            ->where('Community', $community)
            ->where('BedroomsTotal', $bed)
            ->limit(10)->get();*/
        $propType = $request->Type;
        $Style = $request->Style;
        $Status = $request->Status === "Lease" ? 'rent' : 'sold';
        $property_status = $request->property_status  ? $request->property_status : 'A';
        if ($request->Status === "sale") {
            $Status = "sale";
        }
        $sale = getSimilar($community, $Status, $Ml_num, $MinPrice, $MaxPrice, $city, $bath, $bed, $garage, $Lp_dol, $propType, $Style, $property_status);
        // $rent = getSimilar($community, "rent", $Ml_num, $MinPrice, $MaxPrice, $city, $bath, $bed, $garage);
        DB::enableQueryLog();

        $nearestSold = getNearest($Ml_num, "sold", $community, $bed, $propType, $dom, $Status, $property_status, $Style);
        $nearestRent = getNearest($Ml_num, "rent", $community, $bed, $propType, $dom, $Status, $property_status, $Style);

        $queryLog = DB::getQueryLog();
        $data['similar'] = [
            "sale" => $sale['result'],
            "query" => $sale['query'],
            "queryCompare" => $queryLog,
            "rent" => [],
            "nearest" => [
                "sold" => $nearestSold,
                "rent" => $nearestRent,
            ],
            // 'soldFlag' => $soldFlag,
            // 'query' => $q,
            // 'purgedQry' => $purgedQry,
        ];
        return response($data, 200);
    }


    // TODO DELETE
    public function getAutoSearchResultsbck(Request $request)
    {
        $text =  $request->all();
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
            $cities = get_auto_sugesstion('City', $text['query'], $isSoldSearch);
            $municipality = []; //get_auto_sugesstion('Area', $text['query'], $isSoldSearch);
            $Communities = get_auto_sugesstion('Community', $text['query'], $isSoldSearch);
            $Countries = []; //get_auto_sugesstion('County', $text['query'], $isSoldSearch);
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

                // $res = array('label' => $listedid['ListingId'], 'category' => 'MLS');
                // array_push($suggesstionArr, $res);
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

                // $res = array('label' => $city['City'], 'category' => 'Cities');
                // array_push($suggesstionArr, $res);
            }
            $flag = true;
            foreach ($Communities as $Community) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'Neighbourhood', 'value' => $Community['Community'], 'category' => 'Neighbourhood');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $Community['Community'], 'value' => $Community['Community'], 'category' => 'Neighbourhood');
                array_push($suggesstionArr, $res);

                // $res = array('label' => $Community['Community'], 'category' => 'Community');
                // array_push($suggesstionArr, $res);
            }
            $flag = true;
            /* foreach ($Countries as $Country) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'County', 'value' => $Country['County'], 'category' => 'Countries');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $Country['County'], 'value' => $Country['County'], 'category' => 'Countries');
                array_push($suggesstionArr, $res);

                // $res = array('label' => $Country['County'], 'category' => 'Countries');
                // array_push($suggesstionArr, $res);
            }*/
            // foreach ($Zipes as $Zip) {
            //     $res = array('label' => $Zip['PostalCode'], 'category' => 'Zip Code');
            //     array_push($suggesstionArr, $res);
            // }
            $flag = true;
            /*foreach ($municipality as $data) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'Municipality', 'value' => $data['Municipality'], 'category' => 'Municipality');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $data['Municipality'], 'value' => $data['Municipality'], 'category' => 'Municipality');
                array_push($suggesstionArr, $res);

                // $res = array('label' => $data['Municipality'], 'category' => 'Municipality');
                // array_push($suggesstionArr, $res);
            }*/
            $flag = true;
            foreach ($addressSearch as $addr) {
                if ($flag) {
                    $res = array('isHeading' => true, 'text' => 'StandardAddress', 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress');
                    array_push($suggesstionArr, $res);
                    $flag = false;
                }
                $res = array("text" => $addr['StandardAddress'], 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress');
                array_push($suggesstionArr, $res);

                // $res = array('label' => $addr['StandardAddress'], 'category' => 'Addrress');
                // array_push($suggesstionArr, $res);
            }
        }

        return response($suggesstionArr, 200);
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
                }
            }
            else {
                if ($text["type"] == "address") {
                    DB::enableQueryLog();
                    $addressSearch = get_auto_sugesstion('StandardAddress', $text['query'], $isSoldSearch);
                    $q = array("Query" => DB::getQueryLog());
                    array_push($suggesstionArr, $q);
                    $flag = true;
                    foreach ($addressSearch as $addr) {
                        if ($flag) {
                            $res = array('isHeading' => true, 'text' => 'StandardAddress', 'value' => $addr['StandardAddress'], 'category' => 'StandardAddress', 'group' => 'StandardAddress');
                            array_push($suggesstionArr, $res);
                            $flag = false;
                        }
                        $res = array("text" => $addr['StandardAddress'], 'value' => $addr['StandardAddress'], 'ListingId' => $addr['ListingId'], 'category' => 'StandardAddress', 'group' => 'StandardAddress');
                        array_push($suggesstionArr, $res);
                    }
                }
                if ($text["type"] == "listingId") {
                    $isSoldSearch = true;
                    DB::enableQueryLog();
                    $listingId = get_auto_sugesstion('ListingId', $text['query'], $isSoldSearch);
                    $q = array("Query" => DB::getQueryLog());
                    array_push($suggesstionArr, $q);
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

    public function filterData(Request $request)
    {
        $queryData = RetsPropertyData::query();
        $type = $queryData->where("PropertyType", "!=", "")->distinct('PropertyType')->get('PropertyType');
        $subtype = $queryData->where("PropertySubType", "!=", "")->distinct('PropertySubType')->get('PropertySubType');
        $basement = $queryData->where("Bsmt1_out", "!=", "")->distinct('Bsmt1_out')->get('Bsmt1_out');
        $featuresData = FeaturesMaster::limit(20)->get();

        $price = getPriceList();
        // $Sqft = $queryData->distinct('Sqft')->where('Sqft','!=','')->orderBy('Sqft', 'asc')->pluck('Sqft');
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
            && $payload["sort_by"] == ""  &&  !count($payload["basement"]) && !count($payload["features"]) &&
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
        // return $searchFilter;
        unset($params['price_min']);
        unset($params['price_max']);
        unset($params['status']);
        unset($params['soldStatus']);
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
        $cityData = [];
        $areaData = [];
        if (isset($filteredData['total_temp']) && $filteredData['total_temp'] !== 0) {
            $total_properties = $filteredData['total_temp'];
        }
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
    public function getPolygonsData()
    {
        $this->renameAreas();
        return;
        $this->areasPolygons();
        $cities = [];
        $communities = [];
        //Dont Update city from this
        // $cities = RetsPropertyData::groupBy("City")->select('City')->get()->toArray();
        $communities = RetsPropertyData::select("City", "Community")->groupBy("Community")->get()->toArray();
        echo "Storing polygons........\n";
        foreach ($cities as $key => $city) {
            $query = PolygonsData::query();
            if (!$city['City']) {
                continue;
            }
            $searchtext = $city['City'];
            $cityData = $query->where("cityName", $searchtext)->first();

            $searchtext =  $city['City'];
            // $searchtext =  "Ajax";
            $response = Http::get("https://nominatim.openstreetmap.org/search.php?q=" . $searchtext . "&polygon_geojson=1&format=json");
            if ($response->ok()) {
                $data = json_decode($response->body(), true);
                $flag = false;
                foreach ($data as $k => $value) {
                    if ($flag) {
                        continue;
                    }
                    echo "\n$searchtext\n";
                    if (isset($value['geojson'])) {
                        $json = $value['geojson'];
                        if ($json['type'] === "Polygon") {
                            $dataInsert = array(
                                'cityName' => $searchtext,
                                'cityPolygons' => $json['coordinates'][0]
                            );
                            $dataUpdate = array(
                                // 'cityName' => $searchtext,
                                'cityPolygons' => $json['coordinates'][0]
                            );
                            if ($cityData) {
                                $res = $query->where("cityName", $searchtext)->update($dataUpdate);
                                echo "\nupdated :::", $key;
                            } else {
                                $res = $query->create($dataInsert);
                                echo "\ncreated :::", $key;
                            }
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                    }
                }
            } else {
                echo "\nAPI StATUS" . $response->status();
            }
        }
        $i = 1;
        foreach ($communities as $key => $community) {
            if (!$community['Community']) {
                continue;
            }
            if ($i == 5) {
            }
            $i++;
            $searchtext = $community['Community'] . " " . $community['City'];
            $response = Http::get("https://nominatim.openstreetmap.org/search.php?q=" . $searchtext . "&polygon_geojson=1&format=json");
            if ($response->ok()) {
                $data = json_decode($response->body(), true);
                $flag = false;
                foreach ($data as $k => $value) {
                    if ($flag) {
                        continue;
                    }

                    if (isset($value['geojson'])) {
                        $json = $value['geojson'];
                        if ($json['type'] === "Polygon") {
                            $dataInsert = array(
                                'cityName' => $community['City'],
                                'areasName' => $community['Community'],
                                'areasPolygons' => ($json['coordinates'][0]),
                            );
                            $res = PolygonsData::updateOrCreate(['areasName' => $community['Community']], $dataInsert);
                            echo "\ncreated :::", $key;
                            echo "\n";
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                    }
                }
            } else {
                echo "\nAPI StATUS" . $response->status();
            }
            // $cityData = CityPolygons::select("city_name", "city_polygons", "outer_polygon")->where("city_name", 'like', '%' . $cityText . '%')->first();

        }
    }

    public function areasPolygons()
    {
        // "https://nominatim.openstreetmap.org/search.php?q=" . $searchtext . "&polygon_geojson=1&format=json"
        $communities = RetsPropertyData::select("City", "Community")->groupBy("Community")->get()->toArray();
        $i = 0;
        $last = PolygonsCoordinates::orderBy('id', 'desc')->first();
        if ($last) {
            $i = $last->enterId;
        }
        for ($index = 0; $index <= 100; $index++) {
            // foreach ($communities as $key => $communty) {
            // https://xxxxxxxxxxx/v1/mappings/areas?area_type=Locality&area_id=1&target_area_type=Country&target_area_id=1
            $i++;
            echo "\nId for data" . $i;
            $url = "https://xxxxxxxx/v1/mappings/areas?area_type=Locality&area_id=" . $i;
            $response = Http::get($url);
            if ($response->ok()) {
                echo "\n";
                echo "\n";
                echo "\nAPI StATUS" . $response->status();
                echo "\n";
                $data = json_decode($response->body(), true);
                $data = $data['data'];
                $json = json_encode($data);
                if ($data['Areas']) {
                    $areas = $data['Areas'];
                    foreach ($areas as $key => $area) {
                        $label = $area["label"];
                        echo "\nName :::", $label;
                        $polygons = [];
                        $polygon_json = json_decode($area['polygon_json'], true);
                        foreach ($polygon_json as $key => $value) {
                            $latlng = [];
                            $latlng[] = $value['lng'];
                            $latlng[] = $value['lat'];
                            $polygons[] = $latlng;
                        }
                        echo "\nEntry id :::", $i;
                        $dataInsert = array(
                            'enterId' => $i,
                            'areasName' => $label,
                            'areasPolygons' => json_encode($polygons),
                            'fullJson' => $json,
                        );
                        $res = PolygonsCoordinates::updateOrCreate(['areasName' => $label, 'enterId' => $i], $dataInsert);
                    }
                }
            }
            sleep(10);
        }
    }
    public function updateAreasGeoData()
    {
        // $coordinates=PolygonsCoordinates::groupBy('areasName')->pluck('areasName')->toArray();
        $coordinates = PolygonsCoordinates::select("id", "areasName", "areasPolygons")->groupBy('areasName')->get();
        foreach ($coordinates as $key => $coordinat) {
            echo "\n\n id from PolygonsCoordinates table =>::", $coordinat->id;
            echo "\n Name =>::", $coordinat->areasName;
            $dataInsert = array(
                'areasName' => $coordinat->areasName,
                'areasPolygons' => $coordinat->areasPolygons,
            );
            $res = PolygonsData::updateOrCreate(['areasName' => $coordinat->areasName], $dataInsert);
            echo "\n updated and created  => id " . $res->id;
        }
    }

    public function renameAreas()
    {
        $areas = PolygonsData::select("areasName", "id")->where("areasName", "LIKE", "%|%")->get();
        echo "\n \n Total Data ::" . count($areas);
        foreach ($areas as $key => $area) {
            echo "\n \n Rec.Id ::::" . $area->id;
            $dataInsert = array(
                'areasName' => str_replace(' | ', "-", $area->areasName),
            );
            $res = PolygonsData::updateOrCreate(['areasName' => $area->areasName, 'id' => $area->id], $dataInsert);
            echo "\n \n updated id ::::" . $res->id;
        }
    }
    public function updateCityGeoData()
    {
        // $cities = PolygonsData::groupBy('cityName')->pluck("cityName")->toArray();
        $query = PolygonsData::query();
        $cities = CityPolygons::groupBy('city_name')->get();
        foreach ($cities as $key => $city) {
            if (!$city->city_name) {
                continue;
            }
            $json = [];
            $searchtext = $city->city_name;
            $cityData = $data = $query->where("cityName", $searchtext)->first();
            if (!$city->outer_polygon) {
                echo "continue";
                continue;
            }
            $dataInsert = array(
                'cityName' => $searchtext,
                'cityPolygons' => json_encode($city->outer_polygon)
            );

            $dataUpdate = array(
                // 'cityName' => $searchtext,
                'cityPolygons' => json_encode($city->outer_polygon)
            );
            if ($cityData) {
                $res = $query->where("cityName", $searchtext)->update($dataUpdate);
                echo "<br>";
                echo "\nupdated :::", $key;
            } else {
                $res = $query->create($dataInsert);
                echo "<br>";
                echo "\ncreated :::", $key;
            }
        }
        // $res = PolygonsData::updateOrCreate(['areasName' => $community['Community']], $dataInsert);

        // $cityData = CityPolygons::select("city_name", "city_polygons", "outer_polygon")->where("city_name", 'like', '%' . $cityText . '%')->first();
    }
    public function getDataFromYelp(Request $request)
    {
        $postData = $request->all();
        // $postData["latitude"]="43.12855";
        // $postData["longitude"]="-79.20761";
        $websetting = Websetting::select('WebsiteName', 'YelpKey', 'YelpClientId', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag')
            ->where("AdminId", $request->agentId)
            ->first();
        // $key=$request->yelpKey;
        $key = $websetting->YelpKey;
        $data = yelp_data($key, $postData["latitude"], $postData["longitude"], $postData["type"]);
        return response($data, 200);
    }

    public function shareEmail(Request $request)
    {
        $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor',  'FavIconAltTag')
            ->where("AdminId", $request->agentId)
            ->first();
        $retsPropData = getProperties("", $request->property_mls_no);
        $propData = array(
            "name" => $request->name,
            "property_url" => $request->property_url,
            "propertyDetails" => $retsPropData,
            "websetting" => $websetting,
        );
        $subject = $request->name . " has shared a property  #MLS - " . $request->property_mls_no . "  " . $retsPropData->Addr;
        // $emails = explode(',', $request->emails,true);
        $emails =  $request->emails;
        $template = getTemplate($propData);
        //
        array_push($emails, "ram@peregrine-it.com");
        //git check
        sendEmail("SMTP", $request->email, $emails, "mukesh@peregrine-it.com", "sagar@peregrine-it.com", $subject, $template,  "", null);
        $data = array(
            "status" => 200,
            "message" => "Successful",
        );
        return response($data, 200);
    }
    public function saveSearch_24aug_bcp(Request $request)
    {
        $query = SavedSearchFilter::query();
        $filterData = $request->filtersData;
        $filterData = json_decode($filterData, true);
        $data = array(
            "textSearch" => $filterData['text_search'],
            "className" => $filterData['propertyType'],
            "priceMin" => $filterData['price_min'],
            "priceMax" => $filterData['price_max'],
            "bedsTotal" => $filterData['beds'],
            "bathsFull" => $filterData['baths'],
            "status" => $filterData['status'],
            "features" => json_encode($filterData['features']),
            "propertySubType" => json_encode($filterData['propertySubType']),
            "Bsmt1Out" => json_encode($filterData['basement']),
            "openHouse" => $filterData['openhouse'],
            "dom" => $filterData['Dom'],
            "Sqft" => $filterData['Sqft'],
            "shape" => $filterData['shape'],
            "currPathQuery" => $filterData['curr_path_query'],
            "city" => $filterData['City'],
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
    public function saveSearch(Request $request)
    {
        $query = SavedSearchFilter::query();
        if ($request->isWatchListings) {
            $data = array(
                "userId" => $request->userId,
                "agentId" => $request->agentId,
                "ListingId" => $request->watchListings['ListingId'],
                "watchListings" => json_encode($request->watchListings),
            );

            if (isset($request->watchListings['AlertsOn']) && !$request->watchListings['isSold']) {
                $prop = SavedSearchFilter::where('ListingId', $request->watchListings['ListingId'])->where("watchFlag",1)->first();
                $data['watchFlag']=1;
                if ($prop) {
                    $prop->ListingId = $request->watchListings['ListingId'];
                    $prop->watchListings = json_encode($request->watchListings);
                    $res = $prop->save();
                    $data = array(
                        "status" => 200,
                        "message" => "Updated Successful com ",
                    );
                    return response($data, 200);
                } else {
                    $res = $query->insert($data);
                    $data = array(
                        "status" => 200,
                        "message" => "Created Successful com",
                    );
                    return response($data, 200);
                }
            } else {
                $data['watchFlag']=0;
                $prop = SavedSearchFilter::where('ListingId', $request->watchListings['ListingId'])->where("watchFlag",0)->first();
                if ($prop) {
                    $prop->ListingId = $request->watchListings['ListingId'];
                    $prop->watchListings = json_encode($request->watchListings);
                    $res = $prop->save();
                    $data = array(
                        "status" => 200,
                        "message" => "Updated Successful ",
                    );
                    return response($data, 200);
                } else {
                    $res = $query->insert($data);
                    $data = array(
                        "status" => 200,
                        "message" => "Created Successful",
                    );
                    return response($data, 200);
                }
            }
        } else {
            $filterData = $request->filtersData;
            $filterData = json_decode($filterData, true);
            $data = array(
                "textSearch" => isset($filterData['text_search']) ? $filterData['text_search'] : "",
                "className" =>  "",
                "priceMin" => isset($filterData['price_min']) ? $filterData['price_min'] : "",
                "priceMax" => isset($filterData['price_max']) ? $filterData['price_max'] : "",
                "bedsTotal" => isset($filterData['beds']) ? $filterData['beds'] : "",
                "bathsFull" => isset($filterData['baths']) ? $filterData['baths'] : "",
                "status" => isset($filterData['status']) ? $filterData['status'] : "",
                "features" => json_encode(isset($filterData['features']) ? $filterData['features'] : ""),
                "propertySubType" => json_encode(isset($filterData['propertySubType']) ? $filterData['propertySubType'] : ""),
                "Bsmt1Out" => json_encode(isset($filterData['basement']) ? $filterData['basement'] : ""),
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
        }
        $res = $query->insert($data);
        $data = array(
            "status" => 200,
            "message" => "Successful",
        );
        return response($data, 200);
    }
    /*public function markerInfo(Request $request)
    {
        DB::enableQueryLog();
        $res = false;
        if ($request->soldStatus === "U" || $request->soldStatus === "D") {
            $query = RetsPropertyDataPurged::query();
            $query->select(PropertyConstants::SELECT_DATA)->where("ListingId", $request->id)->where("PropertyStatus", $request->status);
            $res = $query->first();
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
            if ($res != []) {
                $res["Dom"] = getActualDom($res["Timestamp_sql"]);
            }
            if ($res) {
                $data = array(
                    "status" => 200,
                    "message" => "Successful",
                    "data" => $res,
                    "queryLog" => DB::getQueryLog()
                );
            }
            return response($data, 200);
        }
        if ($request->soldStatus === "A") {
            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA)
                ->where("ListingId", $request->id)
                ->where("PropertyStatus", $request->status)
                ->where("Status", $request->soldStatus);
            $res = $query->first();
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
            if ($res != []) {
                $res["Dom"] = getActualDom($res["Timestamp_sql"]);
            }
            $data = array(
                "status" => 200,
                "message" => "Successful",
                "data" => $res,
                "queryLog" => DB::getQueryLog()
            );
            return response($data, 200);
        }
        if (!$res) {
            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA)
            ->where("ListingId", $request->id);
            if ($request->status) {
                $query->where("PropertyStatus", $request->status);
            }
            $res = $query->first();

            if ($res != []) {
                $res["Dom"] = getActualDom($res["Timestamp_sql"]);
            }
        }
        if (!$res) {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            )->where("ListingId", $request->id);
            if ($request->status) {
                $query->where("PropertyStatus", $request->status);
            }
            $res = $query->first();

            if ($res != []) {
                $res["Dom"] = getActualDom($res["Timestamp_sql"]);
            }
        }
        if ($res) {
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
        }
        $data = array(
            "status" => 200,
            "message" => "Successful",
            "data" => $res,
            "queryLog" => DB::getQueryLog()
        );
        return response($data, 200);
    }*/
    public function markerInfo_ld(Request $request)
    {
        //DB::enableQueryLog();
        $res = false;
        if ($request->soldStatus === "U" || $request->soldStatus === "D") {
            $query = RetsPropertyDataPurged::query();
            //$query->select(PropertyConstants::SELECT_DATA)->where("ListingId", $request->id)->where("PropertyStatus", $request->status);
            $query->select(PropertyConstants::SELECT_DATA)->where("ListingId", $request->id);
            $res = $query->first();
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
            if ($res != []) {
                $res = collect($res)->all();
                $res = getDom($res);
            }
            if ($res) {
                $data = array(
                    "status" => 200,
                    "message" => "Successful",
                    "data" => $res,
                    //"queryLog" => DB::getQueryLog()
                );
            }
            return response($data, 200);
        }
        if ($request->soldStatus === "A") {
            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA)
                ->where("ListingId", $request->id);



            /*$query->select(PropertyConstants::SELECT_DATA)
                ->where("ListingId", $request->id)
                ->where("PropertyStatus", $request->status)
                ->where("Status", $request->soldStatus);*/
            $res = $query->first();
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
            if ($res != []) {
                $res = collect($res)->all();
                $res = getDom($res);
            }
            $data = array(
                "status" => 200,
                "message" => "Successful",
                "data" => $res,
                //"queryLog" => DB::getQueryLog()
            );
            return response($data, 200);
        }
        /*if (!$res) {
            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA)
            ->where("ListingId", $request->id);
            if ($request->status) {
                $query->where("PropertyStatus", $request->status);
            }
            $res = $query->first();

            if ($res != []) {
                $res = collect($res)->all();
                $res = getDom($res);
            }
        }*/
        /*if (!$res) {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            )->where("ListingId", $request->id);
            if ($request->status) {
                $query->where("PropertyStatus", $request->status);
            }
            $res = $query->first();

            if ($res != []) {
                $res = collect($res)->all();
                $res = getDom($res);
            }
        }*/
        if ($res) {
            if ($res->LastStatus === "Sld") {
                $res->Status = "U";
            }
            if ($res->LastStatus === "Ter") {
                $res->Status = "D";
            }
        }

        $data = array(
            "status" => 200,
            "message" => "Successful",
            "data" => $res,
            //"queryLog" => DB::getQueryLog()
        );
        return response($data, 200);
    }

    public function markerInfo(Request $request)
    {
        /*$sql =  "select `id`, `Orig_dol`, `BedroomsTotal`, `BathroomsFull`, `Sqft`, `ListPrice`, `StandardAddress`, `City`, `ListingId`, `Gar`, `Dom`, `ImageUrl`, `PropertyStatus`, `Status`, `Dom`, `PropertySubType`, `County`, `PropertyType`, `SlugUrl`, `Park_spcs`, `Community`, `Extras`, `Latitude`, `Longitude`, `Sp_dol`, `Vow_exclusive`, `Ad_text`, `Timestamp_sql`, `LastStatus`, `ContractDate`, `Sp_date` from `RetsPropertyData` where `ListingId` = 'X5627657' ";
        $sql_data = DB::select($sql);
        $data = array(
            "status" => 200,
            "message" => "Successful",
            "data" => $sql_data,
            //"queryLog" => DB::getQueryLog()
        );
        return $data;*/
        $ReqFilters = [];
        $status = $request->status;
        $listingId  = $request->id;
        /* if ($request->status) {
            if ($origStatus == "Sold") {
                $status = "U";
            } elseif ($origStatus == "Rented") {
                $status = "U";

            } elseif ($origStatus == "Sale") {
                $status = "A";
            } elseif ($origStatus == "Lease") {
                $status = "A";
            }
        }*/
        if ($status == "U") {
            $query = RetsPropertyDataPurged::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            $query->where("ListingId", $listingId);
            $res = $query->first();
        } else {
            $query = RetsPropertyData::query();
            $query->select(
                PropertyConstants::SELECT_DATA
            );
            $query->where("ListingId", $listingId);
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
}
