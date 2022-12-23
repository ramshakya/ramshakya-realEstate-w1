@extends('agent/layouts.app')
@section('pageLevelStyle')
    <style>
        .form-container {
            padding: 40px !important;
        }
        .card-title {
            margin-bottom: .1rem;
        }
        .nav-item a.nav-link{
            color: #5b69bc  !important;
        }
        .dataTable{
            margin-top: 0px;
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
            <section id="justified-bottom-border">
                <div class="row match-height">
                    <div class="col-xl-6 col-lg-6">
                        <div class="card">
{{--                            <div class="card-header ">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-8">--}}
{{--                                        <h4 class="card-title card-title-heading font-family-class">--}}
{{--                                            Myaccount--}}
{{--                                        </h4>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-4 text-right">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="EditLeads" name="add_agent" enctype="multipart/form-data">
                                        <?php
                                            $lead->FirstName="";
                                            $lead->LastName="";
                                            if(isset($lead) && !empty($lead->ContactName)){
                                                $names=explode(" ",$lead->ContactName);
                                                if(isset($names[0])){
                                                    $lead->FirstName=$names[0];
                                                }
                                                if(isset($names[0])){
                                                    $lead->LastName=$names[1];
                                                }
                                            } ?>
                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <img src="{{ asset('assets') }}/agent/images/avatar.png" alt="user-image" width="110" class="rounded-circle img-thumbnail">
                                            <p><br/><b>Joined At: </b>2021-08-03</p>
                                                <p><b>Agent Assigned </b></p>
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <input type="hidden" name="id" id="id" value="{{@$staff->id}}">
                                                <label for="Subclass">First Name </label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter Agent First Name" value="{{@$lead->FirstName}}" autocomplete="off" data-errormessage-value-missing="First Name is required!">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Last Name </label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Agent Name" value="{{@$lead->LastName}}" autocomplete="off">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Email Address </label>
                                                <input type="email" class="form-control" id="email" name="email" <?php if(isset($staff)){ echo 'readonly'; }?>
                                                       placeholder="Enter Agent Email Address" autocomplete="off" value="{{@$lead->Email}}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Mobile Number </label>
                                                <input type="text" class="form-control" id="phone" name="phone_number" autocomplete="off" placeholder="123-456-7890" value="{{@$lead->Phone}}" data-errormessage-value-missing="Phone Number is required!">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Date Of Birth </label>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" autocomplete="off" value="{{@$staff->date_of_birth}}" placeholder="" data-errormessage-value-missing="Date Of Birth is required!"
                                                >
                                            </div>

                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Worked At </label>
                                                <input type="text" class="form-control" id="WorkedAt" name="WorkedAt" autocomplete="off" placeholder="Enter Agent Social Mobile Number" value="{{@$lead->LeadType}}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Type </label>
                                                <input type="text" class="form-control" id="type" name="type" autocomplete="off" placeholder="Enter Agent Social Mobile Number" value="{{@$lead->LeadType}}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Status </label>
                                                <input type="text" class="form-control" id="Agentstatus" name="status" autocomplete="off" placeholder="Enter Agent Social Mobile Number" value="{{@$lead->Status}}">
                                            </div>
                                            <div class="col-md-12 col-sm-12 form-group">
                                                <button class="btn btn-outline-success waves-effect width-md waves-light" id="submit_btn">
                                                    <div class="spinner-border text-success d-none" role="status" id="rule-btn">
                                                        <span class="sr-only">Loading...</span>
                                                    </div> &nbsp;&nbsp;
                                                    <i aria-hidden="true" class="far fa-check-circle"></i> Save and Exit
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
                                            <a href="#home2" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">Notes & Call</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile2" data-toggle="tab" aria-expanded="true" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">SMS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages2" data-toggle="tab" aria-expanded="false" class="nav-link">
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
                                                        <th colspan="" >Type</th>
                                                        <th colspan="" >Comment</th>
                                                        {{--                                                <th colspan="" >Action</th>--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i=1;?>
                                                    @if($notes)
                                                        @foreach($notes as $k)
                                                            <tr>
                                                                <td style="width:50px;">{{$k->id}}</td>
                                                                <td class="hidden-xs">{{$k->Type}}</td>
                                                                <td class="max-texts border ">{{$k->Notes}}</td>
                                                                </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div><br/>
                                            <form id="NotesForm" name="Notes" enctype="multipart/form-data" method="post">
                                                <input type="hidden" value="{{@$AgentId}}" id="AgentId">
                                                <div class="radio radio-primary col-md-12 col-sm-12">
                                                    <input type="radio" name="radio" id="radio1" value="Notes" class="NotesType" checked>
                                                    <label for="radio1" class="pr-3">
                                                        Notes
                                                    </label>
                                                    <input type="radio" name="radio" id="radio13" class="NotesType" value="Calls">
                                                    <label for="radio13">
                                                        Calls
                                                    </label>
                                                </div><br/>
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <textarea class="form-control" id="AddComment" name="alt_address" placeholder="Add Comment"> </textarea>
                                                </div>
                                                <div class="col-md-12 col-sm-12 form-group">
                                                    <button class="btn btn-outline-success waves-effect width-md waves-light" id="NotificationBtn">
                                                        <div class="spinner-border text-success d-none" role="status" id="rule-btn">
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
                                                            <button class="btn btn-purple" data-toggle="modal" data-target="#exampleModalLong">Add Email</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered mb-0" id="EmailTable">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="">Sr. No</th>
                                                        <th colspan="" >Subject</th>
                                                        <th colspan="" >Message</th>
                                                        {{--                                                <th colspan="" >Action</th>--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i=1;?>
                                                    @if($leademail)
                                                        @foreach($leademail as $k)
                                                            <tr>
                                                                <td style="width:50px;">{{$k->id}}</td>
                                                                <td class="hidden-xs">{{$k->Subject}}</td>
                                                                <td class="max-texts border ">{{$k->Message}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div><br/>
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
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Lead Activity
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="nav-item">
                                            <a href="#home2" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">Properties Viewed</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#profile2" data-toggle="tab" aria-expanded="true" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">Page viewed</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#messages2" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                <span class="d-none d-sm-block">Marked Favourite Properties</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#settings2" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Saved Search</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#settings3" data-toggle="tab" aria-expanded="false" class="nav-link">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Recent Login</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade show active" id="home2">
                                            <p class="mb-0">Raw denim you probably haven't heard
                                                of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro
                                                synth master cleanse. Mustache cliche tempor, williamsburg carles
                                                vegan helvetica. Reprehenderit butcher retro keffiyeh dreamcatcher
                                                synth. Cosby sweater eu banh mi, qui irure terry richardson ex
                                                squid. Aliquip placeat salvia cillum iphone. Seitan aliquip quis
                                                cardigan american apparel, butcher voluptate nisi qui.</p>
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
                                            <p class="mb-0">Etsy mixtape wayfarers, ethical
                                                wes anderson tofu before they sold out mcsweeney's organic lomo
                                                retro fanny pack lo-fi farm-to-table readymade. Messenger bag
                                                gentrify pitchfork tattooed craft beer, iphone skateboard locavore
                                                carles etsy salvia banksy hoodie helvetica. DIY synth PBR banksy
                                                irony. Leggings gentrify squid 8-bit cred pitchfork. Williamsburg
                                                banh mi whatever gluten-free, carles pitchfork biodiesel fixie etsy
                                                retro mlkshk vice blog. Scenester cred you probably haven't heard of
                                                them, vinyl craft beer blog stumptown. Pitchfork sustainable tofu
                                                synth chambray yr.</p>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="settings2">
                                            <p class="mb-0">Trust fund seitan letterpress,
                                                keytar raw denim keffiyeh etsy art party before they sold out master
                                                cleanse gluten-free squid scenester freegan cosby sweater. Fanny
                                                pack portland seitan DIY, art party locavore wolf cliche high life
                                                echo park Austin. Cred vinyl keffiyeh DIY salvia PBR, banh mi before
                                                they sold out farm-to-table VHS viral locavore cosby sweater. Lomo
                                                wolf viral, mustache readymade thundercats keffiyeh craft beer marfa
                                                ethical. Wolf salvia freegan, sartorial keffiyeh echo park
                                                vegan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 col-sm-12 form-group">
                        <label for="Subclass">To </label>
                        <input type="email" class="form-control" id="email" name="email"
                        placeholder="Enter Agent Email Address" autocomplete="off" value="{{@$lead->Email}}">
                    </div>
                    <div class="col-md-12 col-sm-12 form-group">
                        <label for="Subclass">Subject </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Agent Email Address" autocomplete="off" value="">
                    </div>
                    <div class="col-md-12 col-sm-12 form-group">
                        <label for="Subclass">Choose </label>
                        <select class="form-control" name="country_id" id="country_id" data-errormessage-value-missing="Country is required!">
                            <option value="">Select Template</option>
                        </select>
                    </div>
                    <div class="col-md-12 col-sm-12 form-group">
                        <label for="address">Message</label>
                        <textarea class="form-control" rows="6" id="alt_address" name="alt_address"> </textarea>
                    </div>
                </div>
                <div class="modal-footer">
{{--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
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
    <script type="text/javascript">
        $('#datatableses').dataTable( {
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 20,
        } );
        $('#EmailTable').dataTable( {
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 20,
        } );
        $(document).on('submit','#NotesForm',function(e){
            e.preventDefault();
            var Notes = $('#AddComment').val();
            var type=$("input[name='radio']:checked").val();
            var LeadId={{@$id}};
            var AgentId=$("#AgentId").val();
            var data = {
                'Notes': Notes,
                'type':type,
                'LeadId':LeadId,
                "AgentId":AgentId,
                "_token": "{{ csrf_token() }}"
            };
            // alert(data);
            console.log(data);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/leads/AddNotes")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    if(status.status == 401){
                        $.each(errors.error, function(i,v){
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
@endsection
