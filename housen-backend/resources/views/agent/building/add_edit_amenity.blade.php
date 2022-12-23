@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
     <link href="{{ asset('assets') }}/agent/css/new_style.css" rel="stylesheet" type="text/css" />
     <style type="text/css">
         .spinner-border{
            width: 1rem;
            height: 1rem;
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
                                            <?php if(isset($MasterAmenities)){ echo 'Edit'; }else{ echo 'Add'; }?> Amenity
                                        </h4>
                                        
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/building/amenities-list')}}" class="btn btn-purple">All Amenities</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                     <form class="modal-content" id="amenityDetails" method="post" enctype="form-data/multipart">
                                        @csrf
                                        <!-- Modal Header -->
                                        <!-- Modal body -->
                                        <input type="hidden" name="id" value="{{@$MasterAmenities->id}}">
                                        <div class="modal-body row">
                                            <div class="col-md-6 mt-2">
                                                <label>Amenity Name<span class="required">*</span></label>
                                                <input type="text" class="form-control" name="Name" required="" placeholder="Name" value="{{@$MasterAmenities->Name}}">
                                            </div>
                                            <div class="col-md-6 mt-2 mb-2"></div><br>
                                           
                                            <div class="col-md-6 pt-2">
                                                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 float" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save</button>
                                            </div>
                                        </div>
                                        
                                        
                                        <!-- Modal footer -->
                                        
                                        
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
    <!-- <script src="{{ asset('assets') }}/agent/js/image_script.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("Content").value;
            CKEDITOR.replace('Content');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>
   
    <script type="text/javascript">
       

    </script>
    <!-- Adding Builder Details -->
    <script>
        $(document).on('submit','#amenityDetails',function(e){
            e.preventDefault();
            var form_data = new FormData($('#amenityDetails')[0]);
            $('#rule-btn2').removeClass('d-none');
             $('#SubmitBtn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/building/add_amenity_data")}}',
                data: form_data,
                contentType:false,
                processData:false,
                success: function (response) {
                    // console.log('data',data);
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
    </script>
@endsection
