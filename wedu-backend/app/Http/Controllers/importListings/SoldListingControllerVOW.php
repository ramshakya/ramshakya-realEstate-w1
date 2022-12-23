<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\DB;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Schema;


class SoldListingControllerVOW extends Controller
{
    //
    public $mls_config;
    public $images_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function importPropertyListing()
    {
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
        $dir = "/var/www/html/wedu/storage/app/public/img/mls_images/";
        $dir_sold = "/var/www/html/wedu/storage/app/public/img/mls_images/soldProperty/";
        $all_properties= array("RetsPropertyDataResiPurged","RetsPropertyDataCondoPurged","RetsPropertyDataCommPurged");
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
                $result = DB::table($table_purged)->select("Ml_num")->limit($limit)->skip($offset)->get();
                $key_field="ListingId";
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        echo "Listing Id : ".$ml_num = $val->Ml_num;
                        $sold_data_image = DB::table("RetsPropertyDataImages")->where('listingID', $ml_num)->get();
                        if (count($sold_data_image) > 0) {
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
                                DB::table($table_purged)->where('Ml_num', $ml_num)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                            }
                            $activeProperty =  DB::table('RetsPropertyData')->select("ListingId")->where('ListingId',$ml_num)->count();
                            if ($activeProperty == 0) {
                                DB::table("RetsPropertyDataImages")->where("listingID", $ml_num)->delete();
                            }
                        }
                        $img_dir = $dir .$ml_num."/";
                        if (is_readable($img_dir)){
                            if(count(scandir($img_dir)) > 4){
                                echo "This MLS is updated \n $img_dir";
                                DB::table($table_purged)->where("Ml_num", $ml_num)->update(['image_downloaded' => 1, "image_downloaded_time" => $curr_date]);
                            }
                        }
                        $img_dir_sold = $dir_sold .$ml_num."/";
                        if (is_readable($img_dir_sold)){
                            if(count(scandir($img_dir_sold)) > 4){
                                echo "This MLS is updated \n $img_dir_sold";
                                DB::table($table_purged)->where("Ml_num", $ml_num)->update(['image_downloaded' => 1, "image_downloaded_time" => $curr_date]);
                            }
                        }
                    }
                }
            }
        }
    }
}
