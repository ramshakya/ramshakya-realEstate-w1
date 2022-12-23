@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
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
        .menulist select{
            padding: 8px;
            width: 40%;
            margin-left: 10x;
            display: inline-block;
        }
        .menulist label{
            margin-right: 20px;
        }
        .menulist button{
            margin-top: -2px;
        }
        .menu_form{
            background-color: #F0F0F0;
            padding: 10px;
            margin-bottom: 10px;
        }
        .addMEnu{
            width: 70%;
            display: inline-block;
        }
        
        .menu_form{
            background-color: #F0F0F0;
            padding: 10px;
            margin-bottom: 10px;
        }
        .menu-item-bar{
            background-color: #F7F7F7;
            margin:5px 0;
            border: 1px solid #d7d7d7;
            padding: 10px;
            cursor: pointer;
            display: block;

        }
        #menuitems li{
            list-style: none;
            background-color: white;
        }
        .menu-item-bar i{
            float: right;
        }
        .dragged{position: absolute;
            z-index: 1;
        }
        #serialize_output{
            display: none;
        }

        body.dragging {cursor: move;}

        #show_new_menu_box{
            display: none;
        }
        .displayinline{
            display: inline-block;
            width: 50%;
        }
        #addMenu{
            margin-top: -2px;
        }
        #custom_form{
            display: none;
        }
        .fa-trash-alt{
            margin-left: 10px;
        }
        .instruction_msg{
            color: #6c757d;
            font-size: .9rem;
        }
        .moveAbleCard{
            /*background-color: gray;*/
            box-shadow: 0px 0px 8px #d4d2d2;
            border-radius: 5px;
            /*height: 100px;*/
            padding: 10px;
            width: 100%;
            margin-bottom: 20px;
            padding: 30px;
            /*height: 120px;*/
        }
        #menuitems label{
            margin-right: 30px;
        }
        .right-input{
            float: right;
            margin-top: -30px;
        }
        .label1{
            font-size: 16px;
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
                                            <?php if(isset($page)){ echo 'Edit'; }else{ echo 'Add'; }?> code for {{@$page->PageName}}
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{url('agent/pages/')}}" class="btn btn-purple">All Pages</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="ImportForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        <?php $setting = json_decode($page->Setting);?>
                                        <input type="hidden" name="id" id="page_id" value="{{@$page->id}}">
                                        <div class="row ">
                                            <div class="col-md-12">
                                                @if(@$setting->ArrangeSection)
                                                    <ul id="menuitems">
                                                        <?php $arr = json_decode(@$setting->ArrangeSection);
                                                            $sno=0;           
                                                            ?>
                                                        @foreach($arr[0] as $key=>$val) 
                                                            <?php $sno++; $section = $val->value; $type = $val->type;?>
                                                            @if($section=="listingsSection")
                                                            <li class="moveAbleCard" data-value="listingsSection" data-type="Similar listings">
                                                                    <h4 class="control-label">Section {{$sno}}. {{$type}}</h4>
                                                                <div class="right-input">  
                                                                    <label class="mr-1">Hide</label>
                                                                    <label class="mr-1"><input type="checkbox" class="form-check-input" data-plugin="switchery" name ="listingsSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" @if(@$setting->listingsSection=="show"){{'checked'}}@endif /></label>
                                                                    <label>Show</label>
                                                                </div>
                                                                    </br>
                                                                    <h4 class="left-inpt"> Criterias: </h4>
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <h4> Price: </h4>
                                                                    </div>
                                                                    <div class="col-md-4 text-center">
                                                                        <label class="m-0"><i class="fa fa-minus btn btn-outline-danger" onclick="decreasevalue()" aria-hidden="true"></i></label>
                                                                        <label class="w-25 mr-0"><input type="number" class="form-control" name="priceSection" id="priceSection" min="0" value="{{@$setting->priceSection}}"> </label>
                                                                        <label><i class="fa fa-plus btn btn-outline-success" onclick="increasevalue()" aria-hidden="true"></i></label>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                            <h4> Area:  </h4>
                                                                    </div>
                                                                    <div class="col-md-4 text-center">
                                                                        <label  class="w-75">
                                                                        <select class="form-select form-control form-select-sm" name="areaSection" aria-label=".form-select-sm example">
                                                                            <option >--select--</option>
                                                                            <option value="1" @if(@$setting->areaSection=="1"){{'selected'}}@endif>Same</option>
                                                                            <option value="2" @if(@$setting->areaSection=="2"){{'selected'}}@endif>No Dependency</option>
                                                                        </select>  
                                                                        </label> 
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                            <h4> City:  </h4>
                                                                    </div>
                                                                    <div class="col-md-4 text-center">
                                                                        <label  class="w-75">
                                                                            <select class="form-select form-control form-select-sm" name="citySection" aria-label=".form-select-sm example">
                                                                                <option >--select--</option>
                                                                                <option value="1" @if(@$setting->citySection=="1"){{'selected'}}@endif>Same</option>
                                                                                <option value="2" @if(@$setting->citySection=="2"){{'selected'}}@endif>No Dependency</option>
                                                                            </select>  
                                                                        </label> 
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            @elseif($section=="pagevisitsSection")
                                                            <li class="moveAbleCard" data-value="pagevisitsSection" data-type="Page Visits Without Login">
                                                                    <h4 class="control-label">Section {{$sno}}. {{$type}} </h4>
                                                                <div class="row">
                                                                    <div class="col-md-8"></div>
                                                                    <div class="col-md-4 text-center">  
                                                                        <label class="m-0"><i class="fa fa-minus btn btn-outline-danger" onclick="decreaseValue()" aria-hidden="true"></i></label>
                                                                        <label class="w-25 mr-0"><input type="number" class="form-control" id="pagevisitsSection" name="pagevisitsSection" min="0" max="10" value="{{@$setting->pagevisitsSection}}">  </label>
                                                                        <label><i class="fa fa-plus btn btn-outline-success" onclick="increaseValue()" aria-hidden="true"></i></label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            @else
                                                                <?php ?>
                                                                <li class="moveAbleCard" data-value="{{$section}}" data-type="{{$type}}">
                                                                    <h4 class="control-label">Section {{$sno}}. {{$type}}</h4>
                                                                    <div class="right-input">  
                                                                        <label class="mr-1">Hide</label>                         
                                                                        <label class="mr-1"><input type="checkbox" class="form-check-input" data-plugin="switchery" name ="{{$section}}" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" @if(@$setting->$section=="show"){{'checked'}}@endif /></label>
                                                                        <label>Show</label>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>

                                                @else
                                                <ul id="menuitems">

                                                    <li class="moveAbleCard" data-value="descriptionSection" data-type="General Description">
                                                        <h4 class="control-label">Section 1. General Description</h4>
                                                        <div class="right-input">                           
                                                            <label class="mr-1">Hide</label>  
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" data-switchery="true" name="descriptionSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>  
                                                        </div>
                                                    </li>
                                                    
                                                    <li class="moveAbleCard" data-value="extrasSection" data-type="Extras">
                                                        <h4 class="control-label">Section 2. Extras</h4>
                                                        <div class="right-input"> 
                                                            <label class="mr-1">Hide</label>                          
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" data-switchery="true" name="extrasSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="propertySection" data-type="Property Details">
                                                        <h4 class="control-label">Section 3. Property Details  </h4>
                                                        <div class="right-input">                         
                                                            <label class="mr-1">Hide</label>
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" data-switchery="true" name="propertySection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>
                                    
                                                    <li class="moveAbleCard" data-value="listingsSection" data-type="Similar listings">
                                                        <h4 class="control-label">Section 4. Similar listings</h4>
                                                        <div class="right-input"> 
                                                            <label class="mr-1">Hide</label> 
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" data-switchery="true" name="listingsSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                        </br>
                                                        <h4 class="left-input"> Criterias: </h4>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                            <h4> Price: </h4>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <label class="m-0"><i class="fa fa-minus btn btn-outline-danger" onclick="decreasevalue()" aria-hidden="true"></i></label>
                                                                <label class="w-25 mr-0"><input type="number" class="form-control" name="priceSection" id="priceSection" placeholder="$2,50,000" min="0"> {{@$setting->priceSection}}</label>
                                                                <label class="m-0 text-left border"><i class="fa fa-plus btn btn-outline-success" onclick="increasevalue()" aria-hidden="true"></i></label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                            <h4> Area:  </h4>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <label  class="w-50">
                                                                <select class="form-select form-control form-select-sm" name="areaSection" aria-label=".form-select-sm example">
                                                                    <option >--select--</option>
                                                                    <option value="1" @if(@$setting->areaSection=="1"){{'selected'}}@endif>Same</option>
                                                                    <option value="2" @if(@$setting->areaSection=="2"){{'selected'}}@endif>No Dependency</option>
                                                                </select>  
                                                                </label> 
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h4> City:  </h4>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <label  class="w-50">
                                                                    <select class="form-select form-control form-select-sm" name="citySection" aria-label=".form-select-sm example">
                                                                        <option >--select--</option>
                                                                        <option value="1" @if(@$setting->citySection=="1"){{'selected'}}@endif>Same</option>
                                                                        <option value="2" @if(@$setting->citySection=="2"){{'selected'}}@endif>No Dependency</option>
                                                                    </select>  
                                                                </label> 
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="pagevisitsSection" data-type="Page Visits Without Login">
                                                        <h4 class="control-label">Section 5. Page Visits Without Login </h4>
                                                        <div class="row">
                                                            <div class="col-md-6"></div>
                                                            <div class="col-md-6 text-center">  
                                                                <label class="m-0"><i class="fa fa-minus btn btn-outline-danger" onclick="decreaseValue()" aria-hidden="true"></i></label>
                                                                <label class="w-25 mr-0"><input type="number" class="form-control" name="pagevisitsSection" id="pagevisitsSection" min="0" max="10"> {{@$setting->pagevisitsSection}} </label>
                                                                <label class="m-0"><i class="fa fa-plus btn btn-outline-success" onclick="increaseValue()" aria-hidden="true"></i></label>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                @endif
                                            </div> 
                                        </div>  
                                        <div id="serialize_output"></div>                                                            
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">

                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 ml-4" name="addnewemailtemplate"id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                            <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Submit
                                        </button>
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
    <script type="text/javascript">
        $(document).on('submit','#ImportForm',function(e){
            e.preventDefault();

            $('#rule-btn2').removeClass('d-none');
            $('#SubmitBtn').attr('disabled', true);
            var formData = new FormData(this);
            var data = group.sortable("serialize").get();
            var jsonString = JSON.stringify(data,null,'');
            var newContent = jsonString;
            formData.append("ArrangeSection", newContent);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/pages/update-code-property")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },3000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    console.log(errors);
                    $('#rule-btn2').addClass('d-none');
                    $('#SubmitBtn').attr('disabled', false);
                    if(status.status == 422){
                        $.each(errors.errors, function(i,v){
                            console.log(v);
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js" integrity="sha512-9pm50HHbDIEyz2RV/g2tn1ZbBdiTlgV7FwcQhIhvykX6qbQitydd6rF19iLmOqmJVUYq90VL2HiIUHjUMQA5fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var group = $('#menuitems').sortable({
            group: 'serialization',
            onDrag:function($item, container,_super){
                
                var data = group.sortable("serialize").get();
                var jsonString = JSON.stringify(data,null,'');
                $('#serialize_output').text(jsonString);
                _super($item,container);
            }
        });
</script>

<!-- Plugins Js --> 
<script src="{{ asset('assets') }}/agent/libs/switchery/switchery.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/moment/moment.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="{{ asset('assets') }}/agent/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

<!-- Init js-->
<script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>

    <script>
        // Javascript For +/- Values Of Price And Page Visits
        function increaseValue() {
            var value = parseInt(document.getElementById('pagevisitsSection').value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById('pagevisitsSection').value = value;
            }

            function decreaseValue() {
            var value = parseInt(document.getElementById('pagevisitsSection').value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? value = 1 : '';
            value--;
            document.getElementById('pagevisitsSection').value = value;
        }
        function increasevalue() {
            var value = parseInt(document.getElementById('priceSection').value, 10);
            value = isNaN(value) ? 0 : value;
            value+=1000;
            document.getElementById('priceSection').value = value;
            }

            function decreasevalue() {
            var value = parseInt(document.getElementById('priceSection').value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? value = 1 : '';
            value-=1000;
            document.getElementById('priceSection').value = value;
        }
    </script>
@endsection
