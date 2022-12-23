<?php

namespace App\Http\Controllers\superAdmin;

use App\Http\Controllers\Controller;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\Country;
use App\Models\SqlModel\RefMasterData;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index()
    {
        $data["agents"] = User::where("person_id",self::AGENT_PERSON_ID)->get();
        $data["pageTitle"] = "Super Admin | Add Agent";
        return view('superAdmin.agents.view',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return
     */
    public function create()
    {
        //

        $data["genders"] = RefMasterData::where("type_id",self::TYPE_ID_FOR_GENDER)->get();
        $data["countries"] = Country::all();
        $data["pageTitle"] = "Super Admin | Add Agent";
        return view('superAdmin.agents.add',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response_data = [];
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "string",
            "email" => 'required|email|unique:users,email',
            "phone_number" => "required|integer",
            "password" => "required|string|min:8",
            "date_of_birth" => "required",
            "social_mobile" => "string",
            "gender_id" => "required|exists:RefMasterData,id",
            "country_id" => "required|exists:Countries,id",
            "status_id" => "exists:RefStatuses,id",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            $response["status"] = self::SUCCESS_HTTP_RESPONSE_STATUS;
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                $response_data = User::create([
                    'name' => $data['first_name'].''.$data["last_name"],
                    'first_name' => $data['first_name'],
                    'last_name' => $data["last_name"],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone_number' => (int)$data["phone_number"],
                    'person_id' => $data["person_id"],
                    'gender_id' => $data["gender_id"],
                    'country_id' => $data["country_id"],
                    'alt_address' => $data["alt_address"],
                    'social_mobile' => $data["social_mobile"],
                    'type' => 2
                ]);
            }catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }

        }
        return response([
            'success' => "Agent Successfully Added",
            'data' => $response_data,
            'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
        ], self::SUCCESS_HTTP_RESPONSE_STATUS);

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

    public function getProperty()
    {
        $data["pageTitle"] = "Property Details";
        $data['cities'] = RetsPropertyData::distinct('Area')->get('Area');
        $data['heating'] = RetsPropertyData::distinct('Heating')->get('Heating');
        // $data['data'] = RetsPropertyData::paginate(8);
        return view('superAdmin.property.propertyData', $data);
    }
}
