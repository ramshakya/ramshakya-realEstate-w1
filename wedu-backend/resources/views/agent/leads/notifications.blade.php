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
<!-- Notification css (Toastr) -->
        <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title card-title-heading">
                                    All Notifications 
                                </h4>

                            </div>
                            <div class="col-md-12 mb-2">
                                    <div class="row pt-2 Filterrow" >
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Notification Type</label>
                                                <select class="form-control "  data-placeholder="Choose ..." id="NotificationType">
                                                    <option value="">-- Select --</option>
                                                    <option value="registered as new user">New Lead Registered</option>
                                                    <option value="have some message">Message From Lead</option>
                                                    <option value="schedule a meeting">Meeting Schedule</option>
                                                    <option value="get property sold">Get Property Sold</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="DateFrom">
                                            <div class="form-group" >
                                                <label>From </label>
                                                <input type="date" id="datefrom" class="form-control search_input_one " name="datefrom" placeholder="2022-02-20" >
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="DateTo">
                                            <div class="form-group" >
                                                <label>To</label>
                                                <input type="date" id="dateto" class="form-control search_input_one " name="dateto" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Notification Sent For Last</label>
                                                <select class="form-control"  data-placeholder="Choose ..." id="Lastdays">
                                                    <option value=""> -- Days -- </option>
                                                    <option value="1">1 Days</option>
                                                    <option value="7" selected>7 Days</option>
                                                    <option value="15">15 Days</option>
                                                    <option value="customdate">Custom Date</option>
                                                </select>       
                                            </div>
                                        </div>
                                        <div class="col-md-1 pt-0">
                                            <label>  </label>
                                            <button type="submit" class="btn btn-block btn-success commonfilter" id="searchbtn">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-1 pt-0">
                                            <label> </label>

                                            <button id="filtereset" type="button" class="btn btn-block btn-danger clearall_btn"><i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <div class="card-body table-responsive">
                                <table id="datatableses" class="table text-center table-bordered mt-2">
                                        <thead>
                                    <tr>
                                        <th class="col-1">No #</th>
                                        <th class="col-2">Contact Name</th>
                                        <th class="col-2">Email</th>
                                        <th class="col-2">Message</th>
                                        <th class="col-2">Url</th>
                                        <th class="col-2">Time</th>
                                        <th class="col-2">Notification</th>
                                    </tr>
                                    </thead>
                                    <tbody id="PropertyDataList">
                                    </tbody>
                                </table>
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
<script>
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
                    'url':'{{url("api/v1/agent/all-notifications")}}',
                    'data': function(data){
                     data.agentid = {{Auth()->user()->id}};
                     data.NotificationType= $("#NotificationType").val();
                     data.LastDays= $("#Lastdays").val();
                     data.DateFrom= $('#datefrom').val();
                     data.DateTo = $('#dateto').val();
                    },
                },
                columns: [
                    { data:'id'},
                    { data:'ContactName'},
                    { data:'Email'},
                    { data:'Message'},
                    { data:'Url'},
                    { data:'createdAt'},
                    { data:'subject'},

                ],
            });

            $(document).on('click','#Lastdays',function(){
                var custom = document.getElementById('Lastdays');
                var customdate = custom.value;
                
               
                if (customdate == 'customdate') {
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
            $(document).on('click','#searchbtn',function(){
                dataTable.draw();
            }); 
        $(document).on('click','#filtereset',function(){
            $('#dateto').val('');
            $('#datefrom').val('');
            $('#Lastdays').val('');
            $('#NotificationType').val('');
            dataTable.draw();
        });
        $(document).on('click','#datefrom',function(){

            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);

            var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

            $('#dateto').val(today);
        });
            function getTable(){
                $('#datatableses').dataTable( {
                    "bLengthChange": false,
                    "bFilter": true,
                    "searching": false,
                    "bSortable": false,
                } );
            }
        });
        
</script>
@endsection
