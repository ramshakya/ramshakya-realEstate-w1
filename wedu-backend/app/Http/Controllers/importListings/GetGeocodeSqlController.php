<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetGeocodeSqlController extends Controller
{
    public function makeGeoCodeValue()
    {
        $properties_data = DB::table("RetsPropertyData")->get();
        echo "\n Count Properties = " . count($properties_data);
        $prop_count = 0;
        foreach ($properties_data as $property_data) {
            $prop_count++;
            $property_data["geocode"] = "";
            $property_data["geocodeTried"] = 0;
            DB::table("RetsPropertyData")->where("ListingId", $property_data["ListingId"])->update(["geocode" => "", "geocodeTried" => 0]);
            echo "\n Property Updated For ListingId = " . $property_data["ListingId"] . " - Total Count = " . $prop_count;
        }
        // resi
        $properties_data = DB::table("RetsPropertyDataResi")->get();
        echo "\n Count Properties = " . count($properties_data);
        $prop_count = 0;
        foreach ($properties_data as $property_data) {
            $prop_count++;
            $property_data["geocode"] = "";
            $property_data["geocodeTried"] = 0;
            DB::table("RetsPropertyDataResi")->where("Ml_num", $property_data["Ml_num"])->update(["geocode" => "", "geocodeTried" => 0]);
            echo "\n Property resi Updated For ListingId = " . $property_data["Ml_num"] . " - Total Count = " . $prop_count;
        }
        //comm
        $properties_data = DB::table("RetsPropertyDataComm")->get();
        echo "\n Count Properties = " . count($properties_data);
        $prop_count = 0;
        foreach ($properties_data as $property_data) {
            $prop_count++;
            $property_data["geocode"] = "";
            $property_data["geocodeTried"] = 0;
            DB::table("RetsPropertyDataComm")->where("Ml_num", $property_data["Ml_num"])->update(["geocode" => "", "geocodeTried" => 0]);
            echo "\n Property comm Updated For ListingId = " . $property_data["Ml_num"] . " - Total Count = " . $prop_count;
        }
        //condo
        $properties_data = DB::table("RetsPropertyDataCondo")->get();
        echo "\n Count Properties = " . count($properties_data);
        $prop_count = 0;
        foreach ($properties_data as $property_data) {
            $prop_count++;
            $property_data["geocode"] = "";
            $property_data["geocodeTried"] = 0;
            DB::table("RetsPropertyDataCondo")->where("Ml_num", $property_data["Ml_num"])->update(["geocode" => "", "geocodeTried" => 0]);
            echo "\n Property condo Updated For ListingId = " . $property_data["Ml_num"] . " - Total Count = " . $prop_count;
        }
    }

    public function index()
    {
        echo "\n Cron started for getting geocode from mapbox";
        $file_details = ["RetsPropertyDataResi", "RetsPropertyDataComm", "RetsPropertyDataCondo"];
        //$file_details = ["rets_property_data_condo"];
        foreach ($file_details as $file_detail) {
            echo "\n Property Details = " . $file_detail;
            Log::info("Property Details = " . $file_detail);
            $properties_data = DB::table($file_detail)->where("geocode", "=", "")->orWhereNull("geocode")->where("geocodeTried", "<=", 0)->limit(2000)->get();
            echo "\n Count Properties = " . count($properties_data);
            $prop_count = 0;
            foreach ($properties_data as $record) {
                $record = collect($record)->all();
                $prop_count++;
                $custom_address = '';
                $custom_address .= isset($record['St_num']) ? $record['St_num'] . ' ' : '';
                $custom_address .= isset($record['St_dir']) ? $record['St_dir'] . ' ' : '';
                $custom_address .= isset($record['St']) ? $record['St'] . ' ' : '';
                $custom_address .= isset($record['St_sfx']) ? $record['St_sfx'] . ' ' : '';
                $custom_address .= isset($record['Unit']) ? $record['Unit'] . ' ' : '';
                $property_address = isset($record['Addr']) ? $record['Addr'] : $custom_address;
                $unitno = isset($record['Apt_num']) ? $record['Apt_num'] : '';
                $zip = isset($record['Zip']) ? $record['Zip'] : '';
                if (trim($unitno) != '') {
                    $property_address = $property_address . ' #' . $unitno;
                }
                //$property_address = $property_address.' #'.$unitno ;
                $property_address = preg_replace('/\s /', '-', $property_address);
                $property_address = preg_replace('/\s+/', '-', $property_address);
                $property_address = preg_replace('/\#+/', '#', $property_address);
                $property_address = trim($property_address);
                $property_address = str_ireplace("-", " ", $property_address);
                $property_address = preg_replace('/\s+/', ' ', $property_address);
                $property_address = preg_replace('/\#+/', '#', $property_address);
                $property_address = preg_replace('/\-+/', ' ', $property_address);
                $prop_address = $property_address . ', ' . $record['Municipality'] . ', ' . $record['County'] . ' ' . $zip;
                $prop_address = preg_replace('/\s+/', ' ', $prop_address);
                $prop_address = preg_replace('/\,/', '', $prop_address);
                $address = urlencode($prop_address . " CA");
                $string = str_replace(" ", "+", urlencode($address));
                echo "\n string = ." . $string . "For Address = " . $record["Addr"] . " - For Mls No = " . $record["Ml_num"];
                $geourl = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . $string . ".json?access_token=".env('MAPBOXAPI');
                $geocode = file_get_contents($geourl);
                $output = json_decode($geocode);
                //error_log( " ". $address ."<br>".serialize($output) );
                if (!empty($output->features[0]->center)) {
                    $lat = $output->features[0]->center[1];
                    $lng = $output->features[0]->center[0];
                    $lat = round($lat, 17);
                    $lng = round($lng, 17);
                    /*echo "<br> Lat " . $lat . "    Lng " . $lng;*/
                    $record["Latitude"] = $lat;
                    $record["Longitude"] = $lng;
                    DB::table($file_detail)->where("Ml_num", $record["Ml_num"])->update(["geocode" => 1, "geocodeTried" => $record["geocodeTried"] + 1, "Latitude" => $lat, "Longitude" => $lng]);
                    DB::table("RetsPropertyData")->where("ListingId", $record["Ml_num"])->update(["Latitude" => $lat, "Longitude" => $lng]);
                    echo "\n property updated for listingId = " . $record["Ml_num"] . " - Total Count = " . $prop_count;
                    Log::info("Property updated for listingId = " . $record["Ml_num"] . " - Total Count = " . $prop_count);
                } else {
                    // geocode tried code here
                    DB::table($file_detail)->where("Ml_num", $record["Ml_num"])->update(["geocode" => 0, "geocodeTried" => $record["geocodeTried"] + 1]);
                    echo "\n property Not Updated for listingId = " . $record["Ml_num"] . " - Total Count = " . $prop_count;
                    Log::info("Property Not Updated for listingId = " . $record["Ml_num"] . " - Total Count = " . $prop_count);
                }
            }
        }
    }


}
