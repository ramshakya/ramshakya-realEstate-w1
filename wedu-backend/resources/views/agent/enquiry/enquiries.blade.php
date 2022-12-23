@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
<style>
        #DateTo{
        display: none;
        }
        #DateFrom{
            display: none;
        }
</style>
    <!-- third party css -->
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/multiselect/multi-select.css"  rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .cursor-pointer{
            cursor: pointer;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header mb-0 pb-0">
                                <h4 class="card-title card-title-heading mb-0  pb-1">
                                <i class="fa fa-question-circle" aria-hidden="true"></i> &nbsp; All Enquiries
                                </h4>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="col-md-12 mb-2">
                                    <div class="row">
                                      <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Enquiries Sent For Last </label>
                                                <select class="form-control"  data-placeholder="Choose ..." id="Lastdays">
                                                    <option value="">Last Days </option>
                                                    <option value="1">1 Days</option>
                                                    <option value="7" selected>7 Days</option>
                                                    <option value="15">15 Days</option>
                                                    <option value="coustomdate">Coustom Date</option>
                                                </select>       
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group" id="DateFrom">
                                                <label>From </label>
                                                <input type="date" id="datefrom" class="form-control search_input_one " name="DateFrom" placeholder="2022-02-20" >
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="DateTo">
                                            <div class="form-group">
                                                <label>To</label>
                                                <input type="date" id="dateto" class="form-control search_input_one " name="DateTo" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 pt-0">
                                            <label>  </label>
                                            <button type="submit" class="btn btn-block btn-success commonfilter" id="searchbtn">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 pt-0">
                                            <label> </label>

                                            <button id="filtereset" type="button" class="btn btn-block btn-danger clearall_btn"><i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 overflow-auto">
                                    <table id="datatableses" class="table table-bordered text-center">
                                        <div class="spinner-border text-purple m-2" id="spinLoader" role="status" style="display: none;">
                                         <span class="sr-only">Loading...</span>
                                        </div> 
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Message</th>
                                                <th>Property Address</th>
                                                <th>Sent At</th>
                                            </tr>
                                        </thead>
                                         <tbody >
                                        </tbody>  
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->
    </div>

@endsection
@section('pageLevelJS')
    <script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
    <!-- Init js-->
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
    <!-- Init js-->
    <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

    <!--Morris Chart-->
    <!-- <script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>
    <!-- third party js -->
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.html5.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.flash.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.print.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.keyTable.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.select.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // DataTable
            var dataTable = $('#datatableses').DataTable({
                processing: true,
                serverSide: true,
                searching : false,
                "language": {
                    "processing": '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:rgb(71, 137, 230);"></i>'
                 },
                "bLengthChange": false,
                'ajax': {
                    'url':'{{url("api/v1/agent/enquiries")}}',
                    'data': function(data){
                     data.agentid = {{Auth()->user()->id}};
                     data.dateto =  $('#datefrom').val();
                     data.datefrom =  $('#dateto').val();
                     data.days =  $('#Lastdays').val();
                    },
                },
                columns: [
                    { data:'id'},
                    { data:'name'},
                    { data:'email'},
                    { data:'phone'},
                    { data:'message'},
                    { data:'page_from'},
                    { data:'created_at'},

                ],
            });
         
            $(document).on('click','#searchbtn',function(){
                dataTable.draw();
            });
            $(document).on('click','#Lastdays',function(){
            var custom = document.getElementById('Lastdays');
            var customdate = custom.value;

            if (customdate == 'coustomdate') {
                console.log(customdate);
                var customTo = document.getElementById('DateTo');
                customTo.style.display='block';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='block';
                $('#Lastdays').val('')
            }else{
                var customTo = document.getElementById('DateTo');
                customTo.style.display='none';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='none';
            }
        });
        
        $(document).on('click','#filtereset',function(){
            $('#datefrom').val('');
            $('#dateto').val('');
            $('#Lastdays').val('');
            dataTable.draw();
        });
        });
        
    </script>
@endsection
