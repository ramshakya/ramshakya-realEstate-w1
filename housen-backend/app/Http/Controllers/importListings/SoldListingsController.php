<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataImage;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\SqlModel\RetsPropertyDataPurged;
use FilesystemIterator;
use Illuminate\Support\Facades\DB;
use App\lib\phrets;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Schema;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


class SoldListingsController extends Controller
{
    //
    public $mls_config;
    public $images_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        //$this->cron_log_model = $cron_log_model;
    }
    public function importPropertyListing()
    {

        $rets_prop_mapping = [
            "Den_fr",
            "Disp_addr",
            "Dom",
            "Dt_sus",
            "Dt_ter",
            "Timestamp_sql",
            "Area",
            "Community",
            "Municipality_code",
            "Cert_lvl",
            "Energy_cert",
            "Oh_dt_stamp",
            "Oh_to1",
            "Oh_to2",
            "Oh_to3",
            "Green_pis",
            "Oh_from1",
            "Oh_from2",
            "Oh_from3",
            "A_c",
            "Prop_feat4_out",
            "Prop_feat5_out",
            "Prop_feat6_out",
            "Rm9_len",
            "Rm9_wth",
            "Prop_feat3_out",
            "Ad_text",
            "Addl_mo_fee",
            "Addr",
            "All_inc",
            "Condo_corp",
            "Condo_exp",
            "Constr1_out",
            "Constr2_out",
            "Corp_num",
            "County",
            "Cross_st",
            "Elevator",
            "Ens_lndry",
            "Extras",
            "Fpl_num",
            "Fuel",
            "Furnished",
            "Gar",
            "Gar_type",
            "Heat_inc",
            "Heating",
            "Laundry",
            "Laundry_lev",
            "Ld",
            "Level1",
            "Level10",
            "Orig_dol",
            "Outof_area",
            "Parcel_id",
            "Park_chgs",
            "Park_desig",
            "Park_desig_2",
            "Park_fac",
            "Park_lgl_desc1",
            "Park_lgl_desc2",
            "Park_spc1",
            "Park_spc2",
            "Park_spcs",
            "Patio_ter",
            "Perc_dif",
            "Pets",
            "Pr_lsc",
            "Prkg_inc",
            "Prop_feat1_out",
            "Prop_feat2_out",
            "Prop_mgmt",
            "Pvt_ent",
            "Retirement",
            "Rltr",
            "Rm1_dc1_out",
            "Rm1_dc2_out",
            "Rm1_dc3_out",
            "Rm1_len",
            "Rm1_out",
            "Rm1_wth",
            "Rm10_dc1_out",
            "Rm10_dc2_out",
            "Rm10_dc3_out",
            "Rm10_len",
            "Rm10_out",
            "Rm10_wth",
            "Rm11_dc1_out",
            "Rm11_dc2_out",
            "Rm11_dc3_out",
            "Rm11_len",
            "Rm11_out",
            "Rm11_wth",
            "Rm12_dc1_out",
            "Rm12_dc2_out",
            "Rm12_dc3_out",
            "Rm12_len",
            "Rm12_out",
            "Rm12_wth",
            "Rm2_dc1_out",
            "Rm2_dc2_out",
            "Rm2_dc3_out",
            "Rm2_len",
            "Rm2_out",
            "Rm2_wth",
            "Rm3_dc1_out",
            "Rm3_dc2_out",
            "Rm3_dc3_out",
            "Rm3_len",
            "Rm3_out",
            "Rm3_wth",
            "Rm4_dc1_out",
            "Rm4_dc2_out",
            "Rm4_dc3_out",
            "Rm4_len",
            "Rm4_out",
            "Rm4_wth",
            "Rm5_dc1_out",
            "Rm5_dc2_out",
            "Rm5_dc3_out",
            "Rm5_len",
            "Rm5_out",
            "Rm5_wth",
            "Rm6_dc1_out",
            "Rm6_dc2_out",
            "Rm6_dc3_out",
            "Rm6_len",
            "Rm6_out",
            "Rm6_wth",
            "Rm7_dc1_out",
            "Rm7_dc2_out",
            "Rm7_dc3_out",
            "Rm7_len",
            "Rm7_out",
            "Rm7_wth",
            "Rm8_dc1_out",
            "Rm8_dc2_out",
            "Rm8_dc3_out",
            "Rm8_len",
            "Rm8_out",
            "Rm8_wth",
            "Rm9_dc1_out",
            "Rm9_dc2_out",
            "Rm9_dc3_out",
            "Rm9_out",
            "Rms",
            "Rooms_plus",
            "S_r",
            "Sp_dol",
            "Sqft",
            "St",
            "St_dir",
            "Stories",
            "Tour_url",
            "Community_code",
            "Area_code",
            "Type_own1_out",
            "Municipality",
            "Oh_date1",
            "Zip",
            "Br",
            "Bsmt1_out",
            "Bsmt2_out",
            "Lp_dol",
            "Ml_num",
            "Status",
            "Pool",
            "Bath_tot"
        ];
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "SoldTreb";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        $cron_data = array(
            'cron_file_name' => $file_name,
            'cron_start_time' => $curr_date,
            'steps_completed' => 1
        );
        // For all MLSs - Starts
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = $curr_mls['mls_name'];
            echo "\n Started for " . $curr_mls_name;
            //echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $photos_field = config('mls_config.rets_query_array.' . $curr_mls_id . '.photos_field');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            $is_run = 0;
            foreach ($property_class as $className => $classconfig) {
                #if ($className == "ResidentialProperty") continue;
                #if ($className == "CommercialProperty") continue;
                $is_run++;
                $curr_date = date('Y-m-d H:i:s');
                $properties_inserted_in_db = 0;
                $insertion_prop = 0;
                $update_prop = 0;
                $rets = new phrets;
                $login_parameters = config('mls_config.mls_login_parameter');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                if ($className != "CommercialProperty"){
                    $room_mapping = $classconfig['room_data_mapping'][$className];
                }else{
                    $room_mapping = [];
                }
                $login_url = $login_parameter['rets_login_url'];
                $mls_username = $login_parameter['rets_username'];
                $mls_password = $login_parameter['rets_password'];
                $rets_version = $login_parameter['RETS-Version'];
                $user_agent = $login_parameter['User-Agent'];
                $rets->AddHeader("Accept", "*");
                $rets->AddHeader("RETS-Version", $rets_version);
                $rets->AddHeader("User-Agent",  $user_agent);
                $rets->SetParam('compression_enabled', true);
                // $rets->SetParam('debug_mode', true);
                $rets->SetParam("offset_support", true);
                //$rets->SetParam("compression_enabled", true);
                // make first connection
                $connect = $rets->Connect($login_url, $mls_username, $mls_password);
                // $connect = "";
                if ($connect) {
                    //echo "<h3 style='color:green'>Connected.....</h3>";
                    echo "\n Connected.....";
                }
                if (!$connect) {
                    $error_details = $rets->Error();
                    $error_text    = strip_tags($error_details['text']);
                    $error_type    = strtoupper($error_details['type']);
                    echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                    //echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                }
                // dd($connect);
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
                $property_query_start_time = get_start_time_for_cron($cron_tablename, $file_name,  $curr_mls_id,$className);
                if (isset($property_query_start_time) && $property_query_start_time['status'] == 'start') {
                    $complete_pull = true;
                }
                $date_time = explode(' ', $property_query_start_time['time']);
                $property_query_start_time = $date_time[0] . 'T' . $date_time[1];
                $date_query = "($timestamp_field=$property_query_start_time+)";
                $batables = array();
                $curr_timestamp = time();
                $old_timestamp = strtotime($property_query_start_time);
                $diff = ($curr_timestamp - $old_timestamp);
                $new_starttime = 0;
                if ($diff > 3600 * 24 * 1) {
                    $new_starttime = strtotime("-1 day", $old_timestamp);
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
                $rets_query = "((Status=|U))";
                $cron_update_data = array(
                    'properties_download_start_time' => $property_query_start_time,
                    'steps_completed' => 2,
                    'rets_query' => $rets_query
                );
                $cron_cond = array(
                    'id' => $this_cron_log_id
                );
                // update the cron table
                // for testing
                $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                //$rets_metadata      = $rets->GetMetadata($property_resource, $className);
                $search = $rets->SearchQuery($property_resource, $className, $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                //echo "<pre>";
                // print_r($rets_metadata);
                $total_property_count = $rets->TotalRecordsFound($search);
                if ($total_property_count > 0) {
                    //echo "<h2 style='color :green;'>Count :: $total_property_count</h2>";
                    echo "\n Count :: $total_property_count";
                    // Update total property found from MLSu
                    $cron_update_data = array(
                        'properties_count_from_mls' => $total_property_count,
                        'steps_completed' => 3
                    );
                    $cron_cond    = array('id' => $this_cron_log_id);
                    $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                    $mls_arr = array();
                    $i = 0;
                    $limit = 100;
                    $start_index = 1;
                    $time_stamp_array = array();
                    $new_sleep = 0;
                    $lc = (($total_property_count - $start_index) / $limit);
                    $complete_counter = $total_property_count;
                    $lcp = 0;
                    // for ($lcp = 0; $lcp <= $lc; $lcp++) {
                    $new_sleep++;
                    $offset = $start_index + $lcp * $limit;
                    if ($new_sleep == 10) {
                        $new_sleep = 0;
                        sleep(10);
                    }
                    echo "\n --" . $offset . " Limit " . $limit . "\n";
                    $search_chunks       = $rets->Searchquery($property_resource, $className, $rets_query, array('Format' => 'COMPACT-DECODED', 'Count' => 1, "UsePost" => 1));
                    $txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        while ($record = $rets->FetchRow($search_chunks)) {
                            $properties_inserted_in_db++;
                            //echo $record[$mls_key];
                            echo  "\n Properties db". $properties_inserted_in_db;
                            //continue;
                            $sl++;
                            echo "\n sleep value";
                            if ($sl == 1000){
                                $sl = 0;
                                echo "\n In sleep";
                                sleep(5);
                            }
                            $property_data = array();
                            $rets_property_data = array();
                            $main_data = array();
                            foreach ($property_data_mapping as $db_key => $mls_key) {
                                if (in_array($mls_key, $rets_prop_mapping)) {
                                    $rets_property_data[$mls_key] = $record[$mls_key];
                                }
                                if (isset($record[$mls_key]))
                                    $property_data[$db_key] = $record[$mls_key];
                                else
                                    $property_data[$db_key] = '';
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
                            //echo "\n property_table_name  =>  $property_table_name";
                            if (count($rets_data) > 0) {
                                $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->update($property_data);
                                $update_prop++;
                            } else {
                                $insertion_prop++;
                                $property_data['property_insert_time'] = date('Y-m-d H:i:s');
                                DB::table($property_table_name)->insertGetId($property_data);
                                //echo "<br></pre> <h1>Inserted Property  =>  $rets_data</h1> <br/>";
                            }
                            if ($className == "ResidentialProperty"){
                                $rets_property_data["ClassName"] = "Residential";
                            }
                            if ($className == "CommercialProperty"){
                                $rets_property_data["ClassName"] = "Commercial";
                            }
                            if ($className == "CondoProperty"){
                                $rets_property_data["ClassName"] = "Condos";
                            }
                            $rets_property_data["ShortPrice"] = number_format_short($property_data['Lp_dol']);
                            $rets_property_data["PropertySubType"] = $property_data['Type_own1_out'];
                            $rets_property_data["PropertyStatus"] = "For " .$property_data["S_r"];
                            $rets_property_data["City"] = str_replace("'",'',$property_data["Municipality"]);
                            $result = RetsPropertyData::updateOrCreate(["ListingId" => $record[$key_field]], $rets_property_data);
                            //$result = \App\Models\MongoModel\RetsPropertyData::updateOrCreate([$key_field => $record[$key_field]], $rets_property_data);

                            echo "\n property inserted".$properties_inserted_in_db;
                        }
                        $cron_update_data           = array(
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
                    echo "no data...";
                }
            }
        }
    }
    public function importPropertyListingV2()
    {
        $rets_prop_mapping = [
            "Den_fr",
            "Disp_addr",
            "Dom",
            "Dt_sus",
            "Dt_ter",
            "Timestamp_sql",
            "Area",
            "Community",
            "Municipality_code",
            "Cert_lvl",
            "Energy_cert",
            "Oh_dt_stamp",
            "Oh_to1",
            "Oh_to2",
            "Oh_to3",
            "Green_pis",
            "Oh_from1",
            "Oh_from2",
            "Oh_from3",
            "A_c",
            "Prop_feat4_out",
            "Prop_feat5_out",
            "Prop_feat6_out",
            "Rm9_len",
            "Rm9_wth",
            "Prop_feat3_out",
            "Ad_text",
            "Addl_mo_fee",
            "Addr",
            "All_inc",
            "Condo_corp",
            "Condo_exp",
            "Constr1_out",
            "Constr2_out",
            "Corp_num",
            "County",
            "Cross_st",
            "Elevator",
            "Ens_lndry",
            "Extras",
            "Fpl_num",
            "Fuel",
            "Furnished",
            "Gar",
            "Gar_type",
            "Heat_inc",
            "Heating",
            "Laundry",
            "Laundry_lev",
            "Ld",
            "Level1",
            "Level10",
            "Orig_dol",
            "Outof_area",
            "Parcel_id",
            "Park_chgs",
            "Park_desig",
            "Park_desig_2",
            "Park_fac",
            "Park_lgl_desc1",
            "Park_lgl_desc2",
            "Park_spc1",
            "Park_spc2",
            "Park_spcs",
            "Patio_ter",
            "Perc_dif",
            "Pets",
            "Pr_lsc",
            "Prkg_inc",
            "Prop_feat1_out",
            "Prop_feat2_out",
            "Prop_mgmt",
            "Pvt_ent",
            "Retirement",
            "Rltr",
            "Rm1_dc1_out",
            "Rm1_dc2_out",
            "Rm1_dc3_out",
            "Rm1_len",
            "Rm1_out",
            "Rm1_wth",
            "Rm10_dc1_out",
            "Rm10_dc2_out",
            "Rm10_dc3_out",
            "Rm10_len",
            "Rm10_out",
            "Rm10_wth",
            "Rm11_dc1_out",
            "Rm11_dc2_out",
            "Rm11_dc3_out",
            "Rm11_len",
            "Rm11_out",
            "Rm11_wth",
            "Rm12_dc1_out",
            "Rm12_dc2_out",
            "Rm12_dc3_out",
            "Rm12_len",
            "Rm12_out",
            "Rm12_wth",
            "Rm2_dc1_out",
            "Rm2_dc2_out",
            "Rm2_dc3_out",
            "Rm2_len",
            "Rm2_out",
            "Rm2_wth",
            "Rm3_dc1_out",
            "Rm3_dc2_out",
            "Rm3_dc3_out",
            "Rm3_len",
            "Rm3_out",
            "Rm3_wth",
            "Rm4_dc1_out",
            "Rm4_dc2_out",
            "Rm4_dc3_out",
            "Rm4_len",
            "Rm4_out",
            "Rm4_wth",
            "Rm5_dc1_out",
            "Rm5_dc2_out",
            "Rm5_dc3_out",
            "Rm5_len",
            "Rm5_out",
            "Rm5_wth",
            "Rm6_dc1_out",
            "Rm6_dc2_out",
            "Rm6_dc3_out",
            "Rm6_len",
            "Rm6_out",
            "Rm6_wth",
            "Rm7_dc1_out",
            "Rm7_dc2_out",
            "Rm7_dc3_out",
            "Rm7_len",
            "Rm7_out",
            "Rm7_wth",
            "Rm8_dc1_out",
            "Rm8_dc2_out",
            "Rm8_dc3_out",
            "Rm8_len",
            "Rm8_out",
            "Rm8_wth",
            "Rm9_dc1_out",
            "Rm9_dc2_out",
            "Rm9_dc3_out",
            "Rm9_out",
            "Rms",
            "Rooms_plus",
            "S_r",
            "Sp_dol",
            "Sqft",
            "St",
            "St_dir",
            "Stories",
            "Tour_url",
            "Community_code",
            "Area_code",
            "Type_own1_out",
            "Municipality",
            "Oh_date1",
            "Zip",
            "Br",
            "Bsmt1_out",
            "Bsmt2_out",
            "Lp_dol",
            "Ml_num",
            "Status",
            "Pool",
            "Bath_tot"
        ];
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "Treb";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        $cron_data = array(
            'cron_file_name' => $file_name,
            'cron_start_time' => $curr_date,
            'steps_completed' => 1
        );
        // For all MLSs - Starts
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = $curr_mls['mls_name'];
            echo "\n Started for " . $curr_mls_name;
            //echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $photos_field = config('mls_config.rets_query_array.' . $curr_mls_id . '.photos_field');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            $is_run = 0;
            foreach ($property_class as $className => $classconfig) {
                $is_run++;
                $curr_date = date('Y-m-d H:i:s');
                $properties_inserted_in_db = 0;
                $insertion_prop = 0;
                $update_prop = 0;
                $rets = new phrets;
                $login_parameters = config('mls_config.mls_login_parameter');
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
                $rets->AddHeader("User-Agent",  $user_agent);
                $rets->SetParam('compression_enabled', true);
                // $rets->SetParam('debug_mode', true);
                $rets->SetParam("offset_support", true);
                //$rets->SetParam("compression_enabled", true);
                // make first connection
                $connect = $rets->Connect($login_url, $mls_username, $mls_password);
                // $connect = "";
                if ($connect) {
                    //echo "<h3 style='color:green'>Connected.....</h3>";
                    echo "\n Connected.....";
                }
                if (!$connect) {
                    $error_details = $rets->Error();
                    $error_text    = strip_tags($error_details['text']);
                    $error_type    = strtoupper($error_details['type']);
                    echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                    //echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                }
                // dd($connect);
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
                $property_query_start_time = get_start_time_for_cron($cron_tablename, $file_name,  $curr_mls_id,$className);
                if (isset($property_query_start_time) && $property_query_start_time['status'] == 'start') {
                    $complete_pull = true;
                }
                $date_time = explode(' ', $property_query_start_time['time']);
                $property_query_start_time = $date_time[0] . 'T' . $date_time[1];
                $date_query = "($timestamp_field=$property_query_start_time+)";
                $batables = array();
                $curr_timestamp = time();
                $old_timestamp = strtotime($property_query_start_time);
                $diff = ($curr_timestamp - $old_timestamp);
                $new_starttime = 0;
                if ($diff > 3600 * 24 * 1) {
                    $new_starttime = strtotime("-1 day", $old_timestamp);
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
                $rets_query = "((Status=|A))";
                $cron_update_data = array(
                    'properties_download_start_time' => $property_query_start_time,
                    'steps_completed' => 2,
                    'rets_query' => $rets_query
                );
                $cron_cond = array(
                    'id' => $this_cron_log_id
                );
                // update the cron table
                // for testing
                $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                $rets_metadata      = $rets->GetMetadata($property_resource, $className);
                $search = $rets->SearchQuery($property_resource, $className, $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                //echo "<pre>";
                // print_r($rets_metadata);
                $total_property_count = $rets->TotalRecordsFound($search);
                if ($total_property_count > 0) {
                    //echo "<h2 style='color :green;'>Count :: $total_property_count</h2>";
                    echo "\n Count :: $total_property_count";
                    // Update total property found from MLSu
                    $cron_update_data = array(
                        'properties_count_from_mls' => $total_property_count,
                        'steps_completed' => 3
                    );
                    $cron_cond    = array('id' => $this_cron_log_id);
                    $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                    $mls_arr = array();
                    $i = 0;
                    $limit = 100;
                    $start_index = 1;
                    $time_stamp_array = array();
                    $new_sleep = 0;
                    $lc = (($total_property_count - $start_index) / $limit);
                    $complete_counter = $total_property_count;
                    $lcp = 0;
                    // for ($lcp = 0; $lcp <= $lc; $lcp++) {
                    $new_sleep++;
                    $offset = $start_index + $lcp * $limit;
                    if ($new_sleep == 10) {
                        $new_sleep = 0;
                        sleep(10);
                    }
                    echo "\n --" . $offset . " Limit " . $limit . "\n";
                    $search_chunks       = $rets->Searchquery($property_resource, $className, $rets_query, array('Format' => 'COMPACT-DECODED', 'Count' => 1, "UsePost" => 1,'Select'=>'Ml_num,Lp_dol'));
                    $txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        while ($record = $rets->FetchRow($search_chunks)) {
                            $properties_inserted_in_db++;
                            //echo $record[$mls_key];
                            //echo  "\n $recod  db". $properties_inserted_in_db;
                            //continue;
                            /*$sl++;
                            echo "\n sleep value";
                            if ($sl == 1000){
                                $sl = 0;
                                echo "\n In sleep";
                                sleep(5);
                            }*/
                            $property_data = array();
                            $rets_property_data = array();
                            $main_data = array();
                            //$rets_data = DB::table($property_table_name)->where("Ml_num", $record["Ml_num"])->update(["Lp_dol"=>$record["Lp_dol"]]);
                            $rets_data = DB::update('UPDATE `rets_property_data` SET `Lp_dol`="'.$record["Lp_dol"].'" WHERE Ml_num = "'.$record["Ml_num"].'"');
                            $rets_data = DB::update('UPDATE `'.$property_table_name.'` SET `Lp_dol`="'.$record["Lp_dol"].'" WHERE Ml_num = "'.$record["Ml_num"].'"');


                            //$rets_data = DB::table("rets_property_data")->where("Ml_num", $record["Ml_num"])->update(["Lp_dol"=>$record["Lp_dol"]]);
                            //$result = RetsPropertyData::updateOrCreate([$key_field => $record[$key_field]], $rets_property_data);
                            echo "\n Property Updated - ".$properties_inserted_in_db. "- property id - ".$record["Ml_num"];
                            continue;
                            foreach ($property_data_mapping as $db_key => $mls_key) {
                                if (in_array($mls_key, $rets_prop_mapping)) {
                                    $rets_property_data[$mls_key] = $record[$mls_key];
                                }
                                if (isset($record[$mls_key]))
                                    $property_data[$db_key] = $record[$mls_key];
                                else
                                    $property_data[$db_key] = '';
                            }
                            $rets_property_data['mls_no'] = $curr_mls_id;
                            $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->get();
                            $property_data['property_last_updated'] = date('Y-m-d H:i:s');
                            //echo "\n property_table_name  =>  $property_table_name";
                            if (count($rets_data) > 0) {
                                $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->update($property_data);
                                $update_prop++;
                            } else {
                                $insertion_prop++;
                                $property_data['property_insert_time'] = date('Y-m-d H:i:s');
                                DB::table($property_table_name)->insertGetId($property_data);
                                //echo "<br></pre> <h1>Inserted Property  =>  $rets_data</h1> <br/>";
                            }
                            if ($className == "ResidentialProperty"){
                                $rets_property_data["ClassName"] = "Residential";
                            }
                            if ($className == "CommercialProperty"){
                                $rets_property_data["ClassName"] = "Commercial";
                            }
                            if ($className == "CondoProperty"){
                                $rets_property_data["ClassName"] = "Condos";
                            }
                            $result = RetsPropertyData::updateOrCreate([$key_field => $record[$key_field]], $rets_property_data);
                            //$result = \App\Models\MongoModel\RetsPropertyData::updateOrCreate([$key_field => $record[$key_field]], $rets_property_data);

                            echo "\n property inserted".$properties_inserted_in_db;
                            //echo "<pre>";
                            //echo "$property_table_name";
                        }
                        $cron_update_data           = array(
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
                    echo "no data...";
                }
            }
        }
    }
    function createtable()
    {
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "Treb";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        $cron_data = array(
            'cron_file_name' => $file_name,
            'cron_start_time' => $curr_date,
            'steps_completed' => 1
        );
        $res = Schema::getColumnListing('rets_property_data_condo'); // users table
        $alldb_columns = array();
        $all_fields_array = array();
        // foreach ($res as $key => $value) {
        //     // echo $value;
        //     // $all_fields_array[] = $value['Field'];
        //     echo "<br>'" . $value . "'=>'" . $value . "',";
        //     // $alldb_columns[] = $value['Field'];
        // }
        //     exit;
        // For all MLSs - Starts
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = $curr_mls['mls_name'];
            echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $photos_field = config('mls_config.rets_query_array.' . $curr_mls_id . '.photos_field');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            foreach ($property_class as $className => $classconfig) {
                $curr_date = date('Y-m-d H:i:s');
                $properties_inserted_in_db = 0;
                $insertion_prop = 0;
                $update_prop = 0;
                $rets = new phrets;
                $login_parameters = config('mls_config.mls_login_parameter');
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
                $rets->AddHeader("User-Agent",  $user_agent);
                $rets->SetParam('compression_enabled', true);
                // $rets->SetParam('debug_mode', true);
                $rets->SetParam("offset_support", true);
                //$rets->SetParam("compression_enabled", true);
                // make first connection
                $connect = $rets->Connect($login_url, $mls_username, $mls_password);
                $rets_metadata = $rets->GetMetadata($property_resource, $className);
                echo $sql = $rets->create_table_sql_from_metadata('rets_property_data_resi', $rets_metadata, 'Ml_num');
                exit;
            }
        }
        /*$res = $this->db->query("SHOW COLUMNS FROM `rets_agent_data`  ")->result_array();
            $alldb_columns = array();
            $all_fields_array = array();
            foreach ($res as $key => $value) {
                $all_fields_array[] = $value['Field'];
                echo "<br>'".$value['Field']."'=>'".$value['Field']."'," ;
                $alldb_columns[] = $value['Field'];
            }
            exit;*/
        echo "<pre>";
        $resource   = 'Office';
        $className  = 'Office';
        $rets       = mls_login(1);

        //$rets_resource_info = $this->phrets->GetMetadataInfo();
        //$rets_resource_info = $this->phrets->GetMetadataClasses('Property');
        //print_r($rets_resource_info);
        $rets_metadata      = $this->phrets->GetMetadata($resource, $className);
        /*$afml=array();
            foreach(  $rets_metadata as $frty){
                $afml[] = $frty['SystemName'] ;
            }
            $dfr = array_diff($afml,$alldb_columns);
            print_r($dfr);*/
        //$rets_metadata = array_unique($rets_metadata);
        //echo $sql     = $this->phrets->create_table_sql_from_metadata('rets_agent_data', $rets_metadata, 'MemberKeyNumeric');
    }

    //   for images changes
    public function getImageSizeAndExistence() {
        $tables = ["RetsPropertyDataResi","RetsPropertyDataComm","RetsPropertyDataCondo","RetsPropertyDataResiPurged","RetsPropertyDataCommPurged","RetsPropertyDataCondoPurged"];
        $txt ="";
        $txt .= "<table border=1>";
        $bacounter = 0;
        $txt .= "<thead>
                        <tr>
                          <th>Sr. No</th>
                          <th>Table</th>
                          <th>Size</th>
                        </tr>
                     </thead>";
        $total_size = 0;
        $table_data = [];
        foreach ($tables as $table) {
            $sql_query = "SELECT Ml_num,image_downloaded from $table";
            $sql_data = DB::select($sql_query);
            $table_size = 0;
            foreach ($sql_data as $data) {
                $data = collect($data)->all();
                // get the size of the folder
                $size = $this->GetDirectorySize("/home/mukesh/public_html/panel/storage/app/public/img/mls_images/".$data["Ml_num"]);
                echo "\n Ml_num = ".$data["Ml_num"];
                echo "\n got the size of an images = ".$size;
                $table_size = $table_size+$size;
            }
            $table_data[] = ["table" => $table, "size" => $table_size];
            $total_size = $total_size+$table_size;
            echo "\n table size = ".$table_size;
        }
        echo "\n total size = ".$total_size;
        $bacounter = 0;
        foreach ($table_data as $bagent) {
            $bacounter++;
            $txt .= "<tr>";
            $txt .= "<td>" . $bacounter . "</td>";
            $txt .= "<td>" . $bagent['table'] . "</td>";
            $txt .= "<td>" . $bagent['size'] . "</td>";
            $txt .= "</tr>";
        }
        $txt .= "</table> <p>total size - </p>".$total_size ;

        sendEmail("SMTP", env('MAIL_FROM'), "sagar@peregrine-it.com", "sagr7188@gmail.com", "sagarvermaitdeveloper@gmail.com", 'Image calculation - ', $txt, "Sold Listings controller getImageSize", "", env('RETSEMAILS'));
        exit();






        $sql_query = "SELECT Ml_num,image_downloaded from RetsPropertyDataResiPurged";
        $sql_data = DB::select($sql_query);
        foreach ($sql_data as $data) {
            $data = collect($data)->all();
            $image_check = RetsPropertyDataImage::where("listingID",$data["Ml_num"])->get();
            if ($image_check != []) {
                if (collect($image_check)->count() > 2) {
                    echo "\n image found for the properties which is sold";
                    // insert all the data into the purged images table
                    foreach ($image_check as $image) {
                        $image =  collect($image)->all();
                        unset($image["id"]);
                        RetsPropertyDataImagesSold::insert($image);
                        echo "\n inserted into the sold images table";
                    }
                    // get the size of the folder
                    $size = $this->GetDirectorySize("/storage/app/public/img/mls_images/".$data["Ml_num"]);
                    echo "\n got the size of an images = ".$size;
                    // update the table for the images existed in the active table
                    $update_query = "update RetsPropertyDataResiPurged set image_downloaded = 1, imagesSize = '".$size."' where Ml_num = '".$data['Ml_num']."'";
                    DB::update($update_query);
                }
            } else {
                // update the table for the images not existed in the active table
                echo "\n Not found any images for listing = ".$data["Ml_num"];
                $size = '0';
                $update_query = "update RetsPropertyDataResiPurged set image_downloaded = 0, imagesSize = '".$size."' where Ml_num = '".$data['Ml_num']."'";
                DB::update($update_query);
            }
        }
    }



    public function testSize(){
        $size =  $this->GetDirectorySize("/home/peregrine/Videos");
        echo "\n size = ".$size;
        echo "\n ".$this->formatSizeUnits($size);
    }

    public function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public function getImagesForSize()
    {
        $tables = ["RetsPropertyDataResiPurged","RetsPropertyDataCommPurged", "RetsPropertyDataCondoPurged"];
        foreach ($tables as $table) {
            $sql_query = "SELECT Ml_num,image_downloaded from $table where image_downloaded = 0 order by id desc";
            $sql_data = DB::select($sql_query);
            $total_count = count($sql_data);
            echo "\n total Properties = " . $total_count;
            foreach ($sql_data as $data) {
                $data = collect($data)->all();
                $image_check = RetsPropertyDataImage::where("listingID", $data["Ml_num"])->get();
                $count_image = count($image_check);
                echo "\n image count = " . $count_image;
                echo "properties left = " . $total_count--;
                if (count($image_check) != 0) {
                    $count = 0;
                    if (collect($image_check)->count() > 2) {
                        echo "\n image found for the properties which is sold";
                        // insert all the data into the purged images table
                        $all_images = [];
                        foreach ($image_check as $image) {
                            $image = collect($image)->all();
                            unset($image["id"]);
                            $all_images[] = $image["s3_image_url"];
                            /*$insert_query = "INSERT INTO `RetsPropertyDataImagesSold`(`mls_no`, `listingID`, `image_path`, `s3_image_url`, `image_name`, `downloaded_time`, `mls_order`, `is_uploaded_by_agent`, `image_last_tried_time`, `deleted_status`) VALUES
                            ('".$image['mls_no']."','".$image['listingID']."','".$image['image_path']."','".$image['s3_image_url']."','".$image['image_name']."','".$image['downloaded_time']."','".$image['mls_order']."','".$image['is_uploaded_by_agent']."','".$image['image_last_tried_time']."',".$image['deleted_status'].")";
                            DB::insert($insert_query);*/
                            //DB::table("RetsPropertyDataImagesSold")->insert($image);
                            //RetsPropertyDataImagesSold::insert($image);
                            //echo "\n inserted into the sold images table";
                            $count++;
                            if ($count == 1) {
                                //RetsPropertyDataPurged::where("ListingId", $data["Ml_num"])->update(["ImageUrl" => $image['s3_image_url'], "Thumbnail_downloaded" => 1]);
                                $update_querysql = "update RetsPropertyDataPurged set ImageUrl = '".$image['s3_image_url']."', Thumbnail_downloaded =  1 where ListingId = '" . $data['Ml_num'] . "'";                            
                                DB::update($update_querysql);
                            }
                        }
                        echo "\n inserted into the sold images table listing id  = ".$data["Ml_num"];
                        $ins_data = ["listingID" => $data["Ml_num"], "image_urls" => json_encode($all_images)];
                        DB::table("RetsPropertyDataImagesSold")->insert($ins_data);
                        // check for the data in active
                        $rpd_active_sql = "SELECT ListingId from RetsPropertyData where ListingId = '" . $data['Ml_num'] . "'";
                        $rpd_active = DB::select($rpd_active_sql);
                        if (collect($rpd_active)->count() == 0) {
                            //RetsPropertyDataImage::where("listingID", $data["Ml_num"])->update(["deleted_status" => 1]);
                            $update_query_rpd = "update RetsPropertyDataImages set deleted_status = 1 where listingID = '" . $data['Ml_num'] . "'";
                            DB::update($update_query_rpd);
                        }
                        $update_query = "update $table set image_downloaded = 1 where Ml_num = '" . $data['Ml_num'] . "'";
                        DB::update($update_query);
                    }
                } else {
                    // update the table for the images not existed in the active table
                    echo "\n Not found any images for listing = " . $data["Ml_num"];
                    $size = '0';
                    $update_query = "update $table set image_downloaded = 0 where Ml_num = '" . $data['Ml_num'] . "'";
                    DB::update($update_query);
                }
            }
        }

    }


}
