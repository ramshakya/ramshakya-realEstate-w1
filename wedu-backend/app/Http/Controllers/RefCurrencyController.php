<?php

namespace App\Http\Controllers;

use App\Models\RefCurrency;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefCurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $response_data = [];
        $response_data = RefCurrency::all();
        return response([
            'success' => "Get All Currencies",
            'data' => $response_data,
            'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
        ], self::SUCCESS_HTTP_RESPONSE_STATUS);
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
        $response_data = [];
        // this is the validation method
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => "string",
            "icon_as_css" => "string",
            "status_id" => "exists:RefStatuses,id",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                $response_data = RefCurrency::create($request->all());
            } catch (QueryException $exception) {
                return response(['errors' => $exception->errorInfo]);
            }
        }
        return response([
            'success' => "Currency Successfully Added",
            'data' => $response_data,
            'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
        ], self::SUCCESS_HTTP_RESPONSE_STATUS);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RefCurrency  $refCurrency
     * @return \Illuminate\Http\Response
     */
    public function show($id=null)
    {
        $response_data = [];
        if (is_null($id)) {
            return response([
                'error' => "Id is required",
                'data' => $response_data,
                'status' => self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS
            ], self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        } else {
            $response_data = RefCurrency::where("id", $id)->get();
            if (!empty($response_data->all())) {
                return response([
                    'success' => "Success",
                    'data' => $response_data,
                    'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
                ], self::SUCCESS_HTTP_RESPONSE_STATUS);
            } else {
                return response([
                    'success' => "We are not able to find data with this id,please provide valid id",
                    'data' => $response_data,
                    'status' => self::NO_DATA_HTTP_RESPONSE_STATUS
                ], self::SUCCESS_HTTP_RESPONSE_STATUS);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RefCurrency  $refCurrency
     * @return \Illuminate\Http\Response
     */
    public function edit(RefCurrency $refCurrency)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RefCurrency  $refCurrency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RefCurrency $refCurrency)
    {
        //
        $response_data = [];
        // this is the validation method
        $validator = Validator::make($request->all(), [
            "id" => "required|exists:RefStatuses,id",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            try {
                $response_data = RefCurrency::where("id", $request->id)->update($request->all());
                if ($response_data) {
                    $response_data = RefCurrency::where("id", $request->id)->get();
                }
            } catch (QueryException $exception) {
                return response(
                    [
                        'errors' => "Something Went Wrong",
                        'data' => $exception->errorInfo,
                        'status' => self::DB_ERROR_HTTP_RESPONSE_STATUS
                    ],
                    self::SUCCESS_HTTP_RESPONSE_STATUS
                );
            }
        }
        return response([
            'success' => "Currency Successfully Updated",
            'data' => $response_data,
            'status' => self::UPDATE_DATA_HTTP_RESPONSE_STATUS
        ], self::SUCCESS_HTTP_RESPONSE_STATUS);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RefCurrency  $refCurrency
     * @return \Illuminate\Http\Response
     */
    public function destroy($id=null)
    {
        //
        $response_data = [];
        if (is_null($id)) {
            return response([
                'error' => "Id is required",
                'data' => $response_data,
                'status' => self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS
            ], self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        } else {
            $ref_status_value = RefCurrency::where("id", $id)->get();
            if (!empty($ref_status_value->all())) {
                RefCurrency::destroy($id);
                return response([
                    'success' => "Success",
                    'data' => $response_data,
                    'status' => self::SUCCESS_HTTP_RESPONSE_STATUS
                ], self::SUCCESS_HTTP_RESPONSE_STATUS);
            } else {
                return response([
                    'success' => "We are not able to find data with this id,please provide valid id",
                    'data' => $response_data,
                    'status' => self::NO_DATA_HTTP_RESPONSE_STATUS
                ], self::SUCCESS_HTTP_RESPONSE_STATUS);
            }
        }
    }
}
