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

        .canvas-con-inner,
        .legend-con {
            display: inline-block;
        }

        #myTabs {
            color: inherit;
            padding: 10px;
            font-size: 18px;
           
        }

        #myTabs li {
            color: inherit;
            padding: 10px;
            font-size: 18px;
        }

        li .active {
            color: white !important;
            background: #71b6f9;
            padding: 10px;
        }

        .propDetail {
            color: inherit;
            display: flex;
            list-style: none;
            position: relative;
            left: -17%;
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
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Track User View
                                        </h4>
                                        <?php
                                        // dd($data['mostPagesOpened']);
                                        // print_r($data['propertyViews']);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="">
                                        <div class="container">
                                            <ul id="myTabs" class="nav nav-pills nav-justified" role="tablist"
                                                data-tabs="tabs">
                                                <li class=""><a class="propertyView " href="#Commentary"
                                                        data-toggle="tab">Property View</a></li>
                                                <li><a href="#Videos" data-toggle="tab">All Pages View</a></li>
                                                {{-- <li><a href="#Events" data-toggle="tab">Events</a></li> --}}
                                            </ul>
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane fade in " id="Commentary">
                                                    <div class="row">
                                                        <?php
                                                        ?>
                                                        @foreach ($data['propertyViews'] as $prop)
                                                            <div class="col-md-3 m-2">
                                                                <div class="card"
                                                                    style="width: 18rem;border:0.5px solid gray;">
                                                                    <img class="card-img-top"
                                                                        style="height: 13%;width: 61%;"
                                                                        src="{{ isset($prop->propertiesImages[0]) ? $prop->propertiesImages[0] : '/assets/agent/images/no-imag.jpg' }}"
                                                                        alt="Card image cap">
                                                                    <div class="card-body">
                                                                        <ul class="propDetail">
                                                                            <li>Bed: {{ $prop->Br }}</li>
                                                                            <li>Bath: {{ $prop->Bath_tot }}</li>
                                                                            <li>Sqft: {{ $prop->Sqft }}</li>
                                                                            {{-- <li>Garage: {{ $prop->Gar_spaces }}</li> --}}
                                                                        </ul>
                                                                        <h4 class="card-title">{{ $prop->Addr }}</h4>
                                                                        <h5 class="card-title">
                                                                            {{ formatDollars($prop->Lp_dol) }}</h5>
                                                                        {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane fade" id="Videos">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0" id="datatableses">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="">Sr.No</th>
                                                                    <th colspan="">Ip Address</th>
                                                                    <th colspan="">Time</th>
                                                                    <th colspan="">Stay Time</th>
                                                                    <th colspan="">Page Url</th>
                                                                    <th colspan="">View</th>
                                                                    <th colspan="">Create At</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $i = 1; ?>
                                                                @if ($data['mostPagesOpened'])
                                                                    @foreach (@$data['mostPagesOpened'] as $key => $k)
                                                                        <tr>
                                                                            <td style="width:50px;">{{ $i }}
                                                                            </td>
                                                                            <td class="hidden-xs">
                                                                                {{ $k['IpAddress'] }}</td>
                                                                            <td class="max-texts border ">
                                                                                {{ $k['inTime'] }}</td>
                                                                            <td class="max-texts border ">
                                                                                {{ $k['stayTime'] }}</td>
                                                                            <td>{{ $key }}</td>
                                                                            <td>{{ $k['count'] }}</td>
                                                                            <td>{{ $k['inTime'] }}</td>
                                                                        </tr>
                                                                        <?php $i++; ?>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane fade" id="Events">
                                                    Events WP_Query goes
                                                    here.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="canvas-con">
                                        <div class="canvas-con-inner">
                                            <canvas id="myChart" height="550px" width="550px"></canvas>
                                        </div>
                                        {{-- <div id="my-legend-con" class="legend-con"></div> --}}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script>
        $('#datatableses').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 20,
        });
        $(document).ready(function() {
            $(".propertyView").click();
        });

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["M", "T", "W", "T", "F", "S", "S"],
                datasets: [{
                    backgroundColor: [
                        "#2ecc71",
                        "#3498db",
                        "#95a5a6",
                        "#9b59b6",
                        "#f1c40f",
                        "#e74c3c",
                        "#34495e"
                    ],
                    data: [12, 19, 3, 17, 28, 24, 7]
                }]
            }
        });
    </script>
@endsection

{{-- most visited 10 propties in last 7 -15 days   show block in below

property visting time desc.. 
\

menu 
 stats property -1    desc order most
 city/are stats    --}}