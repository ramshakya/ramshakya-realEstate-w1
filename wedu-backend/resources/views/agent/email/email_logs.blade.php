@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
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
                                    <i class="fa fa-envelope"></i> &nbsp; Email Logs
                                </h4>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="col-md-12 mb-2">
                                    <div class="row pt-2 Filterrow" >
                                        <div class="col-md-2">
                                            <label>From To</label>
                                            <select class="select2 select2-multiple" data-placeholder="Choose ..." id="FromTo">
                                                @foreach($FromTo as $Email)
                                                    <option value="{{@$Email->FromEmail}}">{{@$Email->FromEmail}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Sent To</label>
                                                <select class="select2 select2-multiple" data-placeholder="Choose ..." id="SentTo">
                                                    @foreach($ToEmail as $Email)
                                                        <option value="{{@$Email->ToEmail}}">{{@$Email->ToEmail}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Email Type</label>
                                                <select class="form-control "  data-placeholder="Choose ..." id="EmailType">
                                                    <option value="">-- Select --</option>
                                                    <option value="1">Campaign Mails</option>
                                                    <option value="2">Signup Mails</option>
                                                    <option value="3">Enquiry Mails</option>
                                                    <option value="4">Schedule Mails</option>
                                                    <option value="5">Forget Password Mails</option>
                                                    <option value="6">Saved Search Mails</option>
                                                    <option value="7">Home Values Mails</option>
                                                    <option value="8">RETSE Mails</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 row">
                                            <div class="col-md-6 mt-3 ">
                                                <div class="form-group">
                                                    <h4 id="Totalemailsent" class="ml-2">Total Email Sent: 0 </h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <h4 id="Totalemailread">Total Email Read: 0 </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Subject</label>
                                                <input type="text" id="Subject" class="form-control search_input_one " name="Subject" placeholder="Search by subject" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group" id="fromdate">
                                                <label>From </label>
                                                <input type="date" id="DateFrom" class="form-control search_input_one " name="DateFrom" placeholder="2022-02-20" >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>To</label>
                                                <input type="date" id="DateTo" class="form-control search_input_one " name="DateTo" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Email Sent For </label>
                                                <select class="form-control"  data-placeholder="Choose ..." id="Lastdays">
                                                    <option value="">Last Days </option>
                                                    <option value="1">1 Days</option>
                                                    <option value="7">7 Days</option>
                                                    <option value="15">15 Days</option>
                                                    <option value="30">30 Days</option>
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
                                <div class="col-md-12 overflow-auto">
                                    <table id="datatableses" class="table table-bordered">
                                        <div class="spinner-border text-purple m-2" id="spinLoader" role="status" style="display: none;">
                                         <span class="sr-only">Loading...</span>
                                        </div> 
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sent By</th>
                                                <th>Sent To</th>
                                                <th>Subject</th>
                                                <th>Email Opened</th>
                                                <th>Delivered Time</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                         <tbody id="EmailList">
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
                "bLengthChange": false,
                'ajax': {
                    'url':'{{url("agent/email-data")}}',
                    'data': function(data){
                        // Read values
                        data.FromTo=$('#FromTo').val();
                        data.SentTo=$('#SentTo').val();
                        data.EmailType=$('#EmailType').val();
                        data.Subject=$('#Subject').val();
                        data.DateFrom=$('#DateFrom').val();
                        data.DateTo=$('#DateTo').val();
                        data.Lastdays=$('#Lastdays').val();

                    },
                    'complete': function (data) {
                        $("#Totalemailsent").html('');
                        $("#Totalemailread").html('');
                        $("#Totalemailsent").text("Total Email Sent: "+data['responseJSON']['iTotalDisplayRecords']);
                        $("#Totalemailread").text("Total Email Read: "+data['responseJSON']['readcount']);
                        // return data;
                    }
                },
                columns: [
                    { data:'id'},
                    { data:'FromEmail'},
                    { data:'ToEmail'},
                    { data:'Subject'},
                    { data:'OpenedTime'},
                    { data: 'DeliveredTime'},
                    { data:'IsRead'},
                ],
            }); 
         
            $(document).on('click','#searchbtn',function(){
                dataTable.draw();
            });
            $(document).on('click','#DateFrom',function(){
                $('#Lastdays').val('');
                var now = new Date();
                var day = ("0" + now.getDate()).slice(-2);
                var month = ("0" + (now.getMonth() + 1)).slice(-2);

                var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

                $('#DateTo').val(today);
            });
            $(document).on('click','#filtereset',function(){
                $('#DateTo').val('');
                $('#Subject').val('');
                $('#DateFrom').val('');
                $('#EmailType').val('');
                $("#FromTo").select2("val", "0");
                $("#SentTo").select2("val", "0");

                $("select option").prop("selected", false);
                dataTable.draw();
            });
            $(document).on('change','#Lastdays',function(){
                if(this.value !=="" || this.value !== null){
                    $('#DateTo').val('');
                    $('#DateFrom').val('');
                }
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
