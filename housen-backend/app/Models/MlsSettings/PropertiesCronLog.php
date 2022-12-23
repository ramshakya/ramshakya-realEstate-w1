<?php

namespace App\Models\MlsSettings;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertiesCronLog extends Model
{
    use HasFactory;
    protected $table = "PropertiesCronLog";
    protected $fillable = [
        "cron_file_name",
        "property_class",
        "rets_query",
        "cron_start_time",
        "cron_end_time",
        "properties_download_start_time",
        "properties_download_end_time",
        "properties_count_from_mls",
        "properties_count_actual_downloaded",
        "property_inserted",
        "property_updated",
        "steps_completed",
        "force_stop",
        "mls_no",
        "success"
    ];


    public function get_properties_end_time_for_last_sucess_cron($cron_file_name,$get_time=true,$table_name="properties_cron_log",$mls_no=1,$className="") {

        //dd($cron_file_name,$get_time,$table_name,$mls_no,$className);
        $fields = "properties_download_end_time";
        $conditions = array('success'=>1,'cron_file_name'=>$cron_file_name,'cron_end_time <>'=>'0000-00-00 00:00:00','mls_no'=>$mls_no,'property_class'=>$className );
        DB::statement("SET SQL_MODE=''");
        DB::enableQueryLog();
        $query =  PropertiesCronLog::where("success",1)
            ->where("cron_file_name",'=',$cron_file_name)
            ->where("cron_end_time",'<>','0000-00-00 00:00:00')
            ->where("mls_no",'=',$mls_no)
            ->where("property_class",'=',$className)
            ->orderBy("id", "desc")
            ->select($fields)
            ->limit(1);
        //->get();

        $queryy = DB::getQueryLog();
        $queryy = end($queryy);
        return $query;
        //dd($queryy);
        //$query = $this->db->get($table_name);
        /*if($get_time)
            return $query->result_array();
        else
            return $query->num_rows();*/
    }

    public function get_properties_end_time_for_last_sucess_cron_for_mls_ids($file_name,$className,$curr_mls_id) {

        //dd($cron_file_name,$get_time,$table_name,$mls_no,$className);
        $fields = "properties_download_end_time";
        // $conditions = array('success'=>1,'cron_file_name'=>$file_name,'property_class'=>$className,'cron_end_time <>'=>'0000-00-00 00:00:00','mls_no'=>$curr_mls_id);
        DB::statement("SET SQL_MODE=''");
        // DB::enableQueryLog();
        $query =  DB::table('PropertiesCronLog')->where("success",1)
            ->where("cron_file_name",'=',$file_name)
            ->where("cron_end_time",'<>','0000-00-00 00:00:00')
            ->where("properties_download_end_time",'<>','0000-00-00 00:00:00')
            ->where("mls_no",'=',$curr_mls_id)
            ->where("property_class",'=',$className)
            ->orderBy("id", "desc")
            ->select($fields)
            ->limit(1);
        //->get();

        // $queryy = DB::getQueryLog();
        //$queryy = end($queryy);
        return $query;
        //dd($queryy);
        //$query = $this->db->get($table_name);
        /*if($get_time)
            return $query->result_array();
        else
            return $query->num_rows();*/
    }
}
