<?php

use App\Constants\PropertyConstants;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use App\Models\SqlModel\MostSearchedCities;
use App\Models\SqlModel\PropertyFeatures;
use Illuminate\Support\Facades\DB;
use App\Models\SqlModel\Websetting;
use App\Models\SqlModel\RetsPropertyDataPurged;

function updateRestPropertyTable()
{
    dd(time_elapsed_string('2013-05-01 00:22:35'));
    $query = RetsPropertyData::query();
    $query->select('id', 'ListingId');
    $data = $query->whereNotNull("ListingId");
    $data = $query->limit(10);
    $data = $query->with('propertiesImges')->orderBy('id', 'desc')->get();
    foreach ($data as $key => $value) {
        dd(collect($value));
    }
    dd($data[0]);
}

function calculatePayment($price, $down, $term)
{
    $loan = $price - $down;
    $rate = (2.5 / 100) / 12;
    $month = $term * 12;
    $payment = floor(($loan * $rate / (1 - pow(1 + $rate, (-1 * $month)))) * 100) / 100;
    return $payment;
}

function calcPay($MORTGAGE, $AMORTYEARS, $AMORTMONTHS, $INRATE, $COMPOUND, $FREQ, $DOWN)
{
    $MORTGAGE = $MORTGAGE - $DOWN;
    $compound = $COMPOUND / 12;
    $monTime = ($AMORTYEARS * 12) + (1 * $AMORTMONTHS);
    $RATE = ($INRATE * 1.0) / 100;
    $yrRate = $RATE / $COMPOUND;
    $rdefine = pow((1.0 + $yrRate), $compound) - 1.0;
    $PAYMENT = ($MORTGAGE * $rdefine * (pow((1.0 + $rdefine), $monTime))) / ((pow((1.0 + $rdefine), $monTime)) - 1.0);
    if ($FREQ == 12) {
        return $PAYMENT;
    }
    if ($FREQ == 26) {
        return $PAYMENT / 2.0;
    }
    if ($FREQ == 52) {
        return $PAYMENT / 4.0;
    }
    if ($FREQ == 24) {
        $compound2 = $COMPOUND / $FREQ;
        $monTime2 = ($AMORTYEARS * $FREQ) + ($AMORTMONTHS * 2);
        $rdefine2 = pow((1.0 + $yrRate), $compound2) - 1.0;
        $PAYMENT2 = ($MORTGAGE * $rdefine2 * (pow((1.0 + $rdefine2), $monTime2))) / ((pow((1.0 + $rdefine2), $monTime2)) - 1.0);
        return $PAYMENT2;
    }
}

function getPriceList()
{
    $price = array();
    $start = 0;
    $last = 30000;
    for ($i = 0; $i <= PropertyConstants::PRICELIST; $i++) {
        $last = $last - 1;
        $obj = [
            "text" => "$" . number_format_short($start) . " - $" . number_format_short($last),
            "value" => "$start-$last",
        ];
        array_push($price, $obj);
        if ($i == PropertyConstants::PRICELIST) {
            $obj = [
                "text" => "$" . number_format_short(60000000) . " + Above",
                "value" => "0-900000000000",
            ];
            array_push($price, $obj);
        }
        $start = $last;
        if ($last > 900000) {
            $last = $last + 200001;
        } else
            $last = $last + 10001;
    }
    return $price;
}

function get_auto_sugesstionbck($field, $text, $isSoldSearch = false)
{

    // prperty type subty in drop
    $raw_query = "";
    $raw_query .= "select distinct '" . $field . "' from PropertyAddressData where ";
    $query = \App\Models\PropertyAddressData::query();
    $query->select([$field]);
    $query->distinct($field);
    $query->limit(PropertyConstants::AUTO_SUGGESTION_LIMIT);
    if ($isSoldSearch) {
        /*$query->where(function ($it) {
            $it->where("Status", "=", "U")->orWhere("Status", "=", "A");
        });*/
        $raw_query .= "(`Status` = 'A' or `Status` = 'U') and ";
    } else {
        $raw_query .= "Status = 'A' and ";
    }
    if ($field == 'StandardAddress') {
        $raw_query .= "$field like '$text %'";
        $query->where($field, 'like', $text . '%');
    } else {
        $raw_query .= "$field like '% $text %'";
        //$query->where($field, 'like', '%' . $text . '%');
    }
    $raw_query .= " limit " . PropertyConstants::AUTO_SUGGESTION_DEFAULT_LIMIT;
    //$query->groupBy($field);
    //DB::enableQueryLog();

    $data = collect(DB::select($raw_query))->all();


    //$data = $query->get();
    //$query = DB::getQueryLog();
    // print_r($query);

    if (count($data) > 0) {
        $result = $data;
    } else {
        $result = [];
    }
    return $result;
}


function get_auto_sugesstion($field, $text, $isSoldSearch = false)
{

    // prperty type subty in drop
    $query = \App\Models\PropertyAddressData::query();
    $query->select([$field]);
    $query->distinct($field);
    $query->limit(PropertyConstants::AUTO_SUGGESTION_LIMIT);
    if ($isSoldSearch) {
        $query->where(function ($it) {
            $it->where("Status", "=", "U")->orWhere("Status", "=", "A");
        });
    } else {
        $query->where("Status", "=", "A");
    }
    if ($field == 'StandardAddress') {
        $query->where($field, 'like', $text . '%');
    } else {
        $query->where($field, 'like', '%' . $text . '%');
    }
    //$query->groupBy($field);
    //DB::enableQueryLog();
    $data = $query->get();
    //$query = DB::getQueryLog();
    //print_r($query);

    if (count($data) > 0) {
        $result = $data;
    } else {
        $result = [];
    }
    return $result;
}

function get_default_auto_sugesstion()
{
    $query = RetsPropertyData::query();
    $result = array();
    $query->select("Community");
    $query->where("Community", "!=", "");
    $query->limit(PropertyConstants::AUTO_SUGGESTION_DEFAULT_LIMIT);
    // $query->distinct('Community');
    $Community = $query->get();
    $result["Community"] = $Community;
    $query->select("City");
    $query->where("City", "!=", "");
    $query->distinct('Community');
    $city = $query->get();
    $result["City"] = $city;
    return $result;
}


