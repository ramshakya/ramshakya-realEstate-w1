<?php

namespace App\Http\Controllers;

use App\Models\RetsPropertyDataImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataImagesSold;
use App\Models\SqlModel\RetsPropertyDataPurged;

class MoveCondoImagesController extends Controller
{
    //

    public function index() {
        # this code will retrieve the data into 1000 chunks
        # 1 for listingID MOVED
        # 2 for listingId moved by mls insertion
        # 3 for listing id both are availabe on that folders
        $listings = "select Ml_num from RetsPropertyDataCondo where imagesMoved = 0 order by property_insert_time asc limit 1000";
        $img_stat = false;
        $listings = DB::select($listings);
        foreach ($listings as $listing){
            $listing = collect($listing)->all();
            $listingId = $listing["Ml_num"];
            echo "\n for listingId = ".$listingId;
            $folder_path = "/mls_images/".$listingId."/";
            $image_status = Storage::disk('public')->exists($folder_path);
            if ($image_status) {
                echo "\n got the image for this listingId ".$listingId;
                $move_path = "/mnt/condo_disk_image/condo_images/";
                $source_path = "storage/app/public/img/mls_images/".$listingId;
                $check_path = "/mnt/condo_disk_image/condo_images/".$listingId;
                if (!file_exists($check_path)) {
                    $shell_script = "mv ".$source_path." ".$move_path."";
                    shell_exec("mv ".$source_path." ".$move_path."");
                    # this is for the update database images
                    $images = "select * from RetsPropertyDataImages where listingID = '".$listingId."'";
                    $images = DB::select($images);
                    foreach ($images as $image) {
                        $image = collect($image)->all();
                        $extra_string = "condo_property/mls_images";
                        $url = $image["s3_image_url"];
                        $url = $this->AddInTheMiddle(1, 1, $url);
                        $image["s3_image_url"] = $url;
                        $image["image_path"] = $url;
                        $image["image_name"] = $url;
                        $upd = RetsPropertyDataImage::where("id",$image["id"])->update($image);
                        $img_stat = true;
                        echo "\n images are updated for this listingId = ".$listingId;
                    }
                    # update the RPD table for thumbnail images
                    $images_rpd = "select id,ImageUrl from RetsPropertyData where listingID = '".$listingId."'";
                    $images_rpd = DB::select($images_rpd);
                    foreach ($images_rpd as $image_r) {
                        $image_r = collect($image_r)->all();
                        $url = $image_r["ImageUrl"];
                        if (strpos($url,'thumbnailImages') !== false) {
                            $url = "/".$url;
                        }
                        $url = $this->AddInTheMiddle(1, 1, $url);
                        $image_r["ImageUrl"] = $url;
                        $upd = RetsPropertyData::where("id",$image_r["id"])->update($image_r);
                        echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                    }
                    if ($img_stat) {
                        # update the column from regarding table
                        $update_query = "UPDATE RetsPropertyDataCondo set imagesMoved = 1 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                    }
                } else {
                    $update_query = "UPDATE RetsPropertyDataCondo set imagesMoved = 3 where Ml_num = '".$listingId."'";
                    DB::update($update_query);
                    echo "\n images folder not found for this property = ".$listingId;
                }
           }
        }
    }


    /*public function soldImagesMoved() {
        # this code will retrieve the data into 1000 chunks
        $listings = "select Ml_num from RetsPropertyDataCondoPurged where imagesMoved = 0 limit 1000";
        $listings = DB::select($listings);
        foreach ($listings as $listing){
            $listing = collect($listing)->all();
            $listingId = $listing["Ml_num"];
            echo "\n for listingId = ".$listingId;
            $folder_path = "/mls_images/".$listingId;
            $image_status = Storage::exists($folder_path);
            if ($image_status) {
                echo "\n got the image for this listingId ".$listingId;
                $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                Storage::disk('public')->move($folder_path, $move_path);
                # this is for the update database images
                $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                $images = DB::select($images);
                foreach ($images as $image) {
                    $image = collect($image)->all();
                    $extra_string = "condo_property/mls_images";
                    $url = $image["s3_image_url"];
                    $url = $this->AddInTheMiddle(1, 1, $url);
                    $image["s3_image_url"] = $url;
                    $image["image_path"] = $url;
                    $image["image_name"] = $url;
                    $upd = RetsPropertyDataImage::where("id",$image["id"])->update($image);
                    echo "\n images are updated for this listingId".$listingId;
                }
            }
        }
    }*/

    /*public function soldImagesMoved() {
        # this code will retrieve the data into 1000 chunks
        # 1 for listingID MOVED
        # 2 for listingId moved by mls insertion
        # 3 for listing id both are availabe on that folders
        $listings = "select Ml_num from RetsPropertyDataCondoPurged where imagesMoved = 0 and image_downloaded = 1 order by property_insert_time asc limit 1000";

        $img_stat = false;
        $listings = DB::select($listings);
        foreach ($listings as $listing){
            $listing = collect($listing)->all();
            $listingId = $listing["Ml_num"];
            echo "\n for listingId = ".$listingId;
            $rpd_check = RetsPropertyData::where("ListingId",$listingId)->get();
            if (collect($rpd_check)->count() != 0) {
                $update_query = "UPDATE RetsPropertyDataCondoPurged set image_downloaded = 0, imagesMoved = 7  where Ml_num = '".$listingId."'";
                DB::update($update_query);
                echo "\n found in active listings";
                continue;
            }
            $folder_path = "/mls_images/soldProperty/".$listingId."/";
            $image_status = Storage::disk('public')->exists($folder_path);
            if ($image_status) {
                echo "\n got the image if  condition for this listingId ".$listingId;
                $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                $source_path = "storage/app/public/img/mls_images/soldProperty/".$listingId;
                $check_path = "/mnt/condo_disk_image/condo_images/soldProperty/".$listingId;
                if (!file_exists($check_path)) {
                    $shell_script = "mv ".$source_path." ".$move_path."";
                    shell_exec("mv ".$source_path." ".$move_path."");
                    # this is for the update database images
                    $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                    $images = DB::select($images);
                    foreach ($images as $image) {
                        $image = collect($image)->all();
                        $urls = json_decode($image["image_urls"]);
                        $urls = [];
                        $cnt = 0;
                        $single_url = "";
                        foreach ($urls as $url) {
                            $extra_string = "condo_property/mls_images/soldProperty";
                            $url = $this->AddInTheMiddle(1, 1, $url);
                            $urls[] = $url;
                            if ($cnt == 0){
                                $single_url = $url;
                            }
                            $cnt++;
                        }
                        $upd = RetsPropertyDataImagesSold::where("id",$image["id"])->update(["image_urls" => json_encode($urls)]);
                        $img_stat = true;
                        echo "\n images are updated for this listingId = ".$listingId;
                    }
                    # update the RPD table for thumbnail images
                    $images_rpd = "select id,ImageUrl from RetsPropertyDataPurged where listingID = '".$listingId."'";
                    $images_rpd = DB::select($images_rpd);
                    foreach ($images_rpd as $image_r) {
                        $image_r = collect($image_r)->all();
                        $url = $image_r["ImageUrl"];
                        if (strpos($url,'thumbnailImages') !== false) {
                            $url = "/".$url;
                        }
                        $url = $this->AddInTheMiddle(1, 1, $url);
                        $image_r["ImageUrl"] = $single_url;
                        $upd = RetsPropertyDataPurged::where("id",$image_r["id"])->update($image_r);
                        echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                    }
                    if ($img_stat) {
                        # update the column from regarding table
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 1 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                    }
                } else {
                    $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 3 where Ml_num = '".$listingId."'";
                    DB::update($update_query);
                    echo "\n images folder not found for this property = ".$listingId;
                }
            } else {
                $folder_path = "/mls_images/".$listingId."/";
                $image_status = Storage::disk('public')->exists($folder_path);
                if ($image_status) {
                    echo "\n got the image for else  condition this listingId ".$listingId;
                    $rpd_check = RetsPropertyData::where("ListingId",$listingId)->get();

                    $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                    $source_path = "storage/app/public/img/mls_images/".$listingId;
                    $check_path = "/mnt/condo_disk_image/condo_images/soldProperty/".$listingId;
                    if (!file_exists($check_path)) {
                        if (collect($rpd_check)->count() != 0) {
                            $shell_script = "cp ".$source_path." ".$move_path."";
                            shell_exec("cp ".$source_path." ".$move_path."");
                            echo "\n in copied mode";
                        } else {
                            $shell_script = "mv ".$source_path." ".$move_path."";
                            shell_exec("mv ".$source_path." ".$move_path."");
                            echo "\n in move mode";
                        }
                        # this is for the update database images
                        $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                        $images = DB::select($images);
                        foreach ($images as $image) {
                            $image = collect($image)->all();
                            $urls = json_decode($image["image_urls"]);
                            $urlss = [];
                            $cnt = 0;
                            $single_url = "";
                            foreach ($urls as $url) {
                                $extra_string = "condo_property/mls_images/soldProperty";
                                $url = $this->AddInTheMiddle(1, 1, $url);
                                $urlss[] = $url;
                                if ($cnt == 0){
                                    $single_url = $url;
                                }
                                $cnt++;
                            }
                            $upd = RetsPropertyDataImagesSold::where("id",$image["id"])->update(["image_urls" => json_encode($urlss)]);
                            $img_stat = true;
                            echo "\n images are updated for this listingId = ".$listingId;
                        }
                        # update the RPD table for thumbnail images
                        $images_rpd = "select id,ImageUrl from RetsPropertyDataPurged where listingID = '".$listingId."'";
                        $images_rpd = DB::select($images_rpd);
                        foreach ($images_rpd as $image_r) {
                            $image_r = collect($image_r)->all();
                            $url = $image_r["ImageUrl"];
                            if (strpos($url,'thumbnailImages') !== false) {
                                $url = "/".$url;
                            }
                            $url = $this->AddInTheMiddle(1, 1, $url);
                            $image_r["ImageUrl"] = $single_url;
                            $upd = RetsPropertyDataPurged::where("id",$image_r["id"])->update($image_r);
                            //echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                        }
                        if ($img_stat) {
                            # update the column from regarding table
                            $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 1 where Ml_num = '".$listingId."'";
                            DB::update($update_query);
                        }
                    } else {
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 3 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                        echo "\n images folder not found for this property = ".$listingId;
                    }
                }
                $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 5 where Ml_num = '".$listingId."'";

                DB::update($update_query);
            }
        }
    }*/

    public function soldImagesMoved() {
        # this code will retrieve the data into 1000 chunks
        # 1 for listingID MOVED
        # 2 for listingId moved by mls insertion
        # 3 for listing id both are availabe on that folders
        $listings = "select Ml_num from RetsPropertyDataCondoPurged where imagesMoved = 0 and image_downloaded = 1 order by property_insert_time asc limit 1000";
        $listings = "select Ml_num from RetsPropertyDataCondoPurged where imagesMoved = 5";
        $img_stat = false;
        $listings = DB::select($listings);
        foreach ($listings as $listing){
            $listing = collect($listing)->all();
            $listingId = $listing["Ml_num"];
            echo "\n for listingId = ".$listingId;
            $rpd_check = RetsPropertyData::where("ListingId",$listingId)->get();
            if (collect($rpd_check)->count() != 0) {
                $update_query = "UPDATE RetsPropertyDataCondoPurged set image_downloaded = 0, imagesMoved = 7  where Ml_num = '".$listingId."'";
                DB::update($update_query);
                echo "\n found in active listings";
                continue;
            }
            $folder_path = "/mls_images/soldProperty/".$listingId."/";
            $image_status = Storage::disk('public')->exists($folder_path);
            if ($image_status) {
                echo "\n got the image if  condition for this listingId ".$listingId;
                $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                $source_path = "storage/app/public/img/mls_images/soldProperty/".$listingId;
                $check_path = "/mnt/condo_disk_image/condo_images/soldProperty/".$listingId;
                if (!file_exists($check_path)) {
                    $shell_script = "mv ".$source_path." ".$move_path."";
                    shell_exec("mv ".$source_path." ".$move_path."");
                    # this is for the update database images
                    $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                    $images = DB::select($images);
                    foreach ($images as $image) {
                        $image = collect($image)->all();
                        $urls = json_decode($image["image_urls"]);
                        $urls = [];
                        $cnt = 0;
                        $single_url = "";
                        foreach ($urls as $url) {
                            $extra_string = "condo_property/mls_images/soldProperty";
                            $url = $this->AddInTheMiddle(1, 1, $url);
                            $urls[] = $url;
                            if ($cnt == 0){
                                $single_url = $url;
                            }
                            $cnt++;
                        }
                        $upd = RetsPropertyDataImagesSold::where("id",$image["id"])->update(["image_urls" => json_encode($urls)]);
                        $img_stat = true;
                        echo "\n images are updated for this listingId = ".$listingId;
                    }
                    # update the RPD table for thumbnail images
                    $images_rpd = "select id,ImageUrl from RetsPropertyDataPurged where listingID = '".$listingId."'";
                    $images_rpd = DB::select($images_rpd);
                    foreach ($images_rpd as $image_r) {
                        $image_r = collect($image_r)->all();
                        $url = $image_r["ImageUrl"];
                        if (strpos($url,'thumbnailImages') !== false) {
                            $url = "/".$url;
                        }
                        $url = $this->AddInTheMiddle(1, 1, $url);
                        $image_r["ImageUrl"] = $single_url;
                        $upd = RetsPropertyDataPurged::where("id",$image_r["id"])->update($image_r);
                        echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                    }
                    if ($img_stat) {
                        # update the column from regarding table
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 1 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                    }
                } else {
                    $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 3 where Ml_num = '".$listingId."'";
                    DB::update($update_query);
                    echo "\n images folder not found for this property /mnt/condo_disk_image/condo_images/soldProperty/ = ".$listingId;
                }
            } else {
                $folder_path = "/mls_images/".$listingId."/";
                $image_status = Storage::disk('public')->exists($folder_path);
                if ($image_status) {
                    echo "\n got the image for else  condition this listingId ".$listingId;
                    $rpd_check = RetsPropertyData::where("ListingId",$listingId)->get();

                    $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                    $source_path = "storage/app/public/img/mls_images/".$listingId;
                    $check_path = "/mnt/condo_disk_image/condo_images/soldProperty/".$listingId;
                    if (!file_exists($check_path)) {
                        if (collect($rpd_check)->count() != 0) {
                            $shell_script = "cp ".$source_path." ".$move_path."";
                            shell_exec("cp ".$source_path." ".$move_path."");
                            echo "\n in copied mode";
                        } else {
                            $shell_script = "mv ".$source_path." ".$move_path."";
                            shell_exec("mv ".$source_path." ".$move_path."");
                            echo "\n in move mode";
                        }
                        # this is for the update database images
                        $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                        $images = DB::select($images);
                        foreach ($images as $image) {
                            $image = collect($image)->all();
                            $urls = json_decode($image["image_urls"]);
                            $urlss = [];
                            $cnt = 0;
                            $single_url = "";
                            foreach ($urls as $url) {
                                $extra_string = "condo_property/mls_images/soldProperty";
                                $url = $this->AddInTheMiddle(1, 1, $url);
                                $urlss[] = $url;
                                if ($cnt == 0){
                                    $single_url = $url;
                                }
                                $cnt++;
                            }
                            $upd = RetsPropertyDataImagesSold::where("id",$image["id"])->update(["image_urls" => json_encode($urlss)]);
                            $img_stat = true;
                            echo "\n images are updated for this listingId = ".$listingId;
                        }
                        # update the RPD table for thumbnail images
                        $images_rpd = "select id,ImageUrl from RetsPropertyDataPurged where listingID = '".$listingId."'";
                        $images_rpd = DB::select($images_rpd);
                        foreach ($images_rpd as $image_r) {
                            $image_r = collect($image_r)->all();
                            $url = $image_r["ImageUrl"];
                            if (strpos($url,'thumbnailImages') !== false) {
                                $url = "/".$url;
                            }
                            $url = $this->AddInTheMiddle(1, 1, $url);
                            $image_r["ImageUrl"] = $single_url;
                            $upd = RetsPropertyDataPurged::where("id",$image_r["id"])->update($image_r);
                            //echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                        }
                        if ($img_stat) {
                            # update the column from regarding table
                            $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 1 where Ml_num = '".$listingId."'";
                            DB::update($update_query);
                        }
                    } else {
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 3 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                        echo "\n images folder not found for this property /mnt/condo_disk_image/condo_images/soldProperty/ = ".$listingId;
                    }
                } else {
                    $folder_path = "/mnt/condo_disk_image/condo_images/".$listingId."/";
                    $source_path = "/mnt/condo_disk_image/condo_images/".$listingId."/";
                    $move_path = "/mnt/condo_disk_image/condo_images/soldProperty/";
                    if (file_exists($folder_path)) {
                        echo "\n image found on  mounted disk";
                        $shell_script = "mv ".$source_path." ".$move_path."";
                        shell_exec("cp ".$source_path." ".$move_path."");
                        # this is for the update database images
                        $images = "select * from RetsPropertyDataImagesSold where listingID = '".$listingId."'";
                        $images = DB::select($images);
                        foreach ($images as $image) {
                            $image = collect($image)->all();
                            $urls = json_decode($image["image_urls"]);
                            $urlss = [];
                            $cnt = 0;
                            $single_url = "";
                            foreach ($urls as $url) {
                                $extra_string = "condo_property/mls_images/soldProperty";
                                $url = $this->AddInTheMiddle(1, 1, $url);
                                $urlss[] = $url;
                                if ($cnt == 0){
                                    $single_url = $url;
                                }
                                $cnt++;
                            }
                            $upd = RetsPropertyDataImagesSold::where("id",$image["id"])->update(["image_urls" => json_encode($urlss)]);
                            $img_stat = true;
                            echo "\n images are updated for this listingId = ".$listingId;
                        }
                        # update the RPD table for thumbnail images
                        $images_rpd = "select id,ImageUrl from RetsPropertyDataPurged where listingID = '".$listingId."'";
                        $images_rpd = DB::select($images_rpd);
                        foreach ($images_rpd as $image_r) {
                            $image_r = collect($image_r)->all();
                            $url = $image_r["ImageUrl"];
                            if (strpos($url,'thumbnailImages') !== false) {
                                $url = "/".$url;
                            }
                            $url = $this->AddInTheMiddle(1, 1, $url);
                            $image_r["ImageUrl"] = $single_url;
                            $upd = RetsPropertyDataPurged::where("id",$image_r["id"])->update($image_r);
                            //echo "\n Thumbnail images are updated for this listingId = ".$listingId;
                        }
                        if ($img_stat) {
                            # update the column from regarding table
                            $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 1 where Ml_num = '".$listingId."'";
                            DB::update($update_query);
                        }
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 5 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                    } else {
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 3 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                        echo "\n images folder not found for this property $folder_path = ".$listingId;
                    }

                }

            }
        }
    }

    public function deleteImagesFromDisk_old() {
        $query = "SELECT Ml_num from RetsPropertyDataCondoPurged where image_downloaded = 1 limit 1000";
        $query = DB::select($query);
        foreach ($query as $data) {
            $data = collect($data)->all();
            // check in rets property data
            $listingId = $data["Ml_num"];
            $check_rpd = RetsPropertyData::where("ListingId",$data["Ml_num"])->get();
            if (collect($check_rpd)->count() != 0) {
                echo "\n founded on active";
                continue;
            }else{
                $folder_path = "/mls_images/".$listingId."/";
                $image_status = Storage::disk('public')->exists($folder_path);
                if ($image_status) {
                    shell_exec("rm -rf ".$folder_path);
                    $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 8, image_downloaded = 0 where Ml_num = '".$listingId."'";
                    DB::update($update_query);
                    $update_query_rpd = "UPDATE RetsPropertyDataPurged set ImageUrl = '', Thumbnail_downloaded = 0 where ListingId = '".$listingId."'";
                    DB::update($update_query_rpd);

                } else {
                    $folder_path_re = "/mls_images/soldProperty/".$listingId."/";
                    $image_status_re = Storage::disk('public')->exists($folder_path_re);
                    if ($image_status_re) {
                        shell_exec("rm -rf ".$folder_path);
                        $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 8, image_downloaded = 0 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                        $update_query_rpd = "UPDATE RetsPropertyDataPurged set ImageUrl = '', Thumbnail_downloaded = 0 where ListingId = '".$listingId."'";
                        DB::update($update_query_rpd);
                    }
                }
            }
        }
    }

    public function deleteImagesFromDisk() {
        $query = "SELECT Ml_num from RetsPropertyDataCondoPurged where image_downloaded_temp = 1 ORDER BY id ASC  limit 10000";
        $query = DB::select($query);
        foreach ($query as $data) {
            $data = collect($data)->all();
            // check in rets property data
            $listingId = $data["Ml_num"];
            $check_rpd = RetsPropertyData::where("ListingId",$data["Ml_num"])->get();
            if (collect($check_rpd)->count() != 0) {
                echo "\n founded on active";
                echo "\n got in Active table mls no = ".$data["Ml_num"];
                continue;
            }else{
                $folder_path = "/mls_images/".$listingId."/";
                $image_status = Storage::disk('public')->exists($folder_path);
                if ($image_status) {
                    $fld_path = "/home/mukesh/public_html/panel/storage/app/public/img/mls_images/".$listingId."";
                    shell_exec("rm -rf ".$fld_path);
                    //$update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 8, image_downloaded = 0 where Ml_num = '".$listingId."'";
                    //DB::update($update_query);
                    //$update_query_rpd = "UPDATE RetsPropertyDataPurged set ImageUrl = '', Thumbnail_downloaded = 0 where ListingId = '".$listingId."'";
                    //DB::update($update_query_rpd);
                    echo "\n got in Active images row mls no = ".$data["Ml_num"];

                } else {
                    $folder_path_re = "/mls_images/soldProperty/".$listingId."/";
                    $image_status_re = Storage::disk('public')->exists($folder_path_re);
                    if ($image_status_re) {
                        $fld_path = "/home/mukesh/public_html/panel/storage/app/public/img/mls_images/soldProperty/".$listingId."";
                        shell_exec("rm -rf ".$fld_path);
                        echo "\n got in sold images row mls no = ".$data["Ml_num"];
                        /*$update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 8, image_downloaded = 0 where Ml_num = '".$listingId."'";
                        DB::update($update_query);
                        $update_query_rpd = "UPDATE RetsPropertyDataPurged set ImageUrl = '', Thumbnail_downloaded = 0 where ListingId = '".$listingId."'";
                        DB::update($update_query_rpd);*/
                    }
                }
            }
            $update_query = "UPDATE RetsPropertyDataCondoPurged set imagesMoved = 8, image_downloaded_temp = 2 where Ml_num = '".$listingId."'";
            DB::update($update_query);
        }
    }



    function AddInTheMiddle($start, $where, $what){
        $arr = explode("/", $what);
        $str = implode("/", array_splice($arr,$start,$where)) . '/condo_property/mls_images/soldProperty' . implode("/", $arr);;
        return "/".$str;
    }
}


