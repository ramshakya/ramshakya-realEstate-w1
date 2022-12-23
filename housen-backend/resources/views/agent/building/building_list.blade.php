@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
   <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
   
    <style>
        .cursor-pointer{
            cursor: pointer;
        }
        input[type="radio"] {
            float: left;
            margin-top: 3px;
            margin-right: 3px;
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
                                            All Pre Construction Buildings
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/building/create-update-building')}}" class="btn btn-purple">Add New</a>
                                        <a href="{{url('agent/building/builders')}}" class="btn btn-purple">Builders</a>
                                         <!-- <a href="{{url('agent/building/amenities-list')}}" class="btn btn-purple">Amenities</a> -->
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="datatableses" width="100%">
                                            <thead>
                                            <tr>
                                                <th colspan="">Sr. No</th>
                                                <th colspan="" >Building name</th>
                                                <th colspan="" >Address</th>
                                                <th colspan="" >City</th>
                                                <th colspan="" >Latest or Comming Soon</th>
                                                <th colspan="" >Action</th>
                                                
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
    
@include('agent.layouts.delete')
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
                    'url':'{{url("api/v1/agent/building/get_pre_construction")}}',
                    'data': function(data){
                    }
                },
                columns: [
                    { data:'id'},
                    { data:'BuildingName'},
                    { data:'Address'},
                    { data:'City'},
                    { data:'LatestCommingSoon'},
                    { data:'Action'}
                ],
            });
        });
    </script>
@endsection
