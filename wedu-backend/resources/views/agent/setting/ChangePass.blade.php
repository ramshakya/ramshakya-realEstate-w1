@extends('agent/layouts.app')
@section('pageLevelStyle')
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <style>
        .form-container {
            padding: 40px !important;
        }
    </style>
@endsection
@section('pageContent')
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
                                            Change Password
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="add_agent_form" name="add_agent"
                                          enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-4 form-group">
                                                <input type="hidden" name="id" id="id" value="{{@$id}}">
                                                <label for="Subclass">Old Password </label>
                                                <input type="password" class="form-control" id="OldPass" name="OldPass"
                                                       placeholder="Enter Old Password " autocomplete="off" required
                                                >
                                            </div>
                                            <div class="col-md-4 col-sm-4 form-group">
                                                <label for="Subclass">New Password </label>
                                                <input type="password" class="form-control" id="NewPass" required name="NewPass"
                                                       placeholder="New Password" autocomplete="off"
                                                       data-validation-engine="validate[required]"
                                                       data-errormessage-value-missing="New Password required!">
                                            </div>
                                            <div class="col-md-4 col-sm-4 form-group">
                                                <label for="Subclass">Confirm Password </label>
                                                <input type="password" class="form-control" id="ConfirmPass" required name="ConfirmPass"
                                                placeholder="Confirm Password" autocomplete="off"
                                                       data-validation-engine="validate[required]"
                                                       data-errormessage-value-missing="Confirm Password required!">
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
                </div>
            </section>
        </div>
    </div>

@endsection
@section('scriptContent')
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine-en.js" type="text/javascript"
            charset="utf-8"></script>
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine.js" type="text/javascript"
            charset="utf-8"></script>
    <script>
        $(document).on('submit','#add_agent_form',function(e){
            e.preventDefault();
            $('#rule-btn').removeClass('d-none');
            $('#btnSubmit').attr('disabled', true);
            var formData = new FormData(this);
            // var listing=$('#ListingId').val();

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/setting/changePassword")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    if(response.success) {
                        toastr.success(response.message, 'Success');
                        $('#rule-btn5').addClass('d-none');
                        $('#btnSubmit5').attr('disabled', false);
                        setTimeout(function () {
                            $('#rule-btn').addClass('d-none');
                            $('#btnSubmit').attr('disabled', false);
                            location.reload();
                        }, 2000);
                    }
                    if(response.error){
                        toastr.error(response.message, 'error');
                        setTimeout(function () {
                            $('#rule-btn').addClass('d-none');
                            $('#btnSubmit').attr('disabled', false);
                        });
                    }
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    $('#rule-btn').addClass('d-none');
                    $('#btnSubmit').attr('disabled', false);
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
