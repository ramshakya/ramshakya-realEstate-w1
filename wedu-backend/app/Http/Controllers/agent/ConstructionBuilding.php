<?php
namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\Builder;
use App\Models\SqlModel\PreConstruction;
use App\Models\SqlModel\MasterAmenities;
use Illuminate\Http\Request;

class ConstructionBuilding extends Controller
{
    //
    public function building_list()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | All Buildings ";
        return view('agent.building.building_list',$data);
    }
    public function create_update_building($id=null)
    {
        if($id!="")
        {
            $query = PreConstruction::where('id',$id)->first();
            $data['PreConstruction'] = $query;
            $data['enimitiesId'] =  json_decode($query['Amenities']);
            $data['enimitiesMaintId'] =  json_decode($query['AmenitiesMaintenance']);
            $data['MediaImage'] =  json_decode($query['MediaImage']);
            $data['Attechments'] =  json_decode($query['Attechments']);
        }
        $data['builder'] = Builder::where('Status','Active')->get(['id','BuilderName']);
        
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Create | Update Buildings ";
        $data['Amenities'] = MasterAmenities::get(['id','Name']);
        return view('agent.building.create_upate_building',$data);

    }
    public function get_preconstruction_building(Request $request)
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
        $query = PreConstruction::where('Status','!=','Deleted');
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get(['id','BuildingName','Address','City','Status']);
        $data_array = array();
    
        foreach ($records as $key => $record) {
            $id = $record->id;
            $edit = '<a href="'.url('agent/building/create-update-building/'.$id).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $delete = '&nbsp;&nbsp; <a href="#" onclick="get_delete_value(this.id,this.name)" data-toggle="modal" data-target="#delete_data" name="PreConstructionBuilding" class="text-danger" id="'.$id.'" ><i class="fa fa-trash"></i></a>';
            $data_arr['id']=$start+$key+1;
            $data_arr['BuildingName']=$record->BuildingName;
            $data_arr['Address']=$record->Address;
            $data_arr['City']=$record->City;;
            $data_arr['Action']=$edit.$delete;
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
    public function add_builder_data(Request $request)
    {
        $form_data = $request->all();
        if($request->hasFile('Logo'))
        {
            $logo = $request->file('Logo');
            $ext = $logo->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            // $path = 'public/img';
            $logo->move('storage/',$filename);
            // $imagePath =$request->file('Logo')->storeAs($path,$filename);
            $form_data['Logo']=url('storage/'.$filename);
        } 
        else
        {
            unset($form_data['Logo']);
        }

        unset($form_data['_token']);
        $form_data['Status']='Active';
        $id=0;
        if(isset($form_data['id']) && !empty($form_data['id']))
        {
            $id = $form_data['id'];
        }
        $builder = Builder::updateOrCreate(['id' => $id], $form_data);
        if($builder){
            if($id==0){
              $message='Builder Added Successfully !';
            }
            else
            {    
              $message='Builder Updated Successfully !';
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
    // public function get_builder()
    // {
    //     $builder = Builder::where('Status','Active')->get(['id','BuilderName']);
    //     return json_encode($builder);
    // }
    public function add_construction_data(Request $request)
    {
        $form_data = $request->all();
        unset($form_data['_token']);
        $form_data['Status']='Active';
        $id = 0;
        $images=[];
        if($request->hasfile('MediaImage')){
            foreach ($request->file('MediaImage') as $value) {
                $ext = $value->getClientOriginalExtension();
                $filename = time().rand(1,99999).'.'.$ext;
                $value->move('storage/',$filename);
                $images[]=url('storage/'.$filename);
            }  
        }
        if(isset($form_data['addedImage'])){
            foreach (json_decode($form_data['addedImage']) as $key => $value) {
                array_push($images,$value);
            }
        }
        $form_data['MediaImage'] = json_encode($images);
        
        $attech = [];
        if($request->hasfile('Attechments'))
        {
            foreach ($request->file('Attechments') as $value) {
                $ext = $value->getClientOriginalExtension();
                $filename = time().rand(1,99999).'.'.$ext;
                $value->move('storage/',$filename);
                $attech[]=url('storage/'.$filename);
            }
            
        }
        if(isset($form_data['addedAttech'])){
            foreach (json_decode($form_data['addedAttech']) as $key => $value) {
                array_push($attech,$value);
            }
        }
        $form_data['Attechments'] = json_encode($attech);
        if(isset($form_data['id']) && !empty($form_data['id']))
        {
            $id = $form_data['id'];
        }
        if(isset($form_data['Amenities'])){
            $form_data['Amenities'] = json_encode($form_data['Amenities']);
        }
        if(isset($form_data['AmenitiesMaintenance'])){
            $form_data['AmenitiesMaintenance'] = json_encode($form_data['AmenitiesMaintenance']);
        }

        
        $PreConstruction = PreConstruction::updateOrCreate(['id' => $id], $form_data);
        if($PreConstruction){
            if($id==0){
              $message='Pre Construction Building Added Successfully !';
            }
            else
            {    
              $message='Pre Construction Building Updated Successfully !';
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
    public function builders()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | All Builders ";
        return view('agent.building.builders',$data);
    }
    public function add_edit_builder($id=null)
    {
        $APP_NAME = env('APP_NAME');

        if($id!='')
        {
            $data['builder']=Builder::where('id',$id)->first();
        }
        $data["pageTitle"] = $APP_NAME." | All Builders ";
        return view('agent.building.add_edit_builder',$data);
    }
    public function get_builder_data(Request $request)
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
        $query = Builder::where('Status','!=','Deleted');
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get(['id','BuilderName','BuilderAddress','BuilderCity','Status']);
        $data_array = array();
        
        foreach ($records as $key => $record) {
            $id = $record->id;
            $edit = '<a href="'.url('agent/building/add-edit-builder/'.$id).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $delete = '&nbsp;&nbsp; <a href="#" onclick="get_delete_value(this.id,this.name)" data-toggle="modal" data-target="#delete_data" name="builder" class="text-danger" id="'.$id.'" ><i class="fa fa-trash"></i></a>';
            $data_arr['id']=$start+$key+1;
            $data_arr['BuilderName']=$record->BuilderName;
            $data_arr['Address']=$record->BuilderAddress;
            $data_arr['City']=$record->BuilderCity;;
            $data_arr['Action']=$edit.$delete;
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
    public function amenity_list()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | All Amenities ";
        return view('agent.building.amenities',$data);
    }
    public function get_amenity_data(Request $request)
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
        $query = MasterAmenities::where('Status','!=','Deleted');
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get(['id','Name']);
        $data_array = array();
        
        foreach ($records as $key => $record) {
            $id = $record->id;
            $edit = '<a href="'.url('agent/building/add-edit-amenities/'.$id).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $delete = '&nbsp;&nbsp; <a href="#" onclick="get_delete_value(this.id,this.name)" data-toggle="modal" data-target="#delete_data" name="master_amenities" class="text-danger" id="'.$id.'" ><i class="fa fa-trash"></i></a>';
            $data_arr['id']=$start+$key+1;
            $data_arr['Name']=$record->Name;
            $data_arr['Action']=$edit.$delete;
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
    public function add_edit_amenities($id=null)
    {
        $APP_NAME = env('APP_NAME');

        if($id!='')
        {
            $data['MasterAmenities']=MasterAmenities::where('id',$id)->first();
        }
        $data["pageTitle"] = $APP_NAME." | All Builders ";
        return view('agent.building.add_edit_amenity',$data);
    }
    public function add_amenity_data(Request $request)
    {
        $id=0;
        $form_data = $request->all();
        if(isset($form_data['id']) && !empty($form_data['id']))
        {
            $id = $form_data['id'];
        }
        $form_data['Status']='Active';
        $enm = MasterAmenities::updateOrCreate(['id' => $id], $form_data);
        if($enm){
            if($id==0){
              $message='Amenity Added Successfully !';
            }
            else
            {    
              $message='Amenity Updated Successfully !';
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
    public function get_builder(Request $request)
    {
        $form_data = $request->all();
        $id= $form_data['id'];
        $builders = Builder::where('id','!=',$id)->where('Status','=','Active')->get(['id','BuilderName']);
        return json_encode($builders);
    }
}
