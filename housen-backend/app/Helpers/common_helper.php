<?php

use App\Constants\PropertyConstants;
use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataImage;
use App\Models\SqlModel\AlertsLog;
use App\Models\SqlModel\BlogCategory;
use App\Models\SqlModel\BlogModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\Websetting;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\PropertyFeatures;
use App\Models\SqlModel\RetsPropertyDataPurged;
use Illuminate\Support\Facades\DB;

use App\Models\SqlModel\Pages;
use App\Models\SqlModel\SavedSearchFilter;
use App\Models\StatsData;
use Carbon\Carbon;

function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function imagecreatefromfile($filename)
{
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "' . $filename . '" not found.');
    }
    switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
            break;

        case 'png':
            return imagecreatefrompng($filename);
            break;

        default:
            throw new InvalidArgumentException('File "' . $filename . '" is not valid jpg, png or gif image.');
            break;
    }
}

function compress_Image($image = '', $dir = '')
{
    $url = "";
    $file = $image;
    if (preg_match('/^data:image\/(\w+);base64,/', $file)) {
        $data = substr($file, strpos($file, ',') + 1);
        $pos = strpos($file, ';');
        $type = explode(':', substr($file, 0, $pos))[1];
        $name = uniqid();
        $path = storage_path('app/public/img/' . $dir . "_webp/");
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        $url = Storage::disk('public')->put('img/' . $name . '.' . Str::afterLast($type, '/'), base64_decode($data));
        $im = imagecreatefromfile(storage_path('app/public/img/img/') . $name . '.' . Str::afterLast($type, '/'));

        $web = imagewebp($im, storage_path("app/public/img/" . $dir . "_webp/" . $name . ".webp"), "80");
        $url = "/storage/" . $dir . "_webp/" . $name . ".webp";
    } else {
        if (isset($file)) {
            $data = substr($file, strpos($file, ',') + 1);
            $pos = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $name = uniqid();
            $path = storage_path('app/public/img/' . $dir . "_webp/");
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $url = Storage::disk('public')->put('img/' . $name . '.' . Str::afterLast($type, '/'), base64_decode($data));
            $im = imagecreatefromfile(storage_path('app/public/img/img/') . $name . '.' . Str::afterLast($type, '/'));

            $web = imagewebp($im, storage_path("app/public/img/" . $dir . "_webp/" . $name . ".webp"), "80");
            // $url = Storage::disk('public')->url($dir."_webp/" . $name  . ".webp");
            $url = "/storage/" . $dir . "_webp/" . $name . ".webp";
        }
    }
    return $url;
}

function saveImage($file)
{
    $url = "";
    if (preg_match('/^data:image\/(\w+);base64,/', $file)) {
        $data = substr($file, strpos($file, ',') + 1);
        $pos = strpos($file, ';');
        $type = explode(':', substr($file, 0, $pos))[1];
        $name = $random = Str::random(40);;
        //store in local storage
        //        $url =$request->ReportDocument->storeAs('/public/img', $imageName);
        //        $data['ReportDocument'] = storage_path($url);
        $url = Storage::disk('public')->put('img/' . $name . '.' . Str::afterLast($type, '/'), base64_decode($data));
        $url = Storage::disk('public')->url('img/' . $name . '.' . Str::afterLast($type, '/'));
    }
    return $url;
}

function ParentName($id = null)
{
    $parent = BlogCategory::where('id', $id)->first('Name');
    if ($parent) {
        return $parent->Name;
    } else {
        return $parent;
    }
}

function sendEmail($method = "", $from = "", $to = "", $cc = "", $bcc = "", $subject = "", $body = "", $callFrom = "", $agentId = null, $fromId = 0)
{
    $email_logs = "";
    if (env('RUNNING_DB_INFO') == "sql") {
        $email_logs = new \App\Models\SqlModel\EmailLogs();
    } else {
        $email_logs = new \App\Models\SqlModel\EmailLogs();
    }
    $data = [
        "FromEmail" => $from,
        "ToEmail" => is_array($to) ? implode(", ", $to) : $to,
        "ToCc" => $cc,
        "ToBcc" => $bcc,
        "Subject" => $subject,
        "Content" => $body,
        "Method" => $method,
        "FromMethod" => $callFrom,
        "DeliveredTime" => date("Y-m-d h:i:s"),
        "FromId" => $fromId
    ];
    if ($method == "SMTP" || $method == "") {
        $hashData = uniqid();
        $sendData = [
            "body" => $body,
            "from" => $from,
            "subject" => $subject,
            "agentId" => $agentId,
            "hashId" => env('LOGOURL') . "/" . $hashData
        ];
        try {
            //$content =  (new \App\Mail\SendMail($sendData))->render();
            $err = \Illuminate\Support\Facades\Mail::mailer('smtp')->to($to)->cc($cc)->bcc($bcc)->send(new \App\Mail\SendMailNew($sendData));
            $data["IsSent"] = 1;
            $data["HashId"] = $hashData;
            $email_logs->insertAndGetId($data);
            return true;
        } catch (Exception $exception) {
            print_r($exception);
            $data["IsSent"] = 0;
            $email_logs->insertAndGetId($data);
            return false;
        }
    }
    if ($method == "SIMPLEMAIL") {
        $hashData = uniqid();
        $sendData = [
            "body" => $body,
            "from" => $from,
            "subject" => $subject,
            "hashId" => env('LOGOURL') . "/" . $hashData
        ];
        try {
            \Illuminate\Support\Facades\Mail::mailer('sendmail')->to($to)->cc($cc)->bcc($bcc)->send(new \App\Mail\SendMail($sendData));
            $data["IsSent"] = 1;
            $data["hashId"] = $hashData;
            $email_logs->insertAndGetId($data);
            return true;
        } catch (Exception $exception) {
            $data["IsSent"] = 0;
            $email_logs->insertAndGetId($data);
            return false;
        }
    }
}

function Websetting($id = null)
{
    $userdata = "";
    $user = auth()->user();
    if (auth()->user()->person_id == '3') {
        //        $data['AdminId']=auth()->user()->AdminId;
        $userdata = Websetting::where('AdminId', auth()->user()->AdminId)->first();
    } elseif (auth()->user()->person_id == '2') {
        $id = auth()->user()->id;
        $userdata = Websetting::where('AdminId', $id)->first();
    }
    return $userdata;
}

