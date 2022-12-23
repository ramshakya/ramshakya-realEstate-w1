@extends('.superAdmin/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <div class="content-page">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box">
                            <div class="row">
                                <div class="col-9"><h4 class="header-title mt-0 mb-3">All MLS</h4></div>
                                <div class="col-3"><h4 class="header-title mt-0 mb-3">{{@$mls_total}}</h4></div>
                            </div>

                            <div class="widget-chart-1">
                                <div class="widget-chart-box-1 float-left" dir="ltr">
                                    <i class="fas fa-home fa-3x text-purple"></i>
                                </div>

                                <div class="widget-detail-1 text-right">
                                    <p>
                                        <?php $i=1;
                                        // dd($mls);?>
                                        @if($mls)
                                            @foreach($mls as $m)
                                                @if($i!='1')
                                                    ,
                                                @endif
                                                {{$m['mls']}}
                                                <?php $i++; ?>
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div><!-- end col -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box pb-1">
                            <div class="row">
                                <div class="col-9"><h4 class="header-title mt-0 mb-3">TOTAL LISTINGS</h4></div>
                                <div class="col-3"><h4 class="header-title mt-0 mb-3">{{@$listing}}</h4></div>
                            </div>
                            <div class="widget-chart-1">
                                <div class="widget-chart-box-1 float-left" dir="ltr">
                                    <i class="fas fa-poll fa-3x text-primary"></i>
                                </div>
                                <div class="widget-detail-1 text-right">
                                    <p class="text-muted">
                                        <?php $i=1;?>
                                        @if($mls)
                                            @foreach($mls as $m)
                                                @if($i!='1')
                                                    ,
                                                @endif
                                                {{$m['mls']}} - {{$m['listing']}}
                                                <?php $i++; ?>
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">

                    <div class="col-xl-6">
                        <div class="card-box h-45">
                            <div class="dropdown float-right">
                                <input class="form-check-input listingradio" type="radio" value="Dom" id="Created_atlis" name="listing" checked>
                                <label class="form-check-label" for="Created_atlis">
                                    Created at &nbsp;&nbsp;&nbsp;&nbsp;
                                </label>
                                <input class="form-check-input listingradio" type="radio" value="updated_time" id="Updated_atlis" name="listing">
                                <label class="form-check-label" for="Updated_atlis">
                                    Updated at
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

                            <h4 class="header-title mt-0 mb-3">Listing  <span></span></h4>

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
                                <input class="form-check-input purgeradio" type="radio" value="created_at" id="Created_atpurge" name="purge" checked>
                                <label class="form-check-label" for="Created_atpurge">Created at  &nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <input class="form-check-input purgeradio" type="radio" value="updated_at" id="Updated_atpurge" name="purge">
                                <label class="form-check-label" for="Updated_atpurge">
                                    Updated at
                                </label>
                                <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- item-->
                                    {{-- <a href="javascript:void(0);" class="dropdown-item pl-4">
                                           <input class="form-check-input purgeradio" type="radio" value="createdTime" id="Created_atpurge" name="purge" checked>
                                           <label class="form-check-label" for="Created_atpurge">
                                             Created at
                                           </label>
                                     </a>
                                     <!-- item-->
                                     <a href="javascript:void(0);" class="dropdown-item pl-4">
                                         <input class="form-check-input purgeradio" type="radio" value="updated_at" id="Updated_atpurge" name="purge">
                                         <label class="form-check-label" for="Updated_atpurge">
                                           Updated at
                                         </label>
                                     </a>--}}
                                </div>
                            </div>

                            <h4 class="header-title mt-0 mb-3">Purge Data</h4>

                            <div class="text-center">
                                <ul class="list-inline chart-detail-list">
                                    <li class="list-inline-item">
                                        <h5 style="color: #5b69bc;"><i class="fa fa-circle mr-1"></i>TREB</h5>
                                    </li>

                                </ul>
                            </div>
                            <div id="pergeData" class="morris-chart" style="height: 300px;" dir="ltr"></div>

                        </div>
                    </div><!-- end col-->
                </div>
                <!--row -->

            </div> <!-- container -->

        </div> <!-- content -->



    </div>
@endsection
@section('pageLevelJS')
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

    <!--Morris Chart-->
    <script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>

    <!-- Dashboard init js-->
    <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script>
    <script type="text/javascript">
        $("document").ready(function () {
            //allLeadAgent();
            allListing();
            pergeData();
        });

        $(document).on('change','.listingradio',function(){
            allListing();
        });
        $(document).on('change','.lapradio',function(){
            allLeadAgent();
        });
        $(document).on('change','.purgeradio',function(){
            pergeData();
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
        function pergeData() {
            $('#pergeData').html('');
            var type= $('input[name=purge]:checked',).val();
            console.log('type',type);
            function requestData(days, chart){
                $.ajax({
                    type: "GET",
                    url: "{{url('api/v1/agent/pergedata-graph-data')}}", // This is the URL to the API
                    data: { days: days ,type:type}
                })
                    .done(function( data ) {
                        console.log(data);
                        chart.setData(JSON.parse(data.final));
                    })
                    .fail(function() {
                        alert( "error occured" );
                    });
            }
            var chart = Morris.Line({
                element: 'pergeData',
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
    </script>
@endsection
