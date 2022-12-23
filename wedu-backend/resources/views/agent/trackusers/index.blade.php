
@extends($usertype.'/layouts.app')

@section('title','Dashboard')
@section('pageContent')
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">
            <section id="justified-bottom-border">
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            All Users
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses">
                                            <thead>
                                            <tr>
                                                <th colspan="">Sr.No</th>
                                                <th colspan="" >Ip Address</th>
                                                <th colspan="" >Time</th>
                                                <th colspan="" >Stay Time</th>
                                                <th colspan="" >Page Url</th>
                                                <th colspan="" >Create At</th>
                                                <th colspan="" >Action</th>
                                                {{--                                                <th colspan="" >Action</th>--}}
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1;?>
                                            @if($trackUsers)
                                                @foreach(@$trackUsers as $k)
                                                {{-- {{$k}} --}}
                                                    <tr>
                                                        <td style="width:50px;">{{$i}}</td>
                                                        <td class="hidden-xs">{{$k->IpAddress}}</td>
                                                        <td class="max-texts border ">{{$k->InTime}}</td>
                                                        <td class="max-texts border ">{{$k->StayTime}}</td>
                                                        <td>{{$k->PageUrl}}</td>
                                                        <td>{{$k->created_at}}</td>
                                                        <td>
                                                           <a href="{{url('agent/track_user/view/'.$k->IpAddress)}}" class=" text-purple"><i class="fa fa-eye"></i></a>
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
        </div>
        <!-- content -->

    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
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
</script>
@endsection