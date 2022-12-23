<?php

namespace App\Http\Controllers\importListings;

use App\Http\Controllers\Controller;
use App\Models\PropertyAddressData;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCommPurged;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataCondoPurged;
use App\Models\RetsPropertyDataResi;
use App\Models\RetsPropertyDataResiPurged;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    //
    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    }

    public function index() {
        /*$property_tables = ["RetsPropertyDataComm"];
        foreach ($property_tables as $table) {
            if ($table == "RetsPropertyDataResi") {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataResi::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    RetsPropertyDataResi::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    RetsPropertyData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    echo "\n total_properties_left = ".$total_properties--;
                }
            } elseif($table == "RetsPropertyDataCondo") {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataCondo::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    RetsPropertyDataCondo::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    RetsPropertyData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    echo "\n total_properties_left = ".$total_properties--;
                }
            } else {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataComm::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    RetsPropertyDataComm::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    RetsPropertyData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    echo "\n total_properties_left = ".$total_properties--;
                }
            }
        }*/


        // for sold
      /* $property_tables = ["RetsPropertyDataResi","RetsPropertyDataCondo","RetsPropertyDataComm"];
        foreach ($property_tables as $table) {
            if ($table == "RetsPropertyDataResi") {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataResiPurged::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    //RetsPropertyDataResiPurged::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //RetsPropertyDataPurged::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    $get_addr = str_replace( '"', "", $get_addr);
                    DB::update('UPDATE RetsPropertyDataPurged set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE RetsPropertyDataResiPurged set StandardAddress = "'.$get_addr.'" where Ml_num = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE PropertyAddressData set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');

                    //DB::update("UPDATE PropertyAddressData set StandardAddress = '".$get_addr."' where ListingId = '".$data["Ml_num"]."'");
                    echo "\n total_properties_left = ".$total_properties--;
                    Log::info("property updated left in resi purged = ".$total_properties);
                }
            } elseif($table == "RetsPropertyDataCondo") {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataCondoPurged::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    //RetsPropertyDataCondo::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //RetsPropertyData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    $get_addr = str_replace( '"', "", $get_addr);
                    DB::update('UPDATE RetsPropertyDataPurged set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE RetsPropertyDataCondoPurged set StandardAddress = "'.$get_addr.'" where Ml_num = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE PropertyAddressData set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');


                    echo "\n total_properties_left = ".$total_properties--;
                    Log::info("property updated left in condo purged = ".$total_properties);
                }
            } else {
                echo "\n table = ".$table;
                $sql_data = RetsPropertyDataCommPurged::select(["St","Ml_num","St_dir","St_num","St_sfx","Apt_num","Addr"])->get();
                $total_properties = collect($sql_data)->count();
                echo "\n total_properties_count = ".$total_properties;
                foreach ($sql_data as $data) {
                    $data = collect($data)->all();
                    $get_addr = get_address($data);
                    echo "\n updated for mls  = ".$data["Ml_num"]. "where address is = ".$get_addr;
                    //RetsPropertyDataCondo::where("Ml_num",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //RetsPropertyData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    //PropertyAddressData::where("ListingId",$data["Ml_num"])->update(["StandardAddress" => $get_addr]);
                    $get_addr = str_replace( '"', "", $get_addr);
                    DB::update('UPDATE RetsPropertyDataPurged set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE RetsPropertyDataCommPurged set StandardAddress = "'.$get_addr.'" where Ml_num = "'.$data["Ml_num"].'"');
                    DB::update('UPDATE PropertyAddressData set StandardAddress = "'.$get_addr.'" where ListingId = "'.$data["Ml_num"].'"');


                    echo "\n total_properties_left = ".$total_properties--;
                    Log::info("property updated left in condo purged = ".$total_properties);
                }
            }
        }


        echo "done";
        die();*/







        $rpd_count = RetsPropertyData::count();
        $rpd_resi_count = RetsPropertyDataResi::count();
        $rpd_comm_count = RetsPropertyDataComm::count();
        $rpd_condo_count = RetsPropertyDataCondo::count();
        $rpd_sold_count = RetsPropertyDataPurged::count();
        $rpd_resi_sold_count = RetsPropertyDataResiPurged::count();
        $rpd_comm_sold_count = RetsPropertyDataCommPurged::count();
        $rpd_condo_sold_count = RetsPropertyDataCondoPurged::count();
        $rpd_path = "RetsPropertyData_". date('Y_m_d_H_i_s').".sql";
        //dd(date('Y_m_d_H_i_s'));
        $rpd_resi_path = "RetsPropertyDataResi_". date('Y_m_d_H_i_s').".sql";
        $rpd_comm_path = "RetsPropertyDataComm_".date('Y_m_d_H_i_s').".sql";
        $rpd_condo_path = "RetsPropertyDataCondo_".date('Y_m_d_H_i_s').".sql";
        exec('mysqldump --user='.env("DB_USERNAME").' --password='.env("DB_PASSWORD").' '.env("DB_DATABASE").' RetsPropertyData > /home/ubuntu/db_backup/'.$rpd_path, $errors_rpd);
        exec('mysqldump --user='.env("DB_USERNAME").' --password='.env("DB_PASSWORD").' '.env("DB_DATABASE").' RetsPropertyDataResi > /home/ubuntu/db_backup/'.$rpd_resi_path, $errors_rpd_resi);
        exec('mysqldump --user='.env("DB_USERNAME").' --password='.env("DB_PASSWORD").' '.env("DB_DATABASE").' RetsPropertyDataComm > /home/ubuntu/db_backup/'.$rpd_comm_path, $errors_rpd_comm);
        exec('mysqldump --user='.env("DB_USERNAME").' --password='.env("DB_PASSWORD").' '.env("DB_DATABASE").' RetsPropertyDataCondo > /home/ubuntu/db_backup/'.$rpd_condo_path, $errors_rpd_condo);
        $rpd_error = "";
        $rpd_resi_error = "";
        $rpd_comm_error = "";
        $rpd_condo_error = "";
        if ( ! empty($errors_rpd)) {
            $rpd_error = "<span style='color : red'>Not taken backup</span>";
        }else{
            $rpd_error = "<span style='color : green'>Successfully backup</span>";
        }
        if ( ! empty($errors_rpd_resi)) {
            $rpd_resi_error = "<span style='color : red'>Not taken backup</span>";
        }else{
            $rpd_resi_error = "<span style='color : green'>Successfully backup</span>";
        }
        if ( ! empty($errors_rpd_comm)) {
            $rpd_comm_error = "<span style='color : red'>Not taken backup</span>";
        }else{
            $rpd_comm_error = "<span style='color : green'>Successfully backup</span>";
        }
        if ( ! empty($errors_rpd_condo)) {
            $rpd_condo_error = "<span style='color : red'>Not taken backup</span>";
        }else{
            $rpd_condo_error = "<span style='color : green'>Successfully backup</span>";
        }
        $txt = "";
        $txt .= "<table border=1>";
        $bacounter = 0;
        $txt .= "<thead>
                       <tr>
                          <th>Sr. No</th>
                          <th>Table</th>
                          <th>Count</th>
                          <th>Backup Status</th>
                          <th>Stored Path</th>
                        </tr>
                 </thead>";

        $txt .= "<tr>";
        $txt .= "<td>" . 1 . "</td>";
        $txt .= "<td> RetsPropertyData </td>";
        $txt .= "<td>" . $rpd_count . "</td>";
        $txt .= "<td>" . $rpd_error . "</td>";
        $txt .= "<td>/home/mukesh/db_backup/".$rpd_path."</td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 2 . "</td>";
        $txt .= "<td> RetsPropertyDataResi </td>";
        $txt .= "<td>" . $rpd_resi_count . "</td>";
        $txt .= "<td>" . $rpd_resi_error . "</td>";
        $txt .= "<td>/home/mukesh/db_backup/".$rpd_resi_path."</td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 3 . "</td>";
        $txt .= "<td> RetsPropertyDataComm </td>";
        $txt .= "<td>" . $rpd_comm_count . "</td>";
        $txt .= "<td>" . $rpd_comm_error . "</td>";
        $txt .= "<td>/home/mukesh/db_backup/".$rpd_comm_path."</td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 4 . "</td>";
        $txt .= "<td> RetsPropertyDataCondo </td>";
        $txt .= "<td>" . $rpd_condo_count . "</td>";
        $txt .= "<td>" . $rpd_condo_error . "</td>";
        $txt .= "<td>/home/mukesh/db_backup/".$rpd_condo_path."</td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 5 . "</td>";
        $txt .= "<td> RetsPropertyDataPurged </td>";
        $txt .= "<td>" . $rpd_sold_count . "</td>";
        $txt .= "<td> <span style='color : red'> Not taken backup </span></td>";
        $txt .= "<td></td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 6 . "</td>";
        $txt .= "<td> RetsPropertyDataResiPurged </td>";
        $txt .= "<td>" . $rpd_resi_sold_count . "</td>";
        $txt .= "<td> <span style='color : red'> Not taken backup </span></td>";
        $txt .= "<td></td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 7 . "</td>";
        $txt .= "<td> RetsPropertyDataCommPurged </td>";
        $txt .= "<td>" . $rpd_comm_sold_count . "</td>";
        $txt .= "<td> <span style='color : red'> Not taken backup </span></td>";
        $txt .= "<td></td>";
        $txt .= "</tr>";

        $txt .= "<tr>";
        $txt .= "<td>" . 8 . "</td>";
        $txt .= "<td> RetsPropertyDataCondoPurged </td>";
        $txt .= "<td>" . $rpd_condo_sold_count . "</td>";
        $txt .= "<td> <span style='color : red'> Not taken backup </span></td>";
        $txt .= "<td></td>";
        $txt .= "</tr>";
        $txt .= "</table>";

        $curr_date_end = date('Y-m-d H:i:s');
        sendEmail("SMTP", env('MAIL_FROM'), "mukesh.swami8127@gmail.com", "shivasi@peregrine-it.com", "sagar@peregrine-it.com", 'Backup of Active tables Cron FINISHED - ' . $curr_date_end, $txt, "BackupController index", "", env('RETSEMAILS'));
        echo "\n backup successfully";
    }
}
