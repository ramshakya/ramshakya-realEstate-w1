<?php

namespace App\Http\Controllers\frontend\propertiesListings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\PropertyConstants;
use App\Models\PolygonsData;

class SearchController extends Controller
{
    //
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
            // $params["curr_path_query"] = "43.866933327462874 -80.30768976442968,43.095997607180095 -80.30768976442968,43.11859110764115 -79.09732565052512,43.891718326524796 -79.14890366674238,43.866933327462874 -80.30768976442968";
            // $payload["curr_path_query"] = "43.866933327462874 -80.30768976442968,43.095997607180095 -80.30768976442968,43.11859110764115 -79.09732565052512,43.891718326524796 -79.14890366674238,43.866933327462874 -80.30768976442968";
            // $payload['shape'] = "polygon";
            // $params['shape'] = "polygon";
            // $isDefault = true;
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
            $filteredData = weduMapSearch(
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
            $filteredData = weduMapSearch(
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
            $filteredData = weduMapSearch(
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
            $filteredData = weduMapSearch(
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
        $orderBy = 'Asc';
        $response['allparams'] = $params;
        unset($params['sort_by']);
        unset($payload['isMapV2']);
        unset($params['isMapV2']);
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
        $startTiming = strtotime(date("H:i:s"));


        if (empty($textSearchFilter)) {
            $mapData = weduMapSearch(
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
            $mapData = weduMapSearch(
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
            $response['mapdata'] = $mapData['result'];
        }
        $endTiming = strtotime(date("H:i:s"));
        $response['startTiming'] = $startTiming;
        $response['endTiming'] = $endTiming;
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
}
