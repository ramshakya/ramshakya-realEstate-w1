@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <link rel="stylesheet"
          href="{{ asset('assets') }}/agent/plugins/bower_components/dropify/dist/css/dropify.min.css">
    <link href="{{ asset('assets') }}/plugins/bower_components/bootstrap-select/bootstrap-select.min.css"
          rel="stylesheet"/>
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0">Add Events</h4>
                            </div>
                            <div class="col-12">
                                <div class="card-body">
                                    <form class="pro-add-form" id="ImportForm" method="POST" action=""  enctype="multipart/form-data">
                                        <input type="hidden" name="AdminId" value="{{@$AdminId}}" />
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-6 form-group">
                                                    <label class="control-label">Event Title</label>
                                                    <input type="text" class="form-control" id="EventTitle" name="EventTitle" value="{{@$editEvent->EventTitle}}" required="" placeholder="Event Title"/>
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label class="control-label">Event Name</label>
                                                    <input type="text" class="form-control" id="EventName" name="EventName" value="{{@$editEvent->EventName}}" required="" placeholder="Event Name" />
                                                </div>
                                            </div>
                                            <div class="row ">
                                                <div class="col-md-6 form-group">
                                                    <label class="control-label">Colors</label>
                                                    <input type="color" class="form-control" id="Color" name="Color" value="{{@$editEvent->Color}}"  />
                                                </div>

                                            </div>
                                           <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 pl-1 pr-1" name="importbtn" id="SubmitBtn" style="width:100%;">  <div class="spinner-border d-none" role="status" id="rule-btn2">--}}
                                                            <span class="sr-only">Loading...</span>
                                                        </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i>  Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form><br><br/>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0"> Events</h4>
                            </div>
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses">
                                            <thead>
                                            <tr>
                                                <th colspan="">Sr. No</th>
                                                <th colspan="" >Event Name</th>
                                                <th colspan="" >Event Title</th>
                                                <th colspan="" >Event Colors</th>
                                                <th colspan="" >Action</th>
                                                {{--                                                <th colspan="" >Action</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1;?>
                                            @if($events)
                                                @foreach($events as $k)
                                                    <tr>
                                                        <td style="width:50px;">{{$i}}</td>
                                                        <td class="hidden-xs">{{$k->EventName}}</td>
                                                        <td class="max-texts border ">{{$k->EventTitle}}</td>
                                                        <td class="max-texts border "><input type="color" class="form-control" value="{{$k->Color}}" disabled></td>
                                                        <td>
                                                            <a href="{{url('agent/events/editEvent/'.$k->id)}}" class="text-info"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                                                            <a href="javascript:deleteType({{@$k->id}});"  class="text-danger"><i class="fa fa-trash"></i></a>&nbsp;&nbsp;


                                                        </td>
                                                    </tr>
                                                    <?php $i++;?>
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
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->
    </div>
    <div class="modal fade" id="confirm_model" tabindex="-1" role="dialog" aria-labelledby="confirm_model"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Blog Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <p style="text-align:center;" class=""> Are You Want To Delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success confirm_btn">Ok</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('pageLevelJS')
<script>
   $(document).on('submit','#ImportForm',function(e){
        e.preventDefault();
        $('#rule-btn2').removeClass('d-none');
        $('#SubmitBtn').attr('disabled', true);
        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: '{{url("api/v1/agent/events/addEvent")}}',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log('response',response);
                toastr.success(response.message,'Success');
                setTimeout(function(){
                    location.reload();
                },3000);
            },
            error:function(status,error){
                var errors = JSON.parse(status.responseText);
                var msg_error = '';
                console.log(errors);
                $('#rule-btn2').addClass('d-none');
                $('#SubmitBtn').attr('disabled', false);
                if(status.status == 422){
                    $.each(errors.errors, function(i,v){
                        console.log(v);
                        msg_error += v[0]+'!</br>';
                    });
                    toastr.error( msg_error,'Opps!');
                }else{
                    toastr.error(errors.message,'Opps!');
                }
            }
        });


    });
   $(document).ready(function() {
        $('.confirm_btn').click(function() {
            var data = {
                'id': deleteId,
            };
            $.ajax({
                type: "POST",
                url:'{{url("api/v1/agent/events/deleteEvent")}}',
                data: data,
                success: function(response) {
                    console.log(data);
                    $('#confirm_model').modal('toggle');
                     toastr.success(response.message,'Success');
                        setTimeout(function(){
                            location.reload();
                        },2000);
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

        });
    });
   function deleteType(id) {
       deleteId = id;
       $('#confirm_model').modal('toggle');
   }
</script>
@endsection
