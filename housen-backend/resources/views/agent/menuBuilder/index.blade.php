@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
   <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
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
                                        <!-- Button to Open the Modal -->
                                        <h4 class="card-title card-title-heading font-family-class">
                                            Menu Builder
                                        </h4>

                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="/agent/pages" class="btn btn-purple">Go to Pages</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                     <input type="hidden" name="AgentId" id="AgentId" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                     
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                        <form action="{{url('/agent/menu/menuBuilder')}}" method="get" class="menulist">
                                            <label>Select a Menu</label>
                                            <select class="form-control menulist" name="id" id="menu_id" onchange="get_already_added_menus(this.value)">
                                                <option value="">Menu Name</option>
                                                @foreach($menu_list as $menu_data)

                                                <option value="{{$menu_data->id}}" >{{$menu_data->MenuName}}</option>
                                                @endforeach

                                            </select>
                                             
                                            <span> Or </span>
                                            <a class="" href="#" onclick="show_create_box()">Create a New Menu</a>
                                            
                                        </form>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="" id="show_new_menu_box">
                                                <form action="" method="" id="add_menu" onsubmit="event.preventDefault(); add_new_menu();">
                                                <label>Menu Name</label>
                                                <input type="text" id="menu_name" name="menu_name" class="form-control displayinline" placeholder="Enter Menu Name " required="">
                                                <button type="submit" name="submitMenu" class="btn btn-primary" id="addMenu">Save Menu</button>
                                            </form>
                                            </div>
                                        </div>
                                        
                                    </div><hr>
                                    <div class="row">
                                        <div class="col-md-5 col-lg-5">
                                            <div class="form-check pl-0">
                                                <label class="form-check-label">
                                                <span class="pr-3">Custom Page</span>
                                                <input type="checkbox" class="form-check-input" name="custom_page" id="custom_page" onclick="open_custom_box(this.id)"> 
                                                </label>
                                                 <p class="instruction_msg">If you want an external link to be in menu check custom box</p>

                                            </div>
                                            <br>
                                               <form id="custom_form" action="" onsubmit="event.preventDefault(); add_to_menu_by_custom();" method="post">
                                                    <label for="Subclass">Page Label<span class="required">*</span></label>
                                                    <input type="text" class="form-control" id="custom_menu_name" name="custom_menu_name" required="" placeholder="Page Label" value="">
                                                    <br>
                                                    <label for="Subclass">Slug / Url<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="custom_slug" name="custom_slug" required="" placeholder="Url" value=""><br>
                                                    <button class="btn btn-primary" type="submit">Add To menu</button>
                                                </form>

                                                <form id="page_form" action="" onsubmit="event.preventDefault(); add_to_menu_by_page();" method="post">
                                                    <label for="Subclass">Select Page<span class="required">*</span></label>
                                                    
                                                      <select class="form-control" id="page_url" name="page_url" required="" onchange="get_add_name(this.value)">
                                                        <option value="">Select Page</option>
                                                @foreach($page_list as $value)
                                                        <option value="{{$value->PageUrl}}" class="">{{$value->PageName}}</option>

                                                @endforeach
                                                      </select>
                                                      <br>
                                                    <label for="Subclass">Page Label<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="page_menu_name" name="page_menu_name" required="" placeholder="Page Label" value="" required=""><br>
                                                    <label for="Subclass">Page Url<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="page_url_slug" name="page_url_slug" required="" placeholder="Page Url" value="" required=""><br>
                                                    <button class="btn btn-primary" type="submit">Add To menu</button>
                                                </form>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Menu Structure</h4>
                                            <form id="add_menu_form" action="" onsubmit="event.preventDefault(); add_menu_form();" method="post">
                                                <div id="added_menu">
                                                    <ul id="menuitems">

                                                    </ul>
                                                </div>
                                                 <div id="serialize_output"></div>
                                            <button type="submit" value="submit" class="btn btn-outline-primary waves-effect waves-light m-r-10 float-right" name="addnewemailtemplate"id="SubmitBtn"><div class="spinner-border d-none" role="status" id="rule-btn2">
                                                <span class="sr-only">Loading...</span>
                                            </div> &nbsp;<i aria-hidden="true" class="far fa-check-circle"></i> Save to Menu</button>     
                                            </form>

                                            <div id="d1emo" class="collapse">
                                                <h4>Edit Menu</h4>
                                                 <label for="Subclass">Page Label<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="changeName" name="" required="" placeholder="Page label" value="" required=""><br>
                                                <label for="Subclass">Url / slug<span class="required"></span></label>
                                                    <input type="text" class="form-control" id="changeUrl" name="" required="" placeholder="Menu url" value="" required=""><br>
                                                    <input type="hidden" id="place_id" value="">
                                                <button class="btn btn-primary" onclick="save_changes()">Save Changes</button>
                                            </div>
                                        </div>
                                       
                                        
                                    </div>
                                    
                                </div>
                            </div>


                        </div>

                    </div>

                </div>
            </section>
        </div> <!-- container -->

    </div> <!-- content -->
    </div>
    </div>

