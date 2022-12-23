@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
<link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Notification css (Toastr) -->
        <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .select2{
        height: calc(1.5em + .9rem + 2px) !important;
    }
    .form-control{
        min-height: calc(1.5em + .9rem + 2px) !important;
    }
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + .9rem + 2px) !important;
        padding: .4rem !important;
    }
    .select2-search__field{
        height: calc(.9rem + ..8rem + 2px) !important;
        /*padding: .4rem !important;*/
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-weight: bold;
        margin-right: 2px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #aaa;
        border: 1px solid #aaa;
        border-radius: 4px;
        cursor: default;
        float: left;
        margin-right: 5px;
        margin-top: 5px;
        padding: 0 5px;
    }
    .select2 {
         height: auto !important;
    }
</style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
             <!-- Start Content-->
            <section id="justified-bottom-border">
                    <div class="row match-height">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card">
                                <div class="card-header ">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="card-title card-title-heading font-family-class">
                                            <?php if(isset($campaign)){ echo 'Edit'; }else{ echo 'Add'; }?>  Campaigns
                                            </h4>
                                        </div>
                                        <div class="col-4 text-right">
                                                <a href="{{url('agent/campaign/Leadcampaign/')}}" class="btn btn-purple">All Campaigns</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="pro-add-form" method="POST" action="" name="add_emailed" enctype="multipart/form-data" id="campaignForm">
                                            <input type="hidden" name="" id="campid" value="{{@$campaign->id}}">

                                            <input type="hidden" name="" value="Lead" id="agentTable" class="form-control" readonly>
                                            <div class="inline-group">
                                                <div class="row ">
                                                    <div class="col-md-6">
                                                        <label>Email Template</label>
                                                        <select class="form-control select2" id="emailTemp" onchange="changeTemp()" required>
                                                            <option value="">Select Template</option>
                                                            @if($template)
                                                            @foreach($template as $temp)
                                                                <option value="{{$temp->id}}" <?php if(isset($campaign->template) && $campaign->template==$temp->id){ echo "selected"; }?>>{{$temp->name}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                            <div class="col-md-6">
                                                                <label>Campaign Name</label>
                                                                <input type="text" class="form-control" id="campaignName" name="template_name" required="" placeholder="Campaign Name " value="{{@$campaign->campaign_name}}">
                                                            </div>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="inline-group">
                                                <div class="row d-none">
                                                    <div class="col-md-6">
                                                        <label>Select MLS</label>
                                                        <select class="form-control select2" id="mlsData" >
                                                            <option value="">Select MLS</option>
                                                            @if($mls)
                                                            @foreach($mls as $m)

                                                                <option value="{{@$m['id']}}" selected>{{@$m['mls']}} </option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Select Boards 
                                                        </label>
                                                        <select class="form-control" multiple="multiple" multiple data-placeholder="Select Boards" id="boardName" >

                                                            <?php if(isset($boards)){ 
                                                                $campBoard=json_decode($campaign->board_ids);?>
                                                            @foreach($boards as $board)
                                                            
                                                                <option value="{{$board->ListAOR}}" <?php if(isset($campaign->board_ids)){
                                                                   if(in_array($board->ListAOR,$campBoard)){ echo "selected"; } } 
                                                            ?>> {{$board->ListAOR}}</option>
                                                            @endforeach
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row ">
                                                    <div class="col-md-6">
                                                        <label>Select Lead type</label>
                                                        <select class="form-control select2" id="agentType" required>
                                                            <option>Select Lead type</option>
                                                            <?php if(isset($type) && !empty($type)){?>
                                                                @foreach($type as $key)
                                                                <option value="{{@$key->LeadType}}" <?php if(isset($campaign->agent_type) && $campaign->agent_type==$key->LeadType){ echo 'selected';}?>>{{$key->LeadType}}</option>
                                                                @endforeach
                                                            <?php } ?>
                                                        </select>
                                                    </div>
{{--                                                    <div class="col-md-6">--}}
{{--                                                        <label>Select Property Type</label>--}}
{{--                                                        <select class="form-control " multiple="multiple" multiple data-placeholder="Select offices" id="agentOffice" required>--}}
{{--                                                            <?php if(isset($office) && !empty($office)){--}}
{{--                                                                $officeid=json_decode($campaign->office_ids);--}}
{{--                                                                ?>--}}
{{--                                                                @foreach($office as $key)--}}
{{--                                                                <option value="{{@$key->PropType}}" <?php if(in_array($key->PropType,$officeid)){ echo 'selected';}?>>{{$key->PropType}} - ({{$key->total}})</option>--}}
{{--                                                                @endforeach--}}
{{--                                                            <?php } ?>--}}
{{--                                                        </select>--}}
{{--                                                    </div>--}}
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <label>Select Lead</label>
                                                        <select class="form-control select2 select2-multiple" id="agents" multiple="multiple" multiple data-placeholder="Select Leads" required style="height:100px !important;overflow: hidden;">
                                                            <option>Select Leads</option>
                                                            <?php if(isset($agent) && !empty($agent)){
                                                                $agentid=json_decode($campaign->lead_ids);
                                                                ?>
                                                                @foreach($agent as $key)
                                                                <option value="{{@$key->id}}" <?php if(in_array($key->id,$agentid)){ echo 'selected';}?>>{{$key->ListAgentFullName}}</option>
                                                                @endforeach
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <span class="btn btn-danger mt-4" id="clearagents"><i class="fa fa-times"></i>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Select An Interval</label>
                                                        <select class="form-control select2" id="interval" required>
                                                            <option>Select an interval</option>
                                                            @for($i=1;$i<59;$i++)
                                                                <option value="{{$i}}" <?php if(isset($campaign->send_interval) && $campaign->send_interval==$i){ echo 'selected'; }?>>{{$i}} Seconds</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Start Date</label>
                                                        <input type="date" name="" placeholder="start date" class="form-control" id="startDate" value="{{@$campaign->start_date}}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Start Time</label>
                                                        <input type="time" name="" placeholder="start time" class="form-control" id="startTime"  value="{{@$campaign->start_time}}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Finish Time</label>
                                                        <input type="time" name="" placeholder="finish time" class="form-control" id="finishTime" value="{{@$campaign->finish_time}}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Limit Per Day</label>
                                                        <input type="number" name="" class="form-control" placeholder="select of limit per day" value="{{@$campaign->limit}}" id="Limit" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Subject</label>
                                                        <input type="text" class="form-control" placeholder="Subject" id="subject" value="{{@$campaign->subject}}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Content</label>
                                                        <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"> {{@$campaign->content}}
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                    <span class="sr-only">Loading...</span>
                                                </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
@endsection
@section('pageLevelJS')
<!-- Toastr js -->
        <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

        <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
<script>
    var val = "";
    var agent = 0;
    var agent_email = 0;
    var agent_phone = 0;
    var agent_office_name = 0;
    var agent_office_street = 0;
    var agent_office_city = 0;
    var agent_office_state = 0;
    var agent_office_zip = 0;
    var site_name = 0;
    var site_url = 0;
    $(document).ready(function() {
        $(".msgtemplate,.note-editable").html('');
    });

    function setValue(val) {
        HTML = '';
        // HTML = '<span>{{' + val + '}}</span>';
        // alert(val);
        // return
        switch (val) {
            case "add_agent":
                if (!agent) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent++;
                }
                break;
            case "add_agent_email":
                if (!agent_email) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_email++;
                }
                break;
            case "add_agent_phone":
                if (!agent_phone) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_phone++;
                }
                break;
            case "add_office_name":
                if (!agent_office_name) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_name++;
                }
                break;
            case "add_office_street":
                if (!agent_office_street) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_street++;
                }
                break;
            case "add_office_city":
                if (!agent_office_city) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_city++;
                }
                break;
            case "add_office_state":
                if (!agent_office_state) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_state++;
                }
                break;
            case "add_office_zip":
                if (!agent_office_zip) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_zip++;
                }
                break;
            case "add_site_name":
                if (!site_name) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    site_name++;
                }
                break;
            case "add_site_url":
                if (!site_url) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    site_url++;
                }
                break;

            default:
                break;
        }
        CKEDITOR.instances['content1'].insertHtml(HTML);

    }
