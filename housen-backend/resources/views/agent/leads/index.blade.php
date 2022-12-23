@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')

<!-- third party css -->
        <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets') }}/agent/libs/multiselect/multi-select.css"  rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets') }}/agent/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Notification css (Toastr) -->
        <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

        <style type="text/css">
            .border-none{
                border-style: none;
                background-color: #f7f7f7 !important;
            }
            .btn-xs{
                background-color: #fff;
            }
            .card-title {
                margin-bottom: 0px;
            }
            .card-body{
               /* padding-top:0px !important;*/
            }

            button.btn.search_button_one {
                position: absolute;
                top: 2px;
                right: 17px;
                padding: 5px 16px;
                background-color: #5b69bc!important;
                border: none;
                min-height: 10px;
                min-width: 2px;
                /* border-radius: 20px; */
            }
            .cursor-pointer{
                cursor: pointer;
            }
            #spinLoader {
                position: fixed;
                top: 50%;
                left: 48%;
                background-color: #fff;
                z-index: 9999;
            }
            /* *** The Checkboxes *** */
            label.btn.toggle-checkbox > i.fa:before { content:"\f096"; }
                label.btn.toggle-checkbox.active > i.fa:before { content:"\f046"; }

            label.btn.active { box-shadow: none; }
            label.btn.primary.active {
                background-color: #337ab7;
                border-color: #2e6da4;
                color: #ffffff;
                box-shadow: none;
            }
            .btn-default{
                color: #6c757d ;
                border: 1px solid #6c757d !important;
            }
            #datatableses tr:after {
                font-family: 'FontAwesome';
                content: "\f068";
                float: right;
            }
            #datatableses tr.collapsed:after {
                /* symbol for "collapsed" panels */
                content: "\f067";
            }
            .bb-1{
                border-bottom: 1px solid #dee2e6;
            }
        </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title card-title-heading">
                                    Leads List 
                                </h4>

                            </div>
                            <div class="card-body table-responsive">
                                <div class="row">
                                    <div class="col-12 row bb-1 mb-2">
                                        <div class="col-10">
                                            <div class="form-group" data-toggle="buttons">
                                                    @if($status)
                                                    @foreach($status as $s)
                                                    <label class="btn btn-default btn-xs toggle-checkbox primary AgentType">
                                                        <input id="statusbtn" autocomplete="off" class=" d-none status" name="status" type="checkbox" value="{{@$s}}" />
                                                        {{@$s}}
                                                    </label>
                                                    @endforeach
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="col-2 text-right">
                                            <button class="btn btn-purple" id="clearAll">Clear all</button>
                                        </div>
                                        <hr/>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <input type="text" name="" placeholder="Search for lead name, email , phone" id="Search" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <button class="btn btn-purple" id="searchBtn">Search </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="spinner-border text-purple m-2" id="spinLoader" role="status" style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                <table id="datatableses" class="table table-bordered nowrap mt-2">

                                    <thead>
                                    <tr>
                                        <th >No #</th>
                                        <th >Contact Name</th>
                                        <th >Email</th>
                                        <th >Agent</th>
                                        <th >Phone</th>
                                        <th >Lead address</th>
                                        <th >Status</th>
                                        <th >Created at</th>
                                    </tr>
                                    </thead>
                                    <tbody id="PropertyDataList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container -->

        </div> <!-- content -->



    </div>
@endsection
@section('pageLevelJS')

        <script src="{{ asset('assets') }}/agent/libs/multiselect/jquery.multi-select.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/jquery-quicksearch/jquery.quicksearch.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/select2/select2.min.js"></script>
        <!-- Init js-->
        <script src="{{ asset('assets') }}/agent/js/pages/form-advanced.init.js"></script>
    <!-- knob plugin -->
    <script src="{{ asset('assets') }}/agent/libs/jquery-knob/jquery.knob.min.js"></script>

    <!--Morris Chart-->
    <!-- <script src="{{ asset('assets') }}/agent/libs/morris-js/morris.min.js"></script> -->
    <script src="{{ asset('assets') }}/agent/libs/raphael/raphael.min.js"></script>
    <!-- third party js -->
        <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.html5.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.flash.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/buttons.print.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.select.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/pdfmake/pdfmake.min.js"></script>
        <script src="{{ asset('assets') }}/agent/libs/pdfmake/vfs_fonts.js"></script>
        <!-- third party js ends -->

        <!-- Datatables init -->
        <script src="{{ asset('assets') }}/agent/js/pages/datatables.init.js"></script>
    <!-- Dashboard init js-->
   <!--  <script src="{{ asset('assets') }}/agent/js/pages/dashboard.init.js"></script> -->

