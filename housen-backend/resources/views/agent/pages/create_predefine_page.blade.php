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
        .form-check-label{
            font-weight: normal;
            color: #6c757d;
        }
        .overflow_content{
            /*height: 300px;*/
            overflow: hidden;
            height: 300px;
            overflow-y: scroll;
        }

        ::-webkit-scrollbar {
          width: 5px;
        }
        /* Track */
        ::-webkit-scrollbar-track {
          background: #f1f1f1; 
        }
         
        /* Handle */
        ::-webkit-scrollbar-thumb {
          background: #888; 
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
          background: #555; 
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
                                            <?php if(isset($page)){ echo 'Edit'; }else{ echo 'Create'; }?> Predefine Page
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/pages/predefine-pages')}}" class="btn btn-purple">All Predefine Pages</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="templateForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <input type="hidden" name="" id="page_id" value="{{@$page->id}}">
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-12"><h4>Basic Details:</h4></div>
                                                <div class="col-md-6">
                                                    <label for="Subclass">Page Name<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="page_name" name="name" required="" placeholder="Page name" value="{{@$page->PageName}}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Subclass">Url<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="Url" name="Url" required="" placeholder="Url" value="{{@$page->PageUrl}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                       
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-12"><h4>Seo Config:</h4></div>
                                                <div class="col-md-4">
                                                    <label for="Subclass">Meta Title<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaTitle" name="metatitle" required="" placeholder="Meta Title" value="{{@$page->MetaTitle}}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="Subclass">Meta Keyword (use comma for more keywords)<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaKeyword" name="MetaKeyword" required="" placeholder="Meta Keyword" value="{{@$page->MetaTags}}">
                                                </div>
                                                 <div class="col-md-4">
                                                    <label for="Subclass">Meta Description<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="MetaDescription" name="MetaDescription" required="" placeholder="Meta Description" value="{{@$page->MetaDescription}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-12"><h4>Search Criteria:</h4></div>
                                                <div class="col-md-4">
                                                    <label for="Subclass">Mls Status<span class="required">*</span></label><br>
                                                    <div class="form-check-inline">
                                                        <input type="hidden"  id="Mls_status" value="{{@$page->Mls_status}}">
                                                      <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" value="active" onclick="Mls_check_status(this.value)" name="Mls_status" @if(@$page->MlsStatus=='active'){{'checked'}}@endif>Active
                                                      </label>
                                                    </div>
                                                    <div class="form-check-inline">
                                                      <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" value="inactive"  onclick="Mls_check_status(this.value)" name="Mls_status" @if(@$page->MlsStatus=='inactive'){{'checked'}}@endif>Inactive
                                                      </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="zip_code">Zip Code<span class="required">*</span></label>
                                                    <input type="number" class="form-control" id="zip_code" name="zip_code" required="" placeholder="Zip Code" value="{{@$page->ZipCode}}">
                                                </div>
                                                 <div class="col-md-4">
                                                    <label for="sqft">Sqft. Range<span class="required">*</span></label>
                                                    <select class="form-control"  data-placeholder="Choose ..." id="sqft_range">
                                                        <option value="">Sqft.</option>
                                                        
                                                            <option value="100" @if(@$page->SqftRange==100){{'selected'}}@endif>100</option>
                                                            <option value="500" @if(@$page->SqftRange==500){{'selected'}}@endif>500</option>
                                                            <option value="1000" @if(@$page->SqftRange==1000){{'selected'}}@endif>1000</option>
                                                            <option value="1500" @if(@$page->SqftRange==1500){{'selected'}}@endif>1500</option>
                                                            <option value="2000" @if(@$page->SqftRange==2000){{'selected'}}@endif>2000</option>
                                                            <option value="3000" @if(@$page->SqftRange==3000){{'selected'}}@endif>3000</option>
                                                            <option value="4000" @if(@$page->SqftRange==4000){{'selected'}}@endif>4000</option>
                                                            <option value="5000" @if(@$page->SqftRange==5000){{'selected'}}@endif>5000</option>
                                                            <option value="6000" @if(@$page->SqftRange==6000){{'selected'}}@endif>6000</option>
                                                            <option value="7000" @if(@$page->SqftRange==7000){{'selected'}}@endif>7000</option>
                                                            <option value="8000" @if(@$page->SqftRange==8000){{'selected'}}@endif>8000</option>
                                                            <option value="9000" @if(@$page->SqftRange==9000){{'selected'}}@endif>9000</option>
                                                            <option value="10000" @if(@$page->SqftRange==10000){{'selected'}}@endif>10000</option>
                                            
                                                    </select>
                                                   
                                                </div>
                                                 
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                    <label for="city">City<span class="required">*</span></label>
                                                    <div class="row overflow_content">

                                                @foreach($cities as $city)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                              <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input city" value="{{$city->City}}" @php if(@$city_added) if(in_array($city->City,@$city_added)) echo "checked"; @endphp>{{$city->City}}
                                                              </label>
                                                            </div>
                                                        </div>
                                                @endforeach
                                                        
                                                    </div>
                                                   
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="property_type">Property Type<span class="required">*</span></label>
                                                    <div class="row">
                                                @foreach($PropertyType as $property_type)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                              <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input property_type" value="{{$property_type->PropertyType}}" @php if(@$property_type_added) if(in_array($property_type->PropertyType,@$property_type_added)) echo "checked"; @endphp>{{$property_type->PropertyType}}
                                                              </label>
                                                            </div>
                                                        </div>
                                                @endforeach   
                                                    </div>
                                                   
                                                </div>
                                                 <div class="col-md-4">
                                                    <label for="area">Area<span class="required">*</span></label>
                                                    <div class="row">
                                                @foreach($BuildingAreaSource as $area)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                              <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input area" value="{{$area->BuildingAreaSource}}" @php if(@$area_added) if(in_array($area->BuildingAreaSource,@$area_added)) echo "checked"; @endphp>{{$area->BuildingAreaSource}}
                                                              </label>
                                                            </div>
                                                        </div>
                                                @endforeach
                                                    </div>
                                                   
                                                </div>
                                        </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label for="city">Price Range<span class="required">*</span></label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <select class="form-control"  data-placeholder="Choose ..." id="min_price">
                                                                <option value="">Min Price</option>
                                                        @if($price)
                                                            @foreach($price as $m_price)
                                                                <option value="{{@$m_price}}" @if(@$page->MinPrice==$m_price){{'selected'}}@endif>{{@$m_price}}</option>
                                                            @endforeach
                                                        @endif

                                                            </select>
                                                        </div>
                                                         <div class="col-md-6">
                                                            <select class="form-control"  data-placeholder="Choose ..." id="max_price">
                                                                <option value="">Max Price</option>
                                                            @if($price)
                                                                @foreach($price as $m_price)
                                                                    <option value="{{@$m_price}}" @if(@$page->MaxPrice==$m_price){{'selected'}}@endif>{{@$m_price}}</option>
                                                                @endforeach
                                                            @endif
    

                                                            </select>
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="bedrooms">Bedrooms<span class="required">*</span></label>
                                                     <select class="form-control"  data-placeholder="Choose ..." id="bedrooms">
                                                        <option value="">Beds</option>
                                                        @for($bed=1;$bed<6;$bed++)
                                                            <option value="{{$bed}}" @if(@$page->Bedrooms==$bed){{'selected'}}@endif>{{$bed}}+</option>
                                                        @endfor                      
                                                    </select>
                                                   
                                                </div>
                                                 <div class="col-md-3">
                                                    <label for="Bathrooms">Bathrooms<span class="required">*</span></label>
                                                    <select class="form-control"  data-placeholder="Choose ..." id="bathrooms">
                                                        <option value="">Baths</option>
                                                        
                                                        @for($bath=1;$bath<6;$bath++)
                                                            <option value="{{$bath}}" @if(@$page->Bathrooms==$bath){{'selected'}}@endif>{{$bath}}+</option>
                                                        @endfor
                                            
                                                    </select>
                                                   
                                                </div>
                                                
                                        </div>
                                        </div>
                                        <br>
                                        
                        
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                        
                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="addnewemailtemplate"id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
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
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("content1").value;
            CKEDITOR.replace('content1');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>
   
    <script type="text/javascript">
       
        $(document).on('submit','#templateForm',function(e){
            e.preventDefault();
            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var page_name = $('#page_name').val();
            var MetaTitle=$('#MetaTitle').val();
            var MetaKeyword=$('#MetaKeyword').val();
            var MetaDesc=$('#MetaDescription').val();
            var Url=$('#Url').val();
            // 
            var Mls_status = $('#Mls_status').val();
            var zip_code = $('#zip_code').val();
            var sqft_range = $('#sqft_range').val();
            var min_price = $('#min_price').val();
            var max_price = $('#max_price').val();
            var bedrooms = $('#bedrooms').val();
            var bathrooms = $('#bathrooms').val();
            var city=[];
            $(".city:checked").each(function(){
                city.push($(this).val());
            });
            var cities = JSON.stringify(city);

            var property_type=[];
            $(".property_type:checked").each(function(){
                property_type.push($(this).val());
            });
            var propertyType = JSON.stringify(property_type);

            var area=[];
            $(".area:checked").each(function(){
                area.push($(this).val());
            });
            var building_area = JSON.stringify(area);
            
            // 
            var id=$('#page_id').val();
            var agentId=$('#agent_id').val();
            if(id==''){
                id=0;
            }
            var data = {
                'id':id,
                'PageName': page_name,
                'MetaTitle':MetaTitle,
                'MetaTags':MetaKeyword,
                'MetaDescription':MetaDesc,
                'PageUrl':Url,
                'AgentId':agentId,
                'MlsStatus':Mls_status,
                'ZipCode':zip_code,
                'SqftRange':sqft_range,
                'MinPrice':min_price,
                'MaxPrice':max_price,
                'Bedrooms':bedrooms,
                'Bathrooms':bathrooms,
                'City':cities,
                'PropertyType':propertyType,
                'Area':building_area,
                'Status':'Active',
                "_token": "{{ csrf_token() }}"
            };
           
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/pages/add-predefine-page")}}',
                data: data,
                success: function (response) {
                    console.log('data',data);
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
        $("#page_name").keyup(function(){
            var Text = $(this).val();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');
            console.log(Text);
            $("#Url").val('/home/'+Text);
        });
        
        function Mls_check_status(str)
        {
            $('#Mls_status').val(str);
        }
    </script>
@endsection