function get_slug_url($property_data)
{
    $property_data = collect($property_data)->all();
    $custom_address = '';
    $custom_address .= isset($property_data['St_num']) ? $property_data['St_num'] . ' ' : '';
    if (isset($property_data['St_dir'])) {
        $enum_stprefix = $property_data['St_dir'];
        $custom_address .= $enum_stprefix . ' ';
    }
    $custom_address .= isset($property_data['St']) ? $property_data['St'] . ' ' : '';
    if (isset($property_data['St_sfx'])) {
        $enum_stprsufix = $property_data['St_sfx'];
        $custom_address .= $enum_stprsufix . ' ';
    }
    $custom_address .= isset($property_data['Apt_num']) ? $property_data['Apt_num'] . ' ' : '';
    $property_address = $custom_address;
    $property_address = preg_replace('/\s+/', '-', $property_address);
    $property_address = trim($property_address);
    $property_address = str_ireplace("-", " ", $property_address);
    $property_address = preg_replace('/\s+/', ' ', $property_address);
    $property_address = preg_replace('/\-+/', ' ', $property_address);
    $full_address = trim($property_address) . ', ' . $property_data['Municipality'] . " " . $property_data['County'] . " " . $property_data['Zip'];
    $full_address = str_ireplace(',', ' ', $full_address);
    $full_address = preg_replace('/\s+/', ' ', $full_address);

    $property_data['slug_url'] = str_ireplace(' ', '-', $full_address);
    $property_data['slug_url'] = preg_replace('/[^A-Za-z0-9\-\s]/', '-', $property_data['slug_url']);
    $property_data['slug_url'] = preg_replace('/\-+/', '-', $property_data['slug_url']);
    $property_data['slug_url'] = str_ireplace("/", '-', $property_data['slug_url']);
    $property_data['slug_url'] = str_ireplace("&", '-', $property_data['slug_url']);
    $property_data['slug_url'] = str_ireplace("'", '', $property_data['slug_url']);
    $property_data['slug_url'] = preg_replace('/\-+/', '-', $property_data['slug_url']);
    $property_data['slug_url'] = $property_data['slug_url'] . "-" . $property_data["Ml_num"];
    return $property_data["slug_url"];
}

function getsqft_min_max($data)
{
    $saveData = [];
    if (isset($data['Sqft'])) {
        $propArr = trim($data['Sqft']);
        $propArr = str_replace(" ", "", $propArr);
        $exp = array();
        $saveData = array();
        $SqftMax = 9999999;
        $SqftMin = 0;
        if (str_contains($propArr, '<')) {
            $exp = explode("<", $propArr);
            $flag = 1;
        }
        if (str_contains($propArr, '-')) {
            $exp = explode("-", $propArr);
            $flag = 2;
        }
        if (str_contains($propArr, '+')) {
            $exp = explode("+", $propArr);
            $flag = 3;
        }
        if (count($exp) > 0) {
            $saveData["sqftFlag"] = 1;
            if ($flag == 1) {
                if (isset($exp[0]) && $exp[0] !== "") {
                    $SqftMin = $exp[0];
                }
                if (isset($exp[1]) && $exp[1] !== "") {
                    $SqftMax = $exp[1];
                }
                $saveData["SqftMax"] = (int)$SqftMax;
                $saveData["SqftMin"] = (int)$SqftMin;
            }

            if ($flag == 2) {
                if (isset($exp[0]) && $exp[0] !== "") {
                    $SqftMin = $exp[0];
                }
                if (isset($exp[1]) && $exp[1] !== "") {
                    $SqftMax = $exp[1];
                }
                $saveData["SqftMax"] = (int)$SqftMax;
                $saveData["SqftMin"] = (int)$SqftMin;
            }

            if ($flag == 3) {
                if (isset($exp[0]) && $exp[0] !== "") {
                    $SqftMin = $exp[0];
                }
                if (isset($exp[1]) && $exp[1] !== "") {
                    $SqftMax = $exp[1];
                }
                $saveData["SqftMax"] = (int)$SqftMax;
                $saveData["SqftMin"] = (int)$SqftMin;
            }
        }
    }

    return $saveData;
}


function getSuperAdmin()
{
    $users = \App\Models\User::select("email")
        ->where("type", 2)
        ->first();
    return $users->email;
}

function getAdmin($id)
{
    $users = \App\Models\User::select("email")
        ->where("id", $id)
        ->first();
    return $users->email;
}