function get_search_result($condition, $offset, $limit, $sortBy = 'ListPrice', $order = 'ASC', $textSearchField = '', $extra_custom_query = '', $extra_select = '', $shape = '', $array_data = array(), $orFilter = array(), $type = "", $isDefault = false)
{
    $isSoldSearch = false;
    $query = RetsPropertyData::query();
    if ($type == "main") {
        $query->select(
            PropertyConstants::SELECT_DATA
        );
    } else {
        $query->select(
            PropertyConstants::MAP_SELECT_DATA
        );
    }
    //
    //$query->where("ImageUrl", "!=", "");
    if (isset($condition['beds'])) {
        $query->where('BedroomsTotal', '>=', (int)$condition['beds']);
        unset($condition['beds']);
    }
    if (isset($condition['baths'])) {
        $query->where('BathroomsFull', '>=', (int)$condition['baths']);
        unset($condition['baths']);
    }

    if (isset($condition['PropertyType'])) {
        $query->where('PropertyType', $condition['PropertyType']);
    }
    //
    if (isset($condition['Dom'])) {
        $query->where('Dom', '<=', (float)$condition['Dom']);
    }

    if (isset($condition['price_min'])) {
        $query->where('ListPrice', '>=', (int)$condition['price_min']);
    }
    if (isset($condition['price_max'])) {
        $query->where('ListPrice', '<=', (int)$condition['price_max']);
    }
    if (isset($condition['status'])) {
        $query->where('PropertyStatus', $condition['status']);
    }
    if (isset($condition['soldStatus'])) {
        $query->where('Status', $condition['soldStatus']);
        unset($condition['soldStatus']);
    }

    if (isset($condition['Sqft'])) {
        if (str_contains($condition['Sqft'], '-')) {
            $exp = explode("-", $condition['Sqft']);
            $query->where('SqftMin', '>=', $exp[0]);
            $query->where('SqftMax', '<=', $exp[1]);
        }
        unset($condition['Sqft']);
    }
    if (isset($condition['PropertySubType'])) {
        if (is_array($condition['PropertySubType'])) {
            if (count($condition['PropertySubType'])) {
                $query->whereIn('PropertySubType', $condition['PropertySubType']);
            }
        } else {
            $query->where('PropertySubType', $condition['PropertySubType']);
        }
    }
    if (isset($condition['features'])) {
        $ids = PropertyFeatures::whereIn("FeaturesId", $condition['features'])->groupBy("PropertyId")->pluck("PropertyId")->toArray();
        if (is_array($ids) && count($ids)) {
            $query->whereIn('ListingId', $ids);
        }
    }
    if (isset($condition['basement'])) {
        if (count($condition['basement'])) {
            $query->whereIn('Bsmt1_out', $condition['basement']);
        }
    }
    //
    unset($condition['Sqft']);
    unset($condition['features']);
    unset($condition['basement']);
    unset($condition['PropertySubType']);
    unset($condition['radius']);
    unset($condition['curr_path']);
    unset($condition['curr_path_query']);
    unset($condition['center_lat']);
    unset($condition['center_lng']);
    unset($condition['shape']);
    unset($condition['multiplePropType']);
    unset($condition['openhouse']);
    unset($condition['price_min']);
    unset($condition['Dom']);
    unset($condition['PropertyType']);
    unset($condition['status']);
    unset($condition['price_min']);
    unset($condition['price_max']);
    unset($condition['beds']);
    unset($condition['baths']);

    if (isset($condition['Gar'])) {
        $query->where('Gar', $condition['Gar']);
        unset($condition['Gar']);
    }
    if (isset($condition['Park_spcs'])) {
        $query->where('Park_spcs', $condition['Park_spcs']);
        unset($condition['Park_spcs']);
    }
    if (isset($condition['Pool'])) {
        $query->where('Pool', '!=', $condition['Pool']);
        unset($condition['Pool']);
    }

    if (!empty($condition)) {
        $query->where($condition);
    }


    if (!empty($array_data)) {
        foreach ($array_data as $key => $value) {
            $query->whereIn($key, $value);
        }
    }
    if (!empty($orFilter)) {
        $query->orWhere($orFilter);
    }
    if ($textSearchField) {
        $query->where(function ($q) use ($textSearchField) {
            $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
            $q->orWhere('Municipality', 'like', $textSearchField . '%');
            $q->orWhere('ListingId', $textSearchField);
            $q->orWhere('City', $textSearchField);
            $q->orWhere('County', $textSearchField);
            $q->orWhere('Community', $textSearchField);
        });
    }
    if ($textSearchField == trim($textSearchField) && str_contains($textSearchField, ' ')) {
        if (strlen($textSearchField) >= 5) {
            $isSoldSearch = true;
            $PurgedTable = RetsPropertyDataPurged::query();
            if ($type == "main") {
                $PurgedTable->select(
                    PropertyConstants::SELECT_DATA
                );
            } else {
                $PurgedTable->select(
                    PropertyConstants::MAP_SELECT_DATA
                );
            }
            $PurgedTable->orWhere('StandardAddress', 'like', $textSearchField . '%');
            $PurgedTable->orWhere('Municipality', 'like', $textSearchField . '%');
            $PurgedTable->orWhere('ListingId', $textSearchField);
            $PurgedTable->orWhere('City', $textSearchField);
            $PurgedTable->orWhere('County', $textSearchField);
            $PurgedTable->orWhere('Community', $textSearchField);
        }
    } else {
        $query->where("Status", "A");
    }
    // todo:: this is temporary where purged data is not done
    $query->whereNotNull('Latitude');
    $query->whereNotNull('Longitude');
    $total_temp = 0;
    //$total = $query->count();//
    if ($isDefault) {
        //$total = $query->count();
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
                //$total = $query->count();//
            } else {
                $query->whereRaw($extra_custom_query);
                //$total = $query->count(); //
            }
        }
        $total_temp = $query->count();
        $total = RetsPropertyData::count();
    } else {
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
            } else {
                $query->whereRaw($extra_custom_query);
            }
        }
        if ($isSoldSearch) {
            $toget = $PurgedTable->union($query);
            $total = $toget->get();
            $total = collect($total)->count();
            //$total = $PurgedTable->union($query)->count();
        } else {
            $total = $query->count();
        }
    }
    $query->orderBy($sortBy, $order);
    $query->skip($offset);
    $query->take($limit);
    DB::enableQueryLog();
    //$result = $query->get();
    if ($isSoldSearch) {
        $result = $PurgedTable->union($query)->get();
    } else {
        $result = $query->get();
    }
    if ($result && count($result) > 0) {
        $final_result = array(
            "result" => $result,
            "total" => $total,
            "total_temp" => $total_temp
        );
        return $final_result;
    } else {
        $final_result = array(
            "result" => false,
            "total" => $total,
            "total_temp" => $total_temp
        );
        return $final_result;
    }
}
function get_search_result_raw($condition, $offset, $limit, $sortBy = 'ListPrice', $order = 'ASC', $textSearchField = '', $extra_custom_query = '', $extra_select = '', $shape = '', $array_data = array(), $orFilter = array(), $type = "")
{


    $query = RetsPropertyData::query();
    if ($type == "main") {
        $query->select(
            PropertyConstants::SELECT_DATA
        );
    } else {
        $query->select(
            PropertyConstants::MAP_SELECT_DATA
        );
    }

    //$query->where("ImageUrl", "!=", "");
    if (isset($condition['beds'])) {
        $query->where('BedroomsTotal', '>=', (int)$condition['beds']);
        unset($condition['beds']);
    }
    if (isset($condition['baths'])) {
        $query->where('BathroomsFull', '>=', (int)$condition['baths']);
        unset($condition['baths']);
    }
    if (isset($condition['PropertySubType'])) {
        $query->where('PropertySubType', $condition['PropertySubType']);
    }
    if (isset($condition['PropertyType'])) {
        $query->where('PropertyType', $condition['PropertyType']);
    }
    //
    if (isset($condition['Dom'])) {
        $query->where('Dom', '<=', (float)$condition['Dom']);
    }


    if (isset($condition['multiplePropType'])) {
        if (count($condition['multiplePropType'])) {
            $query->whereIn('PropertySubType', $condition['multiplePropType']);
        }
    }

    if (isset($condition['price_min'])) {
        $query->where('ListPrice', '>=', (int)$condition['price_min']);
    }
    if (isset($condition['price_max'])) {
        $query->where('ListPrice', '<=', (int)$condition['price_max']);
    }
    if (isset($condition['status'])) {
        $query->where('PropertyStatus', $condition['status']);
    }
    if (isset($condition['basement'])) {
        $query->where('Bsmt1_out', $condition['basement']);
    }
    if (isset($condition['Sqft'])) {
        if (str_contains($condition['Sqft'], '-')) {
            $exp = explode("-", $condition['Sqft']);
            $query->where('SqftMin', '>=', (int)$exp[0]);
            $query->where('SqftMax', '<=', (int)$exp[1]);
        }
        unset($condition['Sqft']);
    }
    // $result = $query->toSql();
    unset($condition['Sqft']);
    unset($condition['radius']);
    unset($condition['curr_path']);
    unset($condition['curr_path_query']);
    unset($condition['center_lat']);
    unset($condition['center_lng']);
    unset($condition['shape']);
    unset($condition['basement']);
    unset($condition['multiplePropType']);
    unset($condition['openhouse']);
    unset($condition['PropertySubType']);
    unset($condition['price_min']);
    unset($condition['Dom']);
    unset($condition['PropertyType']);
    unset($condition['status']);
    unset($condition['price_min']);
    unset($condition['price_max']);
    unset($condition['beds']);
    unset($condition['baths']);
    unset($condition['PropertySubType']);
    if (isset($condition['Gar'])) {
        $query->where('Gar', $condition['Gar']);
        unset($condition['Gar']);
    }
    if (isset($condition['Park_spcs'])) {
        $query->where('Park_spcs', $condition['Park_spcs']);
        unset($condition['Park_spcs']);
    }
    if (isset($condition['Pool'])) {
        $query->where('Pool', '!=', $condition['Pool']);
        unset($condition['Pool']);
    }

    if (!empty($condition)) {
        $query->where($condition);
    }

    if (isset($extra_custom_query) && $extra_custom_query != '') {
        if (isset($shape) && $shape != '' && $shape == 'circle') {
            $query->select($extra_select);
            $query->having($extra_custom_query);
        } else {
            $query->whereRaw($extra_custom_query);
            // $query->where($extra_custom_query);
        }
    }
    if (!empty($array_data)) {
        foreach ($array_data as $key => $value) {
            $query->whereIn($key, $value);
        }
    }
    if (!empty($orFilter)) {
        $query->orWhere($orFilter);
    }
    if ($textSearchField) {
        $query->where(function ($q) use ($textSearchField) {
            $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
            $q->orWhere('Municipality', 'like', $textSearchField . '%');
            $q->orWhere('ListingId', $textSearchField);
            $q->orWhere('City', $textSearchField);
            $q->orWhere('County', $textSearchField);
            $q->orWhere('Community', $textSearchField);
        });
    }
    $query->whereNotNull('Latitude');
    $query->whereNotNull('Longitude');
    //$query->whereNotNull('ListPrice');
    $query->orderBy($sortBy, $order);
    $total = $query->count();
    $query->skip($offset);
    $query->take($limit);
    $result = $query->get();
    //    echo $result;
    //    die();
    // $result = $query->with('propertiesImges')->pluck("County");
    if ($result && count($result) > 0) {
        $final_result = array(
            "result" => $result,
            "total" => $total,
        );
        return $final_result;
    } else {
        $final_result = array(
            "result" => false,
            "total" => $total,
        );
        return $final_result;
    }
}



