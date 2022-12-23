@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')

<!-- third party css -->
<style type="text/css">
    .avatar-xl {
        height: 10rem;
        width: 10rem;
    }
    .br-1{
        border-right: 1px solid #dee2e6
    }
</style>
<style type="text/css">
    .select2{
        height: calc(1.5em + .9rem + 2px) !important;
    }
    .form-control{
        min-height: calc(1.5em + .9rem + 2px) !important;
    }
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + .9rem + 2px) !important;
        padding: .4rem !important;
    }
    .select2-search__field{
        height: calc(.9rem + ..8rem + 2px) !important;
        /*padding: .4rem !important;*/
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-weight: bold;
        margin-right: 2px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #aaa;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: default;
        float: left;
        margin-right: 5px;
        margin-top: 5px;
        padding: 0 5px;
    }
    .select2 {
         height: auto !important;
    }
</style>
<link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        <div class="card">
                            <!-- <div class="card-header">
                                <h4 class="card-title card-title-heading">
                                    Agent List
                                </h4>

                            </div> -->
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{ asset('assets') }}/agent/images/profile.jpg" class="rounded-circle avatar-xl img-thumbnail" alt="profile-image">
                                    <h5>{{@$agent->ListAgentFullName}}</h5>
                                    <h6>Address</h6>
                                    <hr>
                                        <!-- <h6>Upload a different photo...</h6>
                                        <input type="file" class="text-center center-block file-upload"> -->
                                </div>
                                <div class="col-12">
                                    <h5>Email</h5>
                                    <p>{{@$agent->ListAgentEmail}}</p>
                                </div>
                                <div class="col-12">
                                    <h5>Phone</h5>
                                    <p>{{@$agent->ListAgentDirectPhone}}</p>
                                </div>
                                <div class="col-12">
                                    <h5>Address</h5>
                                    <p>{{@$agent->city}} , {{@$agent->County}} , {{@$agent->agent_state}}</p>
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d57468.768057813184!2d-80.17516095407647!3d25.810237305257054!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d9a6172bfeddb9%3A0x37be1741259463eb!2sMiami%20Beach%2C%20FL%2C%20USA!5e0!3m2!1sen!2sin!4v1628837470656!5m2!1sen!2sin" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card">
                            <!-- <div class="card-header">
                                <h4 class="card-title card-title-heading">
                                    Agent List
                                </h4>

                            </div> -->
                            <div class="card-body p-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                      <a class="nav-link " data-toggle="tab" href="#Timeline">Timeline</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link active" data-toggle="tab" href="#Profile">Profile</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" data-toggle="tab" href="#Setting">Setting</a>
                                    </li>
                                  </ul>

                                  <!-- Tab panes -->
                                  <div class="tab-content">
                                    <div id="Timeline" class="container tab-pane "><br>
                                      <h5>HOME</h5>
                                      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                    </div>
                                    <div id="Profile" class="container tab-pane active"><br>
                                        <div class="row">
                                            <div class="col-3 br-1">
                                                <h5>Full Name</h5>
                                                <p>{{@$agent->ListAgentFullName}}</p>
                                            </div>
                                            <div class="col-3 br-1">
                                                <h5>Email</h5>
                                                <p>{{@$agent->ListAgentEmail}}</p>
                                            </div>
                                            <div class="col-3 br-1">
                                                <h5>Mobile</h5>
                                                <p>{{@$agent->ListAgentDirectPhone}}</p>
                                            </div>
                                            <div class="col-3">
                                                <h5>Location</h5>
                                                <p>{{@$agent->city}} , {{@$agent->County}} , {{@$agent->agent_state}}</p>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                    <div id="Setting" class="container tab-pane fade">
                                        <div class="row mb-1">
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <select class="select2 form-control" id="State" data-placeholder="Select State">
                                                    @if($state)
                                                    @foreach($state as $k)
                                                    <option value="{{@$k->state}}">{{@$k->state}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <span class="text-danger" id="statevalidate" ></span>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <select class="select2 form-control" id="County" data-placeholder="Select County">
                                                    
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <select class="select2 form-control" id="City" data-placeholder="Select City">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-2 mb-1">
                                            <div class="col-12">
                                                <button class="btn btn-purple" id="zipCodebtn">Get Zipcodes</button>
                                            </div>
                                            <br>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-12">
                                                <h4>OR / AND</h4>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <h3>Zip Codes</h3>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4  text-right">
                                                <button class="btn btn-warning btn-xs">Remove all</button>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4  text-right">
                                                <button class="btn btn-purple btn-xs">add all</button>
                                            </div>
                                        </div>
                                        <div class="row mb-1">
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>Requested ZipCodes</label>
                                                <select class="form-control" multiple>
                                                    @if($agent->ZipRequested)
                                                    <option value="{{@$agent->ZipRequested}}" selected>{{$agent->ZipRequested}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>Assigned ZipCodes</label>
                                                <select class="form-control" multiple id="assignedZipcodes">
                                                    
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>All ZipCodes</label>
                                                <select class="form-control" multiple id="allZipCodes">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h3>Specialization</h3>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>Requested Specialization</label>
                                                <?php $arr=explode(",", $agent->SpecializationRequested);
                                                $SpecializationRequested=array_values(array_filter($arr));?>
                                                <select class="form-control" multiple>
                                                    @if($SpecializationRequested)
                                                    @foreach($SpecializationRequested as $k)
                                                    <option value="{{@$k}}">{{@$k}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <?php $assign=[$agent->Specialization0,$agent->Specialization1,$agent->Specialization2,$agent->Specialization3,$agent->Specialization4,$agent->Specialization5,$agent->Specialization6,$agent->Specialization7];
                                            
                                            $assign=array_values(array_filter($assign));
                                            // print_r($assign);?>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>Assigned Specialization</label>
                                                <select class="form-control" multiple id="assignSpecialization">
                                                    @if($assign)
                                                    @foreach($assign as $k)
                                                    <option value="{{@$k}}" selected>{{@$k}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-4">
                                                <label>All Specialization</label>
                                                <select class="form-control" multiple id="allSpecialization">
                                                    @if($all_spc)
                                                    @foreach($all_spc as $k)
                                                    <option value="{{@$k}}" <?php if(in_array($k,$assign)){ echo 'selected';}?> >{{@$k}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->min0}}" placeholder="1">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max0}}" placeholder="5000">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->min1}}" placeholder="1">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max1}}" placeholder="1000">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->min2}}" placeholder="Min Price For RINC">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max2}}" placeholder="Max Price For RINC">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->min3}}" placeholder="Min Price For LAND">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max3}}" placeholder="Max Price For LAND">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->min4}}" placeholder="Min Price For BZOP">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max4}}" placeholder="Max Price For BZOP">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" value="{{@$agent->min5}}" name="" class="form-control" placeholder="Min Price For COMM">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max5}}" placeholder="Max Price For COMM">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" value="{{@$agent->min6}}" name="" class="form-control" placeholder="Min Price For COML">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max6}}" placeholder="Max Price For COML">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" value="{{@$agent->min7}}" name="" class="form-control" placeholder="1">
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-6">
                                                <input type="number" name="" class="form-control" value="{{@$agent->max7}}" placeholder="51500">
                                            </div>
                                        </div>
                                        <button class="btn btn-success">Save Settings</button>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->



    </div>
