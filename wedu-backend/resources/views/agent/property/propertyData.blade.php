@extends('.agent/layouts.app')
@section('title', 'Dashboard')
    <!-- Begin page -->
@section('pageContent')

    <!-- third party css -->
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        .img-size {
            max-height: 500px !important;
            width: 100%;
        }

        .PropertyModelBtn {
            cursor: pointer;
        }

        .border-none {
            border-style: none;
            background-color: #f7f7f7 !important;
        }

        .btn-xs {
            background-color: #fff;
        }

        .card-title {
            margin-bottom: 0px;
        }

        .card-body {
            /* padding-top:0px !important;*/
        }

        .project-tab {
            padding: 10%;
            margin-top: -8%;
        }

        .project-tab #tabs {
            background: #007b5e;
            color: #eee;
        }

        .project-tab #tabs h6.section-title {
            color: #eee;
        }

        .project-tab #tabs .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #0062cc;
            background-color: transparent;
            border-color: transparent transparent #f3f3f3;
            border-bottom: 1px solid !important;
            /*font-size: 16px;
                    font-weight: bold;*/
        }

        .project-tab .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            color: #0062cc;
            /*font-size: 16px;
                    font-weight: 600;*/
        }

        .project-tab .nav-link:hover {
            border: none;
        }

        .project-tab thead {
            background: #f3f3f3;
            color: #333;
        }

        /*.project-tab a{
                    text-decoration: none;
                    color: #333;
                    font-weight: 600;
                }*/
        .nav-link {
            display: block;
            padding: 0px;
        }

        .nav-item {
            font-weight: 400;
        }

        p {
            font-weight: 100;
        }

        .wd-60 {
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto, sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow: hidden;
            text-align: center;
        }

        .wd-30 {
            max-width: 30px !important;
            min-width: 20px !important;
            font-family: Roboto, sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow: hidden;
            text-align: center;
        }

        .th-wd-30 {
            max-width: 30px !important;
            min-width: 10px !important;
            font-family: Roboto, sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }

        .th-wd-60 {
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto, sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }

        button.btn.search_button_one {
            position: absolute;
            top: 2px;
            right: 17px;
            padding: 5px 16px;
            background-color: #5b69bc !important;
            border: none;
            min-height: 10px;
            min-width: 2px;
            /* border-radius: 20px; */
        }

        .cursor-pointer {
            cursor: pointer;
        }

        #spinLoader {
            position: fixed;
            top: 50%;
            left: 48%;
            background-color: #fff;
            z-index: 9999;
        }

        .select2-search__field {
            /*height: 40px;*/
            padding: 2px !important;
        }

        .multiselect {
            /*width: 200px;*/
        }

        .selectBox {
            position: relative;
        }

        .selectBox select {
            width: 100%;
            /*font-weight: bold;*/
        }

        .feature_collapse {
            padding: 10px 20px 10px 10px;
            cursor: pointer;
        }

        .down_icon {
            font-size: 18px;
            float: right;
            margin-left: 80px;
            margin-top: -2px;
        }

        .feature_body {
            padding: 8px;
        }

        .feature_body input[type='checkbox'] {
            margin-right: 10px;
        }

        .feature_body ul {
            padding-left: 10px;
            margin-bottom: 0px;
        }

        .feature_body ul li {
            display: inline-block;
            width: 25%%;
            margin-left: 20px;
        }
        .heartFill{
            color: gray !important;
        }

    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="agentId" id="agentId" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                        <div class="card">
                            <div class="card-header mb-0 pb-0">
                                <h4 class="card-title card-title-heading mb-0  pb-0">
                                    <div class="row p-1 mb-2">
                                        <div class="col-2">
                                            <!-- <i class="fa fa-users"></i> Residential -->
                                            <!-- <select class="border-none" id="PropertyType">
                                                        <option value="Residential"><b>Residential</b></option>
                                                        <option value="Commercial"><b>Commercial</b></option>
                                                        <option value="Condos"><b>Condos</b></option>
                                                        <option value="Custom Properties"><b>Custom Properties</b></option>
                                                    </select> -->
                                        </div>
                                        <!--                                        <div class="col-10 text-right">
                                                    <button class="btn-xs btn-outline-purple">Work On behalf of ...</button>
                                                    <button class="btn-xs btn-outline-purple">Email</button>
                                                    <button class="btn-xs btn-outline-purple">Save</button>
                                                    <button class="btn-xs btn-outline-purple">Print</button>
                                                </div>-->
                                    </div>
                                    <div class="row p-1">
                                        <div class="col-4">
                                            <!--                                            <p>Draft last saved: 12/2/2020 9:50:23 AM</p>-->
                                        </div>
                                        <div class="col-5"></div>
                                        <div class="col-md-3 col-sm-3 col-lg-3 text-right">
                                            <!--                                            <nav>
                                                        <div class="nav nav-tabs nav-fill p-0" id="nav-tab" role="tablist">
                                                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Edit Search</a>
                                                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">List</a>
                                                            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Detail</a>
                                                            <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Photos</a>
                                                        </div>
                                                    </nav>-->
                                        </div>
                                    </div>
                                </h4>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="col-md-12 mb-2">
                                    <div class="row mb-0">
                                        <h4 class="card-title card-title-heading ml-3 filterBtn cursor-pointer">
                                            <i class="fa fa-filter"></i> Filters
                                        </h4>
                                    </div>
                                    <div class="row pt-2 Filterrow" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>Property type</label>
                                                        <select class="form-control" id="PropertyType">
                                                            <option value=""><b>Select</b></option>
                                                            <option value="Residential"><b>Residential</b></option>
                                                            <option value="Commercial"><b>Commercial</b></option>
                                                            <option value="Condos"><b>Condos</b></option>
                                                            <option value="Custom Properties"><b>Custom Properties</b>
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Property sub type</label>
                                                        <select class="form-control" id="PropertySubType">
                                                            <option value=""><b>Select</b></option>
                                                            @foreach ($PropertySubType as $SubType)
                                                                <option value="{{ $SubType->PropertySubType }}">
                                                                    <b>{{ $SubType->PropertySubType }}</b>
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Search by MLS ID , Address , List Price</label>
                                                            <input type="text" id="userinput1"
                                                                class="form-control search_input_one " name="search"
                                                                placeholder="Search MLS , Address , List Price" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Cities</label><br>
                                                            <select class="select2 select2-multiple" multiple="multiple"
                                                                data-placeholder="Choose ..." id="cities">
                                                                <option value="">Select CIty</option>
                                                                @if ($cities)
                                                                    @foreach ($cities as $city)
                                                                        <option value="{{ @$city->City }}">
                                                                            {{ @$city->City }}</option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Min price</label><br>

                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="min_price">
                                                                <option value="">Select Min price</option>
                                                                @if ($price)
                                                                    @foreach ($price as $m_price)
                                                                        <option value="{{ @$m_price }}">
                                                                            {{ @$m_price }}</option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Max price</label><br>
                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="max_price">
                                                                <option value="">Select Max price</option>
                                                                @if ($price)
                                                                    @foreach ($price as $max_price)
                                                                        <option value="{{ @$max_price }}">
                                                                            {{ @$max_price }}</option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Bedrooms</label><br>

                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="beds">
                                                                <option value="">Beds</option>

                                                                <option value="1">1+</option>
                                                                <option value="2">2+</option>
                                                                <option value="3">3+</option>
                                                                <option value="4">4+</option>
                                                                <option value="5">5+</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Bathrooms</label><br>

                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="Baths">
                                                                <option value="">Baths</option>

                                                                <option value="1">1+</option>
                                                                <option value="2">2+</option>
                                                                <option value="3">3+</option>
                                                                <option value="4">4+</option>
                                                                <option value="5">5+</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Area min size</label><br>

                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="Area_min_size">
                                                                <option value="">Min size</option>

                                                                @for ($min_sq = 500; $min_sq <= 4000; $min_sq += 500)
                                                                    <option value="{{ $min_sq }}">
                                                                        {{ $min_sq }} Sq.Ft.</option>
                                                                @endfor


                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Area max size</label><br>

                                                            <select class="form-control" data-placeholder="Choose ..."
                                                                id="Area_max_size">
                                                                <option value="">max size</option>

                                                                @for ($min_sq = 500; $min_sq <= 4000; $min_sq += 500)
                                                                    <option value="{{ $min_sq }}">
                                                                        {{ $min_sq }} Sq.Ft.</option>
                                                                @endfor


                                                            </select>

                                                        </div>

                                                    </div>
                                                    <div class="col-md-10">
                                                        <div id="accordion">
                                                            <div class="card">
                                                                <div class="card-header feature_collapse"
                                                                    data-toggle="collapse" href="#collapseOne">
                                                                    <b>Features</b> <i
                                                                        class="fas fa-sort-down down_icon"></i>
                                                                </div>
                                                                <div id="collapseOne" class="collapse"
                                                                    data-parent="#accordion">
                                                                    <div class="card-body feature_body">
                                                                        <div class="row">
                                                                            @foreach ($features_master as $key => $feature)
                                                                                <div class="col-md-3">
                                                                                    <label
                                                                                        for="features{{ $key }}">
                                                                                        <input type="checkbox"
                                                                                            class="features"
                                                                                            id="features{{ $key }}"
                                                                                            value="{{ $feature->id }}" />
                                                                                        {{ $feature->Features }}</label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="col-md-1 pt-0">
                                                        <button type="submit" class="btn btn-block btn-purple commonfilter"
                                                            id="searchbtn">
                                                            <i class="fa fa-search"></i>
                                                        </button>

                                                    </div>
                                                    <div class="col-md-1 pt-0">
                                                        <button id="filtereset" type="button"
                                                            class="btn btn-block btn-danger clearall_btn"><i
                                                                class="fa fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row pt-2 Filterrow" style="display: none;"> --}}
                                    {{-- <div class="col-md-3"> --}}
                                    {{-- <div class="form-group "> --}}
                                    {{-- <div class="col-md-12"> --}}
                                    {{-- <label>Search MLS , Address , AgentMLSId ..</label> --}}
                                    {{-- <input type="text" id="userinput1" class="form-control border-purple search_input_one " name="search" placeholder="Search MLS , Address , AgentMLSId , OfficeMLSId" value=""> --}}
                                    {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- <div class="col-md-6"> --}}
                                    {{-- <div class="row"> --}}
                                    {{-- <div class="col-md-6"> --}}
                                    {{-- <div class="form-group"> --}}
                                    {{-- <div class="col-md-12"> --}}
                                    {{-- <label>cities</label><br> --}}
                                    {{-- <select class="select2 select2-multiple" multiple="multiple" multiple data-placeholder="Choose ..." id="cities"> --}}
                                    {{-- @if ($cities) --}}
                                    {{-- @foreach ($cities as $city) --}}
                                    {{-- <option value="{{@$city->City}}">{{@$city->City}}</option> --}}
                                    {{-- @endforeach --}}
                                    {{-- @endif --}}

                                    {{-- </select> --}}
                                    {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- </div> --}}

                                    {{-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>Heat</label><br>
                                                            @if ($heating)
                                                            @foreach ($heating as $heat)
                                                               <input type="checkbox" name="heating[]" value="{{@$heat->Heating}}" class="heating"> {{@$heat->Heating}} <br/>
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div> --}}

                                    {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- <div class="col-2">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Cool</label><br>
                                                    @if ($cooling)
                                                    @foreach ($cooling as $cool)
                                                       <input type="checkbox" name="cool[]" value="{{@$cool->Cooling}}" class="heating"> {{@$cool->Cooling}} <br/>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div> --}}
                                    {{-- <div class="col-2">
                                             <div class="form-group">
                                                 <div class="col-md-12">
                                                     <label>Pool</label><br>
                                                     @if ($pool)
                                                     @foreach ($pool as $p)
                                                        <input type="checkbox" name="pool[]" value="{{@$p->PoolFeatures}}" class="heating"> {{@$p->PoolFeatures}} <br/>
                                                     @endforeach
                                                     @endif
                                                 </div>
                                             </div>
                                         </div> --}}
                                    {{-- <div class="col-lg-1 col-sm-1 col-xs-12"> --}}
                                    {{-- <button type="submit" class="btn btn-block btn-purple  commonfilter" id="searchbtn"> --}}
                                    {{-- <i class="fa fa-search"></i> --}}
                                    {{-- </button> --}}
                                    {{-- </div> --}}
                                    {{-- <div class="col-lg-1 col-sm-1 col-xs-12"> --}}
                                    {{-- <button id="filtereset" type="button" class="btn btn-block btn-danger clearall_btn">Clear --}}
                                    {{-- </button> --}}
                                    {{-- </div> --}}
                                </div>
                                <table id="datatableses" class="table text-center table-bordered mt-2">
                                    <div class="spinner-border text-purple m-2" id="spinLoader" role="status"
                                        style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <thead>
                                        <tr>
                                            <th >Id</th>
                                            <th >MLS #</th>
                                            <th >Status</th>
                                            <th >Address</th>
                                            <th >Price</th>
                                            <th >List Price/Sqft</th>
                                            {{-- <th class="th-wd-60">SqFt Living</th> --}}
                                            <th >Total Bedrooms</th>
                                            <th >Total Bathrooms</th>
                                            <th >Type</th>
                                            <th >City </th>
                                            <th >Image </th>
                                            <th >Featured </th>
                                            <th >Post Property</th>

                                        </tr>
                                    </thead>
                                    <tbody id="PropertyDataList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->
    </div>
    <!-- modal -->
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="model-head">
                    <button type="button" class="close bg-danger text-white" data-dismiss="modal" aria-label="Close"
                        style="right:20;right: 20 !important;margin-right: 10px;margin-top: 5px;border-radius: 30px;padding: 2px 8px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- carousel -->
                    <div id='carouselExampleIndicators' class='carousel slide' data-ride='carousel'>
                        <ol class='carousel-indicators'>
                            <li data-target='#carouselExampleIndicators' data-slide-to='0' class='active'></li>
                            <li data-target='#carouselExampleIndicators' data-slide-to='1'></li>
                            <li data-target='#carouselExampleIndicators' data-slide-to='2'></li>
                        </ol>
                        <div class='carousel-inner'>
                            <div class='carousel-item active'>
                                <img class='img-size'
                                    src='https://images.unsplash.com/photo-1485470733090-0aae1788d5af?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1391&q=80'
                                    alt='First slide' />
                            </div>
                            <div class='carousel-item'>
                                <img class='img-size'
                                    src='https://images.unsplash.com/photo-1491555103944-7c647fd857e6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80'
                                    alt='Second slide' />
                            </div>
                            <div class='carousel-item'>
                                <img class='img-size'
                                    src='https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80'
                                    alt='Second slide' />
                            </div>
                        </div>
                        <a class='carousel-control-prev' href='#carouselExampleIndicators' role='button' data-slide='prev'>
                            <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                            <span class='sr-only'>Previous</span>
                        </a>
                        <a class='carousel-control-next' href='#carouselExampleIndicators' role='button' data-slide='next'>
                            <span class='carousel-control-next-icon' aria-hidden='true'></span>
                            <span class='sr-only'>Next</span>
                        </a>
                    </div>
                </div>
                {{-- <div class="modal-footer"> --}}
                {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                {{-- </div> --}}
            </div>
        </div>
    </div>
@endsection
@section('pageLevelJS')

    <script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
    <!-- Init js-->
    <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

    <!--Morris Chart-->
    <!-- <script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>
    <!-- third party js -->
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.html5.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.flash.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.print.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.keyTable.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.select.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/pdfmake/vfs_fonts.js"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>
    <!-- Dashboard init js-->
    <!--  <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script> -->
    <script type="text/javascript">
        $(document).ready(function() {
            // DataTable
            var dataTable = $('#datatableses').DataTable({

                processing: true,
                serverSide: true,
                searching: false,
                "bLengthChange": false,
                {{-- ajax: "{{route('employees.getEmployees')}}", --}} 'ajax': {
                    'url': '{{ url('api/v1/agent/getdata') }}',
                    'data': function(data) {
                        // Read values
                        var types = [];
                        $.each($("input[name='status']:checked"), function() {
                            types.push($(this).val());
                        });
                        data.searchdata = $('#Search').val();
                        data.type = $('#PropertyType').val();
                        data.search = $('#userinput1').val();
                        data.cities = $('#cities').val();
                        data.min_price = $('#min_price').val();
                        data.max_price = $('#max_price').val();
                        data.BedroomsTotal = $('#beds').val();
                        data.BathroomsFull = $('#Baths').val();
                        data.Area_min_size = $('#Area_min_size').val();
                        data.Area_max_size = $('#Area_max_size').val();
                        data.PropertySubType = $('#PropertySubType').val();
                        var features = [];
                        $(".features:checked").each(function() {
                            features.push($(this).val());
                        });
                        data.feature_filter = features;
                        // Append to data
                        // data.searchByGender = gender;
                        // data.searchByName = name;
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'ListingId'
                    },
                    {
                        data: 'MlsStatus'
                    },
                    {
                        data: 'StandardAddress'
                    },
                    {
                        data: 'ListPrice'
                    },
                    {
                        data: 'LotSizeSquareFeet'
                    },
                    // { data:'Sp_dol'},
                    {
                        data: 'BedroomsTotal'
                    },
                    {
                        data: 'BathroomsFull'
                    },
                    {
                        data: 'PropertyType'
                    },
                    {
                        data: 'City'
                    },
                    {
                        data: 'ThumbnailImage'
                    },
                    {
                        data: 'isFav'
                    },
                    {
                        data: 'post'
                    },

                ],
            });
            // $(document).on('click','#clearAll',function(){
            //     // $('#datatableses').DataTable().destroy();
            //     $(".status").prop('checked', false);
            //     $(".status").removeAttr('checked');
            //     $('.AgentType').removeClass('active');
            //     dataTable.draw();
            // });
            $(document).on('change', '.status', function() {
                // $('#datatableses').DataTable().destroy();
                $('.allAgentType').toggle();
                dataTable.draw();

            });

            // $(document).on('change','#PropertyType',function(){
            //     // alert(1);
            //     dataTable.draw();
            // });
            function getTable() {
                $('#datatableses').dataTable({
                    "bLengthChange": false,
                    "bFilter": true,
                    "searching": false,
                    "bSortable": false,
                });
            }
            $(document).on('click', '.filterBtn', function() {
                $('.Filterrow').toggle("slow");
            });
            $(document).on('click', '#searchbtn', function() {
                dataTable.draw();
            });
            $(document).on('click', '#filtereset', function() {
                $('#userinput1').val('');
                $("#cities").select2("val", "0");
                $("select option").prop("selected", false);
                // $("checkbox").prop("checked", false);
                $('input[type="checkbox"]').prop('checked', false);
                dataTable.draw();
            });
            // $(document).on('change','#PropertyType',function(){
            //     dataTable.draw();
            // });

        });


        function property_featured(propId,state) {
            let label = [];
            let datas = [];
            if(state){
                $("#featuredProp"+propId).removeClass("fas");
                $("#featuredProp"+propId).addClass("far");
            }else{
                $("#featuredProp"+propId).removeClass("far");
                $("#featuredProp"+propId).addClass("fas");
            }

            let url = window.location.origin;
            const payload = {
                "_token": '{{ csrf_token() }}',
                "listingId": propId,
                "agentId": $("#agentId").val(),
            }
            $.ajax({
                url: "/api/v1/agent/property/propFeature",
                data: payload,
                type: "post",
                success: function(json) {
                    // console.log("response==>>", json.success);
                    // alert(json.message);
                    // return;
                    if(json.success){
                        toastr.success(json.message,'Success');
                    }else{
                        toastr.error('Opps!');
                    }
                    window.location.reload();
                },
                error: function(e) {
                    console.log("error", e);
                }
            });
        }

    </script>
    <script type="text/javascript">
        function getTable() {
            $('#datatableses').dataTable({
                "bLengthChange": false,
                "bFilter": true,
                "searching": false,
                "bSortable": false,
            });
        }
        $(document).on('click', '.PropertyModelBtn', function() {
            var id = $(this).data('id');
            var data = {
                'id': id,
                "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/property/images') }}',
                data: data,
                success: function(response) {
                    console.log('response', response.inner);
                    $('.carousel-inner').html(response.inner);
                    $('.carousel-indicators').html(response.indicators);
                    $('#largeModal').modal('show');
                },
                error: function(status, error) {
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    if (status.status == 401) {
                        $.each(errors.error, function(i, v) {
                            msg_error += v[0] + '!</br>';
                        });
                        toastr.error(msg_error, 'Opps!');
                    } else {
                        toastr.error(errors.message, 'Opps!');
                    }
                }
            });

        })

    </script>
@endsection
