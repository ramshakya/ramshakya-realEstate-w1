@extends('agent/layouts.app')
@section('pageLevelStyle')
    <style>
        .form-container {
            padding: 40px !important;
        }

        .card-title {
            margin-bottom: .1rem;
        }

        .nav-item a.nav-link {
            color: #5b69bc !important;
        }

        .dataTable {
            margin-top: 0px;
        }

        .img-sizeprop {
            width: 100%;
            max-height: 200px;
        }

        table {
            width: 100% !important;
        }

        .cursor {
            cursor: pointer;
        }
        .hidden-mail{
            display: none;
        }
    </style>
@endsection
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <!-- Start Content-->
            @if(!$lead)
            <section id="justified-bottom-border">
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card p-5">
                            <h3 class="text-center">Not found any lead</h3>
                        </div>
                    </div>
                </div>
            </section>
            @else
            <section id="justified-bottom-border">
                <div class="row match-height"><a id="Editing"></a>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card">
                            {{-- <div class="card-header "> --}}
                            {{-- <div class="row"> --}}
                            {{-- <div class="col-8"> --}}
                            {{-- <h4 class="card-title card-title-heading font-family-class"> --}}
                            {{-- Myaccount --}}
                            {{-- </h4> --}}
                            {{-- </div> --}}
                            {{-- <div class="col-4 text-right"> --}}
                            {{-- </div> --}}
                            {{-- </div> --}}
                            {{-- </div> --}}

                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="EditLeads" name="add_agent"
                                        enctype="multipart/form-data">
                                        <?php
                                        $FirstName = '';
                                        $LastName = '';
                                        if (isset($lead->created_at)) {
                                            $date_agent = strtotime($lead->created_at);
                                            $Date = date("Y-m-d",$date_agent);
                                        }else {
                                            $Date = date("Y-m-d");
                                        }
                                        if (isset($lead) && !empty($lead->ContactName)) {
                                            $names = explode(' ', $lead->ContactName);
                                            if (isset($names[0])) {
                                                $FirstName = $names[0];
                                            }
                                            if (isset($names[1])) {
                                                $LastName = $names[1];
                                            }
                                        } ?>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <img src="{{ asset('assets') }}/agent/images/avatar.png" alt="user-image"
                                                    width="110" class="rounded-circle img-thumbnail">
                                                <p><br /><b>Joined At: </b>{{ @$Date}}</p>
                                                <p><b>Agent Assigned: </b> {{ @$lead->AssignedAgentName }} <i
                                                        class="fa fa-edit text-purple cursor" data-toggle="modal"
                                                        data-target="#exampleModalLong2"></i></p>
                                            </div><a id="eMail"></a>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <input type="hidden" name="id" id="id" value="{{ @$lead->id }}">
                                                <label for="Subclass">First Name </label>
                                                <input type="text" class="form-control" id="first_name" name="FirstName"
                                                    placeholder="First Name" value="{{ @$FirstName }}"
                                                    autocomplete="off"
                                                    data-errormessage-value-missing="First Name is required!">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Last Name </label>
                                                <input type="text" class="form-control" id="last_name" name="LastName"
                                                    placeholder="Last Name" value="{{ @$LastName }}"
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Email Address </label>
                                                <input type="email" class="form-control" id="email" name="Email"
                                                    <?php if (isset($staff)) {
                                                        echo 'readonly';
                                                    } ?> placeholder="Enter Email Address"
                                                    autocomplete="off" value="{{ @$lead->Email }}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Mobile Number </label>
                                                <input type="text" class="form-control" id="phone" name="Phone"
                                                    autocomplete="off" placeholder="(123)456-7890"
                                                    value="{{ @$lead->Phone }}"
                                                    data-errormessage-value-missing="Phone Number is required!">
                                            </div>
                                            {{-- <div class="col-md-6 col-sm-6 form-group"> --}}
                                            {{-- <label for="Subclass">Date Of Birth </label> --}}
                                            {{-- <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" autocomplete="off" value="{{@$staff->date_of_birth}}" placeholder="" data-errormessage-value-missing="Date Of Birth is required!" --}}
                                            {{-- > --}}
                                            {{-- </div> --}}

                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Worked At </label>
                                                <input type="text" class="form-control" id="WorkedAt"
                                                    name="AssgnAgentOffice" autocomplete="off"
                                                    placeholder="Enter Office"
                                                    value="{{ @$lead->AssgnAgentOffice }}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">City </label>
                                                <input type="text" class="form-control" id="type" name="City"
                                                    autocomplete="off" placeholder="Enter City"
                                                    value="{{ @$lead->City }}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Status @if(@$lead->Status)[ <span class="text-secondary">{{ @$lead->Status }}</span> ]@endif  </label>

                                                <select class="form-control" id="Agentstatus" name="Status">

                                                    <option value="{{ @$lead->Status }}">Select</option>
                                                    <option value="Captured">Captured</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 col-sm-12 form-group">
                                                <button class="btn btn-outline-success waves-effect width-md waves-light"
                                                    type="submit" id="submit_btn">
                                                    <div class="spinner-border d-none" role="status" id="rule-btn">
                                                        <span class="sr-only">Loading...</span>
                                                    </div> &nbsp;&nbsp;
                                                    <i aria-hidden="true" class="far fa-check-circle"></i> Update
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Notes
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="nav-item">
                                            <a href="#home2" data-toggle="tab" aria-expanded="false"
                                                class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">Notes & Call</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile2" data-toggle="tab" aria-expanded="true"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">SMS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages2" data-toggle="tab" aria-expanded="false"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                <span class="d-none d-sm-block">Email</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade show active" id="home2">
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0" id="datatableses">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="">Sr. No</th>
                                                            <th colspan="">Type</th>
                                                            <th colspan="">Comment</th>
                                                            {{-- <th colspan="" >Action</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 1; ?>
                                                        @if ($notes)
                                                            @foreach ($notes as $k)
                                                                <tr>
                                                                    <td style="width:50px;">{{ $i }}</td>
                                                                    <td class="hidden-xs">{{ $k->Type }}</td>
                                                                    <td class="max-texts border ">{{ $k->Notes }}</td>
                                                                </tr>
                                                                <?php $i++; ?>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div><br />
                                            <form id="NotesForm" name="Notes" enctype="multipart/form-data" method="post">
                                                <input type="hidden" value="{{ @$AgentId }}" id="AgentId">
                                                <div class="radio radio-primary col-md-12 col-sm-12">
                                                    <input type="radio" name="radio" id="radio1" value="Notes"
                                                        class="NotesType" checked>
                                                    <label for="radio1" class="pr-3">
                                                        Notes
                                                    </label>
                                                    <input type="radio" name="radio" id="radio13" class="NotesType"
                                                        value="Calls">
                                                    <label for="radio13">
                                                        Calls
                                                    </label>
                                                </div><br />
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <textarea class="form-control" id="AddComment" name="alt_address"
                                                        placeholder="Add Comment"> </textarea>
                                                </div>
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <button
                                                        class="btn btn-outline-success waves-effect width-md waves-light"
                                                        id="NotificationBtn">
                                                        <div class="spinner-border text-success d-none" role="status"
                                                            id="rule-btn">
                                                            <span class="sr-only">Loading...</span>
                                                        </div> &nbsp;&nbsp;
                                                        <i aria-hidden="true" class="far fa-check-circle"></i> Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="profile2">
                                            <p class="mb-0">Food truck fixie locavore,
                                                accusamus mcsweeney's marfa nulla single-origin coffee squid.
                                                Exercitation +1 labore velit, blog sartorial PBR leggings next level
                                                wes anderson artisan four loko farm-to-table craft beer twee. Qui
                                                photo booth letterpress, commodo enim craft beer mlkshk aliquip jean
                                                shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda
                                                labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia
                                                yr, vero magna velit sapiente labore stumptown. Vegan fanny pack
                                                odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY
                                                ethical culpa terry richardson biodiesel. Art party scenester
                                                stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed
                                                echo park.</p>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="messages2">

                                            <div class="table-responsive">
                                                <div class="card-header ">
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <h4 class="card-title card-title-heading font-family-class">
                                                                Emails
                                                            </h4>
                                                        </div>
                                                        <div class="col-4 text-right">
                                                            <button class="btn btn-purple" data-toggle="modal"
                                                                data-target="#exampleModalLong">Add Email</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered mb-0" id="EmailTable">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="">Sr. No</th>
                                                            <th colspan="">Subject</th>
                                                            <th colspan="">Message</th>
                                                            {{-- <th colspan="" >Action</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 1; ?>
                                                        @if ($leademail)
                                                            @foreach ($leademail as $k)
                                                                <tr>
                                                                    <td style="width:50px;">{{ $i }}</td>
                                                                    <td class="hidden-xs">{{ $k->Subject }}</td>
                                                                    <td class="max-texts flex-row flex-nowrap borde">
                                                                        <?php 
                                                                        if ($k->Content !="") {
                                                                            $msg = substr($k->Content, 0, 108);
                                                                        }else {
                                                                            $msg="";
                                                                        }
                                                                        ?> 
                                                                        <span class="show-{{$i}}mail"> {{ @$msg }} <a href="#" class="float-right show_content" data-id="{{ $i }}">....Show more</a></span>
                                                                        <span class="hidden-{{$i}}mail hidden-mail"> {{ $k->Content }} <a href="#" class="float-right hide_content" data-id="{{ $i }}"> </br> ....Show less</a></span>
                                                                    </td>
                                                                </tr>
                                                                <?php $i++; ?>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div><br />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="col-8">
                                        <h4 class="card-title card-title-heading font-family-class"><a id="Activity"></a><a id="favProperty"></a><a id="savedSearch"></a>
                                            Lead Activity
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="nav-item">
                                            <a href="#PropertiesViewed" data-toggle="tab" aria-expanded="false"
                                                class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">Properties Viewed</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#Pageviewed" data-toggle="tab" aria-expanded="true"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">Page viewed</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#FavouriteProperties" data-toggle="tab" aria-expanded="false"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                <span class="d-none d-sm-block">Marked Favourite Properties</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#settings2" data-toggle="tab" aria-expanded="false"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Saved Search</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#settings3" data-toggle="tab" aria-expanded="false"
                                                class="nav-link">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Recent Login</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade show active" id="PropertiesViewed">
                                            {{--  --}}
                                            <table class="table table-bordered mb-0" id="PropertiesViewedtable">
                                                <thead>
                                                    <tr>
                                                        <th colspan="">Property Image</th>
                                                        <th colspan=""> Title</th>
                                                        <th colspan="">Price</th>
                                                        <th colspan="">Total View</th>
                                                        {{-- <th colspan="" >Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade table-responsive" id="Pageviewed">
                                            <table class="table table-bordered mb-0" id="PageviewedTable">
                                                <thead>
                                                    <tr>
                                                        <th colspan="">Page</th>
                                                        <th colspan="">Ip Address</th>
                                                        <th colspan="">Total Visited</th>
                                                        <th colspan="">Date</th>
                                                        {{-- <th colspan="" >Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                            {{-- <p class="mb-0">Food truck fixie locavore, --}}
                                            {{-- accusamus mcsweeney's marfa nulla single-origin coffee squid. --}}
                                            {{-- Exercitation +1 labore velit, blog sartorial PBR leggings next level --}}
                                            {{-- wes anderson artisan four loko farm-to-table craft beer twee. Qui --}}
                                            {{-- photo booth letterpress, commodo enim craft beer mlkshk aliquip jean --}}
                                            {{-- shorts ullamco ad vinyl cillum PBR. Homo nostrud organic, assumenda --}}
                                            {{-- labore aesthetic magna delectus mollit. Keytar helvetica VHS salvia --}}
                                            {{-- yr, vero magna velit sapiente labore stumptown. Vegan fanny pack --}}
                                            {{-- odio cillum wes anderson 8-bit, sustainable jean shorts beard ut DIY --}}
                                            {{-- ethical culpa terry richardson biodiesel. Art party scenester --}}
                                            {{-- stumptown, tumblr butcher vero sint qui sapiente accusamus tattooed --}}
                                            {{-- echo park.</p> --}}
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="FavouriteProperties">
                                            <table class="table table-bordered" id="FavouritePropertiesTable">
                                                <thead>
                                                    <tr>
                                                        <th colspan="">Property Image</th>
                                                        <th colspan=""> Title</th>
                                                        <th colspan="">Price</th>
                                                        {{-- <th colspan="" >Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="settings3">
                                            <table class="table table-bordered mb-0" id="Logindatatableses">
                                                <thead>
                                                    <tr>
                                                        <th colspan="">Page</th>
                                                        <th colspan="">Ip Address</th>
                                                        <th colspan="">Date</th>
                                                        {{-- <th colspan="" >Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="pro-add-form" id="EmaitForm" name="EmaitForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12 col-sm-12 form-group">
                            <input type="hidden" name="userId" value="{{ @$lead->id }}">
                            <label for="Subclass">To </label>
                            <input type="email" class="form-control" id="email" name="Email"
                                placeholder="Enter Agent Email Address" autocomplete="off" value="{{ @$lead->Email }}"
                                readonly>
                        </div>
                        <div class="col-md-12 col-sm-12 form-group">
                            <label for="Subclass">Subject </label>
                            <input type="text" class="form-control" id="email" name="Subject"
                                placeholder="Enter Email Subject" autocomplete="off" value="" required>
                        </div>
                        <div class="col-md-12 col-sm-12 form-group">
                            <label for="Subclass">Choose </label>
                            <select class="form-control" name="Template" id="emailTemp"
                                data-errormessage-value-missing="Country is required!" required>
                                <option value="0">Select Template</option>
                                @if ($templates)
                                    @foreach ($templates as $temp)
                                        <option value="{{ @$temp->id }}">{{ @$temp->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-12 col-sm-12 form-group">
                            <label for="address">Message</label>
                            <textarea class="form-control" rows="6" id="add_content" name="Message" required> </textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" value="submit"
                            class="btn btn-sm btn-outline-success waves-effect waves-light m-r-10 pl-1 pr-1"
                            name="EmailBtn" id="SubmitEmailBtn" style="width:100%;">
                            <div class="spinner-border d-none" role="status" id="rule-btn-Email">
                                <span class="sr-only">Loading...</span>
                            </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Agent Modal -->
    <div class="modal fade" id="exampleModalLong2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign Agent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="pro-add-form" id="AssignAgentForm" name="EmaitForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="LeadId" value="{{ @$id }}">
                        <div class="col-md-12 col-sm-12 form-group">
                            <label for="Subclass">Agent </label>
                            <select class="form-control" required name="AssignedAgent" id="PropertySubType">
                                <option value="" disabled selected>Selecte Agent</option>
                                <?php if(isset($Allagent) &&!empty($Allagent)){
                                foreach ($Allagent as $ps){ ?>
                                <option value="{{ @$ps->id }}" <?php if (isset($lead->AssignedAgent) && !empty($lead->AssignedAgent) && $lead->AssignedAgent == $ps->id) {
                                        echo 'selected';
                                    } ?>>{{ @$ps->name }}
                                </option>
                                <?php } }?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" value="submit"
                            class="btn btn-sm btn-outline-success waves-effect waves-light m-r-10 pl-1 pr-1"
                            name="EmailBtn" id="SubmitEmailBtn2" style="width:100%;">
                            <div class="spinner-border d-none" role="status" id="rule-btn-Email2">
                                <span class="sr-only">Loading...</span>
                            </div> &nbsp;&nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Large modal -->
    <button type="button" class="btn btn-primary modelOpen" data-toggle="modal" hidden
        data-target=".bd-example-modal-lg">Large
        modal</button>
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header bg-gray">
                    <h3 class="modal-title" id="">Property Visting Time</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <table class="table table-bordered mb-0" id="propertyDetails">
                    <thead>
                        <tr>
                            <th colspan="">Property Image</th>
                            <th colspan="">Title</th>
                            <th colspan="">Price</th>
                            <th colspan="">Date</th>
                            {{-- <th colspan="" >Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('pageLevelJS')
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    {{-- <script> --}}
    {{-- setTimeout(function(){ --}}
    {{-- var x = document.getElementById("content1").value; --}}
    {{-- CKEDITOR.replace('content1'); --}}
    {{-- CKEDITOR.add; --}}
    {{-- CKEDITOR.instances.add_content.setData(x); --}}
    {{-- },1000); --}}
    {{-- </script> --}}
    <script>
        var x = document.getElementById("add_content").value;
        CKEDITOR.replace('add_content');
        CKEDITOR.add;
        CKEDITOR.instances.add_content.setData(x);
    </script>
    <script type="text/javascript">
        function propertyView(slug,ip) {
            $(".modelOpen").click();
            //
            $('#propertyDetails').dataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                "bLengthChange": false,
                'ajax': {
                    'url': '{{ route('leads.propertyDetails') }}',
                    'data': function(data) {
                        var types = [];
                        var LeadId = {{ @$id }};
                        data.LeadId = LeadId;
                        data.slug = slug;
                        data.IPaddress=ip;
                    }
                },
                columns: [{
                        data: 'Media'
                    },
                    {
                        data: 'UnparsedAddress'
                    },
                    {
                        data: 'ListPrice'
                    },
                    {
                        data: 'Date'
                    },
                ],
            });
        }
        function ShowContent(Id,Value) {
            console.log(".hidden-mail"+Id,Value);
            if (Value =="show") {
                $(".hidden-"+Id+"mail").css("display","block");
            }else{
                $(".hidden-"+Id+"mail").css("display","none");
            }
        }
        $('.show_content').on('click', function(e){
            e.preventDefault();
            ShowContent($(this).attr('data-id'),"show");
            $(".show-"+$(this).attr('data-id')+"mail").css("display","none");
        });
        $('.hide_content').on('click', function(e){
            e.preventDefault();
            ShowContent($(this).attr('data-id'),"hide");
            $(".show-"+$(this).attr('data-id')+"mail").css("display","Block");
        });
        $('#PropertiesViewedtable').dataTable({
            processing: true,
            serverSide: true,
            searching: false,
            "bLengthChange": false,
            'ajax': {
                'url': '{{ route("leads.PropertiesViewed") }}',
                'data': function(data) {
                    var types = [];
                    var LeadId = {{ @$id }};
                    data.LeadId = LeadId;
                }
            },
            columns: [{
                    data: 'Media'
                },
                {
                    data: 'StandardAddress'
                },
                {
                    data: 'ListPrice'
                },
                {
                    data: 'Total View'
                },
            ],
        });

        $('#FavouritePropertiesTable').dataTable({

            processing: true,
            serverSide: true,
            searching: false,
            'ajax': {
                'url': '{{ route('leads.FavPropperty') }}',
                'data': function(data) {
                    var types = [];
                    var LeadId = {{ @$id }};
                    data.LeadId = LeadId;
                }
            },
            columns: [{
                    data: 'Media'
                },
                {
                    data: 'UnparsedAddress'
                },
                {
                    data: 'ListPrice'
                },
            ],
        });
        $('#Logindatatableses').dataTable({

            processing: true,
            serverSide: true,
            searching: false,
            "bLengthChange": false,
            'ajax': {
                'url': '{{ route('leads.LoginDetail') }}',
                'data': function(data) {
                    var types = [];
                    var LeadId = {{ @$id }};
                    data.LeadId = LeadId;
                }
            },
            columns: [{
                    data: 'Sr'
                },
                {
                    data: 'IpAddress'
                },
               
                {
                    data: 'created_at'
                },
            ],
        });
        $('#datatableses').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 10,
        });
        $('#EmailTable').dataTable({
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 10,
        });
        $("#PageviewedTable").dataTable({
            processing: true,
            serverSide: true,
            searching: false,
            "bLengthChange": false,
            'ajax': {
                'url': '{{ route('leads.PageViewed') }}',
                'data': function(data) {
                    var types = [];
                    var LeadId = {{ @$id }};
                    data.LeadId = LeadId;
                }
            },
            columns: [{
                    data: 'PageUrl'
                },
                {
                    data: 'IpAddress'
                },
                {
                    data: 'count'
                },
                {
                    data: 'created_at'
                },

            ],
        });
        $(document).on('submit', '#EditLeads', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#rule-btn').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/leads/UpdateLeadAgent') }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('response', response);
                    toastr.success(response.message, 'Success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(status, error) {
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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

        })
        $(document).on('submit', '#NotesForm', function(e) {
            e.preventDefault();
            var Notes = $('#AddComment').val();
            var type = $("input[name='radio']:checked").val();
            var LeadId = {{ @$id }};
            var AgentId = $("#AgentId").val();
            var data = {
                'Notes': Notes,
                'type': type,
                'LeadId': LeadId,
                "AgentId": AgentId,
                "_token": "{{ csrf_token() }}"
            };
            // alert(data);
            console.log(data);
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/leads/AddNotes') }}',
                data: data,
                success: function(response) {
                    console.log('response', response);
                    toastr.success(response.message, 'Success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
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
        });
        // $(document).on("change",'#emailTemp',function(){
        //     var id=$(this).val();
        //     consol.log(id);
        // });
        $(document).on('change', '#emailTemp', function() {
            var id = $(this).val();
            var data = {
                'id': id,
                "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/leads/EmailTemp') }}',
                data: data,
                success: function(response) {
                    console.log('response', response);
                    $('#add_content').html(response);
                    // var x = document.getElementById("add_content").value;
                    CKEDITOR.replace('add_content');
                    CKEDITOR.add;
                    CKEDITOR.instances.add_content.setData(response);
                    // $('.carousel-indicators').html(response.indicators);
                    // $('#largeModal').modal('show');
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
        });
        $(document).on('submit', '#EmaitForm', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#rule-btn-Email').removeClass('d-none');
            $('#SubmitEmailBtn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/leads/AddMail') }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('response', response);
                    toastr.success(response.message, 'Success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(status, error) {
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn-Email').addClass('d-none');
                    $('#SubmitEmailBtn').attr('disabled', false);
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

        })
           // A function to format text to look like a phone number
           function phoneFormat(input){
                // Strip all characters from the input except digits
                input = input.replace(/\D/g,'');
                
                // Trim the remaining input to ten characters, to preserve phone number format
                input = input.substring(0,10);

                // Based upon the length of the string, we add formatting as necessary
                var size = input.length;
                if(size == 0){
                        input = input;
                }else if(size < 4){
                        input = '('+input;
                }else if(size < 7){
                        input = '('+input.substring(0,3)+') '+input.substring(3,6);
                }else{
                        input = '('+input.substring(0,3)+') '+input.substring(3,6)+' - '+input.substring(6,10);
                }
                return input; 
        }
        document.getElementById('phone').addEventListener('keyup',function(evt){
                var phoneNumber = document.getElementById('phone');
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                phoneNumber.value = phoneFormat(phoneNumber.value);
        });
        $(document).on('submit', '#AssignAgentForm', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#rule-btn-Email2').removeClass('d-none');
            $('#SubmitEmailBtn2').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{ url('api/v1/agent/leads/ChangeAgent') }}',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log('response', response);
                    toastr.success(response.message, 'Success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(status, error) {
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn-Email').addClass('d-none');
                    $('#SubmitEmailBtn').attr('disabled', false);
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

        })
    </script>
@endsection
