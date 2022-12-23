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
                                            <?php if(isset($PreConstruction)){ echo 'Edit'; }else{ echo 'Create'; }?> Pre Construction Building
                                        </h4>
                                        
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/building/')}}" class="btn btn-purple">All Bulding List</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="PreConstuctionForm" method="POST" action="" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{@$PreConstruction->id}}">
                               <div id="accordion">
                                <div class="card">
                                    <div class="card-header" data-toggle="collapse" data-target="#collapseOne">
                                      <h4>Building Address <i class="fas fa-sort-down downBtn"></i></h4>

                                    </div>
                                    <div id="collapseOne" class="collapse show">
                                      <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-6">
                                                    <label for="Subclass">Building Name<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="BuildingName" name="BuildingName" required="" placeholder="Building Name" value="{{@$PreConstruction->BuildingName}}">

                                                    <label for="Subclass" class="mt-2">Builder Name<span class="required">*</span></label>
                                                    <select class="form-control form-contro" required="" name="BuilderId" id="All_Builder">
                                                        <option value="">Select</option>
                                                    @if($builder)
                                                    @foreach($builder as $builderDetail)
                                                        <option value="{{$builderDetail->id}}" @if(@$PreConstruction->BuilderId){{'selected'}}@endif>{{$builderDetail->BuilderName}}</option>
                                                    @endforeach
                                                    @endif
                                                        
                                                    </select>
                                                    <a type="button" class="btn btn-success plusbtn" href="{{url('agent/building/add-edit-builder')}}"><i class="fas fa-plus"></i></a>

                                                    <label for="Subclass" class="mt-2">Address<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="address_id" name="Address" required="" placeholder="Address" value="{{@$PreConstruction->Address}}">

                                                    <label for="Subclass" class="mt-2">Country</label>
                                                   <input type="text" name="Country" id="Country" class="form-control" value="{{@$PreConstruction->Country}}">

                                                    

                                                    <label for="Subclass" class="mt-2">City</label>
                                                    <input type="text" class="form-control" id="City" name="City" placeholder="City" value="{{@$PreConstruction->City}}">

                                                    <label for="Subclass" class="mt-2">Province / State</label>
                                                   <input type="text" name="State" class="form-control" id="State" value="{{@$PreConstruction->State}}">
                                                    
                                                </div>
                                                <div class="col-md-6 col-lg-6">
                                                   <!-- <iframe src="https://www.google.com/maps/d/u/0/embed?mid=1-VlXsvMWMr8EotfMcIwYKt-1SrI" width="100%" height="100%" style="" id="Map"></iframe> -->
                                                   <div id="map" style="height: 100%;width: 100%;"></div>
                                                    <!-- map --></div>
                                                <div class="col-md-6 col-lg-6">
                                                    <label for="Subclass" class="mt-2">Postal Code</label>
                                                    <input type="text" class="form-control" id="post_code" name="PostelCode" placeholder="Postel Code" value="{{@$PreConstruction->PostelCode}}">
                                                    <label for="Subclass" class="mt-2">Main Interection</label>
                                                    <input type="text" class="form-control" id="MainInterection" name="MainInterection" placeholder="Main Interection" value="{{@$PreConstruction->MainInterection}}">

                                                   
                                                    <!-- <label for="Subclass" class="mt-2">Demo_f</label>
                                                    <input type="text" class="form-control" id="Demo_f" name="DemoF" placeholder="Demo_f" value="{{@$PreConstruction->DemoF}}"> -->
                                                </div>
                                                <div class="col-md-6 col-lg-6">
                                                    <label for="Subclass" class="mt-2">Community</label>
                                                    <input type="text" class="form-control" id="Community" name="Community" placeholder="Community" value="{{@$PreConstruction->Community}}">

                                                   <!--  <label for="Subclass" class="mt-2">Demo</label>
                                                     <select class="form-control" name="Demo">
                                                        <option>Select</option>
                                                        <option value="1">one</option>
                                                        <option value="2">two</option>
                                                    </select> -->

                                                   <!--  <label for="Subclass" class="mt-2">Addr_info</label>
                                                    <select class="form-control" name="AddrInfo">
                                                        <option>Select</option>
                                                        <option value="1">one</option>
                                                        <option value="2">two</option>
                                                    </select> -->
                                                </div>
                                            </div>
                                      </div>
                                    </div>

                                    <div class="card-header mt-2" data-toggle="collapse" data-target="#collapseTwo">
                                      <h4>Building Details <i class="fas fa-sort-down downBtn"></i></h4>
                                    </div>
                                    <div id="collapseTwo" class="collapse">
                                      <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <label for="Subclass" class="mt-2">Building Type</label>
                                                <input type="text" class="form-control" id="BuildingType" name="BuildingType" placeholder="Building Type" value="{{@$PreConstruction->BuildingType}}">
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <label for="Subclass" class="mt-2">Building Status</label>
                                                <select class="form-control" name="BuildingStatus">
                                    <?php $build_status = array('Purposed','Under Construction','Ready to Move');?>
                                                    <option value="">Select</option>
                                        @foreach($build_status as $status)
                                                    <option value="{{$status}}" @if(@$PreConstruction->BuildingStatus==$status){{'selected'}}@endif>{{$status}}</option>
                                        @endforeach           
                                                </select>
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <label for="Subclass" class="mt-2">Sale Status</label>
                                                <select class="form-control" name="SaleStatus">
                                                    <option value="">Select</option>
                                            
                                        @foreach($build_status as $status)
                                                    <option value="{{$status}}" @if(@$PreConstruction->SaleStatus==$status){{'selected'}}@endif>{{$status}}</option>
                                        @endforeach    
                                                </select>
                                                
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label for="Subclass" class="mt-2">Size Range</label>
                                                <input type="number" class="form-control" id="SizeRange" name="SizeRange" placeholder="Size Range" value="{{@$PreConstruction->SizeRange}}">
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Price Range</label>
                                                <input type="number" class="form-control" id="PriceRange" name="PriceRange" placeholder="Price Range" value="{{@$PreConstruction->PriceRange}}">
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Storeys</label>
                                                <input type="text" class="form-control" id="Storeys" name="Storeys" placeholder="Storeys" value="{{@$PreConstruction->Storeys}}">
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Suites</label>
                                                <input type="text" class="form-control" id="Suites" name="Suites" placeholder="Suites" value="{{@$PreConstruction->Suites}}">
                                            </div>

                                            <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Bedroom</label>
                                                <input type="number" class="form-control" id="Bed" name="Bedroom" placeholder="Bed" value="{{@$PreConstruction->Bedroom}}">
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Bathroom</label>
                                                <input type="number" class="form-control" id="Bath" name="Bathroom" placeholder="Bath" value="{{@$PreConstruction->Bathroom}}">
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Occupancy/Possession</label>
                                                <input type="text" class="form-control" id="Possession" name="Possession" placeholder="Possession" value="{{@$PreConstruction->Possession}}">
                                            </div>

                                            <div class="col-md-12 col-lg-12">
                                                <label class="mt-2">Description<span class="required">*</span></label>
                                                <textarea class="textarea_editor form-control ck1" required name="Content" id="Content" rows="15" placeholder="Enter text ..." data-validation-engine="validate[required]"> {{@$PreConstruction->Content}}
                                                       </textarea>
                                            </div>

                                             <!-- <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Adrrr55</label>
                                                <select class="form-control" name="Adrrr55">
                                                        <option>Select</option>
                                                        <option value="">Delhi</option>
                                                </select>
                                            </div>

                                             <div class="col-md-6 col-lg-6">
                                                <label class="mt-2">Asasasas@</label>
                                                <input type="text" class="form-control" id="Asasasas" name="Asasasas" placeholder="Asasasas" value="{{@$PreConstruction->Asasasas}}">
                                            </div> -->
                                        </div>
                                        
                                      </div>
                                    </div>

                                    <div class="card-header mt-2" data-toggle="collapse" data-target="#collapseThree">
                                      <h4>Amenities <i class="fas fa-sort-down downBtn"></i></h4>
                                    </div>
                                    <div id="collapseThree" class="collapse ">
                                      <div class="card-body">
                                        <div class="row">
                                                                               
                                        @if($Amenities)
                                        @foreach($Amenities as $key => $val)
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-4"><label class="label">{{$val->Name}}</label></div>
                                                    <div class="col-md-1"><input type="checkbox"  name="Amenities[]" value="{{$val->id}}" class="eminitiesInput" @php if(@$enimitiesId) if(in_array($val->id,@$enimitiesId)) echo "checked"; @endphp></div>
                                                    <div class="col-md-7"> <input type="checkbox" name="AmenitiesMaintenance[]" value="{{$val->id}}" class="eminitiesInput mr-2" @php if(@$enimitiesMaintId) if(in_array($val->id,@$enimitiesMaintId)) echo "checked"; @endphp><small>Include Maintenance fee</small></div>
                                                </div>                                               
                                            </div>
                                        @endforeach
                                        @endif
                                            
                                        </div>
                                      </div>
                                    </div>

                                    <div class="card-header mt-2" data-toggle="collapse" data-target="#collapseFour">
                                      <h4>Media <i class="fas fa-sort-down downBtn"></i></h4>
                                    </div>
                                    <div id="collapseFour" class="collapse">
                                      <div class="card-body">
                                        
                                            <h4>Photo <span class="hint">(Max 40 Photos)</span></h4>
                                            <p class="hint">First photo will be displayed as a primary photo<br>You may arrange the order of the photos by clicking and dropping<br>Use CTRL to select multiple photos</p>
                                            <label class="form__container text-center" id="upload-container">Choose an image file or Drag it here <br>Max file size 2MB
                                            <input class="form__file" id="upload-files" type="file" name="MediaImage[]" accept="image/*" multiple="multiple"/>
                                            </label>

                                            
                                            <ul class="images" id="addimage">
                                                
                                            </ul>
                                            @if(@$MediaImage)
                                            <ul class="images" id="images">
                                            @foreach($MediaImage as $key=> $img)
                                            <li id="list{{$key}}" data-value="{{$img}}">
                                                <img src="{{$img}}" class="addedImage">
                                                <button type="button" onclick="removeImage('list{{$key}}')" class="btn remove_btn">Remove</button>
                                            </li>
                                            @endforeach
                                            </ul>
                                            
                                            @endif
                                            <label>Video <span class="hint">(Formats youtube link,mp4,vimeo)</span></label>
                                            <input type="text" name="VideoLink" class="form-control" value="{{@$PreConstruction->VideoLink}}">
                                      </div>
                                    </div>

                                    <div class="card-header mt-2" data-toggle="collapse" data-target="#collapseFive">
                                      <h4>Attechments <i class="fas fa-sort-down downBtn"></i></h4>
                                    </div>
                                    <div id="collapseFive" class="collapse">
                                      <div class="card-body">
                                         <label class="form__container text-center" id="upload-container1">Choose an image file or Drag it here <br>Max file size 2MB
                                            <input class="form__file" id="upload-files1" type="file" name="Attechments[]" accept="image/*" multiple="multiple" />
                                            </label>
                                           
                                             <ul class="images" id="addattech">
                                                
                                            </ul>
                                            @if(@$Attechments)
                                            <ul class="images" id="attech">
                                            @foreach($Attechments as $key=> $attech)
                                            <li id="Attech_list{{$key}}" data-value="{{$attech}}">
                                                <img src="{{$attech}}" class="addedImage">
                                                <button type="button" onclick="removeImage('Attech_list{{$key}}')" class="btn remove_btn">Remove</button>
                                            </li>
                                            @endforeach
                                            </ul>
                                            
                                            @endif
                                      </div>
                                    </div>
                                </div>
                            </div>
                                       
                                
                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate" id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save & Publish</button>
                                        <!-- <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate"id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save & Publish</button> -->
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
    
    <!-- <script>
        function get_builder()
        {
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/building/get_builder")}}',
                success: function (response) {
                    var arr=JSON.parse(response);
                    var option='<option value="">Select</option>';
                     for (var i = 0; i < arr.length; i++) {
                        var id = arr[i]['id'];
                        console.log(id);
                        option+= '<option value='+arr[i]['id']+'>'+arr[i]['BuilderName']+'</option>';
                     }
                     $('#All_Builder').html(option);
                },
            });
        }
        get_builder();
    </script> -->
  
