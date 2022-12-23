<?php

use App\lib\phrets;

function mls_login($login_parameter)
{
    $rets = new phrets;
    $login_url = $login_parameter['rets_login_url'];
    $mls_username = $login_parameter['rets_username'];
    $mls_password = $login_parameter['rets_password'];
    $rets_version = $login_parameter['RETS-Version'];
    $user_agent = $login_parameter['User-Agent'];
    $rets->AddHeader("Accept", "*/*");
    $rets->AddHeader("RETS-Version", $rets_version);
    $rets->AddHeader("User-Agent",  $user_agent);
    $rets->SetParam('compression_enabled', true);
    // $rets->SetParam('debug_mode', true);
    $rets->SetParam("offset_support", true);
    $rets->SetParam("compression_enabled", true);
    // make first connection
    // $connect =1;
    $connect = $rets->Connect($login_url, $mls_username, $mls_password);
    if ($connect) {
        echo "<h3 style='color:green'>Connected.....</h3>";
    }
    if (!$connect) {
        $error_details = $rets->Error();
        $error_text    = strip_tags($error_details['text']);
        $error_type    = strtoupper($error_details['type']);
        echo "<center><span style='color:red;font-weight:bold;'>{$error_type} ({$error_details['code']}) {$error_text}</span></center>";
        exit;
    }
    return $rets;
}
