@extends($usertype.'/layouts.app')

@section('title', 'Dashboard')
@section('pageContent')
    <style>
        .canvas-con {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 365px;
            position: relative;
        }
        .graph-width{
            width: auto !important;
        }

        .canvas-con-inner {
            height: 100%;
        }
        .tableBody a{
            color: #6c757d;
        }
        .tableBody a:hover{
            color:#71b6f9;
            text-decoration: underline;
        }
        #DateTo{
            display: none;
        }
        #DateFrom{
            display: none;
        }

    </style>
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
                <input type="hidden" value="{{ $agentId }}" id="agentId" />
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header col-xl-12">
                                <div class="col-md-10">
                                    <h4 class="card-title card-title-heading font-family-class">
                                        City Stats
                                    </h4>
                                </div>
                            </div>
                            <div class="row p-1">
                                <div class="col-md-3">
                                    <h4 class="ml-4">
                                        Total: <span id="total"></span>
                                    </h4>
                                </div>
                                <div class="col-7">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label>Cities Viewed For Last</label>
                                            <select class="form-control"  data-placeholder="Choose ..." id="CitiesLastDays">
                                                <option value="">Last Days </option>
                                                <option value="1">1 Days</option>
                                                <option value="7" selected>7 Days</option>
                                                <option value="15">15 Days</option>
                                                <option value="coustomdate">Custom Date</option>
                                            </select>       
                                        </div>
                                        <div class="col-md-4" id="DateFrom">
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
                                    </div>
                                </div>
                                <div class="col-1 pt-0">
                                    <label ></label>
                                    <button type="submit" class="btn btn-block btn-success commonfilter" id="searchbtn">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                                <div class="col-1 mt-0">
                                    <label ></label>
                                    <button id="filtereset" type="button" class="btn btn-block btn-danger clearall_btn"><i class="fa fa-times"></i>
                                    </button>
                                </div>
                                <div class="col-12 px-3 overflow-auto">
                                    <table class="table table-bordered mb-0 text-center" id="cityDetails">
                                        <thead>
                                            <tr>
                                                <th colspan="">Sr.No</th>
                                                <th colspan="">City Name</th>
                                                <th colspan="">Count</th>
                                                <th colspan="">Date</th>
                                                {{-- <th colspan="" >Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody class="tableBody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="canvas-con mt-4 col-md-12">
                                <div class="canvas-con-inner">
                                    {{-- <canvas id="myChart" height="550px" width="550px"></canvas> --}}
                                    <div id="myChart" style="width: 900px; height: 500px;"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row match-height" hidden>
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Area Stats
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="canvas-con graph-width">
                                <div class="canvas-con-inner">
                                    <canvas id="myChart" height="500px" width="500px"></canvas>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>

    <script>
        var colorArray = ['#FF6633', '#FFB399', '#FF33FF', '#FFFF99', '#00B3E6',
            '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
            '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
            '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
            '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
            '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
            '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
            '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
            '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
            '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'
        ];
        google.charts.load('current', {
            'packages': ['corechart']
        });
        function GraphData()
        {
            let url = window.location.origin;
            let label = [];
            let dataList = [];
            const payload = {
                "_token": '{{ csrf_token() }}',
                "is_props": true,
                "agentId": $("#agentId").val(),
                "CitiesLastDays": $("#CitiesLastDays").val(),
                "DateFrom": $('#datefrom').val(),
                "DateTo" : $('#dateto').val(),
            }
            $.ajax({
                url: "/api/v1/agent/stats/getCityGraphData",
                data:payload ,
                type: "post",
                success: function(data) {
                    // console.log("response==>>", (data.aaData));
                    $("#total").text(data.iTotalRecords);
                    let html = "";
                    data.aaData.forEach(city => {
                        if (label.length <= 20) {
                            if (city.id == 1) {
                                let chartData = [
                                    "Search", "Top Search City"
                                ]
                                // label.push(city.CityName);
                                dataList.push(chartData);
                            }
                            let chartData = [
                                city.CityName, city.Count
                            ]
                            // label.push(city.CityName);
                            dataList.push(chartData);
                        }
                    });
                    drawChart(dataList);
                }
            }); 
        }
        var dataTable = $('#cityDetails').DataTable
        ({
            processing: true,
            serverSide: true,
            searching : false,
            "language": {
                    "processing": '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:rgb(71, 137, 230);"></i>'
                },
            "bLengthChange": false,
            'ajax': {
                'url':'{{url("/api/v1/agent/stats/getCityData")}}',
                'data': function(data){
                    data._token='{{ csrf_token() }}';
                    data.is_props= true;
                    data.agentId= {{Auth()->user()->id}};
                    data.CitiesLastDays= $("#CitiesLastDays").val();
                    data.DateFrom= $('#datefrom').val();
                    data.DateTo = $('#dateto').val();
                },
            },
            columns: [
                { data:'id'},
                { data:'CityName'},
                { data:'Count'},
                { data:'created_at'},
                
            ],
        });
        $(document).ready(function() {
            GraphData();
        });
     
        $(document).on('click','#searchbtn',function(){
            dataTable.draw();
            GraphData();

        }); 
        $(document).on('click','#filtereset',function(){
            $('#dateto').val('');
            $('#datefrom').val('');
            $('#CitiesLastDays').val('');
            GraphData();
            dataTable.draw();
        });
        $(document).on('click','#CitiesLastDays',function(){
            var custom = document.getElementById('CitiesLastDays');
            var customdate = custom.value;

            if (customdate == 'coustomdate') {
 
                var customTo = document.getElementById('DateTo');
                customTo.style.display='block';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='block';
                $('#CitiesLastDays').val('')

            }else{
                var customTo = document.getElementById('DateTo');
                customTo.style.display='none';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='none';
            }
        });
        $(document).on('click','#datefrom',function(){

            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);

            var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

            $('#dateto').val(today);
        });
        function drawChart(dataList) {
            let tmp = [
                ['Task', 'Hours per Day'],
                ['Work', 18],
                ['Eat', 1],
                ['Commute', 1],
                ['Watch TV', 0.1],
                ['Sleep', 4]
            ];
            setTimeout(() => {
                var data = google.visualization.arrayToDataTable(dataList);
                var options = {
                    title: 'Cities Search'
                };
                var chart = new google.visualization.PieChart(document.getElementById('myChart'));
                chart.draw(data, options);
            }, 500);
        }

        function sumOfArrVal(arr) {
            let sum = 0;
            arr.map(val => sum += val)
            return sum
        }
    </script>
@endsection
