@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
     <link href="{{ asset('assets') }}/agent/css/new_style.css" rel="stylesheet" type="text/css" />
     <style type="text/css">
         .spinner-border{
            width: 1rem;
            height: 1rem;
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
                                            <?php if(isset($builder)){ echo 'Edit'; }else{ echo 'Add'; }?> Builder Details
                                        </h4>
                                        
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/building/builders')}}" class="btn btn-purple">All Builder List</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                     <form class="modal-content" id="builderDetails" method="post" enctype="form-data/multipart">
                                        @csrf
                                        <!-- Modal Header -->
                                        <!-- Modal body -->
                                        <input type="hidden" name="id" value="{{@$builder->id}}">
                                        <div class="modal-body row">
                                            <div class="col-md-12 mt-2">
                                                <label>Builder Name<span class="required">*</span></label>
                                                <input type="text" class="form-control" name="BuilderName" required="" placeholder="Builder Name" value="{{@$builder->BuilderName}}">
                                            </div>
                                            <!-- <div class="col-md-6 mt-2">
                                                <label>Phone<span class="required">*</span></label>
                                                <input type="number" class="form-control" name="BuilderPhone" required="" value="{{@$builder->BuilderPhone}}" placeholder="Phone">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Email<span class="required">*</span></label>
                                                <input type="email" class="form-control" name="BuilderEmail" required="" value="{{@$builder->BuilderEmail}}" placeholder="Email">
                                            </div>
                                            
                                            
                                            <div class="col-md-6 mt-2">
                                                <label>Address</label>
                                                <input type="text" name="BuilderAddress" id="address_id" class="form-control" value="{{@$builder->BuilderAddress}}">
                                                
                                            </div>
                                             <div class="col-md-6 mt-2">
                                                <label>Postal Code<span class="required">*</span></label>
                                                <input type="text" class="form-control" name="BuilderPostalCode" value="{{@$builder->BuilderPostalCode}}" id="post_code" required="" placeholder="Postal Code">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Country<span class="required">*</span></label>
                                                <input type="text" name="BuilderCountry" class="form-control" value="{{@$builder->BuilderCountry}}" id="Country">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>City<span class="required">*</span></label>
                                                <input type="text" class="form-control" id="City" name="BuilderCity" required="" placeholder="City" value="{{@$builder->BuilderCity}}">
                                            </div>
                                            <div class="col-md-6 mt-2">
                                                <label>Province/State<span class="required">*</span></label>
                                                 <input type="text" class="form-control" id="State" name="BuilderState" required="" placeholder="State" value="{{@$builder->BuilderState}}">
                                               
                                            </div> -->
                                           
                                            
                                            <div class="col-md-12 mt-2">
                                                <label for="Subclass">Description</label>
                                                <textarea class="form-control" name="BuilderDescription">{{@$builder->BuilderDescription}}</textarea>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="Subclass">First Logo</label>
                                                @if(@$builder->Logo)
                                                <img src="{{@$builder->Logo}}" width="70px" height="40px">
                                                @endif
                                                <input type="file" class="form-control" name="Logo">
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="Subclass">Second Logo</label>
                                                @if(@$builder->SecondLogo)
                                                <img src="{{@$builder->SecondLogo}}" width="70px" height="40px">
                                                @endif
                                                <input type="file" class="form-control" name="SecondLogo">
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <label for="Subclass">Third Logo</label>
                                                @if(@$builder->ThirdLogo)
                                                <img src="{{@$builder->ThirdLogo}}" width="70px" height="40px">
                                                @endif
                                                <input type="file" class="form-control" name="ThirdLogo">
                                            </div>
                                            <div class="col-md-6 pt-3">
                                                <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 float" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save</button>
                                            </div>
                                        </div>
                                        
                                        
                                        <!-- Modal footer -->
                                        
                                        
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
    <!-- <script src="{{ asset('assets') }}/agent/js/image_script.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("Content").value;
            CKEDITOR.replace('Content');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>
   
    <script type="text/javascript">
       

    </script>
    <!-- Adding Builder Details -->
    <script>
        $(document).on('submit','#builderDetails',function(e){
            e.preventDefault();
            var form_data = new FormData($('#builderDetails')[0]);
            form_data.append('AgentId','{{\Illuminate\Support\Facades\Auth::user()->id}}');
            
            // $('#builderDetails input').val('');
            // $('#builderDetails select').val('');
            // $('#builderDetails textarea').val('');
            $('#rule-btn2').removeClass('d-none');
             $('#SubmitBtn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/building/add_builder_data")}}',
                data: form_data,
                contentType:false,
                processData:false,
                success: function (response) {
                    // console.log('data',data);
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/pages/')}}";
                        location.reload();
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
     <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDG5Md4sE3QWkq9bPOrpypRj0wsXnoa4ZY&callback=initAutocomplete&libraries=places&v=weekly"
        async
    ></script>
    <script>
        let autocomplete;
        let address1Field;
        // let address2Field;
        let postalField;

        function initAutocomplete() {
            address1Field = document.querySelector("#address_id");
            // address2Field = document.querySelector("#address2");
            postalField = document.querySelector("#post_code");
            autocomplete = new google.maps.places.Autocomplete(address1Field, {
                componentRestrictions: { country: ["us", "ca","in"] },
                fields: ["address_components", "geometry"],
                types: ["address"],
            });
            address1Field.focus();
            autocomplete.addListener("place_changed", fillInAddress);
        }

        function fillInAddress() {
            const place = autocomplete.getPlace();
            let address1 = "";
            let postcode = "";
            var lat = place.geometry.location.lat(),
                lng = place.geometry.location.lng();
            // document.querySelector("#Lat").value = lat;
            // document.querySelector("#Lng").value = lng;
            // console.log(lat);
            for (const component of place.address_components) {
                const componentType = component.types[0];
                switch (componentType) {
                    
                    case "postal_code": {
                        postcode = `${component.long_name}${postcode}`;
                        document.querySelector("#post_code").value = component.long_name;

                        break;
                    }
                    case "country": {
                        document.querySelector("#Country").value = component.long_name;
                        break;
                    }
                    case "locality": {
                        document.querySelector("#City").value = component.long_name;
                        break;
                    }
                    case "administrative_area_level_1": {
                        // document.querySelector("#locality").value = component.long_name;
                        document.querySelector("#State").value = component.long_name;
                        break;
                    }
                    case "route": {
                        address1 += component.short_name;
                        document.querySelector("#address_id").value = component.long_name;
                        break;
                    }
                  
                }

            }

            address1Field.value = address1;
            postalField.value = postcode;
            // After filling the form with address components from the Autocomplete
            // prediction, set cursor focus on the second address line to encourage
            // entry of subpremise information such as apartment, unit, or floor number.
            // address2Field.focus();
        }
$('#address_id').keydown(function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});
    </script>
@endsection
