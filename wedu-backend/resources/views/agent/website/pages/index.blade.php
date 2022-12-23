@extends('.agent/layouts.app')
@section('title','Dashboard')
<!-- Begin page -->
@section('pageContent')
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-4">
                        <a href="{{url('super-admin/agent/add')}}" class="btn btn-success btn-md waves-effect waves-light mb-3" data-animation="fadein" data-plugin="custommodal"
                           data-overlaySpeed="200" data-overlayColor="#36404a"><i class="mdi mdi-plus-circle-outline"></i> Add Agent</a>
                    </div><!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">
                    @foreach(@$pages as $agent)
                        <div class="col-xl-4">
                            <div class="text-center card-box">
                                <div class="dropdown float-right">
                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item">Edit</a>
                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item">Delete</a>

                                    </div>
                                </div>
                                <div>
                                    <img src="{{ asset('assets') }}/superadmin/images/avatar.png" class="rounded-circle avatar-xl img-thumbnail mb-2" alt="profile-image">

                                    <p class="text-muted font-13 mb-3">
                                        Hi I'm Johnathn Deo,has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type.
                                    </p>

                                    <div class="text-left">
                                        <p class="text-muted font-13"><strong>Full Name :</strong> <span class="ml-2">{{$agent->name}}</span></p>

                                        <p class="text-muted font-13"><strong>Mobile :</strong><span class="ml-2">{{$agent->phone_number}}</span></p>

                                        <p class="text-muted font-13"><strong>Email :</strong> <span class="ml-2">{{$agent->email}}</span></p>

                                        <p class="text-muted font-13"><strong>Location :</strong> <span class="ml-2">USA</span></p>
                                    </div>

                                    <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light">Send Message</button>
                                </div>

                            </div>

                        </div>
                @endforeach
                <!-- end col -->
                </div>
                <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
        <!-- content -->

    </div>
    <!-- ============================================================== -->
    <!-- End Page content -->
    <!-- ============================================================== -->
@endsection

@section('pageLevelJS')

@endsection
