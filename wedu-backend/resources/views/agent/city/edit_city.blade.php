@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        /*.btn-outlet-warning {
            color: #fff;
            background-color: #f16e00;
            border-color: #f16e00;
        }*/
        .btn-outline-warning {
            color: #f16e00;
            border-color: #f16e00;
        }
        .btn-outline-warning:hover {
            color: #fff;
            background-color: #f16e00;
            border-color: #f16e00;
        }
        .featured{
            font-size: 20px;
            cursor: pointer;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <section id="justified-bottom-border">
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            <?php if(isset($city_data)){ echo 'Edit'; }else{ echo 'Add'; }?> City Page Meta and Content
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/city/')}}" class="btn btn-purple">All Cities</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="city_name" value="{{@$city_data['CityName']}}">
                                        
                                       
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <label for="Subclass">Meta Title<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaTitle" name="metatitle" required="" placeholder="Meta Title" value="{{@$city_data->MetaTitle}}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Subclass">Meta Keyword (use comma for more keywords)<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaKeyword" name="MetaKeyword" required="" placeholder="Meta Keyword" value="{{@$city_data->MetaTags}}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <label for="Subclass">Meta Description<span class="required">*</span></label>
                                                    <textarea class="form-control" id="MetaDescription" name="MetaDescription" required="" placeholder="Meta Description">{{@$city_data->MetaDescription}}</textarea>
                                                    
                                                </div>
                                                 <div class="col-md-6 row">
                                                    <div class="col-md-6">

                                                        <label for="Subclass">City Image<span class="required">*</span></label>
                                                        <label ></label>

                                                        <input type="file" class="form-control" id="Alias" name="Alias" placeholder="Enter Alias" onchange='openFile(event)' value="">
                                                        <input type="hidden" id="CityImage" name="" value="">
                                                    </div>
                                                    <div class="col-md-6 mt-2">
                                                        @if(@$city_data->Image)
                                                            <img src="{{$city_data->Image}}" width="280px">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-12"><br>
                                                    <label for="Subclass">Add Content<span class="required">*</span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"> {{@$city_data->Content}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                         <div class="inline-group">
                                          <div class="row ">
                                                
                                            </div>
                                        </div>
                                        <br>
                                        
                        
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                        
                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate"id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- content -->
                </div>
            </section>
             <section id="justified-bottom-border">

                <div class="row match-height">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Area / Neighbours
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <!-- <a href="{{url('agent/pages/create-page')}}" class="btn btn-purple">Add Page</a> -->
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th colspan="">Sr. No</th>
                                                <th colspan="" >Name</th>
                                                <th colspan="" >Title</th>
                                                <th colspan="" >Seo tags</th>
                                                <th colspan="" >Description</th>
                                                <th colspan="" >Action</th>
                                                <th colspan="" >Mark as Featured</th>
                                                
                                            </tr>
                                            </thead>
                                            <tbody id="">
                                            <?php $area_list = [];?>
                                            @if($area_list)
                                                @foreach($area_list as $key=> $area)
                                                    <tr>
                                                        <td style="width:50px;">{{$key+1}}</td>
                                                        <td class="hidden-xs">{{$area->BuildingAreaSource}}</td>
                                                        <td class="max-texts border ">--</td>
                                                        <td class="max-texts border ">--</td>
                                                       
                                                        <td><a href="{{url('agent/city/edit-area/'.$area->BuildingAreaSource)}}" class="text-info" title="Edit"><i class="fa fa-edit"></i></a></td>
                                                        <td><i class="far fa-heart featured" ></i></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>


                                </div>
                            </div>


                        </div>

                    </div>

                </div>
            </section>
            
        </div>
    </div>
@endsection
@section('pageLevelJS')
    <!-- Toastr js -->
   
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
     <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("content1").value;
            CKEDITOR.replace('content1');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>

   
    <script type="text/javascript">
       
        $(document).on('submit','#templateForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var city_name = $('#city_name').val();
            var MetaTitle=$('#MetaTitle').val();
            var MetaKeyword=$('#MetaKeyword').val();
            var MetaDesc=$('#MetaDescription').val();
            var content=$('#content1').val();
            var agentId=$('#agent_id').val();
            var slug = city_name.toLowerCase();
            slug = slug.replace(/[^a-zA-Z0-9]+/g,'-');
            var image=$('#CityImage').val();
            var data = {
                'CityName': city_name,
                'MetaTitle':MetaTitle,
                'MetaTags':MetaKeyword,
                'MetaDescription':MetaDesc,
                'Content':content,
                'AgentId':agentId,
                'Slug':slug,
                'Image':image,
                'status':'Active',
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/city/update-city")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/pages/')}}";
                        location.reload();
                    },2000);
                },
                error:function(status,error){
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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
        });
       
        var openFile = function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function(){
                var dataURL = reader.result;
                var output = document.getElementById('CityImage');
                output.value = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
        };

    </script>
    <script>
        $(document).ready(function(){

            var filter_city =$('#city_name').val(); 
            var dataTable = $('#datatableses').DataTable({
                processing: true,
                serverSide: true,
                searching : false,
                "pageLength": 5,
                "bLengthChange": false,
                'ajax': {
                    'type':'POST',
                    'url':'{{url("api/v1/agent/city/get-area")}}',
                    'data': function(data){
                        data.filter_city=filter_city;
                        data.agentId=$('#agent_id').val();
                    }
                },
                columns: [
                    { data:'id'},
                    { data:'AreaName'},
                    { data:'Title'},
                    { data:'Seo_Tags'},
                    { data:'Description'},
                    { data:'Action'},
                    { data:'Featured'}, 
                ],
            });
        });

        
    </script>
    <script type="text/javascript">
        function area_featured(Feature_val,AreaName)
        {
            var agentId=$('#agent_id').val();
            var CityName=$('#city_name').val();
            var data = {
                'CityName': CityName,
                'Featured':Feature_val,
                'AreaName':AreaName,
                'AgentId':agentId,
                "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/city/area-featured")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/pages/')}}";
                        location.reload();
                    },2000);
                },
                error:function(status,error){
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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
    </script>
@endsection
