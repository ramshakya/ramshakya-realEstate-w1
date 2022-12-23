@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
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
                                            All Template
                                            </h4>
                                        </div>
                                        <div class="col-4 text-right">
                                                <a href="{{url('agent/template/create-template')}}" class="btn btn-purple">Add Template</a>
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
                                                        <th colspan="" >Name</th>
                                                        <th colspan="" >Subject</th>
                                                        <th colspan="" >Content</th>
                                                        <th colspan="" >Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i=1;?>
                                                    @if($templates)
                                                    @foreach($templates as $k)
                                                    <tr>
                                                        <td style="width:50px;">{{$i}}</td>
                                                        <td class="hidden-xs">{{$k->name}}</td>
                                                        <td class="max-texts border ">{{$k->subject}}</td>
                                                        <td><?php echo $k->content; ?></td>
                                                        <td>
                                                            <a style="width:50px;" href="{{url('agent/template/create-template/'.$k->id)}}"> <i class="fa fa-edit text-purple"></i>
                                                            </a>
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
                </section>
            </div> <!-- container -->

        </div> <!-- content -->
    </div>
</div>
@endsection
@section('pageLevelJS')
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
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
            "pageLength": 10,
        } );
    </script>
@endsection
