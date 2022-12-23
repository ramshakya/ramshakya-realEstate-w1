@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->

@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:400,500"
        rel="stylesheet"
    />
    <style>
        /*body {*/
        /*    margin-top:40px;*/
        /*}*/
        .remove-image, .remove-image-gal {
            /* display: none; */
            position: absolute;
            top: -2px;
            right: -5px;
            border-radius: 10em;
            /* padding: 2px 6px 3px; */
            text-decoration: none;
            /* font: 700 21px/20px sans-serif; */
            background: #555;
            border: 3px solid #fff;
            color: red;
            /* box-shadow: 0 2px 6px rgb(0 0 0 / 50%), inset 0 2px 4px rgb(0 0 0 / 30%); */
            text-shadow: 0 1px 2px rgb(0 0 0 / 50%);
            -webkit-transition: background 0.5s;
            transition: background 0.5s;
        }
        .remove-image:hover,.remove-image-gal:hover {
            background: #E54E4E;
            /*padding: 3px 7px 5px;*/
            top: -6px;
            right: -6px;
            cursor: pointer;
        }
        .btn-default{
            /*background-color: #6c757d ;*/
        }
        .stepwizard-step p {
            margin-top: 10px;
        }
        .stepwizard-row {
            display: table-row;
        }
        .stepwizard {
            display: table;
            width: 100%;
            position: relative;
        }
        .stepwizard-step button[disabled] {
            opacity: 1 !important;
            filter: alpha(opacity=100) !important;
        }
        .stepwizard-row:before {
            top: 14px;
            bottom: 0;
            position: absolute;
            content: " ";
            width: 100%;
            height: 1px;
            background-color: #ccc;
            z-order: 0;
        }
        .stepwizard-step {
            display: table-cell;
            text-align: center;
            position: relative;
        }
        .btn-circle {
            width: 30px;
            height: 30px;
            text-align: center;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.428571429;
            border-radius: 15px;
        }
        .panel-group .panel {
            border-radius: 0;
            box-shadow: none;
            border-color: #EEEEEE;
        }

        .panel-default > .panel-heading {
            padding: 0;
            border-radius: 0;
            color: #212121;
            background-color: #FAFAFA;
            border-color: #EEEEEE;
        }

        .panel-title {
            font-size: 14px;
        }

        .panel-title > a {
            display: block;
            padding: 15px;
            text-decoration: none;
        }

        .more-less {
            float: right;
            color: #212121;
        }

        .panel-default > .panel-heading + .panel-collapse > .panel-body {
            border-top-color: #EEEEEE;
        }
        .btn-primary{
            background-color: #4ca3f7 !important;
            border-color: #409df7 !important;
        }
    </style>
    <!-- MultiStep Form -->
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 card pb-3 p-4">
                        <div class="stepwizard col-md-offset-3 ">
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step">
                                    <a href="#step-1" type="button" class="btn btn-primary btn-light btn-circle" disabled>1</a>
                                    <p>Main Information</p>
                                </div>
                                <div class="stepwizard-step">
                                    <a href="#step-2" type="button" class="btn btn-light btn-circle" disabled>2</a>
                                    <p>Descriptions</p>
                                </div>
                                <div class="stepwizard-step">
                                    <a href="#step-3" type="button" class="btn btn-light btn-circle" disabled>3</a>
                                    <p>Other Information</p>
                                </div>
                                <div class="stepwizard-step">
                                    <a href="#step-4" type="button" class="btn btn-light btn-circle" disabled>4</a>
                                    <p>List Agent Information</p>
                                </div>
                                <div class="stepwizard-step">
                                    <a href="#step-5" type="button" class="btn btn-light btn-circle" disabled>5</a>
                                    <p>Images</p>
                                </div>
                            </div>
                        </div>
                        <form method="post" enctype="multipart/form-data" id="InformationForm">
                            <div class="row">
                                <div class="col-12 setup-content" id="step-1">
                                    <div class="row">
                                        <div class="col-md-12"><h3> Main Information</h3></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Property Type</label>
                                                <select class="form-control" required name="PropertyType" id="PropertyType">
                                                    <option value="">Select Property Type</option>
                                                    <?php if(isset($PropertyType) &&!empty($PropertyType)){
                                                    foreach ($PropertyType as $pt){ ?>
                                                    <option value="{{@$pt}}" <?php if(isset($property->PropertyType) && !empty($property->PropertyType) && $property->PropertyType==$pt){ echo "selected";}?>>{{@$pt}}</option>
                                                    <?php } }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Property SubType</label>
                                                <select class="form-control" required name="PropertySubType" id="PropertySubType">
                                                    <option value="" disabled selected>Selecte Property Subtype</option>
                                                    <?php if(isset($PropertySubType) &&!empty($PropertySubType)){
                                                        foreach ($PropertySubType as $ps){ ?>
                                                    <option value="{{@$ps}}" <?php if(isset($property->PropertySubType) && !empty($property->PropertySubType) && $property->PropertySubType==$ps){ echo "selected";}?>>{{@$ps}}</option>
                                                    <?php } }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Listing Id</label>
                                                <input maxlength="100" type="text" name="ListingId" required="required" class="form-control" placeholder="Listing Id Like: A112345" id="ListingId" value="{{@$property->ListingId}}">
                                                {{--                                    <input type="text" id="lat">--}}
                                                {{--                                    <input type="text" id="long">--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"> Street Number</label>
                                                <input maxlength="100" type="text" name="StreetNumber" id="StreetNumber" required="required" class="form-control" placeholder=" Street Number" value="{{@$property->StreetNumber}}">
                                                <input type="hidden" id="Lat" name="Latitude" value="{{@$property->Latitude}}">
                                                <input type="hidden" id="Lng" name="Longitude" value="{{@$property->Longitude}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"> Street Direction</label>
                                                <input maxlength="100" type="text" required="required" id="StreetDirPrefix" name="StreetDirPrefix" class="form-control" placeholder=" Street Direction"  value="{{@$property->StreetDirPrefix}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"> Street Name</label>
                                                <input maxlength="100" type="text" name="StreetName" required="required" class="form-control" placeholder=" Street Name" id="StreetName"  value="{{@$property->StreetName}}">
                                                {{--                                    <input type="text" id="lat">--}}
                                                {{--                                    <input type="text" id="long">--}}
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <label class="control-label">Street Prefix</label>
                                            <input type="text" class="form-control" placeholder=" Street Prefix" id="StreetDirPrefix" value="{{@$property->StreetDirPrefix}}">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label"> Unit Number</label>
                                                <input maxlength="100" type="text" name="UnitNumber" id="UnitNumber" required="required" class="form-control" placeholder=" Unit Number" value="{{@$property->UnitNumber}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Address</label>
                                                <input type="text" required="required" name="StandardAddress" id="address1" class="form-control" placeholder="Address" value="{{@$property->StandardAddress}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Community Name</label>
                                                <input maxlength="100" type="text" name="CustomAddress" required="required" class="form-control" id="Community" placeholder="Community Name" value="{{@$property->CustomAddress}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Area</label>
                                                <input maxlength="100" type="text" name="CustomAddress3" id="CustomAddress3" class="form-control" value="{{@$property->CustomAddress3}}" placeholder="Area">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">City</label>
                                                <input maxlength="100" type="text" name="City" id="locality" required="required" class="form-control" placeholder="Enter City" value="{{@$property->City}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">County</label>
                                                <input maxlength="100" type="text" name="County" id="county" class="form-control" placeholder="County"  value="{{@$property->County}}">
                                            </div>
                                        </div>
{{--                                        <div class="col-md-4">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label class="control-label">State</label>--}}
{{--                                                <input maxlength="100" type="text" name="StateOrProvince" required="required" class="form-control" placeholder="State" id="State" value="{{@$property->StateOrProvince}}">--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row">--}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Zip Code</label>
                                                <input maxlength="100" type="text" name="PostalCode" required="required" id="postcode" class="form-control" placeholder="Zip Code" value="{{@$property->PostalCode}}">
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-success btn-sm pull-right" id="btnSubmit" type="submit">
                                        <div class="spinner-border d-none" role="status" id="rule-btn2">
                                            <span class="sr-only">Loading...</span>
                                        </div> &nbsp<i aria-hidden="true" class="far fa-check-circle"></i> Save & Next
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form method="post" enctype="multipart/form-data" id="Description">
                            <div class="row">
                                <div class="col-12 setup-content" id="step-2">
                                    <div class="col-xs-6 col-md-offset-3">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12"><h3> Description</h3></div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Subdivision</label>
                                                        <input maxlength="100" type="text" required="required" id="SubdivisionName" class="form-control" placeholder="Subdivision" value="{{@$property->SubdivisionName}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Year Build</label>
                                                        <input maxlength="100" type="year" id="YearBuilt" required="required" class="form-control" placeholder="Year Build" value="{{@$property->YearBuilt}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Price</label>
                                                        <input maxlength="100" type="number" name="ListPrice" id="ListPrice" required="required" class="form-control" placeholder="Price" value="{{@$property->ListPrice}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Living Area </label>
                                                        <input maxlength="100" type="text" id="LivingArea" required="required"  class="form-control" placeholder="Building Square" value="{{@$property->LivingArea}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Lot Square</label>
                                                        <input maxlength="100" type="text" class="form-control" placeholder="Lot Square">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Bedrooms</label>
                                                        <input maxlength="100" type="number" id="BedroomsTotal" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" pattern="^[0-9]" min="0" required="required" class="form-control" placeholder="Bedrooms" value="{{@$property->BedroomsTotal}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Bathrooms</label>
                                                        <input maxlength="100" type="number" id="BathroomsFull" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" pattern="^[0-9]" min="0" required="required" class="form-control" placeholder="Bathrooms" value="{{@$property->BathroomsFull}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Remarks </label>
                                                        <textarea type="number" id="Remarks" required="required" class="form-control"> {{@$property->PublicRemarks}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Previous</button>
                                            <button class="btn btn-outline-success btn-sm pull-right" id="btnSubmit2" type="submit"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                    <span class="sr-only">Loading...</span>
                                                </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save & Next</button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post" enctype="multipart/form-data" id="OtherInformation">
                            <div class="row">
                                <div class="col-12 setup-content" id="step-3">
                                    <div class="col-md-12">
                                        <h3> Other Information</h3>
                                        <div class="row">

                                            <div class="panel-group col-12" id="accordion" role="tablist" aria-multiselectable="true">
                                                <?php $x=1;
                                                $pfeatures=[];
                                                //                                                        dd($property['featureProperty']);
                                                if(isset($property['featureProperty']) && !empty($property['featureProperty'])){
                                                    $pf=$property['featureProperty'];
//                                                            dd($pf);
                                                    $pf = $pf->toArray();
                                                    $pfeatures=array_column($pf, 'FeaturesId');
                                                }
                                                ?>
                                                @if($FeatureType)
                                                    @foreach($FeatureType as $type)
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading" role="tab" id="headingOne{{$x}}">
                                                                <h4 class="panel-title">
                                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$x}}" aria-expanded="true" aria-controls="collapseOne">
                                                                        <i class="more-less glyphicon glyphicon-plus"></i>
                                                                        {{@$type['type']}}
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapseOne{{$x}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                                <div class="panel-body row pl-3">
                                                                    <?php if(isset($type['features']) && !empty($type['features'])){
                                                                    foreach ($type['features'] as $featuresd){
                                                                    ?>
                                                                    <div class="form-check mb-2 col-md-3 form-check-primary">
                                                                        <input class="form-check-input" type="checkbox" name="aminity" id="customckeck{{@$featuresd->id}}" value="{{@$featuresd->id}}" <?php if( isset($featuresd->id) && in_array($featuresd->id, $pfeatures)){ echo "checked"; }?>>
                                                                        <label class="form-check-label" for="customckeck1">{{@$featuresd->Features}}</label>
                                                                    </div>
                                                                    <?php } } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php $x++;?>
                                                    @endforeach
                                                @endif


                                            </div><!-- panel-group -->
                                        </div>
                                        <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Previous</button>
                                        <button class="btn btn-outline-success btn-sm pull-right" id="btnSubmit3" type="submit"><div class="spinner-border d-none" role="status" id="rule-btn3">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save & Next</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post" enctype="multipart/form-data" id="DocumentsForm">
                            <input type="hidden" name="ListingId" id="DocsListingId" value="{{@$property->ListingId}}">
                            <div class="col-12 setup-content" id="step-4">
                                <div class="col-xs-6 col-md-offset-3">
                                    <div class="col-md-12">

                                        <div class="row">
                                            <div class="col-md-12"><h3> Documents/Video/OpenHouse Information/List Agent Information</h3></div>
                                            <div class=" col-4 mt-2" >
                                                <label class="control-label"> Video URL</label>
                                                <br/>
                                                <input type="text" id="VirtualTourURLBranded" class="form-control" name="VirtualTourURLBranded" placeholder="YouTube or Vimeo Video URL here" value="{{@$property->VirtualTourURLBranded}}">
                                            </div>
                                            <div class=" col-4 mt-2" >
                                                <label class="control-label"> Document</label>
                                                <input type="file" class="form-control" placeholder="documrnt" id="document"  accept=".pdf,.docx" >
                                            </div>
                                        </div>
                                        <div class="row Pl-2">
                                            <div class="col-12">
                                                <h4>ListAgent Information - </h4><br>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Name</label>
                                                    <input maxlength="100" type="text" name="ListAgentFullName" id="ListAgentFullName" required="required" class="form-control" placeholder="Agent Name" value="{{@$property->ListAgentFullName}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <label class="control-label">Email</label>
                                                    <input maxlength="100" type="email" name="ListAgentEmail" id="ListAgentEmail" required="required" class="form-control" placeholder="Agent Email" value="{{@$property->ListAgentEmail}}">
                                                </div>
                                            </div>
                                            <div class="col-4" >
                                                <div class="form-group">
                                                    <label class="control-label">Phone </label>
                                                    <input maxlength="100" type="number" name="ListAgentDirectPhone"  required="required" class="form-control" placeholder="Agent Phone"  id="ListAgentDirectPhone" value="{{@$property->ListAgentDirectPhone}}">
                                                </div>
                                            </div>
                                            <div class="col-4" >
                                                <div class="form-group">
                                                    <label class="control-label">List Agent MlsId </label>
                                                    <input maxlength="100" type="text" name="ListAgentMlsId"   required="required" class="form-control" placeholder="List Agent MlsId"  id="ListAgentMlsId" value="{{@$property->ListAgentMlsId}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pl-2">
                                            <h4>Open House Information </h4><br>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Openhouse Date</label>
                                                    <input maxlength="100" type="date" name="Openhousedate1" class="form-control" placeholder=" Unit Number" id="Openhousedate1" value="{{@$property->BathroomsFull}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4" >
                                                <div class="form-group">
                                                    <label class="control-label">Start Time</label>
                                                    <input maxlength="100" type="time" name="Openhousestarttime1"  class="form-control" placeholder="Openhouse Date" id="Openhousestarttime1" value="{{@$property->BathroomsFull}}">
                                                </div>
                                            </div>
                                            <div class="col-4" >
                                                <div class="form-group">
                                                    <label class="control-label">End Time</label>
                                                    <input maxlength="100" type="time" name="Openhouseendtime1" class="form-control" placeholder="Openhouse Date"  id="Openhouseendtime1" value="{{@$property->BathroomsFull}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4" >
                                                <label class="control-label">Openhouse Date</label>
                                                <input maxlength="100" type="date" name="Openhousedate2" class="form-control" placeholder="Openhouse Date"  id="Openhousedate2" value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">Start Time</label>
                                                <input maxlength="100" type="time" name="Openhousestarttime2" class="form-control" placeholder="Openhouse Date" id="Openhousestarttime2" value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">End Time</label>
                                                <input maxlength="100" type="time" name="Openhouseendtime2" class="form-control" placeholder="Openhouse Date"  id="Openhouseendtime2" value="{{@$property->BathroomsFull}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4" >
                                                <label class="control-label">Openhouse Date</label>
                                                <input maxlength="100" type="date" name="Openhousedate3" class="form-control" placeholder="Openhouse Date"  id="Openhousedate3" value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">Start Time</label>
                                                <input maxlength="100" type="time" name="Openhousestarttime3" class="form-control" placeholder="Openhouse Date"  id="Openhousestarttime3"value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">End Time</label>
                                                <input maxlength="100" type="time" name="Openhouseendtime3" class="form-control" placeholder="Openhouse Date" id="Openhouseendtime3" value="{{@$property->BathroomsFull}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4" >
                                                <label class="control-label">Openhouse Date</label>
                                                <input maxlength="100" type="date" name="Openhousedate4"  class="form-control" placeholder="Openhouse Date" id="Openhousedate4" value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">Start Time</label>
                                                <input maxlength="100" type="time" name="Openhousestarttime4"  class="form-control" placeholder="Openhouse Date" id="Openhousestarttime4" value="{{@$property->BathroomsFull}}">
                                            </div>
                                            <div class="col-4" >
                                                <label class="control-label">End Time</label>
                                                <input maxlength="100" type="time" name="Openhouseendtime4"  class="form-control" placeholder="Openhouse Date" id="Openhouseendtime4" value="{{@$property->BathroomsFull}}">
                                            </div>
                                        </div>
                                        <br/>
                                        <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Previous</button>
                                        <button class="btn btn-outline-success btn-sm pull-right" id="btnSubmit4" type="submit"><div class="spinner-border d-none" role="status" id="rule-btn4">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save & Next</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post" enctype="multipart/form-data" id="ImagesForm">
                            <div class="col-12 setup-content" id="step-5">
                                <div class="col-xs-6 col-md-offset-3">
                                    <div class="col-md-12">
                                        <h3> Image</h3>
                                        <input type="hidden" id="DataListingId" name="ListingId" value="{{@$property->ListingId}}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Images</label>
                                                    <input maxlength="100" type="file" id="images" class="form-control" name="imageUrl[]" multiple placeholder="Subdivision">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php if(isset($img) && !empty($img)){
                                            foreach ($img as $image){?>
                                            <div class="p-1 col-1 preview-file-input p-0">
                                                <a class="th" href="{{$image->s3_image_url}}" target="_blank">
                                                    <img width="100%" src="{{$image->s3_image_url}}" style="background-size:cover;height: 60px;width: 100%;">
                                                </a><i class="far fa-times-circle cross-icon remove-image-gal galbtn g1"  data-id="{{$image->s3_image_url}}"></i>
                                            </div>
                                            <?php }
                                            }?>
                                        </div>
                                        <br/>
                                        <button class="btn btn-primary prevBtn btn-sm pull-left" type="button">Previous</button>
                                        <button class="btn btn-outline-success btn-sm pull-right" id="btnSubmit5" type="submit"><div class="spinner-border d-none" role="status" id="rule-btn5">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageLevelJS')
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("Remarks").value;
            CKEDITOR.replace('Remarks');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
        var Googleapikey={{$Googleapikey}};
        if(Googleapikey=="")
        {
            toastr.error(errors.message,'Please add a valid Map Api Key');
        }
    </script>
    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{$Googleapikey}}&callback=initAutocomplete&libraries=places&v=weekly"
        async
    ></script>
    <script>
        let autocomplete;
        let address1Field;
        let address2Field;
        let postalField;

        function initAutocomplete() {
            address1Field = document.querySelector("#address1");
            address2Field = document.querySelector("#address2");
            postalField = document.querySelector("#postcode");
            autocomplete = new google.maps.places.Autocomplete(address1Field, {
                componentRestrictions: { country: ["us", "ca","in"] },
                fields: ["address_components", "geometry"],
                types: ["address"],
            });
            address1Field.focus();
            autocomplete.addListener("place_changed", fillInAddress);
        }

        function fillInAddress() {
            const place = autocomplete.getPlace();
            let address1 = "";
            let postcode = "";
            var lat = place.geometry.location.lat(),
                lng = place.geometry.location.lng();
            document.querySelector("#Lat").value = lat;
            document.querySelector("#Lng").value = lng;
            console.log(lat);
            for (const component of place.address_components) {
                const componentType = component.types[0];

                switch (componentType) {
                    case "street_number": {
                        address1 = `${component.long_name} ${address1}`;
                        break;
                    }
                    case "sublocality_level_2": {
                        address1 += ", " + component.long_name;
                        break;
                    }
                    case "route": {
                        address1 += component.short_name;
                        document.querySelector("#Community").value = component.long_name;
                        break;
                    }
                    case "postal_code": {
                        postcode = `${component.long_name}${postcode}`;
                        document.querySelector("#postcode").value = component.long_name;
                        console.log(component.long_name);
                        break;
                    }

                    case "postal_code_suffix": {
                        postcode = `${postcode}-${component.long_name}`;
                        break;
                    }
                    case "locality": {
                        document.querySelector("#locality").value = component.long_name;
                        break;
                    }
                    case "administrative_area_level_1": {
                        // document.querySelector("#locality").value = component.long_name;
                        document.querySelector("#State").value = component.long_name;
                        break;
                    }
                    case "administrative_area_level_2": {
                        // document.querySelector("#locality").value = component.long_name;
                        document.querySelector("#county").value = component.long_name;
                        break;
                    }
                    case "country": {
                        // document.querySelector("#country").value = component.long_name;
                        break;
                    }
                    case "sublocality_level_1": {
                        document.querySelector("#Community").value = component.long_name;
                        break;
                    }
                    case "sublocality_level_2":{
                        document.querySelector("#CustomAddress3").value = component.long_name;
                        break;
                    }
                }

            }

            address1Field.value = address1;
            postalField.value = postcode;
            // After filling the form with address components from the Autocomplete
            // prediction, set cursor focus on the second address line to encourage
            // entry of subpremise information such as apartment, unit, or floor number.
            // address2Field.focus();
        }

    </script>
    {{--    <script--}}
    {{--            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-r5oodsnCHgKazTsuUWAO7-Lv3QVeYzw&callback=initMap&libraries=places&v=weekly"--}}
    {{--            async--}}
    {{--    ></script>--}}
    {{--    <script async defer--}}
    {{--            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-r5oodsnCHgKazTsuUWAO7-Lv3QVeYzw&callback=initMap">--}}
    {{--    </script>--}}
    <!-- Dashboard init js-->
    <script>
        $(document).ready(function () {
            var navListItems = $('div.setup-panel div a'),
                allWells = $('.setup-content'),
                allNextBtn = $('.nextBtn'),
                allPrevBtn = $('.prevBtn');

            allWells.hide();

            navListItems.click(function (e) {
                e.preventDefault();
                var $target = $($(this).attr('href')),
                    $item = $(this);

                if (!$item.hasClass('disabled')) {
                    navListItems.removeClass('btn-primary').addClass('btn-default');
                    $item.addClass('btn-primary');
                    allWells.hide();
                    $target.show();
                    $target.find('input:eq(0)').focus();
                }
            });

            allPrevBtn.click(function(){
                var curStep = $(this).closest(".setup-content"),
                    curStepBtn = curStep.attr("id"),
                    prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");
                prevStepWizard.removeAttr('disabled').trigger('click');
            });

            // allNextBtn.click(function(){
            //     var curStep = $(this).closest(".setup-content"),
            //         curStepBtn = curStep.attr("id"),
            //         nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            //         curInputs = curStep.find("input[type='text'],input[type='url']"),
            //         isValid = true;
            //
            //     $(".form-group").removeClass("has-error");
            //     for(var i=0; i<curInputs.length; i++){
            //         if (!curInputs[i].validity.valid){
            //             isValid = false;
            //             $(curInputs[i]).closest(".form-group").addClass("has-error");
            //         }
            //     }
            //     nextStepWizard.removeAttr('disabled').trigger('click');
            // });

            $('div.setup-panel div a.btn-primary').trigger('click');
        });
    </script>
    <script>
        $(document).on('submit','#InformationForm',function(e){
            e.preventDefault();
            // alert(1);

            $('#rule-btn2').removeClass('d-none');
            $('#btnSubmit').attr('disabled', true);
            var data = {
                // 'id':id,
                'PropertyType': $('#PropertyType').val(),
                'PropertySubType':$('#PropertySubType').val(),
                'StreetName': $('#StreetName').val(),
                'StreetNumber': $('#StreetNumber').val(),
                'StreetDirPrefix': $('#StreetDirPrefix').val(),
                'StandardAddress': $('#address1').val(),
                'UnitNumber':$('#UnitNumber').val(),
                'CustomAddress': $('#Community').val(),
                'CustomAddress3':$('#CustomAddress3').val(),
                'City': $('#locality').val(),
                'County': $('#county').val(),
                // 'StateOrProvince': $('#State').val(),
                'PostalCode': $('#postcode').val(),
                'ListingId': $('#ListingId').val(),
                'Latitude': $('#Lat').val(),
                'Longitude': $('#Lng').val(),
                "_token": "{{ csrf_token() }}"
            };
            $('#DataListingId').val($('#ListingId').val());
            $('#DocsListingId').val($('#ListingId').val());
            $('#DocsListingId').val($('#ListingId').val());
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/AddPropertyInfo")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    setTimeout(function(){
                        var curStep = $("#btnSubmit").closest(".setup-content"),
                            curStepBtn = curStep.attr("id"),
                            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                            curInputs = curStep.find("input[type='text'],input[type='url']"),
                            isValid = true;

                        $(".form-group").removeClass("has-error");
                        for(var i=0; i<curInputs.length; i++){
                            if (!curInputs[i].validity.valid){
                                isValid = false;
                                $(curInputs[i]).closest(".form-group").addClass("has-error");
                            }
                        }
                        nextStepWizard.removeAttr('disabled').trigger('click');
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('submit','#Description',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#btnSubmit').attr('disabled', true);
            var Remark = $('#Remarks').val();
            var data = {
                // 'id':id,
                // 'ListingId': $('#ListingIddesc').val(),
                'SubdivisionName': $('#SubdivisionName').val(),
                'YearBuilt': $('#YearBuilt').val(),
                'BedroomsTotal': $('#BedroomsTotal').val(),
                'BathroomsFull': $('#BathroomsFull').val(),
                'PublicRemarks': Remark,
                'LivingArea': $('#LivingArea').val(),
                'ListingId': $('#ListingId').val(),
                'ListPrice': $('#ListPrice').val(),
                "_token": "{{ csrf_token() }}"
            };
            console.log("Remarks",$('#Remarks').val());
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/DescriptionAdd")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    setTimeout(function(){
                        var curStep = $("#btnSubmit2").closest(".setup-content"),
                            curStepBtn = curStep.attr("id"),
                            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                            curInputs = curStep.find("input[type='text'],input[type='url']"),
                            isValid = true;

                        $(".form-group").removeClass("has-error");
                        for(var i=0; i<curInputs.length; i++){
                            if (!curInputs[i].validity.valid){
                                isValid = false;
                                $(curInputs[i]).closest(".form-group").addClass("has-error");
                            }
                        }
                        nextStepWizard.removeAttr('disabled').trigger('click');
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('submit','#OtherInformation',function(e){
            e.preventDefault();
            $('#rule-btn3').removeClass('d-none');
            $('#btnSubmit3').attr('disabled', true);
            var aminity=[];
            $.each($("input[name='aminity']:checked"), function(){
                aminity.push($(this).val());
            });
            console.log(aminity);
            var data = {
                'aminity': aminity,
                'ListingId': $('#ListingId').val(),
            };
            $('#DataListingId').val($('#ListingId').val());
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/FeaturesAdd")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    $('#rule-btn3').addClass('d-none');
                    $('#btnSubmit3').attr('disabled', false);
                    setTimeout(function(){
                        var curStep = $("#btnSubmit3").closest(".setup-content"),
                            curStepBtn = curStep.attr("id"),
                            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                            curInputs = curStep.find("input[type='text'],input[type='url']"),
                            isValid = true;

                        $(".form-group").removeClass("has-error");
                        for(var i=0; i<curInputs.length; i++){
                            if (!curInputs[i].validity.valid){
                                isValid = false;
                                $(curInputs[i]).closest(".form-group").addClass("has-error");
                            }
                        }
                        nextStepWizard.removeAttr('disabled').trigger('click');
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn3').addClass('d-none');
                    $('#btnSubmit3').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('submit','#DocumentsForm',function(e){
            e.preventDefault();
            $('#rule-btn4').removeClass('d-none');
            $('#btnSubmit4').attr('disabled', true);
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/DocumentAdd")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');

                    setTimeout(function(){
                        $('#rule-btn4').addClass('d-none');
                        $('#btnSubmit4').attr('disabled', false);
                        var curStep = $("#btnSubmit4").closest(".setup-content"),
                            curStepBtn = curStep.attr("id"),
                            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                            curInputs = curStep.find("input[type='text'],input[type='url']"),
                            isValid = true;

                        $(".form-group").removeClass("has-error");
                        for(var i=0; i<curInputs.length; i++){
                            if (!curInputs[i].validity.valid){
                                isValid = false;
                                $(curInputs[i]).closest(".form-group").addClass("has-error");
                            }
                        }
                        nextStepWizard.removeAttr('disabled').trigger('click');
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn4').addClass('d-none');
                    $('#btnSubmit4').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('submit','#ImagesForm',function(e){
            e.preventDefault();
            $('#rule-btn5').removeClass('d-none');
            $('#btnSubmit5').attr('disabled', true);
            var formData = new FormData(this);
            var listing=$('#ListingId').val();

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/ImagesAdd")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    $('#rule-btn5').addClass('d-none');
                    $('#btnSubmit5').attr('disabled', false);
                    setTimeout(function(){
                        window.location.href="{{url('agent/property/AddProperty')}}/"+listing;
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('click','.galbtn',function(e){
            // var url = $(this).closest('img.galimage').attr('src');
            e.preventDefault();
            var url=$(this).data("id");
            var listing=$('#ListingId').val();
            var data = {
                'listing':listing,
                'url':url,
                "_token": "{{ csrf_token() }}"
            };
            $(this).parent('div').remove();
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/DelImg")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    $(this).parent('div').remove();
                    setTimeout(function(){
                        {{--window.location.href="{{url('agent/property/AddProperty')}}/"+listing;--}}
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn2').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });

        });
        function toggleIcon(e) {
            $(e.target)
                .prev('.panel-heading')
                .find(".more-less")
                .toggleClass('fa-plus fa-minus');
        }
        $('.panel-group').on('hidden.bs.collapse', toggleIcon);
        $('.panel-group').on('shown.bs.collapse', toggleIcon);

    </script>
    <script language="JavaScript">

        // this prevents from typing non-number text, including "e".
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            let charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();
            } else {
                return true;
            }
        }
    </script>
@endsection
