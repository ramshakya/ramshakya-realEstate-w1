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
                                            <?php if(isset($blogs)){ echo 'Edit'; }else{ echo 'Add'; }?> Blogs
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/blog/')}}" class="btn btn-purple">All Blogs</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="tempid" value="{{@$blogs->id}}">
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Blog Title<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="BlogTitle" name="name" required="" placeholder="Blog Title" value="{{@$blogs->Title}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Meta Title<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaTitle" name="metatitle" required="" placeholder="Meta Title" value="{{@$blogs->MetaTitle}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Meta Keyword<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaKeyword" name="MetaKeyword" required="" placeholder="Meta Keyword" value="{{@$blogs->MetaKeyword}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Meta Description<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaDescription" name="MetaDescription" required="" placeholder="Meta Description" value="{{@$blogs->MetaDesc}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Url<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="Url" name="Url" required="" placeholder="Url" value="{{@$blogs->Url}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Add Content<span class="required">*</span></label>
                                                    <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"> {{@$blogs->content}}
                                                        {{@$blogs->Content}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Blog Categories <span class="required">*</span></label><br/>
                                                    <?php $categ=[];
                                                    if(isset($blogs->Categories) && !empty($blogs->Categories)){
                                                        $categ=json_decode($blogs->Categories);
//                                                        echo $categ;
                                                    } ?>
                                                    @foreach($category as $k)
                                                        <input type="checkbox" name="categories" value="{{@$k->id}}" <?php if(in_array($k->id,$categ)){ echo "checked";}?>>  {{@$k->Name}} <br/>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Blog Image <span class="required"></span></label>
                                                    <input type="file" class="form-control" id="Alias" name="Alias" placeholder="Enter Alias" onchange='openFile(event)' value="">
                                                    <input type="hidden" id="BlogImage" name="" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Image Alt tag<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="ImageAlttag" name="ImageAlttag" placeholder="Image Alt tag" value="{{@$blogs->ImgTags}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Blog Tags<span class="required"></span></label>
                                                    <input type="text" class="form-control" value="{{@$blogs->BlogTags}}" id="BlogTags" name="BlogTags" placeholder="Seperated with comma Ex: Tag1,Tag2,Tag3" >
                                                </div>
                                            </div>
                                        </div>
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
    <script>
        var x = document.getElementById("add_content").value;
        CKEDITOR.replace('add_content');
        CKEDITOR.add;
        CKEDITOR.instances.add_content.setData(x);
    </script>
    <script type="text/javascript">
        changeType();
        function changeType(){
            var type = $('#TemplateType').val();
            if(type=='email'){
                $('#emailContent').show();
                $('#smsContent').hide();
            } else{
                $('#emailContent').hide();
                $('#smsContent').show();
            }

        }
        $(document).on('submit','#templateForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var Title = $('#BlogTitle').val();
            var MetaTitle=$('#MetaTitle').val();
            var MetaKeyword=$('#MetaKeyword').val();
            var MetaDesc=$('#MetaDescription').val();
            var Url=$('#Url').val();

            var type=[];
            $("input[name='categories']:checked").each(function (i) {
                type[i] = $(this).val();
            });
            var Categories=type;
            var ImgTags=$('#ImageAlttag').val();
            var Content=$('#content1').val();
            // alert(Content);
            var BlogTags=$('#BlogTags').val();
            var id=$('#tempid').val();
            var agentId=$('#agent_id').val();
            if(id==''){
                id=0;
            }
            var BlogImage=$('#BlogImage').val();
            var data = {
                'id':id,
                'Title': Title,
                'MetaTitle':MetaTitle,
                'MetaKeyword':MetaKeyword,
                'MetaDesc':MetaDesc,
                'Url':Url,
                'Categories':Categories,
                'MainImg':BlogImage,
                'ImgTags':ImgTags,
                'Content':Content,
                'BlogTags':BlogTags,
                'AdminId':agentId,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/blog/add-blog")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/blog/')}}";
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
        $("#BlogTitle").keyup(function(){
            var Text = $(this).val();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');
            console.log(Text);
            $("#Url").val(Text);
        });
        // conversion and live show of image
        var openFile = function(event) {
            var input = event.target;

            var reader = new FileReader();
            reader.onload = function(){
                var dataURL = reader.result;
                var output = document.getElementById('BlogImage');
                output.value = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
        };

    </script>
@endsection
