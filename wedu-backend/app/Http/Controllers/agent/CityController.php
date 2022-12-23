<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\RetsPropertyData;
use App\Models\SqlModel\CityData;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\CityNeighbours;
use Illuminate\Http\Request;


class CityController extends Controller
{
    //
    public function get_all_city()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | All City ";
        // $city_list = PropertyData::distinct('City')->get('City');
        return view('agent.city.city_list',$data);
    }
    public function get_city_list(Request $request)
    {
        # Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $columnIndex_arr[0]['dir']; // asc or desc
        $query = RetsPropertyData::distinct('City');
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get('City');
        $data_array = array();

        foreach ($records as $key => $record) {
            $cityname = $record->City;
            $Featured= CityData::where('CityName',$cityname)->get('Featured');
            $featured_or_not='<i class="far fa-heart featured" onclick="city_featured(1,this.title)" title="'.$cityname.'"></i>';
            if(count($Featured)>0)
            {
                if($Featured[0]->Featured==1)
                {
                    $featured_or_not = '<i class="fas fa-heart featured" onclick="city_featured(0,this.title)" title="'.$cityname.'"></i>';
                }
            }
            $propertyList= RetsPropertyData::where('City',$cityname)->count();
            $edit = '<a href="'.url('agent/city/edit-city/'.$cityname).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $data_arr['id']=$start+$key+1;
            $data_arr['City']=$record->City;
            $data_arr['City']=$record->City;
            $data_arr['Properties']=$propertyList;
            $data_arr['Action']=$edit;
            $data_arr['Featured']=$featured_or_not;
            $data_array[]=$data_arr;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_array
        );

        echo json_encode($response);
        exit;

    }
    public function edit_city($cityname=null)
    {
        $APP_NAME = env('APP_NAME');
        $title= $APP_NAME." | Edit City";
        if($cityname)
        {
            $agentId = Auth::user()->id;
            $city_exist=CityData::where('AgentId',$agentId)->where('CityName',$cityname)->first();
            if($city_exist)
            {
                $data['city_data']=$city_exist;
            }
            else
            {
                $data['city_data']=['CityName'=>$cityname];
            }

            $title= $APP_NAME." | Edit City ";
        }
        $data["pageTitle"] = $title;
        return view('agent.city.edit_city',$data);
    }
    public function update_create_city(Request $request)
    {
        $form_data= $request->all();
        if(isset($form_data['Image']) && !empty($form_data['Image'])){
            $image = $form_data['Image'];
            $dir_img = "cities";
            $webp = compress_Image($image,$dir_img);
            $form_data['Image'] = $webp;
            // $form_data['Image']=saveImage($form_data["Image"]);
        }else{
            unset($form_data['Image']);
        }
        if(empty($form_data['Content'])){
            $form_data['Content'] = '-';
        }

        $city_name = $form_data['CityName'];
        $edit_city = CityData::updateOrCreate(['CityName' => $city_name], $form_data);
        if($edit_city){

            $message='City updated successfully !';
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
    public function get_area_list(Request $request)
    {
        # Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 5; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $columnIndex_arr[0]['dir']; // asc or desc
        $filter_city = $request->post('filter_city');
        $query = RetsPropertyData::distinct('Community')->where('City',$filter_city);
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get('Community');
        $data_array = array();
        $agentId = $request->post('agentId');;
        foreach ($records as $key => $record) {
            $area = $record->Community;
            $added_area = CityNeighbours::where('AgentId',$agentId)->where('AreaName',$area)->where('CityName',$filter_city)->first();
            $Featured= CityNeighbours::where('AgentId',$agentId)->where('AreaName',$area)->where('CityName',$filter_city)->get('Featured');
            $Featured =[];
            $featured_or_not='<i class="far fa-heart featured" onclick="area_featured(1,this.title)" title="'.$area.'"></i>';
            if(count($Featured)>0)
            {
                if($Featured[0]->Featured==1)
                {
                    $featured_or_not = '<i class="fas fa-heart featured" onclick="area_featured(0,this.title)" title="'.$area.'"></i>';
                }
            }
            $title = $tags = $description='--';
            if($added_area)
            {
                $title = $added_area->MetaTitle;
                $tags = $added_area->MetaTags;
                $description = $added_area->MetaDescription;
            }
            $edit = '<a href="'.url('agent/city/edit-area/'.$area.'/'.$filter_city).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $data_arr['id']=$start+$key+1;
            $data_arr['AreaName']=$record->Community;
            $data_arr['Title']=$title;
            $data_arr['Seo_Tags']=$tags;
            $data_arr['Description']=$description;
            $data_arr['Action']=$edit;
            $data_arr['Featured']=$featured_or_not;
            $data_array[]=$data_arr;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_array
        );

        echo json_encode($response);
        exit;
    }

    public function edit_area($area_name=null,$cityname=null)
    {
        $APP_NAME = env('APP_NAME');
        $title= $APP_NAME." | Edit Area";
        if($area_name && $cityname)
        {
            $agentId = Auth::user()->id;
            $area_exist=CityNeighbours::where('AgentId',$agentId)->where('AreaName',$area_name)->where('CityName',$cityname)->first();
            if($area_exist)
            {
                $data['area_data']=$area_exist;
            }
            else
            {
                $data['area_data']=['AreaName'=>$area_name,'CityName'=>$cityname];
            }

            $title= $APP_NAME." | Edit Area ";
        }
        $data["pageTitle"] = $title;
        return view('agent.city.edit_area',$data);
    }
    public function update_create_area(Request $request)
    {
        $form_data= $request->all();

        $AreaName = $form_data['AreaName'];
        $edit_area = CityNeighbours::updateOrCreate(['AreaName' => $AreaName], $form_data);
        if($edit_area){

            $message='Neighbours / Area updated successfully !';
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
    function city_featured(Request $request)
    {
         $form_data= $request->all();
        $city_name = $form_data['CityName'];
        $edit_city = CityData::updateOrCreate(['CityName' => $city_name], $form_data);
        if($edit_city){

            if($form_data['Featured']==0)
            {
                $message='City Unmark as Featured!';
            }
            else
            {
                $message='City Mark as Featured!';
            }

            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
    function area_featured(Request $request)
    {
        $form_data= $request->all();
        $city_name = $form_data['CityName'];
        $AreaName = $form_data['AreaName'];
        $MarkArea = CityNeighbours::updateOrCreate(['AreaName' => $AreaName], $form_data);
        if($MarkArea){

            if($form_data['Featured']==0)
            {
                $message='Area Unmark as Featured!';
            }
            else
            {
                $message='Area Mark as Featured!';
            }

            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Somethings wents wrong',
        ]);
    }
}