<!-- model to add new page -->
<!-- The Modal /////////////////-->
<div class="modal" id="addpage">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Page</h4>
        <button type="button" class="close" data-dismiss="modal" id="close_modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form class="modal-body" method="post" action="" id="addPageForm">
        @csrf
        <div class="form-group">
            <label for="page_name">Page Name</label>
            <input class="form-control form-control-sm" name="page_name" id="page_name" type="text" placeholder="" required="">
        </div>
        <div class="form-group">
            <label for="page_url">Page Url</label>
            <input class="form-control form-control-sm" name="page_url" id="page_url" type="text" placeholder="" required="">
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input class="form-control form-control-sm" name="title" id="title" type="text" placeholder="" required="">
        </div>
        <div class="form-group">
            <label for="meta_tags">Meta tags (use , for more tags)</label>
            <input class="form-control form-control-sm" name="meta_tags" id="meta_tags" type="text" placeholder="">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <input class="form-control form-control-sm" name="meta_description" id="meta_description" type="text" placeholder="">
        </div>
        <div class="form-group">
            
           <!--  <input class="form-control form-control-sm" name="status" id="status" type="hidden" placeholder="" value="Active"> -->
            <input type="hidden" name="AgentId" id="AgentId" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
        </div>
        <button type="submit" class="btn btn-primary" id="add_page">Add Page</button>
      </form>

    </div>
  </div>
</div>

<!-- ///////////////////end -->


@endsection
@section('pageLevelJS')
<script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>
<script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>

