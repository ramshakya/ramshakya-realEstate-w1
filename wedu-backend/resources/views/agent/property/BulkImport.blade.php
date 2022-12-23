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
                                        Bulk Import
                                        </h4>
                                    </div>
                                    <div class="col-4 text-right">
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="pro-add-form" id="ImportForm" method="POST" action="" name="add_emailed" enctype="multipart/form-data">
                                        @csrf
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <label for="Subclass">Source<span class="required">*</span></label>
                                                    <select class="select2 form-control" id="Source" name="source" required>
                                                        <option value="" selected disabled>Source</option>
                                                        @if($folder_names)
                                                            @foreach($folder_names as $val)
                                                                <option value="{{@$val}}">{{@$val}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <input type="hidden" id="agent_id" name="" value="{{\Illuminate\Support\Facades\Auth::user()->id}}">
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="inline-group">
                                            <div class="row ">
                                                <div class="col-md-9">
                                                    <input type="file" class="form-control" id="import" name="file" required="" placeholder="Import">
                                                </div>
                                            </div>
                                        </div>
                                        <br>

                                        <button type="submit" value="submit" class="btn btn-outline-success waves-effect waves-light m-r-10" name="importbtn">Import</button>
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

    <script type="text/javascript">

        $(document).on('submit','#ImportForm',function(e){
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/property/bulk-import-file")}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log('response',response);
                    toastr.success(response.message,'Success');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                },
                error:function(status,error){
                    var errors = JSON.parse(status.responseText);
                    var msg_error = '';
                    console.log(errors);
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
@endsection
