<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use App\Models\RetsPropertyData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SqlModel\Pages;
use App\Models\SqlModel\Predefined_page;
use Image;


use Illuminate\Http\Request;


class PagesController extends Controller
{
    //
    public function index()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Pages ";
        $page_list = Pages::where("AgentId",Auth::user()->id)->get();
        $data['page_list'] = $page_list;
        return view('agent.pages.pages',$data);
    }
    public function create_page($id=null)
    {
        $APP_NAME = env('APP_NAME');
        $title= $APP_NAME." | Create page ";
        if($id)
        {
            $data['page']=Pages::where('id',$id)->first();
            $title= $APP_NAME." | Edit page ";
        }
        $data["pageTitle"] = $title;
        return view('agent.pages.create_page',$data);
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
                $message='Page updated successfully !';
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
    // public function create_code($id=null)
    // {
    //     $APP_NAME = env('APP_NAME');
    //     $data["pageTitle"] = $APP_NAME." | Create code ";
    //     if($id)
    //     {
    //         $data['page']=Pages::where('id',$id)->first();
    //     }

    //     return view('agent.pages.create_code',$data);
    // }
    public function create_code($id=null)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Create code ";
        $pageName="";
        $source="not-found";
        $id = $id;
        if($id)
        {
            $data['page']=Pages::where('id',$id)->first();
            if($data['page']){
                $pageName = $data['page']->PageName;
                if($pageName=='home'){
                    $source = "home_edit";
                }elseif ($pageName == 'property details') {
                    $source = "property_edit";
                }
            }
        }
        return view('agent.pages.edit.'.$source,$data);
    }
    public function update_code(Request $request)
    {
        $form_data= $request->all();
        $id=0;
        // return $form_data;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        if ($request->hasfile('TopBanner')) {
            $file = $request->file('TopBanner');
            $name = time() . '.' . $request->TopBanner->extension() ;
            $path = $request->file('TopBanner')->storeAs('public/img/img/',$name);
            $dir_img = "banner";
            $path = storage_path('app/public/img/img/'  .  $name);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dat = file_get_contents($path);
            $base64 = 'data:image/' . $type . '; base64,' . base64_encode($dat);
            $webp = compress_Image($base64,$dir_img);
            unset($form_data['TopBanner']);
            $form_data['TopBanner'] = $webp;
        }
        else
        {
           $form_data['TopBanner'] = $form_data['OlderImage'];
        }
        if ($request->hasfile('CommunityBanner')) {
            $file = $request->file('CommunityBanner');
            $name = time() . '.' . $request->TopBanner->extension() ;
            $path = $request->file('CommunityBanner')->storeAs('public/img/img/',$name);
            $dir_img = "banner";
            $path = storage_path('app/public/img/img/'  .  $name);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $dat = file_get_contents($path);
            $base64 = 'data:image/' . $type . '; base64,' . base64_encode($dat);
            $webp = compress_Image($base64,$dir_img);
            unset($form_data['CommunityBanner']);
            $form_data['CommunityBanner'] = $webp;
        }
        else
        {
            $form_data['CommunityBanner'] = $form_data['OlderCommunityImage'];
        }
        unset($form_data['OlderImage']);
        unset($form_data['OlderCommunityImage']);
        if (isset($form_data['topBannerSection'])=="checked") {
            $form_data['topBannerSection'] = 'show';
        }else{
            $form_data['topBannerSection'] = 'hide';
        }
        if (isset($form_data['communityBannerSection'])=="checked") {
            $form_data['communityBannerSection'] = 'show';
        }else{
            $form_data['communityBannerSection'] = 'hide';
        }
        if (isset($form_data['contentSection'])=="checked") {
            $form_data['contentSection'] = 'show';
        }else{
            $form_data['contentSection'] = 'hide';
        }
        if (isset($form_data['blogSection'])=="checked") {
            $form_data['blogSection'] = 'show';
        }else{
            $form_data['blogSection'] = 'hide';
        }
        if (isset($form_data['citySection'])=="checked") {
            $form_data['citySection'] = 'show';
        }else{
            $form_data['citySection'] = 'hide';
        }
        if (isset($form_data['featuredSection'])=="checked") {
            $form_data['featuredSection'] = 'show';
        }else{
            $form_data['featuredSection'] = 'hide';
        }
        if (isset($form_data['recentSection'])=="checked") {
            $form_data['recentSection'] = 'show';
        }else{
            $form_data['recentSection'] = 'hide';
        }
        if (isset($form_data['testimonialSection'])=="checked") {
            $form_data['testimonialSection'] = 'show';
        }else{
            $form_data['testimonialSection'] = 'hide';
        }
        if (isset($form_data['contectFormSection'])=="checked") {
            $form_data['contectFormSection'] = 'show';
        }else{
            $form_data['contectFormSection'] = 'hide';
        }
        if (isset($form_data['profileSection'])=="checked") {
            $form_data['profileSection'] = 'show';
        }else{
            $form_data['profileSection'] = 'hide';
        }
