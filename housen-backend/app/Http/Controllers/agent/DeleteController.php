<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\PreConstruction;
use App\Models\SqlModel\Notifications;

class DeleteController extends Controller
{
    public function delete_data(Request $request)
    {
        $form_data= $request->all();
        $id = $form_data['id'];
        $tableName = $form_data['tableName'];
        if($tableName=='builder')
        {
            $asign_id = $form_data['asign_builder'];
            PreConstruction::where('BuilderId','=', $id)->update(['BuilderId'=>$asign_id]);
        }
        if(isset($form_data['permanent_delete']))
        {
            $permanent_delete = $form_data['permanent_delete'];
            $deleted = DB::table($tableName)->where('id', '=', $id)->delete();
        }
        else
        {
            if($tableName=='User'){
                $deleted = User::where('id',$id)->update(['status_id'=>2]);
            }elseif (isset($tableName) == 'leads') {

                $deleted = DB::table($tableName)->where('id',$id)->delete();

            }else{
                // DB::enableQueryLog();
                $deleted = DB::table($tableName)->where('id',$id)->update(['Status'=>'Deleted']);
                // dd($deleted);
                // dd(DB::getQueryLog());
            }
        }
        
        if($deleted){
            return response()->json([
                    'success' => true,
                    'data' => "Deleted",
                    'message' => "Deleted Successfully",
                ]);
        }
        else
        {
            return response()->json([
                'error' => true,
                'data' => "Not deleted",
                'message' => 'Somethings went wrong',
            ]);
        }
    }
    public function ClearNotification(Request $request)
    {
        $form_data = $request->all();
        $agentId = $form_data['AgentId'];
        $query = Notifications::where('AgentId',$agentId)->update(['StatusId'=>'1']);
        if($query){
            return response()->json([
                    'success' => true,
                    'data' => "Cleared",
                    'message' => "Cleared Successfully",
                ]);
        }
        else
        {
            return response()->json([
                'error' => true,
                'data' => "Not Cleared",
                'message' => 'Somethings went wrong',
            ]);
        }
    }
}
