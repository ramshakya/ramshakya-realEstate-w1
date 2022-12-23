<?php

use App\Constants\PropertyConstants;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\BlogCategory;
use App\Models\SqlModel\Pages;
use App\Models\StatsData;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\Websetting;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\PropertyFeatures;
use Illuminate\Support\Facades\DB;

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
function imagecreatefromfile( $filename ) {
    if (!file_exists($filename)) {
        throw new InvalidArgumentException('File "'.$filename.'" not found.');
    }
    switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
        break;

        case 'png':
            return imagecreatefrompng($filename);
        break;

        default:
            throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
        break;
    }
}
function compress_Image($image='',$dir='')
{
    $url = "";
    $file = $image;
    if (preg_match('/^data:image\/(\w+);base64,/', $file)) {
        $data = substr($file, strpos($file, ',') + 1);
        $pos = strpos($file, ';');
        $type = explode(':', substr($file, 0, $pos))[1];
        $name = uniqid();
        $path = storage_path('app/public/img/'.$dir."_webp/");
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }
        $url = Storage::disk('public')->put('img/' . $name . '.' . Str::afterLast($type, '/'), base64_decode($data));
         $im = imagecreatefromfile(storage_path('app/public/img/img/').$name . '.' . Str::afterLast($type, '/'));

         $web =imagewebp($im, storage_path("app/public/img/".$dir."_webp/" . $name  . ".webp"), "80");
         $url = Storage::disk('public')->url($dir."_webp/" . $name  . ".webp");
    }else{
        if (isset($file)) {
            $data = substr($file, strpos($file, ',') + 1);
                $pos = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $name = uniqid();
            $path = storage_path('app/public/img/'.$dir."_webp/");
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
            $url = Storage::disk('public')->put('img/' . $name . '.' . Str::afterLast($type, '/'), base64_decode($data));
            $im = imagecreatefromfile(storage_path('app/public/img/img/').$name . '.' . Str::afterLast($type, '/'));

            $web =imagewebp($im, storage_path("app/public/img/".$dir."_webp/" . $name  . ".webp"), "80");
            $url = Storage::disk('public')->url($dir."_webp/" . $name  . ".webp");
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
        "ToEmail" => is_array($to)?implode(", ", $to):$to,
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
            $err =  \Illuminate\Support\Facades\Mail::mailer('smtp')->to($to)->cc($cc)->bcc($bcc)->send(new \App\Mail\SendMailNew($sendData));
            $data["IsSent"] = 1;
            $data["HashId"] = $hashData;
            $email_logs->insertAndGetId($data);
            return true;
        } catch (Exception $exception) {
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
    if ($instance_flag == 1) {
        $select_alert = \App\Models\SqlModel\SavedSearchFilter::where("enabled", 1)->where("subscribe",1)->where("frequency", "=", "instantly")->get();
    } else {
        $select_alert = \App\Models\SqlModel\SavedSearchFilter::where("enabled", 1)->where("subscribe",1)->where("frequency", "!=", "instantly")->get();
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
                if ($alert_freq == 'daily' && $diff > 86400) {
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
                    $filteredData = get_search_result_common($search_fields, 0, 10, "ListPrice", "ASC", $search_fields["text_search"], "", $insert_time_query);
                } else {
                    $filteredData = get_search_result_common($search_fields, 0, 10, "ListPrice", "ASC", "", "", $insert_time_query);
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
                        $userInsert = insertemailhash($emailHash,$alert_id);
                        $email_template_data["emailHash"] = $emailHash;
                    } catch (Exception $exception) {}
                    //this is for Map url
                    $email_template_data['sort_by']='map?sort_by=price_low';
                    $email_template_data['text_search'] =isset($alert['textSearch']) && !empty($alert['textSearch']) ?"&text_search=".$alert['textSearch']:"";
                    $email_template_data['propertyType']=isset($alert['className']) && !empty($alert['className']) ?"&propertyType=".$alert['className']:"";
                    $email_template_data['basement']=isset($alert['Bsmt1Out']) && !empty($alert['Bsmt1Out']) && count($alert['Bsmt1Out'])?'&basement=["'.implode('","',$alert['Bsmt1Out']).'"]':"";
                    $email_template_data['price_min']=isset($alert['priceMin']) && !empty($alert['priceMin'])?"&price_min=".$alert['priceMin']:"";
                    $email_template_data['price_max']=isset($alert['priceMax']) && !empty($alert['priceMax']) ?"&price_max=".$alert['priceMax']:"";
                    $email_template_data['beds']=isset($alert['bedsTotal']) && !empty($alert['bedsTotal']) ?"&beds=".$alert['bedsTotal']:"";
                    $email_template_data['Dom']=isset($alert['dom']) && !empty($alert['Dom']) ?"&Dom=".$alert['dom']:"";
                    $email_template_data['features']=isset($alert['features']) && !empty($alert['features']) && count($alert['features'])?'&features=["'.implode('","',$alert['features']).'"]':"";
                    $email_template_data['propertySubType'] =isset($alert['propertySubType']) && !empty($alert['propertySubType']) && count($alert['propertySubType'])?'&propertySubType=["'.implode('","',$alert['propertySubType']).'"]':"";
                    $email_template_data['shape']=isset($alert['shape']) && !empty($alert['shape']) ?"&shape=".$alert['shape']:"";
                    $email_template_data['status']=isset($alert['status']) && !empty($alert['status']) ?"&status=".$alert['status']:"";
                    $email_template_data['sqft']=isset($alert['Sqft']) && !empty($alert['Sqft'])?"&sqft=".$alert['Sqft']:"";
                    $email_template_data['curr_bounds']=isset($alert['currBounds']) && !empty($alert['currBounds']) ?"&curr_bounds=".$alert['currBounds']:"";
                    $email_template_data['baths']=isset($alert['bathsFull']) && !empty($alert['bathsFull']) ?"&baths=".$alert['bathsFull']:"";

                    $txt_email = view('emails.multipleListingsEmail', $email_template_data)->render();
                    $curr_date = date('Y-m-d');
                    $subject = "New Listing Notification - " . $alert_name . "";
                    $alert_log['alertId'] = $alert_id;
                    if ($alert['emailAlert'] == 1 && $filteredData["result"] != []) {
                        $alert_log['toEmail'] = $user_email;
                        $alert_log['emailContent'] = $txt_email;
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
    $query->where("Status", "=", "A");
    //$query->whereNotNull('ListPrice');
    if ($inserted_time_query !== "") {
        $query->where("inserted_time", ">", $inserted_time_query);
    }
    $query->orderBy('inserted_time', 'DESC');
    $total = $query->count();
    //$total = 22000;
    $query->skip($offset);
    $query->take($limit);
    $result = $query->get();
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

function  getMedianbck($numbers = array())
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
    $query =  LeadsModel::where('Seen', '1')->where('AssignedAgent', auth()->user()->AdminId)->orderBy('id', 'Desc');
    // $query =  LeadsModel::where('AssignedAgent',auth()->user()->AdminId)->orderBy('id','Desc');
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

//By siddharth for increamental Hash for unsubscribe email
function incrementalHash()
{
    $hash = substr(md5(microtime()),rand(0,26),15);
    return $hash;
}
function insertemailhash($hash = NULL, $user_id=NULL)
{
    if (isset($hash) && isset($user_id)) {
        $users = \App\Models\SqlModel\SavedSearchFilter::select("emailHash","id")->where("id", $user_id)->first();
        if (isset($users) && !empty($users)) {
            $users->emailHash = $hash;
            $users->save();
        }

    }
}
function updateHomePageJson() {
    $websetting = Websetting::select('WebsiteName', 'WebsiteTitle', 'UploadLogo', 'LogoAltTag', 'Favicon', 'WebsiteEmail', 'PhoneNo', 'WebsiteAddress', 'FacebookUrl', 'TwitterUrl', 'LinkedinUrl', 'InstagramUrl', 'YoutubeUrl', 'WebsiteColor', 'WebsiteMapColor', 'GoogleMapApiKey', 'HoodQApiKey', 'WalkScoreApiKey', 'FavIconAltTag', 'ScriptTag', 'TopBanner', 'FbAppId', 'GoogleClientId','OfficeName')
        ->where("AdminId", env('WEDUAGENTID'))
        ->first();
    $seo = Pages::select('MetaTitle', 'MetaDescription', 'MetaTags', 'Setting')->where('PageName', "home")
        ->where("AgentId", env('WEDUAGENTID'))
        ->first();
    $response['websetting'] = collect($websetting)->all();
    if ($seo && $seo->Setting != '') {
        $response['pageSetting'] = json_decode($seo->Setting,true);
        $arrangeSection =  json_decode($response['pageSetting']["ArrangeSection"]);
        $arrangeSections = [];
        foreach ($arrangeSection[0] as $key => $value) {
            $arrangeSections[] =  $value->value;
        }
        unset($response['pageSetting']->ArrangeSection);
        $response['arrangeSections'] = collect($arrangeSections)->all();
    }
    unset($seo['Setting']);
    $response['seo'] = collect($seo)->all();
    $response = collect($response)->all();
    // now for recentListing
    $featuredListings = [];
    $recentListings = [];
    $propertyList = [];
    $tmp_data = [];
    $agentId = env('WEDUAGENTID');
    $ids = array();
    $recentListings = RetsPropertyData::select(PropertyConstants::HOME_SELECT_DATA)->whereNotNull('ImageUrl')->where("PropertyStatus", "Sale")->limit(20)->orderBy('ContractDate', 'desc')->get();
    $durationtimeRecentListing = "";
    $starttimecount = microtime(true);
    $resiCountQuery = "SELECT count(*) as count from RetsPropertyData where  PropertyType = 'Residential' and Status = 'A'";
    $condosCountQuery = "SELECT count(*) as count from RetsPropertyData where  PropertyType = 'Condos' and Status = 'A'";
    $soldCountQuery = "SELECT count(ListingId) as count from RetsPropertyDataPurged where  LastStatus = 'Sld'";
    $resiCount =  DB::selectOne($resiCountQuery);
    $condosCount =  DB::selectOne($condosCountQuery);
    $soldCount =  DB::selectOne($soldCountQuery);
    $resiCount =  $resiCount->count;
    $condosCount =  $condosCount->count;
    $soldCount =  $soldCount->count;
    $endtimetimecount  = microtime(true);
    $durationtimecount = $endtimetimecount - $starttimecount;
    $response["recentListing"] = collect($recentListings)->toArray();
    $final_props = [];
    foreach ($response["recentListing"] as &$property) {
        $final_props[] = getDom($property);
    }
    $response["recentListing"] = $final_props;
    $response["resiCount"] = $resiCount;
    $response["condosCount"] = $condosCount;
    $response["soldCount"] = $soldCount;
    $response["countTime"] = $durationtimecount;
    $response["recentListingTime"] = $durationtimeRecentListing;
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
        "durationtimeAvg" => $durationtimecount
    ];
    $final_props = [];
    foreach ($response["recentListing"] as &$property) {
        $final_props[] = getDom($property);
    }
    $response["recentListing"] = $final_props;
     /*$response["recentListing"] = collect($response["recentListing"])->map(function ($item) {
        $item["Dom"] = getActualDom($item["Timestamp_sql"]);
        return $item;
    })->all();*/
    $response["last_update_time"] = date("Y-m-d H:i:s");
    $json = json_encode($response);
    file_put_contents(env('WEDUFRONTJSONPATH')."websetting.json",$json);
}

function getDom($item) {
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


function makeLabel($item) {
    if ($item->Lsc == "Exp"){
        $item->label = "Expired on ".date('M d, Y', strtotime($item->Unavail_dt));
        $item->Sp_dol = $item->Lp_dol;
        $item->LastStatusButton = "Expired";
    } elseif ($item->Lsc == "Lsd") {
        $item->label = "Rented on ".date('M d, Y', strtotime($item->Cd));
        $item->LastStatusButton = "Rented";
    } elseif ($item->Lsc == "Ter") {
        $item->label = "Terminated on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->Sp_dol = $item->Lp_dol;
        $item->LastStatusButton = "Terminated";
    } elseif ($item->Lsc == "Sus") {
        $item->label = "Suspended on ".date('M d, Y', strtotime($item->Unavail_dt));
        $item->Sp_dol = $item->Lp_dol;
        $item->LastStatusButton = "Suspended";
    } elseif ($item->Lsc == "Sld") {
        $item->label = "Sold on ".date('M d, Y', strtotime($item->Cd));
        $item->LastStatusButton = "Sold";
    } elseif ($item->Lsc == "Dft") {
        $item->label = "Drafted on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Drafted";
        $item->Sp_dol = $item->Lp_dol;
    } elseif ($item->Lsc == "Lc") {
        $item->label = "Lc on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Lc";
    } elseif ($item->Lsc == "Pc") {
        $item->label = "Pc on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Pc";
        $item->Sp_dol = $item->Lp_dol;
    } elseif ($item->Lsc == "Ext") {
        $item->label = "Extended on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Extended";
        $item->Sp_dol = $item->Lp_dol;
    } elseif ($item->Lsc == "New") {
        $item->label = "Sold on ".date('M d, Y', strtotime($item->Cd));
        $item->LastStatusButton = "Sold";
    } elseif ($item->Lsc == "Sce") {
        $item->label = "Sce on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Sce";
        $item->Sp_dol = $item->Lp_dol;
    } else {
        $item->label = "Sold on ".date('M d, Y', strtotime($item->Timestamp_sql));
        $item->LastStatusButton = "Sold";
        $item->Sp_dol = $item->Lp_dol;
    }
    return $item;
}





function updateAutoSuggestionJson() {
    $query = "select distinct City from PropertyAddressData";
    $data = DB::select($query);
    $temp_city_array = [];
    $data = collect($data)->pluck("City")->all();
    $num = 1;
    foreach ($data as $datum){
        $array = [];
        if ($num==1){
            $array["isHeading"] = true;
        }
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
    foreach ($data2 as $datum2){
        $array2 = [];
        if ($num2==1){
            $array2["isHeading"] = true;
        }
        $array2["text"] = $datum2;
        $array2["value"] = $datum2;
        $array2["category"] = "Neighborhood";
        $array2["group"] = "Community";
        $temp_city_array[] = $array2;
        $num2++;
    }
    //$temp_city_array["last_update_time"] = date("Y-m-d H:i:s");
    //dd($temp_city_array);
    $json = json_encode($temp_city_array,true);
    file_put_contents(env('WEDUFRONTJSONPATH')."data.json",$json);
}



function getActualDom ($timestamp_sql=null){
    //$now = time();
    //$your_date = strtotime($timestamp_sql);
    //$datediff = $now - $your_date;
    //return round($datediff / (60 * 60 * 24));
    date_default_timezone_set("Canada/Central");
    $now = date('Y-m-d');
    $datetime1 = new DateTime($timestamp_sql);
    $datetime2 = new DateTime($now);
    $difference = $datetime1->diff($datetime2)->format('%a');
    return $difference;
}

//For function Traces
function generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result);
}

function get_address($record) {
    $custom_address = '';
    if ($record["Apt_num"] != "") {
        $custom_address .= isset($record['Apt_num']) ? $record['Apt_num'] . ' - ' : '';
    }
    $custom_address .= isset($record['St_num']) ? $record['St_num'] . ' ' : '';
    $custom_address .= isset($record['St']) ? $record['St'] . ' ' : '';
    $custom_address .= isset($record['St_sfx']) ? $record['St_sfx'] . ' ' : '';
    $custom_address .= isset($record['St_dir']) ? $record['St_dir'] . ' ' : '';
    $custom_address = trim(preg_replace('/\s+/',' ', $custom_address));
    return $custom_address;
}
