<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SqlModel\BlogCategory;
use App\Models\SqlModel\BlogModel;

class BlogController extends Controller
{
    //
    public function index(){
        $data["pageTitle"] = "Blogs";
        $category= BlogModel::all();
//        $cat=[];
//        foreach ($category as &$k){
//            $k->ParentName=ParentName($k->ParentId);
//        }
        $data['category']=$category;
//        return $data;
        return view('agent.blog.blogs',$data);
    }
    public function category() {
        $data["pageTitle"] = "Categories";
        $category= BlogCategory::all();
//        $cat=[];
        foreach ($category as &$k){
            $k->ParentName=ParentName($k->ParentId);
        }
        $data['category']=$category;
//        return $data;
        return view('agent.blog.Category',$data);
    }
    public function CreateCategory($id=null){
        $data["pageTitle"] = "Create Category";
        $data['category']= BlogCategory::where('ParentId',0)->get();
        if($id){
            $data['cat']=BlogCategory::where('id',$id)->first();
        }
        return view('agent.blog.CreateCategory',$data);
    }
    public function CreateBlog($id=null){
        $data["pageTitle"] = "Create Blog";
        $data['category']= BlogCategory::where('ParentId',0)->get();
        if($id){
            $data['blogs']=BlogModel::where('id',$id)->first();
        }
        return view('agent.blog.CreateBlog',$data);
    }
    public function addCategory(Request $request){
        $form_data= $request->all();
        $id=0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        $unit_id = BlogCategory::updateOrCreate(['id' => $id], $form_data);
        if($unit_id){
            if($id==0){
                $message='Category added successfully !';
            }else{
                $message='Category updated successfully !';
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
    public function addBlog(Request $request){
        $form_data= $request->all();
//        return $request;
        $id=0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        if(isset($form_data['Categories']) && !empty($form_data['Categories'])){
            $form_data['Categories']=json_encode($form_data['Categories']);
        }
        if(isset($form_data['MainImg']) && !empty($form_data['MainImg'])){

            $image = $form_data['MainImg'];
            $dir_img = "blog";
            $webp = compress_Image($image,$dir_img);
           $form_data['MainImg']=$webp;
        //    $form_data['MainImg']=saveImage($form_data["MainImg"]);

        }else{
            unset($form_data['MainImg']);
        }
//        return $form_data;
        $unit_id = BlogModel::updateOrCreate(['id' => $id], $form_data);
        if($unit_id){
            if($id==0){
                $message='Blog added successfully !';
            }else{
                $message='Blog updated successfully !';
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
    public function DeleteBlog(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            $id = $data['id'];
//            $this->authorize('delete',ProjectModel::class);
                $res= BlogModel::where("id",$id)->delete();
                // return redirect('project');
                if ($res) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Blog Deleted !',
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
        public function DeleteBlogCategory(Request $request)
        {
            $data = $request->all();
            if (isset($data['id'])) {
                $id = $data['id'];
                $blog_cat = BlogCategory::select("id")->where("id",$id)->first();
                if (isset($blog_cat) && !empty($blog_cat)) {
                    $blog_catogery = $blog_cat->id;
                    $blogs= BlogModel::select("Categories")->where("Categories","like","%".$blog_catogery."%")->get();
                    foreach($blogs as $blog){
                        $blog_cats= json_decode($blog->Categories);
                        if (in_array($blog_catogery, $blog_cats)){
                            return response()->json([
                                'warning' => true,
                                'data' => $data,
                                'message' => 'Please, first delete active blogs of this catogery',
                            ]);
                        }
                    }
                }
                $res= BlogCategory::where("id",$id)->delete();
                if ($res) {
                    return response()->json([
                        'success' => true,
                        'data' => $data,
                        'message' => 'Blog Deleted !',
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
