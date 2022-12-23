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
                                            <?php if(isset($testimonial)){ echo 'Edit'; }else{ echo 'Add'; }?> Testimonial
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/testimonial')}}" class="btn btn-purple">Testimonials</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="tempid" value="{{@$testimonial->id}}">
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Name<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="name" name="name" required="" placeholder="Name" value="{{@$testimonial->Name}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Description<span class="required">*</span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]">{{@$testimonial->Description}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                       
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Image <span class="required"></span></label>
                                                    <input type="file" class="form-control" id="Alias" name="Alias" placeholder="Enter Alias" onchange='openFile(event)' value="">
                                                    <input type="hidden" id="Image" name="" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <br>
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Image Alt tag<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="ImageAlttag" name="ImageAlttag" placeholder="Image Alt tag" value="{{@$testimonial->ImgTags}}">
                                                </div>
                                            </div>
                                        </div>-->
                                        <br> 
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                        <!-- <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Blog Tags<span class="required"></span></label>
                                                    <input type="text" class="form-control" value="{{@$testimonial->BlogTags}}" id="BlogTags" name="BlogTags" placeholder="Seperated with comma Ex: Tag1,Tag2,Tag3" >
                                                </div>
                                            </div>
                                        </div> -->
                                        <br>
                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
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
        // changeType();
        // function changeType(){
        //     var type = $('#TemplateType').val();
        //     if(type=='email'){
        //         $('#emailContent').show();
        //         $('#smsContent').hide();
        //     } else{
        //         $('#emailContent').hide();
        //         $('#smsContent').show();
        //     }

        // }
        $(document).on('submit','#templateForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var name = $('#name').val();
            var description=$('#content1').val();
            var image=$('#Image').val();
            var agentId=$('#agent_id').val();
            var id=$('#tempid').val();
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'Name': name,
                'Image':image,
                'Description':description,
                'AgentId':agentId
                
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/testimonial/add-edit-testimonial")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/testimonial/')}}";
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
        
        // conversion and live show of image
        var openFile = function(event) {
            var input = event.target;

            var reader = new FileReader();
            reader.onload = function(){
                var dataURL = reader.result;
                var output = document.getElementById('Image');
                output.value = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
        };

    </script>
@endsection
