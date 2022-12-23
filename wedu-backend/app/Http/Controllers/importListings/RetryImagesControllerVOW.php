<?php

namespace App\Http\Controllers\importListings;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataImage;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RetryImagesControllerVOW extends Controller
{
    public $mls_config;
    public $rets_query_array;
    public $cron_log_model;
    public $images_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        //$this->cron_log_model = $cron_log_model;
    }
    public function imageImport()
    {
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "retryImagesCronVow";
        $image_table_name       = "rets_property_data_image";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        // For all MLSs - Starts
        $txt = "<table>";
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = 'Treb';//$curr_mls['mls_name'];
            echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            //$txt .= "<td>Started for " . $curr_mls_name . "</td>";
            if ($property_class === null)
                continue;
            $imageTables = [];
            foreach ($property_class as $className => $classconfig) {
                $curr_date = date('Y-m-d H:i:s');
                echo "\n $className";
                //$txt .= "<td>$className</td>";
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterVOW');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                $properties_data = DB::table($property_table_name)->select(['id','Ml_num','image_download_tried','image_downloaded','Status'])->where("image_downloaded","=",0)->where('Status',"=","A")->get();
                Log::alert("images count for retry started => ".collect($properties_data)->count());
                $properties_count = collect($properties_data)->count();
                echo "\n total Properties count = ".$properties_count;
                foreach ($properties_data as $key => $property_data) {
                    $txt .= "<tr>";
                    $mlsId = $property_data->$key_field;
                    echo "\n mlsId = ".$mlsId;
                    $image_download_tried = $property_data->image_download_tried;
                    $curr_date = date('Y-m-d H:i:s');
                    $img_found = 0;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '*');
                    $showS3 = "";
                    $showFile = "";
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===".$photo_count;
                    $photo_number = 0;
                    Log::alert("images left for retry => ".$properties_count--);
                    echo "\n images left for retry ===".$properties_count--;
                    if (count($photos) > 0) {
                        $checkRpdImage = RetsPropertyDataImage::where("listingID",$mlsId)->get();
                        if (collect($checkRpdImage)->all() != []) {
                            RetsPropertyDataImage::where("listingID",$mlsId)->delete();
                        }
                        //if (false) {
                        $file = "";
                        $upload_dir = "/mls_images/".$mlsId."/";
                        $img_found = 1;
                        $photoArray = array();
                        $s3photoArray = array();
                        echo "<br>$property_table_name";
                        foreach ($photos as $key => $photo) {
                            if ($photo["Success"]) {
                                $img_dir = $upload_dir."Photo-" . $mlsId . "_" . $key . ".jpeg";
                                $dir_name = $img_dir;
                                //S3
                                //$success = file_put_contents($file, $img);
                                //$img_n = file_get_contents($file);
                                $img_n = $photo["Data"];
                                try {
                                    //Storage::disk('s3')->put($img_dir, $img_n, "public");
                                    Storage::disk('public')->put($img_dir, $img_n, "public");
                                    $photo_number++;
                                } catch (\Exception $exception) {
                                    return response("Something went wrong", $exception->getStatusCode());
                                }
                                $fileUrl = Storage::disk('public')->url($img_dir);
                                echo "<a href='$fileUrl'><p>$fileUrl</p></a>";
                                $update_data = array(
                                    "mls_no" => $curr_mls_id,
                                    "listingID" => $mlsId,
                                    "image_path" => $img_dir,
                                    "s3_image_url" => $img_dir,
                                    "image_name" => $dir_name,
                                    "downloaded_time" => $curr_date,
                                    "updated_time" => $curr_date,
                                    "image_last_tried_time" => $curr_date,
                                );
                                if ($img_found) {
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => DB::raw('image_downloaded+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);

                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    if ($photo_number == 1){
                                        $imageTables[] = [
                                            "propertyTableName" => $property_table_name,
                                            "imagesInserted" => "Images Inserted",
                                            "listingId" => $mlsId,
                                            "fileName" => $file_name,
                                            "imageUrl" => $fileUrl,
                                        ];
                                        //$rpd_update = RetsPropertyData::where("ListingId",$mlsId)->update(["ImageUrl" => $fileUrl]);
                                    }
                                } else {
                                    //$txt .= "<td style='border:1px solid black'><span style='color:red;'>Not Uploaded </span>" . $mlsId."</td>";
                                    $update_data["is_download"] = 0;
                                    $image_download_tried = $image_download_tried + 1;
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                }
                                // check before image downloaded or not
                                RetsPropertyDataImage::create($update_data);
                                //Log::info("Images are created");

                                $txt .= "</tr>";
                            } else {
                                $update_data["is_download"] = 0;
                                $image_download_tried = $image_download_tried + 1;
                                //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                Log::warning("No image data found =>" . $photo['Data']);
                            }
                        }
                        //RetsPropertyDataImageMongo::updateOrCreate(["listingID" => $mlsId], $update_data);
                    } else {
                        echo "\n images not found";
                        $image_download_tried = $image_download_tried + 1;
                        $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                        echo "\n update value = " . $properties_data;
                        Log::warning("No image data found");
                    }

                }
                // exit;
            }
            if (count($imageTables) > 0) {
                $txt .= "<table border=1>";
                $bacounter = 0;
                $txt .= "<thead>
   <tr>
      <th>Sr. No</th>
      <th>Property Table Name</th>
      <th>Image Inserted</th>
      <th>Listing ID</th>
      <th>File Name</th>

      <th>Image URL</th>
   </tr>
</thead>";
                foreach ($imageTables as $bagent) {
                    $bacounter++;
                    $txt .= "<tr>";
                    $txt .= "<td>" . $bacounter . "</td>";
                    $txt .= "<td>" . $bagent['propertyTableName'] . "</td>";
                    $txt .= "<td>" . $bagent['imagesInserted'] . "</td>";
                    $txt .= "<td>" . $bagent['listingId'] . "</td>";
                    $txt .= "<td>" . $bagent['fileName'] . "</td>";
                    $txt .= "<td>" . $bagent['imageUrl'] . "</td>";
                    $txt .= "</tr>";
                }
                $txt .= "</table>";
                $subject = "Images Insertion";
                $superAdminEmail = getSuperAdmin();
                sendEmail("SMTP",env('MAIL_FROM'),$superAdminEmail,env('ALERT_CC_EMAIL_ID'),env('ALERT_BCC_EMAIL_ID'), $subject,$txt,"RetryImagesController","3",env('RETSEMAILS'));

            }
            //echo $txt;
        }
    }

}