// delete after Test
function getFormatedData($resutl_properties, $proprtyDetail = false)
{
    $final_result = array();
    if ($resutl_properties) {
        foreach ($resutl_properties as $key => $prop) {
            if ($proprtyDetail) {
                $prop = collect($prop)->all();
                $data['properties_imge'] = isset($prop['properties_imge']) ? $prop['properties_imge'] : [];
            }
            $data['addedOn'] = [
                "text" => "DOM",
                "value" => time_elapsed_string(isset($prop['updated_time']) ? $prop['updated_time'] : "")
            ];
            $data['address'] = $prop['StandardAddress'];
            $data['bagdes'] = [
                "bed" => [
                    'text' => "Bed",
                    'value' => (int)$prop["BedroomsTotal"] ? $prop["BedroomsTotal"] : ""
                ],
                "bath" => [
                    'text' => "Bath",
                    'value' => (int)$prop["BathroomsFull"] ? $prop["BathroomsFull"] : ""
                ],

                "garage" => [
                    'text' => "Garage",
                    'value' => $prop["Gar"] ? $prop["Gar"] : ""
                ],

                "Sqft" => $prop["Sqft"]
            ];
            $data['county'] = $prop['County'];
            $data['city'] = $prop['City'];
            $data['sp_dol'] = $prop['Sp_dol'];
            $data['Latitude'] = $prop['Latitude'];
            $data['Longitude'] = $prop['Longitude'];
            $data['propertyType'] = $prop['PropertyType'];
            $data['propertySubType'] = $prop['PropertySubType'];
            $data['extras'] = [
                'text' => 'Extras',
                'value' => $prop['Extras']
            ];
            $data['generalDesc'] = [
                'text' => 'General Description',
                'value' => $prop['PublicRemarks']
            ];
            $img = "";
            $imgArr = isset($prop["properties_imges"][0]) ? $prop["properties_imges"][0] : [];
            if (count($imgArr) > 0)
                $img = $imgArr['s3_image_url'];
            $data['image'] = [
                'text' => 'propertyImage',
                'value' => $img
            ];
            $data['rltr'] = [
                'text' => 'Rltr',
                'value' => $prop['Rltr']
            ];

            $data['community'] = $prop['Community'];
            $data["isSale"] = $prop['MlsStatus'] == "Sale" ? 1 : 0;
            $data["isRent"] = $prop['MlsStatus'] == "Rent" ? 1 : 0;
            $data["isLease"] = $prop['MlsStatus'] == "Lease" ? 1 : 0;
            $data["isOpenHouse"] = 0;
            $data['landSize'] = [
                'value' => ($prop["Sqft"]) ? str_replace("<", "", $prop["Sqft"]) . ' Sqft' : ""
            ];
            $data['mortgage'] = [
                'text' => 'Estimated Mortgage',
                'value' => "12972",
                'type' => "m"
            ];
            $data['price'] = $prop['ListPrice'];
            $data["Short_price"] = number_format_short($prop['ListPrice']);
            $data['county'] = $prop['County'];
            $data['city'] = $prop['City'];
            $data['sp_dol'] = $prop['Sp_dol'];
            $data['propertyDetail'] = [
                "title" => "Property Details",
                "details" => [
                    [
                        "text" => "Mls no",
                        "value" => $prop['ListingId']
                    ],
                    [
                        "text" => "Property Type",
                        "value" => $prop['PropertyType']
                    ],
                    [
                        "text" => "Neighborhood",
                        "value" => $prop['Community']
                    ],
                    [
                        "text" => "Type",
                        "value" => $prop['PropertySubType']
                    ],
                    [
                        "text" => "Land Size",
                        "value" => $prop["Sqft"] . ' Sqft'
                    ],
                    [
                        "text" => "Parking Type",
                        "value" => "field not found"
                    ],
                    [
                        "text" => "Bedrooms",
                        "value" => $prop['BedroomsTotal']
                    ],
                    [
                        "text" => "Bathrooms",
                        "value" => $prop['BathroomsFull']
                    ],
                    [
                        "text" => "Air Conditioning",
                        "value" => $prop['A_c']
                    ],
                    [
                        "text" => "Kitchen",
                        "value" => "field not found"
                    ],
                    [
                        "text" => "Basement",
                        "value" => $prop['Bsmt1_out']
                    ],
                    [
                        "text" => "Fireplace",
                        "value" => $prop['Fpl_num']
                    ],
                    [
                        "text" => "Cross street",
                        "value" => $prop['Cross_st']
                    ],
                    [
                        'text' => "Garage Type",
                        'value' => $prop["Gar_type"]
                    ],
                    [
                        "text" => "Status",
                        "value" => $prop['MlsStatus']
                    ],
                    [
                        "text" => "Fuel",
                        "value" => $prop['Fuel']
                    ],
                    [
                        "text" => "Pool",
                        "value" => $prop['Pool']
                    ],
                ]
            ];

            //             $prop = collect($prop)->all();
            //             $prop["isSale"] = $prop['MlsStatus'] == "Sale" ? 1 : 0;
            //             $prop["isRent"] = $prop['MlsStatus'] == "Rent" ? 1 : 0;
            //             $prop["isLease"] = $prop['MlsStatus'] == "Lease" ? 1 : 0;
            //             $prop["isOpenHouse"] = 0;
            //             $prop["Short_price"] = number_format_short($prop['ListPrice']);
            //             $prop["badges"] = [[
            //                 "Br" => $prop["BedroomsTotal"],
            //                 "Bath_tot" => $prop["BathroomsFull"],
            //                 "Sqft" => $prop["Sqft"]
            //             ]];

            //$prop["media"] = $prop["srcImg"] = isset($prop["properties_imgesGeneral Description"][0]) ? $prop["properties_imges"][0] : [];
            $prop["srcImg"] = "";
            unset($prop['properties_imges']);
            array_push($final_result, $data);
        }
    }
    return $final_result;
}