@endsection
@section('pageLevelJS')
<script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
        <!-- Init js-->
        <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
        <script type="text/javascript">
            $(document).on('change','#State',function(){
                getCounty();
            });
            function getCounty(){
                var state=$('#State').val();
                var data = {
                'state': state,
                "_token": "{{ csrf_token() }}"
                };
                $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/profile/get-county")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    html='<option value="" disabled selected>Select county</option>';
                    $.each(response.county, function(key, val) {
                        html+='<option value="'+val.county+'">'+val.county+'</option>';
                    
                    });
                    $('#County').html(html);
                    console.log(html);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
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
        }
            // onchange County
            $(document).on('change','#County',function(){
                // alert(1);
                getCity();
            });
            function getCity(){
                var county=$('#County').val();
                var data = {
                'county': county,
                "_token": "{{ csrf_token() }}"
                };
                $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/profile/get-city")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    html='<option value="" disabled selected>Select City</option>';
                    $.each(response.city, function(key, val) {
                        html+='<option value="'+val.city+'">'+val.city+'</option>';
                    
                    });
                    $('#City').html(html);
                    console.log(html);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
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
        }
        $(document).on('click','#zipCodebtn',function(){
            var state=$('#State').val();
            console.log(state);
            if($.trim(state).length == 0)
            {
                $('#statevalidate').html('Please select state');
                return false;
            }
            getZip();
        });
        function getZip(){
            var state=$('#State').val();
            var county=$('#County').val();
            var city=$('#City').val();
                var data = {
                    'state':state,
                    'county': county,
                    'city': city,
                    "_token": "{{ csrf_token() }}"
                };
                $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/profile/get-zip")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    html='';
                    $.each(response.zip, function(key, val) {
                        html+='<option value="'+val.zipcode+'">'+val.zipcode+'</option>';
                    
                    });
                    $('#allZipCodes').html(html);
                    console.log(html);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
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
        }
        $(document).on('change','#allZipCodes',function(){
            var allzip=$('#allZipCodes').val();
            console.log(allzip);
            html='';
            $.each(allzip, function(key, val) {
                html+='<option value="'+val+'" selected>'+val+'</option>';
            });
            $('#assignedZipcodes').html(html);
        });
        $(document).on('change','#allSpecialization',function(){
            var allSpecialization=$('#allSpecialization').val();
            // console.log(allzip);
            html='';
            $.each(allSpecialization, function(key, val) {
                html+='<option value="'+val+'" selected>'+val+'</option>';
            });
            $('#assignSpecialization').html(html);
        });
        </script>
@endsection