<!-- Toastr js -->
        <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

        <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script type="text/javascript">
          getData();
         $(document).on('click','#searchBtn',function(){
            getData();
         });
        function getData(){
            $('#datatableses').DataTable().destroy();
            $('#spinLoader').show();
            var types = [];
            $.each($("input[name='status']:checked"), function(){
                types.push($(this).val());
            });
            var data = {
                "_token": "{{ csrf_token() }}"

            };
            var search =$('#Search').val();
            if(search!=''){
                data['search']=search;
            }
            if(types!=''){
                data['status']=types;
            }
            console.log(types);
            var url='{{url("/agent/leads/")}}';
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/leads/get-leads")}}',
                data: data,
                success: function (response) {
                    console.log(response);
                    setTimeout(function(){
                        $('#spinLoader').hide();
                    },500);
                    var i=1;
                    var html='';
                    $.each(response.leads, function(key, val) {
                        const date = new Date(val.created_at);
                        html+='<tr  class="pr-2 leadTr"';

                        html+='>'+
                            '<td class="wd-30">'+i;
                            if(val.additional_properties!=''){
                                html+=' <i class="fas fa-angle-down cursor-pointer" data-toggle="collapse" data-target="#collapseme'+val.id+'" class="officeTable" data-id="'+val.id+'"></i>';
                            }
                            html+='</td>'+
                            '<td class="wd-60"><a href="javascript:void(0)">'+val.AssignedAgentName+'</a></td>'+
                            '<td class="wd-60">'+val.AssgnAgentEmail+'</td>'+
                            '<td class="wd-60">'+val.AssignedAgentName+'</td>'+
                            '<td class="wd-60">'+val.AssgnAgentPhone+'</td>'+
                            '<td class="wd-60">'+val.Address+'</td>'+
                            '<td class="wd-60">'+val.Status+'</td>'+
                            '<td class="wd-60">'+date.toDateString()+'</td>'+
                        '</tr>';
                        if(val.additional_properties!=''){
                            html+='<tr id="collapseme'+val.id+'" class="collapse agentdatatr"><td colspan="8" class="p-0"><table id="collapseme_t'+val.id+'" class="table nowrap bordered">';
                            if(val.property.length!=0){
                                html+='<thead>'+
                                        '<tr>'+
                                            '<th class="th-wd-30"></th>'+
                                            '<th class="th-wd-30">MLS </th>'+
                                            '<th class="th-wd-30">Additional address </th>'+
                                            '<th class="th-wd-30" >Bedrooms </th>'+
                                            '<th class="th-wd-30">Bathrooms </th>'+
                                            '<th class="th-wd-30" colspan="4" >Price </th>'+
                                        '</tr></thead>'+
                                    '<tbody >';
                                $.each(val.property, function(key1, val1) {
                                     html+='<tr><td></td>'+
                                    '<td >#'+val1.ListOfficeMlsId+'</td>'+
                                    '<td >'+val1.UnparsedAddress+'</td>'+
                                    '<td >'+val1.BedroomsTotal+'</td>'+
                                    '<td >'+val1.BathroomsFull+'</td>'+
                                    '<td >'+val1.ListPrice+'</td>'+
                                    '</tr>';
                                });
                                    html+='</tbody>';

                            }else{
                                html+='<thead>'+
                                        '<tr>'+
                                            '<th class="th-wd-30"></th>'+
                                            '<th class="th-wd-30">Additional address </th>'+
                                            '<th class="th-wd-30" >Bedrooms </th>'+
                                            '<th class="th-wd-30">Bathrooms </th>'+
                                            '<th class="th-wd-30" colspan="4" >Price </th>'+
                                        '</tr></thead>'+
                                    '<tbody ><tr><td></td><td >'+val.additional_properties+'</td>'+
                                    '<td >';
                                    if(val.Beds){
                                        html+=val.Beds;
                                    }else{
                                    }
                                    html+='</td>'+
                                    '<td >'
                                    if(val.Baths){
                                        html+=val.Baths;
                                    }else{
                                    }
                                    html+='</td>'+
                                    '<td >';
                                    if(val.price!='N/A'){
                                        html+=val.price;
                                    }else{
                                    }
                                    html+='</td>'+
                                    '</tr></tbody>';
                            }
                                    html+='</table></td><td class="d-none"></td><td class="d-none"></td><td class="d-none"></td><td class="d-none"></td><td class="d-none"></td><td class="d-none"></td><td class="d-none"></td></tr>';
                        }
                        i++;
                    });
                        $('#PropertyDataList').html(html);

                    getTable();
                },
                error:function(status,error){
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
        }
        $(document).on('change','.status',function(){
            $('.allAgentType').toggle();
            getData();

        });
        function getTable(){
            $('#datatableses').dataTable( {
                "bLengthChange": false,
                "bFilter": false,
                "searching": false,
                "bSortable": false,
                "ordering": false,
                'orderable': false,
                "pageLength": 16,
            } );
        }
        $(document).on('click','#clearAll',function(){
            $(".status").prop('checked', false);
            $(".status").removeAttr('checked');
            $('.AgentType').removeClass('active');
            getData();
        });
        $(document).on('click','.filterBtn',function(){
            $('.Filterrow').toggle("slow");
        });
        $(document).on('click','#searchbtn',function(){
            getData();
        });
        $(document).on('click','#filtereset',function(){
            location.reload();
        });
        $(document).on('click','.leadTr',function(){
            $(this).find('td i').toggleClass('fas fa-angle-down fas fa-angle-up');
        });
    </script>
@endsection
