@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
    <link rel="stylesheet"
          href="{{ asset('assets') }}/agent/plugins/bower_components/dropify/dist/css/dropify.min.css">
    <link href="{{ asset('assets') }}/plugins/bower_components/bootstrap-select/bootstrap-select.min.css"
          rel="stylesheet"/>
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets') }}/agent/css/fullcalendar.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/agent/css/bootstrap-datetimepicker.css">
    <style type="text/css">
        .bookAshowNew:hover {
            color: black !important;
            background-color: #10c469 !important;
        }

        .collapse.in {
            height: auto;
        }

        .collapse {
            position: relative;
            height: 0;
            overflow: hidden;
            -webkit-transition: height 0.35s ease;
            -moz-transition: height 0.35s ease;
            -o-transition: height 0.35s ease;
            transition: height 0.35s ease;
        }

        .accordion-inner {
            padding: 9px 6px;
            border-top: 1px solid #e5e5e5;
        }

        .accordion-heading {
            border-bottom: 0;
        }

        .accordion-inner {
            padding: 9px 6px;
            border-top: 1px solid #e5e5e5;
        }

        .table {
            width: 100%;
            margin-bottom: 0px;
        }

        table {
            max-width: 100%;
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .table caption + thead tr:first-child th,
        .table caption + thead tr:first-child td,
        .table colgroup + thead tr:first-child th,
        .table colgroup + thead tr:first-child td,
        .table thead:first-child tr:first-child th,
        .table thead:first-child tr:first-child td {
            border-top: 0;
        }

        .table thead th {
            vertical-align: bottom;
            text-align: center;
        }

        .table-condensed th,
        .table-condensed td {
            padding: 4px 0px !important;
        }

        .table th {
            font-weight: bold;
        }

        .table tr .accordion-inner {
            padding: 9px 0px;
        }

        .table th,
        .table td {
            padding: 8px;
            line-height: 20px;
            text-align: center;
            vertical-align: top;
            border-top: 1px solid #dddddd;
        }

        .table-striped tbody > tr:nth-child(2n+1) > td,
        .table-striped tbody > tr:nth-child(2n+1) > th {
            background-color: #f9f9f9;
        }

        .active_row_bg td {
            /* background-color: #93cf84 !important; */
            font-weight: 500;
            color: blue;
            background: rgba(168, 242, 173, 0.3) !important
        }

        .table-condensed th,
        .table-condensed td {
            padding: 4px 5px;
        }

        .table th,
        .table td {
            padding: 8px;
            line-height: 20px;
            text-align: center;
            vertical-align: top;
            border-top: 1px solid #dddddd;
        }

        a {
            color: #0088cc;
            text-decoration: none;
        }

        .xpand-icon {
            color: #0f25cc;
        }

        .xpand-icon:after {
            /* content: '\002B' !important; */
            color: white;
            font-weight: bold;
            float: right;
            margin-left: 5px;
        }

        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .white-box {
            padding: 25px 12px;
        }

        .rows_count {
            width: 13%;
        }

        .rows_count2 {
            width: 12%;
        }

        .rows_ad {
            width: 8%;
        }

        .rows_id {
            width: 11%;
        }

        .rows_name {
            width: 24%;
        }

        .rows_phone {
            width: 11%;
        }

        .type_count {
            width: 5%;
        }

        #page_links {
            font-family: arial, verdana;
            font-size: 12px;
            border: 1px #000000 solid;
            padding: 6px;
            margin: 3px;
            background-color: #cccccc;
            text-decoration: none;
        }

        #page_a_link {
            font-family: arial, verdana;
            font-size: 12px;
            border: 1px #000000 solid;
            color: #ff0000;
            background-color: #cccccc;
            padding: 6px;
            margin: 3px;
            text-decoration: none;
        }

        .pagination {
            display: inline-block;
        }

        .pagination span {
            color: black;
            float: left;
            padding: 6px 16px;
            font-size: 10px;
            text-decoration: none;
            border: 1px solid #ddd;
        }

        .pagination span {
            cursor: pointer;
        }

        .pagination span.active {
            background-color: #fb9678;
            color: white;
            border: 1px solid #fb9678;
        }

        .pagination span:hover:not(.active) {
            background-color: #ddd;
        }


        .pagination span:first-child {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        .pagination span:last-child {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }


        th a.sort-by,
        th a.sort-bya,
        th a.sort-byl {
            padding-right: 18px;
            position: relative;
        }

        a.sort-by:before,
        a.sort-bya:before,
        a.sort-byl:before,
        a.sort-by:after,
        a.sort-bya:after,
        a.sort-byl:after {
            border: 4px solid transparent;
            content: "";
            display: block;
            height: 0;
            right: 5px;
            top: 50%;
            position: absolute;
            width: 0;
        }

        a.asc_sort:before,
        a.asc_sort:after {
            border-top: 0px solid transparent;
        }

        a.desc_sort:before,
        a.desc_sort:after {
            border-bottom: 0px solid transparent;
        }

        a.sort-by:before,
        a.sort-bya:before,
        a.sort-byl:before {
            border-bottom-color: #666;
            margin-top: -9px;
        }

        a.sort-by:after,
        a.sort-bya:after,
        a.sort-byl:after {
            border-top-color: #666;
            margin-top: 1px;
        }

        .modal-lg {
            width: 90%;
        }

        .irs-single,
        .irs-to,
        .irs-max,
        .irs-min {
            font-size: 20px;
            height: 24px;
        }

        .bolddd {
            font-weight: bold;
            font-size: 30px;
        }

        .upload_profile_img_flash_msg {
            font-size: 24px;
            color: #0fb91a;
            display: inline-block;
            font-weight: 900;
        }

        .fc-event-dot {
            background-color: #0a0a0a;
        }

        .fc-daygrid-event-dot {
        <?php
        echo 'background-color:red;';


        /* if($_GET['status']=="Open")
        {
            echo'border-color:blue;';
        }
        if($_GET['status']=="Overdue")
        {
            echo'border-color:red;';
        }
        if($_GET['status']=="Upcoming")
        {
            echo'border-color:yellow;';
        }
        if($_GET['status']=="Close")
        {
            echo'border-color:green;';
        }*/
        ?>


        }

        .bookAshowNew {
            width: 50%;
            height: 30px;
            background-color: white;
            color: #000000;
            border-color: black;
        }

        .bookAshowNew:hover {
            background-color: #000000;
            color: white;
        }

        .bookAshowNew .active {
            background-color: #000000;
            color: white;
        }

        .scroll-class {
            max-height: 100px;
            overflow-y: auto;
        }

        .scroll-class2 {
            max-height: 450px;
            overflow-y: auto;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0">Add Events</h4>
                            </div>
                            <div class="col-12">
                                <div class="card-body">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <br/>
                                        <a id="createnewschedule" href="#" data-toggle="modal" data-target="#addEvent"
                                           class="btn btn-sm btn-success font-16 btn-block waves-effect waves-light">
                                            <i class="fa fa-plus mr-1"></i> Create New Schedule
                                        </a>

                                        <div id="external-events" class="">
                                            <p style="margin-bottom:-2px;margin-top:15px; color:#4c4c4c !important;font-size:15px;">
                                                Event Colors : </p>
                                            <?php
                                            foreach ($events as $color) {
                                            ?>
                                            <div
                                                style="padding:4px;background-color:<?php echo $color["Color"]; ?>; margin-top:5px;border-radius:2px;">
                                                <span
                                                    style="padding: 5px;color:white"><?php echo $color["EventTitle"]; ?></span>
                                            </div>
                                            <?php } ?>

                                        </div>
                                        <!-- Modal Add Category -->
                                        <div class="modal fade none-border" id="addEvent">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title mt-0"><strong>Add a schedule </strong>
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-hidden="true">&times;
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{url('agent/events/addSchedule')}}" method="post"
                                                              enctype="multipart/form-data"
                                                              class="form-horizontal" name="novoevento" onsubmit="return validateField()">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-12 form-group">
                                                                    <label class="control-label">Title</label>
                                                                    <select name="Subject"
                                                                            class="form-control input-md">
                                                                        <?php
                                                                        foreach ($events as $color) {
                                                                        ?>
                                                                        <option
                                                                            value="<?php echo $color["id"]; ?>"><?php echo $color["EventTitle"]; ?>
                                                                        </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                    <label class="control-label"
                                                                           for="start">Date</label>
                                                                    <div class="input-append date form_datetime">
                                                                        <input type="text" class="form-control"
                                                                               name="Date" id="date2"
                                                                               value="<?php  //echo date('Y-m-d'); ?>"
                                                                               autocomplete="off" required/>
                                                                        <!-- <input type="text" value="" name="date" id="date" class="form-control input-md" value="dkfj" > -->
                                                                        <span class="add-on"><i
                                                                                class="icon-th"></i></span>
                                                                    </div>
                                                                    <input id="Start" name="Start" type="hidden"
                                                                           value="" required="">

                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                    <label class="control-label" for="end">Available
                                                                        Slots</label>
                                                                    <div class="scroll-class">
                                                                        <div class="text-center" id="availSlots">

                                                                        </div>
                                                                    </div>
                                                                    <p id="errorMsg" class="text-danger"></p>
                                                                </div>

                                                                <div class=" col-md-12 form-group">
                                                                    <label class=" control-label" for="description">Description</label>
                                                                    <div class="col-md-9">
                                                                        <textarea class="form-control" rows="5"
                                                                                  name="Description"
                                                                                  id="description" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <!-- Button -->
                                                                <div class=" col-md-12 form-group">
                                                                    <label class=" control-label"
                                                                           for="singlebutton"></label>
                                                                    <div class="col-md-4">
                                                                        <input type="submit" class="btn btn-primary "
                                                                               value="New Event">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light waves-effect"
                                                                data-dismiss="modal">Close
                                                        </button>
                                                        <!-- <button type="button" class="btn btn-danger waves-effect waves-light save-category" data-dismiss="modal">Save</button> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END MODAL -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0"> Calendar</h4>
                            </div>
                            <div class="card-box">
                                <div id="calendar" class=""></div>
                                <br/>
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
    <script src="{{ asset('assets') }}/agent/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>

    <script src="{{ asset('assets') }}/agent/Calendar/moment.min.js"></script>
    <script src="{{ asset('assets') }}/agent/Calendar/fullcalendar.min.js"></script>
    <script>
        $(document).ready(function () {
            var base = $('#base').val();
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: new Date(),
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true,
                displayEventTime: true,
                // allow "more" link when too many events
                events: [<?php
                    foreach (@$schedules as $schedule) {
                        /* $schedule["schedule_start_time"] = str_replace("-","T",$schedule["schedule_start_time"]);
                 $schedule["schedule_end_time"] = str_replace("-","T",$schedule["schedule_end_time"]);*/
                        echo "{
                                        title:'" . $schedule["Subject"] . "',
                                        start:'" . date("Y-m-d H:i:s", strtotime($schedule["ScheduleStartTime"])) . "',
                                        backgroundColor:'" . $schedule["events"]["Color"] . "',
                                        description:'" . $schedule["Description"] . "',
                                        end:'" . date("Y-m-d H:i:s", strtotime($schedule["ScheduleEndTime"])) . "',

                                    },";
                    }

                    ?>],
                selectable: false,
                selectHelper: false,
                select: function (start, end, allDay) {
                    var title = prompt("Enter Event Title");
                    if (title) {
                        var start = new Date();
                        var end = new Date();
                        $.ajax({
                            url: base + "admin/insertEvent",
                            type: "POST",
                            data: {
                                title: title,
                                start: start,
                                end: end
                            },
                            success: function () {
                                location.reload();
                            }
                        })
                    }
                },
                eventClick: function (event, jsEvent, view) {

                    $('#modalTitle').html(event.title);
                    $('#modalBody').html(event.description);
                    $('#startTime').html(moment(event.start).format('HH:mm'));
                    $('#endTime').html(moment(event.end).format('HH:mm'));
                    $('#fullCalModal').modal();

                },
                eventDrop: function () {
                    console.log("Hello");
                },
                dayClick: function (date, jsEvent, view) {
                    //alert('Clicked on: ' + date.format());
                    /*console.log("date",date.format("DD-MM-YYYY"));*/
                    var curdateclick = date.format("DD-MM-YYYY");
                    $('#date2').val(curdateclick);
                    $('#addEvent').modal();
                    slottimefunction();
                }

            });

            function slottimefunction() {
                var date = $("#date2").val();
                var urli = "{{url("api/v1/agent/events/timeShow")}}";
                $.ajax({
                    url: urli,
                    type: 'post',
                    data: {
                        "value": date,
                    },
                    success(response) {
                        for (i = 0; i < response.length; i++) {
                            //var nameArr = response[i].split(':');
                            $("#availSlots").append(
                                `<div class="text-center" data-toggle="buttons">
                                             <label class="bookAshowNew btn btn-secondary waves-effect waves-light" for="id${i}">
                                                 <input type="checkbox" class="time" id="id${i}" name="StartEndTime[]" autocomplete="off" required value="${response[i]}" style="display: none"> ${response[i]}
                                             </label>
                                         </div>`
                            );
                        }
                    }
                });
            }

            $(document).on('click', '#createnewschedule', function (e) {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1;
                var yyyy = today.getFullYear();
                if (dd < 10) {
                    dd = '0' + dd;
                }

                if (mm < 10) {
                    mm = '0' + mm;
                }
                //today = mm+'-'+dd+'-'+yyyy;
                today = yyyy + '-' + mm + '-' + dd;
                //alert(today);
                slottimefunction();
            });


        });

        $(".form_datetime").datetimepicker({
            minView: 2,
            format: 'dd-mm-yyyy',
            todayBtn: true,
            pickerPosition: "bottom-right",
            autoclose: true
        });

        $("#date2").change(function () {
            $("#availSlots").html("");
            var date = $("#date2").val();
            var urli = "{{url('api/v1/agent/events/timeShow')}}";
            $.ajax({
                url: urli,
                type: 'post',
                data: {
                    "value": date,
                },
                success(response) {
                    for (i = 0; i < response.length; i++) {
                        //var nameArr = response[i].split(':');
                        $("#availSlots").append(
                            `<div class="text-center required" data-toggle="buttons">
                                         <label class="bookAshowNew btn btn-secondary waves-effect waves-light" for="id${i}">
                                             <input type="checkbox" class="time" id="id${i}" name="StartEndTime[]" autocomplete="off" value="${response[i]}" style="opacity:0"> ${response[i]}
                                         </label>
                                     </div>`
                        );
                    }
                }
            });

        });

        function validateField(){
            if($('div.required :checkbox:checked').length>1){
                return true
            }
            else
            {
                $('#errorMsg').html('Select minimum 2 slots, start time and end time');
                 return false;
            }
           
        }
    </script>
@endsection
