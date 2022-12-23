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
                                            Edit code
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/pages/')}}" class="btn btn-purple">All Pages</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body text-center">
                                    Not found requested page
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
            var page_name = $('#page_name').val();
            var MetaTitle=$('#MetaTitle').val();
            var MetaKeyword=$('#MetaKeyword').val();
            var MetaDesc=$('#MetaDescription').val();
            var Url=$('#Url').val();
            var id=$('#page_id').val();
            var agentId=$('#agent_id').val();
            var content = $('#content1').val();
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'PageName': page_name,
                'Content': content,
                'MetaTitle':MetaTitle,
                'MetaTags':MetaKeyword,
                'MetaDescription':MetaDesc,
                'PageUrl':Url,
                'AgentId':agentId,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/pages/add-page")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/pages/')}}";
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
            $("#Url").val('/'+Text);
        });


    </script>
@endsection
