<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\SqlModel\EmailLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FrontendGetProperty extends Controller
{
    //
    public function getFiltersData(Request $request)
    {
        // city -> region
       // community ->sub region
        $subRegion = [];
        if ($request->City) {
            $subRegion = RetsPropertyData::distinct('Community')->where("Community","!=","")->where('City', $request->City)->get('Community');
        }
        $region = RetsPropertyData::distinct('City')->where("City","!=","")->get('City');
        $data['subRegion'] = $subRegion;
        $data['region'] = $region;
        return json_encode($data);
    }



    public function getMoreFilters()
    {
        $type = RetsPropertyData::distinct('PropertyType')->get('PropertyType');
        $property_type = [];
        foreach ($type as $key => $value) {
            $property_type[] = $value['PropertyType'];
        }
        $beds = RetsPropertyData::distinct('BedroomsTotal')->orderBy('BedroomsTotal', 'asc')->get('BedroomsTotal');
        $zero = '.00';
        $bedrooms = [];
        foreach ($beds as $key => $value) {
            if ($value['BedroomsTotal'] != 0) {
                $bedrooms[] = str_replace($zero, '', $value['BedroomsTotal']);
            }
        }
        sort($bedrooms);
        $baths = RetsPropertyData::distinct('BathroomsFull')->orderBy('BathroomsFull', 'asc')->get('BathroomsFull');
        $bathrooms = [];
        foreach ($baths as $key => $value) {
            if ($value['BathroomsFull'] != 0) {
                $bathrooms[] = str_replace($zero, '', $value['BathroomsFull']);
            }
        }
        sort($bathrooms);
        $status = RetsPropertyData::distinct('MlsStatus')->get('MlsStatus');
        $Mls_status = [];
        foreach ($status as $key => $value) {
            $Mls_status[] = $value['MlsStatus'];
        }
        $basement = RetsPropertyData::distinct('Bsmt1_out')->get('Bsmt1_out');
        $basement_ = [];
        foreach ($basement as $key => $value) {
            if ($value['Bsmt1_out'] != '') {
                $basement_[] = $value['Bsmt1_out'];
            }
        }
        $data['type'] = $property_type;
        $data['beds'] = $bedrooms;
        $data['baths'] = $bathrooms;
        $data['status'] = $Mls_status;
        $data['basement'] = $basement_;
        return json_encode($data);
    }
    public function getCities()
    {
        $query = RetsPropertyData::distinct('City')->limit(7)->get('City');
        $city = [];
        foreach ($query as $key => $value) {
            $city[] = $value['City'];
        }
        return $data['city'] = json_encode($city);
    }
    public function GetPropertyByCity(Request $request)
    {
        $form_data = $request->all();

        if (!empty($form_data)) {
            $city = $form_data['city'];
        } else {
            $city = "toronto";
        }
        $offset = 0;
        $limit = 10;
        $query = RetsPropertyData::where('City', $city)->offset($offset)->limit($limit)->get();
        return json_encode($query);
    }
    public function GetFavouriteProperty(Request $request)
    {
        $id = $request->all('LeadId');
        $agentId = $request->all('AgentId');
        $favourite = DB::table('FavouriteProperties')->where('LeadId', $id)
            ->where("AgentId", $agentId)
            ->get();
        $fav_array = array();
        foreach ($favourite as $key => $value) {
            if (!in_array($value->ListingId, $fav_array)) {
                $fav_array[] = $value->ListingId;
            }
        }
        // return $fav_array;
        $query = RetsPropertyData::whereIn('ListingId', $fav_array)->get();
        $temp_array = [];
        if (collect($query)->count() > 0) {
            foreach ($query as $data) {
                $data = collect($data)->all();
                $data = getDom($data);
                $temp_array[] = $data;
            }
        }
        return $temp_array;
    }

    public function MakeFavouriteProperty(Request $request)
    {
        $response = [];
        $validator = Validator::make($request->all(), [
            "LeadId" => "required",
            "AgentId" => "required",
            "ListingId" => "required",
            "Fav" => "required"
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $form_data = $request->all();
            $data['LeadId'] = $form_data['LeadId'];
            $data['ListingId'] = $form_data['ListingId'];
            $data['AgentId'] = $form_data['AgentId'];
            $fav = $form_data['Fav'];
            if ($fav) {
                $getFav = DB::table('FavouriteProperties')
                    ->where("LeadId", $data["LeadId"])
                    ->where("ListingId", $data["ListingId"])
                    ->where("AgentId", $data["AgentId"])
                    ->get();
                if (count($getFav) > 0) {
                    $response["errors"] = "Listing already in favourite collection";
                    return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
                } else {
                    $data['created_at'] = date("Y-m-d H:i:s");
                    $fav = DB::table('FavouriteProperties')->insert($data);
                }
                if ($fav) {
                    $response["success"] = "Listing Marked As Favorite";
                } else {
                    $response["errors"] = "Something went wrong";
                }
            } else {
                $DelFav = DB::table('FavouriteProperties')
                    ->where('LeadId', $data['LeadId'])
                    ->where('ListingId', $data['ListingId'])
                    ->where('AgentId', $data['AgentId'])
                    ->delete();
                if ($DelFav) {
                    $response["success"] = "Listings Removed From Favourites";
                } else {
                    $response["errors"] = "Listings is not present in your Favourites List";
                }
            }
        }
        return response($response, 200);
    }
    public function DeleteFavouriteProperty(Request $request)
    {
        $response = [];
        $validator = Validator::make($request->all(), [
            "LeadId" => "required",
            "AgentId" => "required",
            "ListingId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $form_data = $request->all();
            $LeadId = $form_data['LeadId'];
            $ListingId = $form_data['ListingId'];
            $AgentId = $form_data['AgentId'];
            $DelFav = DB::table('FavouriteProperties')
                ->where('LeadId', $LeadId)
                ->where('ListingId', $ListingId)
                ->where('AgentId', $AgentId)
                ->delete();
            if ($DelFav) {
                $response["success"] = "Unmark as favourite";
            } else {
                $response["errors"] = "Listings is not present in your Favourites List";
            }
        }
        return response($response, 200);
    }

    public function getFavouriteProperties(Request $request)
    {
        $response = [];
        $getFav = [];
        $validator = Validator::make($request->all(), [
            "LeadId" => "required",
            "AgentId" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        if ($validator->passes()) {
            $form_data = $request->all();
            $LeadId = $form_data['LeadId'];
            $AgentId = $form_data['AgentId'];
            $getFav = DB::table('FavouriteProperties')
                ->where('LeadId', $LeadId)
                ->where('AgentId', $AgentId)
                ->get();
        }
        return response($getFav, 200);
    }

    public function getSlugs()
    {
        $response = [];
        $query = "SELECT  `SlugUrl` FROM `RetsPropertyData` ;";
        $data = DB::select($query);
        $data = collect($data)->pluck("SlugUrl")->all();
        return response($data, 200);
    }

    public function updateEmail($hash = null)
    {
        if (isset($hash)) {
            $data['IsSent'] = 1;
            $data['OpenedTime'] = 1;
            $data['IsRead'] = 1;
            $data['LastSeen'] = date("Y-m-d h:i:s");
            $opened_time = EmailLogs::select('OpenedTime', 'SeenAt')->where("HashId", $hash)->first();
            if (isset($opened_time)) {
                $i = $opened_time->OpenedTime + 1;
                $data['OpenedTime'] = $i;
                if (isset($opened_time->SeenAt)) {
                    $data['SeenAt'] = $opened_time->SeenAt;
                } else {
                    $data['SeenAt'] = date("Y-m-d h:i:s");
                }
            }
            $data['SeenAt'] = date("Y-m-d h:i:s");
        }
        EmailLogs::updateOrCreate(['HashId' => $hash], $data);
        $data = \App\Models\SqlModel\Websetting::where("AdminId", '=', 3)->first();
        $websiteLogo = $data->UploadLogo;
        return $websiteLogo;
    }

    public function UnsubscribeEmail($hash = null)
    {
        if (isset($hash)) {
            $users = \App\Models\SqlModel\SavedSearchFilter::select("emailHash", "id", "subscribe")->where("emailHash", $hash)->first();
            if (isset($users) && !empty($users) && $users->emailHash !== NULL) {
                $users->subscribe = 0;
                $users->save();
            }
        }
        return Redirect::to(env('HOUSENFRONTURL'));
    }
}
