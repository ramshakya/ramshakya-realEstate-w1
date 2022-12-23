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

class ImagesControllerIDX extends Controller
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
        $file_name = "imagesCronIdx";
        $image_table_name       = "rets_property_data_image";
        /************ Entry in DB for This Cron Run time **********/
        $curr_date = date('Y-m-d H:i:s');
        // For all MLSs - Starts
        $txt = "<table>";
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = $curr_mls['mls_name'];
            echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            //$txt .= "<td>Started for " . $curr_mls_name . "</td>";
            foreach ($property_class as $className => $classconfig) {
                $curr_date = date('Y-m-d H:i:s');
                echo "<br/>$className";
                //$txt .= "<td>$className</td>";
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterIDX');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                $txt .= "<tr>
                            <th style='border:1px solid black'>Table Name</th>
                            <th style='border:1px solid black'>Action</th>
                            <th style='border:1px solid black'>ListingId</th>
                            <th style='border:1px solid black'>Image name</th>
                            <th style='border:1px solid black'>S3 Url </th
                            <th style='border:1px solid black'>Images Url</th>
                         </tr>";
                $properties_data = DB::table($property_table_name)->where("image_downloaded","=",0)->orWhereNull("image_downloaded")->limit(100)->get();
                foreach ($properties_data as $key => $property_data) {
                    $txt .= "<tr>";
                    $mlsId = $property_data->$key_field;
                    $curr_date = date('Y-m-d H:i:s');
                    $img_found = 0;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '*');
                    $showS3 = "";
                    $showFile = "";
                    $photo_count = collect($photos)->count();
                    echo "<h3>Photo Count = </h3> ==".$photo_count;
                    if (count($photos) > 0) {
                        $file = "";
                        $upload_dir = "/mls_images/";
                        $img_found = 1;
                        $photoArray = array();
                        $s3photoArray = array();
                        echo "<br>$property_table_name";
                        foreach ($photos as $key => $photo) {
                            if ($photo["Success"]) {
                                $upload_dir_img = storage_path() . $upload_dir;
                                if (!File::exists($upload_dir_img)) {
                                    Log::info("Creating directory for image [mls_images]");
                                    File::makeDirectory($upload_dir_img, 0755, true, true);
                                }
                                $img_dir = "Photo-". $mlsId . "_" . $key . ".jpeg";
                                $file = $upload_dir_img . $img_dir;
                                //$dir_name = $upload_dir . $img_dir;
                                $dir_name = $img_dir;
                                //S3
                                //$success = file_put_contents($file, $img);
                                //$img_n = file_get_contents($file);
                                $img_n = $photo["Data"];
                                try {
                                    Storage::disk('s3')->put($img_dir, $img_n, "public");
                                } catch (S3Exception $exception) {
                                    $errorData["message1"] = $exception->getAwsErrorMessage();
                                    $errorData["message2"] = $exception->getAwsErrorCode();
                                    return response($errorData, $exception->getStatusCode());
                                }
                                $fileUrl = Storage::disk('s3')->url( $img_dir);
                                echo "<a href='$fileUrl'><p>$fileUrl</p></a>";

                                $update_data = array(
                                    "mls_no" => $curr_mls_id,
                                    "listingID" => $mlsId,
                                    "image_directory" => $upload_dir,
                                    "image_path" => $upload_dir,
                                    "image_url" => $dir_name,
                                    "s3_image_url" => $fileUrl,
                                    "image_name" => $dir_name,
                                    "downloaded_time" => $curr_date,
                                    "is_download" => 1,
                                    "is_resized1" => 0,
                                    "is_resized2" => 0,
                                    "is_resized3" => 0,
                                    "mls_order" => $curr_mls_id,
                                    "updated_time" => $curr_date,
                                    "image_last_tried_time" => $curr_date,
                                    "property_id" => $property_data->id
                                );
                                if ($img_found) {
                                    $txt .= "<td style='border:1px solid black'>$property_table_name</td>";
                                    $txt .= "<td style='border:1px solid black'><span style='color:green;'> Images Inserted </span></td>";
                                    $txt .= "<td style='border:1px solid black'>".$update_data['listingID']."</td>";
                                    $txt .= "<td style='border:1px solid black'>".$file_name."</td>";
                                    $txt .= "<td style='border:1px solid black'>".$fileUrl."</td>";
                                    $txt .= "<td style='border:1px solid black'>".$update_data['image_url']."</td>";
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => DB::raw('image_downloaded+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    $rpd_update = RetsPropertyData::where("ListingId",$mlsId)->update(["ImageUrl" => $fileUrl]);
                                } else {
                                    $txt .= "<td style='border:1px solid black'><span style='color:red;'>Not Uploaded </span>" . $mlsId."</td>";
                                    $update_data["is_download"] = 0;
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                }
                                //Log::info("Images are created");
                                RetsPropertyDataImage::create($update_data);
                                $txt .= "</tr>";
                            } else {
                                Log::warning("No image data found =>" . $photo['Data']);
                            }
                        }
                        //RetsPropertyDataImageMongo::updateOrCreate(["listingID" => $mlsId], $update_data);
                    } else {
                        Log::warning("No image data found");
                    }

                }
                // exit;
            }
            $subject = "Images Insertion";
            //sendEmail("SMTP",env('SUPERBBROKERFROM'),env('SUPERBBROKERTO'),env('ALERT_CC_EMAIL_ID'),env('ALERT_BCC_EMAIL_ID'), $subject,$txt);
            echo $txt;
        }
    }
    public function deleteImage()
    {
        $tables = array(
            'rets_property_data_sf',
            'rets_property_data_cc',
            'rets_property_data_ld',
            'rets_property_data_mf',
            'rets_property_data_rn',
            'rets_property_data_mh',
        );
        $properties_data =  RetsPropertyDataImage::get();
        foreach ($properties_data as $key => $value) {
            $imgs = json_decode($value['s3_image_url']);
            $id = $value['id'];
            foreach ($imgs as $key => $img) {
                $image_name = str_replace("https://cidare-mls-dev.s3.s3sys.com/", "", $img);
                try {
                    $res = Storage::disk('s3')->delete($image_name);
                    print("==>>$res");
                } catch (S3Exception $exception) {
                    $errorData["message1"] = $exception->getAwsErrorMessage();
                    $errorData["message2"] = $exception->getAwsErrorCode();
                    return response($errorData, $exception->getStatusCode());
                }
                echo "Deleted==>$img";
            }
            RetsPropertyDataImage::where('id', $id)->update(['s3_image_url' => json_encode([])]);
        }
    }
}