function getMapFormatedData($resutl_properties)
{
    $final_result = array();
    if ($resutl_properties) {
        foreach ($resutl_properties as $key => $prop) {
            $prop = collect($prop)->all();
            $data["Short_price"] = number_format_short($prop['ListPrice']);
            $data["Latitude"] = $prop['Latitude'];
            $data["Longitude"] = $prop['Longitude'];
            array_push($final_result, $data);
        }
    }
    return $final_result;
}
// code changed by vinay added $priceMin,priceMax, $city
function getSimilar($community, $type, $priceMin = null, $priceMax = null, $city = null)
{
    $data = array();
    if ($priceMin != null && $priceMax != null) {
        // $query->where('ListPrice', '>=', (int)$priceMin);
        // $query->where('ListPrice', '<=', (int)$priceMax);
    }
    // $query->orderBy('updated_time', 'desc');
    if ($city && $community) {

        $query = RetsPropertyData::query();
        $query->select(PropertyConstants::SELECT_DATA);
        if ($type == 'sale') {
            $query->where('PropertyStatus', 'Sale');
        }
        if ($type == 'rent') {
            $query->where('PropertyStatus', 'Lease');
        }
        $query->where('City', $city);
        $query->where('Community', $community);
        $query->where(function ($q) {
            $q->where('ImageUrl', '!=', '')
                ->orWhere('ImageUrl', '!=',  null);
        });
        $query->distinct('Community');
        $query->orderBy('Dom', 'asc');
        $query->limit(PropertyConstants::SIMILAR_LIST);
        $data = $query->get();
    }

    if (!count($data)) {
        if ($community) {

            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA);
            if ($type == 'sale') {
                $query->where('PropertyStatus', 'Sale');
            }
            if ($type == 'rent') {
                $query->where('PropertyStatus', 'Lease');
            }
            $query->where('Community', $community);
            $query->where(function ($q) {
                $q->where('ImageUrl', '!=', '')
                    ->orWhere('ImageUrl', '!=',  null);
            });
            $query->distinct('Community');
            $query->orderBy('Dom', 'asc');
            $query->limit(PropertyConstants::SIMILAR_LIST);
            $data = $query->get();
        }
    }
    if (!count($data)) {
        if ($city) {

            $query = RetsPropertyData::query();
            $query->select(PropertyConstants::SELECT_DATA);
            if ($type == 'sale') {
                $query->where('PropertyStatus', 'Sale');
            }
            if ($type == 'rent') {
                $query->where('PropertyStatus', 'Lease');
            }
            $query->where('City', $city);
            $query->where(function ($q) {
                $q->where('ImageUrl', '!=', '')
                    ->orWhere('ImageUrl', '!=',  null);
            });
            $query->distinct('Community');
            $query->orderBy('Dom', 'asc');
            $query->limit(PropertyConstants::SIMILAR_LIST);
            $data = $query->get();
        }
    }
    if (!count($data)) {

        $query = RetsPropertyData::query();
        $query->select(PropertyConstants::SELECT_DATA);
        if ($type == 'sale') {
            $query->where('PropertyStatus', 'Sale');
        }
        if ($type == 'rent') {
            $query->where('PropertyStatus', 'Lease');
        }
        $query->where(function ($q) {
            $q->where('ImageUrl', '!=', '')
                ->orWhere('ImageUrl', '!=',  null);
        });
        $query->distinct('Community');
        $query->orderBy('Dom', 'asc');
        $query->limit(PropertyConstants::SIMILAR_LIST);
        $data = $query->get();
    }
    return array(
        'result' => $data,
    );
}

function getFormatedDataMongo($resutl_properties)
{
    $final_result = array();
    if ($resutl_properties) {
        foreach ($resutl_properties as $key => $prop) {
            $prop = collect($prop)->all();
            $prop["isSale"] = $prop['MlsStatus'] == "Sale" ? 1 : 0;
            $prop["isRent"] = $prop['MlsStatus'] == "Rent" ? 1 : 0;
            $prop["isLease"] = $prop['MlsStatus'] == "Lease" ? 1 : 0;
            $prop["isOpenHouse"] = 0;
            $prop["Short_price"] = number_format_short($prop['ListPrice']);
            $prop["badges"] = [[
                "Br" => $prop["BedroomsTotal"],
                "Bath_tot" => $prop["BathroomsFull"],
                "Sqft" => $prop["Sqft"]
            ]];
            $prop["srcImg"] = "";
            //$prop["media"] = $prop["srcImg"] = isset($prop["properties_images"][0]) ? $prop["properties_images"][0]["s3_image_url"] : "";
            //unset($prop['properties_images']);
            //unset($prop['media']);
            array_push($final_result, $prop);
        }
    }
    return $final_result;
}

