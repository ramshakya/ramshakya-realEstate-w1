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
            <input type="hidden" value="{{ $agentId }}" id="agentId" />
            <section id="justified-bottom-border">
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card">
                                <div class="card-header col-xl-12  pb-0">
                                    <h4 class="card-title card-title-heading mb-0  pb-3">
                                         Properties Stats
                                    </h4>
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                <div class="col-7">
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label>Properties Viewed For Last</label>
                                            <select class="form-control"  data-placeholder="Choose ..." id="PropertiesLastDays">
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
                                        <table class="table table-bordered mb-0" id="propertyDetails">
                                            <thead>
                                                <tr>
                                                    <th colspan="">Sr.No</th>
                                                    <th colspan="">#MLS</th>
                                                    <th colspan="">Property Image</th>
                                                    <th colspan="">Title</th>
                                                    <th colspan="">Price</th>
                                                    <th colspan="" >Count</th>
                                                    <th colspan="">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="canvas-con overflow-auto">
                                <div class="canvas-con-inner" id="Graph-container">
                                    <canvas id="myChart" height="550px" width="550px"></canvas>
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
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
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
		  '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF'];
        var ctx = document.getElementById("myChart").getContext('2d');
        $(document).ready(function() {
            GraphData();
        });
        function GraphData()
        {
            let url = window.location.origin;
            let label = [];
            let datas = [];
            const payload = {
                "_token": '{{ csrf_token() }}',
                "is_props": true,
                "agentId": $("#agentId").val(),
                "PropertiesLastDays": $("#PropertiesLastDays").val(),
                "DateFrom": $('#datefrom').val(),
                "DateTo" : $('#dateto').val(),
            }
            $.ajax({
                url: "/api/v1/agent/stats/getPropertyGraphData",
                data:payload ,
                type: "post",
                  success: function(json) {
                    // console.log("response==>>", (json.aaData));
                    let html = "";
                    let sno = 1;
                    let qts = "'";
                    let image = "/assets/agent/images/no-imag.jpg";
                    //Function for sorting properties
                    // const propertyViews = (b, a) => {
                    //     return a.count - b.count;
                    //     };
                    //     const sortByAge = arr => {
                    //     arr.sort(propertyViews);
                    //     };
                    //     sortByAge(json.aaData);
                        json.aaData.forEach(prop => {
                            label.push(prop.Ml_num);
                            datas.push(prop.count);
                        });
                    const labels = ['q', 'w', 'e', 'r', 't', 'y', 'u'];
                    let finalData=[];
                    // datas = [52, 50, 54, 103, 10, 19, 140];
                    let sum = sumOfArrVal(datas);
                    datas.forEach(e => {
                        let tempStore=0;
                        if(e){
                            tempStore=e;
                            finalData.push(tempStore);
                        }else{

                        }

                    });
                    const data = {
                        labels: label,
                        datasets: [{
                            label: 'Properties View',
                            data: finalData,
                            backgroundColor: colorArray,
                            borderColor: colorArray,
                            borderWidth: 1
                        }]
                    };
                    
                    var bar_heights =Object.values(finalData);
    
                    var max_height = Math.max(...bar_heights);
                    max_height = max_height+(10-(max_height%10));
                   

                     myChart = new Chart(ctx, {
                        type: 'bar',
                        data: data,
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Total no. of visits',
                                        fontSize: 30,
                                    },
                                    stacked: true,
                                    ticks: {
                                        min: 0,
                                        max: max_height,
                                        callback: function(value) {
                                            return value 
                                        }
                                    }
                                }],
                                xAxes: [{
                                    stacked: true
                                }]
                            },
                            tooltips: {
                                enabled: true,
                                mode: 'single',
                                callbacks: {
                                    label: function(tooltipItems, data) {
                                        return tooltipItems.yLabel ;
                                    }
                                }
                            }
                        }
                    });
                },
            }); 
        }
        var dataTable = $('#propertyDetails').DataTable({
                processing: true,
                serverSide: true,
                searching : false,
                "language": {
                    "processing": '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:rgb(71, 137, 230);"></i>'
                 },
                "bLengthChange": false,
                'ajax': {
                    'url':'{{url("/api/v1/agent/stats/getstatsdata")}}',
                    'data': function(data){
                        data._token= '{{ csrf_token() }}';
                        data.is_props = true;
                        data.agentId = $("#agentId").val();
                        data.PropertiesLastDays= $("#PropertiesLastDays").val();
                        data.DateFrom = $('#datefrom').val();
                        data.DateTo = $('#dateto').val();
                    },
                    // 'type': 'POST',
                },
                columns: [
                { data:'id'},
                { data:'Ml_num'},
                { data:'ImageUrl'},
                { data:'Addr'},
                { data:'Lp_dol'},
                { data:'count'},
                { data:'date'},

            ],
        });


        $(document).on('click','#searchbtn',function(){
            myChart.destroy();
            GraphData();
            dataTable.draw();
        }); 
        $(document).on('click','#filtereset',function(){
            $('#PropertiesLastDays').val('');
            $('#dateto').val('');
            $('#datefrom').val('');
            GraphData();
            dataTable.draw();
        });
        $(document).on('click','#PropertiesLastDays',function(){
            var custom = document.getElementById('PropertiesLastDays');
            var customdate = custom.value;

            if (customdate == 'coustomdate') {
                var customTo = document.getElementById('DateTo');
                customTo.style.display='block';
                var customFrom = document.getElementById('DateFrom');
                customFrom.style.display='block';
                $('#PropertiesLastDays').val('')

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
        // $(document).on('click','#DateFrom',function(){
        //     var now = new Date();
        //     var day = ("0" + now.getDate()).slice(-2);
        //     var month = ("0" + (now.getMonth() + 1)).slice(-2);

        //     var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        //     $('#DateTo').val(today);
        // });
        function sumOfArrVal(arr) {
            let sum = 0;
            arr.map(val => sum += val)
            return sum
        }
    </script>
@endsection
