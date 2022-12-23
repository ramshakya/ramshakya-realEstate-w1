<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use App\Models\PropertiesCronLog;
use App\Models\RetsPropertyData;


use App\Models\SqlModel\lead\LeadsModel;
use App\Models\UserTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainSuperAdminController extends Controller
{
    //
    public $LeadsModel;


    public $PropertyData;
    public function __construct() {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql"){
            $this->LeadsModel = new LeadsModel();


            $this->PropertyData = new RetsPropertyData();
        }else{

        }
    }
    public function index() {
        if (!is_null(Auth::user())){
            return redirect('super-admin/dashboard');
        }else{
            return view('superAdmin.login');
        }
    }

    public function dashboard() {

        $data['mls']=collect(config('mls_config.mls'))->all();
        $data['mls_total']=count($data['mls']);
        $data['listing']=$this->PropertyData::distinct('id')->count();
        $data['agentsprofile']=0;
        $data['activeagentsprofile']=[];
        $data['leads']=$this->LeadsModel::count();
        foreach($data['mls'] as &$mls){
            $mls['agentsprofile']=[];
            $mls['activeagentsprofile']=[];
            $mls['listing']=$this->PropertyData::distinct('id')->count();

        }
        $mlsName=[];
        $data['mlsName']=$mlsName;
        $data["pageTitle"] = "Super Admin Dashboard";
        return view('superAdmin.dashboard',$data);
    }
    public function getlistingGraphData(Request $request){
        $type=$request['type'];
        $days=$request['days'];
        // return $request;
        $data['mls']=collect(config('mls_config.mls'))->all();
        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        $date = \Carbon\Carbon::today()->subDays($days);

        $data['chart_day'] = PropertiesCronLog::selectRaw('count(id) as total, Date('.$type.') as udate,mls_no')->where($type,'>=',$date)->groupBy('mls_no')->groupBy('udate')->orderBy('udate','ASC')->get();
        $final=[];
        $date=[];
        foreach($data['chart_day'] as $val)
        {
            $dir=array('day'=>'', 'mls1'=>0, 'mls2'=>0 , 'mls3'=>0,'mls4'=>0);
            if(in_array($val->udate,$date)){
                $arr=array_search($val->udate,$date);
                if($val->mls_no==1){
                    $dir['day']=$val->udate;
                    $dir['mls1']=$val->total;
                    $final[$arr]['mls1']=$val->total;
                }
                if($val->mls_no==2){
                    $dir['day']=$val->udate;
                    $dir['mls2']=$val->total;
                    $final[$arr]['mls2']=$val->total;
                }
                if($val->mls_no==3){
                    $dir['day']=$val->udate;
                    $dir['mls3']=$val->total;
                    $final[$arr]['mls3']=$val->total;
                }
                if($val->mls_no==4){
                    $dir['day']=$val->udate;
                    $dir['mls4']=$val->total;
                    $final[$arr]['mls4']=$val->total;
                }
            }else{
                if($val->mls_no==1){
                    $dir['day']=$val->udate;
                    $dir['mls1']=$val->total;
                }
                if($val->mls_no==2){
                    $dir['day']=$val->udate;
                    $dir['mls2']=$val->total;
                }
                if($val->mls_no==3){
                    $dir['day']=$val->udate;
                    $dir['mls3']=$val->total;
                }
                if($val->mls_no==4){
                    $dir['day']=$val->udate;
                    $dir['mls4']=$val->total;
                }
                $date[]=$val->udate;
                $final[]=$dir;
            }
        }
        $data['final']=json_encode($final);

        $mlsName=[];
        foreach($data['mls'] as $mls){
            $mlsName[]=$mls['mls'];
        }
        $data['mls']=$mlsName;
        return $data;
    }
    public function getPergeGraphData(Request $request){
        $type=$request['type'];
        $days=$request['days'];
        // return $request;
        $data['mls']=collect(config('mls_config.mls'))->all();
        $data['date'] = \Carbon\Carbon::today()->subDays($days);
        $date = \Carbon\Carbon::today()->subDays($days);

        $data['chart_day'] = PropertiesCronLog::selectRaw('count(id) as total, Date('.$type.') as udate,mls_no')->where($type,'>=',$date)->groupBy('mls_no')->groupBy('udate')->orderBy('udate','ASC')->get();
        $final=[];
        $date=[];
        foreach($data['chart_day'] as $val)
        {
            $dir=array('day'=>'', 'mls1'=>0, 'mls2'=>0 , 'mls3'=>0,'mls4'=>0);
            if(in_array($val->udate,$date)){
                $arr=array_search($val->udate,$date);
                if($val->mls_no==1){
                    $dir['day']=$val->udate;
                    $dir['mls1']=$val->total;
                    $final[$arr]['mls1']=$val->total;
                }
                if($val->mls_no==2){
                    $dir['day']=$val->udate;
                    $dir['mls2']=$val->total;
                    $final[$arr]['mls2']=$val->total;
                }
                if($val->mls_no==3){
                    $dir['day']=$val->udate;
                    $dir['mls3']=$val->total;
                    $final[$arr]['mls3']=$val->total;
                }
                if($val->mls_no==4){
                    $dir['day']=$val->udate;
                    $dir['mls4']=$val->total;
                    $final[$arr]['mls4']=$val->total;
                }
            }else{
                if($val->mls_no==1){
                    $dir['day']=$val->udate;
                    $dir['mls1']=$val->total;
                }
                if($val->mls_no==2){
                    $dir['day']=$val->udate;
                    $dir['mls2']=$val->total;
                }
                if($val->mls_no==3){
                    $dir['day']=$val->udate;
                    $dir['mls3']=$val->total;
                }
                if($val->mls_no==4){
                    $dir['day']=$val->udate;
                    $dir['mls4']=$val->total;
                }
                $date[]=$val->udate;
                $final[]=$dir;
            }
        }
        $data['final']=json_encode($final);

        $mlsName=[];
        foreach($data['mls'] as $mls){
            $mlsName[]=$mls['mls'];
        }
        $data['mls']=$mlsName;
        return $data;
    }

    public function trackUsers(Request $request)
    {
        $query=UserTracker::query();
        $query->orderByDesc('id');
        $query->limit(1);
        $query->groupBy('UserId');
        // $query->where("AgentId",Auth::user()->id);
        dd($query->get());
        return view("agent.trackusers.index");
    }
}
