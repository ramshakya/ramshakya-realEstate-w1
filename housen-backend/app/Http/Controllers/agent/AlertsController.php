<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    //
    public function index() {
        sendAlerts([],2,"");
    }
    
    public function daily() {
        $todayDate = new \DateTime(); // For today/now, don't pass an arg.
        $todayDate->sub(new \DateInterval('PT3H55M10S'));
        $desiredDate = $todayDate->format("Y-m-d H:i:s");
        sendAlerts([],1,$desiredDate);
    }
}
