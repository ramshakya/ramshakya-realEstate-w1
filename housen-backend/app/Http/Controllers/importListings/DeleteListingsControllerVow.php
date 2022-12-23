<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\agent\AlertsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateXMLFile;
use App\Models\PropertyAddressData;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCommPurged;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataCondoPurged;
use App\Models\RetsPropertyDataResi;
use App\Models\RetsPropertyDataResiPurged;
use App\Models\SqlModel\FeaturesMaster;
use App\Models\SqlModel\PropertyFeatures;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\DB;
use App\lib\phrets;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Schema;
use App\Models\RetsPropertyDataDelete;


class DeleteListingsControllerVow extends Controller
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
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "DeleteListingsController";
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
                $timestamp_field = "Deleted_timestamp"; //$classconfig['timestamp_field'];
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
                $rets_query = "(" . $date_query . ")";
                $rets_query = "(Deleted_timestamp=2020-08-27T05:40:03+)";
                //$rets_query = "(Deleted_timestamp=2022-08-26T00:00:28-2022-09-05T00:00:28)";
                //$rets_query = "(Deleted_timestamp=*)";
                //$rets_query = "((Status=|A))";
                //$rets_query = "(Ml_num=0+)";
                
                $todayDate = new \DateTime(); // For today/now, don't pass an arg.
                 $todayDate->modify("-10 day");
                 $desiredDate =  $todayDate->format("Y-m-d H:i:s");
                 $desiredDate = explode(' ', $desiredDate);
                 $date_query = $desiredDate[0] . 'T' . $desiredDate[1].'+';
                $rets_query = "(Deleted_timestamp=".$date_query.")";
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
                //$rets_metadata = $rets->GetMetadata($property_resource, $className);
                echo "\n rets query = " . $rets_query;
                $search = $rets->SearchQuery("Property", "DeletedProperty", $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                $total_property_count = $rets->TotalRecordsFound($search);
                echo "\n total property count = $total_property_count";
                echo "\n Class : = $className and total count = $total_property_count";
                //continue;
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
                    $search_chunks = $rets->Searchquery("Property", "DeletedProperty", $rets_query, array('Format' => 'COMPACT-DECODED', 'Count' => 1, "UsePost" => 1));
                    //  $txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        while ($record = $rets->FetchRow($search_chunks)) {
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
                            $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->get();
                            $rpd_data = DB::table('RetsPropertyData')->where("ListingId", $record[$key_field])->get();
                            //echo "\n property_table_name  =>  $property_table_name";
                            if (count($rets_data) > 0) {
                                if ($property_table_name == "RetsPropertyDataResi") {
                                    $rets_data["Status"] = "D";
                                    /*RetsPropertyDataResiPurged::updateOrCreate(["Ml_num"=>$rets_data["Ml_num"]],$rets_data);
                                    $retsPropDataResiQuery = "DELETE from RetsPropertyDataResi where Ml_num='".$rets_data['Ml_num']."';";
                                    $retsPropDataQuery = "DELETE from RetsPropertyData where ListingId='".$rets_data['Ml_num']."';";
                                    DB::delete($retsPropDataResiQuery);
                                    $rpd_data["Status"] = "D";
                                    RetsPropertyDataPurged::updateOrCreate(["ListingId"=>$rets_data],$rpd_data);
                                    DB::delete($retsPropDataQuery);*/
                                    RetsPropertyData::where("ListingId",$record[$key_field])->update(["Status"=>"D","updated_time" => date("Y-m-d H:i:s")]);
                                    RetsPropertyDataResi::where("Ml_num",$record[$key_field])->update(["Status"=>"D","property_last_updated" => date("Y-m-d H:i:s")]);
                                }
                                if ($property_table_name == "RetsPropertyDataComm") {
                                    /*$rets_data["Status"] = "D";
                                    RetsPropertyDataCommPurged::updateOrCreate(["Ml_num"=>$rets_data["Ml_num"]],$rets_data);
                                    $retsPropDataCommQuery = "DELETE from RetsPropertyDataComm where Ml_num='".$rets_data['Ml_num']."';";
                                    $retsPropDataQuery = "DELETE from RetsPropertyData where ListingId='".$rets_data['Ml_num']."';";
                                    DB::delete($retsPropDataCommQuery);
                                    $rpd_data["Status"] = "D";
                                    RetsPropertyDataPurged::updateOrCreate(["ListingId"=>$rets_data],$rpd_data);
                                    $retsPropDataQuery = "DELETE from RetsPropertyData where ListingId='".$rets_data['Ml_num']."';";
                                    DB::delete($retsPropDataQuery);*/
                                    RetsPropertyData::where("ListingId",$record[$key_field])->update(["Status"=>"D","updated_time" => date("Y-m-d H:i:s")]);
                                    RetsPropertyDataComm::where("Ml_num",$record[$key_field])->update(["Status"=>"D","property_last_updated" => date("Y-m-d H:i:s")]);
                                }
                                if ($property_table_name == "RetsPropertyDataCondo") {
                                    /*$rets_data["Status"] = "D";
                                    RetsPropertyDataCondoPurged::updateOrCreate(["Ml_num"=>$rets_data["Ml_num"]],$rets_data);
                                    $retsPropDataCommQuery = "DELETE from RetsPropertyDataCondo where Ml_num='".$rets_data['Ml_num']."';";
                                    $retsPropDataCommQuery = "DELETE from RetsPropertyData where Ml_num='".$rets_data['Ml_num']."';";
                                    DB::delete($retsPropDataCommQuery);
                                    $rpd_data["Status"] = "D";
                                    RetsPropertyDataPurged::updateOrCreate(["ListingId"=>$rets_data],$rpd_data);
                                    DB::delete($retsPropDataQuery);*/
                                    RetsPropertyData::where("ListingId",$record[$key_field])->update(["Status"=>"D","updated_time" => date("Y-m-d H:i:s")]);
                                    RetsPropertyDataCondo::where("Ml_num",$record[$key_field])->update(["Status"=>"D","property_last_updated" => date("Y-m-d H:i:s")]);
                                }
                                echo "in update rets = " . $record[$key_field];
                            } else {

                            }
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
        }
        // need to update sitemap
        //$sitemap = new GenerateXMLFile();
        //$sitemap->generatePropertyXml();
        //$sitemap->generateBlogXml();
        //$sitemap->generateCityXml();
        // execute frontend commands
        updateHomePageJson();
        updateAutoSuggestionJson();
        $commands = "./frontendRestartScript.sh";
        shell_exec($commands);
        $this->deleteProperty();
    }
    
    public function deleteProperty() {
        $query = RetsPropertyData::where("Status","D")->get();
        foreach ($query as $data){
            $data = collect($data)->all();
            unset($data["id"]);
            $data["updated_time"] = date("Y-m-d H:i:s");
            RetsPropertyDataDelete::updateOrCreate($data);
        }
        RetsPropertyData::where("Status","D")->delete();
    }

    public function testFeatures()
    {
        $query = "SELECT Extras,Prop_feat1_out,Prop_feat2_out,Prop_feat4_out,Prop_feat5_out,Prop_feat6_out,A_c,Fuel,Heating,Laundry,Pool from RetsPropertyData";
        $prev_featured_query = "SELECT Features from FeaturesMaster";
        $prev_featured = collect(DB::select($prev_featured_query))->pluck('Features')->all();
        $data = DB::select($query);
        $temp_data = [];
        $properties_inserted_in_db = 0;
        foreach ($data as $value) {
            $properties_inserted_in_db++;
            $value = collect($value)->all();
            $extras = explode(',', $value["Extras"]);
            if ($extras !== []) {
                foreach ($extras as $extra) {
                    if (in_array($extra, $prev_featured) == false) {
                        $temp_data[] = $extra;
                    }
                }
            }
            if ($value["Prop_feat1_out"] !== "" && in_array($value["Prop_feat1_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat1_out"];
            }
            if ($value["Prop_feat2_out"] !== "" && in_array($value["Prop_feat2_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat2_out"];
            }
            if ($value["Prop_feat4_out"] !== "" && in_array($value["Prop_feat4_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat4_out"];
            }
            if ($value["Prop_feat6_out"] !== "" && in_array($value["Prop_feat6_out"], $prev_featured) == false) {
                $temp_data[] = $value["Prop_feat6_out"];
            }
            if ($value["A_c"] !== "" && in_array($value["A_c"], $prev_featured) == false) {
                $temp_data[] = $value["A_c"];
            }
            if ($value["Fuel"] !== "" && in_array($value["Fuel"], $prev_featured) == false) {
                $temp_data[] = $value["Fuel"];
            }
            if ($value["Heating"] !== "" && in_array($value["Heating"], $prev_featured) == false) {
                $temp_data[] = $value["Heating"];
            }
            if ($value["Laundry"] !== "" && in_array($value["Laundry"], $prev_featured) == false) {
                $temp_data[] = $value["Laundry"];
            }
            if ($value["Pool"] !== "" && in_array($value["Pool"], $prev_featured) == false) {
                $temp_data[] = $value["Pool"];
            }
        }
        foreach ($temp_data as $val) {
            $ins_array["Features"] = $val;
            $temp_property = ["Features" => $val];
            FeaturesMaster::updateOrCreate(
                $temp_property,
                $temp_property
            );
            //FeaturesMaster::create($ins_array);
        }
        echo "\n working on data = " . $properties_inserted_in_db;
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

