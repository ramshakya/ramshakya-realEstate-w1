<?php

namespace App\Http\Controllers\importListings;


use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\DB;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataImage;

class ImageCheckSold extends Controller
{
    public $mls_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function imageCheck()
     {
        $all_mls = config('mls_config.mls_login_parameter');
        $dir = "/var/www/html/wedu/storage/app/public/img/mls_images/";
        $dir_sold = "/var/www/html/wedu/storage/app/public/img/mls_images/soldProperty/";
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $arr = array();
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            foreach ($property_class as $className => $classconfig) {
                if ($className == "CommercialProperty") continue;
                $curr_date = date('Y-m-d H:i:s');
                echo "\n $className";
                echo "\n".$property_table_name = $classconfig['purge_property_table_name'];
                $key_field = $classconfig['key_field'];
                echo "<pre>";
                $properties_data_count = DB::table($property_table_name)->select($key_field)->where("image_downloaded","!=", 1)->count();
                $start_index = 0;
                $limit       = 1000;
                $lc          = (($properties_data_count - $start_index) / $limit);
                $lcp = 0;
                $deleted_count_purged = 1;
                $count_img = 0;
                for ($lcp = 0; $lcp <= $lc; $lcp++) {
                    $offset = $start_index + $lcp * $limit;
                    echo "Limit $limit and skip $offset \n <br>";
                    $properties_data = DB::table($property_table_name)->select($key_field)->where("image_downloaded","!=", 1)->limit($limit)->skip($offset)->get();
                    if (count($properties_data) > 0) {
                        foreach ($properties_data as $key => $value) {
                            $mls_num = $value->$key_field;
                            echo "\n $mls_num:: MLS# <br>";
                            $img_dir = $dir .$mls_num."/";
                            if (is_readable($img_dir)){
                                if(count(scandir($img_dir)) > 4){
                                    echo "This MLS is updated \n $img_dir";
                                    $count_img++;
                                    DB::table($property_table_name)->where($key_field, $mls_num)->update(['image_downloaded' => 1, "image_downloaded_time" => $curr_date]);
                                }
                            }
                            $img_dir_sold = $dir_sold .$mls_num."/";
                            if (is_readable($img_dir_sold)){
                                if(count(scandir($img_dir_sold)) > 4){
                                    echo "This MLS is updated \n $img_dir_sold";
                                    $count_img++;
                                    DB::table($property_table_name)->where($key_field, $mls_num)->update(['image_downloaded' => 1, "image_downloaded_time" => $curr_date]);
                                }
                            }
                        }
                    }
                }
                echo "\n Table :: ".$property_table_name ." Count :: $count_img";
            }
            exit;
        }
    }

}
