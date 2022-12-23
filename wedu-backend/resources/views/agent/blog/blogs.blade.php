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
                                            All Blogs
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/blog/create-blog')}}" class="btn btn-purple">Add Blog</a>
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
                                                <th colspan="" >Title</th>
                                                <th colspan="" >Slug</th>
                                                <th colspan="" >Image</th>
                                                <th colspan="" >Action</th>
                                                {{--                                                <th colspan="" >Action</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1;?>
                                            @if($category)
                                                @foreach($category as $k)
                                                    <tr>
                                                        <td style="width:50px;">{{$i}}</td>
                                                        <td class="hidden-xs">{{$k->Title}}</td>
                                                        <td class="max-texts border ">{{$k->Url}}</td>
                                                        <td colspan="" ><?php if(isset($k->MainImg) && !empty($k->MainImg)){?><a href="{{$k->MainImg}}" target="_blank"><img src="{{$k->MainImg}}" width="100" alt="{{$k->ImgTags}}"></a><?php } ?></td>
                                                        <td><a href="{{url('agent/blog/create-blog/'.$k->id)}}" class="text-info"><i class="fa fa-edit"></i></a>&nbsp;&nbsp; <span href="" class="text-danger cursor-pointer"  onclick="deleteType({{@$k->id}})"><i class="fa fa-trash"></i></span></td>
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
            </section>
        </div> <!-- container -->

    </div> <!-- content -->
    </div>
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
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
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

    </script>
    <script type="text/javascript">
        var deleteId = 0;
        $(document).ready(function() {
            $('.confirm_btn').click(function() {

                var data = {
                    'id': deleteId,
                    "_token": "{{ csrf_token() }}"
                };
                console.log(data);
                $.ajax({
                    type: "POST",
                    url:'{{url("api/v1/agent/blog/delete-blog")}}',
                    data: data,
                    success: function(response) {
                        console.log(data);
                        $('#confirm_model').modal('toggle');
                        if(response.success){
                            toastr.success(response.message,'Success');
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }
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
