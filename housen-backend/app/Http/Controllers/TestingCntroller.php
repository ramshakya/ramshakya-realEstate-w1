<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
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
use Carbon\Carbon;
use DateTime;
use App\Models\RetsPropertyDataImage;
use App\Models\SqlModel\lead\LeadsModel;

class TestingCntroller extends Controller
{
    //
 
    public function homeStats(Request $request)
    {
        $query = RetsPropertyData::query();
        $finalPriceData = [];
        $finalDateData = [];
        $finalData = [];
        $finalSoldData = [];
        $c_date = new DateTime();
        for ($mnth = 0; $mnth < 6; $mnth++) {
            $prices = array();
            $c_date->modify("-1 month");
            // $date = $c_date->format('Y-m-d H:i:s');
            $date = $c_date->format('Y-m-d ') . '00:00:00';
            $s_date = $c_date->format('Y-m');
            $response_data = $query->select('Sp_Dol', 'id', 'ListingId', 'updated_time', 'inserted_time')
                ->where('Sp_Dol', '>', '0')
                ->where('City', PropertyConstants::GTACITY)
                ->where('inserted_time', '<=', $date)
                ->orderBy('inserted_time', 'desc')->get();
            foreach ($response_data as $key => $value) {
                $prices[] = (int)round($value->Sp_Dol);
            }
            $finalSoldData[] = count($response_data);
            if (count($prices)) {
                // $obj = array(
                //     'price' => getMedian($prices),
                //     'date' => $s_date,
                // );
                // $finalData[] = $obj;
                $finalPriceData[] = getMedian($prices);
                $finalDateData[] = $s_date;
            } else {
                $finalDateData[] = $s_date;
                $finalPriceData[] = 0;
                // $obj = array(
                //     'price' => 0,
                //     'date' => $s_date,
                // );
            }
        }
        $finalData = array(
            "date" => $finalDateData,
            "price" => $finalPriceData,
            "sold" => $finalSoldData
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
    public function marketStatsFilterDataBck(Request $request)
    {
        $field = $request->key;
        $cityTemp = array();
        $communityTemp = array();
        $propertyTypeTemp = array();
        $cities = array();
        $community = array();
        $propertyType = array();
        $field = "City";
        $cityTemp = getFiltersData($field);
        $field = "Community";
        $communityTemp = getFiltersData($field);

        $field = "PropertyType";
        $propertyTypeTemp = getFiltersData($field);
        foreach ($propertyTypeTemp as $key => $prop) {
            $obj = array(
                'value' => $prop,
                'text' => $prop,
            );
            $propertyType[] = $obj;
        }
        foreach ($communityTemp as $key => $com) {
            $obj = array(
                'value' => $com,
                'text' => $com,
            );
            $community[] = $obj;
        }
        foreach ($cityTemp as $key => $city) {
            $obj = array(
                'value' => $city,
                'text' => $city,
            );
            $cities[] = $obj;
        }
        $result = array(
            'city' => $cities,
            'community' => $community,
            'propertyType' => $propertyType,
        );
        return response($result, 200);
    }

    function totalSold(Request $request)
    {
        $query = RetsPropertyData::query();
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
        $soldList = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city AND  Sp_dol > 0 GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        // "Status","A"
        $finalData = array(
            "totalSold" => $soldList
        );
        return response($finalData, 200);
    }

    function soldActive(Request $request)
    {
        $query = RetsPropertyData::query();
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
        $avgDom = array();
        $active = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city AND  Status = 'A'  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        $soldList = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city AND  Sp_dol > 0 GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        $finalPriceData = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, count(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        // "Status","A"
        $finalData = array(
            "newList" => $finalPriceData,
            "soldList" => $soldList,
            "activeList" => $active
        );
        return response($finalData, 200);
    }
    function domAvgMedian(Request $request)
    {
        $query = RetsPropertyData::query();
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
        $avgDom = array();
        $avgDom = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, SUM(Dom) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");
        $finalPriceData = DB::select("SELECT  MONTH(inserted_time) AS periodNumber, MONTHNAME(inserted_time ) AS period, YEAR(inserted_time) AS year, SUM(Sp_dol) as data FROM `RetsPropertyData` WHERE (inserted_time BETWEEN '$s_date' AND '$datePeriod') $propType $city  GROUP BY year , periodNumber ORDER BY year ASC , periodNumber DESC ");

        $finalData = array(
            "median" => $finalPriceData,
            "dom" => $avgDom
        );
        return response($finalData, 200);
    }
    public function GetPreferenceData(Request $request)
    {
        $data['PropertySubType'] = RetsPropertyData::select('PropertySubType')->distinct('PropertySubType')->get();
        $data['City'] = RetsPropertyData::select('City')->distinct('City')->get();
        $data['PropertyType'] = RetsPropertyData::select('PropertyType')->distinct('PropertyType')->get();
        return $data;
    }


    public function statsFilterData()
    {
    }


}
