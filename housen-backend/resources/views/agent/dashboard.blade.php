@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css'> 
    <style>
        .h-14{
            height: 140px;
        }
        .h-45{
            height: 450px;
        }
        .h-30{
            height: 42vh;
        }
        #notificationField{
            height: 85%;
            overflow-y: scroll;
        }
        #notificationField::-webkit-scrollbar {
          width: 2px;
        }
        #notificationField::-webkit-scrollbar-track {
          background: #f1f1f1;
        }

        /* Handle */
        #notificationField::-webkit-scrollbar-thumb {
          background: #888;
        }
        .leads{
            /*background-color: #EBEFF2;*/
            padding: 2px 0px;
            margin-bottom: 2px;
            /*border: 1px solid #EBEFF2;*/
            transition: .5s;
        }
        .leads a{
            color: currentColor;
        }
        .leads:hover{
            cursor: pointer;
            background-color: white;
        }
        .greenDot{
            color: #18CB65;
            font-size: 13px;
        }
        .inbox-lead:hover{
            background-color: #f7f7ed;
        }
        .inbox-lead{
            border-bottom: 1px solid rgba(222,226,230,.5) !important;
        }
        .Blink {
            color: #108221;
            font-size: 11px;
            animation: blinker 1.2s cubic-bezier(.5, 0, 1, 1) infinite alternate;  
            }
        .inbox-widget{
            height:100%;
        }
        hr {
            margin-top: 0.7rem;
            margin-bottom: 0.7rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        .graph{
            position: relative;
            width: 100%;
            padding-right: 4%;
            padding-left: 4%;
        }
        @keyframes blinker {  
             from { opacity: 1; }
            to { opacity: 0; }
        }
        #oilChart0{
            height: auto !important;
        }
        #oilChart1{
            height: auto !important;
        }
        #oilChart2{
            height: auto !important;
        }
        #oilChart3{
            height: auto !important;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card-box h-30">
                            <div class="row">
                                <div class="col-9"><h4 class="header-title mt-0 mb-0">Leads</h4></div>
                                <div class="col-12">
                                    <div class="widget-chart-1 row mt-3">
                                        <div class="col-md-4"></div>
                                        <div class="widget-chart-box-1 col-md-8 text-center" dir="ltr">
                                            <input data-plugin="knob" data-width="100" data-height="100" id="bar_captured_leads" data-fgColor="#018014" value=""
                                            data-bgColor="#e7f5d7" 
                                            data-skin="tron" data-angleOffset="180" data-readOnly=true
                                            data-thickness=".15"/>
                                        </div>
                                        <div class="widget-detail-1 col-md-12 ">
                                            <div class="col-md-12 mb-1">
                                                <div class="col-xl-12">
                                                   <i class="fa fa-circle Blink" aria-hidden="true"> <span class="Onlineusers"></span> Users Online </i> 
                                                </div>
                                                <div class="col-xl-12">
                                                    <span class="mt-1"><i class="fa fa-circle Blink" aria-hidden="true"> <span class="Onlinelead"></span> Leads Online </i>  </span> 
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <span class="text-muted col-md-12  mt-2">Captured leads - <span class="captured_leads"></span></span>
                                            </div>
                                            <div class="col-md-12 ">
                                                <span class="text-muted col-md-12 mb-2">Registered leads - <span class="all-lead"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card-box h-30">
                            <h4 class="header-title mt-0 mb-3">Properties</h4>
                            <div class="widget-box-2">
                                <div class="mb-0 mt-5" >Active Listings
                                 <span class="listing ml-1"> </span><span class="listing_bar float-right"> </span></div>
                                <div class="progress progress-bar-alt-success progress-sm mt-1 mb-1">
                                    <div class="progress-bar bg-success" id="progress-bar-listing" role="progressbar"
                                        aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                        >
                                    </div>
                                </div>
                                <div class="mt-5 mb-0">Sold Listings <span class="Soldlisting ml-1"> </span> <span class="float-right soldlisting_bar"></span></div>
                                <div class="progress progress-bar-alt-danger progress-sm mt-2 mb-2">
                                    <div class="progress-bar bg-warning" id="progress-bar-soldlisting" role="progressbar"
                                        aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                    
                    <div class="col-xl-3">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card-box h-30 overflow-auto">
                                    <h4 class="header-title mt-0 mb-1">Emails Analytics Stats</h4>
                                    <div class="row">
                                        <div class="col-12">
                                            <?php if($sent > 0){$percentage = intval(($read/$sent)*100) ;}else{$percentage=0;}?>
                                            <div class="mb-0 mt-1" > Email Sent/Read ({{@$percentage}}%)
                                                <span class="ml-2"> </span><span class="float-right">{{@$sent}} / {{@$read}} </span>
                                            </div>
                                            <div class="progress progress-bar-alt-primary progress-sm mt-1 mb-1">
                                                <div class="progress-bar bg-primary" style="width:{{@$percentage}}%;" role="progressbar" value=""
                                                    aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                                    >
                                                </div>
                                            </div><hr>
                                        </div>
                                        <div class="col-12">
                                        <?php if($signupsent > 0){$percentage = intval(($signupread/$signupsent)*100) ;}else{$percentage=0;}?>
                                            <div class="mb-0" > Signup Emails Sent/Read ({{@$percentage}}%)
                                                <span class="ml-2"> </span><span class="float-right">{{@$signupsent}} / {{@$signupread}} </span>
                                            </div>
                                            <div class="progress progress-bar-alt-warning progress-sm mt-1 mb-1">
                                                <div class="progress-bar bg-warning" style="width:{{@$percentage}}%;" role="progressbar" value=""
                                                    aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                                    >
                                                </div>
                                            </div><hr>
                                        </div>
                                        <div class="col-12">
                                        <?php if($enquirysent > 0){$percentage = intval(($enquiryread/$enquirysent)*100) ;}else{$percentage=0;}?>
                                            <div class="mb-0" > Enquiry Email Sent/Read ({{@$percentage}}%)
                                                <span class="ml-2"> </span><span class="float-right">{{@$enquirysent}} / {{@$enquiryread}} </span>
                                            </div>
                                            <div class="progress progress-bar-alt-danger progress-sm mt-1 mb-1">
                                                <div class="progress-bar bg-danger" style="width:{{@$percentage}}%;" role="progressbar" value=""
                                                    aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                                    >
                                                </div>
                                            </div><hr>
                                        </div>
                                        <div class="col-12">
                                        <?php if($campaignssent > 0){$percentage = intval(($campaignsread/$campaignssent)*100) ;}else{$percentage=0;}?>
                                            <div class="mb-0" > Campaigns Emails Sent/Read ({{@$percentage}}%)
                                                <span class="ml-2"> </span><span class="float-right">{{@$campaignssent}} / {{@$campaignsread}} </span>
                                            </div>
                                            <div class="progress progress-bar-alt-info progress-sm mt-1 mb-1">
                                                <div class="progress-bar bg-info" style="width:{{@$percentage}}%;" role="progressbar" value=""
                                                    aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="col-xl-3">
                    <div class="card-box h-30">
                        <div class="col-12"><h4 class="header-title mt-0 mb-1">Notifications <small class="float-right"><a href="/agent/Notifications">View all</a></small></h4></div>
                            <div class="inbox-widget overflow-auto">
                                <!-- <div class="inbox-item"> -->
                                    <div id="notificationField">
                                    
                                    </div>
                                    <!-- </div> -->
                                    <div class="my-1 col-md-12 row">
                                        <div class="col-6 text-center">
                                            <a href="/agent/Enquiries" class="badge w-100 badge-primary"><span >All Enquiries</span></a>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="/agent/Schedules" class="badge px-0 w-100 badge-primary"><span>All Schedules</span></a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card-box h-45">
                            <div class="dropdown float-right">
                                <input class="form-check-input listingradio" type="radio" value="inserted_time" id="Created_atlis" name="listing" checked>
                                <label class="form-check-label" for="Created_atlis">
                                    Property Created &nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                                <input class="form-check-input listingradio" type="radio" value="updated_time" id="Updated_atlis" name="listing">
                                <label class="form-check-label" for="Updated_atlis">
                                    Property Updated
                                </label>
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item pl-4">

                                        <!-- <input type="radio" name="listing" value="Created_at">Created at  -->
                                    </a>
                                    <!-- item-->
                                    <a href="javascript:void(0);" class="dropdown-item pl-4">

                                    </a>
                                </div>
                            </div>

                            <h4 class="header-title mt-0 mb-3">Listing <span></span></h4>

                            <div class="text-center">
                                <ul class="list-inline chart-detail-list">
                                    <li class="list-inline-item">
                                            <h5 style="color: #5b69bc;"><i class="fa fa-circle mr-1"></i>TREB</h5>
                                    </li>

                                </ul>
                            </div>
                            <div id="Listing" class="morris-chart" style="height: 300px;" dir="ltr"></div>

                        </div>
                    </div><!-- end col-->

                    
                    <!-- end col-->
                    <div class="col-xl-6">
                        <div class="card-box h-45">
                            <div class="dropdown float-right">
                                <!-- <input class="form-check-input Leadradio" type="radio" value="captured" id="captured" name="lead" checked> -->
                                <!-- <label class="form-check-label" for="captured">Captured</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                                <input class="form-check-input Leadradio" type="hidden" value="registered" id="registered" name="lead" checked>
                                <label class="form-check-label" for="registered">
                                    Registered
                                </label>
                               
                                
                            </div>

                            <h4 class="header-title mt-0 mb-3">Leads Data</h4>

                            <div class="text-center">
                                <ul class="list-inline chart-detail-list">
                                    <li class="list-inline-item">
                                        <h5 style="color: #5b69bc;"><i class="fa fa-circle mr-1"></i>Leads</h5>
                                    </li>

                                </ul>
                            </div>
                            <div id="leadData" class="morris-chart" style="height: 300px;" dir="ltr"></div>

                        </div>
                    </div><!-- end col-->
                </div>
                <!--row -->

            </div> <!-- container -->

        </div> <!-- content -->



    </div>