</script>
<script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
        <!-- Init js-->
        <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

<script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script>
        var x = document.getElementById("content1").value;
        CKEDITOR.replace('content1');
        CKEDITOR.add;
        CKEDITOR.instances.add_content.setData(x);
    </script>
    </script>
    <script>
        var x = document.getElementById("add_content").value;
        CKEDITOR.replace('add_content');
        CKEDITOR.add;
        CKEDITOR.instances.add_content.setData(x);
    </script>
    <script type="text/javascript">
        function changeTemp(){
            var temp = $('#emailTemp').val();
            var data = {
                'id': temp,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/get-template")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    $('#content1').html(response.content);
                    $('#subject').val(response.subject);
                    CKEDITOR.instances['content1'].setData(response.content);
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
        }
        $(document).on('change','#mlsData',function(){
            getBoard();
        });
        function getBoard(){
            var msl_no=$('#mlsData').val();
            $('#spinLoader').show();
            // console.log('city',cities);
            var data = {
                'msl_no': msl_no,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/get-board")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    var html='';
                    $.each(response.board, function(key, val) {
                        html+='<option value="'+val.ListAOR+'">'+val.ListAOR+'</option>'
                    });
                    $('#boardName').html(html);
                    getagentType();

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
        }
        $(document).on('change','#boardName',function(){
            getagentType();
        });
        function getagentType(){
            var msl_no=$('#mlsData').val();
            var boards=$('#boardName').val();
            var data = {
                'msl_no': msl_no,
                'boards':boards,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/get-agent-type")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    var html='<option value="">Select Agent type</option>';
                    $.each(response.board, function(key, val) {
                        html+='<option value="'+val.AgentLeadType+'">'+val.AgentLeadType+'</option>'
                    });
                     $('#agentType').html(html);
                     getOffice();
                    
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
        }  
        $(document).on('change','#agentType',function(){
            getLeadData();
        });
        $(document).on('change','#agentOffice',function(){
            getLeads();
        });
        function getLeadData(){
            // var msl_no=$('#mlsData').val();
            // var boards=$('#boardName').val();
            var agentType=$('#agentType').val();
            var data = {
                // 'msl_no': msl_no,
                // 'boards':boards,
                'agenttype':agentType,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/get-leads")}}',
                data: data,
                success: function (response) {
                    console.log('response1',response);
                    // var html='';
                    // $.each(response.board, function(key, val) {
                    //     html+='<option value="'+val.PropType+'">'+val.PropType+' - ('+val.total+')</option>'
                    // });
                    //  $('#agentOffice').html(html);
                    //  $("#agents").val([]);
                    //  $("#agents").select2();
                    //  getAgents();
                    var html='<option value="">Select Leads</option>';
                    $.each(response.board, function(key, val) {
                        html+='<option value="'+val.id+'" selected>'+val.ListAgentFullName+' </option>'
                    });
                    $('#agents').html(html);

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
        }
        function getLeads(){
            // var msl_no=$('#mlsData').val();
            // var boards=$('#boardName').val();
            var agentType=$('#agentType').val();
            var office=$('#agentOffice').val();
            var data = {
                // 'msl_no': msl_no,
                // 'boards':boards,
                'agenttype':agentType,
                'office':office,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/get-leads")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    var html='<option value="">Select Agent</option>';
                    $.each(response.board, function(key, val) {
                        html+='<option value="'+val.id+'" selected>'+val.ListAgentFullName+' </option>'
                    });
                     $('#agents').html(html);
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
        }
        $(document).on('click', '#clearagents', function() {
        $("#agents").val([]);
        $("#agents").select2();
        // $("#clearagents").hide();
    });
        $(document).on('submit','#campaignForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var emailTemp = $('#emailTemp').val();
            var agentTable=$('#agentTable').val();
            var campaignName=$('#campaignName').val();
            var mls_id=$('#mlsData').val();
            var boardName=$('#boardName').val();
            var agentType=$('#agentType').val();
            var agentOffice=$('#agentOffice').val();
            var agents=$('#agents').val();
            var interval=$('#interval').val();
            var startDate=$('#startDate').val();
            var startTime=$('#startTime').val();
            var finishTime=$('#finishTime').val();
            var Limit=$('#Limit').val();
            var subject=$('#subject').val();
            var content=$('#content1').val();
            var id=$('#campid').val();
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'campaign_name': campaignName,
                'mls_no':mls_id,
                'office_ids':agentOffice,
                'content':content,
                'agent_table':agentTable,
                'template':emailTemp,
                'board_ids':boardName,
                'agent_type':agentType,
                'lead_ids':agents,
                'send_interval':interval,
                'start_date':startDate,
                'start_time':startTime,
                'finish_time':finishTime,
                'limit':Limit,
                'subject':subject,
                "_token": "{{ csrf_token() }}"
            };
            console.log(data);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/add-campaign")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/campaign/Leadcampaign/')}}";
                    },2000);
                },
                error:function(status,error){
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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
