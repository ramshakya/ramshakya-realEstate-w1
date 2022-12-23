@extends($usertype.'/layouts.app')

@section('title', 'Dashboard')
@section('pageContent')
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
        #DateTo{
            display: none;
        }
        #DateFrom{
            display: none;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header mb-0 pb-3">
                                <h4 class="card-title card-title-heading mb-0  pb-1">
                                    <i class="fa fa-user"></i> &nbsp; User Stats
                                </h4>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Users Login For Last</label>
                                        <select class="form-control"  data-placeholder="Choose ..." id="UserStatsdata">
                                            <option value="">Last Days </option>
                                            <option value="1">1 Days</option>
                                            <option value="7" selected>7 Days</option>
                                            <option value="15">15 Days</option>
                                            <option value="coustomdate">Custom Date</option>
                                        </select>      
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="fromdate">
                                        <label> User's Name</label>
                                        <div id="Users">
                                        <select id="Users_name" class="form-control" data-placeholder="Choose ..." >
                                            <option value=""> -- Select --</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> User's IpAddress </label>
                                        <div id="IpAddress">
                                            <select class="form-control" data-placeholder="Choose ..." id="Users_IpAddress">
                                                <option value=""> -- Select --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-4 ml-5" id="DateFrom">
                                        <div class="form-group" >
                                            <label>From </label>
                                            <input type="date" id="datefrom" class="form-control search_input_one " name="DateFrom" placeholder="2022-02-20" >
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="DateTo">
                                        <div class="form-group" >
                                            <label>To</label>
                                            <input type="date" id="dateto" class="form-control search_input_one " name="DateTo" value="">
                                        </div>
                                    </div>
                                <div class="col-3">
                                    <div class="form-group row">
                                        <div class="col-5 ml-3 pt-0">
                                            <label ></label>
                                            <button type="submit" class="btn btn-block btn-success commonfilter" id="searchbtn">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="col-5 mt-0">
                                            <label></label>
                                            <button id="filtereset" type="button" class="btn btn-block btn-danger clearall_btn"><i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive col-md-12">
                                <table id="datatableses" class="table table-bordered nowrap col-md-12 mt-2">
                                    <div class="spinner-border text-purple m-2" id="spinLoader" role="status" style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Name</th>
                                            <th>Ip Address</th>
                                            <th>Home Page</th>
                                            <th>Property Page</th>
                                            <th>Map Page</th>
                                            <th>Profile Page</th>
                                            <th>ContactUs Page</th>
                                            <th>City Page</th>
                                        </tr>
                                    </thead>
                                    <tbody id="EmailList">
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
                    'url':'{{url("api/v1/agent/stats/getuserstats")}}',
                    'data': function(data){
                     data.agentId = {{Auth()->user()->id}};
                     data.UserId = $('#Users_name').val();
                     data.IpAddress = $('#Users_IpAddress').val();
                     data.UserStatsdata= $("#UserStatsdata").val();
                     data.DateFrom = $('#datefrom').val();
                     data.DateTo = $('#dateto').val();
                    },
                },
                columns: [
                    { data:'id'},
                    { data:'UserId'},
                    { data:'IpAddress'},
                    { data:'HomePage'},
                    { data:'PropertyPage'},
                    { data: 'MapPage'},
                    { data:'ProfilePage'},
                    { data:'ContactPage'},
                    { data:'CityPage'}

                ],
            });
            function getTable(){
                $('#datatableses').dataTable( {
                    "bLengthChange": false,
                    "bFilter": true,
                    "searching": false,
                    "bSortable": false,
                } );
            }
        $(document).on('click','#searchbtn',function(){
            dataTable.draw();
        });
        $(document).on('click','#filtereset',function(){
            $('#Users_name').val('');
            $('#Users_IpAddress').val('');
            dataTable.draw();
        });
        $(document).on('click','#UserStatsdata',function(){
            var custom = document.getElementById('UserStatsdata');
            var customdate = custom.value;

            if (customdate == 'coustomdate') {
                console.log(customdate);
                var customTo = document.getElementById('DateTo');
                customTo.style.display='block';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='block';
                $('#UserStatsdata').val('')

            }else{
                var customTo = document.getElementById('DateTo');
                customTo.style.display='none';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='none';
            }
            var userid = '';
            var agentid = {{Auth()->user()->id}};
            var UserStatsdata = $("#UserStatsdata").val();
            var DateFrom = $('#datefrom').val();
            var DateTo = $('#dateto').val();
            var Filter = $.get('{{url("api/v1/agent/stats/getuserstats_filter")}}',{ 'userid': userid, 'agentid': agentid ,'UserStatsdata':UserStatsdata, 'DateFrom':DateFrom,'DateTo':DateTo} ,function( data ) {
                    var users = data.Users;
                    $('#Users_name')
                        .find('option')
                        .remove()
                        .end()
                        .append($("<option></option>")
                        .attr("value", '')
                        .text('-- Select --'))                        
                        $.each(users, function(index, value) {   
                            $('#Users_name')
                            .append($("<option></option>")
                            .attr("value", value['UserId'])
                            .text( value['Username'])); 
                        });
                        var IpAddress = data.IpAddress;
                        $('#Users_IpAddress')
                        .find('option')
                        .remove()
                        .end()
                        .append($("<option></option>")
                        .attr("value", '')
                        .text('-- Select --'))  
                        $.each(IpAddress, function(index, value) {   
                            $('#Users_IpAddress')
                            .append($("<option></option>")
                            .attr("value", value['IpAddress'])
                            .text( value['IpAddress'])); 
                        });
                });
        });
        $(document).on('change','#datefrom',function(){
            var userid = '';
            var agentid = {{Auth()->user()->id}};
            var UserStatsdata = $("#UserStatsdata").val();
            var DateFrom = $('#datefrom').val();
            var DateTo = $('#dateto').val();

            var Filter = $.get('{{url("api/v1/agent/stats/getuserstats_filter")}}',{ 'userid': userid, 'agentid': agentid ,'UserStatsdata':UserStatsdata, 'DateFrom':DateFrom,'DateTo':DateTo} ,function( data ) {
                    var users = data.Users;
                    if (users !== null) {
                        $('#Users_name')
                        .find('option')
                        .remove()
                        .end()
                        .append($("<option></option>")
                        .attr("value", '')
                        .text('-- Select --'))                        
                        $.each(users, function(index, value) {   
                            $('#Users_name')
                            .append($("<option></option>")
                            .attr("value", value['UserId'])
                            .text( value['Username'])); 
                        });
                        var IpAddress = data.IpAddress;
                        $('#Users_IpAddress')
                        .find('option')
                        .remove()
                        .end()
                        .append($("<option></option>")
                        .attr("value", '')
                        .text('-- Select --'))  
                        $.each(IpAddress, function(index, value) {   
                            $('#Users_IpAddress')
                            .append($("<option></option>")
                            .attr("value", value['IpAddress'])
                            .text( value['IpAddress'])); 
                        });
                    }
                });
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);

            var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

            $('#dateto').val(today);
        });
        $(document).ready(function () {
            var userid = '';
            var agentid = {{Auth()->user()->id}};
            var UserStatsdata = $("#UserStatsdata").val();
            var DateFrom = $('#datefrom').val();
            var DateTo = $('#dateto').val();

            var Filter = $.get('{{url("api/v1/agent/stats/getuserstats_filter")}}',{ 'userid': userid, 'agentid': agentid ,'UserStatsdata':UserStatsdata, 'DateFrom':DateFrom,'DateTo':DateTo} ,function( data ) {
                    var users = data.Users;
                     if (users !== null) {

                        $.each(users, function(index, value) {   
                            $('#Users_name')
                            .append($("<option></option>")
                            .attr("value", value['UserId'])
                            .text( value['Username'])); 
                        });
                        var IpAddress = data.IpAddress;
                        $.each(IpAddress, function(index, value) {   
                            $('#Users_IpAddress')
                            .append($("<option></option>")
                            .attr("value", value['IpAddress'])
                            .text( value['IpAddress'])); 
                        });
                    }
                    
                });
            });
            $(document).on('click','#Users_name',function(){
                var userid = $('#Users_name').val();
                var agentid = {{Auth()->user()->id}};
                var UserStatsdata = $("#UserStatsdata").val();
                var DateFrom = $('#datefrom').val();
                var DateTo = $('#dateto').val();

                var Filter = $.get('{{url("api/v1/agent/stats/getuserstats_filter")}}',{ 'userid': userid, 'agentid': agentid,'UserStatsdata':UserStatsdata, 'DateFrom':DateFrom,'DateTo':DateTo } ,function( data ) {
                    var IpAddress = data.IpAddress;
                    var options = new Array();
                    $('#Users_IpAddress')
                    .find('option')
                    .remove()
                    .end()
                    .append($("<option></option>")
                    .attr("value", '')
                    .text('-- Select --'))  
                    $.each(IpAddress, function(index, value) {   
                        $('#Users_IpAddress')
                        .append($("<option></option>")
                        .attr("value", value['IpAddress'])
                        .text( value['IpAddress'])); 
                    });
                });
            });

        });

        
    </script>
        <script>
    </script>
@endsection