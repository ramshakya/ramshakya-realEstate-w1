@extends('.superAdmin/layouts.app')
@section('title', 'Dashboard')
<!-- Begin page -->
@section('pageContent')

    <!-- third party css -->
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/multiselect/multi-select.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        .border-none {
            border-style: none;
            background-color: #f7f7f7 !important;
        }

        .btn-xs {
            background-color: #fff;
        }

        .card-title {
            margin-bottom: 0px;
        }

        .card-body {
            /* padding-top:0px !important;*/
        }

        .project-tab {
            padding: 10%;
            margin-top: -8%;
        }

        .project-tab #tabs {
            background: #007b5e;
            color: #eee;
        }

        .project-tab #tabs h6.section-title {
            color: #eee;
        }

        .project-tab #tabs .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            color: #0062cc;
            background-color: transparent;
            border-color: transparent transparent #f3f3f3;
            border-bottom: 1px solid !important;
            /*font-size: 16px;
                    font-weight: bold;*/
        }

        .project-tab .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            color: #0062cc;
            /*font-size: 16px;
                    font-weight: 600;*/
        }

        .project-tab .nav-link:hover {
            border: none;
        }

        .project-tab thead {
            background: #f3f3f3;
            color: #333;
        }

        /*.project-tab a{
                    text-decoration: none;
                    color: #333;
                    font-weight: 600;
                }*/
        .nav-link {
            display: block;
            padding: 0px;
        }

        .nav-item {
            font-weight: 400;
        }

        p {
            font-weight: 100;
        }

        .wd-60 {
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto, sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow: hidden;
            text-align: center;
        }

        .wd-30 {
            max-width: 30px !important;
            min-width: 20px !important;
            font-family: Roboto, sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow: hidden;
            text-align: center;
        }

        .th-wd-30 {
            max-width: 30px !important;
            min-width: 10px !important;
            font-family: Roboto, sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }

        .th-wd-60 {
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto, sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }

        button.btn.search_button_one {
            position: absolute;
            top: 2px;
            right: 17px;
            padding: 5px 16px;
            background-color: #5b69bc !important;
            border: none;
            min-height: 10px;
            min-width: 2px;
            /* border-radius: 20px; */
        }

        .cursor-pointer {
            cursor: pointer;
        }

        #spinLoader {
            position: fixed;
            top: 50%;
            left: 48%;
            background-color: #fff;
            z-index: 9999;
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
                                <h4 class="card-title card-title-heading mb-0  pb-0">
                                    <div class="row p-1 mb-2">
                                        <div class="col-2">
                                            <!-- <i class="fa fa-users"></i> Residential -->
                                            <select class="border-none" id="PropertyType">
                                                <option value="Residential"><b>Residential</b></option>
                                                <option value="Commercial"><b>Commercial</b></option>
                                                <option value="Condos"><b>Condos</b></option>
                                                <option value="Custom Properties"><b>Custom Properties</b></option>
                                            </select>
                                        </div>
                                        <div class="col-10 text-right">
                                            <button class="btn-xs btn-outline-purple">Work On behalf of ...</button>
                                            <button class="btn-xs btn-outline-purple">Email</button>
                                            <button class="btn-xs btn-outline-purple">Save</button>
                                            <button class="btn-xs btn-outline-purple">Print</button>
                                        </div>
                                    </div>
                                    <div class="row p-1">
                                        <div class="col-4">
                                            <p>Draft last saved: 12/2/2020 9:50:23 AM</p>
                                        </div>
                                        <div class="col-5"></div>
                                        <div class="col-md-3 col-sm-3 col-lg-3 text-right">
                                            <nav>
                                                <div class="nav nav-tabs nav-fill p-0" id="nav-tab" role="tablist">
                                                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab"
                                                        href="#nav-home" role="tab" aria-controls="nav-home"
                                                        aria-selected="true">Edit Search</a>
                                                    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab"
                                                        href="#nav-profile" role="tab" aria-controls="nav-profile"
                                                        aria-selected="false">List</a>
                                                    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab"
                                                        href="#nav-contact" role="tab" aria-controls="nav-contact"
                                                        aria-selected="false">Detail</a>
                                                    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab"
                                                        href="#nav-contact" role="tab" aria-controls="nav-contact"
                                                        aria-selected="false">Photos</a>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                </h4>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="col-md-12 mb-2">
                                    <div class="row mb-0">
                                        <h4 class="card-title card-title-heading ml-3 filterBtn cursor-pointer">
                                            <i class="fa fa-filter"></i> Filters
                                        </h4>
                                    </div>
                                    <div class="row pt-2 Filterrow" style="display: none;">
                                        <div class="col-md-3">
                                            <div class="form-group ">
                                                <div class="col-md-12">
                                                    <label>Search MLS , Address , AgentMLSId ..</label>
                                                    <input type="text" id="userinput1"
                                                        class="form-control border-purple search_input_one " name="search"
                                                        placeholder="Search MLS , Address , AgentMLSId , OfficeMLSId"
                                                        value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>cities</label><br>
                                                            <select class="select2 select2-multiple" multiple="multiple"
                                                                multiple data-placeholder="Choose ..." id="cities">
                                                                @if ($cities)
                                                                    @foreach ($cities as $city)
                                                                        <option value="{{ @$city->City }}">
                                                                            {{ @$city->City }}</option>
                                                                    @endforeach
                                                                @endif

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>Heat</label><br>
                                                            @if ($heating)
                                                                @foreach ($heating as $heat)
                                                                    <input type="checkbox" name="heating[]"
                                                                        value="{{ @$heat->Heating }}"
                                                                        class="heating"> {{ @$heat->Heating }} <br />
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-sm-1 col-xs-12">
                                            <button type="submit" class="btn btn-block btn-purple  commonfilter"
                                                id="searchbtn">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="col-lg-1 col-sm-1 col-xs-12">
                                            <button id="filtereset" type="button"
                                                class="btn btn-block btn-purple clearall_btn">Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <table id="datatableses" class="table table-bordered nowrap mt-2">
                                    <div class="spinner-border text-purple m-2" id="spinLoader" role="status"
                                        style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <thead>
                                        <tr>
                                            <th class="th-wd-30"><input type="checkbox" name=""></th>
                                            <th class="th-wd-30">MLS #</th>
                                            <th class="th-wd-60">Status</th>
                                            <th class="th-wd-60">Address</th>
                                            <th class="th-wd-60">Price</th>
                                            <th class="th-wd-60">Solid Price/Sqft</th>
                                            <th class="th-wd-60">SqFt Living</th>
                                            <th class="th-wd-60">Total Bedrooms</th>
                                            <th class="th-wd-30">Bathfull</th>
                                            <th class="th-wd-60">Type</th>
                                            <th class="th-wd-60">WaterFront </th>
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
    <script src="{{ asset('assets') }}/agent/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
    <!-- Init js-->
    <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

    <!--Morris Chart-->
    <!-- <script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>
    <!-- third party js -->
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
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
    <script src="{{ asset('assets') }}/agent/libs/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/pdfmake/vfs_fonts.js"></script>
    <!-- third party js ends -->

    <!-- Datatables init -->
    <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>
    <!-- Dashboard init js-->
    <!--  <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script> -->
    <script type="text/javascript">
        getData();

        function getData() {
            $('#datatableses').DataTable().destroy();
            $('#spinLoader').show();
            var search = $('#userinput1').val();
            var cities = $('#cities').val();
            console.log('city', cities);
            var data = {
                'type': $('#PropertyType').val(),
                'cities': cities,
                "_token": "{{ csrf_token() }}"
            };
            if (search != '') {
                data['search'] = search;
            }
            // if(cities!=''){
            //     $data['cities']=cities;
            // }

            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/getdata') }}',
                data: data,
                success: function(response) {
                    console.log(response);
                    setTimeout(function() {
                        $('#spinLoader').hide();
                    }, 500);
                    var i = 1;
                    var html = '';
                    $.each(response.property.data, function(key, val) {
                        html += '<tr  class="pr-2">' +
                            '<td class="wd-30"><input type="checkbox" name="">  ' + i + '</td>' +
                            '<td class="wd-30">' + val.Ml_num + '</td>' +
                            '<td class="wd-60">' + val.S_r + '</td>' +
                            '<td class="wd-60">' + val.Addr + '</td>' +
                            '<td class="wd-60">' + val.Lp_dol + '</td>' +
                            '<td class="wd-60">' + val.Orig_dol + '</td>' +
                            '<td class="wd-60">' + val.Sqft + '</td>' +
                            '<td class="wd-60">' + val.Br + '</td>' +
                            '<td class="wd-30">' + val.Bath_tot + '</td>' +
                            '<td class="wd-60">' + val.Type_own1_out + '</td>' +
                            '<td class="wd-60">' + val.Water_front + '</td>' +
                            '</tr>';
                        i++;
                    });
                    $('#PropertyDataList').html(html);

                    getTable();

                    // alert(1);
                },
                error: function(status, error) {
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    if (status.status == 401) {
                        $.each(errors.error, function(i, v) {
                            msg_error += v[0] + '!</br>';
                        });
                        toastr.error(msg_error, 'Opps!');
                    } else {
                        toastr.error(errors.message, 'Opps!');
                    }
                }
            });
        }
        $(document).on('change', '#PropertyType', function() {
            // alert(1);
            getData();
        });

        function getTable() {
            $('#datatableses').dataTable({
                "bLengthChange": false,
                "bFilter": true,
                "searching": false,
                "bSortable": false,
                "pagination": false,
            });
        }
        $(document).on('click', '.filterBtn', function() {
            $('.Filterrow').toggle("slow");
        });
        $(document).on('click', '#searchbtn', function() {
            getData();
        });
        $(document).on('click', '#filtereset', function() {
            location.reload();
        });
    </script>
@endsection
