<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateXMLFile;
use App\Models\PropertyAddressData;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\lib\phrets;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Schema;
use App\Models\RetsPropertyDataImagesSold;

class ListingsSoldControllerVOW extends Controller
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
	$rets_purged_arr=[
            "Disp_addr"         => "Disp_addr"         ,
            "Dom"               => "Dom"               ,
            "Timestamp_sql"     => "Timestamp_sql"     ,
            "City"              => "City"              ,
            "Community"         => "Community"         ,
            "Municipality_code" => "Municipality_code" ,
            "A_c"               => "A_c"               ,
            "Prop_feat4_out"    => "Prop_feat4_out"    ,
            "Prop_feat5_out"    => "Prop_feat5_out"    ,
            "Prop_feat6_out"    => "Prop_feat6_out"    ,
            "Prop_feat3_out"    => "Prop_feat3_out"    ,
            "PublicRemarks"     => "PublicRemarks"     ,
            "StandardAddress"   => "StandardAddress"   ,
            "All_inc"           => "All_inc"           ,
            "County"            => "County"            ,
            "Cross_st"          => "Cross_st"          ,
            "Elevator"          => "Elevator"          ,
            "Extras"            => "Extras"            ,
            "Fuel"              => "Fuel"              ,
            "Furnished"         => "Furnished"         ,
            "Gar"               => "Gar"               ,
            "Gar_type"          => "Gar_type"          ,
            "Heat_inc"          => "Heat_inc"          ,
            "Heating"           => "Heating"           ,
            "Laundry"           => "Laundry"           ,
            "Laundry_lev"       => "Laundry_lev"       ,
            "Orig_dol"          => "Orig_dol"          ,
            "Pets"              => "Pets"              ,
            "Prop_feat1_out"    => "Prop_feat1_out"    ,
            "Prop_feat2_out"    => "Prop_feat2_out"    ,
            "Rltr"              => "Rltr"              ,
            "MlsStatus"         => "MlsStatus"         ,
            "Sp_dol"            => "Sp_dol"            ,
            "Sqft"              => "Sqft"              ,
            "Community_code"    => "Community_code"    ,
            "Area_code"         => "Area_code"         ,
            "PropertySubType"   => "PropertySubType"   ,
            "Municipality"      => "Municipality"      ,
            "PostalCode"        => "PostalCode"        ,
            "BedroomsTotal"     => "BedroomsTotal"     ,
            "Bsmt1_out"         => "Bsmt1_out"         ,
            "Bsmt2_out"         => "Bsmt2_out"         ,
            "ListPrice"         => "ListPrice"         ,
            "ListingId"         => "ListingId"         ,
            "Status"            => "Status"            ,
            "Pool"              => "Pool"              ,
            "Park_spcs"         => "Park_spcs"         ,
            "BathroomsFull"     => "BathroomsFull"     ,
            "inserted_time"     => "inserted_time"     ,
            "updated_time"      => "updated_time"      ,
            "PropertyType"      => "PropertyType"      ,
            "Latitude"          => "Latitude"          ,
            "Longitude"         => "Longitude"         ,
            "YearBuilt"         => "YearBuilt"         ,
            "geocode"           => "geocode"           ,
            "geocodeTried"      => "geocodeTried"      ,
            "ImageUrl"          => "ImageUrl"          ,
            "ShortPrice"        => "ShortPrice"        ,
            "PropertyStatus"    => "PropertyStatus"    ,
            "SqftMin"           => "SqftMin"           ,
            "SqftMax"           => "SqftMax"           ,
            "sqftFlag"          => "sqftFlag"          ,
            "SlugUrl"           => "SlugUrl"           ,
            "Vow_exclusive"     => "Vow_exclusive"     ,
            "Thumbnail_downloaded"=> "Thumbnail_downloaded",
            "Ad_text"           => "Ad_text"
        ];
        $rets_prop_mapping = [
            "Disp_addr" => "Disp_addr",
            "Dom" => "Dom",
            "Park_spcs" => "Park_spcs",
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
	    "PostalCode" => "Zip",
            "BedroomsTotal" => "Br",
            "Bsmt1_out" => "Bsmt1_out",
            "Bsmt2_out" => "Bsmt2_out",
            "ListPrice" => "Lp_dol",
            "ListingId" => "Ml_num",
            "Status" => "Status",
            "Pool" => "Pool",
            "BathroomsFull" => "Bath_tot",
            "Sp_date" => "Cd",
            "Price" => "Lp_dol",
            "Style" => "Style",
            "ContractDate" => "Ld",
            "Address" => "Addr"
        ];
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "SoldListingsControllerVOW";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
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
                // if ($className == "ResidentialProperty") continue;
                // if ($className == "CommercialProperty") continue;
                // if ($className == "CondoProperty") continue;
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
                $property_table_name = $classconfig['purge_property_table_name'];
                $property_active_table_name = $classconfig['property_table_name'];
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
                if ($diff > 3600 * 24 * 45) {
                    $new_starttime = strtotime("+45 day", $old_timestamp);
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
                //10 days before
                $todayDate = new \DateTime(); // For today/now, don't pass an arg.
                 $todayDate->modify("-10 day");
                 $desiredDate =  $todayDate->format("Y-m-d H:i:s");
                 $desiredDate = explode(' ', $desiredDate);
                 $date_query = $desiredDate[0] . 'T' . $desiredDate[1].'+';
                $rets_query = "((Status=|U),(Timestamp_sql=".$date_query."))";
                //$rets_query = "((Status=|U),(Timestamp_sql=2022-04-07T17:55:21+))";
                //echo "\n retsQuery = ".$rets_query;
                //$rets_query = "((Status=|U),(Timestamp_sql=2020-04-27T17:55:21-2020-07-13T17:55:21))";
                //$rets_query = "((Status=|U),$date_query)";
                //  $rets_query = "(Ml_num=W5571749)";
                //$rets_query = "((Status=|U))";
                $cron_update_data = array(
                    'properties_download_start_time' => $property_query_start_time,
                    'steps_completed' => 2,
                    'rets_query' => $rets_query
                );
                $cron_cond = array(
                    'id' => $this_cron_log_id
                );
                echo "\n retsQuery = " . $rets_query;
                $res = DB::table($cron_tablename)->where('id', $this_cron_log_id)->update($cron_update_data);
                $search = $rets->SearchQuery($property_resource, $className, $rets_query, array('Count' => 2, 'Format' => 'COMPACT-DECODED', "UsePost" => 1));
                $total_property_count = $rets->TotalRecordsFound($search); //100;
                echo "\n Class : = $className and total count = $total_property_count";
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
                    //$txt = "<table border='1'><thead><tr><th>Sr.No.</th><th>MLS#</th><th>Address</th><th>Status</th><th>PropertyType</th><th>Action</th></tr></thead>";
                    if ($rets->NumRows($search_chunks) > 0) {
                        $sl = 0;
                        while ($record = $rets->FetchRow($search_chunks)) {
                            $properties_inserted_in_db++;
                            //echo $record[$mls_key];
                            $ml_num = $record[$key_field];
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
                            $main_data = array();
                            foreach ($property_data_mapping as $db_key => $mls_key) {
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
				                $rets_property_data["LastStatus"] = $record["Lsc"];
                                $rets_property_data["ExpiredDate"] = $record["Unavail_dt"];
                            }

                            $rets_property_data['mls_no'] = $curr_mls_id;
                            $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->get();
                            $property_data['property_last_updated'] = date('Y-m-d H:i:s');
                            $property_data["SlugUrl"] = get_slug_url($property_data);
                            //echo "\n property_table_name  =>  $property_table_name";
                            // for address
                            $property_data["StandardAddress"] = get_address($record);
                            $rets_property_data["StandardAddress"] = get_address($record);
                            $key_field_resi = "Ml_num";
                            $img_count = 0;
                            $img_count_tried = 0;
                            if (count($rets_data) > 0) {
                                $rets_data = DB::table($property_table_name)->where($key_field, $record[$key_field])->update($property_data);
                                $update_prop++;
                                $rets_property_data["updated_time"] = date("Y-m-d H:i:s");
                                $rpd_operation = "<span style='color:green;'>Update</span>";
                            } else {
                                $insertion_prop++;
                                $property_data['property_insert_time'] = date('Y-m-d H:i:s');
                                $property_data["image_downloaded"] = $img_count;
                                $property_data["image_download_tried"] = $img_count_tried;
                                DB::table($property_table_name)->insertGetId($property_data);
                                $rets_property_data["inserted_time"] = date("Y-m-d H:i:s");
                                $rpd_operation = "<span style='color:green;'>Insert</span>";
                            }
                            //for images transfer from active to purged table
                            $sold_data_resi = DB::table($property_active_table_name)->select("image_downloaded")->where($key_field_resi, $ml_num)->get();
                            if (isset($sold_data_resi) && count($sold_data_resi) > 0 ) {
                                if (isset($sold_data[0]->image_downloaded)) {
                                    $img_count = $sold_data_resi[0]->$image_downloaded ? $sold_data_resi[0]->$image_downloaded:"";
                                }
                                echo "$ml_num $property_table_name Field Added \n";
                            }
                            if ($img_count != 0 && $img_count == 1) {
                                $sold_data_image = DB::table("RetsPropertyDataImages")->where('listingID', $ml_num)->get();
                                if (count($sold_data_image) > 0) {
                                    $rets_purged_arr_img = [
                                        "mls_no"=>"mls_no",
                                        "listingID"=>"listingID",
                                        "image_path"=>"image_path",
                                        "s3_image_url"=>"s3_image_url",
                                        "image_name"=>"image_name",
                                        "downloaded_time"=>"downloaded_time",
                                        "is_uploaded_by_agent"=>"is_uploaded_by_agent",
                                        "updated_time"=>"updated_time",
                                        "image_last_tried_time"=>"image_last_tried_time",
                                        "created_at"=>"created_at",
                                        "updated_at"=>"updated_at",
                                    ];
                                    $key_field_img ="listingID";
                                    $checkRpdImage = RetsPropertyDataImagesSold::where("listingID", $ml_num)->get();
                                    if (collect($checkRpdImage)->all() != []) {
                                        RetsPropertyDataImagesSold::where("listingID", $ml_num)->delete();
                                    }
                                    $curr_date = date("Y-m-d H:i:s");
                                    foreach ($sold_data_image as $key => $valu) {
                                        $sold_Data_insert = array();
                                        foreach ($rets_purged_arr_img as $key => $value) {
                                            if (isset($valu->$value)) {
                                                $sold_Data_insert[$key]=$valu->$value;
                                            }
                                        }
                                        RetsPropertyDataImagesSold::create($sold_Data_insert);
                                        DB::table($property_table_name)->where('Ml_num', $ml_num)->update(['image_downloaded' => 1, "image_downloaded_time" => $curr_date]);
                                    }
                                    DB::table("RetsPropertyDataImages")->where("listingID", $ml_num)->delete();
                                }
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
                            $get_sqft = getsqft_min_max($property_data);
                            if (count($get_sqft) > 0) {
                                $rets_property_data["SqftMin"] = $get_sqft["SqftMin"];
                                $rets_property_data["SqftMax"] = $get_sqft["SqftMax"];
                                $rets_property_data["sqftFlag"] = $get_sqft["sqftFlag"];
                            }
                            $key_field_rets = "ListingId";
                            $rets_map = array(
                                "Thumbnail_downloaded" => "Thumbnail_downloaded",
                                "ImageUrl" => "ImageUrl",
                                "geocode" => "geocode",
                                "Latitude" => "Latitude",
                                "Longitude" => "Longitude"
                            );
                            $sold_data = DB::table("RetsPropertyData")->select("Thumbnail_downloaded","ImageUrl","geocode","Latitude","Longitude")->where($key_field_rets, $ml_num)->get();
                            if (count($sold_data) > 0) {
                                foreach ($rets_map as $key => $value) {
                                    if (isset($sold_data[0])) {
                                        $rets_property_data[$key]=$sold_data[0]->$value ? $sold_data[0]->$value:"";
                                    }
                                }
                                echo "$ml_num Rets Field Added \n";
                            }
                            $result = RetsPropertyDataPurged::updateOrCreate(["ListingId" => $record[$key_field]], $rets_property_data);
                            echo "\n property inserted" . $properties_inserted_in_db;
                            $addressData["StandardAddress"] = $rets_property_data["StandardAddress"];
                            $addressData["ZipCode"] = $rets_property_data["Zip"];
                            $addressData["City"] = $rets_property_data["Municipality"];
                            $addressData["Area"] = $rets_property_data["Area"];
                            $addressData["County"] = $rets_property_data["County"];
                            $addressData["Status"] = $rets_property_data["Status"];
                            $addressData["Community"] = $rets_property_data["Community"];
                            $addressResult = PropertyAddressData::updateOrCreate(['ListingId' => $rets_property_data["ListingId"]], $addressData);
                            // for Rets Active data Parts
                            $rets_active_data = DB::table($property_active_table_name)->where($key_field, $record[$key_field])->get();
                            if (count($rets_active_data) > 0) {
                                $single_rets_active_data = collect($rets_active_data)->first();
                                $batables[] = array(
                                    'addressfullhere' => $single_rets_active_data->Addr,
                                    'listingId' => $single_rets_active_data->Ml_num,
                                    'operation' => "<span style='color:red;'>Deleted</span>",
                                    'price' => $single_rets_active_data->Lp_dol,
                                    'status' => $single_rets_active_data->Status,
                                    'propertyType' => $className
                                );
                                echo "\n property deleted = " . $record[$key_field];
                                $delete_query = "DELETE FROM `" . $property_active_table_name . "`where Ml_num = '" . $record[$key_field] . "'";
                                DB::delete($delete_query);
                                $delete_rpd_query = "DELETE FROM `RetsPropertyData` where ListingId = '" . $record[$key_field] . "'";
                                DB::delete($delete_rpd_query);
                            }
                            $retsactive_data = DB::table("RetsPropertyData")->where("ListingId", $record[$key_field])->get();
                            if (count($retsactive_data) > 0 && !isset($rets_active_data)) {
                                $rets_data = DB::table("RetsPropertyData")->where("ListingId", $ml_num)->update(["Status" => "P"]);
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
                        echo "\n total_property_updated :: $update_prop";
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
            } else {
                $txt .= "<tr>";
                $txt .= "<td><h1>No Listing Found</h1></td>";
                $txt .= "</tr>";
            }
        }
        //$this->deleteSoldProperties();
        $curr_date_end = date('Y-m-d H:i:s');
        $superAdminEmail = getSuperAdmin();
        sendEmail("SMTP", env('MAIL_FROM'), $superAdminEmail, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), 'Sold VOW Listing Import Cron FINISHED - ' . $curr_date_end, $txt, "Sold Listings controller ImportListing", "", env('RETSEMAILS'));
        updateHomePageJson();
        updateAutoSuggestionJson();
	$sitemap = new GenerateXMLFile();
        $sitemap->generateSoldListingXml();
        // execute frontend commands
        $commands = "./frontendRestartScript.sh";
        shell_exec($commands);
	$call = new ImagesControllerSoldVOW();
        $call->getThumbnail();
    }

    public function deleteSoldProperties()
    {
        $retsPropDataQuery = "DELETE from RetsPropertyData where Status='U';";
        $retsPropDataResiQuery = "DELETE from RetsPropertyDataResi where Status='U';";
        $retsPropDataCommQuery = "DELETE from RetsPropertyDataComm where Status='U';";
        $retsPropDataCondoQuery = "DELETE from RetsPropertyDataCondo where Status='U';";
        DB::delete($retsPropDataQuery);
        DB::delete($retsPropDataResiQuery);
        DB::delete($retsPropDataCommQuery);
        DB::delete($retsPropDataCondoQuery);
    }

}