/***
 *
 *
 */
function number_format_short($n, $precision = 1)
{
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
    // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
    // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ($precision > 0) {
        $dotzero = '.' . str_repeat('0', $precision);
        $n_format = str_replace($dotzero, '', $n_format);
    }
    return $n_format . $suffix;
}

/**
 *
 *
 */
function getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
{
    //defaults
    if (!$adjacents) $adjacents = 1;
    if (!$limit) $limit = 15;
    if (!$page) $page = 1;
    if (!$targetpage) $targetpage = "/";
    $prev = $page - 1;                                  //previous page is page - 1
    $next = $page + 1;                                  //next page is page + 1
    $lastpage = ceil($totalitems / $limit);             //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                              //last page minus 1
    /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
    */
    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= '<nav aria-label="Page navigation"><ul class="pagination justify-content">';
        if ($page > 1)
            $pagination .= "<li class='page-item active'><button class='pagination_page ' rel='$prev' >« prev</button></li>";
        else
            $pagination .= "<li class='page-item active'><button class='pagination_page page-link active' rel='' >« prev</button></li>";
        if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
        {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= "<li class='page-item active'><button class='pagination_page page-link active' rel='$counter' >$counter</button></li>";
                else
                    $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$counter' >$counter</button></li>";
            }
        } elseif ($lastpage >= 7 + ($adjacents * 2))   //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 3)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='page-item active'><button class='pagination_page page-link active' rel='$counter' >$counter</button></li>";
                    else
                        $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$counter' >$counter</button></li>";
                }
                $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='' >...</button></li>";
                $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$lpm1' >$lpm1</button></li>";
                $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$lastpage' >$lastpage</button></li>";
            } //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<li class='page-item '><button class='pagination_page page-link ' rel='1' >1</button></li>";
                $pagination .= "<li class='page-item '><button class='pagination_page page-link ' rel='2' >2</button></li>";
                $pagination .= "<li class='page-item '><button class='pagination_page page-link'  rel='' >...</button></li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='page-item active'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$counter' >$counter</button></li>";
                    else
                        $pagination .= "<li class='page-item '><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$counter' >$counter</button></li>";
                }
                $pagination .= "<li class='page-item '><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='' >...</button></li>";
                $pagination .= "<li class='page-item '><button class='pagination_page page-link ' rel='$lpm1' >$lpm1</button></li>";
                $pagination .= "<li class='page-item '><button class='pagination_page page-link ' rel='$lastpage' >$lastpage</button></li>";
            } //close to end; only hide early pages
            else {
                $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate(1)' rel='1' >1</button></li>";
                $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate(2)' rel='2' >2</button></li>";
                $pagination .= "<li class='page-item'><button class='pagination_page page-link'  rel='' >...</button></li>";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='page-item active'><button class='pagination_page page-link active' rel='$counter' >$counter</button></li>";
                    else
                        $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$counter' >$counter</button></li>";
                }
            }
        }

        //next button
        if ($page < $counter - 1)
            $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' rel='$next' >next »</button></li>";
        else
            $pagination .= "<li class='page-item'><button class='pagination_page page-link' onclick='getPaginate($counter)' >next »</button></li>";
        //$pagination .= "<span class='pagination_page' >next »</span>";
        $pagination .= "</ul></nav>\n";
    }

    return $pagination;
}

function getUserIP()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function mortagage($main_price, $taxes = 0)
{
    $dp = (20 / 100) * $main_price;
    $maintainance_amt = ($dp / 100) * 0;
    $loanAmount = $main_price - $dp;
    $numberOfMonths = 20 * 12;
    $rateOfInterest = 6;
    $monthlyInterestRatio = ($rateOfInterest / 100) / 12;
    $top = pow((1 + $monthlyInterestRatio), $numberOfMonths);
    $bottom = $top - 1;
    $sp = $top / $bottom;
    $emi = (($loanAmount * $monthlyInterestRatio) * $sp);
    $final = $emi + $maintainance_amt;
    $final = ($emi / 2) + ((int)$maintainance_amt / 2);
    $tx = $taxes / 12;
    $s = (float)$final;
    $rt = (float)$tx;
    $newPrice = (float)$s + (float)$rt + 0;
}

function formatDollars($dollars)
{
    $formatted = "$" . number_format(substr($dollars, 0, strpos($dollars, ".")));
    // return $formatted = "$" . number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $dollars)), 2);
    return $dollars < 0 ? "({$formatted})" : "{$formatted}";
}

function getProperties($slug, $mls_num = 0)
{
    $table = "";
    $metDescString = "";
    try {
        DB::enableQueryLog();
        $query = RetsPropertyDataResi::query();
        $table = "Residential";
        $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA);
        if ($mls_num) {
            $query->where('Ml_num', $mls_num);
        } else {
            $query->where('SlugUrl', $slug);
        }
        $res = $query->with('propertiesImages:s3_image_url,listingID')->first();
        $res->PropertyType = $table;
        // $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
        unset($res["Extras"]);
        unset($res["Ad_text"]);
        unset($res["RoomsDescription"]);
        if ($res) {
            return $res;
        }

        if (empty($res)) {

            $table = "Condo";
            $query = RetsPropertyDataCondo::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_CONDO);
            if ($mls_num) {
                $query->where('Ml_num', $mls_num);
            } else {
                $query->where('SlugUrl', $slug);
            }
            $res = $query->with('propertiesImages')->first();
            if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }
            $res->PropertyType = $table;
            unset($res["Extras"]);
            unset($res["Ad_text"]);
            unset($res["RoomsDescription"]);
            return $res;
            // $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;

        }
        if (empty($res)) {
            $table = "Commercial";
            $query = RetsPropertyDataComm::query();
            $query->select(PropertyConstants::PROPERTY_DETAILS_SELECT_DATA_COMM);
            if ($mls_num) {
                $query->where('Ml_num', $mls_num);
            } else {
                $query->where('SlugUrl', $slug);
            }
            $res = $query->with('propertiesImages')->first();
            if ($res->propertiesImages != []) {
                $res->propertiesImages = collect($res->propertiesImages)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }
            $res->PropertyType = $table;
            unset($res["Extras"]);
            unset($res["Ad_text"]);
            unset($res["RoomsDescription"]);
            return $res;
            // $metDescString = $res->Addr . " " . $res->County . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;

        }
    } catch (\Throwable $th) {
        //throw $th;
    }
}
//Made by siddharth
function getPropstats($slug, $mls_num = 0)
{
    $table = "";
    $metDescString = "";
    try {
        DB::enableQueryLog();
        $query = RetsPropertyData::query();
        $table = "Residential";
        $query->select();
        if ($slug) {
            $query->where('SlugUrl', $slug);
        } else {
            $query->where('Ml_num', $mls_num);
        }
        $res = $query->first();
        // dd($res);
        $res->PropertyType = $table;
        // $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;
        unset($res["Extras"]);
        unset($res["Ad_text"]);
        unset($res["RoomsDescription"]);
        if ($res) {
            return $res;
        }

        if (empty($res)) {

            $table = "Condo";
            $query = RetsPropertyDataPurged::query();
            $query->select();
            if ($mls_num) {
                $query->where('Ml_num', $mls_num);
            } else {
                $query->where('SlugUrl', $slug);
            }
            $res = $query->with('ImageUrl')->first();
            if ($res->ImageUrl != []) {
                $res->ImageUrl = collect($res->ImageUrl)->map(function ($data) {
                    return $data->s3_image_url;
                });
            }
            $res->PropertyType = $table;
            unset($res["Extras"]);
            unset($res["Ad_text"]);
            unset($res["RoomsDescription"]);
            return $res;
            // $metDescString = $res->Addr . " " . $res->County . " " . $res->Br . " " . $res->Bath_tot . " " . $res->Sqft . " " . $res->Ml_num;

        }
    } catch (\Throwable $th) {
        //throw $th;
    }
}
//Changes By Siddharth
function cityStats($agentId, $CitiesLastDays = '', $DateTo = '', $DateFrom = '')
{
    // dd($DateTo);
    if (isset($CitiesLastDays)) {
        return MostSearchedCities::where("AgentId", $agentId)->where('created_at', '>=', \Carbon\Carbon::now()->subdays($CitiesLastDays))->orderBy('Count', 'desc')->get();
    } elseif (isset($DateTo)) {
        if (isset($DateTo)) {
            if (isset($DateFrom)) {
                $DateFrom;
            } else {
                $DateFrom = date("Y-m-d");
            }
            return MostSearchedCities::where("AgentId", $agentId)->whereDate('created_at', '<=', $DateTo)->whereDate('created_at', '>=', $DateFrom)->get();
        }
    } else {
        // return MostSearchedCities::where("AgentId",$agentId)->orderBy('updated_at', 'desc')->get();
        return MostSearchedCities::where("AgentId", $agentId)->orderBy('Count', 'desc')->get();
    }
}