<script>
    $(document).on('submit','#PreConstuctionForm',function(e){
            e.preventDefault();
            var form_data = new FormData($('#PreConstuctionForm')[0]);
            // var map = document.getElementById('Map').src;
             $('#AgentId').val('{{\Illuminate\Support\Facades\Auth::user()->id}}');
             form_data.append('AgentId','{{\Illuminate\Support\Facades\Auth::user()->id}}');
             // form_data.append('Map',map);
             var added_img = get_image_links();
             var attech = get_attech_links()
             form_data.append('addedImage',added_img);
             form_data.append('addedAttech',attech);
             $('#rule-btn2').removeClass('d-none');
             $('#SubmitBtn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/building/add_construction_data")}}',
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
    
    function removeImage(id)
    {
        document.getElementById(id).remove();
    }
    function get_image_links()
    {
        const images = [];
       $('#images li').each(function(i)
            {
              images.push($(this).attr('data-value'));
            });
       return JSON.stringify(images);
    }
    function get_attech_links()
    {
        const images = [];
       $('#attech li').each(function(i)
            {
              images.push($(this).attr('data-value'));
            });
       return JSON.stringify(images);
    }
</script>
<script>
const INPUT_FILE = document.querySelector('#upload-files');
const FILE_LIST = [];
    INPUT_FILE.addEventListener('change', () => {
      const files = [...INPUT_FILE.files];
      console.log("changed");
      files.forEach(file => {
        const fileURL = URL.createObjectURL(file);
        const fileName = file.name;
        if (!file.type.match("image/")) {
          alert(file.name + " is not an image");
          console.log(file.type);
        } else {
          const uploadedFiles = {
            name: fileName,
            url: fileURL };


          FILE_LIST.push(uploadedFiles);
          var img = "";
          $('#addimage').html('');
          for (var i = 0; i < FILE_LIST.length; i++) {
                var link = FILE_LIST[i]['url']; 
              img+= '<li id="addlist'+i+'"><img src="'+link+'" click="addedImage"><button type="button" onclick="removeImage(this.id)" id="addlist'+i+'" class="btn remove_btn">Remove</button></li>';
              
          }
          $('#addimage').append(img);
        }
      });
  });

const INPUT_FILE1 = document.querySelector('#upload-files1');
const FILE_LIST1 = [];
    INPUT_FILE1.addEventListener('change', () => {
      const files1 = [...INPUT_FILE1.files];
      console.log("changed");
      files1.forEach(file => {
        const fileURL = URL.createObjectURL(file);
        const fileName = file.name;
        if (!file.type.match("image/")) {
          alert(file.name + " is not an image");
          console.log(file.type);
        } else {
          const uploadedFiles = {
            name: fileName,
            url: fileURL };


          FILE_LIST1.push(uploadedFiles);
          var img = "";
          $('#addattech').html('');
          for (var i = 0; i < FILE_LIST1.length; i++) {
                var link = FILE_LIST1[i]['url']; 
              img+= '<li id="addattechlist'+i+'"><img src="'+link+'" click="addedImage"><button type="button" onclick="removeImage(this.id)" id="addattechlist'+i+'" class="btn remove_btn">Remove</button></li>';
              
          }
          $('#addattech').append(img);
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
            var map = new google.maps.Map(document.getElementById('map'), {
              center: {lat: -33.8688, lng: 151.2195},
              zoom: 13
            });
            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });
            address1Field = document.querySelector("#address_id");
            // address2Field = document.querySelector("#address2");
            postalField = document.querySelector("#post_code");
            autocomplete = new google.maps.places.Autocomplete(address1Field, {
                componentRestrictions: { country: ["us", "ca","in"] },
                fields: ["address_components", "geometry"],
                types: ["address"],
            });
            address1Field.focus();
            autocomplete.addListener("place_changed", function(){
                const place = autocomplete.getPlace();
                let address1 = "";
                let postcode = "";
                var lat = place.geometry.location.lat(),
                lng = place.geometry.location.lng();
           
            for (const component of place.address_components) {
                const componentType = component.types[0];
                switch (componentType) {
                    
                    case "postal_code": {
                        // postcode = `${component.long_name}${postcode}`;
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
                }
            }
            var address = '';
            if (place.address_components) {
                address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
            infowindow.close();
            marker.setVisible(false);
            
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
      
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
            });
            
        }
        
$('#address_id').keydown(function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});
    </script>
@endsection
