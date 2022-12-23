<?php $notification = LeadNotification(); $total = $notification['count'];$leadsNotify = $notification['notification'];?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
{{--    <title>{{@$pageTitle}}</title>--}}
    <title>Peregrine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build SearchReality, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/agent/images/favicon.ico">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets') }}/agent/css/bootstrap.min.css" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets') }}/agent/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets') }}/agent/css/app.min.css" id="app-stylesheet" rel="stylesheet" type="text/css" />
    @yield('pageLevelStyle')
    <style>
        #blur {
            font-size: 40px;
            color: #00000024;
            text-shadow: 0 0 3px #000;
        }
        .clearall{
            cursor: pointer;
        }
    </style>
</head>

<body class="enlarged" data-keep-enlarged="true">
<!-- Pre-loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">Loading...</div>
    </div>
</div>
<!-- End Preloader-->
<!-- Begin page -->
<div id="wrapper">
    <!-- Topbar Start -->
    <div class="navbar-custom">
        <ul class="list-unstyled topnav-menu float-right mb-0">


            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle  waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="fe-bell noti-icon"></i>
                    <span class="badge badge-danger rounded-circle noti-icon-badge" id="Totalnotifications"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">
                                    <span class="float-right">
                                       <a  class="text-dark clearall" onclick="ClearAllNotification()">
                                            <small>Clear All</small>
                                        </a>
                                    </span>Notification
                        </h5>
                    </div>

                    <div class="slimscroll h-50 noti-scroll">
                        <!-- item-->
                            <div class="inbox-widget">
                                    <div id="notificationNav">

                                    </div>
                            </div>
                        <!-- item-->
                    <!-- All-->
                    <a href="/agent/Notifications" class="dropdown-item text-center text-primary notify-item notify-all">
                        View all
                        <i class="fi-arrow-right"></i>
                    </a>

                </div>
            </li>

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets') }}/agent/images/avatar.png" alt="user-image" class="rounded-circle">
                    <span class="pro-user-name ml-1">
                                {{\Illuminate\Support\Facades\Auth::user()->name}} <i class="mdi mdi-chevron-down"></i>
                            </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    <!-- item-->
                    <a href="{{url('agent/myaccount')}}" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="{{url('agent/setting/change-password')}}" class="dropdown-item notify-item">
                        <i class="fe-settings mr-1"></i>
                        <span>Change Password</span>
                    </a>

                    <!-- item-->
                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock"></i>
                        <span>Lock Screen</span>
                    </a> -->

                    <div class="dropdown-divider"></div>

                    <!-- item-->
                    <a href="{{ url('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"  class="dropdown-item notify-item profile_logout_link">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>

{{--            <li class="dropdown notification-list">--}}
{{--                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect">--}}
{{--                    <i class="fe-settings noti-icon"></i>--}}
{{--                </a>--}}
{{--            </li>--}}


        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{url('agent/')}}" class="logo logo-dark text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets') }}/agent/images/logo-dark.png" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets') }}/agent/images/logo-sm.png" alt="" height="24">
                        </span>
            </a>
            <a href="{{url('agent/')}}" class="logo logo-light text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets') }}/agent/images/logo-light.png" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets') }}/agent/images/logo-sm.png" alt="" height="24">
                        </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
            <li>
                <button class="button-menu-mobile disable-btn waves-effect">
                    <i class="fe-menu"></i>
                </button>
            </li>

            <li>
                <h4 class="page-title-main"></h4>
            </li>

        </ul>

    </div>
    <!-- end Topbar -->



    <!-- ========== Left Sidebar Start ========== -->
    <div class="left-side-menu">

        <div class="slimscroll-menu">
            <!-- User box -->
            <div class="user-box text-center">
                <img src="{{ asset('assets') }}/agent/images/users/user-1.jpg" alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail avatar-md">
                <div class="dropdown">
                    <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown"  aria-expanded="false">{{\Illuminate\Support\Facades\Auth::user()->name}}</a>
                    <div class="dropdown-menu user-pro-dropdown">
                        <!-- item-->
                        <a href="{{url('agent/myaccount')}}" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>My Account</span>
                        </a>

                        <!-- item-->
                        <a href="{{url('agent/setting/change-password')}}" class="dropdown-item notify-item">
                            <i class="fe-settings mr-1"></i>
                            <span>Change Password</span>
                        </a>

                        <!-- item-->
                        <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-lock mr-1"></i>
                            <span>Lock Screen</span>
                        </a> -->
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-log-out mr-1"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
                <p class="text-muted">Admin Head</p>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="#" class="text-muted">
                            <i class="mdi mdi-cog"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#">
                            <i class="mdi mdi-power"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!--- Sidemenu -->
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">

                    <li class="menu-title">Navigation</li>

                    <li>
                        <a href="{{url('agent/')}}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{url('agent/lead')}}">
                            <i class="fas fa-users-cog"></i>
                            <span> Leads </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                             <li><a href="{{url('agent/staff')}}">Staff</a></li>
                        </ul>
                    </li>
                    <!-- <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-user"></i>
                            <span> Teams </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('agent/staff')}}">Staff</a></li>

                            <li><a href="{{url('agent/list')}}">Agents</a></li>
                        <- <li><a href="{{url('agent/lead')}}">All leads</a></li> -->
                            <!--- <li><a href="#">Condos</a></li>
                             <li><a href="#">Custom Properties</a></li> -->
                        <!--</ul>
                    </li>-->
                    <li>
                        <a href="javascript: void(0);">
                            {{-- <i class="far fa-graph"></i> --}}
                            <i class="fa fa-calendar"></i>
                            <span> Events </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('agent/events/')}}">Events</a></li>
                            <li><a href="{{url('agent/events/calendar')}}">Calendar</a></li>
                        </ul>
                    </li>
