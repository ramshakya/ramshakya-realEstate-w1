@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- Notification css (Toastr) -->
    <link href="{{ asset('assets') }}/agent/libs/toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets') }}/agent/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
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
                                            All Campaigns
                                            </h4>
                                        </div>
                                        <div class="col-4 text-right">
                                                <a href="{{url('agent/campaign/create-lead-campaign')}}" class="btn btn-purple">Add Campaign</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0" id="datatableses">
                                                <thead>
                                                    <tr>
                                                        <th colspan="">Sr. No</th>
                                                        <th colspan="" >Name</th>
                                                        <th colspan="" >Subject</th>
                                                        <th colspan="" >Leads</th>
                                                        <th colspan="" >Start date</th>
                                                        <th colspan="" >Start time</th>
                                                        <th colspan="" >finish time</th>
                                                        <th colspan="" >limit</th>
                                                        <th colspan="" >Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i=1;$x=1;?>
                                                    @if($campaign)
                                                    @foreach($campaign as $k)
                                                    <tr>
                                                        <td style="width:50px;">{{$x}}</td>
                                                        <td class="hidden-xs">{{$k->campaign_name}}</td>
                                                        <td class="max-texts border ">{{$k->subject}}</td>
                                                        <td class="max-texts border "><?php
                                                        $i=1;
                                                           foreach ($k->agents as $key) {
                                                            if($i<6){
                                                               echo $key->ListAgentFullName.' - '.$key->ListAgentEmail.' , ';
                                                            }
                                                            if($i==6){
                                                                echo '... <a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal'.$k->id.'">View more </a>';
                                                            }
                                                               $i++;
                                                           }?></td>
                                                        <td>{{$k->start_date}}</td>
                                                        <td>{{$k->start_time}}</td>
                                                        <td>{{$k->finish_time}}</td>
                                                        <td>{{$k->limit}} / day</td>
                                                        <td>
                                                            <a style="width:50px;" href="{{url('agent/campaign/create-lead-campaign/'.$k->id)}}" clas"text-purple">
                                                                <i class="fa fa-edit text-purple"></i>
                                                            </a>
                                                            <a href="javascript:void(0)"  onclick="deleteType({{@$k->id}})" clas"text-danger">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </a>
                                                      </td>
                                                    </tr>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="exampleModal{{@$k->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                      <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Send to </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                              <span aria-hidden="true">&times;</span>
                                                            </button>
                                                          </div>
                                                          <div class="modal-body">
                                                            <p class="font-20">
                                                            <?php foreach ($k->agents as $key) {
                                                               echo $key->ListAgentFullName.' - '.$key->ListAgentEmail.' , ';
                                                            }
                                                            $x++;
                                                            ?>
                                                        </p>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
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
    <div class="modal fade" id="confirm_model" tabindex="-1" role="dialog" aria-labelledby="confirm_model"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"> Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <p style="text-align:center;" class=""> Are You Want To Delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success confirm_btn">Ok</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('pageLevelJS')
    <!-- Toastr js -->
    <script src="{{ asset('assets') }}/agent/libs/toastr/toastr.min.js"></script>

    <script src="{{ asset('assets') }}/agent/js/pages/toastr.init.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.bootstrap4.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/datatables/dataTables.buttons.min.js"></script>
    <script>
        $('#datatableses').dataTable( {
            "bLengthChange": false,
            "bFilter": false,
            "searching": false,
            "bSortable": false,
            "ordering": false,
            'orderable': false,
            "pageLength": 10,
        } );
    </script>
    <script type="text/javascript">
        var deleteId = 0;
        $(document).ready(function() {
            $('.confirm_btn').click(function() {

                var data = {
                    'id': deleteId,
                    "_token": "{{ csrf_token() }}"
                };
                console.log(data);
                $.ajax({
                    type: "POST",
                    url:'{{url("api/v1/agent/campaign/delete-leadCamp")}}',
                    data: data,
                    success: function(response) {
                        console.log(data);
                        $('#confirm_model').modal('toggle');
                        if(response.success){
                            toastr.success(response.message,'Success');
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }
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

            });
        });
        function deleteType(id) {
            deleteId = id;
            $('#confirm_model').modal('toggle');
        }

    </script>
@endsection
