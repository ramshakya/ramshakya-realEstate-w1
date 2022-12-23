@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
<link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
@section('pageContent')

    <!-- third party css -->

    <style type="text/css">
        .PropertyModelBtn{
            cursor: pointer;
        }
        .spinner-border{
            height: 16px;
            width: 16px;
        }
        .img-size{
            /* 	padding: 0;
                margin: 0; */
            height: 400px;
            width: 100%;
            background-size: cover;
            overflow: hidden;
        }
        .border-none{
            border-style: none;
            background-color: #f7f7f7 !important;
        }
        .btn-xs{
            background-color: #fff;
        }
        .card-title {
            margin-bottom: 0px;
        }
        .card-body{
            /* padding-top:0px !important;*/
        }
        .project-tab {
            padding: 10%;
            margin-top: -8%;
        }
        .project-tab #tabs{
            background: #007b5e;
            color: #eee;
        }
        .project-tab #tabs h6.section-title{
            color: #eee;
        }
        .project-tab #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
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
        .project-tab thead{
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
        .nav-item{
            font-weight: 400;
        }
        p{
            font-weight: 100;
        }
        .wd-60{
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto,sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow:hidden;
            text-align: center;
        }
        .wd-30{
            max-width: 30px !important;
            min-width: 20px !important;
            font-family: Roboto,sans-serif;
            font-size: .8rem;
            font-weight: 300;
            line-height: 1;
            padding: 8px !important;
            overflow:hidden;
            text-align: center;
        }
        .th-wd-30{
            max-width: 30px !important;
            min-width: 10px !important;
            font-family: Roboto,sans-serif;
            overflow: hidden;
            font-weight: bold;
            text-align: center;
            /*padding: 8px !important;*/
        }
        .th-wd-60{
            max-width: 50px !important;
            min-width: 30px !important;
            font-family: Roboto,sans-serif;
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
            background-color: #5b69bc!important;
            border: none;
            min-height: 10px;
            min-width: 2px;
            /* border-radius: 20px; */
        }
        .cursor-pointer{
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
    <style>
        center {
            font-size: 30px;
            color: green;
        }

        .popup {
            display: none;
            width: 500px;
            border: solid red 3px
        }
        /* Base styling*/
        /*body {*/
        /*    background-color: lightgrey;*/
        /*    max-width: 768px;*/
        /*    margin: 0 auto;*/
        /*    padding: 1em 0;*/
        /*    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;*/
        /*}*/

        /* Popover styling */

        /*a {*/
        /*    text-decoration: none;*/
        /*}*/

        .popover__title {
            font-size: 24px;
            line-height: 36px;
            text-decoration: none;
            color: rgb(228, 68, 68);
            text-align: center;
            padding: 15px 0;
        }

        .popover__wrapper {
            position: relative;
            margin-top: 1.5rem;
            display: inline-block;
        }
        .popover__content {
            opacity: 0;
            display: none;
            position: absolute;
            left: -150px;
            transform: translate(0, 10px);
            background-color: #bfbfbf;
            padding: 1.5rem;
            box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.26);
            width: auto;
        }
        .popover__content:before {
            /*position: absolute;*/
            z-index: -1;
            content: "";
            right: calc(50% - 10px);
            top: -8px;
            border-style: solid;
            border-width: 0 10px 10px 10px;
            border-color: transparent transparent #bfbfbf transparent;
            transition-duration: 0.3s;
            transition-property: transform;
        }
        .popover__wrapper:hover .popover__content {
            z-index: 10;
            opacity: 1;
            display: block;
            transform: translate(0, -20px);
            transition: all 0.5s cubic-bezier(0.75, -0.02, 0.2, 0.97);
        }
        .popover__message {
            text-align: center;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header mb-0 ">
                                <h4 class="card-title card-title-heading mb-0  pb-0"> Import Property</h4>
                            </div>
                            <div class="card-body">
                                <div class="row p-1 mb-2">
                                    <div class="col-md-12 mb-2">
                                        <p>You need to have a csv formate for properties to be added/updated. Must provided ListingId(MLS#) to add/update a property. You can download sample csv from <a href="{{url('agent/property/downloadfile')}}" target="_blank">Download </a> </p>
                                    </div>
                                </div>
                                <form class="pro-add-form" id="ImportForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                    <div class="inline-group">
                                        <div class="row ">
                                            <div class="col-md-9">
                                                <input type="file" class="form-control" id="import" name="file" required="" placeholder="Import" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 pl-5 pr-5" name="importbtn" id="SubmitBtn" style="width:100%;">  <div class="spinner-border d-none" role="status" id="rule-btn2">
                                                        <span class="sr-only">Loading...</span>
                                                    </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i>  Import</button>
                                            </div>
                                        </div>
                                    </div>
                                </form><br><br/>
                                <form method="POST" enctype="multipart/form-data" id="ImportZip">
                                    <div class="inline-group">
                                        <div class="row ">
                                            <div class="col-md-9">
                                                <input type="file" class="form-control" id="import" name="file" required="" placeholder="Import" accept=".zip,.rar,.7zip" >
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 pl-5 pr-5" name="importbtn"  id="Zipimport" style="width:100%;">  &nbsp;<div class="spinner-border d-none" role="status" id="zipimportBtn">
                                                        <span class="sr-only">Loading...</span>
                                                    </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Import Image </button>
                                            </div><br/>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <p>Please provide image name as MLS-1.jpg or MLS-1.jpeg, should be same formatted.</p>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                                <div class="row p-1 mb-2">
                                    <div class="col-md-12 mb-2">
                                        <p></p>
                                    </div>
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
    <!-- third party js ends -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>

    <!-- Datatables init -->
    <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>
    <!-- Dashboard init js-->
    <!--  <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script> -->
    <script type="text/javascript">
        $(document).on('submit','#ImportForm',function(e){
            e.preventDefault();

            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/importCSV")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    console.log(errors);
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
                    if(status.status == 422){
                        $.each(errors.errors, function(i,v){
                            console.log(v);
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
        $(document).on('submit','#ImportZip',function(e){
            e.preventDefault();
            $('#zipimportBtn').removeClass('d-none');
            $('#Zipimport').attr('disabled', true);
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/importZip")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    console.log(errors);
                    $('#zipimportBtn').addClass('d-none');
                    $('#Zipimport').attr('disabled', false);
                    if(status.status == 422){
                        $.each(errors.errors, function(i,v){
                            console.log(v);
                            msg_error += v[0]+'!</br>';
                        });
                        toastr.error( msg_error,'Opps!');
                    }else{
                        toastr.error(errors.message,'Opps!');
                    }
                }
            });
        });
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