function yelp_data($key, $lat, $lng, $type)
{
    /* API URL */
    /* Init cURL resource */
    $ch = curl_init();
    /*set authorization*/
    $authorization = "Authorization: Bearer " . $key;
    $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
        $authorization
    );

    /*url making*/
    $url = PropertyConstants::YELP_BUSINESS_SEARCH . $type . '&latitude=' . $lat . '&longitude=' . $lng . '&radius=' . PropertyConstants::RADIUS_FOR_NEARBY;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    /* execute request */
    $result = curl_exec($ch);
    /* close cURL resource */
    curl_close($ch);
    return $result;
}


function getTemplate($propData)
{
    $name = $propData['name'];
    $propertyDetails = $propData['propertyDetails'];
    $property_url = $propData['property_url'];
    $websetting = $propData['websetting'];
    $addr = isset($propertyDetails['Addr'])?$propertyDetails['Addr']:"";
    $price = isset($propertyDetails['Lp_dol'])?formatDollars($propertyDetails['Lp_dol']):0;
    $PropertyStatus = isset($propertyDetails['S_r'])?$propertyDetails['S_r']:"";
    $dom =  isset($propertyDetails['Dom'])?$propertyDetails['Dom']:"";
    $image = RetsPropertyData::select("ImageUrl")->where("ListingId", isset($propertyDetails['Ml_num'])?$propertyDetails['Ml_num']:0)->first();
    $ImageUrl =  $image?env('APP_URL') . "/storage/".$image->ImageUrl:'https://www.wedu.ca/images/comingSoon.webp';
    $PropertyType = isset($propertyDetails['PropertyType'])?$propertyDetails['PropertyType']:"";//$propertyDetails->PropertyType;
    $bed =  isset($propertyDetails['Br'])?$propertyDetails['Br']:"";//$propertyDetails->Br;
    $bath = isset($propertyDetails['Bath_tot'])?$propertyDetails['Bath_tot']:"";//$propertyDetails->Bath_tot;
    $mls =  isset($propertyDetails['Ml_num'])?$propertyDetails['Ml_num']:"";//$propertyDetails->Ml_num;
    $Ad_text =  isset($propertyDetails['Ad_text'])?$propertyDetails['Ad_text']:"";//$propertyDetails->Ad_text;
    $logo = $websetting->UploadLogo;
    $html = "";
    $html .= '<!DOCTYPE html>
    <html>
    <body style="text-align: center;">
        <div role="article" >
            <table dir="ltr" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f6f8f9">
                <tbody>
                    <tr>
                        <td class="m_-2176963290679972721mlTemplateContainer" align="center">
                            <table class="m_-2176963290679972721mlContentTable" width="600" cellspacing="0" cellpadding="0"
                                border="0" align="center">
                                <tbody>
                                    <tr>
                                        <td>
                                        <table class="m_-2176963290679972721mlContentTable" width="600" cellspacing="0"
                                        cellpadding="0" border="0" bgcolor="#ffffff" align="center">


                                        <tbody>


                                            <tr>


                                                <td>


                                                    <table class="m_-2176963290679972721mlContentTable"
                                                        style="" width="600"
                                                        cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"
                                                        align="center">


                                                        <tbody>


                                                            <tr>


                                                                <td>


                                                                    <table role="presentation"
                                                                        style=""
                                                                        class="m_-2176963290679972721mlContentTable"
                                                                        width="600" cellspacing="0" cellpadding="0"
                                                                        border="0" align="center">


                                                                        <tbody>


                                                                            <tr>


                                                                                <td style="line-height:10px;min-height:10px"
                                                                                    height="10"></td>


                                                                            </tr>


                                                                        </tbody>


                                                                    </table>


                                                                    <table role="presentation"
                                                                        style=""
                                                                        class="m_-2176963290679972721mlContentTable"
                                                                        width="600" cellspacing="0" cellpadding="0"
                                                                        border="0" align="center">


                                                                        <tbody>


                                                                            <tr>


                                                                                <td style="padding:0px 0px"
                                                                                    class="m_-2176963290679972721mlContentOuter"
                                                                                    align="center">


                                                                                    <table role="presentation"
                                                                                        style="width:100%;min-width:100%"
                                                                                        width="100%" cellspacing="0"
                                                                                        cellpadding="0" border="0"
                                                                                        align="center">


                                                                                        <tbody>


                                                                                            <tr>


                                                                                                <td align="center">


                                                                                                    <table
                                                                                                        role="presentation"
                                                                                                        style="width:100%;min-width:100%"
                                                                                                        width="100%"
                                                                                                        cellspacing="0"
                                                                                                        cellpadding="0"
                                                                                                        border="0">


                                                                                                        <tbody>


                                                                                                            <tr>


                                                                                                                <td class="m_-2176963290679972721mlContentButton"
                                                                                                                    style="font-family:"
                                                                                                                    Poppins",sans-serif"
                                                                                                                    align="center">
                                                                                                                    <img src="' . $ImageUrl . '"
                                                                                                                        alt="propertyimage" width="100%" / >



                                                                                                                </td>


                                                                                                            </tr>


                                                                                                        </tbody>


                                                                                                    </table>


                                                                                                </td>


                                                                                            </tr>


                                                                                        </tbody>


                                                                                    </table>


                                                                                </td>


                                                                            </tr>


                                                                        </tbody>


                                                                    </table>


                                                                    <table role="presentation"
                                                                        style=""
                                                                        class="m_-2176963290679972721mlContentTable"
                                                                        width="600" cellspacing="0" cellpadding="0"
                                                                        border="0" align="center">


                                                                        <tbody>


                                                                            <tr>


                                                                                <td class="m_-2176963290679972721spacingHeight-30"
                                                                                    style="line-height:30px;min-height:30px"
                                                                                    height="30"></td>


                                                                            </tr>


                                                                        </tbody>


                                                                    </table>


                                                                </td>


                                                            </tr>


                                                        </tbody>


                                                    </table>


                                                </td>


                                            </tr>


                                        </tbody>


                                    </table>
                                            <table class="m_-2176963290679972721mlContentTable" width="600" cellspacing="0"
                                                cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="m_-2176963290679972721mlContentTable"
                                                                style="" width="600"
                                                                cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"
                                                                align="center">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table role="presentation"
                                                                                style=" ; margin:2% auto"
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="line-height:10px;min-height:10px;padding:2% 6%"
                                                                                            height="10">
                                                                                            Price:' . $price . '
                                                                                        </td>
                                                                                        <td style="line-height:10px;min-height:10px;padding:2% 6%;text-align:right;"
                                                                                            height="10">
                                                                                            Status:' . $PropertyStatus . '
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td style="line-height:10px;min-height:10px;padding:2% 6%"
                                                                                            height="10">
                                                                                            Address:' . $addr . '
                                                                                        </td>
                                                                                        <td style="line-height:10px;min-height:10px;padding:2% 6%;text-align:right;"
                                                                                            height="10">
                                                                                            Dom:' . $dom . '
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                    <td style="line-height:10px;min-height:10px;padding:2% 6%;text-align:right;"
                                                                                        height="10">
                                                                                        Bed:' . $bed . '
                                                                                    </td>
                                                                                    <td style="line-height:10px;min-height:10px;padding:2% 6%"
                                                                                        height="10">
                                                                                        Bath:' . $bath . '
                                                                                    </td>

                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>

                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="m_-2176963290679972721mlContentTable" width="600" cellspacing="0"
                                                cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="m_-2176963290679972721mlContentTable"
                                                                style="" width="600"
                                                                cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"
                                                                align="center">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="line-height:10px;min-height:10px"
                                                                                            height="10"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding:0px 40px"
                                                                                            class="m_-2176963290679972721mlContentOuter"
                                                                                            align="center">
                                                                                            <table role="presentation"
                                                                                                width="100%" cellspacing="0"
                                                                                                cellpadding="0" border="0"
                                                                                                align="center">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td id="m_-2176963290679972721bodyText-12"
                                                                                                            style="font-family:"Poppins",sans-serif;font-size:14px;line-height:150%;color:#6f6f6f">
                                                                                                            <p
                                                                                                                style="margin-top:0px;margin-bottom:0px;line-height:150%;text-align:justify">
                                                                                                                <span
                                                                                                                    style="color:rgb(128,189,255)"><span
                                                                                                                        style="color:rgb(0,0,0)"> ' . $Ad_text . '
                                                                                                                    </span></span>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="m_-2176963290679972721spacingHeight-20"
                                                                                            style="line-height:20px;min-height:20px"
                                                                                            height="20"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table class="m_-2176963290679972721mlContentTable" width="600" cellspacing="0"
                                                cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table class="m_-2176963290679972721mlContentTable"
                                                                style="" width="600"
                                                                cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff"
                                                                align="center">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="line-height:10px;min-height:10px"
                                                                                            height="10"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding:0px 40px"
                                                                                            class="m_-2176963290679972721mlContentOuter"
                                                                                            align="center">
                                                                                            <table role="presentation"
                                                                                                style="width:100%;min-width:100%"
                                                                                                width="100%" cellspacing="0"
                                                                                                cellpadding="0" border="0"
                                                                                                align="center">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td align="center">
                                                                                                            <table
                                                                                                                role="presentation"
                                                                                                                style="width:100%;min-width:100%"
                                                                                                                width="100%"
                                                                                                                cellspacing="0"
                                                                                                                cellpadding="0"
                                                                                                                border="0">
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td class="m_-2176963290679972721mlContentButton"
                                                                                                                            style="font-family:"Poppins",sans-serif"
                                                                                                                            align="center">

                                                                                                                            <a class="m_-2176963290679972721mlContentButton"
                                                                                                                                href="' . $property_url . '"
                                                                                                                                style="font-family:"Poppins",sans-serif;background-color:#ff5a5f;border-radius:3px;color:#ffffff;display:inline-block;font-size:14px;font-weight:400;line-height:20px;padding:15px 0 15px 0;text-align:center;text-decoration:none;width:220px"
                                                                                                                                target="_blank"
                                                                                                                                >Click
                                                                                                                                Here
                                                                                                                                For
                                                                                                                                Details</a>

                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <table role="presentation"
                                                                                style=""
                                                                                class="m_-2176963290679972721mlContentTable"
                                                                                width="600" cellspacing="0" cellpadding="0"
                                                                                border="0" align="center">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="m_-2176963290679972721spacingHeight-30"
                                                                                            style="line-height:30px;min-height:30px"
                                                                                            height="30"></td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
    </html>';
    return $html;
}
//git check
// data send to zapier
function ZapierSender($array_data = null, $agentId = null)
{
    $query = Websetting::select('WebhookUrl', 'ZapierToken', 'ZapierSID');
    if ($agentId != null) {
        $query = $query->where('AdminId', $agentId);
    }
    $query = $query->first();
    if ($query) {
        if ($query->WebhookUrl !== '') {
            $url = $query->WebhookUrl;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $array_data);
            $result = curl_exec($ch);
            curl_close($ch);
            $res = json_decode($result);
            if($res){
                if ($res->status == "success") {
                    return $array_data;
                }
            }
        }
    }
}
function getFiltersData($field)
{
    $query = RetsPropertyData::query();
    $result = array();
    $query->select($field);
    $query->where($field, "<>", "");
    $query->distinct($field);
    $query->orderBy($field, 'asc');
    return $query->pluck($field)->toArray();
}


