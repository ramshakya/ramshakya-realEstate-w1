<?php

namespace App\Http\Controllers\agent\property;

use App\Models\RetsPropertyData;

use App\Models\SqlModel\Pages;
use Response;
use App\Http\Controllers\Controller;
use App\Models\RetsPropertyDataImage;
use App\Models\SqlModel\PropertyData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SqlModel\retsPropertyDataPurged;
use App\Models\RetsPropertyData as ModelsRetsPropertyData;
use App\Models\SqlModel\lead\LeadsModel;
use App\Models\SqlModel\RetsPropertyDataSql;
use App\Models\SqlModel\FeaturedListing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\SqlModel\FeaturesMaster;
use App\Models\SqlModel\PropertyFeatures;
use ZipArchive;
use File;

class PropertyController extends Controller
{
    public $retsPropertyData;
    protected $sql;
    public function __construct()
    {
        $db = env('RUNNING_DB_INFO');
        if ($db == "sql") {
            $this->sql = "sql";
            $this->retsPropertyData = new RetsPropertyDataSql();
            $this->PropertyData = new RetsPropertyData();
            $this->retsPropertyDataPurged = new RetsPropertyData();
            $this->LeadsModel = new LeadsModel();
            $this->PostalMasterModel = "";

        } else {
            $this->retsPropertyData = new RetsPropertyData();
            //$this->PropertyData = new \App\Models\MongoModel\Property_Data();

            //$this->LeadsModel = new \App\Models\MongoModel\LeadsModel();
            $this->PostalMasterModel = "";

        }
    }
    //
    public function index()
    {
        $data["pageTitle"] = "Property Details";
        $data['cities'] = \App\Models\RetsPropertyData::distinct('City')->orderBy('City')->get('City');
        $data['heating'] = \App\Models\RetsPropertyData::distinct('Heating')->get('Heating');
        $data['cooling'] = [];
        $data['pool'] = [];
        $data['PropertySubType'] = \App\Models\RetsPropertyData::distinct('PropertySubType')->get('PropertySubType');
        $data['features_master'] = FeaturesMaster::select('Features','id')->limit(500)->get();
        // $data['features_master'] = [];
        //
        $price = 25000;
        $price_array = [];
        $increase = 10000;
        while ($price <= 5000000) {
            $price_array[] = $price;
            if ($price == 45000) {
                $increase = 30000;
            } elseif ($price == 100000) {
                $increase = 50000;
            } elseif ($price == 1000000) {
                $increase = 500000;
            }
            $price += $increase;
        }
        $data['price'] = $price_array;
        return view('agent.property.propertyData', $data);
    }
    // Property data filter
    /*public function getData(Request $request) {
        $request_data = $request->all();
        $data = $this->retsPropertyData->get_data($request_data);
        //$data = $this->retsPropertyData::a
        return $data;
    }*/
    public function getData(Request $request)
    {

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 10; // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        //$searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $query = \App\Models\RetsPropertyData::select();
        if (isset($getdata['type'])) {
            $type = $getdata['type'];
            $query = $query->where('PropertyType', $type);
        }
        if (isset($getdata['search'])) {
            $search = $getdata['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('ListingId', 'like', '%' . $search . '%')
                    ->orWhere('StandardAddress', 'like', '%' . $search . '%')
                    ->orWhere('MlsStatus', 'like', '%' . $search . '%')
                    ->orWhere('ListPrice', 'like', '%' . $search . '%');
            });
        }
        if (isset($getdata['cities'])) {
            $cities = $getdata['cities'];
            $query = $query->whereIn('City', $cities);
        }
        if (isset($getdata['min_price'])) {
            $min_price = (int)$getdata['min_price'];
            $query = $query->where('ListPrice', '>=', $min_price);
        }

        if (isset($getdata['max_price'])) {
            $max_price = (int)$getdata['max_price'];
            $query = $query->where('ListPrice', '<=', $max_price);
        }
        if (isset($getdata['BedroomsTotal'])) {
            $BedroomsTotal = (int)$getdata['BedroomsTotal'];
            $query = $query->where('BedroomsTotal', '>=', $BedroomsTotal);
        }
        if (isset($getdata['BathroomsFull'])) {
            $BathroomsFull = (int)$getdata['BathroomsFull'];
            $query = $query->where('BathroomsFull', '>=', $BathroomsFull);
        }

