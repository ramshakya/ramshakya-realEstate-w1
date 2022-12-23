@extends($usertype.'/layouts.app')
@section('pageLevelStyle')
    <link rel="stylesheet" href="{{ asset('assets') }}/superadmin/css/validationEngine.jquery.css" type="text/css"/>
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
                                            <?php if(isset($staff)){ echo 'Edit'; }else{ echo 'Add'; }?> Staff
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <?php if(auth()->user()->person_id == 1) { ?>  <a href="{{url('super-admin/staff')}}" class="btn btn-purple">All Staff</a>
                                            <?php }else{?><a href="{{url('agent/staff')}}" class="btn btn-purple">All Staff</a> <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="add_agent_form" name="add_agent"
                                          enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <input type="hidden" name="id" id="id" value="{{@$staff->id}}">
                                                <label for="Subclass">First Name </label>
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                       placeholder="Enter Agent First Name" value="{{@$staff->first_name}}" autocomplete="off"
                                                       data-validation-engine="validate[required]"
                                                       data-errormessage-value-missing="First Name is required!"
                                                >
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Last Name </label>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                       placeholder="Agent Name" value="{{@$staff->last_name}}" autocomplete="off">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Email Address </label>
                                                <input type="email" class="form-control" id="email" name="email" <?php if(isset($staff)){ echo 'readonly'; }?>
                                                       {{--data-validation-engine="validate[required,custom[email]]"--}}
                                                       data-errormessage-value-missing="Email is required!"
                                                       data-errormessage-custom-error="Let me give you a hint: someone@nowhere.com"
                                                       data-errormessage="This is the fall-back error message."
                                                       placeholder="Enter Agent Email Address" autocomplete="off" value="{{@$staff->email}}">
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Phone Number </label>
                                                <input type="text" class="form-control" id="phone" name="phone_number" autocomplete="off"
                                                       placeholder="(123)456-7890" value="{{@$staff->phone_number}}"
                                                       data-validation-engine="validate[required]"
                                                       data-errormessage-value-missing="Phone Number is required!"
                                                >

                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="Subclass">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" autocomplete="off"
                                                       placeholder="********" {{--data-validation-engine="validate[required]"--}} data-errormessage-value-missing="Password is required!"
                                                >
                                            </div>
                                            <div class="col-md-3 col-sm-6 form-group">
                                                <label for="Subclass">Date Of Birth </label>
                                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                                       autocomplete="off" value="{{@$staff->date_of_birth}}"
                                                       placeholder=""
                                                       data-validation-engine="validate[required]"
                                                       data-errormessage-value-missing="Date Of Birth is required!"
                                                >
                                            </div>
                                            <?php if(auth()->user()->person_id == 1) { ?>
                                            <div class="col-md-3 col-sm-6 form-group">
                                                <label for="Subclass">Admin </label>
                                                <select class="form-control" name="AdminId" id="AdminId"
                                                        data-validation-engine="validate[required]"
                                                        data-errormessage-value-missing="Admin is required!">
                                                    @foreach($agents as $k)
                                                        <option value="{{$k->id}}">{{$k->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <?php }else{ ?>
                                            <input type="hidden" id="AdminId" name="AdminId" value="{{auth()->user()->id}}">
                                            <?php } ?>
{{--                                            <div class="col-md-3 col-sm-6 form-group">--}}
{{--                                                <label for="Subclass">Social Mobile </label>--}}
{{--                                                <input type="number" class="form-control" id="social_mobile" name="social_mobile"--}}
{{--                                                       autocomplete="off"--}}
{{--                                                       placeholder="Enter Agent Social Mobile Number" value="">--}}
{{--                                            </div>--}}
                                            <div class="col-md-3 col-sm-6 form-group">
                                                <label for="Subclass">Gender </label>
                                                <select class="form-control" name="gender_id" id="gender_id"
                                                        data-validation-engine="validate[required]"
                                                        data-errormessage-value-missing="Gender is required!">
                                                    @foreach(@$genders as $gender)
                                                        <option value="{{$gender->id}}" <?php if(@$staff->gender_id==$gender->id){ echo "selected"; } ?>>{{$gender->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-sm-6 form-group">
                                                <label for="Subclass">Country </label>
                                                <select class="form-control" name="country_id" id="country_id"
                                                        data-validation-engine="validate[required]"
                                                        data-errormessage-value-missing="Country is required!">
                                                    @foreach(@$countries as $country)
                                                        <option value="{{$country->id}}" <?php if(@$staff->country_id==$country->id){ echo "selected"; } ?>>{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-sm-6 form-group">
                                                <label for="selectedImage">Image</label>
                                                <input type="file" class="form-control" id="selectedImage" name="selectedImage" autocomplete="off" onchange='openFile(event)'>
                                                <input type="hidden" value="" id="photo" name="photo" class="photo">
                                            </div>
                                            <div class="col-md-3 col-sm-3 form-group">
                                                <img src="{{@$ImageUrl->ImageUrl}}" style="height:70px;width:50px">
                                            </div>
                                            <div class="col-md-12 col-sm-12 form-group">
                                                <label for="address">Address</label>
                                                <textarea class="form-control" rows="6" id="alt_address" name="alt_address"> {{@$staff->alt_address}}</textarea>
                                            </div>
                                            <div class="col-md-12 col-sm-12 form-group">
                                                <button class="btn btn-outline-success waves-effect width-md waves-light" id="submit_btn">
                                                    <div class="spinner-border text-success d-none" role="status" id="rule-btn">
                                                        <span class="sr-only">Loading...</span>
                                                    </div> &nbsp;&nbsp;
                                                    <i aria-hidden="true" class="far fa-check-circle"></i> Save and Exit
                                                </button>
                                            </div>
                                            <input type="hidden" id="person_id" value="3" name="person_id">
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
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine-en.js" type="text/javascript"
            charset="utf-8"></script>
    <script src="{{ asset('assets') }}/superadmin/js/jquery.validationEngine.js" type="text/javascript"
            charset="utf-8"></script>
    <script>
        $("#add_agent_form").validationEngine({
            promptPosition: "center",
            scroll: false,
            ajaxFormValidation: true,
            ajaxFormValidationURL: '{{url('/api/v1/agent/staff/add')}}',
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
                toastr.success("Success", "Staff Updated Successfully");
                // alert("hello");
                setTimeout(function(){
                    window.location.href="{{url($userurl.'/staff')}}";
                },2000);
                $("#add_agent_form")[0].reset();
                $('#rule-btn').addClass('d-none');
                $('#submit_btn').attr('disabled', false);

            }
        });
        
        var openFile = function(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function(){
                var dataURL = reader.result;
                var output = document.getElementById('photo');
                output.value = dataURL;
            };
            reader.readAsDataURL(input.files[0]);
        };
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
    </script>
@endsection
