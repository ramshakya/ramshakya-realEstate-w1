<?php

namespace App\Http\Controllers;

use App\Models\RetsPropertyData;
use App\Models\RetsPropertyDataComm;
use App\Models\RetsPropertyDataCondo;
use App\Models\RetsPropertyDataResi;
use Illuminate\Http\Request;

class TestMlsConfig extends Controller
{
    public function __construct()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        //$this->cron_log_model = $cron_log_model;
    }
    public function retsApiTest()
    {
        # code...
        $rets_login_url="http://retsau.torontomls.net:6103/rets-treb3pv/server/login";
        $rets_username  = '';
        $rets_password  = '';
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl($rets_login_url);
        $config->setUsername($rets_username);
        $config->setPassword($rets_password);
        // TREB
        // optional.  value shown below are the defaults used when not overridden
        $config->setRetsVersion('RETS/1.7'); // see constants from \PHRETS\Versions\RETSVersion
        $config->setUserAgent('');
        // $config->setUserAgentPassword($rets_user_agent_password); // string password, if given
        $config->setHttpAuthenticationMethod('basic'); // or 'basic' if required
        $config->setOption('use_post_method', false); // boolean
        $config->setOption('disable_follow_location', false); // boolean
        $rets = new \PHRETS\Session($config);
        $bulletin =  $rets->Login();
        $results = $rets->Search("Property", "ResidentialProperty", $query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        /*$properties = RetsPropertyDataResi::select("Ml_num")->get('Ml_num');

        foreach ($properties as $property){

            echo "\n Property Inserted - ".$property["Ml_num"]."  For Property Table Rets property data residential";
            RetsPropertyData::where("Ml_num",$property["Ml_num"])->update(["ClassName" => "Residential"]);
        }*/

        $properties = RetsPropertyDataCondo::select("Ml_num")->get();
        foreach ($properties as $property){
            echo "\n Property Inserted - ".$property["Ml_num"]."  For Property Table Rets property data condos";
            RetsPropertyData::where("Ml_num",$property["Ml_num"])->update(["ClassName" => "Condos"]);
        }
        $properties = RetsPropertyDataComm::select("Ml_num")->get();
        foreach ($properties as $property){
            echo "\n Property Inserted - ".$property["Ml_num"]."  For Property Table Rets property data Commercial";
            RetsPropertyData::where("Ml_num",$property["Ml_num"])->update(["ClassName" => "Commercial"]);
        }
        echo "\n Done";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
