<?php

namespace App\Http\Controllers\importListings;


use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataImage;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Image;

class ImagesControllerSoldVOW extends Controller
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
                if ($className == "CommercialProperty") continue;
                if ($className == "ResidentialProperty") continue;
                $curr_date = date('Y-m-d H:i:s');
                echo "\n $className";
                //$txt .= "<td>$className</td>";
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterVOW');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['purge_property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                //$properties_data = DB::table($property_table_name)->where("image_downloaded_temp", "=", 0)->where('Status', '=', "U")->limit(15000)->orderBy("id","desc")->get();
                $properties_data = DB::table($property_table_name)->where("image_downloaded", "=", 0)->where("image_download_tried", "<", 3)->where('Status', '=', "U")->limit(10000)->orderBy("id","desc")->get();

                foreach ($properties_data as $key => $property_data) {
                    $txt .= "<tr>";
                    $mlsId = $property_data->$key_field;
                    $all_images = [];
                    $image_download_tried = $property_data->image_download_tried;
                    $curr_date = date('Y-m-d H:i:s');
                    $img_found = 0;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '*');
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===" . $photo_count;
                    $photo_number = 0;
                    if (count($photos) > 0) {
                        $checkRpdImage = RetsPropertyDataImagesSold::where("listingID", $mlsId)->get();
                        if (collect($checkRpdImage)->all() != []) {
                            RetsPropertyDataImagesSold::where("listingID", $mlsId)->delete();
                        }
                        //if (false) {
                        $file = "";
                        if ($className == "CondoProperty") {
                            $upload_dir = "/mls_images/condo_property/mls_images/soldProperty/".$mlsId."/";
                        }else{
                            $upload_dir = "/mls_images/soldProperty/" . $mlsId . "/";
                        }
                        $img_found = 1;
                        $photoArray = array();
                        $s3photoArray = array();
                        echo "<br>$property_table_name";
                        foreach ($photos as $key => $photo) {
                            if ($photo["Success"]) {
                                $img_dir = $upload_dir . "Photo-" . $mlsId . "_" . $key . ".jpeg";
                                $dir_name = $img_dir;
                                $img_n = $photo["Data"];
                                try {
                                    #Storage::disk('s3')->put($img_dir, $img_n, "public");
                                    Storage::disk('public')->put($img_dir, $img_n, "public");
                                    $photo_number++;
                                } catch (\Exception $exception) {
                                    return response("Something went wrong", $exception->getStatusCode());
                                }
                                $fileUrl = Storage::disk('public')->url($img_dir);
                                echo "<a href='$fileUrl'><p>$fileUrl</p></a>";
                                $update_data = array(

                                    "listingID" => $mlsId,

                                );
                                $all_images[] = $img_dir;
                                if ($img_found) {
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => DB::raw('image_downloaded+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 1,'image_downloaded_temp' => 1, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    if ($photo_number == 1) {
                                        $imageTables[] = [
                                            "propertyTableName" => $property_table_name,
                                            "imagesInserted" => "Images Inserted",
                                            "listingId" => $mlsId,
                                            "fileName" => $file_name,
                                            "imageUrl" => $fileUrl,
                                        ];
                                        $rpd_update = RetsPropertyDataPurged::where("ListingId",$mlsId)->update(["ImageUrl" => $img_dir]);
                                    }
                                } else {
                                    //$txt .= "<td style='border:1px solid black'><span style='color:red;'>Not Uploaded </span>" . $mlsId."</td>";
                                    $update_data["is_download"] = 0;
                                    $image_download_tried = $image_download_tried + 1;
                                    //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                    $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_downloaded_temp' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                }
                                //Log::info("Images are created");
                                //RetsPropertyDataImagesSold::create($update_data);
                                $txt .= "</tr>";
                            } else {
                                $update_data["is_download"] = 0;
                                $image_download_tried = $image_download_tried + 1;
                                //$properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_download_tried' => DB::raw('image_download_tried+1'), "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_downloaded_temp' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                                Log::warning("No image data found =>" . $photo['Data']);
                            }
                        }
                        //RetsPropertyDataImageMongo::updateOrCreate(["listingID" => $mlsId], $update_data);
                    } else {
                        echo "\n images not found";
                        $image_download_tried = $image_download_tried + 1;
                        $properties_data = DB::table($property_table_name)->where($key_field, $mlsId)->update(['image_downloaded' => 0,'image_downloaded_temp' => 0, 'image_download_tried' => $image_download_tried, "property_last_updated" => $curr_date, "image_downloaded_time" => $curr_date]);
                        echo "\n update value = " . $properties_data;
                        Log::warning("No image data found");
                    }
                    if ($all_images != []){
                        RetsPropertyDataImagesSold::create(["listingID" => $mlsId,"image_urls" => json_encode($all_images)]);
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
                $subject = "Sold Images Insertion";
                $superAdminEmail = getSuperAdmin();
                sendEmail("SMTP", env('MAIL_FROM'), env('ALERT_TO_MAIL'), env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $txt, "imagesControllerVow", "3", env('RETSEMAILS'));
            }
        }
    }


   /* public function getThumbnail()
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
                if ($className == "ResidentialProperty") continue;
                if ($className === "CommercialProperty") continue;
                //if ($className === "CondoProperty") continue;
                echo "\n className = " . $className;
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterVOW');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                $properties_data = DB::table('RetsPropertyDataPurged')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded'])->where("Thumbnail_downloaded", "=", 0)->where('Status', '=', "U")->limit('10000')->get();
                //$listingIds = ['X5599837','X5599869','X5599239','X5600350','N5599633','N5599339','N5600390','N5599392'];
                //$properties_data = DB::table('RetsPropertyData')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded'])->whereIn('ListingId',$listingIds)->get();
                foreach ($properties_data as $key => $property_data) {
                    $mlsId = $property_data->ListingId;
                    $photos = $rets->GetObject($property_resource, 'Photo', $mlsId, '1');
                    $photo_count = collect($photos)->count();
                    echo "\n Photo Count ===" . $photo_count;
                    $photo_number = 0;
                    if (count($photos) > 0) {
                        $img_found = 1;
                        echo "<br>$property_table_name";
                        foreach ($photos as $keys => $photo) {
                            if ($photo["Success"]) {
                                $img_dir = "mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
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
                                imagewebp($im, public_path("storage/mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                $fileUrl2 = Storage::disk('public')->url("mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp");
                                echo "\n fileUrl2 - " . $fileUrl2;
                                //S3 storage here
                                $contents = file_get_contents($fileUrl2);
                                $s3_image_dir = "mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                try {
                                    Storage::disk('s3')->put($s3_image_dir, $contents, "public");
                                    $photo_number++;
                                } catch (S3Exception $exception) {
                                    dd($exception);
                                    $errorData["message1"] = $exception->getAwsErrorMessage();
                                    $errorData["message2"] = $exception->getAwsErrorCode();

                                    return response($errorData, $exception->getStatusCode());
                                }
                                $fileUrl = Storage::disk('s3')->url($s3_image_dir);
                                echo "\n file URL = " . $fileUrl;
                                Storage::disk('public')->delete($s3_image_dir);
                                Storage::disk('public')->delete($img_dir);
                                if ($img_found) {
                                    if ($photo_number == 1) {
                                        $rpd_update = RetsPropertyDataPurged::where("ListingId", $mlsId)->update(["ImageUrl" => $fileUrl, "Thumbnail_downloaded" => 1]);
                                        $checkRpdImage = RetsPropertyDataImagesSold::where("listingID", $mlsId)->get();
                                        if (collect($checkRpdImage)->all() == []) {
                                            // upload default image on
                                            $img_dir_detail = "Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            $dir_name_detail = $img_dir_detail;
                                            $img_n_detail = $photo["Data"];
                                            try {
                                                Storage::disk('s3')->put($img_dir_detail, $img_n_detail, "public");
                                                //$photo_number++;
                                            } catch (S3Exception $exception) {
                                                $errorData["message1"] = $exception->getAwsErrorMessage();
                                                $errorData["message2"] = $exception->getAwsErrorCode();
                                                return response($errorData, $exception->getStatusCode());
                                            }
                                            $fileUrl_detail = Storage::disk('s3')->url($img_dir_detail);
                                            $update_data_detail = array(
                                                "listingID" => $mlsId,
                                                "image_urls" => json_encode([$img_dir_detail])
                                            );
                                            RetsPropertyDataImagesSold::create($update_data_detail);
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
    }*/

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
                if ($className == "ResidentialProperty") continue;
                if ($className === "CommercialProperty") continue;
                //if ($className === "CondoProperty") continue;
                echo "\n className = " . $className;
                $login_parameters = config('mls_config.mls_login_parameter.mls_login_parameterVOW');
                // curr_mls_id
                $login_parameter = $login_parameters[$curr_mls_id];
                $property_data_mapping = $classconfig['property_data_mapping'][$className];
                $property_table_name = $classconfig['property_table_name'];
                $key_field = $classconfig['key_field'];
                //Helper for login
                $rets = mls_login($login_parameter);
                //$properties_data = DB::table('RetsPropertyDataPurged')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded','PropertyType'])->where("Thumbnail_downloaded", "=", 0)->where("propertyType","Condos")->where('Status', '=', "U")->orderBy('updated_time','DESC')->limit('50000')->get();
                $properties_data = DB::table('RetsPropertyDataPurged')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded','PropertyType'])->whereNull('ImageUrl')->where('Status', '=', "U")->orderBy('updated_time','DESC')->limit('50000')->get();



                //$listingIds = ['N5100240',  'N5101775'  ,'N5105272'  ,'N5106930'];
                //$properties_data = DB::table('RetsPropertyDataPurged')->select(['id', 'ListingId', 'Status', 'Thumbnail_downloaded','PropertyType'])->whereIn('ListingId',$listingIds)->get();
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
                                    $img_dir = "mls_images/condo_property/mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
                                } else {
                                    $img_dir = "mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
                                }
                                //$img_dir = "mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".jpeg";
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
                                if ($propertyType == "Condos"){
                                     $webp_path = "mls_images/condo_property/mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                     imagewebp($im, public_path("storage/mls_images/condo_property/mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                }else {
                                    $webp_path = "mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                    imagewebp($im, public_path("storage/mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                }
                                //$webp_path = "mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                //imagewebp($im, public_path("storage/mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp"), "100");
                                $fileUrl2 = Storage::disk('public')->url("mls_images/soldProperty/" . $mlsId . "/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp");
                                echo "\n fileUrl2 - " . $fileUrl2;
                                //S3 storage here
                                //$contents = file_get_contents($fileUrl2);
                                //$s3_image_dir = "mls_images/thumbnailImages/Photo-" . $mlsId . "_" . $keys . ".webp";
                                //try {
                                //Storage::disk('s3')->put($s3_image_dir, $contents, "public");
                                $photo_number++;
                                //} catch (S3Exception $exception) {
                                //$errorData["message1"] = $exception->getAwsErrorMessage();
                                //$errorData["message2"] = $exception->getAwsErrorCode();
                                //return response($errorData, $exception->getStatusCode());
                                //}
                                //$fileUrl = Storage::disk('s3')->url($s3_image_dir);
                                //echo "\n file URL = " . $fileUrl;
                                //Storage::disk('public')->delete($s3_image_dir);
                                Storage::disk('public')->delete($img_dir);
                                if ($img_found) {
                                    if ($photo_number == 1) {
                                        $rpd_update = RetsPropertyDataPurged::where("ListingId", $mlsId)->update(["ImageUrl" => $webp_path, "Thumbnail_downloaded" => 1]);
                                        $checkRpdImage = RetsPropertyDataImagesSold::where("listingID", $mlsId)->get();
                                        if (collect($checkRpdImage)->all() == []) {
                                            // upload default image on
                                            if($propertyType == "Condos") {
                                                $img_dir_detail = "mls_images/condo_property/mls_images/soldProperty/" . $mlsId . "/Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            } else {
                                                $img_dir_detail = "mls_images/soldProperty/" . $mlsId . "/Photo-" . $mlsId . "_" . 0 . ".jpeg";
                                            }

                                            $dir_name_detail = $img_dir_detail;
                                            $img_n_detail = $photo["Data"];
                                            try {
                                                //Storage::disk('s3')->put($img_dir_detail, $img_n_detail, "public");
                                                Storage::disk('public')->put($img_dir_detail, $img_n_detail, "public");
                                                //$photo_number++;
                                            } catch (\Exception $exception) {
                                                /*$errorData["message1"] = $exception->getAwsErrorMessage();
                                                $errorData["message2"] = $exception->getAwsErrorCode();*/
                                                return response("Something went wrong", $exception->getStatusCode());
                                            }
                                            $fileUrl_detail = Storage::disk('public')->url($img_dir_detail);
                                            //$fileUrl_detail = Storage::disk('s3')->url($img_dir_detail);
                                            echo "\n file URL details Page = " . $fileUrl_detail;
                                            $update_data_detail = array(
                                                "listingID" => $mlsId,
                                                "image_urls" => json_encode([$img_dir_detail])
                                            );
                                            RetsPropertyDataImagesSold::create($update_data_detail);
                                            //echo "\n file URL details Page = " . $fileUrl_detail;
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
