<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SqlModel\Pages;
use App\Models\SqlModel\Menu;
use Illuminate\Support\Facades\DB;

class MenuBuilderController extends Controller
{
    //
    public function index(Request $request)
    {
        $APP_NAME=env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Menu builder ";
        $menu_list = Menu::where('Status','Active')->get();
        $page_list = Pages::where('Status','Active')->get();
        $data['menu_list']= $menu_list;
        $data['page_list']=$page_list;
        return view('agent.menuBuilder.index',$data);
    }
    public function AddPage(Request $request)
    {
        $form_data= $request->all();
        $id=0;
        // return $form_data;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        $pages = Pages::updateOrCreate(['id' => $id], $form_data);
        if($pages){
            if($id==0){
                $message='Page added successfully !';
            }else{
                $message='{Page updated successfully !';
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
    public function add_menu(Request $request)
    {
        $form_data['AgentId'] = $request->AgentId;
        $form_data['MenuName'] = $request->MenuName;
        $insert_menu = Menu::insert($form_data);
        if($insert_menu)
        {
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => "Menu added successfully",
            ]);
        }
        else
        {
            return response()->json([
                'error' => true,
                'data' => $form_data,
                'message' => 'Somethings wents wrong',
            ]);
        }
    }
    public function add_menu_data(Request $request)
    {
        $form_data = $request->all();
        $id = $form_data['id'];
        $menu = Menu::updateOrCreate(['id' => $id], $form_data);
        if($menu){
            $message='Menu Saved successfully !';
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
    public function get_menu(Request $request)
    {
        $form_data = $request->all();
        $id = $form_data['id'];
        $menu = Menu::where(['id' => $id])->first();
        if($menu){
            if($menu->MenuContent!='')
            {
                $menuList = json_decode($menu->MenuContent);
                foreach ($menuList[0] as $key => $value) {
                    
                    echo '<li id="oldLinks'.$key.'" data-url="'.$value->url.'" data-value="'.$value->value.'" data-id="'.$value->id.'" data-type="'.$value->type.'"><span class="menu-item-bar"><b class="oldLinks'.$key.'">'.$value->value.'</b><a href="#" id="oldLinks'.$key.'" onclick="delete_menu(this.id)"><i class="fas fa-trash-alt"></i></a> <a href="#" id="oldLinks'.$key.'" data-value="'.$value->value.'" data-url="'.$value->url.'" data-type="'.$value->type.'" onclick="get_edited_value(this.id)" data-toggle="collapse" data-target="#d1emo"><i class="fa fa-edit"></i></a></span><ul>';
                
                        if(isset($value->children))
                        {

                            
                            foreach ($value->children[0] as $child_key=> $child) {
                               echo  '<li id="oldchildLinks'.$child_key.'" data-url="'.$child->url.'" data-value="'.$child->value.'" data-id="'.$child->id.'" data-type="'.$child->type.'"><span class="menu-item-bar"><b class="oldchildLinks'.$child_key.'">'.$child->value.'</b><a href="#" id="oldchildLinks'.$child_key.'" onclick="delete_menu(this.id)"><i class="fas fa-trash-alt"></i></a> <a href="#" id="oldchildLinks'.$child_key.'" data-value="'.$child->value.'" data-url="'.$child->url.'" data-type="'.$child->type.'" onclick="get_edited_value(this.id)" data-toggle="collapse" data-target="#d1emo"><i class="fa fa-edit"></i></a></span></li>';
                            }
                            
                        }

                     echo "</ul></li>";
                    }           
            }
            else
            {
                return '';
            }
        }
        else
        {
            return '';
        }
    }

    public function delete_data(Request $request)
    {
        $form_data= $request->all();
        $id = $form_data['id'];
        $tableName = $form_data['tableName'];
        $deleted = DB::table($tableName)->where('id', '=', $id)->delete();
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
                'message' => 'Somethings wents wrong',
            ]);
        }
    }
    
    
}
