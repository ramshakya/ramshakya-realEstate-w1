<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
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

}
