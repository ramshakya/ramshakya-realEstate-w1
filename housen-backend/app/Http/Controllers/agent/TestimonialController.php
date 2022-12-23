<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SqlModel\Testimonial;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    //
    public function index()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | All Testimonial ";
        // $city_list = PropertyData::distinct('City')->get('City');
        return view('agent.testimonial.testimonial',$data);
    }
    public function getTestimonial(Request $request)
    {   
         # Read value
        // $agentId = 2;
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $columnIndex_arr[0]['dir']; // asc or desc
        $query = Testimonial::select();
        $totalRecords = $query->count();
        $query=$query->orderBy($columnName,$columnSortOrder);
        $query=$query->skip($start);
        $query=$query->take($rowperpage);
        $records = $query->get();
        $data_array = array();

        foreach ($records as $key => $record) {
            $id = $record->id;
            $edit = '<a href="'.url('agent/testimonial/edit-testimonial/'.$id).'" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>';
            $edit.='&nbsp;&nbsp;&nbsp;<span href="" class="text-danger cursor-pointer"  onclick="deleteType('.$id.')"><i class="fa fa-trash"></i></span>';
            $data_arr['id']=$start+$key+1;
            $data_arr['name']=$record->Name;
            $data_arr['description']=$record->Description;
            $data_arr['image']='<img src="'.$record->Image.'" width="30px">';
            $data_arr['Action']=$edit;
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
    public function addTestimonial($id=null)
    {
        $data["pageTitle"] = "Add testimonial";
        if($id){
            $data['testimonial']=Testimonial::where('id',$id)->first();
        }
        return view('agent.testimonial.add-testimonial',$data);
    }
    public function addEditTestimonial(Request $request)
    {
        $form_data= $request->all();
        $id=0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        if(isset($form_data['Image']) && !empty($form_data['Image'])){
            $image = $form_data['Image'];
            $dir_img = "testimonial";
            $webp = compress_Image($image,$dir_img);
            $form_data['Image']= $webp;
     
        }else{
            unset($form_data['Image']);
        }
        $unit_id = Testimonial::updateOrCreate(['id' => $id], $form_data);
        if($unit_id){
            if($id==0){
                $message='Testimonial added successfully !';
            }else{
                $message='Testimonial updated successfully !';
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
     public function DeleteTestimonial(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $id = $data['id'];
//            $this->authorize('delete',ProjectModel::class);
                $res= Testimonial::where("id",$id)->delete();
                // return redirect('project');
                if ($res) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Testimonial Deleted !',
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'data' => $data,
                        'message' => 'Something Wents Wrong !',
                    ]);
                }
            }


        }
}