<!-- this script is use for drag and drop and arrange list -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js" integrity="sha512-9pm50HHbDIEyz2RV/g2tn1ZbBdiTlgV7FwcQhIhvykX6qbQitydd6rF19iLmOqmJVUYq90VL2HiIUHjUMQA5fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // creating new page
    $(document).on('submit','#addPageForm',function(e){
            e.preventDefault();
      // var formdata = new FormData($(this).closest('form')[0]);
      // console.log(formdata);
      var page_name=$('#page_name').val();
      var page_url=$('#page_url').val();
      var title=$('#title').val();
      var meta_tags=$('#meta_tags').val();
      var meta_description=$('#meta_description').val();
     
      var AgentId=$('#AgentId').val();
      var data = {
            'page_name':page_name,
            'page_url':page_url,
            'title':title,
            'meta_tags':meta_tags,
            'meta_description':meta_description,
            'status':'Active',
            'AgentId':AgentId,
            "_token": "{{ csrf_token() }}"
      }
      $('#add_page').attr('disabled', true);
      $.ajax({
              url:'{{url("api/v1/agent/menu/add-page")}}',
              type:"POST",
              data:data,
              success: function (response) {
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        // window.location.href="{{url('agent/menu/menuBuilder')}}";
                        location.reload();
                    },1000);
                },
                error:function(status,error){
                    $('#add_page').attr('disabled', false);
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
      $('#close_modal').click();
  });
    //show create box for adding new menubar
    function show_create_box()
    {
        $("#show_new_menu_box").show();
    }
    //adding new menubar for creating menues inside
    function add_new_menu()
    {
        var menu_name = $('#menu_name').val();
        var AgentId = $('#AgentId').val();
        var _token = "{{ csrf_token() }}";
        var data = {
            'MenuName':menu_name,
            'AgentId':AgentId
      }
        $('#addMenu').attr('disabled', true);
        $.ajax({
            url:'{{url("api/v1/agent/menu/add-menu")}}',
            type:'POST',
            data:data,
            success:function(response){
                toastr.success(response.message,'Success');
                setTimeout(function(){
                    location.reload();
                },1000);
            }
           
        });

    }
    //costum box for add custom pages and links
    function open_custom_box(id)
    {
        if(document.getElementById(id).checked==true)
        {
            $("#page_form").hide();
            $("#custom_form").show();
        }
        else
        {
            $("#custom_form").hide();
            $("#page_form").show();
        }
    }
    //add new items to the menubar which you selected through already created pages
    var page=1;
    function add_to_menu_by_page()
    {
        
        var paglinkId = "page"+page;
        var menu_url = $("#page_url_slug").val();
        var menu_name = $("#page_menu_name").val();
        var type = "page";
        var AgentId = $('#AgentId').val();
        $("#menuitems").append('<li id="'+paglinkId+'" data-url = "'+menu_url+'" data-value="'+menu_name+'" data-id="'+AgentId+'" data-type="custom"><span class="menu-item-bar"><b class="'+paglinkId+'">'+menu_name+'</b><a href="#" id="'+paglinkId+'" onclick="delete_menu(this.id)"><i class="fas fa-trash-alt"></i></a> <a href="#" id="'+paglinkId+'" data-value="'+menu_name+'" data-url="'+menu_url+'" data-type="custom" onclick="get_edited_value(this.id)" data-toggle="collapse" data-target="#d1emo"><i class="fa fa-edit"></i></a></span><ul></ul></li>');
       
       page+=1;

    }

    //add new items to the menubar which you creating through custom pages

    var custom=1;
    function add_to_menu_by_custom()
    {
        var linkId = "custom"+custom;
        var menu_url = $("#custom_slug").val();
        var menu_name = $("#custom_menu_name").val();
        var type = "custom";
        var AgentId = $('#AgentId').val();
       $("#menuitems").append('<li id="'+linkId+'" data-url = "'+menu_url+'" data-value="'+menu_name+'" data-id="'+AgentId+'" data-type="custom"><span class="menu-item-bar"><b class="'+linkId+'">'+menu_name+'</b><a href="#" id="'+linkId+'" onclick="delete_menu(this.id)"><i class="fas fa-trash-alt"></i></a><a href="#" id="'+linkId+'" data-value="'+menu_name+'" data-url="'+menu_url+'" data-type="custom" onclick="get_edited_value(this.id)" data-toggle="collapse" data-target="#d1emo"><i class="fa fa-edit"></i></a></span><ul></ul></li>');
       
       custom+=1;
    }

    //
    function add_menu_form()
    {
        var menuId = $("#menu_id").val();
        var data = group.sortable("serialize").get();
        var jsonString = JSON.stringify(data,null,'');
        var newContent = jsonString;
        if(menuId!="")
        {
            if(newContent!='')
            {
                $('#rule-btn2').removeClass('d-none');
                var data = {
                    'MenuContent':newContent,
                    'id':menuId
                 }
                $.ajax({
                    url:'{{url("api/v1/agent/menu/add-menu-data")}}',
                    type:'POST',
                    data:data,
                    success:function(response){
                        console.log(response);
                        toastr.success(response.message,'Success');
                        setTimeout(function(){
                        },1000);
                        $('#rule-btn2').addClass('d-none');
                    }
                   
                });
            }
            else{
                alert("Please add some page or custom page");
            }
        }
        else
        {
            alert("please select menu first");
            $("#menu_id").focus();
        }
        
        
    }

    function get_add_name(str)
    {
        $("#page_url_slug").val(str);
    }
    function get_edited_value(str)
    {
        var menu_name = $("#"+str).attr('data-value');
        var menu_url = $("#"+str).attr('data-url');
        var menu_type = $("#"+str).attr('data-type');
        $("#changeName").val(menu_name);
        $("#changeUrl").val(menu_url);
        $("#place_id").val(str);

    }

    function save_changes()
    {
       var menu_name =$("#changeName").val();
       var menu_url =$("#changeUrl").val();
       var place_id =$("#place_id").val();
       $("#"+place_id).attr('data-value',menu_name);
       $("#"+place_id).attr('data-url',menu_url);
       $("#"+place_id+" ."+place_id).html(menu_name);
        var menu_name =$("#changeName").val("");
       var menu_url =$("#changeUrl").val("");
       var place_id =$("#place_id").val("");
       $("#d1emo").removeClass('show');
    }

    function delete_menu(str)
    {
        $("#"+str).remove();
    }
    function get_already_added_menus(menu_id)
    {
        if(menu_id!="")
        {
            var data = {'id':menu_id}
                
                $.ajax({
                    url:'{{url("api/v1/agent/menu/get-menu")}}',
                    type:'POST',
                    data:data,
                    success:function(response){
                        // console.log(response);
                        $("#menuitems").html(response);
                       
                    }
                   
                });
          $('#changeName').val('');
          $('#changeUrl').val('');
          $('#place_id').val('');
          $('#d1emo').removeClass('show');
        }
    }
</script>
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