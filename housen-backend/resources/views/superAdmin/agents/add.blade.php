@extends('.superAdmin/layouts.app')
@section('pageLevelStyle')
    <link rel="stylesheet" href="{{ asset('assets') }}/superadmin/css/validationEngine.jquery.css" type="text/css"/>
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
            <div class="container bg-white form-container">
                <form class="pro-add-form" id="add_agent_form" name="add_agent"
                      enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="Subclass">First Name </label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   placeholder="Enter Agent First Name" value="" autocomplete="off"
                                   data-validation-engine="validate[required]"
                                   data-errormessage-value-missing="First Name is required!"
                            >
                        </div>
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="Subclass">Last Name </label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   placeholder="Agent Name" value="" autocomplete="off">
                        </div>
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="Subclass">Email Address </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   data-validation-engine="validate[required,custom[email]]"
                                   data-errormessage-value-missing="Email is required!"
                                   data-errormessage-custom-error="Let me give you a hint: someone@nowhere.com"
                                   data-errormessage="This is the fall-back error message."
                                   placeholder="Enter Agent Email Address" autocomplete="off" value="">
                        </div>
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="Subclass">Phone Number </label>
                            <input type="number" class="form-control" id="phone" name="phone_number" autocomplete="off"
                                   placeholder="123-456-7890"
                                   data-validation-engine="validate[required]"
                                   data-errormessage-value-missing="Phone Number is required!"
                            >

                        </div>
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="Subclass">Password</label>
                            <input type="password" class="form-control" id="password" name="password" autocomplete="off"
                                   placeholder="********"
                                   data-validation-engine="validate[required]"
                                   data-errormessage-value-missing="Password is required!"
                            >
                        </div>
                        <div class="col-md-3 col-sm-6 form-group">
                            <label for="Subclass">Date Of Birth </label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   autocomplete="off"
                                   placeholder=""
                                   data-validation-engine="validate[required]"
                                   data-errormessage-value-missing="Date Of Birth is required!"
                            >
                        </div>
                        <div class="col-md-3 col-sm-6 form-group">
                            <label for="Subclass">Social Mobile </label>
                            <input type="number" class="form-control" id="social_mobile" name="social_mobile"
                                   autocomplete="off"
                                   placeholder="Enter Agent Social Mobile Number" value="">
                        </div>
                        <div class="col-md-3 col-sm-6 form-group">
                            <label for="Subclass">Gender </label>
                            <select class="form-control" name="gender_id" id="gender_id"
                                    data-validation-engine="validate[required]"
                                    data-errormessage-value-missing="Gender is required!">
                                @foreach(@$genders as $gender)
                                    <option value="{{$gender->id}}">{{$gender->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 form-group">
                            <label for="Subclass">Country </label>
                            <select class="form-control" name="country_id" id="country_id"
                                    data-validation-engine="validate[required]"
                                    data-errormessage-value-missing="Country is required!">
                                @foreach(@$countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-6 form-group">
                            <label for="photo">Image</label>
                            <input type="file" class="form-control" id="photo" name="photo" autocomplete="off">
                        </div>
                        <div class="col-md-12 col-sm-12 form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" rows="6" id="alt_address" name="alt_address"></textarea>
                        </div>
                        <button class="btn btn-outline-purple waves-effect width-md waves-light" id="submit_btn">
                            <div class="spinner-border text-success d-none" role="status" id="rule-btn">
                                <span class="sr-only">Loading...</span>
                            </div> &nbsp;&nbsp;
                            <i aria-hidden="true" class="far fa-check-circle"></i> Save and Exit
                        </button>
                        <input type="hidden" id="person_id" value="2" name="person_id">
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('scriptContent')
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine-en.js" type="text/javascript"
            charset="utf-8"></script>
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine.js" type="text/javascript"
            charset="utf-8"></script>
    <script>
        $("#add_agent_form").validationEngine({
            promptPosition: "center",
            scroll: false,
            ajaxFormValidation: true,
            ajaxFormValidationURL: '{{url("/api/v1/super-admin/agent/add")}}',
            ajaxFormValidationMethod: 'POST',
            onBeforeAjaxFormValidation: function () {
                $('#rule-btn').removeClass('d-none');
                $('#submit_btn').attr('disabled', true);
            },
            onFailure: function (res) {

                errors = res.responseJSON;
                $.each(errors.errors, function (key, value) {
                    toastr.error(value[0], key);
                });
                $('#rule-btn').addClass('d-none');
                $('#submit_btn').attr('disabled', false);
            },
            onAjaxFormComplete: function (status, b, c, d) {
                toastr.success("Success", "form Submited");
                window.location = "{{url('super-admin/agent/')}}"; 
                $("#add_agent_form")[0].reset();
                $('#rule-btn').addClass('d-none');
                $('#submit_btn').attr('disabled', false);
            }
        });

    </script>
@endsection
