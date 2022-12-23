<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RetsPropertyData;
class FrontendGetProperty extends Controller
{
    public function get_more_filters()
    {
        $type = RetsPropertyData::distinct('PropertyType')->get('PropertyType');
        $property_type=[];
        foreach ($type as $key => $value) {
            $property_type[] = $value['PropertyType'];
        }
        $beds = RetsPropertyData::distinct('BedroomsTotal')->orderBy('BedroomsTotal', 'asc')->get('BedroomsTotal');
        $zero = '.00';
        $bedrooms = [];
        foreach ($beds as $key => $value) {
            if($value['BedroomsTotal']!=0){
                $bedrooms[] = str_replace($zero, '', $value['BedroomsTotal']);
            }

        }
        sort($bedrooms);
        $baths = RetsPropertyData::distinct('BathroomsFull')->orderBy('BathroomsFull', 'asc')->get('BathroomsFull');
        $bathrooms = [];
        foreach ($baths as $key => $value) {
            if($value['BathroomsFull']!=0){
                $bathrooms[] = str_replace($zero, '', $value['BathroomsFull']);
            }

        }
        sort($bathrooms);
        $status = RetsPropertyData::distinct('MlsStatus')->get('MlsStatus');
        $Mls_status = [];
        foreach ($status as $key => $value) {
           $Mls_status[]= $value['MlsStatus'];
        }
        $basement = RetsPropertyData::distinct('Bsmt1_out')->get('Bsmt1_out');
        $basement_ = [];
        foreach ($basement as $key => $value) {
            if($value['Bsmt1_out']!=''){
               $basement_[]= $value['Bsmt1_out'];
           }
        }
        $data['type'] = $property_type;
        $data['beds'] = $bedrooms;
        $data['baths'] = $bathrooms;
        $data['status'] = $Mls_status;
        $data['basement'] = $basement_;
        return json_encode($data);
    }
    public function get_cities()
    {
       $query = RetsPropertyData::distinct('City')->get('City');
       $city = [];
       foreach ($query as $key => $value) {
           $city[] = $value['City'];
       }
       return $data['city'] = json_encode($city);
    }
    public function GetPropertyByCity(Request $request)
    {
        $form_data = $request->all();

        if(!empty($form_data))
        {
           $city = $form_data['city'];
        }
        else
        {
            $city = "toronto";
        }
        $offset = 0;
        $limit = 10;
        $query = RetsPropertyData::where('City',$city)->offset($offset)->limit($limit)->get();
        return json_encode($query);
    }
    
}