@endsection
@section('pageLevelJS')
<script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

<!--Morris Chart-->
<script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>

<!-- Dashboard init js-->
<script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script>
<script src="{{ asset('assets') }}/agent/libs/chart-js/Chart.bundle.min.js"></script>
    <!-- Dashboard init js-->
    <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script>
    <script type="text/javascript">
        $("document").ready(function () {
            //allLeadAgent();
            allListing();
            // pergeData();
            leadData();
        });
        $(document).ready( function(){
            AllLeads();
        });
        function AllLeads() {
            var leads = $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/leads/all-leads")}}',
                success: function(response) {
                //  $('#bar_captured_leads').attr('value',response.bar_captured);
                 $('#bar_captured_leads').val(response.allleads);
                 $('.registered_leads').html(response.registered_leads);
                 $('.captured_leads').html(response.captured_leads);
                 $('.Onlinelead').html(response.Onlineleads);
                 $('.all-lead').html(response.allleads);
                }
            })
        }
        $(document).ready( function(){
            allListingdata();
        });
        function allListingdata() {
            var listings = $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/leads/allListing")}}',
                success: function(response) {
                 $('.Soldlisting').html(`(${response.Soldlisting})`);
                 $('.soldlisting_bar').html(response.Soldlisting_bar);
                 $('.listing').html(`(${response.listing})`);
                 $('.listing_bar').html(response.listing_bar);

                 $('#progress-bar-listing').width(response.listing_bar);
                 $('#progress-bar-soldlisting').width(response.Soldlisting_bar);
                
                }
            })
        }

        $(document).on('change','.listingradio',function(){
            allListing();
        });
        $(document).on('change','.lapradio',function(){
            allLeadAgent();
        });
        $(document).on('change','.Leadradio',function(){
            leadData();
        });
        $(document).on('change','.Onlinelead',function(){
            refreshOnlinelead();
        });
        function allLeadAgent() {
            $('#stats-container').html('');
            // Create a function that will handle AJAX requests
            // requestData('days', chart);
            var type= $('input[name=lap]:checked',).val();
            console.log('lap type',type);
            function requestData(days, chart){
                $.ajax({
                    type: "GET",
                    url: "{{url('api/v1/agent/agent-graph-data')}}", // This is the URL to the API
                    data: { days: days,type:type }
                })
                    .done(function( data ) {
                        // When the response to the AJAX request comes back render the chart with new data
                        console.log(data);
                        chart.setData(JSON.parse(data.final));
                        console.log(chart);
                    })
                    .fail(function() {
                        // If there is no communication between the server, show an error
                        alert( "error occured" );
                    });
            }
            var chart = Morris.Line({
                // ID of the element in which to draw the chart.
                element: 'stats-container',
                // Set initial data (ideally you would provide an array of default data)
                data: [0,0,0,0,0],
                xkey: 'day',
                parseTime: false,
                ykeys: ['mls1','mls2','mls3','mls4'],
                labels: ['MFRMLS','SEF','RAGFL','RAPB'],
                lineColors: ['#5b69bc','#71b6f9','#ff5b5b','#98a6ad']
            });
            requestData(30, chart);
            $('ul.ranges a').click(function(e){
                e.preventDefault();
                var el = $(this);
                days = el.attr('data-range');
                // Request the data and render the chart using our handy function
                requestData(days, chart);
                // Make things pretty to show which button/tab the user clicked
                el.parent().addClass('active');
                el.parent().siblings().removeClass('active');
            })
        }

        function allListing() {
            //alert("hello");
            $('#Listing').html('');
            var type= $('input[name=listing]:checked',).val();
            console.log('type',type);
            function requestData(days, chart){
                $.ajax({
                    type: "GET",
                    url: "{{url('api/v1/agent/listing-graph-data')}}", // This is the URL to the API
                    data: { days: days ,type:type}
                })
                    .done(function( data ) {
                        console.log("data",data);
                        chart.setData(JSON.parse(data.final));
                    })
                    .fail(function() {
                        alert( "error occured" );
                    });
            }
            var chart = Morris.Line({
                element: 'Listing',
                data: [0,0,0,0,0],
                xkey: 'day',
                parseTime: false,
                ykeys: ['mls1'],
                labels: ['TREB'],
                lineColors: ['#5b69bc','#71b6f9','#ff5b5b','#98a6ad']
            });
            // Request initial data for the past 30 days:
            requestData(100, chart);
            $('ul.ranges a').click(function(e){
                e.preventDefault();
                var el = $(this);
                days = el.attr('data-range');
                requestData(days, chart);
                el.parent().addClass('active');
                el.parent().siblings().removeClass('active');
            })
        }

        // Perge
        // function pergeData() {
        //     $('#pergeData').html('');
        //     var type= $('input[name=purge]:checked',).val();
        //     console.log('type',type);
        //     function requestData(days, chart){
        //         $.ajax({
        //             type: "GET",
        //             url: "{{url('api/v1/agent/pergedata-graph-data')}}", // This is the URL to the API
        //             data: { days: days ,type:type}
        //         })
        //             .done(function( data ) {
        //                 console.log(data);
        //                 chart.setData(JSON.parse(data.final));
        //             })
        //             .fail(function() {
        //                 alert( "error occured" );
        //             });
        //     }
        //     var chart = Morris.Line({
        //         element: 'pergeData',
        //         data: [0,0,0,0,0],
        //         xkey: 'day',
        //         parseTime: false,
        //         ykeys: ['mls1'],
        //         labels: ['TREB'],
        //         lineColors: ['#5b69bc','#71b6f9','#ff5b5b','#98a6ad']
        //     });
        //     // Request initial data for the past 30 days:
        //     requestData(100, chart);
        //     $('ul.ranges a').click(function(e){
        //         e.preventDefault();
        //         var el = $(this);
        //         days = el.attr('data-range');
        //         requestData(days, chart);
        //         el.parent().addClass('active');
        //         el.parent().siblings().removeClass('active');
        //     })
        // }

        // leads
        function leadData() {
            $('#leadData').html('');
            var type= $('#registered',).val();
            // console.log('type',type);
            function requestData(days, chart){
                $.ajax({
                    type: "POST",
                    url: "{{url('api/v1/agent/leaddata-graph-data')}}", // This is the URL to the API
                    data: { days: days ,type:type}
                })
                    .done(function( data ) {
                        console.log(data);
                        chart.setData(JSON.parse(data));
                        // var result = JSON.parse(data);
                        // console.log(result);
                        // for (var i = 0; i < dates.length; i++) {
                        //     dataDisplay = {y:dates[]}
                        // }
                        // chart.setData(JSON.parse(data.final));
                    })
                    .fail(function() {
                        alert( "error occured" );
                    });
            }
            var chart = Morris.Line({
                element: 'leadData',
                data: [
                    { y: '0', a: 0 },
                    
                  ],
                xkey: 'y',
                ykeys: ['a'],
                labels: ['Total Leads']
            });
            // Request initial data for the past 30 days:
            requestData(30, chart);
            // $('ul.ranges a').click(function(e){
            //     e.preventDefault();
            //     var el = $(this);
            //     days = el.attr('data-range');
            //     requestData(days, chart);
            //     el.parent().addClass('active');
            //     el.parent().siblings().removeClass('active');
            // })
        }
        // Morris.Line({
        //   element: 'leadData',
        //   data: [
        //     { y: '2006', a: 100 },
        //     { y: '2007', a: 75 },
        //     { y: '2008', a: 50 },
        //     { y: '2009', a: 75 },
        //     { y: '2010', a: 50 },
        //     { y: '2011', a: 75 },
        //     { y: '2012', a: 100 }
        //   ],
        //   xkey: 'y',
        //   ykeys: ['a'],
        //   labels: ['Series A'   ]
        // });

        function getNotifications(){
            var agentId='{{auth()->user()->AdminId}}';
            var data = {
                'dashboard':'dashboard',
                'AgentId':agentId,
                "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/getNotification")}}',
                data: data,
                success: function (response) {
                   console.log(response);
                   $('#notificationField').html(response);
                },
            });
        }
        getNotifications();

        function refreshOnlinelead() {
            var refresh = $.ajax({
                type: "POST",
                // data:data,
                url: '{{url("api/v1/agent/refreshlead")}}',
                success: function(response) {
                    // console.log(response.Onlineleads);
                 $('.Onlinelead').html(response.Onlineleads);
                 $('.Onlineusers').html(response.Onlineusers);
                 
                }
            })
            // console.log(data.Onlineleads);
        }
        setInterval(refreshOnlinelead,5000);
        setInterval(getNotifications,10000);

        
    </script>
      <script type="text/javascript">
        function getTable(){
            $('#datatableses').dataTable( {
                "bLengthChange": false,
                "bFilter": true,
                "searching": false,
                "bSortable": false,
            } );
        }
    </script>

@endsection