function weduMapSearch($condition, $offset, $limit, $sortBy = 'ListPrice', $order = 'ASC', $textSearchField = '', $extra_custom_query = '', $extra_select = '', $shape = '', $array_data = array(), $orFilter = array(), $type = "", $isDefault = false, $isTotal = false)
{
    $isSoldSearch = false;
    $query = RetsPropertyData::query();
    if ($type == "main") {
        $flag = false;
        $query->select(
            PropertyConstants::SELECT_DATA
        );
    } elseif ($type == "map") {
        $flag = true;
        $query->select(
            PropertyConstants::MAP_MARKERS_SELECT_DATA
        );
    } else {
        $flag = false;
        $query->select(
            PropertyConstants::MAP_SELECT_DATA
        );
    }

    //$query->where("ImageUrl", "!=", "");
    if (isset($condition['beds'])) {
        $query->where('BedroomsTotal', '>=', (int)$condition['beds']);
        unset($condition['beds']);
    }
    if (isset($condition['baths'])) {
        $query->where('BathroomsFull', '>=', (int)$condition['baths']);
        unset($condition['baths']);
    }

    if (isset($condition['PropertyType'])) {
        $query->where('PropertyType', $condition['PropertyType']);
    }
    //
    if (isset($condition['Dom'])) {
        $query->where('Dom', '<=', (float)$condition['Dom']);
    }

    if (isset($condition['price_min'])) {
        $query->where('ListPrice', '>=', (int)$condition['price_min']);
    }
    if (isset($condition['price_max'])) {
        $query->where('ListPrice', '<=', (int)$condition['price_max']);
    }
    if (isset($condition['status'])) {
        $query->where('PropertyStatus', $condition['status']);
    }
    if (isset($condition['soldStatus'])) {
        $query->where('Status', $condition['soldStatus']);
        unset($condition['soldStatus']);
    }

    if (isset($condition['Sqft'])) {
        if (str_contains($condition['Sqft'], '-')) {
            $exp = explode("-", $condition['Sqft']);
            $query->where('SqftMin', '>=', $exp[0]);
            $query->where('SqftMax', '<=', $exp[1]);
        }
        unset($condition['Sqft']);
    }
    if (isset($condition['PropertySubType'])) {
        if (is_array($condition['PropertySubType'])) {
            if (count($condition['PropertySubType'])) {
                $query->whereIn('PropertySubType', $condition['PropertySubType']);
            }
        } else {
            $query->where('PropertySubType', $condition['PropertySubType']);
        }
    }
    if (isset($condition['features'])) {
        $ids = PropertyFeatures::whereIn("FeaturesId", $condition['features'])->groupBy("PropertyId")->pluck("PropertyId")->toArray();
        if (is_array($ids) && count($ids)) {
            $query->whereIn('ListingId', $ids);
        }
    }
    if (isset($condition['basement'])) {
        if (count($condition['basement'])) {
            $query->whereIn('Bsmt1_out', $condition['basement']);
        }
    }
    //
    unset($condition['Sqft']);
    unset($condition['features']);
    unset($condition['basement']);
    unset($condition['PropertySubType']);
    unset($condition['radius']);
    unset($condition['curr_path']);
    unset($condition['curr_path_query']);
    unset($condition['center_lat']);
    unset($condition['center_lng']);
    unset($condition['shape']);
    unset($condition['multiplePropType']);
    unset($condition['openhouse']);
    unset($condition['price_min']);
    unset($condition['Dom']);
    unset($condition['PropertyType']);
    unset($condition['status']);
    unset($condition['price_min']);
    unset($condition['price_max']);
    unset($condition['beds']);
    unset($condition['baths']);

    if (isset($condition['Gar'])) {
        $query->where('Gar', $condition['Gar']);
        unset($condition['Gar']);
    }
    if (isset($condition['Park_spcs'])) {
        $query->where('Park_spcs', $condition['Park_spcs']);
        unset($condition['Park_spcs']);
    }
    if (isset($condition['Pool'])) {
        $query->where('Pool', '!=', $condition['Pool']);
        unset($condition['Pool']);
    }
    if (!empty($array_data)) {
        foreach ($array_data as $key => $value) {
            $query->whereIn($key, $value);
        }
    }
    if (!empty($orFilter)) {
        $query->orWhere($orFilter);
    }
    if ($textSearchField) {
        if (isset($condition['group'])) {
            $query->where($condition['group'], "$textSearchField");
            // if ($condition['group'] === "StandardAddress") {
            //     $query->where($condition['group'], 'like', "$textSearchField" . '%');
            // } else {
            // }
        }
        // $query->where(function ($q) use ($textSearchField) {
        //     $q->orWhere('StandardAddress', 'like', "$textSearchField" . '%');
        //     $q->orWhere('Community', "$textSearchField");
        //     $q->orWhere('City', "$textSearchField");
        //     $q->orWhere('ListingId', "$textSearchField");
        // $q->orWhere('Municipality', 'like', "$textSearchField" . '%');
        // $q->orWhere('County', "$textSearchField");
        // });
    }
    if ($textSearchField == trim($textSearchField) && str_contains($textSearchField, ' ')) {
        if (strlen($textSearchField) >= 5) {
            $isSoldSearch = true;
            $PurgedTable = RetsPropertyDataPurged::query();
            if ($type == "main") {
                $PurgedTable->select(
                    PropertyConstants::SELECT_DATA
                );
            } elseif ($type == "map") {
                $PurgedTable->select(
                    PropertyConstants::MAP_MARKERS_SELECT_DATA
                );
            } else {
                $PurgedTable->select(
                    PropertyConstants::MAP_SELECT_DATA
                );
            }
            if (isset($condition['group'])) {
                // if ($condition['group'] === "StandardAddress") {
                //     $PurgedTable->where($condition['group'], 'like', "$textSearchField" . '%');
                // } else {
                // }
                $PurgedTable->where($condition['group'], "$textSearchField");
            }
            // $PurgedTable->orWhere('StandardAddress', 'like', "$textSearchField" . '%');
            // $PurgedTable->orWhere('ListingId', "$textSearchField");
            // $PurgedTable->orWhere('City', "$textSearchField");
            // $PurgedTable->orWhere('Community', "$textSearchField");
            // $PurgedTable->orWhere('County', "$textSearchField");
            // $PurgedTable->orWhere('Municipality', 'like', "$textSearchField" . '%');
        }
    } else {
        $query->where("Status", "A");
    }
    unset($condition['group']);
    if (!empty($condition)) {
        $query->where($condition);
    }
    // todo:: this is temporary where purged data is not done
    $query->whereNotNull('Latitude');
    $query->whereNotNull('Longitude');
    $total = $total_temp = 0;
    //$total = $query->count();//
    if ($isDefault) {
        //$total = $query->count();
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
                //$total = $query->count();//
            } else {
                $query->whereRaw($extra_custom_query);
                //$total = $query->count(); //
            }
        }
        $total_temp = $query->count();
        $total = RetsPropertyData::count();
    } else {
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
            } else {
                $query->whereRaw($extra_custom_query);
            }
        }
        if ($isSoldSearch) {
            $toget = $PurgedTable;
            if ($isTotal) {
                //$toget = $toget->union($query);
                $total = $toget->get();
                $total = collect($total)->count();
            }
            //$total = $PurgedTable->union($query)->count();
        } else {
            if ($isTotal) {
                $total = $query->count();
            }
        }
    }
    $query->orderBy($sortBy, $order);
    $query->skip($offset);
    $query->take($limit);
    DB::enableQueryLog();
    //$result = $query->get();
    if ($isSoldSearch) {
        $result = $PurgedTable->union($query)->get();
        $total = collect($result)->count();
    } else {
        $result = $query->get();
    }
    $queryLog = DB::getQueryLog();
    if ($result && count($result) > 0) {
        $final_result = array(
            "result" => $result,
            "total" => $total,
            "total_temp" => $total_temp,
            "query" => $queryLog
        );
        return $final_result;
    } else {
        $final_result = array(
            "result" => false,
            "total" => $total,
            "total_temp" => $total_temp,
            "query" => $queryLog
        );
        return $final_result;
    }
}
