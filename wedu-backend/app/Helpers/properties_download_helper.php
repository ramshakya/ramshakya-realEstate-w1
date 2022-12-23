<?php

use App\Models\MlsSettings\PropertiesCronLog;

function get_start_time_for_cron($table_name, $file_name,$mls_no=1,$className="")
{
    //$ci =& get_instance();
    //$ci->load->model('properties_res_model');
    //$ci->load->model('cron_log_model');
    $cron_log_table_name = "PropertiesCronLog";
    $ci = new PropertiesCronLog();
    $res_num = $ci->get_properties_end_time_for_last_sucess_cron($file_name, false, $cron_log_table_name,$mls_no,$className)->get();
    //echo $ci->db->last_query();
    if (!$res_num->all()) {
        // $property_query_start_time = strtotime("-2 hours", time());
        $property_query_start_time = strtotime("-30 Days", time());
        //$property_query_start_time = strtotime("-1 year", time());
        $property_query_start_time = date("Y-m-d H:i:s", $property_query_start_time);
        $status = 'start';
    } else {
        // Get last success cron property end time //
        $resN = $ci->get_properties_end_time_for_last_sucess_cron($file_name, true , $cron_log_table_name,$mls_no,$className)->get();
        $last_success_cron_end_time = $resN[0]['properties_download_end_time'];
        if ($last_success_cron_end_time == '' || $last_success_cron_end_time == "0000-00-00 00:00:00" || $last_success_cron_end_time == "0000-00-00") {
            $property_query_start_time = strtotime("-1 year", time());
            $property_query_start_time = date("Y-m-d H:i:s", $property_query_start_time);
            $status = 'start';
        } else{
            $property_query_start_time = $last_success_cron_end_time;
            $status = 'incremental';
        }
    }
    $property_query_start_time_array['time'] = $property_query_start_time ;
    $property_query_start_time_array['status'] = $status ;
    return $property_query_start_time_array;
}