<!--                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-user"></i>
                            <span> Agents </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('agent/list')}}">All Agents</a></li>
                            &lt;!&ndash; <li><a href="{{url('agent/lead')}}">All leads</a></li> &ndash;&gt;
                           &lt;!&ndash;- <li><a href="#">Condos</a></li>
                            <li><a href="#">Custom Properties</a></li> &ndash;&gt;
                        </ul>
                    </li>-->
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fab fa-accusoft"></i>
                            <span> Marketing </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
{{--                            <li><a href="{{url('agent/campaign/')}}">Email Campaigns</a></li>--}}

                            <li><a href="{{url('agent/campaign/Leadcampaign')}}">Lead's Campaign</a></li>
                            <li><a href="{{url('agent/template')}}">Email Template</a></li>
                            <li><a href="{{url('agent/email_logs')}}">Email Logs</a></li>
<!--                            <li><a href="#">Segmentation</a></li>
                            <li><a href="#">Reporting</a></li>-->
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-globe" ></i>
                            <span> Blogs </span>
                            <span class="menu-arrow"></span>
                        </a>
                       <ul class="nav-second-level" aria-expanded="false">
                           <li><a href="{{url('agent/blog/')}}">Blogs</a></li>
                           <li><a href="{{url('agent/blog/categories')}}">Blog Category</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            {{-- <i class="far fa-graph"></i> --}}
                            <i class="fa fa-chart-line"></i>
                            <span> Stats and Analysis</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('agent/stats/city')}}">Cities</a></li>
                            <li><a href="{{url('agent/stats/property_viewed')}}">Properties</a></li>
                            <li><a href="{{url('agent/stats/user_stats')}}">Lead Tracker</a></li>
                            <li><a href="{{url('agent/stats/property_viewed')}}">Google Analytics</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-globe" ></i>
                            <span> Manage Website </span>
                            <span class="menu-arrow"></span>
                        </a>
                       <ul class="nav-second-level" aria-expanded="false">
{{--                            <li><a href="#">Menu Builder</a></li>--}}
{{--                            <li><a href="#">Pages</a></li>--}}
{{--                            <li><a href="#">Team Members</a></li>--}}
{{--                            <li><a href="#">Landing Pages</a></li>--}}
{{--                            <li><a href="#">Blogs</a></li>--}}
{{--                            <li><a href="#">Widgets</a></li>--}}
                                <li><a href="{{url('agent/menu/menuBuilder')}}">Menu Builder</a></li>
                                <li><a href="{{url('agent/pages')}}">Page Builder</a></li>
                                <li><a href="{{url('agent/pages/predefine-pages')}}">Predefine Page</a></li>
                                <li><a href="{{url('agent/city/')}}">Cities</a></li>
                                <li><a href="{{url('agent/city/')}}">Areas</a></li>
                                <li><a href="{{url('agent/testimonial/')}}">Testimonials</a></li>
                                <li><a href="{{url('agent/setting/')}}">Main Settings</a></li>
                                <li><a href="{{url('agent/sitemap/')}}">SiteMap XML</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="far fa-building"></i>
                            <span> Properties </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('agent/property')}}">Property List</a></li>
                            <li><a href="{{url('agent/building/')}}">New construction</a></li>
                            <li><a href="{{url('agent/property/import')}}">Import Properties</a></li>
                        </ul>
                    </li>



                   <li class="" hidden>
                        <a href="{{url('agent/track_users')}}">
                            <i class="fas fa-users"></i>
                            <span> Users Trackers </span>
                            {{-- <span class="menu-arrow"></span> --}}
                        </a>

                    </li>
                </ul>

            </div>
            <!-- End Sidebar -->

            <div class="clearfix"></div>

        </div>
        <!-- Sidebar -left -->

    </div>
    <!-- Left Sidebar End -->


@yield('pageContent')
<!-- Footer Start -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    2021 &copy; Peregrine <a href="">#</a>
                </div>
                <div class="col-md-6">
                    <div class="text-md-right footer-links d-none d-sm-block">
                        <a href="javascript:void(0);">About Us</a>
                        <a href="javascript:void(0);">Help</a>
                        <a href="javascript:void(0);">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
    <!-- Vendor js -->
    <script src="{{ asset('assets') }}/agent/js/vendor.min.js"></script>

@yield('pageLevelJS')
    <!-- App js -->
    <script src="{{ asset('assets') }}/agent/js/app.min.js"></script>
    <script src="{{ asset('assets') }}/agent/libs/switchery/switchery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script>
    <script>
         function getNotifications(){
            var agentId='{{auth()->user()->AdminId}}';
            var data = {
                'dashboard':'dashboard',
                'AgentId':agentId,
                "_token": "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: '{{url("api/v1/agent/Notifications")}}',
                data: data,
                success: function (response) {
                //    console.log(response);
                   $('#notificationNav').html(response.msg);
                   $('#Totalnotifications').html(response.total);

                },
            });
        }
        getNotifications();
        setInterval(getNotifications,5000);
         function ClearAllNotification()
            {
            $.ajax({
              url:'{{url("api/v1/agent/ClearNotification")}}',
              type:"POST",
              data:{AgentId:{{\Illuminate\Support\Facades\Auth::user()->id}}},

          });
        }
    </script>
@yield('pageLevelScript')
@yield('scriptContent')
</body>
</html>





