<?php

namespace App\Http\Controllers;

use App\Models\RetsPropertyData;


use App\Models\SqlModel\lead\LeadsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct() {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql"){
            $this->LeadsModel = new LeadsModel();


            $this->PropertyData = new RetsPropertyData();
        }else{
//            $this->LeadsModel = new \App\Models\MongoModel\LeadsModel();

//            $this->AssignmentModel = new \App\Models\MongoModel\BrokerAgents();
//            $this->PropertyData = new \App\Models\MongoModel\RetsPropertyData();
        }
    }

    public function index()
    {

        if (!is_null(Auth::user())) {
            return redirect('agent/dashboard');
        } else {
            return view('agent.login');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {

        $data["pageTitle"] = "Agent Dashboard";
        $data['mls']=collect(config('mls_config.mls'))->all();
        $data['mls_total']=count($data['mls']);
        $data['boards']=0;//AssignmentModel::distinct('ListAOR')->count();
        $data['offices']=0;//RetsPropertyData::distinct('Rltr')->count();
        $data['agents']=0;//AssignmentModel::distinct('ListAgentMlsId')->count();
        $data['listing']=$this->PropertyData::distinct('id')->count();
//        return $data['listing'];
        $data['agentsprofile']=[];
        $data['activeagentsprofile']=0;
        $data['leads']=$this->LeadsModel::count();

        foreach($data['mls'] as &$mls){
            $mls['boards']=0;//AssignmentModel::where('mls_no',$mls["id"])->distinct('ListAOR')->count();
            $mls['offices']=RetsPropertyData::where('mls_no',''.$mls['id'].'')->distinct('Rltr')->count();
            $mls['agents']=0;//AssignmentModel::where('mls_no',''.$mls['id'].'')->distinct('ListAgentMlsId')->count();
            $mls['agentsprofile']=[];
            // dd($mls['agentsprofile']);
            $mls['activeagentsprofile']=[];
            $mls['listing']=$this->PropertyData::distinct('id')->count();
        }
        // dd(collect($data['mls'])->all());
        // return $data['mls'];
        $mlsName=[];
        // foreach($data['mls'] as $mls){
        //      // $mlsName[]=$mls['mls'];
        // }
        // return $mlsName;
        $data['mlsName']=$mlsName;

        // dd(collect($data['mlsName'])->all());
        return view('agent.dashboard',$data);
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
