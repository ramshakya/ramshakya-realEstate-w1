@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
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
                                            All Predefined Pages
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/pages/create-predefine-page')}}" class="btn btn-purple">Add New Page</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses">
                                            <thead>
                                            <tr>
                                                <th colspan="">Sr. No</th>
                                                <th colspan="" >Page Name</th>
                                                <th colspan="" >Slug</th>
                                                <th colspan="" >Seo Title</th>
                                                <th colspan="" >Action</th>
                                                
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1;?>
                                            @if($page_list)
                                                @foreach($page_list as $page)
                                                    <tr>
                                                        <td style="width:50px;">{{$page->id}}</td>
                                                        <td class="hidden-xs">{{$page->PageName}}</td>
                                                        <td class="max-texts border ">{{$page->PageUrl}}</td>
                                                        <td class="max-texts border ">{{$page->MetaTitle}}</td>
                                                       
                                                        <td><a href="{{url('agent/pages/create-predefine-page/'.$page->id)}}" class="text-info" title="Edit"><i class="fa fa-edit"></i></a>&nbsp;&nbsp; <a href="#" onclick="get_delete_value('{{$page->id}}}','predefined_page')" data-toggle="modal" data-target="#delete_data" title="Delete" class="text-danger"><i class="fa fa-trash"></i></a></td>
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
        </div> <!-- container -->

    </div> <!-- content -->
    </div>
    </div>

<!-- delete confirmation model  -->
  <div class="modal fade" id="delete_data">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
      
        <!-- Modal Header -->
       
        
        <!-- Modal body -->
        <div class="modal-body">
          This action can wipe your data! Do you want to proceed?<br><br>
          <div class="text-center"><a href="javascript:void();" class="btn btn-primary" id="anchor_for_delete" onclick="delete_data(this.id)">yes</a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="#" class="btn btn-secondary" data-dismiss="modal">No</a></div>
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
    <script>
        $('#datatableses').dataTable( {
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 20,
        } );

        function get_delete_value(delete_id,table_name)
          {
               document.getElementById('anchor_for_delete').setAttribute('data-id',delete_id); 
               document.getElementById('anchor_for_delete').setAttribute('data-table',table_name); 
          }
    function delete_data(clickId)
            {
                var delete_id=document.getElementById(clickId).getAttribute('data-id');
                var table_name=document.getElementById(clickId).getAttribute('data-table');
                var data = {
                    'id':delete_id,
                    'tableName':table_name
            }
            $.ajax({
              url:'{{url("api/v1/agent/delete")}}',
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
                    $('#add_page').attr('disabled', false);
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
