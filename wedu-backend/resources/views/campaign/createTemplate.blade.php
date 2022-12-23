@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
<!-- Notification css (Toastr) -->
        <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            /*.btn-outlet-warning {
                color: #fff;
                background-color: #f16e00;
                border-color: #f16e00;
            }*/
            .btn-outline-warning {
                color: #f16e00;
                border-color: #f16e00;
            }
            .btn-outline-warning:hover {
                color: #fff;
                background-color: #f16e00;
                border-color: #f16e00;
            }
        </style>
    <div class="content-page">
        <div class="content">
             <!-- Start Content-->
            <section id="justified-bottom-border">
                    <div class="row match-height">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card">
                                <div class="card-header ">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4 class="card-title card-title-heading font-family-class">
                                            <?php if(isset($template)){ echo 'Edit'; }else{ echo 'Add'; }?> Template 
                                            </h4>
                                        </div>
                                        <div class="col-4 text-right">
                                                <a href="{{url('agent/template')}}" class="btn btn-purple">All Template</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                            <input type="hidden" name="" id="tempid" value="{{@$template->id}}">
                                            <div class="inline-group">
                                                <div class="row ">
                                                    <div class="col-md-9">
                                                        <label for="Subclass">Template Type<span class="required">*</span></label>
                                                        <select class="select2 form-control" id="TemplateType" onchange="changeType()" name="type">
                                                            <option value="email" <?php if(isset($template->type) && $template->type=='email'){ echo "selected"; }?> >Email</option>
                                                            <option value="sms" <?php if(isset($template) && $template->type=='sms'){ echo "selected"; }?> >SMS</option>
                                                        </select>
                                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row ">
                                                    <div class="col-md-9">
                                                        <label for="Subclass">Template Name<span class="required">*</span></label>

                                                        <input type="text" class="form-control" id="template_name" name="name" required="" placeholder="Template Name" value="{{@$template->name}}" @if(@$template->name){{'readonly'}}@endif>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <label for="Subclass">Template Subject<span class="required">*</span></label>
                                                        <input type="text" class="form-control" id="subject" name="subject" required="" placeholder="Template Subject" value="{{@$template->subject}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="inline-group" id="">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label for="Subclass">Content<span class="required">*</span></label>

                                                        <div class="row" id="emailContent">
                                                            <div class="col-md-8">
                                                                <textarea class="textarea_editor form-control ck1" required name="content1" id="content1" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"><?php if(isset($template) && $template->type=='email'){ echo $template->content; }?>
                                                                </textarea>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="button" id="btn11"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Lead Name" onclick="setValue('LeadName')">
                                                                <input type="button" id="btn12"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Lead Email" onclick="setValue('LeadEmail')">
                                                                <input type="button" id="btn13"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Lead Phone" onclick="setValue('LeadPhone')">
                                                                <input type="button" id="btn14"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Link" onclick="setValue('ForgetLink')">

                                                                <input type="button" id="btn1" class="btn  btn-inverse btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1"  value="Add Agent" onclick="setValue('Agent')">
                                                                <input type="button" id="btn2"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Agent Email" onclick="setValue('AgentEmail')">
                                                                <input type="button" id="btn3"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Agent Phone" onclick="setValue('AgentPhone')">
                                                                <input type="button" id="btn4"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Office Name" onclick="setValue('OfficeName')">
                                                                <input type="button" id="btn5"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Office Street" onclick="setValue('OfficeStreet')">
                                                                <input type="button" id="btn6"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Office City" onclick="setValue('OfficeCity')">
                                                                <input type="button" id="btn7"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Office State" onclick="setValue('OfficeState')">
                                                                <input type="button" id="btn8"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Office Zip" onclick="setValue('OfficeZip')">
                                                                <input type="button" id="btn9"  class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Site Name" onclick="setValue('SiteName')">
                                                                <input type="button" id="btn10" class="btn btn-outline-warning btn-sm waves-effect waves-light events-btn rounded mb-2 mr-1" value="Add Site URL" onclick="setValue('SiteUrl')">
                                                            </div>
                                                        </div>
                                                        <!-- SMS Containt -->
                                                        <div class="row" id="smsContent">
                                                            <div class="col-9">
                                                                <textarea class="form-control" cols="3" id="content"><?php if(isset($template) && $template->type=='sms'){ echo $template->content; }?>
                                                                </textarea>
                                                            </div>
                                                        </div>
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
                        <!-- content -->
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
    var lead_name = 0;
    var lead_email = 0;
    var lead_phone = 0;
    $(document).ready(function() {
        $(".msgtemplate,.note-editable").html('');
    });

    function setValue(val) {
        console.log(val);
        var HTML='';
        switch (val) {
            case "Agent":
                // if (!agent) {
                    HTML = '<span>{'+val+'}</span><br/>';
                    agent++;
                // }
                break;
            case "AgentEmail":
                // if (!agent_email) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_email++;
                // }
                break;
            case "AgentPhone":
                // if (!agent_phone) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_phone++;
                // }
                break;
            case "OfficeName":
                // if (!agent_office_name) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_office_name++;
                // }
                break;
            case "OfficeStreet":
                // if (!agent_office_street) {
                    HTML = '<span>{{' + val + '}}</span><br/>';
                    agent_office_street++;
                // }
                break;
            case "OfficeCity":
                // if (!agent_office_city) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_office_city++;
                // }
                break;
            case "OfficeState":
                // if (!agent_office_state) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_office_state++;
                // }
                break;
            case "OfficeZip":
                // if (!agent_office_zip) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    agent_office_zip++;
                // }
                break;
            case "SiteName":
                // if (!site_name) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_name++;
                // }
                break;
            case "SiteUrl":
                // if (!site_url) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_url++;
                // }
                break;
            case "LeadName":
                // if (!lead_name) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_url++;
                // }
                break;
            case "LeadEmail":
                // if (!lead_email) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_url++;
                // }
                break;
            case "LeadPhone":
                // if (!lead_phone) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_url++;
                // }
                break;
            case "ForgetLink":
                // if (!lead_phone) {
                    HTML = '<span>{' + val + '}</span><br/>';
                    site_url++;
                // }
                break;

            default:
                break;
        }
        console.log(HTML);
        CKEDITOR.instances['content1'].insertHtml(HTML);

    }
</script>
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
        changeType();
        function changeType(){
            var type = $('#TemplateType').val();
            if(type=='email'){
                $('#emailContent').show();
                $('#smsContent').hide();
            } else{
                $('#emailContent').hide();
                $('#smsContent').show();
            }
            
        }
        $(document).on('submit','#templateForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var name = $('#template_name').val();
            var type=$('#TemplateType').val();
            var subject=$('#subject').val();
            var emailcontent=$('#content1').val();
            var smscontent=$('#content').val();
            var agent_id=$('#agent_id').val();
            var id=$('#tempid').val();
            if(type=='email'){
                var content=emailcontent;
            }else{
                var content=smscontent;
            }
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'name': name,
                'type':type,
                'subject':subject,
                'content':content,
                'agent_id':agent_id,
                "_token": "{{ csrf_token() }}"
            };

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/campaign/add-template")}}',
                data: data,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        window.location.href="{{url('agent/template')}}";
                    },2000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
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

