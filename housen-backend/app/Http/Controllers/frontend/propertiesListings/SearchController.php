<?php

namespace App\Http\Controllers\frontend\propertiesListings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\PropertyConstants;
use App\Models\PolygonsData;
use App\Models\SqlModel\CityData;
use App\Models\SqlModel\CityNeighbours;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    //
    public function mapSearchList111(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        unset($payload['isMapV2']);
        unset($params['isMapV2']);
        $isDefault = false;
        $isTotal = true;
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
                        // $isDefault = true;
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
        // $isDefault = false;
        if (empty($textSearchFilter)) {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal,
            );
        } else {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal
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

        $response['total'] = $total_properties;
        $response['countInWords'] = $countInWords;
        $response["textShow"] = $txt;
        $response['alldata'] = $filteredData['result'];
        $response['query'] = $filteredData['query'];
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
        // getting city content

        $response['city_content'] = "";
        if (isset($params['text_search'])) {
            $searchQuery = $params['text_search'];
            $citydata = CityData::select('Content')->where('CityName', $searchQuery)->first();
            if ($citydata) {
                $response['city_content'] = $citydata->Content;
            } else {
                $area = CityNeighbours::select('Content')->where('AreaName', $searchQuery)->first();
                if ($area) {
                    $response['city_content'] = $area->Content;
                }
            }
        }

        return response($response, 200);
    }
    public function mapSearchList(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        unset($payload['isMapV2']);
        unset($params['isMapV2']);
        $isDefault = false;
        $isTotal = true;
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
        /*if (isset($params['sort_by']) && !empty($params['sort_by'])) {
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
                $field = 'Timestamp_sql';
                $orderBy = 'Desc';
            } else if ($sortData === 'dom_low') {
                $field = 'Timestamp_sql';
                $orderBy = 'Asc';
            }
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'Timestamp_sql';
            $orderBy = 'DESC';
        }*/
        if (isset($params['sort_by']) && !empty($params['sort_by'])) {
            $field = 'inserted_time';
            $orderBy = 'Desc';
            $sortData = $params['sort_by'];
            if ($sortData === 'price_high') {
                $field = 'Price';
                $orderBy = 'Desc';
            } else if ($sortData === 'price_low') {
                $field = "Price";
                $orderBy = 'Asc';
            } else if ($sortData === 'dom_high') {
                $field = 'Timestamp_sql';
                if ($payload["soldStatus"] == "A" && $payload["status"] == "Sale") {
                    $field = 'ContractDate';
                } elseif ($payload["soldStatus"] == "A" && $payload["status"] == "Lease") {
                    $field = 'ContractDate';
                } elseif ($payload["soldStatus"] == "U" && $payload["status"] == "Lease") {
                    $field = 'Sp_date';
                } elseif ($payload["soldStatus"] == "U" && $payload["status"] == "Sale") {
                    $field = 'Sp_date';
                } elseif ($payload["soldStatus"] == "D") {
                    $field = 'Timestamp_sql';
                } else {
                    $field = 'Timestamp_sql';
                }
                $orderBy = 'Desc';
            } else if ($sortData === 'dom_low') {
                $field = 'Timestamp_sql';
                if ($payload["soldStatus"] == "A" && $payload["status"] == "Sale") {
                    $field = 'ContractDate';
                } elseif ($payload["soldStatus"] == "A" && $payload["status"] == "Lease") {
                    $field = 'ContractDate';
                } elseif ($payload["soldStatus"] == "U" && $payload["status"] == "Lease") {
                    $field = 'Sp_date';
                } elseif ($payload["soldStatus"] == "U" && $payload["status"] == "Sale") {
                    $field = 'Sp_date';
                } elseif ($payload["soldStatus"] == "D") {
                    $field = 'Timestamp_sql';
                } else {
                    $field = 'Timestamp_sql';
                }
                $orderBy = 'Asc';
            }
            unset($params['sort_by']);
        } else {
            $sortData = 'dom_high';
            $field = 'Timestamp_sql';
            if (isset($payload["soldStatus"]) && $payload["soldStatus"] == "A" && $payload["status"] == "Sale") {
                $field = 'ContractDate';
            } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "A" && $payload["status"] == "Lease") {
                $field = 'ContractDate';
            } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "U" && $payload["status"] == "Lease") {
                $field = 'Sp_date';
            } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "U" && $payload["status"] == "Sale") {
                $field = 'Sp_date';
            } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "D") {
                $field = 'Timestamp_sql';
            } else {
                $field = 'Timestamp_sql';
            }
            $orderBy = 'DESC';
        }
        $response['allparams'] = $params;
        unset($params['sort_by']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['shape']) && $payload['shape'] != '' && isset($payload['text_search']) && !empty($payload['text_search'])) {
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
                        $isDefault = false;
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
        } else
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
                        // $isDefault = true;
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
        // $isDefault = false;
    //     if ($searchFilter["group"] != ""  && $searchFilter["group"] == "ListingId"){
    //         //$searchFilter = [];
	   // //unset($searchFilter["PropertyType"]);
    //         //unset($searchFilter["status"]);
	   // $dk = 0;
    //         if (isset($searchFilter["StandardAddress"])){
    //             $add = $searchFilter["StandardAddress"];
    //             $dk = 1;
    //         }
    //         $searchFilter = [];
    //         $searchFilter["group"] = "ListingId";
    //         if ($dk) {
    //             $searchFilter["StandardAddress"] = $add;
    //         }
    //     }
        if (empty($textSearchFilter)) {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal,
            );
        } else {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal
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
        if ($filteredData['result'] != []) {
            $filteredData['result'] = collect($filteredData['result'])->map(function ($item) {
                /*if ($item["LastStatus"] == "Ter") {
                    if ($item["Sp_date"] == ""){
                        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                    } else {
                        $item["Dom"] = getActualDom($item["Sp_date"]);
                    }
                } elseif ($item["LastStatus"] == "New") {
                    $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                } elseif ($item["LastStatus"] == "Lsc") {
                    if ($item["Sp_date"] == ""){
                        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                    } else {
                        $item["Dom"] = getActualDom($item["Sp_date"]);
                    }
                } else {
                    $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                }*/
                $item = getDom($item);
                /*if ($item["PropertyStatus"] == "Sale" && $item["Status"] == "A") {
                    if ($item["ContractDate"] != "") {
                        $item["Dom"] = getActualDom($item["ContractDate"]);
                    } else {
                        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                    }
                } elseif ($item["Status"] == "U") {
                    if ($item["Sp_date"] != "") {
                        $item["Dom"] = getActualDom($item["Sp_date"]);
                    } else {
                        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                    }
                } elseif ($item["PropertyStatus"] == "Lease" && $item["Status"] == "A") {
                    if ($item["ContractDate"] != "") {
                        $item["Dom"] = getActualDom($item["ContractDate"]);
                    } else {
                        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                    }
                } else {
                    $item["Dom"] = getActualDom($item["Timestamp_sql"]);
                }*/
                $item["ImageUrl"] = str_replace(env('KEYFILEIMAGE'),"",$item["ImageUrl"]);
                $item["LastStatusKey"] = "";
                $item["LastStatusColor"] = "";
                if ($item["Status"] == "D") {
                    $item["LastStatusKey"] = "De-listed";
                    $item["LastStatusColor"] = "Yellow";
                } elseif ($item["LastStatus"] == "Sld" && $item["Status"] == "U") {
                    $item["LastStatusKey"] = "Sold";
                    $item["LastStatusColor"] = "Red";
                } elseif ($item["Status"] == "A" && $item["PropertyStatus"] == "Sale") {
                    $item["LastStatusKey"] = "For Sale";
                    $item["LastStatusColor"] = "Green";
                } elseif ($item["Status"] == "U" && $item["PropertyStatus"] == "Lease" && $item["LastStatus"] == "Lsd") {
                    $item["LastStatusKey"] = "For Leased";
                    $item["LastStatusColor"] = "Brown";
                } elseif ($item["Status"] == "A" && $item["PropertyStatus"] == "Lease" ) {
                    $item["LastStatusKey"] = "For Lease";
                    $item["LastStatusColor"] = "Brown";
                } elseif ($item["Status"] == "U"  && $item["LastStatus"] == "Ter") {
                    $item["LastStatusKey"] = "Terminated";
                    $item["LastStatusColor"] = "Yellow";
                } elseif ($item["Status"] == "D"  && $item["LastStatus"] == "Ter") {
                    $item["LastStatusKey"] = "De-Listed";
                    $item["LastStatusColor"] = "Yellow";
                } elseif ($item["Status"] == "U"  && $item["LastStatus"] == "Exp" ) {
                    $item["LastStatusKey"] = "Expired";
                    $item["LastStatusColor"] = "Pink";
                } elseif ($item["Status"] == "U"  && $item["LastStatus"] == "Dft" ) {
                    $item["LastStatusKey"] = "Draft";
                    $item["LastStatusColor"] = "Pink";
                } elseif ($item["Status"] == "U"  && $item["LastStatus"] == "Sus" ) {
                    $item["LastStatusKey"] = "Suspended";
                    $item["LastStatusColor"] = "Pink";
                } elseif ($item["Status"] == "U"  && $item["LastStatus"] == "Pc" ) {
                    $item["LastStatusKey"] = "Terminated";
                    $item["LastStatusColor"] = "Yellow";
                } else {
                    if ($item["LastStatusKey"] == "" && $item["LastStatusColor"] == "") {
                        if ($item["Status"] == "U") {
                            $item["LastStatusKey"] = "Sold";
                            $item["LastStatusColor"] = "Red";
                        } else {
                            $item["LastStatusKey"] = "For Sale";
                            $item["LastStatusColor"] = "Green";
                        }
                    }
                }

                if($item["LastStatus"]==="Sld"){
                    $item["Status"]="U";
                }
                if($item["LastStatus"]==="Ter"){
                    $item["Status"]="D";
                }
                return $item;
            });
        }

        $response['total'] = $total_properties;
        $response['countInWords'] = $countInWords;
        $response["textShow"] = $txt;
        $response['alldata'] = $filteredData['result'];
        $response['query'] = $filteredData['query'];
        $response['offset'] = $offset;
        $response['limit'] = $limit;
        $response['extra'] = $curr_page;
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
        // getting city content

        $response['city_content'] = "";
        if (isset($params['text_search'])) {
            $searchQuery = $params['text_search'];
            $citydata = CityData::select('Content')->where('CityName', $searchQuery)->first();
            if ($citydata) {
                $response['city_content'] = $citydata->Content;
            } else {
                $area = CityNeighbours::select('Content')->where('AreaName', $searchQuery)->first();
                if ($area) {
                    $response['city_content'] = $area->Content;
                }
            }
        }

        return response($response, 200);
    }
    public function mapSearchTotal(Request $request)
    {
        $propType = $request->propType;
        $payload = $params = $request->all();
        unset($payload['isMapV2']);
        unset($params['isMapV2']);
        $isDefault = false;
        $isTotal = true;
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
        $field = "";
        $orderBy = "";
        $offset = 0;
        if (empty($textSearchFilter)) {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal,
            );
        } else {
            $filteredData = housenMapSearch(
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
                $isDefault,
                $isTotal,
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
        $response["textShow"] = $txt;
        $response['total'] = $total_properties;
        $response['countInWords'] = $countInWords;
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
        return response($response, 200);
    }
    public function mapSearchMarkers(Request $request)
    {
        $isMapV2 = $request->isMapV2 ? $request->isMapV2 : false;
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
        if ($payload['City'] == "") {
            unset($payload['City']);
            unset($params['City']);
        } else {
        }
        $searchFilter = array();
        $filteredData = array();
        $orFilter = array();
        $array_data = array();
        $textSearchFilter = '';
        $offset = 0;
        $field = 'Dom';
        if (isset($payload["soldStatus"]) && $payload["soldStatus"] == "A" && $payload["status"] == "Sale") {
            $field = 'ContractDate';
        } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "A" && $payload["status"] == "Lease") {
            $field = 'ContractDate';
        } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "U" && $payload["status"] == "Lease") {
            $field = 'Sp_date';
        } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "U" && $payload["status"] == "Sale") {
            $field = 'Sp_date';
        } elseif (isset($payload["soldStatus"]) && $payload["soldStatus"] == "D") {
            $field = 'Timestamp_sql';
        } else {
            $field = 'Timestamp_sql';
        }
        $orderBy = 'Asc';
        $response['allparams'] = $params;
        unset($params['sort_by']);
        unset($payload['isMapV2']);
        unset($params['isMapV2']);
        $dis_sel = "";
        $dis_cond = "";
        $shape = '';
        if (isset($payload['shape']) && $payload['shape'] != '' && isset($payload['text_search']) && !empty($payload['text_search'])) {
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
                        $isDefault = false;
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
        } else
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
        $startTiming = strtotime(date("H:i:s"));
    //     if ($searchFilter["group"] != ""  && $searchFilter["group"] == "ListingId"){
    //         //$searchFilter = [];
	   //     //unset($searchFilter["PropertyType"]);
    //         //unset($searchFilter["status"]);
	   // $dk = 0;
    //         if (isset($searchFilter["StandardAddress"])){
    //             $add = $searchFilter["StandardAddress"];
    //             $dk = 1;
    //         }
    //         $searchFilter = [];
    //         $searchFilter["group"] = "ListingId";
    //         if ($dk) {
    //             $searchFilter["StandardAddress"] = $add;
    //         }
    //     }


        if (empty($textSearchFilter)) {
            $mapData = housenMapSearch(
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
        } else {
            $mapData = housenMapSearch(
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
        $geojson = "";
        $groups = array();
        if ($isMapV2) {
            $centerLat = $mapData['result'] ? $mapData['result'][count($mapData['result']) - 1]->Latitude : 565;
            $centeralLong = $mapData['result'] ? $mapData['result'][count($mapData['result']) - 1]->Longitude : -656;
            $center = [$centeralLong, $centerLat];
            foreach ($mapData['result'] as $key => $value) {
                $html = '<div className="marker-info" id="marker-info' . $value->ListingId . '"><p id="marker-loader' . $value->ListingId . '" >loading......</p></div>';
                $groups[] = array(
                    "type" => "Feature",
                    "id" => $value->ListingId,
                    "geometry" => array(
                        "type" => "Point",
                        "coordinates" => [$value->Longitude, $value->Latitude]
                    ),
                    "properties" => array(
                        "title" => 'circle',
                        "id" => $value->ListingId,
                        "class" => "",
                        "description" => $html,
                        "price" => $value->ShortPrice,
                        "originalPrice" => $value->ListPrice,
                        'marker-size' => 'small',
                        'marker-color' => '#ff5b60',
                        'marker-symbol' => 'circle'
                    )
                );
            }
            $response['geojson'] = $groups;
            $response['center'] = $center;
        } else {
            if ($mapData['result'] != []) {
                $mapData['result'] = collect($mapData['result'])->map(function ($item) {
                    $item->Dom = getActualDom($item->Timestamp_sql);
                    return $item;
                });
            }
            $response['mapdata'] = $mapData['result'];
        }
        $endTiming = strtotime(date("H:i:s"));
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
        $response['query'] = $mapData['query'];
        return response($response, 200);
    }
    /**
     *
     * Map Boundary
     * @param request
     * @return Json Response
     *
     */
    function mapBoundary(Request $request)
    {
        $searchText = $request->text_search ? $request->text_search : PropertyConstants::GTACITY;
        $cityData = [];
        $areaData = [];
        $cityData = PolygonsData::where("cityName", $searchText)->where("cityPolygons", "<>", "")->first();
        if (!$cityData) {
            $areaData = PolygonsData::where("areasName", $searchText)->where("areasPolygons", "<>", "")->first();
        }
        $response["areaData"] = $areaData;
        $response["cityData"] = $cityData;
        return response($response, 200);
    }

    public function updategeojson(Request $request)
    {
        // just for check
        $code=$request->code;
        $city=$request->city;
        $type=$request->type?$request->type:"city";
        $housenCode = "housen_2022_muhammad";
        $url = "https://strata.ca/_next/data/3u2xgqIGBb-7aYHgfV4TH/en/$city/condos-for-sale.json?route=$city&route=condos-for-sale";
        if ($code === $housenCode) {
            $response = Http::get($url);
            if ($response->ok()) {
                echo "\n";
                echo "\n";
                echo "\nAPI StATUS" . $response->status();
                echo "\n";
                $data = json_decode($response->body(), true);
                // $data = $data['data'];
                // boundary
                $polygons = [];
                $geodata = $data['pageProps']['staticPageProps']['neighbourhoodGroup']['geoPoints'];
                foreach ($geodata as $key => $value) {
                    $latlng = [];
                    $latlng[] = $value['lon'];
                    $latlng[] = $value['lat'];
                    $polygons[] = $latlng;
                }
                if($type=="city"){
                    $cityData = PolygonsData::where("cityName", $city)->update(['cityPolygons' => json_encode($polygons)]);
                }
                if($type=="area"){
                    $cityData = PolygonsData::where("areasName", $city)->update(['areasPolygons' => json_encode($polygons)]);
                }
                dd("updated ",$cityData);
            }
        } else {
            echo "Secret code mismatch !";
        }
    }
}
