<?php

namespace App\Http\Controllers\importListings;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GetImagesSize extends Controller
{
    public $mls_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function getSize()
     {
        if (!Schema::hasTable('getImagesSize')) 
        {
            Schema::create('getImagesSize', function($table){
                $table->increments('id');
                $table->string('MLS', 40);
                $table->string('Status', 40);
                $table->string('ImagesSize', 250);
           });
        }
        $dir = "/var/www/html/wedu/storage/app/public/img/mls_images/";
        $dir_sold = "/var/www/html/wedu/storage/app/public/img/mls_images/soldProperty/";
        $all_properties= array("RetsPropertyDataResiPurged","RetsPropertyDataCondoPurged","RetsPropertyDataCommPurged","RetsPropertyDataResi","RetsPropertyDataCondo","RetsPropertyDataComm");
        $curr_date = date('Y-m-d H:i:s');
        foreach ($all_properties as $table_purged) {
            $properties_data_count = DB::table($table_purged)->select("Ml_num")->count();
            $start_index = 0;
            $limit       = 1000;
            $lc          = (($properties_data_count - $start_index) / $limit);
            $lcp = 0;
            $deleted_count_purged = 1;
            $count_img = 0;
            for ($lcp = 0; $lcp <= $lc; $lcp++) {
                $offset = $start_index + $lcp * $limit;
                echo "Limit $limit and skip $offset \n <br>";
                $result = DB::table($table_purged)->select("Ml_num","Status")->limit($limit)->skip($offset)->get();
                $key_field="ListingId";
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        echo "\n Listing Id : ".$ml_num = $val->Ml_num;
                        echo "\n Listing Status : ".$status = $val->Status;
                        $img_dir = $dir .$ml_num."/";
                        if (is_readable($img_dir)){
                            if(count(scandir($img_dir)) > 2){
                                $ImageSize =  $this->getImageSize($img_dir);
                                $ImageSize = $this->format_Size($ImageSize);
                                echo "This MLS Size is \n $ImageSize";
                                DB::table("getImagesSize")->insert(['MLS' => $ml_num, "Status" => $status,"ImagesSize" => $ImageSize]);
                            }
                        }
                        $img_dir_sold = $dir_sold .$ml_num."/";
                        if (is_readable($img_dir_sold)){
                            if(count(scandir($img_dir_sold)) > 2){
                                $ImageSize = $this->format_Size($this->getImageSize($img_dir_sold));
                                echo "This MLS Size is \n $ImageSize";
                                DB::table("getImagesSize")->insert(['MLS' => $ml_num, "Status" => $status,"ImagesSize" => $ImageSize]);
                            }
                        }
                    }
                }
            }
        }

    }
    function getImageSize($set_dir = null)
    {
        $set_total_size = 0;
        $set_count = 0;
        $set_dir_array = scandir($set_dir);
        foreach($set_dir_array as $key=>$set_filename)
        {
            if($set_filename!=".." && $set_filename!=".")
            {
                if(is_dir($set_dir."/".$set_filename))
                {
                    $new_foldersize = $this->getImageSize($set_dir."/".$set_filename);
                    $set_total_size = $set_total_size+ $new_foldersize;
                }
                else if(is_file($set_dir."/".$set_filename))
                {
                    $set_total_size = $set_total_size + filesize($set_dir."/".$set_filename);
                    $set_count++;
                }
            }
        }
        return $set_total_size;
    }

    function format_Size($set_bytes)
    {
        $set_kb = 1024;
        $set_mb = $set_kb * 1024;
        $set_gb = $set_mb * 1024;
        $set_tb = $set_gb * 1024;
        if (($set_bytes >= 0) && ($set_bytes < $set_kb))
        {
            return $set_bytes . ' B';
        }
        elseif (($set_bytes >= $set_kb) && ($set_bytes < $set_mb))
        {
            return ceil($set_bytes / $set_kb) . ' KB';
        }
        elseif (($set_bytes >= $set_mb) && ($set_bytes < $set_gb))
        {
                return ceil($set_bytes / $set_mb) . ' MB';
        }
        elseif (($set_bytes >= $set_gb) && ($set_bytes < $set_tb))
        {
            return ceil($set_bytes / $set_gb) . ' GB';
        }
        elseif ($set_bytes >= $set_tb)
        {
            return ceil($set_bytes / $set_tb) . ' TB';
        } else {
            return $set_bytes . ' Bytes';
        }
    }
}
