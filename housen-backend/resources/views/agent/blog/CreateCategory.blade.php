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
        .spinner-border{
            height: 16px;
            width: 16px;
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
                                            <?php if(isset($cat)){ echo 'Edit'; }else{ echo 'Add'; }?> Category
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/blog/categories')}}" class="btn btn-purple">All Categories</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="tempid" value="{{@$cat->id}}">
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Category Name<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="categoryName" name="name" required="" placeholder="Category Name" value="{{@$cat->Name}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Parent<span class="required">*</span></label>
                                                    <select class="select2 form-control" id="PrentId" onchange="changeType()" name="type">
                                                        <option value="0" >Select</option>
                                                        @if($category)
                                                            @foreach($category as $k)
                                                        <option value="{{@$k->id}}" <?php if(isset($cat->ParentId) && !empty($cat->ParentId) && $k->id==$cat->ParentId){ echo "selected";} ?>>{{@$k->Name}}</option>
                                                            @endforeach
                                                            @endif
                                                    </select>
                                                    <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>

                                        <div class="inline-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Alias <span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="Alias" name="Alias" required placeholder="Enter Alias" value="{{@$cat->Alias}}">
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
        var x = document.getElementById("content1").value;
        CKEDITOR.replace('content1');
        CKEDITOR.add;
        CKEDITOR.instances.add_content.setData(x);
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
            var name = $('#categoryName').val();
            var parentId=$('#PrentId').val();
            var Alias=$('#Alias').val();
            var id=$('#tempid').val();
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'Name': name,
                'ParentId':parentId,
                'Alias':Alias,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/blog/add-category")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/blog/categories')}}";
                    },3000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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
    </script>
@endsection