        if (isset($getdata['Area_min_size'])) {
            $Area_min_size = (int)$getdata['Area_min_size'];
            $query = $query->where('Sqft', '>=', $Area_min_size);
        }
        if (isset($getdata['Area_max_size'])) {
            $Area_max_size = (int)$getdata['Area_max_size'];
            $query = $query->where('Sqft', '<=', $Area_max_size);
        }
        if (isset($getdata['PropertySubType'])) {
            $PropertySubType = $getdata['PropertySubType'];
            $query = $query->where('PropertySubType', $PropertySubType);
        }
        if (isset($getdata['feature_filter'])) {
            $feature_filter = $getdata['feature_filter'];
            $propertyId = PropertyFeatures::whereIn('FeaturesId', $feature_filter)->pluck('propertyId');
            $array = [];
            $array1 = json_decode(json_encode($propertyId), true);
            $counter = array_count_values($array1);
            foreach ($array1 as $value) {
                if ($counter[$value] == count($feature_filter)) {
                    if (!in_array($value, $array)) {
                        $array[] = $value;
                    }
                }
            }

            $query = $query->whereIn('ListingId', $array);
        }
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        $query = $query->orderBy($columnName, $columnSortOrder);
        $query = $query->offset($start);
        // $query = $query->with("isFeatured");
        $query = $query->limit($rowperpage);
        $records = $query->get();
        //        dd($records);
        $data_arr = array();
        $data_array = [];
        $srno = intval($start) + 1;
        foreach ($records as $record) {
            // dd($record->ListingId,$record->isFeatured->ListingId);
            $thumbnailImage = "";

            $image = RetsPropertyDataImage::where("listingID", $record["ListingId"])->first();
                    //    return $image;
            if ($image) {
                if ($image->s3_image_url != null) {
                    //                    $image = json_decode($image->s3_image_url);
                    //                    $image=explode($image->image_url);
                    if ($image != []) {
                        $thumbnailImage = "<img src='/storage" . $image->s3_image_url . "' width='50' class='PropertyModelBtn' data-id='" . $record["ListingId"] . "'/>";
                    } else {
                        $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
                    }
                } else {
                    $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
                }
            } else {
                $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
            }

            $data_arr['id'] = $srno;
            $data_arr['ListingId'] = $record->ListingId;
            $data_arr['MlsStatus'] = $record->MlsStatus;
            $data_arr['StandardAddress'] = $record->StandardAddress;
            $data_arr['ListPrice'] = intval($record->ListPrice);
            $data_arr['LotSizeSquareFeet'] = $record->Sqft;
            //            $data_arr['Sp_dol'] = $record->Sp_dol;
            $data_arr['BedroomsTotal'] = intval($record->BedroomsTotal);
            $data_arr['BathroomsFull'] = $record->BathroomsFull;
            $data_arr['PropertyType'] = $record->PropertyType;
            $data_arr['City'] = $record->City;
            $data_arr['ThumbnailImage'] = $thumbnailImage;
            $isFav = 0;
            $qts = "'";
            if ($record->isFeatured) {
                if ($record->isFeatured->ListingId) {
                    $isFav = 1;
                } else {
                    $isFav = 0;
                }
            } else {
                $isFav = 0;
            }
            if ($isFav) {
                $isFavIcon = '<i id="featuredProp' . $record->ListingId . '" style="font-size: 25px;" class="fas fa-heart featured featuredProp' . $record->ListingId . '" onclick="property_featured(' . $qts . $record->ListingId . $qts . ',' .  $isFav . ')" title="featured"></i>';
            } else {
                $isFavIcon = '<i id="featuredProp' . $record->ListingId . '" style="font-size: 25px;" class="far fa-heart featured   featuredProp' . $record->ListingId . '" onclick="property_featured(' . $qts . $record->ListingId . $qts . ',' .  $isFav   . ')" title="featured"></i>';
            }
            $data_arr['isFav'] = $isFavIcon;
            $data_arr['post'] = '<a href="#" class="w-100"><span class="badge badge-success" title="Post to Zillow">Post</span> </a>';
            // $data_arr['post'] = '<a href="#" ><i class="fa fa-upload" aria-hidden="true" title="Post this property to Zillow"></i> </a>';
            $data_array[] = $data_arr;
            /*$data_arr[] = array(
                'id'=>$record->id,
                'ListOfficeMlsId'=>$record->Ml_num,
                'MlsStatus'=>$record->MlsStatus,
                'UnparsedAddress'=>$record->Addr,
                'ListPrice'=>$record->ListPrice,
                'LotSizeSquareFeet'=>$record->Sqft,
                'ClosePrice'=>$record->Sp_dol,
                'BedroomsTotal'=>$record->BedroomsTotal,
                'BathroomsFull'=>$record->BathroomsFull,
                'PropertyType'=>$record->ClassName,
                'Heating'=>$record->Heating,
                'ThumbnailImage'=>$thumbnailImage,
            );*/
            $srno++;

        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_array
        );
        // dd($response);
        return response($response, 200);
        //        /echo json_encode($response);
        exit;
    }
    public function getPropData(Request $request)
    {

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = 100; // Rows display per page

        //        $columnIndex_arr = $request->get('order');
        //        $columnName_arr = $request->get('columns');
        //        $order_arr = $request->get('order');
        //        $search_arr = $request->get('search');

        //        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        //        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        //        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        //$searchValue = $search_arr['value']; // Search value
        $getdata = $request->all();
        $data['request'] = $getdata;
        $query = \App\Models\RetsPropertyData::select();
        if (isset($getdata['type'])) {
            $type = $getdata['type'];
            $query = $query->where('PropertyType', $type);
        }
        if (isset($getdata['search'])) {
            $search = $getdata['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('ListOfficeMlsId', 'like', '%' . $search . '%')
                    ->orWhere('UnparsedAddress', 'like', '%' . $search . '%')
                    ->orWhere('MlsStatus', 'like', '%' . $search . '%')
                    ->orWhere('ListPrice', 'like', '%' . $search . '%');
            });
        }
        if (isset($getdata['cities'])) {
            $cities = $getdata['cities'];
            $query = $query->whereIn('City', $cities);
        }
        if (isset($getdata['min_price'])) {
            $min_price = (int)$getdata['min_price'];
            $query = $query->where('ListPrice', '>=', $min_price);
        }

        if (isset($getdata['max_price'])) {
            $max_price = (int)$getdata['max_price'];
            $query = $query->where('ListPrice', '<=', $max_price);
        }
        if (isset($getdata['BedroomsTotal'])) {
            $BedroomsTotal = (int)$getdata['BedroomsTotal'];
            $query = $query->where('BedroomsTotal', '>=', $BedroomsTotal);
        }
        if (isset($getdata['BathroomsFull'])) {
            $BathroomsFull = (int)$getdata['BathroomsFull'];
            $query = $query->where('BathroomsFull', '>=', $BathroomsFull);
        }

        // if(isset($getdata['Area_min_size']))
        // {
        //     $Area_min_size = (int)$getdata['Area_min_size'];
        //     $query = $query->where('Sqft','>=',$Area_min_size);
        // }
        // if(isset($getdata['Area_max_size']))
        // {
        //     $Area_max_size = (int)$getdata['Area_max_size'];
        //     $query = $query->where('Sqft','<=',$Area_max_size);
        // }
        if (isset($getdata['PropertySubType'])) {
            $PropertySubType = $getdata['PropertySubType'];
            $query = $query->where('PropertySubType', $PropertySubType);
        }
        if (isset($getdata['feature_filter'])) {
            $feature_filter = $getdata['feature_filter'];
            $propertyId = PropertyFeatures::whereIn('FeaturesId', $feature_filter)->get('propertyId');
            $array = [];
            foreach ($propertyId as $value) {
                $array[] = $value->propertyId;
            }

            $query = $query->whereIn('ListingId', $array);
        }
        $totalRecords = $query->count();
        $totalRecordswithFilter = $query->count();
        //        $query=$query->orderBy($columnName,$columnSortOrder);
        $query = $query->offset($start);
        $query = $query->limit($rowperpage);
        $records = $query->get();
        //        dd($records);

        $data_arr = array();
        $data_array = [];
        foreach ($records as $record) {
            $thumbnailImage = "";

            $image = RetsPropertyDataImage::where("ListingId", $record["ListingId"])->first();
            //            return $image;
            if ($image) {
                if ($image->image_url != null) {
                    //                    $image = json_decode($image->s3_image_url);
                    //                    $image=explode($image->image_url);
                    if ($image != []) {
                        $thumbnailImage = "<img src='" . $image->s3_image_url . "' width='50' class='PropertyModelBtn' data-id='" . $record["ListingId"] . "'/>";
                    } else {
                        $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
                    }
                } else {
                    $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
                }
            } else {
                $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
            }

            $data_arr['id'] = $record->id;
            $data_arr['ListingId'] = $record->ListingId;
            $data_arr['MlsStatus'] = $record->MlsStatus;
            $data_arr['StandardAddress'] = $record->StandardAddress;
            $data_arr['ListPrice'] = intval($record->ListPrice);
            $data_arr['LotSizeSquareFeet'] = $record->Sqft;
            //            $data_arr['Sp_dol'] = $record->Sp_dol;
            $data_arr['BedroomsTotal'] = intval($record->BedroomsTotal);
            $data_arr['BathroomsFull'] = $record->BathroomsFull;
            $data_arr['PropertyType'] = $record->PropertyType;
            $data_arr['City'] = $record->City;
            $data_arr['ThumbnailImage'] = $thumbnailImage;
            $data_array[] = $data_arr;
            /*$data_arr[] = array(
                'id'=>$record->id,
                'ListOfficeMlsId'=>$record->Ml_num,
                'MlsStatus'=>$record->MlsStatus,
                'UnparsedAddress'=>$record->Addr,
                'ListPrice'=>$record->ListPrice,
                'LotSizeSquareFeet'=>$record->Sqft,
                'ClosePrice'=>$record->Sp_dol,
                'BedroomsTotal'=>$record->BedroomsTotal,
                'BathroomsFull'=>$record->BathroomsFull,
                'PropertyType'=>$record->ClassName,
                'Heating'=>$record->Heating,
                'ThumbnailImage'=>$thumbnailImage,
            );*/
        }
        $data['data'] = $data_array;
        $data['success'] = true;
        $data['count'] = $totalRecordswithFilter;
        return $data;
        //        $response = array(
        //            "draw" => intval($draw),
        //            "iTotalRecords" => $totalRecords,
        //            "iTotalDisplayRecords" => $totalRecordswithFilter,
        //            "aaData" => $data_array
        //        );
        //        return response($response,200);
        //        /echo json_encode($response);
        exit;
    }
    public function SliderImg(Request $request)
    {
        $indicators = '';
        $inner = "";
        $images = [];
        $image = RetsPropertyDataImage::where("ListingId", $request->id)->get(['s3_image_url']);
        //            return $image;
        if ($image) {
            foreach ($image as $k) {
                $images[] = $k->s3_image_url;
            }
        }
        //            if ($image->image_url != null) {
        ////                    $image = json_decode($image->s3_image_url);
        ////                    $image=explode($image->image_url);
        //                if ($image != []) {
        //                    $thumbnailImage = "<img src='" . $image->image_url. "' width='50' class='PropertyModelBtn' data-id='".$record["Ml_num"]."'/>";
        //                } else {
        //                    $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
        //                }
        //            } else {
        //                $thumbnailImage = "<img src='" . url('assets/agent/images/no-imag.jpg') . "' width='50' />";
        //            }
        if (isset($images) && !empty($images)) {
            $i = 0;
            //            $images=explode(',',$query->ImagesUrls);
            foreach ($images as $img) {
                $indicators .= '<li data-target="#carouselExampleIndicators" data-slide-to="' . $i . '" class="active" ></li>';
                $inner .= "<div class='carousel-item";
                if ($i == 0) {
                    $inner .= " active";
                }
                $inner .= "'><img class='img-size' src='/storage" . $img . "' alt='First slide' /></div>";
                $i++;
            }
            $data['inner'] = $inner;
            $data['indicators'] = $indicators;
            return $data;
        } else {
            $data['inner'] = $inner;
            $data['indicators'] = $indicators;
            return $data;
        }
    }




    // Additional Listing
    public function AdditionaListing(Request $request)
    {
        $agentidget = $request->agentid;
        $apikey = $request->apikey;
        $leadid = $request->mlsidget;
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $leadid = intval($leadid);
                    $lead_data = $this->LeadsModel::where('id', $leadid)->first();
                    // $this->db->select("*");
                    // $this->db->where('id', $leadid);
                    // $this->db->from('Leads');
                    // $query = $this->db->get();
                    // $lead_data = $query->row_array();
                    $leadata2get = "" . $lead_data->AdditionalPropertiesText . "";
                    $check = '';
                    // return $leadata2get;
                    $addprop_listingid = '';
                    if (isset($leadata2get) && !empty($leadata2get)) {
                        $getallmls = (array)json_decode($leadata2get);
                        $all_add_mls = array();
                        foreach ($getallmls as $adkey => $admls) {
                            $admls = (array)$admls;
                            if (is_array($admls) && count($admls) > 0) {
                                foreach ($admls as $keyn => $valn) {
                                    $all_add_mls[] = $valn;
                                }
                            }
                        }
                        // return $all_add_mls;
                        // $nm = implode("','", $all_add_mls);
                        if (!empty($all_add_mls) && $all_add_mls != "") {
                            // return $all_add_mls;
                            $resulnumrows = $this->retsPropertyData::whereIn('ListingId', $all_add_mls)->first(['ListingId', 'ListAgentFullName', 'Furnished', 'SubdivisionName', 'BuildingName', 'PrivateRemarks', 'ListAgentDirectPhone', 'ListAgentEmail', 'BedroomsTotal', 'BathroomsTotalInteger', 'ListPrice', 'YearBuilt',  'PublicRemarks', 'ImagesUrls']);

                            $resulnumrows_purge = $this->retsPropertyDataPurged::whereIn('ListingId', $all_add_mls)->first(['ListingId', 'ListAgentFullName', 'Furnished', 'SubdivisionName', 'BuildingName', 'PrivateRemarks', 'ListAgentDirectPhone', 'ListAgentEmail', 'BedroomsTotal', 'BathroomsTotalInteger', 'ListPrice', 'YearBuilt',  'PublicRemarks', 'ImagesUrls']);
                            // return $resulnumrows_purge;
                            // $query20 = "SELECT ListingId, ListAgentFullName,CustomAddress,Furnished,SubdivisionName,BuildingName,ShowingRequirements,PrivateRemarks, ListAgentDirectPhone, ListAgentEmail, BedroomsTotal, BathroomsTotalInteger, ListPrice, YearBuilt,  PublicRemarks, images_urls FROM rets_property_data where ListingId in('$nm')";
                            // $resulnumrows = $this->db->query($query20);
                            // $query_purge = "SELECT ListingId, ListAgentFullName,CustomAddress,Furnished,SubdivisionName,BuildingName,ShowingRequirements,PrivateRemarks, ListAgentDirectPhone, ListAgentEmail, BedroomsTotal, BathroomsTotalInteger, ListPrice, YearBuilt,  PublicRemarks, images_urls FROM rets_purged_data where ListingId in('$nm')";
                            // $resulnumrows_purge = $this->db->query($query_purge);
                            if ($resulnumrows) {
                                if ($resulnumrows) {
                                    $result_addeach = $resulnumrows;
                                }
                                if ($resulnumrows_purge) {
                                    $result_addeach = $resulnumrows_purge;
                                }
                                //$result_addeach = array_merge($result_addeach1,$result_addeach2);
                                $html = '';
                                $arr = [];
                                // $addprop_listingid .= '<div class="addition_property_class_highlight" style="margin-bottom:1px;box-shadow:unset;"><center><b>Additional Properties : </b></center></div>';
                                $getadd_proprties_res = $result_addeach;
                                // return $result_addeach;
                                $arr = [];
                                // $address = $getadd_proprties_res->CustomAddress;
                                $listingId = $getadd_proprties_res->ListingId;
                                $publicRemarks = $getadd_proprties_res->PublicRemarks;
                                $agentname = $getadd_proprties_res->ListAgentFullName;
                                $phone = $getadd_proprties_res->ListAgentDirectPhone;
                                $email = $getadd_proprties_res->ListAgentEmail;
                                $showinginstruction = $getadd_proprties_res->ShowingRequirements;
                                $privateremark =  $getadd_proprties_res->PrivateRemarks;
                                $bathroom = $getadd_proprties_res->BathroomsTotalInteger;
                                $bedroom = $getadd_proprties_res->BedroomsTotal;
                                $price = $getadd_proprties_res->ListPrice;
                                $buildyear = $getadd_proprties_res->YearBuilt;
                                $furnished = $getadd_proprties_res->Furnished;
                                if ($getadd_proprties_res->BuildingName != '' && $getadd_proprties_res->BuildingName != null) {
                                    $SubdivisionName = $getadd_proprties_res->BuildingName;
                                } else {
                                    $SubdivisionName = $getadd_proprties_res->SubdivisionName;
                                }
                                $getadd_prop11 = $getadd_proprties_res->images_urls;
                                $image_parts = explode(",", $getadd_prop11);

                                $image_url = 'https://brokerlinx.com/crm/uploads/blinx%20concept_v2.jpg';
                                $images = '<img class="preload-image" style="max-height:60px;" src="' . $image_url . '" data-src="' . $image_url . '" alt="img">';
                                $arr['images'] = $image_url;
                                if (isset($image_parts[0]) && !empty($image_parts[0]) && $image_parts[0] != '') {
                                    $images = '<span class="productslider" data-toggle="modal" data-target="#myModal2" slider-id="' . $listingId . '"><img src="' . $image_parts[0] . '" alt="" height="100px" style="max-width:96%;" /></span>';
                                    $arr['images'] = $image_parts[0];
                                }
                                $icon_class1 = '<svg  viewBox="0 0 32 32" aria-label="expand row" style="float:right;" class=" Icon-sc-13llmml-0 fatDfl sc-1b8bq6y-1 gcKrid " rel="' . $listingId . '" aria-hidden="true" focusable="false" role="img"><title>Chevron Down</title><path stroke="none" d="M29.41 8.59a2 2 0 00-2.83 0L16 19.17 5.41 8.59a2 2 0 00-2.83 2.83l12 12a2 2 0 002.82 0l12-12a2 2 0 00.01-2.83z"></path></svg>';
                                $icon_class2 = '<svg  viewBox="0 0 32 32" aria-label="collapse row" style="float:right;" class="  Icon-sc-13llmml-0 fatDfl IconChevronUp-sc-3309cs-0 sc-1b8bq6y-0 cGeqHO" rel="' . $listingId . '" role="img"><title>Chevron Up</title><path stroke="none" d="M29.41 8.59a2 2 0 00-2.83 0L16 19.17 5.41 8.59a2 2 0 00-2.83 2.83l12 12a2 2 0 002.82 0l12-12a2 2 0 00.01-2.83z"></path></svg>';


                                $phone_parse_arr = array(
                                    '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i',
                                    '(\d{3}\s\d{3}\s\d{4})',
                                    '(\d{3}\.\d{3}\.\d{4})',
                                    '(\d{3}\-\d{3}\-\d{4})',
                                    '(\d{3}\s\d{3}-\d{4})',
                                    '(\(\d{3}\)\s\d{3}-\d{4})',
                                    '(\(\d{3}\)\s\d{3}-\d{4})',
                                    '(\d{10,11}\s*)',
                                    '(\(\d{3}\)%20\d{3}-\d{4})'
                                );
                                if (isset($privateremark) && $privateremark != '') {
                                    $privateremark_text = $privateremark;
                                    $all_phones = array();
                                    foreach ($phone_parse_arr as $key_prse => $prsevalue) {
                                        preg_match_all('' . $prsevalue . '', $privateremark_text, $phone_no);
                                        if (isset($phone_no[0]) && !empty($phone_no[0])) {
                                            foreach ($phone_no[0] as $each_phone) {
                                                $all_phones[] = $each_phone;
                                            }
                                        }
                                    }
                                    if (!empty($all_phones) && count($all_phones) > 0) {
                                        foreach ($all_phones as $each_phone) {
                                            $new_text = '<a class="anchor_links"  style="display:inline;" href="tel:' . $each_phone . '">' . $each_phone . '</a>';
                                            $privateremark_text = str_ireplace($each_phone, $new_text, $privateremark_text);
                                        }
                                    }
                                    $privateremark = $privateremark_text;
                                }

                                // $arr['address'] = $address;
                                $arr['agentname'] = $agentname;
                                $arr['phone'] = $phone;
                                $arr['listingId'] = $listingId;
                                $arr['email'] = $email;
                                $arr['price'] = $price;
                                $arr['bedroom'] = $bedroom;
                                $arr['bathroom'] = $bathroom;
                                $arr['SubdivisionName'] = $SubdivisionName;
                                $arr['buildyear'] = $buildyear;
                                $arr['furnished'] = $furnished;
                                $arr['publicRemarks'] = $publicRemarks;
                                // $arr['showinginstruction'] = $showinginstruction;
                                $arr['privateremark'] = $privateremark;
                                $data['additionallisting'][] = $arr;

                                // $addprop_listingid .= $html ;
                            }
                        }
                    }
                    // return $data;
                    if (!isset($data['additionallisting'])) {
                        $data['additionallisting'] = '';
                    }
                    // $check = '<div>' . $addprop_listingid . '</div>';
                    // $data['varr2'] = $check;
                    $data['success'] = true;
                    $data['is_login'] = true;
                } else {
                    $data = array('success' => false, 'msg' => 'Please pass correct api key.');
                }

            } else {
                $data = array('success' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }
    // All Zip Codes
    public function AllZipCodes(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey) ? $request->apikey : '';
        // return $request;
        $ct_text = "";
        if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
            $city    = isset($request->city) ? $request->city : '';
            $county    = isset($request->county) ? $request->county : '';
            $state    = isset($request->state) ? $request->state : '';
            $ct_text = "";
            $var = "city";
            if ($city != '') {
                $city = rtrim($city, ',');
                $city = ltrim($city, ',');
                $all_cities = explode(',', $city);
                $ct_text = $all_cities;
                // $ct_text = "'".$ct_text."'";
                $var = "city";
            } elseif ($county != '') {
                $county = rtrim($county, ',');
                $county = ltrim($county, ',');
                $all_county = explode(',', $county);
                $ct_text = $all_county;
                // $ct_text = "'".$ct_text."'";
                $var = "county";
            } elseif ($state != '') {
                $state = rtrim($state, ',');
                $state = ltrim($state, ',');
                $all_state = explode(',', $state);
                $ct_text = $all_state;
                // $ct_text = "'".$ct_text."'";
                $var = "st_abb";
            }
            if ($ct_text != "") {

                $result = $this->PostalMasterModel::whereIn($var, $ct_text)->distinct('zipcode')->get(['zipcode']);
                $data['zipcodes'] = array();
                if ($result) {
                    foreach ($result as $zip) {
                        $data['zipcodes'][] = $zip->zipcode;
                    }
                }
                $data['success'] = true;
            } else {
                $result = $this->PostalMasterModel::distinct('zipcode')->get(['zipcode']);
                $data['zipcodes'] = array();
                if ($result) {
                    foreach ($result as $zip) {
                        $data['zipcodes'][] = $zip->zipcode;
                    }
                }
                $data['success'] = true;
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct api key.');
        }
        return $data;
    }
    // All Cities
    public function AllCities(Request $request)
    {
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey) ? $request->apikey : '';
        // return $request;
        $ct_text = array();
        if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
            $county    = isset($request->county) ? $request->county : '';
            // $state_cond = '';
            if ($county != '') {
                $county = rtrim($county, ',');
                $county = ltrim($county, ',');
                $all_county = explode(',', $county);
                $ct_text = $all_county;
                // $ct_text = implode("','",$all_county);
                // $ct_text = "'".$ct_text."'";
                // //$state_cond .= ' and state="'.$state.'" ' ;
                // $state_cond.=' and county in('.$ct_text.')';
            }
            if ($county != '') {
                $result = $this->PostalMasterModel::whereIn('county', $ct_text)->distinct('city')->get(['city']);
                // $query = "SELECT distinct(city) as city FROM `postal_master` where 1=1 $state_cond";
                // $result = $this->db->query( $query )->result_array();
                $data['cities'] = array();
                if ($result) {
                    foreach ($result as $zip) {
                        $data['cities'][] = $zip->city;
                    }
                }
                // $data['data'] = $result;
                $data['success'] = true;
            } else {
                $data = array('success' => false, 'msg' => 'Please select a county first.');
            }
        } else {
            $data = array('success' => false, 'msg' => 'Please pass correct api key.');
        }
        return $data;
    }
    // Get info by mls id
    public function GetInfoBymlsid(Request $request)
    {
        $mls_id    = isset($request->mlsid) ? $request->mlsid : '';
        $loginagentid    = isset($request->loginagentid) ? $request->loginagentid : '';
        $flag    = isset($request->flag) ? $request->flag : '';
        $ApiKeyData = env('ApiKey');
        $apikey    = isset($request->apikey) ? $request->apikey : '';
        if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
            $data = array();
            $data = GetInfoBymlsid($request);
        } else {
            $data = array('success' => false, 'is_login' => false, 'msg' => 'Please pass correct api key.');
        }
        return $data;
    }
    //
    public function getOwnerDetailsFromDadePropertytbl($address, $addressful, $unit)
    {
        // echo "address".$address."<br>";
        // echo "addressful".$addressful."<br>";
        if ($unit == "yes") {
            $address1 = trim(str_replace('#', '', $address));
            // echo $address1."<br>";
            //$sql = DadePropertiesMaster::where('TrueSiteAddr', $address)->orWhere('TrueSiteAddr', $address1)->first();
            // $sql = "select * from dade_properties_master where  TRUE_SITE_ADDR = '$address' or TRUE_SITE_ADDR = '$address1'";
            // echo  $sql;
            $atomdata['Address1Query'] =  $sql;
        } else {
            $address = trim(str_replace('#', '', $address));
            //$sql = DadePropertiesMaster::where('TrueSiteAddr', $address)->first();
            // $sql = "select * from dade_properties_master where  TRUE_SITE_ADDR = '$address'";
            $atomdata['Address1Query'] =  $sql;
        }
        $query = $sql;
        $arr = array();
        if ($query) {
            $res = $query;
            $OwnerName = "";
            if (isset($res->TrueOwner1) && !empty($res->TrueOwner1)) {
                $OwnerName = $res->TrueOwner1;
            } else {
                $OwnerName = "";
            }
            if (isset($res->TrueOwner2) && !empty($res->TrueOwner2)) {
                if ($OwnerName != "" && !empty($OwnerName)) {
                    $OwnerName = $OwnerName . "," . $res->TrueOwner2;
                } else {
                    $OwnerName = "";
                }
            }
            if (isset($res->TrueOwner3) && !empty($res->TrueOwner3)) {
                if ($OwnerName != "" && !empty($OwnerName)) {
                    $OwnerName = $OwnerName . "," . $res->TrueOwner3;
                } else {
                    $OwnerName = "";
                }
            }
            $arr['OwnerName'] = $OwnerName;
            if (isset($res->YearBuilt) && !empty($res->YearBuilt)) {
                $arr['YearBuilt'] = $res->YearBuilt;
            } else {
                $arr['YearBuilt'] = "";
            }
            if (isset($res->Legal) && !empty($res->Legal)) {
                $arr['legalDescription'] = $res->Legal;
            } else {
                $arr['legalDescription'] = "";
            }
            if (isset($res->SubdivisionName) && !empty($res->SubdivisionName)) {
                $arr['subDivision'] = $res->SubdivisionName;
            } else {
                $arr['subDivision'] = "";
            }
            if (isset($res->Folio) && !empty($res->Folio)) {
                $arr['apnNumber'] = $res->Folio;
            } else {
                $arr['apnNumber'] = "";
            }
            if (isset($res->Folio) && !empty($res->Folio)) {
                $arr['apnNumber'] = $res->Folio;
            } else {
                $arr['apnNumber'] = "";
            }
            if (isset($res->TrueSiteAddr) && !empty($res->TrueSiteAddr)) {
                $arr['Address'] = $res->TrueSiteAddr;
            } else {
                $arr['Address'] = "";
            }
            $arr['atom_message'] = "success";
            // echo "<pre>";
            // print_r($arr);
            $atomdata['PropertyTblResponse'] = json_encode($arr);
            // $this->db->insert('atom_address_missing',$atomdata);

            // AtomAddressMissingSql::Create($atomdata);
            return $arr;
        }
        $arr = array('OwnerName' => "", 'YearBuilt' => "", 'legalDescription' => "", 'subDivision' => "", 'apnNumber' => "", 'atom_message' => "Sorry but we dont have that address  available for Auto Fill Owner Details - We will look into adding this address - Please Fill in the info manually", 'Address' => "", 'success' => false);
        $atomdata['PropertyTblResponse'] = json_encode($arr);
        //AtomAddressMissingSql::Create($atomdata);
        // $this->db->insert('atom_address_missing',$atomdata);
        return $arr;
    }

    public function getOwners($tblname, $property_row)
    {
        $tblname = $tblname;
        $property_row = $property_row;
        $id = $property_row['id'];
        $street = $property_row['StreetNumber'] . ' ' . $property_row['StreetDirPrefix'] . ' ' . $property_row['StreetName'] . ' ' . $property_row['StreetSuffix'];
        $state = $property_row['StateOrProvince'];
        $zipcode = $property_row['PostalCode'];
        $parcelNumber = str_ireplace('-', '', $property_row['ParcelNumber']);
        $unit = $property_row['UnitNumber'];
        $county = $property_row['County'];
        $city = $property_row['City'];
        $CustomPropertyType = $property_row['CustomPropertyType'];
        $PropertySubType = $property_row['PropertySubType'];
        if ($CustomPropertyType == "RLSE" && $PropertySubType == "Multi Family") {
            $StandardPropType = "Apartment";
        } else {
            $StandardPropType = $property_row['StandardPropType'];
        }


        $OwnerName = "";
        $legalDescription = "";
        $subdivisonName = "";
        $apnNumber = "";
        $flag = "yes";

        $mls_id = $property_row['ListingId'];
        if ($property_row['OwnerName'] == "" && empty($property_row['OwnerName']) && $property_row['OwnerName'] == null) {
            $responseData = $this->getOwnerDetailsByAdress($street, $unit, $city, $state, $zipcode, $mls_id, $StandardPropType, $flag);
            $getData = $responseData['json_data'];
            if ($getData != "" && !empty($getData) && $getData != null) {
                if (isset($getData['Response'])) {
                    $getData =  $getData['Response'];
                }
                $status = $getData['status'];
                if ($status['code'] != 0 && $StandardPropType == "Apartment") {
                    $responseData = $this->getOwnerDetailsByAdress($street, $unit, $city, $state, $zipcode, $mls_id, $StandardPropType, $flag = "no");
                }
            }
            $get_all_data = $responseData['json_data'];
            if ($get_all_data != "" && !empty($get_all_data) && $get_all_data != null) {
                if (isset($get_all_data['Response'])) {
                    $get_all_data =  $get_all_data['Response'];
                }
                $status = $get_all_data['status'];
                if ($status['code'] == 0) {
                    $property = $get_all_data['property'];
                    $propertyDetail = $property[0];
                    $identifierArray = $propertyDetail['identifier'];
                    $assessmentArray = $propertyDetail['assessment'];
                    $areaArray = $propertyDetail['area'];
                    $summaryArray  = $propertyDetail['summary'];
                    if (array_key_exists("legal1", $summaryArray)) {
                        $legalDescription = $summaryArray['legal1'];
                    } else {
                        $legalDescription = "";
                    }
                    if (array_key_exists("yearBuilt", $summaryArray)) {
                        $yearBuilt = $summaryArray['yearBuilt'];
                    } else {
                        $yearBuilt = "";
                    }
                    if (array_key_exists("subdName", $areaArray)) {
                        $subdivisonName = $areaArray['subdName'];
                    } else {
                        $subdivisonName = "";
                    }
                    if (array_key_exists("apn", $identifierArray)) {
                        $apnNumber = $identifierArray['apn'];
                    } else {
                        $apnNumber = $parcelNumber;
                    }


                    $AllOwners = $assessmentArray['owner'];
                    $OwnerName = "";
                    for ($i = 1; $i < 5; $i++) {

                        if (array_key_exists("owner$i", $assessmentArray['owner'])) {
                            $Owner = $AllOwners['owner' . $i];

                            if ($Owner != "" && !empty($Owner) && $Owner != null) {
                                if (array_key_exists("firstNameAndMi", $Owner)) {
                                    $firstName = str_ireplace(",", " ", $Owner['firstNameAndMi']);
                                } else {
                                    $firstName = "";
                                }
                                if (array_key_exists("lastName", $Owner)) {
                                    $lastName = " " . str_ireplace(",", " ", $Owner['lastName']);
                                } else {
                                    $lastName = "";
                                }
                                $OwnerName .= $firstName . $lastName . ",";
                            }
                        }
                    }
                } elseif ($status['code'] != 0) {
                    $AtomApiDetail = array('OwnerName' => $property_row['OwnerName'], 'YearBuilt' => $property_row['YearBuilt'], 'legalDescription' => $property_row['legalDescription'], 'subDivision' => $subdivisonName, 'apnNumber' => $apnNumber, 'atom_message' => "Sorry but we dont have that address  available for Auto Fill Owner Details - We will look into adding this address - Please Fill in the info manually");
                    return $AtomApiDetail;
                } else {
                    $AtomApiDetail = array('OwnerName' => $property_row['OwnerName'], 'YearBuilt' => $property_row['YearBuilt'], 'legalDescription' => $property_row['legalDescription'], 'subDivision' => $subdivisonName, 'apnNumber' => $apnNumber, "atom_message" => "success");
                    return $AtomApiDetail;
                }
            }
            $OwnerName = isset($OwnerName) ? $OwnerName : '';
            if ($OwnerName != '') {
                $OwnerName = preg_replace('/\,+/', ',', $OwnerName);
            }
            // $response['OwnerName'] = isset($OwnerName)?$OwnerName:'';
            if ($property_row['YearBuilt'] != "" &&  $property_row['YearBuilt'] != null &&  $property_row['YearBuilt'] != 0 &&  $property_row['YearBuilt'] != '0') {
                $yearBuilt = $property_row['YearBuilt'];
            }
            $this->addApnDataInRpd($id, $tblname, $OwnerName, $yearBuilt, $legalDescription, $apnNumber, json_encode(array("response" => $responseData)));
            $AtomApiDetail = array('OwnerName' => $OwnerName, 'YearBuilt' => $yearBuilt, 'legalDescription' => $legalDescription, 'subDivision' => $subdivisonName, 'apnNumber' => $apnNumber, "atom_message" => "success");
            return $AtomApiDetail;
        } else {
            $AtomApiDetail = array('OwnerName' => $property_row['OwnerName'], 'YearBuilt' => $property_row['YearBuilt'], 'legalDescription' => $property_row['legalDescription'], 'subDivision' => $subdivisonName, 'apnNumber' => $apnNumber, "atom_message" => "success");
            return $AtomApiDetail;
        }
    }

    public function getOwnerDetailsByAdress($street, $unit, $city, $state, $zipcode, $mls_id, $StandardPropType, $flag)
    {
        $tbl_street = $street;
        $street = preg_replace('/\s+/', ' ', $street);
        $tbl_unit = $unit;
        $tbl_city = $city;
        $tbl_state = $state;
        $tbl_zipcode = $zipcode;
        $tbl_mls_id = $mls_id;
        $address1 = "";
        $address2 = "";
        $street = preg_replace('/ /', '%20', $street);
        $unit = preg_replace('/ /', '%20', $unit);
        $city = preg_replace('/ /', '%20', trim($city));
        if ($unit != '' && !empty($unit) && $unit != null) {
            if ($StandardPropType != "Apartment") {
                $unit = "%23" . $unit . "%20";
                $address1 = $street . $unit;
            } else {
                if ($StandardPropType == "Apartment" && $flag == "yes") {
                    $unit = "%23" . $unit; //."%20";
                    $address1 = $street . $unit;
                } else if ($StandardPropType == "Apartment" && $flag == "no") {
                    $unit = $unit . "%20";
                    log_message('error', 'AtomApiUrl : ' . $flag);
                    $address1 = rtrim($street, '%20'); //.$unit;
                }
            }
        } else {
            $address1 = $street;
        }
        $address2 = $city . "%2C%20" . $state;
        //echo $address1."add".$address2;
        // $base_url =$this->config->base_url();
        // $curl_url = $base_url."uploads/contracts/json/atom.json";
        $curl_url = 'https://api.gateway.attomdata.com/propertyapi/v1.0.0/property/expandedprofile?address1=' . $address1 . '&address2=' . $address2;
        //log_message('error', 'AtomApiUrl : '.$curl_url);
        // $curl_url ='https://api.gateway.attomdata.com/propertyapi/v1.0.0/property/expandedprofile?address1=800%20west%20ave%20%23537&address2=Miami%20Beach%2C%20Fl';
        $get_all_data = $this->getDataByApn($curl_url);
        $created_at = date('Y-m-d H:i:s');
        $data = array(
            "mls_id" => $tbl_mls_id,
            "street" => $tbl_street,
            "unit"   => $tbl_unit,
            "city"   => $tbl_city,
            "state" =>  $tbl_state,
            "zipcode" => $tbl_zipcode,
            "Address1Query" => $address1,
            "address2_query" => $address2,
            "url"     =>  $curl_url,
            "atom_response" => json_encode($get_all_data),
            "inserted_time" => $created_at,
        );
        $response =  $get_all_data['json_data'];
        if (isset($response['Response'])) {
            $response =  $response['Response'];
        }
        if ($response != "" && !empty($response) && $response != null) {
            if (isset($response['status'])) {
                $status = $response['status'];
                if ($status['code'] != 0) {
                    if ($data != "") {
                        // $this->db->insert('atom_address_missing',$data);
                       // AtomAddressMissingSql::Create($data);
                    }
                }
            }
        }
        //  print_r($get_all_data);
        return $get_all_data;
    }
    private function getDataByApn($curr_url = '')
    {
        $url = $curr_url;
        $curl = curl_init();
        $header = array(
            'Accept: application/json',
            'apikey:42fb0fad7d4b5eacca22f31070c0055a'
        );

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $return['status'] = false;
        } else {
            $return['status'] = true;
        }
        $json_data = json_decode($response, true);
        $return['json_data'] = $json_data;
        return $return;
    }
    public function getMatchingMlsAndAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "county" => "required",
            "CustomPropertyType" => "required",
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, self::VALIDATION_ERROR_HTTP_RESPONSE_STATUS);
        }
        $ApiKeyData = env('ApiKey');
        if (isset($request->token) && !empty($request->token)) {
            $token = json_decode(base64_decode($request->token));
            if ($token->login_user_id) {
                $apikey    = isset($request->apikey) ? $request->apikey : '';
                if (isset($apikey) && !empty($apikey) && $apikey == $ApiKeyData) {
                    $listingId          = $request->listingId;
                    $address        = $request->address;
                    $county          = $request->county;
                    $CustomPropertyType              = $request->CustomPropertyType;
                    $agentId              = $request->agentId;

                    $query = Pages::where('County', $county)->where('CustomPropertyType', $CustomPropertyType);
                    //                      return $listingId;
                    if ($request->has('listingId')) {
                        $query = $query->where('listingId', 'like', '%' . $listingId . '%');
                    } elseif ($request->has('address')) {
                        $query = $query->where('Address', 'like', '%' . $address . '%');
                    }
                    $data['PropAddress'] = $query->get();
                    $data['success'] = true;
                    $data['isLogin'] = true;
                } else {
                    $data = array('success' => false, 'isLogin' => true, 'msg' => 'Please pass correct api key.');
                }
            } else {
                $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
            }
        } else {
            $data = array('success' => false, 'isLogin' => false, 'msg' => 'Please pass correct token.');
        }
        return $data;
    }

    public function BulkImport()
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Bulk Import";
        //        $base_url=base_path('assets/');
        $import_folder = public_path("import_files/");
        //        $import_folder=url("assets/import_files");
        $import_folder = "import_files/";
        $data['folder_names'] = $this->GetDire($import_folder);
        //        $page_data['sub']=$files = scandir($import_folder);
        //        return $page_data;
        return view('agent.property.BulkImport', $data);
    }

    function GetDire($dir)
    {
        if (is_dir($dir)) {
            if ($mkdir = opendir($dir)) {
                while ($file = readdir($mkdir)) {
                    if ($file != '.' && $file != '..') {
                        if (is_dir($dir . $file)) {
                            $this->folder_name[] =  trim(str_ireplace("./", "", $dir . $file));
                            //echo $dir . $file."<br>";
                            // since it is a directory we recurse it.
                            $this->GetDire($dir . $file . '/');
                        } else {
                            //echo $dir . $file;
                        }
                    }
                }
            }
            closedir($mkdir);
        }
        return $this->folder_name;
    }
    public function BulkImportFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source' => 'required',
            'file' => 'required|max:50000|mimes:xlsx,csv,xls'
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, 422);
        }
        $source = $request->source;
        $upload_path = public_path($source);
        $image = time() . '.' . $request->file->extension();
        $path = $request->file->move($upload_path, $image);
        return response()->json([
            'success' => true,
            'data' => $request->all(),
            'message' => 'File Uploaded SuccessFully',
            'path' => $upload_path . '/' . $image,
        ]);
    }

    public function ImportProperty(Request $request)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Property Import";
        return view('agent.property.PropertyImport', $data);
    }
    public function import_data_back(Request $request)
    {
        $data = [];
        $form_data = $request->all();
        // Get uploaded CSV file
        $file = $request->file('file');
        $fileName = $_FILES["file"]["tmp_name"];
        $file = fopen($fileName, "r");
        $count = 0;
        $check_index = 0;
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($count == 0) {
                $count++;
                continue;
            }
            //CREATE PROJECT
            $pr = [];
            //            $pr["updated_by_admin"]=1;
            $pr["BathroomsFull"] = $column[0];
            $pr["BedroomsTotal"] = $column[1];
            $pr["Status"] = $column[2];
            $pr["ListingId"] = $column[3];
            $pr["St_dir"] = $column[4];
            $pr["City"] = $column[5];
            $pr["PostalCode"] = $column[6];
            $pr["County"] = $column[7];
            $pr["Furnished"] = $column[8];
            $pr["Latitude"] = $column[9];
            $pr["Longitude"] = $column[10];
            $pr["ListPrice"] = $column[11];
            $pr["Sqft"] = $column[12];
            $pr["Park_spcs"] = $column[13];
            $pr["Pool"] = $column[14];
            $pr["Ad_text"] = $column[15];
            $pr["Ad_text"] = $column[16];
            $pr["Type_own1_out"] = $column[17];
            $pr["PropertyType"] = $column[18];
            $pr["Addr"] = $column[19];
            $pr["Extras"] = $column[20];
            $pr["Water_inc"] = $column[21];
            $pr["As_year"] = $column[22];
            $pr["image_downloaded"] = $column[23];
            if (isset($pr["ListingId"]) && !empty($pr["ListingId"]) && $pr["ListingId"] != '') {
                $pr["property_last_updated"] = date("Y-m-d H:i:s");
                $pr['upd'] = \App\Models\RetsPropertyData::where("ListingId", $pr["ListingId"])->update($pr);
                $data["upd"][] = $pr;
            } else {
                //dd(collect(\App\Models\RetsPropertyData::where("id",1))->values());
                $pr["property_insert_time"] = date("Y-m-d H:i:s");
                $pr['ins'] = \App\Models\RetsPropertyData::firstOrCreate($pr);

                dd(1);
                //RetsPropertyDataSql::create($pr);
                $data["add"][] = $pr;
                //                return $ins;
            }
            //            $wings_id = RetsPropertyData::firstOrCreate($wi);
            //            $wings_id = $wings_id["id"];
        }
        if (isset($data["upd"]) || isset($data["add"])) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Data imported SuccessFully',
                'request' => $request->all(),
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => 'Data imported SuccessFully',
            'request' => $request->all(),
        ]);
    }
    public function import_data(Request $request)
    {
        $data = [];
        $total = 0;
        $update = 0;
        $create = 0;
        $total = $this->retsPropertyData::count();
        $form_data = $request->all();
        $file = $request->file('file');
        $fileName = $_FILES["file"]["tmp_name"];
        $file = fopen($fileName, "r");
        $count = 0;
        $check_index = 0;
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            if ($count == 0) {
                $count++;
                continue;
            }
            $pr = [];
            $pr["UpdatedByAdmin"] = 1;
            $pr["BathroomsFull"] = $column[0];
            $pr["BedroomsTotal"] = $column[1];
            $pr["Status"] = $column[2];
            $pr["ListingId"] = $column[3];
            $pr["StreetDirPrefix"] = $column[4];
            $pr["City"] = $column[5];
            $pr["PostalCode"] = $column[6];
            $pr["County"] = $column[7];
            $pr["Furnished"] = $column[8];
            $pr["Latitude"] = $column[9];
            $pr["Longitude"] = $column[10];
            $pr["ListPrice"] = $column[11];
            $pr["Sqft"] = $column[12];
            $pr["Park_spcs"] = $column[13];
            $pr["Pool"] = $column[14];
            //            $pr["Ad_text"] = $column[15];
            $pr["PublicRemarks"] = $column[16];
            $pr["PropertySubType"] = $column[17];
            $pr["PropertyType"] = $column[18];
            $pr["StandardAddress"] = $column[19];
            $pr["Extras"] = $column[20];
            if (isset($pr["ListingId"]) && !empty($pr["ListingId"]) && $pr["ListingId"] != '') {
                $check = \App\Models\RetsPropertyData::where("ListingId", $pr["ListingId"])->count();
                if (isset($check) && !empty($check) && intval($check) > 0) {
                    $pr["updated_time"] = date("Y-m-d H:i:s");
                    $pr['upd'] = \App\Models\RetsPropertyData::where("ListingId", $pr["ListingId"])->update($pr);
                    $data["upd"][] = $pr;
                    $update++;
                } else {
                    $pr["inserted_time"] = date("Y-m-d H:i:s");
                    $pr['ins'] = \App\Models\RetsPropertyData::firstOrCreate($pr);
                    $data["add"][] = $pr;
                    $create++;
                }
            } else {
                $pr["inserted_time"] = date("Y-m-d H:i:s");
                $pr['ins'] = \App\Models\RetsPropertyData::firstOrCreate($pr);
                $data["add"][] = $pr;
                $create++;
            }
        }
        $message = "";
        if (isset($create) && $create > 0) {
            $message .= $create . " Property Created ";
        }
        if (isset($update) && isset($create) && $create > 0 && $update > 0) {
            $message .= " and ";
        }
        if (isset($update) && $update > 0) {
            $message .= $update . " Property Updated ";
        }
        $message .= " Successfully. ";
        if (isset($data["upd"]) || isset($data["add"])) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $message,
                'request' => $request->all(),
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => 'Data imported SuccessFully',
            'request' => $request->all(),
        ]);
    }
    public function downloadfile()
    {
        $filepath = public_path('assets/csv/propertycsv.csv');
        return Response::download($filepath);
    }
    public function importZip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|max:5000000|mimes:zip'
        ]);
        if ($validator->fails()) {
            $response["errors"] = $validator->errors();
            return response($response, 422);
        }
        $message = "";
        $source = $request->source;
        $upload_path = public_path('/assets/property');
        $image = $request->file->getClientOriginalName();
        $filename = pathinfo($image, PATHINFO_FILENAME);
        $path = $request->file->move($upload_path, $image);
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $extractpath = $upload_path;
            $zip->extractTo($extractpath);
            $zip->close();
            $img_dir = $extractpath . '/' . $filename;
            $imgs = $this->listImages($img_dir);
            $all_images = array();
            foreach ($imgs as $finalImage) {
                $this_mls = substr($finalImage, 0, strrpos($finalImage, '-'));
                $imagePath = $img_dir . $finalImage;
                $all_images[$this_mls][] = $finalImage;
            }
            if (isset($all_images) && !empty($all_images) && count($all_images) > 0) {
                foreach ($all_images as $ListingId => $images) {
                    $query = RetsPropertyDataImage::where('listingID', $ListingId)->first();
                    $property = $query;
                    if (isset($property) && !empty($property)) {
                        //$image_tried = $property['image_tried'] ;
                        if (isset($property['s3_image_url']) && $property['s3_image_url'] != '') {
                        }
                        $all_pic = array();
                        foreach ($images as $image_name) {
                            $org_image  = $img_dir . "/" . $image_name;
                            $dest_image = $extractpath . "/mls_images/" . $image_name;
                            $move = \Illuminate\Support\Facades\File::move($org_image, $dest_image);
                            $curr_time = date('Y-m-d H:i:s');
                            $updData['s3_image_url'] = url('assets/property/mls_images/' . $image_name);
                            $updData['is_download'] = 1;
                            $updData['downloaded_time'] = $curr_time;
                            $up = RetsPropertyDataImage::where('listingID', $ListingId)->update($updData);
                            $message .= "<br> Image added : " . $ListingId . " (" . count($all_pic) . " Images) ";
                        }
                    } else {
                        foreach ($images as $image_name) {
                            $org_image  = $img_dir . "/" . $image_name;
                            $dest_image = $extractpath . "/mls_images/" . $image_name;
                            $move = File::move($org_image, $dest_image);
                            //$all_pic[] = url('assets/property/mls_images/'.$image_name);
                            $curr_time = date('Y-m-d H:i:s');
                            $updData['s3_image_url'] = url('assets/property/mls_images/' . $image_name);
                            $updData['is_download'] = 1;
                            $updData['downloaded_time'] = $curr_time;
                            $updData['listingID'] = $ListingId;
                            $up = RetsPropertyDataImage::create($updData);
                        }
                        $message .= "<br> Image added : " . $ListingId . " (" . count($images) . " Images) ";
                        //$message .= "<br><font style='color:red'> MLS# ".$ListingId."  Not Found</font>";
                    }
                }
            }
            unlink($extractpath . '/' . $image);
            File::deleteDirectory($img_dir);
        }
        return response()->json([
            'success' => true,
            'data' => $message,
            'message' => 'File uploaded SuccessFully',
            'request' => $request->all(),
        ]);
        //        return $message;
    }
    function listImages($img_dir)
    {
        $imgs = array();
        if (is_dir($img_dir)) {
            if ($dh = opendir($img_dir)) {
                while (($file = readdir($dh)) !== false) {
                    $file_check = strtolower($file);
                    if ((substr($file_check, -4) == '.jpg' || substr($file_check, -5) == '.jpeg' || substr($file_check, -4) == '.png')) {
                        $imgs[$file] = $file;
                    }
                }
                closedir($dh);
            }
        }
        $i = 0;
        natsort($imgs);
        return $imgs;
    }
    public function AddProperty($id = null)
    {
        $APP_NAME = env('APP_NAME');
        $data["pageTitle"] = $APP_NAME . " | Property Add";
        $FeatureType = config("mls_config.FeaturesType");
        $data['PropertySubType'] = config("mls_config.PropertySubType");
        $data['PropertyType'] = config("mls_config.PropertyType");
        if (isset(Websetting()->MapApiKey) && !empty(Websetting()->MapApiKey)) {
            $data['Googleapikey'] = Websetting()->MapApiKey;
        } else {
            $data['Googleapikey'] = "";
        }
        foreach ($FeatureType as &$value) {
            $value['features'] = FeaturesMaster::where('Type', $value['type'])->where('AdminId', 0)->limit(100)->get();
        }
        if (isset($id) && !empty($id)) {
            $property = $this->retsPropertyData::where('ListingId', $id)->first();
            if (isset($property->id) && !empty($property->id)) {
                $property['featureProperty'] = PropertyFeatures::where('PropertyId', $property->id)->get();
                //                return $property['featureProperty'];
            }
            $data['property'] = $property;
        }
        $data['img'] = RetsPropertyDataImage::where('ListingId', $id)->get();
        //        return $data['property'];
        $data['FeatureType'] = $FeatureType;
        //        return $data;
        return view('agent.property.AddProperty', $data);
    }
    public function AddPropertyInfo(Request $request)
    {
        //            return $request->all();
        $form_data = $request->all();
        $id = 0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            return $form_data['id'];
            $id = $form_data['id'];
            unset($form_data['id']);
        }
        if (isset($form_data['_token']) && !empty(isset($form_data['_token']))) {
            $token = $form_data['_token'];
            unset($form_data['_token']);
        }
        //        return $id;
        if ($id == 0) {

            $check = $this->retsPropertyData::where("ListingId", $form_data["ListingId"])->count();
            if (isset($check) && !empty($check) && intval($check) > 0) {
                $form_data["updated_time"] = date("Y-m-d H:i:s");
                $form_data['MlsStatus'] = 'Active';
                $form_data['Status'] = 'A';
                $form_data["UpdatedByAdmin"] = 1;
                $upd = $this->retsPropertyData::where("ListingId", $form_data["ListingId"])->update($form_data);
                $data["upd"][] = $form_data;
                if ($upd) {
                    $message = 'Main Information updated successfully !';
                    return response()->json([
                        'success' => true,
                        'data' => $form_data,
                        'message' => $message,
                    ]);
                }
            } else {
                $form_data["inserted_time"] = date("Y-m-d H:i:s");
                $form_data['MlsStatus'] = 'Active';
                $form_data["UpdatedByAdmin"] = 1;
                $ins = $this->retsPropertyData::firstOrCreate($form_data);
                $data["add"][] = $form_data;
                if ($ins) {
                    $message = 'Main Information added successfully !!';
                    return response()->json([
                        'success' => true,
                        'data' => $form_data,
                        'message' => $message,
                    ]);
                }
            }
        } else {
            $unit_id = $this->retsPropertyData::updateOrCreate(['id' => $id], $form_data);
            $message = 'Main Information updated successfully !';
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => 'Data imported SuccessFully',
            'request' => $request->all(),
        ]);
    }
    public function DescriptionAdd(Request $request)
    {
        //            return $request->all();
        $form_data = $request->all();
        $id = 0;
        if (isset($form_data['id']) && !empty(isset($form_data['id']))) {
            $token = $form_data['id'];
            unset($form_data['id']);
        }
        if (isset($form_data['_token']) && !empty(isset($form_data['_token']))) {
            $id = $form_data['_token'];
            unset($form_data['_token']);
        }

        if ($form_data["ListingId"]) {
            $id = $form_data["ListingId"];
            unset($form_data["ListingId"]);
            if ($this->sql == 'sql') {
                $query = RetsPropertyDataSql::query();
                $upd = $query->where("ListingId", $id)->update($form_data);
            } else {
                $upd = RetsPropertyData::where("ListingId", $id)->update($form_data);
            }
            $message = 'Property Description updated successfully !';
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
        }
        return response()->json([
            'error' => true,
            'data' => $form_data,
            'message' => 'Something wents wrong',
            'request' => $request->all(),
        ]);
    }
    public function FeaturesAdd(Request $request)
    {
        $form_data = $request->all();

        if (isset($form_data["ListingId"]) && !empty($form_data["ListingId"])) {

            $check = $this->retsPropertyData::where("ListingId", $form_data["ListingId"])->first();
            //            return $check;
            if ($check->id) {
                $del = PropertyFeatures::where('PropertyId', $check->id)->delete();
                $features = $form_data["aminity"];
                //                return $features;
                if (isset($features) && !empty($features)) {
                    foreach ($features as $val) {
                        //                        return $val;
                        $data['PropertyId'] = $check->id;
                        $data['FeaturesId'] = $val;
                        $add = PropertyFeatures::Create($data);
                    }
                    $message = "Features Added Successfully.";
                    return response()->json([
                        'success' => true,
                        'data' => $form_data,
                        'message' => $message,
                    ]);
                }
            }
        }
        return response()->json([
            'success' => $request->all(),
            'data' => $data,
            'message' => 'Something wents wrong.',
            'request' => $request->all(),
        ]);
    }
    public function DocumentAdd(Request $request)
    {
        $form_data = $request->all();
        $data['VirtualTourURLBranded'] = $form_data['VirtualTourURLBranded'];
        $data['ListAgentFullName'] = $form_data['ListAgentFullName'];
        $data['ListAgentEmail'] = $form_data['ListAgentEmail'];
        $data['ListAgentMlsId'] = $form_data['ListAgentMlsId'];
        $data['ListAgentDirectPhone'] = $form_data['ListAgentDirectPhone'];
        $ListingId = $form_data['ListingId'];
        if ($ListingId) {
            $upd = $this->retsPropertyData::where("ListingId", $ListingId)->update($data);
            //            if ($upd) {
            $message = 'Property Agent Info updated successfully !';
            return response()->json([
                'success' => true,
                'data' => $form_data,
                'message' => $message,
            ]);
            //            }
        }
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => 'Something wents wrong',
            'request' => $request->all(),
        ]);
    }
    public function ImagesAdd(Request $request)
    {
        $form_data = $request->all();
        $ListingId = $form_data['ListingId'];
        $imgData = [];
        $upload_path = public_path('/assets/property');
        if ($request->hasfile('imageUrl')) {
            foreach ($request->file('imageUrl') as $file) {
                $name = $file->getClientOriginalName();
                $name = time() . '.' . $file->extension();

                $path = $file->move(public_path('/assets/property/mls_images'), $name);
                $query = $this->retsPropertyData::where('ListingId', $ListingId)->first();
                //                return $query;
                $upd1['s3_image_url'] = url('/assets/property/mls_images/' . $name);
                $upd1['listingID'] = $ListingId;
                $upd1['image_url'] = $ListingId;
                $upd1['image_directory'] = '/mls_images/';
                $upd1['image_path'] = '/mls_images/';
                $upd1['mls_no'] = 1;
                $upd1['property_id'] = $query->id;
                $upd1['created_at'] = $query->id;
                $upd1['updated_at'] = $query->id;
                $upd1['is_uploaded_by_agent'] = 1;
                $upd = RetsPropertyDataImage::Create($upd1);
                //                return $upd;
            }
            if ($upd1) {
                $message = 'Images uploaded successfully !';
                return response()->json([
                    'success' => true,
                    'data' => $upd1,
                    'message' => $message,
                    'upd' => $upd,
                ]);
            }
        }
        return response()->json([
            'success' => $request->all(),
            'data' => $data,
            'message' => 'Something wents wrong.',
            'request' => $request->all(),
        ]);
    }
    public function DelImg(Request $request)
    {
        $form_data = $request->all();
        $listing = $form_data['listing'];
        $url = $form_data['url'];
        //        $pro = $this->retsPropertyData::where("ListingId", $listing)->first(['ImagesUrls']);
        $del = RetsPropertyDataImage::where('listingID', $listing)->where('s3_image_url', $url)->delete();

        return response()->json([
            'success' => true,
            'data' => $del,
            'message' => 'Image Removed Successfully',
        ]);
    }
    public function propFeature(Request $request)
    {
        $prop = ModelsRetsPropertyData::where("ListingId", $request->listingId)->first();
        $feature=FeaturedListing::where("ListingId",$request->listingId)->first();
        $payload = array(
            'PropertyId' => $prop->id,
            'AgentId' => $request->agentId,
            'ListingId' => $request->listingId,
        );
        $check = array(
            'AgentId' => $request->agentId,
            'ListingId' => $request->listingId,
        );
        if($feature){
            $res = FeaturedListing::where("ListingId",$request->listingId)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed Successfully',
            ]);
        }else{
            $res = FeaturedListing::updateOrCreate($check, $payload);
            return response()->json([
                'success' => true,
                'message' => 'Added Successfully',
            ]);
        }
    }
}
