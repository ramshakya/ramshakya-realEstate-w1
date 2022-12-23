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
        .bannerImg{
            margin-top: -52px;
            width: 114px;
            height: 55px;
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
                                                            $type="";    
                                                            $section="";   
                                                            
                                                            ?>
                                                        @foreach($arr[0] as $key=>$val) 
                                                            <?php $sno++; $section = $val->value; $type = $val->type;?> 
                                                            @if($section=="bannerSection")
                                                                <li class="moveAbleCard" data-value="bannerSection" data-type="{{$type}}">
                                                                    <div class="right-input mt-1 ml-2">  
                                                                        <label class="mr-1">Hide</label>
                                                                        <label class="mr-1"><input type="checkbox" class="form-check-input" data-plugin="switchery" name="topBannerSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" @if(@$setting->topBannerSection=="show"){{'checked'}}@endif /></label>
                                                                        <label>Show</label>
                                                                    </div>
                                                                    <label class="control-label label1">Section {{$sno}}. Banner</label> <center> <img src="{{@$setting->TopBanner}}" class="rounded mb-2 bannerImg" height="30"> </center></center>
                                                                    <input type="file" class="form-control" id="TopBanner" name="TopBanner"/>
                                                                                                
                                                                    <input type="hidden" name="OlderImage" value="{{@$setting->TopBanner}}">
                                                                </li>
                                                                @elseif($section=="communitySection")
                                                                    <li class="moveAbleCard" data-value="communitySection" data-type="{{$type}}">
                                                                        <div class="right-input mt-1 ml-2"> 
                                                                        <label class="mr-1">Hide</label>                          
                                                                            <label class="mr-1"><input type="checkbox" class="form-check-input" data-plugin="switchery" name ="communityBannerSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" @if(@$setting->communityBannerSection=="show"){{'checked'}}@endif /></label>
                                                                            <label>Show</label>
                                                                        </div>
                                                                        <label class="control-label label1">Section {{$sno}}. {{$type}}</label> <center><img src="{{@$setting->CommunityBanner}}" class="rounded bannerImg mb-2" height="30"></center>
                                                                        <input type="file" class="form-control" id="CommunityBanner" name="CommunityBanner"/>
                                                                        <input type="hidden" name="OlderCommunityImage" value="{{@$setting->CommunityBanner}}">
                                                                    </li>
                                                                @elseif($section=="htmlContentSection")
                                                                <li class="moveAbleCard" data-value="htmlContentSection" data-type="{{$type}}">
                                                                    <h1></h1>
                                                                            <h4 class="control-label">Section {{$sno}}. Html content</h4>
                                                                        <div class="right-input">                           
                                                                            <label class="mr-1">Hide</label> 
                                                                            <label class="mr-1"><input type="checkbox" class="form-check-input" data-plugin="switchery" name ="contentSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" @if(@$setting->contentSection=="show"){{'checked'}}@endif /></label>
                                                                            <label>Show</label> 
                                                                        </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <textarea class="textarea_editor form-control ck1" required name="htmlContent" id="content1" rows="4" placeholder="Enter text ..."> {{@$setting->htmlContent}}</textarea>
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
                                                    <li class="moveAbleCard" data-value="bannerSection" data-type="Banner">
                                                        <div class="right-input mt-1 ml-2 form-switch">    
                                                            <label class="mr-1">Hide</label>
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" data-switchery="true" name="topBannerSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>                       
                                                        </div>
                                                        <label class="control-label label1">Section 1. Banner</label>  
                                                        <input type="file" class="form-control" id="TopBanner" name="TopBanner"/>
                                                        <input type="hidden" name="OlderImage" value="{{@$setting->TopBanner}}">
                                                    </li>

                                                    <li class="moveAbleCard" data-value="blogSection" data-type="Blogs">
                                                        <h4 class="control-label">Section 2. Blogs</h4>
                                                        <div class="right-input"> 
                                                            <label class="mr-1">Hide</label>
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" value="checked" name="blogSection" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label >Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="citySection" data-type="Search in city">
                                                        <h4 class="control-label">Section 3. Search in city</h4>
                                                        <div class="right-input">  
                                                            <label class="mr-1">Hide</label>                       
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="citySection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="featuredSection" data-type="Featured MLS listings">
                                                        <h4 class="control-label">Section 4. Featured MLS listings</h4>
                                                        <div class="right-input">  
                                                            <label class="mr-1">Hide</label>                             
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="featuredSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="recentSection" data-type="Recent MLS listings">
                                                        <h4 class="control-label">Section 5. Recent MLS listings</h4>
                                                        <div class="right-input">                            
                                                            <label class="mr-1">Hide</label>
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="recentSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="communitySection" data-type="Community image">
                                                        <div class="right-input mt-1 ml-2"> 
                                                            <label class="mr-1">Hide</label>                          
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="communityBannerSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                        <label class="control-label label1">Section 6. Community image</label> 
                                                        <input type="file" class="form-control" id="CommunityBanner" name="CommunityBanner"/>
                                                                                    
                                                        <input type="hidden" name="OlderCommunityImage" value="{{@$setting->CommunityBanner}}">
                                                    </li>
                                                    <li class="moveAbleCard" data-value="testimonialSection" data-type="Testimonial">
                                                        <h4 class="control-label">Section 7. Testimonial</h4>
                                                        <div class="right-input"> 
                                                            <label class="mr-1">Hide</label>                             
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="testimonialSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>

                                                    <li class="moveAbleCard" data-value="contectFormSection" data-type="Contact form">
                                                        <h4 class="control-label">Section 8. Contact form</h4>
                                                        <div class="right-input"> 
                                                            <label class="mr-1">Hide</label>                             
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="contectFormSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                    </li>
                                                    <li class="moveAbleCard" data-value="htmlContentSection" data-type="Html content">
                                                        <h4 class="control-label">Section 9. Html content</h4>
                                                        <div class="right-input">  
                                                            <label class="mr-1">Hide</label>                         
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="contentSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label>
                                                        </div>
                                                        <div class="m-3">     
                                                            <textarea class="textarea_editor form-control ck1" required name="htmlContent" id="content1" rows="4" placeholder="Enter text ..."> {{@$setting->htmlContent}}</textarea>
                                                        </div>
                                                    </li>
                                                    <li class="moveAbleCard" data-value="profileSection" data-type="About">
                                                        <h4 class="control-label">Section 10. About</h4>
                                                        <div class="right-input">   
                                                            <label class="mr-1">Hide</label>                        
                                                            <label class="mr-1"><input type="checkbox" data-plugin="switchery" name="profileSection" value="checked" data-color="#87c93a"  data-secondary-color="#ED5565" /></label>
                                                            <label>Show</label> 
                                                        </div>
                                                    </li>

                                                </ul>
                                                @endif
                                                
                                            </div> 
                                        </div>  
                                        <div id="serialize_output"></div>
                                        <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addSection">Add new section</button> -->
                                        <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10 ml-4" name="addnewemailtemplate"id="SubmitBtn">
                                            <div class="spinner-border d-none" role="status" id="rule-btn2">
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
    <!-- add section model  -->
  <!-- Modal -->
<div class="modal fade" id="addSection" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add section</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="" method="POST">
            <label>Select Section</label>
            <select name="" id="sectionName" class="form-control">
                <option value="">Select Section</option>
                <option value="bannerSection">Banner</option>
                <option value="blogSection">Blogs</option>
                <option value="citySection">City</option>
                <option value="featuredSection">Featured Listing</option>
                <option value="recentSection">Recent Listing</option>
                <option value="communitySection">Community Banner</option>
                <option value="htmlContentSection">Html Content</option>
                <option value="testimonialSection">Testimonial</option>
                <option value="contectFormSectio">Contact Form</option>
            </select>
            <br>
            <button class="btn btn-success float-right" onclick="createElementFunc()" type="button">Add</button>
        </form>
      </div>
    
    </div>
  </div>
</div>
  <!-- end -->
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
                url: '{{url("api/v1/agent/pages/update-code")}}',
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
    <script src="{{ asset('assets') }}/agent/js/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/switchery/switchery.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/jquery-mask-plugin/jquery.mask.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

        <!-- Init js-->
        <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>

        <!-- App js -->
        <script src="{{ asset('assets') }}/agent/js/app.min.js"></script>
    <script>
        setTimeout(function(){
            var x = document.getElementById("content1").value;
            CKEDITOR.replace('content1');
            CKEDITOR.add;
            CKEDITOR.instances.add_content.setData(x);
        },1000);
    </script>
<script type="text/javascript">
    function createElementFunc(){
        var sectionName = document.getElementById('sectionName').value;
//         var array= {"bannerSection": "Banner", "blogSection": "Blogs", "citySection":"Search in city","featuredSection":"featured Mls listings","recentSection":"Recent Mls listings","communitySection":"Community image",""}; 





// htmlContentSection
// testimonialSection
// contectFormSectio
        if(sectionName!=""){
            var ul = document.getElementById('menuitems').getElementsByTagName('li');
            for (var i = 0; i < ul.length; i++) {
                var a = $(ul[i]).attr('data-value');
                if(a==sectionName){
                    alert("This section already added");
                }
                else
                {
                    var section = "";
                
                    if(sectionName=="bannerSection"){
                        section+='<li class="moveAbleCard" data-value="bannerSection" data-type="">';
                        section+='<label class="control-label label1">Section . Banner</label>  <img src="" class="rounded float-right mb-2" height="30">'; 
                        section+='<input type="file" class="form-control" id="TopBanner" name="TopBanner"/>';
                        section+='<input type="hidden" name="OlderImage" value=""></li>';
                    }
                }
            }
             
        }
        
    }
</script>
@endsection