function get_visitor_IP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    // Sometimes the `HTTP_CLIENT_IP` can be used by proxy servers
    $ip = @$_SERVER['HTTP_CLIENT_IP'];
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    // Sometimes the `HTTP_X_FORWARDED_FOR` can contain more than IPs
    $forward_ips = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    if ($forward_ips) {
        $all_ips = explode(',', $forward_ips);

        foreach ($all_ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'];
}

function get_client_ip()
{
    $ipaddress = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    return $ipaddress;
}

function sendAlerts($all_mls = array(), $instance_flag = false, $insert_time_query = "")
{
    $tmp = ["instantly", "daily"];
    if ($instance_flag == 1) {
        $select_alert = \App\Models\SqlModel\SavedSearchFilter::where("enabled", 1)->where("subscribe", 1)->whereIn("frequency", $tmp)->get();
    } else {
        $select_alert = \App\Models\SqlModel\SavedSearchFilter::where("enabled", 1)->where("subscribe", 1)->where("frequency", "!=", "instantly")->get();
    }
    if (collect($select_alert)->count() > 0) {
        $select_alert = collect($select_alert)->all();
        foreach ($select_alert as $alert) {
            $need_to_send = 0;
            $insert_time_query = '';
            $alert_id = $alert['id'];
            $alert_freq = $alert['frequency'];
            $user_id = $alert['userId'];
            $alert_name = $alert['filterName'];
            $alert_query = \App\Models\SqlModel\AlertsLog::where("alertId", $alert_id)->whereNotNull("alertFreq")->orderBy('id', 'DESC')->limit(1)->first();
            if (collect($alert_query)->count() > 0) {
                $last_send_row = collect($alert_query)->all();
                $last_time_send = $last_send_row['sentAt'];
                $diff = time() - strtotime($last_time_send);
                $insert_time_query = " updated_time > '" . $last_time_send . "' ";
                $insert_time_query = $last_time_send;
                echo "\n alert_id = ".$alert_id;
                if ($alert_freq == 'daily' && $diff > 1 * 86400) {
                    $need_to_send = 1;
                    $new_starttime = strtotime("-1 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                } else if ($alert_freq == 'weekely' && $diff > 7 * 86400) {
                    $need_to_send = 1;
                    $new_starttime = strtotime("-7 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                } else if ($alert_freq == 'month' && $diff > 30 * 86400) {
                    $need_to_send = 1;
                    $new_starttime = strtotime("-30 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                }else if ($alert_freq == 'instantly') {
                    $need_to_send = 1;
                    //$new_starttime = strtotime("-30 days", time());
                    //$last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                } else {
                    $need_to_send = 1;
                }
            } else {
                $need_to_send = 1;
                if ($alert_freq == 'daily') {
                    $new_starttime = strtotime("-1 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                } else if ($alert_freq == 'weekely') {
                    $new_starttime = strtotime("-7 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                } else if ($alert_freq == 'month') {
                    $new_starttime = strtotime("-30 days", time());
                    $last_time_send = date('Y-m-d H:i:s', $new_starttime);
                    //$insert_time_query = " updated_time > '" . $last_time_send . "' ";
                    $insert_time_query = $last_time_send;
                }
            }
            if ($need_to_send) {
                $search_fields = createSearchDataArr($alert);
                $array_data = array();
                if (isset($search_fields["text_search"]) && $search_fields["text_search"] != "") {
                    $filteredData = get_search_result_common($search_fields, 0, 8, "ListPrice", "ASC", $search_fields["text_search"], "", $insert_time_query);
                } else {
                    $filteredData = get_search_result_common($search_fields, 0, 8, "ListPrice", "ASC", "", "", $insert_time_query);
                }
                // work needs to here
                $user_email = $user_phone = $user_name = '';
                $user_data = \App\Models\SqlModel\lead\LeadsModel::where("id", $user_id)->first();
                if (collect($user_data)->count() > 0) {
                    $user_data = collect($user_data)->all();
                    $user_email = $user_data['Email'];
                    $user_phone = $user_data['Phone'];
                    $user_name = $user_data['ContactName'];
                }
                $txt_email = '';
                $txt_sms = '';
                if (collect($filteredData)->count() > 0 && $filteredData != [] && is_array($filteredData) && collect($user_data)->count() > 0) {
                    $email_template_data["listings"] = $filteredData["result"];
                    $email_template_data["user_email"] = $user_email;
                    $email_template_data["username"] = $user_name;
                    try {
                        $emailHash = incrementalHash();
                        $userInsert = insertemailhash($emailHash, $alert_id);
                        $email_template_data["emailHash"] = $emailHash;
                    } catch (Exception $exception) {
                    }
                    $txt_email = view('emails.multipleListingsEmail', $email_template_data)->render();
                    $curr_date = date('Y-m-d');
                    $subject = "New Listing Notification - " . $alert_name . "";
                    $alert_log['alertId'] = $alert_id;
                    if ($alert['emailAlert'] == 1 && $filteredData["result"] != []) {
                        $alert_log['toEmail'] = $user_email;
                        $alert_log['emailContent'] = $txt_email;
                        //$user_email = 'sagar@peregrine-it.com';
                        sendEmail("SMTP", env('MAIL_FROM'), $user_email, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $txt_email, "Common Helper send Emails", "3", env('SAVEDSEARCH'));
                        $alert_log['sentAt'] = date('Y-m-d H:i:s');
                        $alert_log['alertFreq'] = $alert_freq;
                        $alert_log['userId'] = $user_id;
                        \App\Models\SqlModel\AlertsLog::create($alert_log);
                    }
                    if ($alert['text_alert'] == 1) {
                        $alert_log['toPhone'] = $user_phone;
                        $alert_log['smsContent'] = $txt_sms;
                        if ($user_phone != '') {
                            //send_sms();
                        }
                    }
                    echo "\n alerts sent module complete";
                }
            }
        }
    }
    sentWatchAlerts();
}

function sentWatchAlerts()
{
    $select_alert = SavedSearchFilter::where("enabled", 1)->where("subscribe", 1)->whereNotNull("watchListings")->get();
    if (($select_alert)->count() > 0) {
        foreach ($select_alert as $alert) {
            $insert_time_query = '';
            $user_id = $alert['userId'];
            $alert_freq = "daily";
            $alert_id = $alert['id'];
            $alert_name = $alert['filterName'];
            $need_to_send = 0;
            $need_to_send = 1;
            // $new_starttime2 = strtotime("-10 hours");
            // $last_time_send2 = date('Y-m-d H:i:s', $new_starttime2);
            $new_starttime = strtotime("-6 hours", time());
            $last_time_send = date('Y-m-d H:i:s', $new_starttime);
            $insert_time_query = $last_time_send;
            $watchList = json_decode($alert->watchListings, true);
            $search_fields = createSearchDataArr($alert);
            $filteredData = array(
                "result" => [],
                "total" => 0,
            );
            $subject = "Watch Listings Alerts";
            $soldSelectData = PropertyConstants::SELECT_SOLD_DATA;
            if (isset($watchList['isSold']) && $watchList['isSold']) {
                $listingId = $alert->ListingId;
                // $listingId='C4655939';
                /*$status = array(
                    "Sld",
                    "Lsd",
                    "Exp",
                    "Lc",
                    "Ter",
                    "Sus"
                );*/
                $status = array(
                    "Sld",
                );
                $results = RetsPropertyDataPurged::select($soldSelectData)->where("ListingId", $listingId)->where("inserted_time", ">=", $last_time_send)->whereIn("LastStatus", $status)->where("PropertyType","Condos")->get();
                $total = count($results);
                $filteredData['result'] = $results;
                $filteredData['total'] = $total;
                if ($total) {
                    $subject = $total . " watched listing update ";
                    sendWatchAlerts($filteredData, $user_id, $subject, $alert, $alert_freq);
                }
            } else {

                $selectData = ['id'];
                if (isset($watchList['City'])) {
                    $search_fields["City"] = $watchList['City'];
                }
                if (isset($watchList['Community'])) {
                    $search_fields["Community"] = $watchList['Community'];
                }
                if (isset($watchList['AlertsOn'])) {
                    $countActive = $countSold = $countDelisted = [];
                    $AlertsOn = $watchList['AlertsOn'];
                    if (isset($AlertsOn['NewListings']) && $AlertsOn['NewListings']) {
                        $countActive = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Community', $watchList['Community'])->where("Status", "A")->where('inserted_time', '>=', $insert_time_query)->where("PropertyType","Condos")->get();
                        $total = count($countActive);
                        $filteredData['result'] = $countActive;
                        $filteredData['total'] = $total;
                        if ($total) {
                            $subject = $total . " Active listings in your watched areas for community ".$watchList['Community'];
                            sendWatchAlerts($filteredData, $user_id, $subject, $alert, $alert_freq);
                        }
                    }
                    if (isset($AlertsOn['SoldListings']) && $AlertsOn['SoldListings']) {
                        $countSold = RetsPropertyDataPurged::select($soldSelectData)->where('Community', $watchList['Community'])->where("LastStatus", "Sld")->where('inserted_time', '>=', $insert_time_query)->where("PropertyType","Condos")->distinct()->get();
                        $total = count($countSold);
                        $filteredData['result'] = $countSold;
                        $filteredData['total'] = $total;
                        if ($total) {
                            $subject = $total . " sold listings in your watched areas for community ".$watchList['Community'];
                            sendWatchAlerts($filteredData, $user_id, $subject, $alert, $alert_freq);
                        }
                    }
                    if (isset($AlertsOn['DelistedListings']) && $AlertsOn['DelistedListings']) {
                        $countDelisted = RetsPropertyDataPurged::select($soldSelectData)->where('Community', $watchList['Community'])->where("LastStatus", "Ter")->where('inserted_time', '>=', $insert_time_query)->where("PropertyType","Condos")->distinct()->get();
                        $total = count($countDelisted);
                        $filteredData['result'] = $countDelisted;
                        $filteredData['total'] = $total;
                        if ($total) {
                            $subject = $total . "Delisted listings in your watched areas for community ".$watchList['Community'];
                            sendWatchAlerts($filteredData, $user_id, $subject, $alert, $alert_freq);
                        }
                    }
                }
            }
        }
    }
}

function sendWatchAlerts($filteredData, $user_id, $subject, $alert, $alert_freq)
{
    // work needs to here
    $alert_id = $alert['id'];
    $user_email = $user_phone = $user_name = '';
    $user_data = LeadsModel::where("id", $user_id)->first();
    if (collect($user_data)->count() > 0) {
        $user_data = collect($user_data)->all();
        $user_email = $user_data['Email'];
        $user_phone = $user_data['Phone'];
        $user_name = $user_data['ContactName'];
    }
    $txt_email = '';
    $txt_sms = '';

    if (collect($filteredData)->count() > 0 && $filteredData != [] && is_array($filteredData) && collect($user_data)->count() > 0) {

        $email_template_data["listings"] = $filteredData["result"];
        $email_template_data["user_email"] = $user_email;
        $email_template_data["username"] = $user_name;

        try {
            $emailHash = incrementalHash();
            $userInsert = insertemailhash($emailHash, $alert_id);
            $email_template_data["emailHash"] = $emailHash;
        } catch (Exception $exception) {
            echo "\n error occured";
        }
        $txt_email = view('emails.watchMultipleListingsEmail', $email_template_data)->render();
        $curr_date = date('Y-m-d');
        $alert_log['alertId'] = $alert_id;
        if ($filteredData["result"] != []) {

            $alert_log['toEmail'] = $user_email;
            $alert_log['emailContent'] = $txt_email;
            //$user_email = 'sagar@peregrine-it.com';
            sendEmail("SMTP", env('MAIL_FROM'), $user_email, env('ALERT_CC_EMAIL_ID'), env('ALERT_BCC_EMAIL_ID'), $subject, $txt_email, "Common Helper send Emails", "3", env('SAVEDSEARCH'));
            $alert_log['sentAt'] = date('Y-m-d H:i:s');
            $alert_log['alertFreq'] = $alert_freq;
            $alert_log['userId'] = $user_id;
            AlertsLog::create($alert_log);
        }
        if ($alert['text_alert'] == 1) {
            $alert_log['toPhone'] = $user_phone;
            $alert_log['smsContent'] = $txt_sms;
            if ($user_phone != '') {
                //send_sms();
            }
        }
        echo "\n alerts sent module complete";
    }
}

function createSearchDataArr($data)
{
    $returnArr = array();
    if (isset($data['sub_class']) && !empty($data['sub_class'])) {
        $returnArr['subClass'] = $data['sub_class'];
    }
    if (isset($data['bedsTotal']) && !empty($data['bedsTotal'])) {
        $returnArr['beds'] = $data['bedsTotal'];
    }
    if (isset($data['bathsFull']) && !empty($data['bathsFull'])) {
        $returnArr['baths'] = $data['bathsFull'];
    }
    if (isset($data['GarType']) && !empty($data['GarType'])) {
        $returnArr['Gar_type'] = $data['GarType'];
    }
    if (isset($data['lotSizeAreaMax']) && !empty($data['lotSizeAreaMax'])) {
        $returnArr['SqftMax'] = $data['lotSizeAreaMax'];
    }
    if (isset($data['textSearch']) && !empty($data['textSearch'])) {
        $returnArr['text_search'] = $data['textSearch'];
    }
    if (isset($data['priceMin']) && !empty($data['priceMin'])) {
        $returnArr['price_min'] = $data['priceMin'];
    }
    if (isset($data['priceMax']) && !empty($data['priceMax'])) {
        $returnArr['price_max'] = $data['priceMax'];
    }
    if (isset($data['sqftMin']) && !empty($data['sqftMin'])) {
        $returnArr['SqftMin'] = $data['sqftMin'];
    }
    if (isset($data['sqftMax']) && !empty($data['sqftMax'])) {
        $returnArr['SqftMax'] = $data['sqftMax'];
    }
    if (isset($data['city']) && !empty($data['city'])) {
        $returnArr['City'] = $data['city'];
    }
    if (isset($data['countyName']) && !empty($data['countyName'])) {
        $returnArr['County'] = $data['countyName'];
    }
    if (isset($data['dom']) && !empty($data['dom'])) {
        $returnArr['Dom'] = $data['dom'];
    }
    if (isset($data['shape']) && !empty($data['shape'])) {
        $returnArr['shape'] = $data['shape'];
    }
    if (isset($data['currPathQuery']) && !empty($data['currPathQuery'])) {
        $returnArr['curr_path_query'] = $data['currPathQuery'];
    }
    if (isset($data['currBounds']) && !empty($data['currBounds'])) {
        $returnArr['curr_bounds'] = $data['currBounds'];
    }
    if (isset($data['latitude']) && !empty($data['latitude'])) {
        $returnArr['Latitude'] = $data['latitude'];
    }
    if (isset($data['longitude']) && !empty($data['longitude'])) {
        $returnArr['Longitude'] = $data['longitude'];
    }
    if (isset($data['radius']) && !empty($data['radius'])) {
        $returnArr['curr_radius'] = $data['radius'];
    }
    if (isset($data['className']) && !empty($data['className'])) {
        $returnArr['PropertyType'] = $data['className'];
    }
    if (isset($data['status']) && !empty($data['status'])) {
        $returnArr['status'] = $data['status'];
    }

    if (isset($data['propertySubType']) && !empty($data['propertySubType'])) {
        $returnArr['PropertySubType'] = $data['propertySubType'];
    }
    if (isset($data['multiplePropType']) && !empty($data['multiplePropType'])) {
        $returnArr['multiplePropType'] = $data['multiplePropType'];
    }
    if (isset($data['Bsmt1Out']) && !empty($data['Bsmt1Out'])) {
        $returnArr['basement'] = $data['Bsmt1Out'];
    }
    if (isset($data['features']) && !empty($data['features'])) {
        $returnArr['features'] = $data['features'];
    }
    return $returnArr;
}

function get_search_result_common($condition, $offset, $limit, $sortBy = 'ListPrice', $order = 'ASC', $textSearchField = '', $type = "", $inserted_time_query = "")
{
    $query = \App\Models\RetsPropertyData::query();
    $query->select(\App\Constants\PropertyConstants::SELECT_DATA);
    //$query->where("ImageUrl", "!=", "");
    if (isset($condition['beds'])) {
        $query->where('BedroomsTotal', '>=', (int)$condition['beds']);
        unset($condition['beds']);
    }
    if (isset($condition['baths'])) {
        $query->where('BathroomsFull', '>=', (int)$condition['baths']);
        unset($condition['baths']);
    }
    /*if (isset($condition['PropertySubType']) && $condition['PropertySubType'] !== []) {
        $query->where('PropertySubType', $condition['PropertySubType']);
    }*/
    if (isset($condition['PropertySubType'])) {
        if (count($condition['PropertySubType'])) {
            if (in_array("Condo Townhouse", $condition['PropertySubType']) || in_array("Condo Apt", $condition['PropertySubType'])) {
                $condition['PropertyType'] = "Condos";
            }
        }
    }
    if (isset($condition['PropertyType'])) {
        $query->where('PropertyType', $condition['PropertyType']);
    }
    //
    if (isset($condition['Dom'])) {
        $query->where('Dom', '<=', (float)$condition['Dom']);
    }
    if (isset($condition['multiplePropType'])) {
        if (count($condition['multiplePropType'])) {
            $query->whereIn('PropertySubType', $condition['multiplePropType']);
        }
    }
    if (isset($condition['PropertySubType'])) {
        if (count($condition['PropertySubType'])) {
            $query->whereIn('PropertySubType', $condition['PropertySubType']);
        }
    }
    if (isset($condition['features'])) {
        $ids = PropertyFeatures::whereIn("FeaturesId", $condition['features'])->groupBy("PropertyId")->pluck("PropertyId")->toArray();
        if (is_array($ids) && count($ids)) {
            $query->whereIn('ListingId', $ids);
        }
    }

    if (isset($condition['price_min'])) {
        $query->where('ListPrice', '>=', (int)$condition['price_min']);
    }
    if (isset($condition['price_max'])) {
        $query->where('ListPrice', '<=', (int)$condition['price_max']);
    }
    if (isset($condition['status'])) {
        $query->where('PropertyStatus', $condition['status']);
    }
    if (isset($condition['basement'])) {
        if (count($condition['basement'])) {
            $query->whereIn('Bsmt1_out', $condition['basement']);
        }
    }
    if (isset($condition['Sqft'])) {
        if (str_contains($condition['Sqft'], '-')) {
            $exp = explode("-", $condition['Sqft']);
            $query->where('SqftMin', '>=', (int)$exp[0]);
            $query->where('SqftMax', '<=', (int)$exp[1]);
        }
        unset($condition['Sqft']);
    }
    // $result = $query->toSql();
    unset($condition['text_search']);
    unset($condition['Sqft']);
    unset($condition['radius']);
    unset($condition['curr_path']);
    unset($condition['curr_path_query']);
    unset($condition['center_lat']);
    unset($condition['center_lng']);
    unset($condition['shape']);
    unset($condition['basement']);
    unset($condition['features']);
    unset($condition['multiplePropType']);
    unset($condition['openhouse']);
    unset($condition['PropertySubType']);
    unset($condition['price_min']);
    unset($condition['Dom']);
    unset($condition['PropertyType']);
    unset($condition['status']);
    unset($condition['price_min']);
    unset($condition['price_max']);
    unset($condition['beds']);
    unset($condition['baths']);
    unset($condition['PropertySubType']);
    if (isset($condition['Gar'])) {
        $query->where('Gar', $condition['Gar']);
        unset($condition['Gar']);
    }
    if (isset($condition['Park_spcs'])) {
        $query->where('Park_spcs', $condition['Park_spcs']);
        unset($condition['Park_spcs']);
    }
    if (isset($condition['Pool'])) {
        $query->where('Pool', '!=', $condition['Pool']);
        unset($condition['Pool']);
    }

    if (!empty($condition)) {
        $query->where($condition);
    }
    if ($textSearchField) {

        $query->where(function ($q) use ($textSearchField) {
            $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
            $q->orWhere('Municipality', 'like', $textSearchField . '%');
            $q->orWhere('ListingId', $textSearchField);
            $q->orWhere('City', $textSearchField);
            $q->orWhere('County', $textSearchField);
            $q->orWhere('Community', $textSearchField);
        });
    }
    $query->whereNotNull('Latitude');
    $query->whereNotNull('Longitude');
    //$query->where("updated_time",">",$inserted_time_query);
    // $query->where("Status", "=", "A");
    //$query->whereNotNull('ListPrice');
    if ($inserted_time_query !== "") {
        $query->where("inserted_time", ">", $inserted_time_query);
    }
    $query->orderBy('ContractDate', 'DESC');
    $total = $query->count();
    //$total = 22000;
    $query->skip($offset);
    $query->take($limit);
    $result = $query->get();
    //    /dd($result);
    // $result = $query->with('propertiesImges')->pluck("County");
    if ($result && count($result) > 0) {
        $final_result = array(
            "result" => $result,
            "total" => $total,
        );
        return $final_result;
    } else {
        $final_result = array(
            "result" => [],
            "total" => $total,
        );
        return $final_result;
    }
}

function getMedianbck($numbers = array())
{
    if (!is_array($numbers))
        $numbers = func_get_args();
    rsort($numbers);
    $mid = (int)(count($numbers) / 2);
    return ($mid % 2 != 0) ? $numbers[$mid] : (($numbers[$mid - 1]) + $numbers[$mid]) / 2;
}

function getMedian($arr)
{
    //Make sure it's an array.
    if (!is_array($arr)) {
        throw new Exception('$arr must be an array!');
    }
    //If it's an empty array, return FALSE.
    if (empty($arr)) {
        return false;
    }
    //Count how many elements are in the array.
    $num = count($arr);
    //Determine the middle value of the array.
    $middleVal = floor(($num - 1) / 2);
    //If the size of the array is an odd number,
    //then the middle value is the median.
    if ($num % 2) {
        return $arr[$middleVal];
    }
    //If the size of the array is an even number, then we
    //have to get the two middle values and get their
    //average
    else {
        //The $middleVal var will be the low
        //end of the middle
        $lowMid = $arr[$middleVal];
        $highMid = $arr[$middleVal + 1];
        //Return the average of the low and high.
        return (($lowMid + $highMid) / 2);
    }
}

function LeadNotification()
{
    // 5 days older data will get
    $query = LeadsModel::where('Seen', '1')->where('AssignedAgent', auth()->user()->AdminId)->orderBy('id', 'Desc');
    $data['count'] = $query->count();
    $data['notification'] = $query->limit(4)->get('ContactName', 'Message');
    return $data;
}

function saveNotificationData($data)
{
    if (!empty($data)) {
        \App\Models\SqlModel\Notifications::create($data);
        return true;
    } else {
        return false;
    }
}

function largest($x, $y, $z)
{
    $max = $x;

    if ($x >= $y && $x >= $z)
        $max = $x;
    if ($y >= $x && $y >= $z)
        $max = $y;
    if ($z >= $x && $z >= $y)
        $max = $z;
    return $max;
}
function housenMapSearch($condition, $offset, $limit, $sortBy = 'Timestamp_sql', $order = 'DESC', $textSearchField = '', $extra_custom_query = '', $extra_select = '', $shape = '', $array_data = array(), $orFilter = array(), $type = "", $isDefault = false, $isTotal = false)
{
    $isSoldSearch = false;
    $isAddSearch = false;
    $isEnterSearch = false;
    $group = "";
    $ReqFilters = [];
    $selectData = [];
    if ($type == "main") {
        $selectData = PropertyConstants::SELECT_DATA;
    } elseif ($type == "map") {
        $selectData = PropertyConstants::MAP_MARKERS_SELECT_DATA;
    } else {
        $flag = false;
        $selectData = PropertyConstants::MAP_SELECT_DATA;
    }
    if (isset($condition['beds'])) {
        $ReqFilters['BedroomsTotal'] = (int)$condition['beds'];
        unset($condition['beds']);
    }
    if (isset($condition['baths'])) {
        $ReqFilters['BathroomsFull'] = (int)$condition['baths'];
        unset($condition['baths']);
    }
    if (isset($condition['PropertyType'])) {
        //$ReqFilters['PropertyType'] = $condition['PropertyType'];
        unset($condition['baths']);
    }
    if (isset($condition['Dom'])) {
        $ReqFilters['Dom'] = $condition['Dom'];
    }
    if (isset($condition['price_min'])) {
        $ReqFilters['price_min'] = $condition['price_min'];
    }
    if (isset($condition['price_max'])) {
        $ReqFilters['price_max'] = $condition['price_max'];
    }
    if (isset($condition['status'])) {
        $ReqFilters['PropertyStatus'] = $condition['status'];
    }
    if (isset($condition['soldStatus'])) {
        $ReqFilters['Status'] = "A";
        if ($condition['soldStatus'] == "U") {
            $ReqFilters['Status'] = "U";
            $isSoldSearch = true;
            if ($condition['status'] == "Lease") {
                $ReqFilters["LastStatus"] = "Lsd";
            }
            if ($condition['status'] == "Sale") {
                $ReqFilters["LastStatus"] = "Sld";
            }
        }
        if ($condition['soldStatus'] == "D") {
            $isSoldSearch = true;
            $ReqFilters['Status'] = "U";
            $ReqFilters["LastStatus"] = "Ter";
        }
        unset($condition['soldStatus']);
    }
    if (isset($condition['Sqft'])) {
        if (str_contains($condition['Sqft'], '-')) {
            $exp = explode("-", $condition['Sqft']);
            $ReqFilters['SqftMin'] = $exp[0];
            $ReqFilters['SqftMax'] = $exp[1];
        }
        unset($condition['Sqft']);
    }
    if (isset($condition['PropertySubType'])) {
        $ReqFilters['PropertySubType'] = $condition['PropertySubType'];
    }
    if (isset($condition['features'])) {
        $ids = PropertyFeatures::whereIn("FeaturesId", $condition['features'])->groupBy("PropertyId")->pluck("PropertyId")->toArray();
        if (is_array($ids) && count($ids)) {
            $ReqFilters['features'] = $ids;
        }
    }
    if (isset($condition['basement'])) {
        $ReqFilters['basement'] = $condition['basement'];
    }
    unset($condition['soldStatus']);
    unset($condition['Sqft']);
    unset($condition['features']);
    unset($condition['basement']);
    unset($condition['PropertySubType']);
    unset($condition['radius']);
    unset($condition['curr_path']);
    unset($condition['curr_path_query']);
    unset($condition['center_lat']);
    unset($condition['center_lng']);
    unset($condition['shape']);
    unset($condition['multiplePropType']);
    unset($condition['openhouse']);
    unset($condition['price_min']);
    unset($condition['Dom']);
    unset($condition['PropertyType']);
    unset($condition['status']);
    unset($condition['price_min']);
    unset($condition['price_max']);
    unset($condition['beds']);
    unset($condition['baths']);
    if (isset($condition['Gar'])) {
        $ReqFilters['Gar'] = $condition['Gar'];
        unset($condition['Gar']);
    }
    if (isset($condition['Park_spcs'])) {
        $ReqFilters['Park_spcs'] = $condition['Park_spcs'];
        unset($condition['Park_spcs']);
    }
    if (isset($condition['Pool'])) {
        $ReqFilters['Pool'] = $condition['Pool'];
        unset($condition['Pool']);
    }
    if (!empty($array_data)) {
        foreach ($array_data as $key => $value) {
            $ReqFilters[$key] = $value;
        }
    }
    // if (!empty($orFilter)) {
    //     $query->orWhere($orFilter);
    // }
    if ($textSearchField) {
        if (isset($condition['group'])) {
            if ($condition['group'] === "ListingId") {
                $isAddSearch = true;
            }
            $ReqFilters[$condition['group']] = "$textSearchField";
        } else {
            $isAddSearch = true;
            $isEnterSearch = true;
        }
    }
    if (isset($condition['StandardAddress'])) {
        // unset($condition['StandardAddress']);
        // $ReqFilters["StandardAddress"] = $condition['StandardAddress'];
    }
    $total_temp = 0;
    if ($isSoldSearch) {
        // $ReqFilters["Status"] = "U";
        $query = RetsPropertyDataPurged::query();
        $query->select($selectData);
    } else {
        $ReqFilters["Status"] = "A";
        $query = RetsPropertyData::query();
        $query->select($selectData);
    }
    foreach ($ReqFilters as $key => $data) {
        $flags = true;
        if ($key === "BedroomsTotal") {
            $query->where($key,   $data);
            $flags = false;
        }
        if ($key === "BathroomsFull") {
            $query->where($key,   $data);
            $flags = false;
        }
        /*if ($key === "Dom") {
            $c_date = new \DateTime();
            $startDate = $c_date->modify("-$data day");
            $startDate = $startDate->format('Y-m-d');
            $query->where('Timestamp_sql', '>=', $startDate);
            $flags = false;
        }*/
        if ($key === "Dom") {
            /*$c_date = new \DateTime();
            $startDate = $c_date->modify("-$data day");
            $startDate = $startDate->format('Y-m-d');
            //$query->where('Timestamp_sql', '>=', $startDate);
            if($sortBy=="ListPrice"){
                $dm = 'Timestamp_sql';
            }
            else{
                $dm= $sortBy;
            }
            $query->where($dm, '>=', $startDate);*/
            $temp_date = str_replace("+","",$data);
            $c_date = new \DateTime();
            $startDate = $c_date->modify("-$temp_date day");
            $startDate = $startDate->format('Y-m-d');
            //$query->where('Timestamp_sql', '>=', $startDate);
            if($sortBy=="Price"){
                if($sortBy=="Price" && $ReqFilters['Status'] != "U"){
                    $dm = 'ContractDate';
                }

            }
            else{
                $dm= $sortBy;
            }
            if (strpos($data, "+") !== false) {
                $query->where($dm, '<=', $startDate);
            } else {
                $query->where($dm, '>=', $startDate);
            }
            $flags = false;
        }
        if ($key === "price_min") {
            $query->where('Price', '>=', $data);
            $flags = false;
        }
        if ($key === "price_max") {
            $query->where('Price', '<=', $data);
            $flags = false;
        }

        if ($key === "SqftMin") {
            $query->where($key, '>=', $data);
            $flags = false;
        }
        if ($key === "SqftMax") {
            $query->where($key, '<=', $data);
            $flags = false;
        }
        if ($key === "PropertySubType") {
            if (is_array($data)) {
                if (count($data)) {
                    $query->whereIn($key, $data);
                }
            }
            $flags = false;
        }
        if ($key === "features") {
            if (is_array($data)) {
                if (count($data) > 0) {
                    $query->whereIn("ListingId", $data);
                }
            }
            $flags = false;
        }
        if ($key === "basement") {
            if (is_array($data)) {
                if (count($data) > 0) {
                    $query->whereIn('Bsmt1_out', $data);
                }
            }
            $flags = false;
        }
        if ($flags) {
            $query->where($key, $data);
        }
    }


    if ($isDefault) {
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
            } else {
                $query->whereRaw($extra_custom_query);
            }
        }
        $total_temp = $query->count();
        $total = RetsPropertyData::count();
    } else {
        if (isset($extra_custom_query) && $extra_custom_query != '') {
            if (isset($shape) && $shape != '' && $shape == 'circle') {
                $query->select($extra_select);
                $query->having($extra_custom_query);
            } else {
                $query->whereRaw($extra_custom_query);
            }
        }
        if ($isEnterSearch) {
            $query->where(function ($q) use ($textSearchField) {
                $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
                $q->orWhere('ListingId', 'like', $textSearchField . '%');
                $q->orWhere('City', $textSearchField);
                $q->orWhere('Community', $textSearchField);
            });
        }
        $total = $query->count();
    }

    DB::enableQueryLog();
    if (!$isSoldSearch && !$total && $isAddSearch) {
        $query = "";
        $query = RetsPropertyDataPurged::query();
        $query->select($selectData);
        // $ReqFilters["Status"] = "U";
        unset($ReqFilters['Status']);
        foreach ($ReqFilters as $key => $data) {
            $flags = true;
            if ($key === "BedroomsTotal") {
                $query->where($key,   $data);
                $flags = false;
            }
            if ($key === "BathroomsFull") {
                $query->where($key,   $data);
                $flags = false;
            }
            if ($key === "Dom") {
                $c_date = new \DateTime();
                $startDate = $c_date->modify("-$data day");
                $startDate = $startDate->format('Y-m-d');
                $query->where('Timestamp_sql', '>=', $startDate);
                $flags = false;
            }
            if ($key === "price_min") {
                $query->where('ListPrice', '>=', $data);
                $flags = false;
            }
            if ($key === "price_max") {
                $query->where('ListPrice', '<=', $data);
                $flags = false;
            }
            if ($key === "SqftMin") {
                $query->where($key, '>=', $data);
                $flags = false;
            }
            if ($key === "SqftMax") {
                $query->where($key, '<=', $data);
                $flags = false;
            }
            if ($key === "PropertySubType") {
                if (is_array($data)) {
                    if (count($data)) {
                        $query->whereIn($key, $data);
                    }
                }
                $flags = false;
            }
            if ($key === "features") {
                if (is_array($data)) {
                    if (count($data) > 0) {
                        $query->whereIn("ListingId", $data);
                    }
                }
                $flags = false;
            }
            if ($key === "basement") {
                if (is_array($data)) {
                    if (count($data) > 0) {
                        $query->whereIn('Bsmt1_out', $data);
                    }
                }
                $flags = false;
            }
            if ($flags) {
                $query->where($key, $data);
            }
        }
        if ($isEnterSearch) {
            $query->where(function ($q) use ($textSearchField) {
                $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
                $q->orWhere('ListingId', 'like', $textSearchField . '%');
                $q->orWhere('City', $textSearchField);
                $q->orWhere('Community', $textSearchField);
            });
        }

        $total = $query->count();
        //$total = 10000;
        $query->orderBy($sortBy, $order);
        $query->skip($offset);
        $query->take($limit);
        $result = $query->get();
        $queryLog = DB::getQueryLog();
    } else {
        // if ($isEnterSearch) {
        //     $query->where(function ($q) use ($textSearchField) {
        //         $q->orWhere('StandardAddress', 'like', $textSearchField . '%');
        //         $q->orWhere('ListingId', $textSearchField);
        //         $q->orWhere('City', $textSearchField);
        //         $q->orWhere('Community', $textSearchField);
        //     });
        // }
        $total = $query->count();
        //$total = 10000;
        $query->orderBy($sortBy, $order);
        $query->skip($offset);
        $query->take($limit);
        $result = $query->get();
        $queryLog = DB::getQueryLog();
    }
    if ($result && count($result) > 0) {
        $final_result = array(
            "result" => $result,
            "total" => $total,
            "total_temp" => $total_temp,
            "query" => $queryLog
        );
    } else {
        $final_result = array(
            "result" => false,
            "total" => $total,
            "total_temp" => $total_temp,
            "query" => $queryLog
        );
    }
    return $final_result;
}
//By siddharth for increamental Hash for unsubscribe email
function incrementalHash()
{
    $hash = substr(md5(microtime()), rand(0, 26), 15);
    return $hash;
}
function insertemailhash($hash = NULL, $user_id = NULL)
{
    if (isset($hash) && isset($user_id)) {
        $users = \App\Models\SqlModel\SavedSearchFilter::select("emailHash", "id")->where("id", $user_id)->first();
        if (isset($users) && !empty($users)) {
            $users->emailHash = $hash;
            $users->save();
        }
    }
}
///
function updateHomePageJson()
{
    $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'bodyscriptTag', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag', 'TopBanner', 'FbAppId', 'GoogleClientId', 'OfficeName')
        ->where("AdminId", env('HOUSENAGENTID'))
        ->first();
    $seo = Pages::select('MetaTitle', 'MetaDescription', 'MetaTags', 'Setting')->where('PageName', "home")
        ->where("AgentId", env('HOUSENAGENTID'))
        ->first();
    $response['websetting'] = collect($websetting)->all();
    if ($seo && $seo->Setting != '') {
        $response['pageSetting'] = json_decode($seo->Setting, true);
        $arrangeSection = json_decode($response['pageSetting']["ArrangeSection"]);
        $arrangeSections = [];
        foreach ($arrangeSection[0] as $key => $value) {
            $arrangeSections[] = $value->value;
        }
        unset($response['pageSetting']->ArrangeSection);
        $response['arrangeSections'] = collect($arrangeSections)->all();
    }
    unset($seo['Setting']);
    $response['seo'] = collect($seo)->all();
    $response = collect($response)->all();

    // now for recentListing
    $PropertyType = "Residential";
    $preDefineCity = [
        "Brampton",
        "Mississauga",
        "Toronto",
        "Vaughan",
        "Milton",
        "Oakville",
        "Burlington",
        "Ajax",
        "Whitby",
        "Pickering",
        "Markham",
        "Richmond Hill",
        "Newmarket"
    ];
    $PredefinedPropertySubType = [
        "Detached",
        "Semi-Detached",
        "Att/Row/Townhouse",
        "Condo Townhouse",
        "Condo Apt"
    ];
    $minPrice = 450000;
    $maxPrice = 2500000;
    $city = null;
    $propertySubType = null;
    $Dom = 2;
    $query1 = RetsPropertyData::select(PropertyConstants::SELECT_DATA)->where('Status', 'A');
    if ($maxPrice != null) {

        $query1 = $query1->where('ListPrice', '<=', (int)$maxPrice);
    }
    if ($minPrice != null) {

        $query1 = $query1->where('ListPrice', '>=', (int)$minPrice);
    }
    if ($city != null && count($city) > 0) {

        $query1 = $query1->whereIn('City', $city);
    } else {
        $query1 = $query1->whereIn('City', $preDefineCity);
    }
    if ($propertySubType != null && count($propertySubType) > 0) {

        $query1 = $query1->whereIn('PropertySubType', $propertySubType);
    } else {
        $query1 = $query1->whereIn('PropertySubType', $PredefinedPropertySubType);
    }
    $limit = 18;
    $data = $query1->where('Dom', '<', (float)$Dom)->where('PropertyStatus', 'Sale')->whereNotNull('ImageUrl')->orderBy('inserted_time', 'desc')->limit($limit)->get();
    $images = [];
    if (1) {
        if ($data) {
            foreach ($data as $key => $value) {
                $listing_id = $value->ListingId;
                $images[$listing_id] = RetsPropertyDataImage::select('s3_image_url')->where('listingID', $listing_id)->limit(5)->get();
                $images[$listing_id] = collect($images[$listing_id])->unique('s3_image_url')->values()->all();
            }
        }
        $response['recentProperty'] = collect($data)->toArray();
        $final_props = [];
        foreach ($response["recentProperty"] as &$property) {
            $final_props[] = getDom($property);
        }
        $response["recentProperty"] = $final_props;
        $response['recentPropertyimages'] = collect($images)->toArray();
    } else {
        $response_data = collect($data)->toArray();
    }


    // for sold data
    $PropertyType = "Residential";
    $preDefineCity = [
        "Brampton",
        "Mississauga",
        "Toronto",
        "Vaughan",
        "Milton",
        "Oakville",
        "Burlington",
        "Ajax",
        "Whitby",
        "Pickering",
        "Markham",
        "Richmond Hill",
        "Newmarket"
    ];
    $PredefinedPropertySubType = [
        "Detached",
        "Semi-Detached",
        "Att/Row/Townhouse",
        "Condo Townhouse",
        "Condo Apt"
    ];
    $minPrice = 450000;
    $maxPrice = 2500000;
    $city = null;
    $propertySubType = null;
    $query1 = RetsPropertyDataPurged::select(PropertyConstants::SELECT_DATA);
    if ($maxPrice != null) {
        $query1 = $query1->where('ListPrice', '<=', (int)$maxPrice);
    }
    if ($minPrice != null) {

        $query1 = $query1->where('ListPrice', '>=', (int)$minPrice);
    }
    if ($city != null && count($city) > 0) {

        $query1 = $query1->whereIn('City', $city);
    } else {
        $query1 = $query1->whereIn('City', $preDefineCity);
    }

    if ($propertySubType != null && count($propertySubType) > 0) {

        $query1 = $query1->whereIn('PropertySubType', $propertySubType);
    } else {
        $query1 = $query1->whereIn('PropertySubType', $PredefinedPropertySubType);
    }
    $limit = 6;
    $data = $query1->where('LastStatus', "Sld")->orderBy('Sp_date', 'desc')->limit($limit)->get();
    $images = [];
    if (1) {
        if ($data) {
            foreach ($data as $key => $value) {
                $listing_id = $value->ListingId;
                $img = \App\Models\RetsPropertyDataImagesSold::select('image_urls')->where('listingID', $listing_id)->get();
                if(collect($img)->all() != []){
                    $img = json_decode($img[0]["image_urls"]);
                    $tmp_img = [];
                    foreach($img as &$si){
                        $k["s3_image_url"] = $si;
                        $tmp_img[] = $k;
                    }
                    $images[$listing_id] = $tmp_img;
                }else{
                    $images[$listing_id] = [];
                }

            }
        }
        $response['soldProperty'] = collect($data)->toArray();
        $final_sld_props = [];
        foreach ($response["soldProperty"] as &$soldproperty) {
            $final_sld_props[] = getDom($soldproperty);
        }
        $response["soldProperty"] = $final_sld_props;
        $response['soldPropertyimages'] = collect($images)->toArray();
      } else {
        $response["soldProperty"] = collect($data)->toArray();
    }
    // home stats data
    $finalData = [];
    $c_date = new \DateTime();
    $reqDate = 12;
    $startDate = $c_date->modify("-$reqDate month");
    $startDate = $startDate->format('Y-m-d');
    $starttimeCount = microtime(true);
    $avgSoldPrice = [];
    $soldCount = [];
    $TimePeriod = [];
    // DB::enableQueryLog();
    $queries = StatsData::query();
    $queries->select("AvgPrice", "Count", "TimePeriod", "Date");
    $queries->where("Type", "Sale");
    $queries->where("Date", ">=", $startDate);
    $queries->orderBy('Date', 'ASC');
    $statsData = $queries->get();
    foreach ($statsData as $key => $d) {
        $avgSoldPrice[] = $d->AvgPrice;
        $soldCount[] = $d->Count;
        $TimePeriod[] = $d->TimePeriod;
    }
    $endttimeCount = microtime(true);
    $durationtimecount = $endttimeCount - $starttimeCount;
    $response["state"] = [
        "date" => $TimePeriod,
        "price" => $avgSoldPrice,
        "sold" => $soldCount,
        "durationTimeCount" => $durationtimecount,
        "durationtimeAvg" => $durationtimecount,
        "updateJsonDate" => date('Y-m-d H:i:s')
    ];

    $query = BlogModel::where('AdminId', env('HOUSENAGENTID'));
    $topPost = BlogModel::where('AdminId', env('HOUSENAGENTID'))->orderBy('id', 'asc')->limit(3)->get();
    try {
        foreach ($topPost as $key => $value) {
            $value->MainImg = env('APP_URL') . $value->MainImg;
        }
    } catch (\Throwable $th) {
        //throw $th;
    }
    $response["blog"] = collect($topPost)->toArray();
    $json = json_encode($response);
    file_put_contents(env('HOUSENFRONTJSONPATH') . "websetting.json", $json);
}

function updateAutoSuggestionJson()
{
    $query = "select distinct City from PropertyAddressData";
    $data = DB::select($query);
    $temp_city_array = [];
    $data = collect($data)->pluck("City")->all();
    $num = 1;
    foreach ($data as $datum) {
        $array = [];
        //if ($num == 1) {
        //    $array["isHeading"] = true;
        //}
        $array["text"] = $datum;
        $array["value"] = $datum;
        $array["category"] = "Cities";
        $array["group"] = "City";
        $temp_city_array[] = $array;
        $num++;
    }
    $query2 = "select distinct Community from PropertyAddressData";
    $data2 = DB::select($query2);
    $data2 = collect($data2)->pluck("Community")->all();
    $num2 = 1;
    foreach ($data2 as $datum2) {
        $array2 = [];
        if ($num2 == 1) {
            $array2["isHeading"] = true;
        }
        $array2["text"] = $datum2;
        $array2["value"] = $datum2;
        $array2["category"] = "Neighborhood";
        $array2["group"] = "Community";
        $temp_city_array[] = $array2;
        $num2++;
    }
    $json = json_encode($temp_city_array);
    file_put_contents(env('HOUSENFRONTJSONPATH') . "data.json", $json);
}

function getActualDom($timestamp_sql = null)
{
    date_default_timezone_set("Canada/Central");
    $now = date('Y-m-d');
    /*$timestamp_sql = "2022-09-29";
    $now = time();
    $your_date = strtotime($timestamp_sql);
    $datediff = $now - $your_date;
    $round_value = round($datediff / (60 * 60 * 24));
    dd($timestamp_sql,$round_value,date('Y-m-d'));*/
    /*$now = date('Y-m-d');

            $diff = abs(strtotime($now) - strtotime($timestamp_sql));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));*/

    $datetime1 = new DateTime($timestamp_sql);

    $datetime2 = new DateTime($now);

    $difference = $datetime1->diff($datetime2)->format('%a');

    return $difference;
    //return round($datediff / (60 * 60 * 24));
}


function getDom($item)
{
    if ($item["PropertyStatus"] == "Sale" && $item["Status"] == "A") {
        if ($item["ContractDate"] != "") {
            $item["Dom"] = getActualDom($item["ContractDate"]);
        } else {
            $item["Dom"] = getActualDom($item["Timestamp_sql"]);
        }
    } elseif ($item["Status"] == "U" && $item["Sp_date"] != "0000-00-00") {
        if ($item["Sp_date"] != "") {
            $item["Dom"] = getActualDom($item["Sp_date"]);
        } else {
            $item["Dom"] = getActualDom($item["Timestamp_sql"]);
        }
    } elseif ($item["PropertyStatus"] == "Lease" && $item["Status"] == "A") {
        if ($item["ContractDate"] != "") {
            $item["Dom"] = getActualDom($item["ContractDate"]);
        } else {
            $item["Dom"] = getActualDom($item["Timestamp_sql"]);
        }
    } elseif ($item["Status"] == "D") {
        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
    } else {
        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
    }
    return $item;
}

function get_address($record)
{
    $custom_address = '';
    if ($record["Apt_num"] != "") {
        $custom_address .= isset($record['Apt_num']) ? $record['Apt_num'] . ' - ' : '';
    }
    $custom_address .= isset($record['St_num']) ? $record['St_num'] . ' ' : '';
    $custom_address .= isset($record['St']) ? $record['St'] . ' ' : '';
    $custom_address .= isset($record['St_sfx']) ? $record['St_sfx'] . ' ' : '';
    $custom_address .= isset($record['St_dir']) ? $record['St_dir'] . ' ' : '';
    $custom_address = trim(preg_replace('/\s+/', ' ', $custom_address));
    return $custom_address;
}
