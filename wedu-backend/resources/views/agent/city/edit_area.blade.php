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
                                            <?php if(isset($area_data)){ echo 'Edit'; }else{ echo 'Add'; }?> Neighbours / Area Page Meta and Content
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/city/')}}" class="btn btn-purple">All City list</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="AreaName" value="{{@$area_data['AreaName']}}">
                                        <input type="hidden" name="" id="CityName" value="{{@$area_data['CityName']}}">
                                        
                                       
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <label for="Subclass">Meta Title<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaTitle" name="metatitle" required="" placeholder="Meta Title" value="{{@$area_data->MetaTitle}}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Subclass">Meta Keyword (use comma for more keywords)<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaKeyword" name="MetaKeyword" required="" placeholder="Meta Keyword" value="{{@$area_data->MetaTags}}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-12">
                                                    <label for="Subclass">Meta Description<span class="required">*</span></label>
                                                    <textarea class="form-control" id="MetaDescription" name="MetaDescription" required="" placeholder="Meta Description">{{@$area_data->MetaDescription}}</textarea>
                                                    
                                                </div>
                                                
                                                <div class="col-md-12"><br>
                                                    <label for="Subclass">Add Content<span class="required">*</span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"> {{@$area_data->Content}}</textarea>
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
           
        </div>
    </div>
@endsection
@section('pageLevelJS')
    <!-- Toastr js -->
   
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
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
            var AreaName = $('#AreaName').val();
            var CityName = $('#CityName').val();
            var MetaTitle=$('#MetaTitle').val();
            var MetaKeyword=$('#MetaKeyword').val();
            var MetaDesc=$('#MetaDescription').val();
            var content=$('#content1').val();
            var agentId=$('#agent_id').val();
            var slug = AreaName.toLowerCase();
            slug = slug.replace(/[^a-zA-Z0-9]+/g,'-');
            var data = {
                'AreaName': AreaName,
                'CityName': CityName,
                'MetaTitle':MetaTitle,
                'MetaTags':MetaKeyword,
                'MetaDescription':MetaDesc,
                'Content':content,
                'AgentId':agentId,
                'Slug':slug,
                'status':'Active',
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/city/update-area")}}',
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
        $("#page_name").keyup(function(){
            var Text = $(this).val();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');
            console.log(Text);
            $("#Url").val('/home/'+Text);
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
@endsection
