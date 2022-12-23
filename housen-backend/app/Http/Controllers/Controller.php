<?php

namespace App\Http\Controllers;

use App\Models\ApiRequestLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        date_default_timezone_set(env('TIME_ZONE'));
    }
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const SUCCESS_HTTP_RESPONSE_STATUS = 200;
    const VALIDATION_ERROR_HTTP_RESPONSE_STATUS = 422;
    const DB_ERROR_HTTP_RESPONSE_STATUS = 502;
    const NO_DATA_HTTP_RESPONSE_STATUS = 204;
    const UPDATE_DATA_HTTP_RESPONSE_STATUS = 201;
    const TYPE_ID_FOR_GENDER = 1;
    const AGENT_PERSON_ID = 2;
    const STAFF_PERSON_ID = 3;
    const DEVNEWENVIRONMENT = "From New Environment ";

    public function apiTrackLog(Request $request)
    {
        $ip = getUserIP();
        $data = $this->logRequest($request);
        $domain = env('DOMAIN');
        $payload = array(
            "url"=>$domain.$data['route'],
            "params"=>json_encode($data["params"]),
            "callFrom"=>"",// how can we get
            "domain"=>$data['host'],
            "ip"=>$ip,
            "status"=>"success",
        );
        if ($request->is('api/*')) {
            //write your logic for api call
            $host = substr($data['host'], (strpos($data['host'], ':') ?: -1) + 1);
            $host = str_replace(":$host", "", $data['host']);
            if (($host == $domain)) {
                ApiRequestLog::create($payload);
                return true;
            } else {
                $payload["status"]='failed';
                ApiRequestLog::create($payload);
                exit();
                return false;
            }
        } else {
            //write your logic for web call
        }
    }
    private function logRequest(Request $request)
    {
        return $data = [
            'host' => $request->server('HTTP_HOST'),
            'route' => $request->server('REQUEST_URI'),
            'method' => $request->server('REQUEST_METHOD'),
            'user_agent' => $request->header('user-agent'),
            'params' => $request->all(),
            'duration' => $request->server('REQUEST_TIME')
        ];
    }
}
