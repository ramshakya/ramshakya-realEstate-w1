<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\agent\AlertsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateXMLFile;
use App\Models\PropertyAddressData;
use App\Models\SqlModel\FeaturesMaster;
use App\Models\SqlModel\PropertyFeatures;
use Illuminate\Support\Facades\DB;
use App\lib\phrets;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Schema;


class ListingsControllerTestVOW extends Controller
{
    //
    public $mls_config;
    public $images_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        //$this->cron_log_model = $cron_log_model;
        date_default_timezone_set("Canada/Central");
    }

    public function importPropertyListing()
    {
        $select_data = "Select ListingId from RetsPropertyData where Reimport = 0 and PropertyType = 'Condos'";
        $properties_data = DB::select($select_data);
        $total_property_count = count($properties_data);
        echo "\n total count = ".$total_property_count;
        foreach ($properties_data as $property) {
            $property = collect($property)->all();
            $req = ["ListingId" => $property["ListingId"],"type" => ""];
            $req = json_encode($req);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://admin.housen.ca/api/testStats',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$req,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);
            $response = collect($response)->all();
            echo "\n listingsId = ".$response["ListingId"];
            echo "\n matched status = ".$response["isRpd"];
            $upd_query = "UPDATE RetsPropertyData set needDelete = ".$response["isRpd"]." where ListingId = '".$property['ListingId']."'";
            //$upd_query_sq = "UPDATE RetsPropertyDataResi set needDelete = ".$response["isRpd"]."where Ml_num = '".$property['ListingId']."'";
            echo "\n updated";
            DB::update($upd_query);
            //DB::update($upd_query_sq);
            echo "\n property left = ".$total_property_count--;
        }

        dd(1);



	$rets_prop_mapping = [
            "Disp_addr" => "Disp_addr",
            "Dom" => "Dom",
            "Timestamp_sql" => "Timestamp_sql",
            "Area" => "Area",
            "Community" => "Community",
            "Municipality_code" => "Municipality_code",
            "A_c" => "A_c",
            "Prop_feat4_out" => "Prop_feat4_out",
            "Prop_feat5_out" => "Prop_feat5_out",
            "Prop_feat6_out" => "Prop_feat6_out",
            "Prop_feat3_out" => "Prop_feat3_out",
            "Ad_text" => "Ad_text",
            "StandardAddress" => "Addr",
            "All_inc" => "All_inc",
            "County" => "County",
            "Cross_st" => "Cross_st",
            "Elevator" => "Elevator",
            "Extras" => "Extras",
            "Fuel" => "Fuel",
            "Furnished" => "Furnished",
            "Gar" => "Gar",
            "Gar_type" => "Gar_type",
            "Heat_inc" => "Heat_inc",
            "Heating" => "Heating",
            "Laundry" => "Laundry",
            "Laundry_lev" => "Laundry_lev",
            "Orig_dol" => "Orig_dol",
            "Pets" => "Pets",
            "Prkg_inc" => "Prkg_inc",
            "Prop_feat1_out" => "Prop_feat1_out",
            "Prop_feat2_out" => "Prop_feat2_out",
            "Park_spcs" => "Park_spcs",
            "Rltr" => "Rltr",
            "S_r" => "S_r",
            "Sp_dol" => "Sp_dol",
            "Sqft" => "Sqft",
            "St" => "St",
            "St_dir" => "St_dir",
            "Stories" => "Stories",
            "Tour_url" => "Tour_url",
            "Community_code" => "Community_code",
            "Area_code" => "Area_code",
            "Type_own1_out" => "Type_own1_out",
            "Municipality" => "Municipality",
            "Zip" => "Zip",
            "BedroomsTotal" => "Br",
            "Bsmt1_out" => "Bsmt1_out",
            "Bsmt2_out" => "Bsmt2_out",
            "ListPrice" => "Lp_dol",
            "ListingId" => "Ml_num",
            "Status" => "Status",
            "Pool" => "Pool",
            "BathroomsFull" => "Bath_tot"
        ];
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "ListingsControllerVOW";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        $initial_timing = date('Y-m-d H:i:s');
        // For all MLSs - Starts
        $txt = "<table>";
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            if ($curr_mls_id != 1) continue;
            $curr_mls_name = $curr_mls['mls_name'];
            echo "\n Started for " . $curr_mls_name;
            //echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $photos_field = config('mls_config.rets_query_array.' . $curr_mls_id . '.photos_field');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            $is_run = 0;
            $batables = array();
            foreach ($property_class as $className => $classconfig) {
                //if ($className == "ResidentialProperty") continue;
                //if ($className == "CommercialProperty") continue;
                //if ($className == "CondoProperty") continue;
                $is_run++;
                $curr_date = date('Y-m-d H:i:s');
                $properties_inserted_in_db = 0;
                $insertion_prop = 0;
                $update_prop = 0;
                $rets = new phrets;
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterVOW');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                if ($className != "CommercialProperty") {
                    $room_mapping = $classconfig['room_data_mapping'][$className];
                } else {
                    $room_mapping = [];
                }
                $login_url = $login_parameter['rets_login_url'];
                $mls_username = $login_parameter['rets_username'];
                $mls_password = $login_parameter['rets_password'];
                $rets_version = $login_parameter['RETS-Version'];
                $user_agent = $login_parameter['User-Agent'];
                $rets->AddHeader("Accept", "*");
                $rets->AddHeader("RETS-Version", $rets_version);
                $rets->AddHeader("User-Agent", $user_agent);
                $rets->SetParam('compression_enabled', true);
                $rets->SetParam("offset_support", true);
                // make first connection
                $connect = $rets->Connect($login_url, $mls_username, $mls_password);
                // $connect = "";
                if ($connect) {
                    echo "\n login username = " . $mls_username;
                    echo "\n Connected.....";
                }
                if (!$connect) {
                    $error_details = $rets->Error();
                    $error_text = strip_tags($error_details['text']);
                    $error_type = strtoupper($error_details['type']);
                    echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                }

                $curr_date_end_time = $curr_date;
                $cron_data = array(
                    'cron_file_name' => $file_name,
                    'cron_start_time' => $curr_date,
                    'property_class' => $className,
                    'mls_no' => $curr_mls_id,
                    'steps_completed' => 1
                );
                // inserting the cron start time in property data
                $this_cron_log_id = DB::table($cron_tablename)->insertGetId($cron_data);
                $timestamp_field = $classconfig['timestamp_field'];
                $status_field = $classconfig['status_field'];
                $status_field_val = $classconfig['status_field_val'];
                $key_field = $classconfig['key_field'];
                //$property_data_mapping = $classconfig['property_data_mapping'];
                $class_type = (isset($classconfig['class_type']) && !empty($classconfig['class_type'])) ? ',' . $classconfig['class_type'] : '';
                $complete_pull = false;
                $property_query_start_time = get_start_time_for_cron($cron_tablename, $file_name, $curr_mls_id, $className);
                if (isset($property_query_start_time) && $property_query_start_time['status'] == 'start') {
                    $complete_pull = true;
                }
                $date_time = explode(' ', $property_query_start_time['time']);
                $property_query_start_time = $date_time[0] . 'T' . $date_time[1];
                $date_query = "($timestamp_field=$property_query_start_time+)";

                $curr_timestamp = time();
                $old_timestamp = strtotime($property_query_start_time);
                $diff = ($curr_timestamp - $old_timestamp);
                $new_starttime = 0;

                if ($diff > 3600 * 24 * 1) {
                    $new_starttime = strtotime("+1 day", $old_timestamp);
                }
                if ($new_starttime > 0 && $new_starttime < time()) {
                    $time_range1 = $property_query_start_time;
                    $time_range2 = date('Y-m-d H:i:s', $new_starttime);
                    $curr_date_end_time = $time_range2;
                    $date_time2 = explode(' ', $time_range2);
                    $datetime_range2 = $date_time2[0] . 'T' . $date_time2[1];
                    $date_query = "($timestamp_field=$time_range1-$datetime_range2)";
                    $property_end_time = $time_range2;
                } else {
                    $property_end_time = $curr_date;
                    $date_query = "($timestamp_field=$property_query_start_time+)";
                }
                echo "\n Class : " . $className;
                $last_date = $property_query_start_time;
                $rets_cond = "(($status_field=$status_field_val))";
                //$rets_query = "((Status=|A)," . $date_query . ")";
                $rets_query = "(Ml_num=W5629381)";
                //$rets_query = "((Timestamp_sql=2022-05-11T05:40:03+))";
                //$rets_query = "((Status=|A),(Timestamp_sql=2022-04-06T05:40:03+))";
                //$rets_query = "((Status=|A))";
                echo "\n rets query = " . $rets_query;
                $cron_update_data = array(
                    'properties_download_start_time' => $property_query_start_time,
                    'steps_completed' => 2,
                    'rets_query' => $rets_query
                );
                $cron_cond = array(
                    'id' => $this_cron_log_id
                );
                $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                $rets_metadata = $rets->GetMetadata($property_resource, $className);
                echo "\n rets query = " . $rets_query;
                $search = $rets->SearchQuery($property_resource, $className, $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                $total_property_count = $rets->TotalRecordsFound($search);
                echo "\n total property count = $total_property_count";
                echo "\n Class : = $className and total count = $total_property_count";
                continue;
                if ($total_property_count > 0) {
                    echo "\n Count :: $total_property_count";
                    // Update total property found from MLSu
                    $cron_update_data = array(
                        'properties_count_from_mls' => $total_property_count,
                        'steps_completed' => 3
                    );
                    $cron_cond = array('id' => $this_cron_log_id);
                    $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                    $limit = 100;
                    $start_index = 1;
                    $new_sleep = 0;
                    $lcp = 0;
                    // for ($lcp = 0; $lcp <= $lc; $lcp++) {
                    $new_sleep++;
                    $offset = $start_index + $lcp * $limit;
                    if ($new_sleep == 10) {
                        $new_sleep = 0;
                        sleep(10);
                    }
                    echo "\n --" . $offset . " Limit " . $limit . "\n";
                    $search_chunks = $rets->Searchquery($property_resource, $className, $rets_query, array('Format' => 'COMPACT-DECODED', 'Count' => 1, "UsePost" => 1));
                    //  $txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        while ($record = $rets->FetchRow($search_chunks)) {
                            dd($record);
                            $properties_inserted_in_db++;
                            //echo $record[$mls_key];
                            echo "\n Properties db" . $properties_inserted_in_db;
                            //continue;
                            $sl++;
                            echo "\n sleep value";
                            if ($sl == 1000) {
                                $sl = 0;
                                echo "\n In sleep";
                                sleep(5);
                            }
                            $property_data = array();
                            $rets_property_data = array();
                            $addressData = array();
                            $main_data = array();
                            foreach ($property_data_mapping as $db_key => $mls_key) {
//                                if (in_array($mls_key, $rets_prop_mapping)) {
//                                    $rets_property_data[$mls_key] = $record[$mls_key];
//                                }
                                if (isset($record[$mls_key]))
                                    $property_data[$db_key] = $record[$mls_key];
                                else
                                    $property_data[$db_key] = '';
                            }
                            foreach ($rets_prop_mapping as $db_key => $mls_key) {
                                if (isset($record[$mls_key]))
                                    $rets_property_data[$db_key] = $record[$mls_key];
                                else
                                    $rets_property_data[$db_key] = '';
                            }
                            $room_data = [];
                            foreach ($room_mapping as $db_key => $mls_key) {
                                if (isset($record[$mls_key]))
                                    $room_data[$db_key] = $record[$mls_key];
                                else
                                    $room_data[$db_key] = '';
                            }
                            if ($className != "CommercialProperty") {
                                $property_data["RoomsDescription"] = json_encode($room_data);
                            }
                            $rets_property_data['mls_no'] = $curr_mls_id;
                            $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->get();
                            $property_data['property_last_updated'] = date('Y-m-d H:i:s');
                            $property_data["SlugUrl"] = get_slug_url($property_data);
                            //echo "\n property_table_name  =>  $property_table_name";
                            if (count($rets_data) > 0) {
                                $property_data["Reimport"] = 1;
                                $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->update($property_data);
                                $update_prop++;
                                $rets_property_data["updated_time"] = date("Y-m-d H:i:s");
                                $rets_property_data["Reimport"] = 1;
                                $rpd_operation = "<span style='color:green;'>Update</span>";
                                echo "in update rets = " . $record[$key_field];
                            } else {
                                $insertion_prop++;
                                $property_data['property_insert_time'] = date('Y-m-d H:i:s');
                                $property_data["image_downloaded"] = 0;
                                $property_data["image_download_tried"] = 0;
                                $property_data["Reimport"] = 1;
                                DB::table($property_table_name)->insertGetId($property_data);
                                $rets_property_data["inserted_time"] = date("Y-m-d H:i:s");
                                $rets_property_data["updated_time"] = date("Y-m-d H:i:s");
                                $rpd_operation = "<span style='color:red;'>Insert</span>";
                                echo "in insert rets = " . $record[$key_field];
                            }
                            if ($className == "ResidentialProperty") {
                                $rets_property_data["PropertyType"] = "Residential";
                            }
                            if ($className == "CommercialProperty") {
                                $rets_property_data["PropertyType"] = "Commercial";
                            }
                            if ($className == "CondoProperty") {
                                $rets_property_data["PropertyType"] = "Condos";
                            }
                            $rets_property_data["SlugUrl"] = $property_data["SlugUrl"];
                            $rets_property_data["ShortPrice"] = number_format_short($property_data['Lp_dol']);
                            $rets_property_data["PropertySubType"] = $property_data['Type_own1_out'];
                            $rets_property_data["PropertyStatus"] = $property_data["S_r"];
                            $rets_property_data["City"] = str_replace("'", '', $property_data["Municipality"]);
                            $rets_property_data["ListPrice"] = $property_data["Lp_dol"];
                            $rets_property_data["Reimport"] = 1;
                            $get_sqft = getsqft_min_max($property_data);
                            if (count($get_sqft) > 0) {
                                $rets_property_data["SqftMin"] = $get_sqft["SqftMin"];
                                $rets_property_data["SqftMax"] = $get_sqft["SqftMax"];
                                $rets_property_data["sqftFlag"] = $get_sqft["sqftFlag"];
                            }
                            $result = RetsPropertyData::updateOrCreate(["ListingId" => $record[$key_field]], $rets_property_data);
                            $addressData["StandardAddress"] = $rets_property_data["StandardAddress"];
                            $addressData["ZipCode"] = $rets_property_data["Zip"];
                            $addressData["City"] = $rets_property_data["Municipality"];
                            $addressData["Area"] = $rets_property_data["Area"];
                            $addressData["County"] = $rets_property_data["County"];
                            $addressData["Status"] = $rets_property_data["Status"];
                            $addressData["Community"] = $rets_property_data["Community"];
                            $addressResult = PropertyAddressData::updateOrCreate(['ListingId'=>$rets_property_data["ListingId"]],$addressData);
                            echo "\n property inserted" . $properties_inserted_in_db;
                            $batables[] = array(
                                'addressfullhere' => $record["Addr"],
                                'listingId' => $record["Ml_num"],
                                'operation' => $rpd_operation,
                                'price' => $record["Lp_dol"],
                                'status' => $record["Status"],
                                'propertyType' => $rets_property_data["PropertyType"]
                            );
                        }
                        $cron_update_data = array(
                            'cron_end_time' => date('Y-m-d H:i:s'),
                            'steps_completed' => 4,
                            'properties_count_actual_downloaded' => $properties_inserted_in_db,
                            'property_inserted' => $insertion_prop,
                            'property_updated' => $update_prop,
                            'properties_download_end_time' => $curr_date_end_time,
                            'success' => 1
                        );
                        // update the cron table
                        $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                        //echo "<br>";
                        echo "\n total_property_fetched :: $properties_inserted_in_db";
                        echo "\n total_property_inserted :: $insertion_prop";
                        echo "\n todal_property_updated :: $update_prop";
                    }
                    // }
                } else {
                    $cron_update_data = array(
                        'cron_end_time' => date('Y-m-d H:i:s'),
                        'steps_completed' => 4,
                        'properties_count_actual_downloaded' => $properties_inserted_in_db,
                        'property_inserted' => $insertion_prop,
                        'property_updated' => $update_prop,
                        'properties_download_end_time' => $curr_date_end_time,
                        'success' => 1
                    );
                    // update the cron table
                    $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                    //echo "<br>";
                    echo "\n total_property_fetched :: $properties_inserted_in_db";
                    echo "\n total_property_inserted :: $insertion_prop";
                    echo "\n todal_property_updated :: $update_prop";
                    echo "no data...";
                }
            }
            if (count($batables) > 0) {
                $txt .= "<table border=1>";
                $bacounter = 0;
                $txt .= "<thead>
   <tr>
      <th>Sr. No</th>
      <th>ListingId</th>
      <th>Address</th>
      <th>Price</th>
      <th>Status</th>
      <th>PropertyType</th>
      <th>Database Operation</th>
   </tr>
</thead>";
                foreach ($batables as $bagent) {
                    $bacounter++;
                    $txt .= "<tr>";
                    $txt .= "<td>" . $bacounter . "</td>";
                    $txt .= "<td>" . $bagent['listingId'] . "</td>";
                    $txt .= "<td>" . $bagent['addressfullhere'] . "</td>";
                    $txt .= "<td>" . $bagent['price'] . "</td>";
                    $txt .= "<td>" . $bagent['status'] . "</td>";
                    $txt .= "<td>" . $bagent['propertyType'] . "</td>";
                    $txt .= "<td>" . $bagent['operation'] . "</td>";
                    $txt .= "</tr>";
                }
                $txt .= "</table>";
                $curr_date_end = date('Y-m-d H:i:s');
                $superAdminEmail = getSuperAdmin();
                sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), 'Vow Listing Import Cron FINISHED - ' . $curr_date_end, $txt, "Listings controller ImportListing", "", env('RETSEMAILS'));
            }
        }
        /*$call_thum = new ImagesControllerVOW();
        // to get thumbnailImages
        $call_thum->getThumbnail();
        // to get Geo Location
        $get_geo = new GetGeocodeSqlController();
        $get_geo->index();
        // to send alerts
        $todayDate = new \DateTime(); // For today/now, don't pass an arg.
        $todayDate->sub(new \DateInterval('PT3H55M10S'));
        $desiredDate = $todayDate->format("Y-m-d H:i:s");
        sendAlerts([], 1, $desiredDate);
        // need to update sitemap
        $sitemap = new GenerateXMLFile();
        $sitemap->generatePropertyXml();
        $sitemap->generateBlogXml();
        $sitemap->generateCityXml();
        // execute frontend commands
        $commands = "./frontendRestartScript.sh";
        shell_exec($commands);
        // this is for property features
        $this->filterPropertyFeatured($initial_timing);*/
    }



    public function filterPropertyFeatured($updated_time)
    {
        //$query = "SELECT ListingId,Extras,Prop_feat1_out,Prop_feat2_out,Prop_feat4_out,Prop_feat5_out,Prop_feat6_out,A_c,Fuel,Heating,Laundry,Pool from RetsPropertyData Where Status = 'A'";
        $query = "SELECT ListingId,Extras,Prop_feat1_out,Prop_feat2_out,Prop_feat4_out,Prop_feat5_out,Prop_feat6_out,A_c,Fuel,Heating,Laundry,Pool from RetsPropertyData Where Status = 'A' and updated_time >= '$updated_time'";

        $prev_featured_query = "SELECT id,Features from FeaturesMaster";
        $prev_featured = DB::select($prev_featured_query);
        $data = DB::select($query);
        $temp_data = [];
        $temp_property = [];
        echo "\n total count properties for update features = " . collect($data)->count();
        foreach ($data as $value) {
            $value = collect($value)->all();
            $extras = explode(',', $value["Extras"]);
            if ($extras !== []) {
                foreach ($extras as $extra) {
                    $temp_data[] = $extra;
                }
            }
            if ($value["Prop_feat1_out"] !== "") {
                $temp_data[] = $value["Prop_feat1_out"];
            }
            if ($value["Prop_feat2_out"] !== "") {
                $temp_data[] = $value["Prop_feat2_out"];
            }
            if ($value["Prop_feat4_out"] !== "") {
                $temp_data[] = $value["Prop_feat4_out"];
            }
            if ($value["Prop_feat6_out"] !== "") {
                $temp_data[] = $value["Prop_feat6_out"];
            }
            if ($value["A_c"] !== "") {
                $temp_data[] = $value["A_c"];
            }
            if ($value["Fuel"] !== "") {
                $temp_data[] = $value["Fuel"];
            }
            if ($value["Heating"] !== "") {
                $temp_data[] = $value["Heating"];
            }
            if ($value["Laundry"] !== "") {
                $temp_data[] = $value["Laundry"];
            }
            if ($value["Pool"] !== "") {
                $temp_data[] = $value["Pool"];
            }
            foreach ($prev_featured as $prev) {
                if (in_array($prev->Features, $temp_data)) {
                    $temp_property = ["PropertyId" => $value["ListingId"], "FeaturesId" => $prev->id];
                    PropertyFeatures::updateOrCreate(
                        $temp_property,
                        $temp_property
                    );
                }
            }
            echo "\n value inserted = " . $value['ListingId'];
        }
    }

}

