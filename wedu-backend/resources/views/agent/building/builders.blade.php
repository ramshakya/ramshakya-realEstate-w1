@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <style>
        .cursor-pointer{
            cursor: pointer;
        }
        .spinner-border{
            width: 1rem;
            height: 1rem;
        }
        .deleteModal{
        top: 150px!important;
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
                                            All Builder list
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/building/add-edit-builder')}}" class="btn btn-purple">Add New</a>
                                        <a href="{{url('agent/building/')}}" class="btn btn-purple">Pre Construction</a>
                                          <a href="{{url('agent/building/amenity-list')}}" class="btn btn-purple">Amenity</a>
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
                                                <th colspan="">Builder Name</th>
                                                <th colspan="">Address</th>
                                                <th colspan="">City</th>
                                                <th colspan="">Action</th> 
                                            </tr>
                                            </thead>
                                            <tbody id="datavalue">
                                               
                                            </tbody>
                                        </table>
                                    </div>


                                </div>
                            </div>


                        </div>

                    </div>

                </div>
            </section>
        </div> <!-- container -->

    </div> <!-- content -->
    </div>
    </div>
    
<!-- for deleting items -->
<!-- delete confirmation model  -->
  <div class="modal fade" id="delete_data">
    <div class="modal-dialog modal-sm">
      <div class="modal-content deleteModal">
      
        <!-- Modal Header -->
       
        
        <!-- Modal body -->
        <div class="modal-body">
          This action can wipe your data! Do you want to proceed?<br><br>
          Note: The entire project of this builder will have to be given to another builder.
          <form class="modal-content" id="builderDetails" method="post" enctype="form-data/multipart">
            @csrf
            <label>Select builder</label>
            <select class="form-control" name="change_builder_id" id="All_Builder" required="">
                <option value="">Select</option>
            </select>
            <input type="hidden" name="id" id="builerId">
            <input type="hidden" name="id" id="tableName">
        
              <div class="text-center mt-2">
                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                 <span class="sr-only">Loading...</span>
               </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle "></i> Proceed</button>
                <!-- <button type="submit" class="btn btn-primary" id="SubmitBtn">Proceed</button> -->
                &nbsp;&nbsp;&nbsp;&nbsp;
              <a href="#" class="btn btn-secondary " data-dismiss="modal">Cancel</a></div>
          </form>
        </div>
        
        <!-- Modal footer -->
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div> -->
        
      </div>
    </div>
  </div>
  <!-- end -->
@endsection
@section('pageLevelJS')
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    
    <script type="text/javascript">
       $(document).ready(function(){
            var dataTable = $('#datatableses').DataTable({
                processing: true,
                serverSide: true,
                searching : false,
                bLengthChange: false,
                'ajax': {
                    'type':'POST',
                    'url':'{{url("api/v1/agent/building/get_builder_data")}}',
                    'data': function(data){
                    }
                },
                columns: [
                    { data:'id'},
                    { data:'BuilderName'},
                    { data:'Address'},
                    { data:'City'},
                    { data:'Action'}
                ],
            });
        });
    </script>
    <script type="text/javascript">
    function get_delete_value(delete_id,table_name)
          {
                $('#builerId').val(delete_id);
                $('#tableName').val(table_name);
               var data={
                        'id':delete_id
                   };
               $.ajax({
                    type: "POST",
                    url: '{{url("api/v1/agent/building/get_builder")}}',
                    data:data,
                    success: function (response) {
                        var arr=JSON.parse(response);
                        var option='<option value="">Select</option>';
                         for (var i = 0; i < arr.length; i++) {
                            var id = arr[i]['id'];
                            // console.log(id);
                            option+= '<option value='+arr[i]['id']+'>'+arr[i]['BuilderName']+'</option>';
                         }
                         $('#All_Builder').html(option);
                    },
                });
          }
   $(document).on('submit','#builderDetails',function(e){
            e.preventDefault();
                var delete_id=$('#builerId').val();
                var table_name=$('#tableName').val();
                var asign_builder=$('#All_Builder').val();
                $('#rule-btn2').removeClass('d-none');
                $('#SubmitBtn').attr('disabled', true);
                var data = {
                    'id':delete_id,
                    'tableName':table_name,
                    'asign_builder':asign_builder,
                    "_token": "{{ csrf_token() }}"
                    }
            $.ajax({
              url:'{{url("api/v1/agent/delete-data")}}',
              type:"POST",
              data:data,
              success: function (response) {
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/menu/menuBuilder')}}";
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