// dd(json_decode($data));
        $data['Setting'] = json_encode($form_data);
        $pages = Pages::updateOrCreate(['id' => $id], $data);
        if($pages){
            if($id==0){
                $message='Code added successfully !';
            }else{
                $message='Code updated successfully !';
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
    public function update_code_property(Request $request)
    {
        $form_data= $request->all();
        $id=4;
        // return $form_data;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }
        if (isset($form_data['descriptionSection'])=="checked") {
            $form_data['descriptionSection'] = 'show';
        }else{
            $form_data['descriptionSection'] = 'hide';
        }
        if (isset($form_data['listingsSection'])=="checked") {
            $form_data['listingsSection'] = 'show';
        }else{
            $form_data['listingsSection'] = 'hide';
        }
        if (isset($form_data['extrasSection'])=="checked") {
            $form_data['extrasSection'] = 'show';
        }else{
            $form_data['extrasSection'] = 'hide';
        }
        if (isset($form_data['propertySection'])=="checked") {
            $form_data['propertySection'] = 'show';
        }else{
            $form_data['propertySection'] = 'hide';
        }
        $data['Setting'] = json_encode($form_data);
        $pages = Pages::updateOrCreate(['id' => $id], $data);
        if($pages){
            if($id==0){
                $message='Code added successfully !';
            }else{
                $message='Code updated successfully !';
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
    public function createCode($id=null)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Create code ";
        if($id)
        {
            $data['page']=Pages::where('id',$id)->first();
        }

        return view('agent.pages.page-code',$data);
    }
    public function predefine_pages()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME." | Predefine Pages ";
        $page_list = Predefined_page::All();
        $data['page_list'] = $page_list;
        return view('agent.pages.predefinePage',$data);
    }
    public function create_predefine_pages($id=null)
    {
         $APP_NAME = env('APP_NAME');
        $title= $APP_NAME." | Create Predefine Page ";
        if($id)
        {
            $data['page']=Predefined_page::where('id',$id)->first();
            $data['city_added'] = json_decode($data['page']['City']);
            $data['property_type_added'] = json_decode($data['page']['PropertyType']);
            $data['area_added'] = json_decode($data['page']['Area']);

            $title= $APP_NAME." | Edit Predefine page ";
        }
        $data["pageTitle"] = $title;
        $data['cities']=RetsPropertyData::distinct('City')->get('City');
        $data['PropertyType']=RetsPropertyData::distinct('PropertyType')->get('PropertyType');
        $data['BuildingAreaSource']=[];
        $price = 25000;
            $price_array=[];
            $increase = 10000;
            while ($price<=5000000) {
                $price_array[]=$price;
                if($price==45000)
                {
                    $increase=30000;
                }
                elseif($price==100000)
                {
                    $increase=50000;
                }
                elseif($price==1000000)
                {
                    $increase=500000;
                }
                $price+=$increase;
            }
            $data['price']=$price_array;
        // return $data['BuildingAreaSource'];
        return view('agent.pages.create_predefine_page',$data);
    }
    public function AddPredefinePage(Request $request)
    {
        $form_data= $request->all();
        $id=0;
        // return $form_data;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $id=$form_data['id'];
            unset($form_data['id']);
        }

        $pages = Predefined_page::updateOrCreate(['id' => $id], $form_data);
        if($pages){
            if($id==0){
                $message='Predefined page added successfully !';
            }else{
                $message='Predefined page updated successfully !';
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
