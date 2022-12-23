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
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Top Banner Image</label> 
                                                <input type="file" class="form-control" id="TopBanner" name="TopBanner"/>
                                                <img src="{{@$setting->TopBanner}}" height="50">
                                                <input type="hidden" name="OlderImage" value="{{@$setting->TopBanner}}">
                                            </div>
                                            <div class="col-md-6 form-group"></div>
                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Blog section</h4>
                                               
                                                <label><input type="radio" value="show" name="blogShow" @if(@$setting->blogShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="blogShow" @if(@$setting->blogShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Search in city section</h4>
                                               
                                                <label><input type="radio" value="show" name="cityShow" @if(@$setting->cityShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="cityShow" @if(@$setting->cityShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Featured MLS listings section</h4>
                                               
                                                <label><input type="radio" value="show" name="featuredShow" @if(@$setting->featuredShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="featuredShow" @if(@$setting->featuredShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Recent MLS listings section</h4>
                                               
                                                <label><input type="radio" value="show" name="recentShow" @if(@$setting->recentShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="recentShow" @if(@$setting->recentShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label class="control-label">Community Image</label> 
                                                <input type="file" class="form-control" id="CommunityBanner" name="CommunityBanner"/>
                                                <img src="{{@$setting->CommunityBanner}}" height="50">
                                                <input type="hidden" name="OlderCommunityImage" value="{{@$setting->CommunityBanner}}">
                                            </div>
                                            <div class="col-md-6 form-group"></div>
                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Testimonial section</h4>
                                               
                                                <label><input type="radio" value="show" name="testimonialShow" @if(@$setting->testimonialShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="testimonialShow" @if(@$setting->testimonialShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <h4 class="control-label">Contact form section</h4>
                                               
                                                <label><input type="radio" value="show" name="contactFormShow" @if(@$setting->contactFormShow=="show"){{'checked'}}@endif > Show</label><br/>
                                                 <label><input type="radio" value="hide" name="contactFormShow" @if(@$setting->contactFormShow=="hide"){{'checked'}}@endif > Hide</label>
                                                
                                            </div>
                                        <div class="col-md-12">
                                        <h4>Arrange sections</h4>
                                        <ul id="menuitems">
                                        @if(@$setting->ArrangeSection)
                                            <?php $arr = json_decode(@$setting->ArrangeSection);
                                                
                                            ?>
                                            @foreach($arr[0] as $key=>$val)
                                                <li data-value="{{$val->value}}" data-type="{{$val->type}}"><span class="menu-item-bar">{{$val->type}}</span></li>
                                            @endforeach
                                        @else
                                            <li data-value="BlogSection" data-type="Blog Section"><span class="menu-item-bar">Blog Section</span></li>
                                            <li data-value="CitySection" data-type="Search in city section"><span class="menu-item-bar">Search in city section</span></li>
                                            <li data-value="featuredSection" data-type="Featured MLS listings section"><span class="menu-item-bar">Featured MLS listings section</span></li>
                                            <li data-value="recentSection" data-type="Recent MLS listings section"><span class="menu-item-bar">Recent MLS listings section</span></li>
                                            <li data-value="communitySection" data-type="Community section"><span class="menu-item-bar">Community section</span></li>
                                            <li data-value="testimonialSection" data-type="Testimonial section"><span class="menu-item-bar">Testimonial section</span></li>
                                            <li data-value="contactFormSection" data-type="Contact Form section"><span class="menu-item-bar">Contact Form section</span></li>
                                        @endif
                                            
                                            
                                        </ul>
                                        
                                       
                                        <div id="serialize_output">
                                            
                                        </div>
                                            </div>
                                        </div>
                                        
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
@endsection
