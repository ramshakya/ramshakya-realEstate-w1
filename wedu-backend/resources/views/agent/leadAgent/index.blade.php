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
                                    Agent List
                                </h4>

                            </div>
                            <div class="card-body table-responsive">
                                <div class="col-12">
                                    <div class="form-group" data-toggle="buttons">
                                            <label class="btn btn-default btn-lg toggle-checkbox primary">
                                                <input id="one" autocomplete="off" class="d-none allAgentType" type="checkbox" name="checkbox[]" value="0" checked  />
                                                All Agents ( {{$total}} )
                                            </label>
                                            @if($agents)
                                            @foreach($agents as $agent)
                                            <label class="btn btn-default btn-lg toggle-checkbox primary AgentType">
                                                <input id="one" autocomplete="off" class=" d-none" name="agenttype" type="checkbox" />
                                                {{@$agent->AgentType}} ({{@$agent->total}})
                                            </label>
                                            @endforeach
                                            @endif
                                    </div>
                                </div>
                                <table id="datatableses" class="table table-bordered nowrap mt-2">
                                    <div class="spinner-border text-purple m-2" id="spinLoader" role="status" style="display: none;">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <thead>
                                    <tr>
                                        <th >No #</th>
                                        <th >Name</th>
                                        <th >Email</th>
                                        <th >Phone</th>
                                        <th >Phone</th>
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
    <script type="text/javascript">
        getData();
        function getData(){
            $('#datatableses').DataTable().destroy();
            $('#spinLoader').show();
            
            var types = [];
            $.each($("input[name='agenttype']:checked"), function(){
                types.push($(this).val());
            });
            var data = {
                'type': types,
                "_token": "{{ csrf_token() }}"

            };
             var search='';
            // var cities =$('#cities').val();
            if(search!=''){
                data['search']=search;
            }
            var url='{{url("/agent/details/")}}';
            // if(cities!=''){
            //     $data['cities']=cities;
            // }

            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/get-lead-agent")}}',
                data: data,
                success: function (response) { 
                    console.log(response);
                    setTimeout(function(){
                        $('#spinLoader').hide();
                    },500);
                    var i=1;
                    var html='';
                    $.each(response.property, function(key, val) {
                        html+='<tr  class="pr-2">'+
                            '<td class="wd-30">'+i+'</td>'+
                            '<td class="wd-60"><a href="'+url+'/'+val.id+'">'+val.ListAgentFullName+'</a></td>'+
                            '<td class="wd-60">'+val.ListAgentDirectPhone+'</td>'+
                            '<td class="wd-60">'+val.ListAgentEmail+'</td>'+
                            '<td class="wd-60">'+val.ListAgentEmail+'</td>'+
                        '</tr>';
                        i++;
                    });
                        $('#PropertyDataList').html(html);
                        
        getTable();
                        
                    // alert(1);
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
        $(document).on('click','.AgentType',function(){
            $('.allAgentType').toggle();
            getData();

        });
        function getTable(){
            $('#datatableses').dataTable( {
                "bLengthChange": false,
                "bFilter": true,
                "searching": false,
                "bSortable": false, 
            } );
        }
        $(document).on('click','.filterBtn',function(){
            $('.Filterrow').toggle("slow");
        });
        $(document).on('click','#searchbtn',function(){
            getData();
        });
        $(document).on('click','#filtereset',function(){
            location.reload();
        });
    </script>
@endsection
