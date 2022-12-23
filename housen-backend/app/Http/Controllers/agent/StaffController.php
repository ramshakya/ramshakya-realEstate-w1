<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use App\Models\SqlModel\Country;
use App\Models\SqlModel\RefMasterData;
use App\Models\User;
use App\Models\SqlModel\Staff;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    //    Staff
    public function staff()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Staff";
        if(auth()->user()->person_id == 1) {
            $data["agents"] = User::where("person_id",self::STAFF_PERSON_ID)->where('status_id',1)->get();
            $data['usertype']='superAdmin';
        }else{
            $data["agents"] = User::where("person_id",self::STAFF_PERSON_ID)->where('AdminId',auth()->user()->id)->where('status_id',1)->get();
            $data['usertype']='agent';
        }

        return view('superAdmin.staff.view',$data);
    }
    public function createStaff($id=null)
    {
        //
        $data["genders"] = RefMasterData::where("type_id",self::TYPE_ID_FOR_GENDER)->get();
        $data["agents"] = User::where("person_id",self::AGENT_PERSON_ID)->get();
        if($id!=null){
            $data['staff']=User::where("person_id",self::STAFF_PERSON_ID)->where('id',$id)->first();
            if($data['staff']){
                $img_id = $data['staff']->id;
            
                $data["ImageUrl"] = Staff::select('ImageUrl')->where('UserId',$img_id)->first();
            }
            
        }
        $data["countries"] = Country::all();
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Add Staff";
        if(auth()->user()->person_id == 1) {
            $data['usertype']='superAdmin';
            $data['userurl']='super-admin';
        }else{
            $data['usertype']='agent';
            $data['userurl']='agent';
        }
        return view('superAdmin.staff.add',$data);
    }
    public function store(Request $request)
    {

        $response_data = [];
        $data = $request->all();
        if (empty($data['id'])) {
            $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "string",
            "email" => "required|email|unique:users,email",
            "phone_number" => "required",
            "date_of_birth" => "required",
            "social_mobile" => "string",
            "gender_id" => "required|exists:RefMasterData,id",
            "country_id" => "required|exists:Countries,id",
            "status_id" => "exists:RefStatuses,id",
        ]);
        }else{
            $validator = Validator::make($request->all(), [
                "first_name" => "required",
                "last_name" => "string",
                "email" => "required|email",
                "phone_number" => "required",
                "date_of_birth" => "required",
                "social_mobile" => "string",
                "gender_id" => "required|exists:RefMasterData,id",
                "country_id" => "required|exists:Countries,id",
                "status_id" => "exists:RefStatuses,id",
            ]);
         }
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            $response["status"] = self::SUCCESS_HTTP_RESPONSE_STATUS;
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        $id=0;
        if (isset($data['id']) && !empty(isset($data['id']))) {
            $id=$data['id'];
            unset($data['id']);
        }else{
            $validator = Validator::make($request->all(), [
            "password" => "required|string|min:8",
            ]);
            if ($validator->fails()) {
                $response["errors"] = $validator->errors();
                $response["status"] = self::SUCCESS_HTTP_RESPONSE_STATUS;
                return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
            }
        }
        
//        if ($validator->passes()) {
//            try {
                   

                    $res['name'] = $data['first_name'].' '.$data["last_name"];
                    $res['first_name'] = $data['first_name'];
                    $res['last_name'] = $data["last_name"];
                    $res['email'] = $data['email'];

                   if(isset($data['password']) && !empty($data['password'])){
                       $res['password'] = Hash::make($data['password']);
                   }
                    $res['phone_number'] = $data["phone_number"];
                    if(isset($data['AdminId']) && !empty($data['AdminId'])) {
                        $res['AdminId'] = $data["AdminId"];
                    }
                    if(isset($data['person_id']) && !empty($data['person_id'])) {
                        $res['person_id'] = $data["person_id"];
                    }
                    $res['gender_id'] = $data["gender_id"];
                    $res['country_id'] = $data["country_id"];
                    $res['alt_address'] = $data["alt_address"];
                    $res['date_of_birth'] = $data["date_of_birth"];
                    $res['type'] = 3;
                    
                $response_data = User::updateOrCreate(['id' => $id], $res);
                if($id!=0)
                {
                    $ids = User::select('id')->where('id',$id)->first();
                    $imageId =  $ids->id;
                }
                else
                {
                    $em = $res['email'];
                    $ids = User::select('id')->where('email',$em)->orderBy('id','desc')->first();
                    $imageId =  $ids->id;
                }
                
                // return $ids['id'];
                if(isset($data['photo']) && !empty($data['photo']))
                    {
                       // unset($data['photo']);
                       $image['ImageUrl']=saveImage($data["photo"]);
                       Staff::updateOrCreate(['UserId' => $imageId], $image);
                    }
                    
//            }catch (QueryException $exception) {
//                return response(['errors' => $exception->errorInfo]);
//            }
//
//        }

        return response([
            'success' => "Staff Successfully Added",
            'data' => $response_data,
            'res'=>$res,
            'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
        ], self::SUCCESS_HTTP_RESPONSE_STATUS);

    }
}
