<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use App\lib\phrets;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\Temp_listing_idx_sql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingsSoldControllerIDX extends Controller
{
    //
    public $images_config;
    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }
    public function importPropertyListing()
    {

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
        $file_name = "SoldListingsControllerIDX";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
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
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterIDXACTIVE');
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
                    echo "\n Connected.....";
                }
                if (!$connect) {
                    $error_details = $rets->Error();
                    $error_text = strip_tags($error_details['text']);
                    $error_type = strtoupper($error_details['type']);
                    echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
                }
                $curr_date_end_time = $curr_date;
                $timestamp_field = $classconfig['timestamp_field'];
                $status_field = $classconfig['status_field'];
                $status_field_val = $classconfig['status_field_val'];
                $key_field = $classconfig['key_field'];
                //$property_data_mapping = $classconfig['property_data_mapping'];
                $class_type = (isset($classconfig['class_type']) && !empty($classconfig['class_type'])) ? ',' . $classconfig['class_type'] : '';
                $complete_pull = false;
                $rets_query = "((Status=|A))";
                $search = $rets->SearchQuery($property_resource, $className, $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                $total_property_count = $rets->TotalRecordsFound($search);
                if ($total_property_count > 0) {
                    echo "\n Count :: $total_property_count";
                    // Update total property found from MLSu
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
                    $txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        DB::statement("TRUNCATE TempListingIdx");
                        while ($record = $rets->FetchRow($search_chunks)) {
                            $properties_inserted_in_db++;
                            $property_data = [];
                            $property_data["Ml_num"] = $record["Ml_num"];
                            $property_data["Status"] = $record["Status"];
                            $id = Temp_listing_idx_sql::create($property_data)->id;
                            echo  "\n Properties inserted in tempListing db". $properties_inserted_in_db;
                        }
                        $temp_listing = Temp_listing_idx_sql::select("Ml_num")->get()->toArray();
                        $property_data_db = DB::select("SELECT Ml_num FROM " . $property_table_name . " WHERE Status = 'A'");
                        $diff = array_diff(array_column($property_data_db, "Ml_num"), array_column($temp_listing, "Ml_num"));
                        $showDiff = implode(',', $diff);
                        Log::info("This is the differences of IDX Properties = ".$showDiff);
                        Log::info("This is the count of differences of IDX Properties = ".count($diff));
                        foreach ($diff as $val) {
                            DB::update("UPDATE " . $property_table_name . " SET Status='U' where Ml_num='$val'");
                            $updateData["Status"] = "U";
                            RetsPropertyData::where("ListingId", $val)->update($updateData);
                        }
                        // update the cron table
                        #$res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                        Log::info("total_property_fetched = ".$properties_inserted_in_db." For ClassName = ".$className);
                        Log::info("total_property_inserted = ".$insertion_prop." For ClassName = ".$className);
                        Log::info("total_property_updated = ".$update_prop." For ClassName = ".$className);
                    }
                    // }
                } else {
                    echo "no data...";
                }
            }
        }
    }
}
