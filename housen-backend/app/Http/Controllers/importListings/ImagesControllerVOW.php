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
use Intervention\Image\Image;

class ImagesControllerVOW extends Controller
{
    public $mls_config;
    public $rets_query_array;
    public $cron_log_model;
    public $images_config;

    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1128M');
        ini_set("upload_max_filesize","300M");
        //$this->cron_log_model = $cron_log_model;
    }

    public function imageImport()
    {
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "imagesCronVow";
        $image_table_name = "rets_property_data_image";
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
                if ($className == "CommercialProperty") continue;
                //todo:: need to remove
                //if ($className == "ResidentialProperty") continue;
                echo "\n $className";
                //$txt .= "<td>$className</td>";
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterIDX');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                $properties_data = DB::table($property_table_name)->where("image_downloaded", "=", 0)->where("image_download_tried", "<", 3)->where('Status', '=', "A")->limit(400)->orderBy('id', 'DESC')->get();
                foreach ($properties_data as $key => $property_data) {
                    $txt .= "<tr>";
                    $mlsId = $property_data->$key_field;
                    $image_download_tried = $property_data->image_download_tried;
                    $curr_date = date('Y-m-d H:i:s');
                    $img_found = 0;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '*');
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===" . $photo_count;
                    $photo_number = 0;
                    if (count($photos) > 0) {
                        $checkRpdImage = RetsPropertyDataImage::where("listingID",$mlsId)->get();
                        if (collect($checkRpdImage)->all() != []) {
                            //todo:: need to remove
                            RetsPropertyDataImage::where("listingID",$mlsId)->delete();
                        }
                        //if (false) {
                        $file = "";
                        if ($className == "CondoProperty") {
                            $upload_dir = "/mls_images/condo_property/mls_images/".$mlsId."/";
                        }else{
                            $upload_dir = "/mls_images/".$mlsId."/";
                        }
                        $img_found = 1;
                        $photoArray = array();
                        $s3photoArray = array();
                        echo "<br>$property_table_name";
                        foreach ($photos as $key => $photo) {
                            if ($key == 0){
                                $key = 90;
                            }
                            if ($photo["Success"]) {
                                $img_dir = $upload_dir."Photo-" . $mlsId . "_" . $key . ".jpeg";
                                $dir_name = $img_dir;
                                $img_n = $photo["Data"];
                                try {
                                    echo "\n = ".$mlsId;
                                    //Storage::disk('s3')->put($img_dir, $img_n, "public");
                                    Storage::disk('public')->put($img_dir, $img_n, "public");
                                    $photo_number++;
                                } catch (\Exception $exception) {
                                    print_r($exception);
                                    return response("Something went wrong", 200);
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
                                    if ($className == "CondoProperty") {
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date,"imagesMoved" => 2]);
                        }else{
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                        }
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    if ($photo_number == 1) {
                                        $imageTables[] = [
                                            "propertyTableName" => $property_table_name,
                                            "imagesInserted" => "Images Inserted",
                                            "listingId" => $mlsId,
                                            "fileName" => $file_name,
                                            "imageUrl" => $fileUrl,
                                        ];
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
                sendEmail("SMTP", env('MAIL_FROM'), env('ALERT_TO_MAIL'), env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $txt, "imagesControllerVow", "3", env('RETSEMAILS'));
            }
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
        $properties_data = RetsPropertyDataImage::get();
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


    public function getThumbnailBck()
    {
        $all_mls = config('mls_config.mls_login_parameter');
        $cron_tablename = "PropertiesCronLog";
        $file_name = "imagesCronVow";
        $image_table_name = "rets_property_data_image";
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
                $txt .= "<tr>
                            <th style='border:1px solid black'>Table Name</th>
                            <th style='border:1px solid black'>Action</th>
                            <th style='border:1px solid black'>ListingId</th>
                            <th style='border:1px solid black'>Image name</th>
                            <th style='border:1px solid black'>S3 Url </th
                            <th style='border:1px solid black'>Images Url</th>
                         </tr>";
                $properties_data = DB::table($property_table_name)->select(['id', 'Ml_num', 'image_download_tried', 'image_downloaded', 'Status'])->where("image_downloaded", "=", 0)->where('Status', '=', "A")->get();
                foreach ($properties_data as $key => $property_data) {
                    $txt .= "<tr>";
                    $mlsId = $property_data->$key_field;
                    $image_download_tried = $property_data->image_download_tried;
                    $curr_date = date('Y-m-d H:i:s');
                    $img_found = 0;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '1');
                    $showS3 = "";
                    $showFile = "";
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===" . $photo_count;
                    $photo_number = 0;
                    if (count($photos) > 0) {
                        //if (false) {
                        $file = "";
                        $upload_dir = "/mls_images/";
                        $img_found = 1;
                        $photoArray = array();
                        $s3photoArray = array();
                        echo "<br>$property_table_name";
                        foreach ($photos as $key => $photo) {
                            if ($photo["Success"]) {
                                /*$upload_dir_img = storage_path() . $upload_dir;
                                if (!File::exists($upload_dir_img)) {
                                    Log::info("Creating directory for image [mls_images]");
                                    File::makeDirectory($upload_dir_img, 0755, true, true);
                                }*/
                                $img_dir = "Photo-" . $mlsId . "_" . $key . ".jpeg";
                                //$file = $upload_dir_img . $img_dir;
                                //$dir_name = $upload_dir . $img_dir;
                                $dir_name = $img_dir;
                                //S3
                                //$success = file_put_contents($file, $img);
                                //$img_n = file_get_contents($file);
                                $img_n = $photo["Data"];
                                try {
                                    Storage::disk('s3')->put($img_dir, $img_n, "public");
                                    $photo_number++;
                                } catch (S3Exception $exception) {
                                    $errorData["message1"] = $exception->getAwsErrorMessage();
                                    $errorData["message2"] = $exception->getAwsErrorCode();
                                    return response($errorData, $exception->getStatusCode());
                                }
                                $fileUrl = Storage::disk('s3')->url($img_dir);
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
                                    $txt .= "<td style='border:1px solid black'>" . $update_data['listingID'] . "</td>";
                                    $txt .= "<td style='border:1px solid black'>" . $file_name . "</td>";
                                    $txt .= "<td style='border:1px solid black'>" . $fileUrl . "</td>";
                                    $txt .= "<td style='border:1px solid black'>" . $update_data['image_url'] . "</td>";
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => DB::raw('image_downloaded+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    if ($photo_number == 1) {
                                        $rpd_update = RetsPropertyData::where("ListingId", $mlsId)->update(["ImageUrl" => $fileUrl]);
                                    }
                                } else {
                                    $txt .= "<td style='border:1px solid black'><span style='color:red;'>Not Uploaded </span>" . $mlsId . "</td>";
                                    $update_data["is_download"] = 0;
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                }
                                //Log::info("Images are created");
                                //RetsPropertyDataImage::create($update_data);
                                $txt .= "</tr>";
                            } else {
                                Log::warning("No image data found =>" . $photo['Data']);
                            }
                        }
                        //RetsPropertyDataImageMongo::updateOrCreate(["listingID" => $mlsId], $update_data);
                    } else {
                        echo "\n images not found";
                        $image_download_tried = $image_download_tried + 1;
                        //$sql_image_downloaded_tried = 'UPDATE RetsPropertyDataResi SET image_download_tried = '.$image_download_tried.' and image_downloaded = 0   where Ml_num = "'.$mlsId.'"';
                        //$edit = DB::update($sql_image_downloaded_tried);
                        //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                        echo "\n update value = " . $properties_data;
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

    public function getThumbnail()
    {
        $all_mls = config('mls_config.mls_login_parameter');
        $txt = "<table>";
        foreach ($all_mls as $curr_mls_id => $curr_mls) {
            $curr_mls_name = 'Treb';//$curr_mls['mls_name'];
            echo "<hr><h3><b>Started for " . $curr_mls_name . "</b></h3>";
            $property_resource = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_resource');
            $property_class = config('mls_config.rets_query_array.' . $curr_mls_id . '.property_classes');
            if ($property_class === null)
                continue;
            foreach ($property_class as $className => $classconfig) {
                $curr_date = date('Y-m-d H:i:s');
                //if ($className == "ResidentialProperty") continue;
                //if ($className === "CommercialProperty") continue;
                //if ($className === "CondoProperty") continue;
                echo "\n className = " . $className;
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterIDX');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                //$properties_data = DB::table('RetsPropertyData')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded'])->where("Thumbnail_downloaded", "=", 0)->where('Status', '=', "A")->get();
                $properties_data = DB::table('RetsPropertyData')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded','PropertyType'])->where("Thumbnail_downloaded", "=", 0)->where('Status', '=', "A")->orderBy('inserted_time', 'desc')->get();

                //$listingIds = ['X5599837','X5599869','X5599239','X5600350','N5599633','N5599339','N5600390','N5599392'];
                //$properties_data = DB::table('RetsPropertyData')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded'])->whereIn('ListingId',$listingIds)->get();
                foreach ($properties_data as $key => $property_data) {
                    $mlsId = $property_data->ListingId;
                    $propertyType = $property_data->PropertyType;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '1');
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===" . $photo_count;
                    $photo_number = 0;
                    if (count($photos) > 0) {
                        $img_found = 1;
                        echo "<br>$property_table_name";
                        foreach ($photos as $keys => $photo) {
                            if ($photo["Success"]) {
                                if ($propertyType == "Condos") {
                                    $img_dir = "mls_images/condo_property/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
                                }else {
                                    $img_dir = "mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
                                }
                                //$img_dir = "mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
                                $dir_name = $img_dir;
                                $img_n = $photo["Data"];
                                $image = \Intervention\Image\Facades\Image::make($img_n)->resize(520, 480);
                                Storage::disk('public')->put($img_dir, $image->encode(), "public");
                                $fileUrl = Storage::disk('public')->url($img_dir);
                                $URL = $fileUrl;
                                $image_name = (stristr($URL, '?', true)) ? stristr($URL, '?', true) : $URL;
                                $pos = strrpos($image_name, '/');
                                $image_name = substr($image_name, $pos + 1);
                                $extension = stristr($image_name, '.');
                                //create webp image
                                //Resize
                                echo "\n image_url = " . $fileUrl;
                                $im = imagecreatefromjpeg($fileUrl);
                                // store image in webp
                                 if ($propertyType == "Condos") {
                                     $webp_path = "mls_images/condo_property/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                     imagewebp($im, public_path("storage/mls_images/condo_property/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                     $fileUrl2 = Storage::disk('public')->url("mls_images/condo_property/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp");
                                 }else{
                                     $webp_path = "mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                     imagewebp($im, public_path("storage/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                     $fileUrl2 = Storage::disk('public')->url("mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp");
                                 }
                                //$webp_path = "mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                //imagewebp($im, public_path("storage/mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                //$fileUrl2 = Storage::disk('public')->url("mls_images/".$mlsId."/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp");
                                echo "\n fileUrl2 - " . $fileUrl2;
                                $photo_number++;
                                //S3 storage here
                                //$contents = file_get_contents($fileUrl2);
                                //$s3_image_dir = "mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                /*try {
                                    Storage::disk('s3')->put($s3_image_dir, $contents, "public");
                                    $photo_number++;
                                } catch (S3Exception $exception) {
                                    $errorData["message1"] = $exception->getAwsErrorMessage();
                                    $errorData["message2"] = $exception->getAwsErrorCode();
                                    return response($errorData, $exception->getStatusCode());
                                }*/
                                //$fileUrl = Storage::disk('s3')->url($s3_image_dir);
                                //echo "\n file URL = " . $fileUrl2;
                                //Storage::disk('public')->delete($s3_image_dir);
                                Storage::disk('public')->delete($img_dir);
                                if ($img_found) {
                                    if ($photo_number == 1) {
                                        $rpd_update = RetsPropertyData::where("ListingId", $mlsId)->update(["ImageUrl" => $webp_path, "Thumbnail_downloaded" => 1]);
                                        $checkRpdImage = RetsPropertyDataImage::where("listingID",$mlsId)->get();
                                        if (collect($checkRpdImage)->all() == []){
                                            // upload default image on
                                            if($propertyType == "Condos") {
                                                $img_dir_detail = "mls_images/condo_property/mls_images/".$mlsId."/Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            } else {
                                               $img_dir_detail = "mls_images/".$mlsId."/Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            }
                                            //$img_dir_detail = "mls_images/".$mlsId."/Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            $dir_name_detail = $img_dir_detail;
                                            $img_n_detail = $photo["Data"];
                                            try {
                                                //Storage::disk('s3')->put($img_dir_detail, $img_n_detail, "public");
                                                Storage::disk('public')->put($img_dir_detail, $img_n_detail, "public");
                                                $fileUrl_detail = Storage::disk('public')->url($img_dir_detail);
                                                //$photo_number++;
                                            } catch (\Exception $exception) {
                                                /*$errorData["message1"] = $exception->getAwsErrorMessage();
                                                $errorData["message2"] = $exception->getAwsErrorCode();*/
                                                return response("Something went wrong", $exception->getStatusCode());
                                            }
                                            echo "\n file URL details Page = " . $fileUrl_detail;
                                            //$fileUrl_detail = Storage::disk('s3')->url($img_dir_detail);
                                            $update_data_detail = array(
                                                "mls_no" => $curr_mls_id,
                                                "listingID" => $mlsId,
                                                "image_path" => $img_dir_detail,
                                                "s3_image_url" => $img_dir_detail,
                                                "image_name" => $dir_name,
                                                "downloaded_time" => $curr_date,
                                                "updated_time" => $curr_date,
                                                "image_last_tried_time" => $curr_date,
                                            );
                                            RetsPropertyDataImage::create($update_data_detail);
                                            echo "\n file URL details Page = " . $fileUrl_detail;
                                        }
                                    }
                                } else {
                                    $txt .= "<td style='border:1px solid black'><span style='color:red;'>Not Uploaded </span>" . $mlsId . "</td>";
                                }
                                $txt .= "</tr>";
                            } else {
                                Log::warning("No image data found =>" . $photo['Data']);
                            }
                        }
                    } else {
                        echo "\n images not found";
                        echo "\n update value = " . $properties_data;
                        Log::warning("No image data found");
                    }

                }
                // exit;
            }
            $subject = "Images Insertion";
            echo $txt;
        }
    }


    function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null)
    {

        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }
        if ($permissions != null) {
            chmod($filename, $permissions);
        }
    }

    function output($image_type = IMAGETYPE_JPEG)
    {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    function getWidth()
    {
        return imagesx($this->image);
    }

    function getHeight()
    {
        return imagesy($this->image);
    }

    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
}
