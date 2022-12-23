<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{{@$pageTitle}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build SearchReality, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/superadmin/images/favicon.ico">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets') }}/superadmin/css/bootstrap.min.css" id="bootstrap-stylesheet" rel="stylesheet"
          type="text/css"/>
    <!-- Icons Css -->
    <link href="{{ asset('assets') }}/superadmin/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <!-- App Css-->
    <link href="{{ asset('assets') }}/superadmin/css/app.min.css" id="app-stylesheet" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    @yield('pageLevelStyle')
    <style>
        #blur {
            font-size: 40px;
            color: #00000024;
            text-shadow: 0 0 3px #000;
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
                <a class="nav-link dropdown-toggle  waves-effect" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="fe-bell noti-icon"></i>
                    <span class="badge badge-danger rounded-circle noti-icon-badge">9</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">
                                    <span class="float-right">
                                        <a href="" class="text-dark">
                                            <small>Clear All</small>
                                        </a>
                                    </span>Notification
                        </h5>
                    </div>

                    <div class="slimscroll noti-scroll">

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item active">
                            <div class="notify-icon">
                                <img src="" class="img-fluid rounded-circle" alt=""/></div>
                            <p class="notify-details">Cristina Pride</p>
                            <p class="text-muted mb-0 user-msg">
                                <small>Hi, How are you? What about our next meeting</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon bg-primary">
                                <i class="mdi mdi-comment-account-outline"></i>
                            </div>
                            <p class="notify-details">Caleb Flakelar commented on Admin
                                <small class="text-muted">1 min ago</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon">
                                <img src="" class="img-fluid rounded-circle" alt=""/></div>
                            <p class="notify-details">Karen Robinson</p>
                            <p class="text-muted mb-0 user-msg">
                                <small>Wow ! this admin looks good and awesome design</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon bg-warning">
                                <i class="mdi mdi-account-plus"></i>
                            </div>
                            <p class="notify-details">New user registered.
                                <small class="text-muted">5 hours ago</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon bg-info">
                                <i class="mdi mdi-comment-account-outline"></i>
                            </div>
                            <p class="notify-details">Caleb Flakelar commented on Admin
                                <small class="text-muted">4 days ago</small>
                            </p>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon bg-secondary">
                                <i class="mdi mdi-heart"></i>
                            </div>
                            <p class="notify-details">Carlos Crouch liked
                                <b>Admin</b>
                                <small class="text-muted">13 days ago</small>
                            </p>
                        </a>
                    </div>

                    <!-- All-->
                    <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                        View all
                        <i class="fi-arrow-right"></i>
                    </a>

                </div>
            </li>

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('assets') }}/superadmin/images/avatar.png" alt="user-image"
                         class="rounded-circle">
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
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings"></i>
                        <span>Settings</span>
                    </a>

                    <!-- item-->
                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-lock"></i>
                        <span>Lock Screen</span>
                    </a> -->

                    <div class="dropdown-divider"></div>

                    <!-- item-->

                    <a href="{{ url('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                       class="dropdown-item notify-item profile_logout_link">
                        <i class="fe-log-out"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                </div>
            </li>

            <li class="dropdown notification-list">
                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect">
                    <i class="fe-settings noti-icon"></i>
                </a>
            </li>


        </ul>

        <!-- LOGO -->
        <div class="logo-box">
            <a href="/" class="logo logo-dark text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets') }}/superadmin/images/logo-dark.png" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets') }}/superadmin/images/logo-sm.png" alt="" height="24">
                        </span>
            </a>
            <a href="index.html" class="logo logo-light text-center">
                        <span class="logo-lg">
                            <img src="{{ asset('assets') }}/superadmin/images/logo-light.png" alt="" height="16">
                        </span>
                <span class="logo-sm">
                            <img src="{{ asset('assets') }}/superadmin/images/logo-sm.png" alt="" height="24">
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
                <img src="{{ asset('assets') }}/superadmin/images/avatar.png" alt="user-img" title="Mat Helme"
                     class="rounded-circle img-thumbnail avatar-md">
                <div class="dropdown">
                    <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown"
                       aria-expanded="false">{{\Illuminate\Support\Facades\Auth::user()->name}}</a>
                    <div class="dropdown-menu user-pro-dropdown">

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-user mr-1"></i>
                            <span>My Account</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-settings mr-1"></i>
                            <span>Settings</span>
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
            <!--- Sidemenu -->
            <div id="sidebar-menu">

                <ul class="metismenu" id="side-menu">

                    <li class="menu-title">Navigation</li>

                    <li>
                        <a href="{{url('super-admin/')}}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-users-cog"></i>
                            <span> Agents </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('super-admin/agent/add')}}">Add Agent</a></li>
                            <li><a href="{{url('super-admin/agent/')}}">View Agents</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-users-cog"></i>
                            <span> SearchReality </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('super-admin/lead')}}">Leads</a></li>
<!--                            <li><a href="#">Contacts</a></li>
                            <li><a href="#">Property Stats</a></li>
                            <li><a href="#">Automations</a></li>-->
                        </ul>
                    </li>

                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-user"></i>
                            <span> Agents </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('super-admin/list')}}">All Agents</a></li>
                        <!-- <li><a href="{{url('super-admin/lead')}}">All leads</a></li> -->
                            <!--- <li><a href="#">Condos</a></li>
                             <li><a href="#">Custom Properties</a></li> -->
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fab fa-accusoft"></i>
                            <span> Marketing </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('super-admin/campaign/')}}">Email Campaigns</a></li>
                            <li><a href="{{url('super-admin/template')}}">Email Templates</a></li>
<!--                            <li><a href="#">Segmentation</a></li>
                            <li><a href="#">Reporting</a></li>-->
                        </ul>
                    </li>
                    <li>
                        {{-- <a href="javascript: void(0);">
                            <i class="fas fa-globe" id="blur"></i>
                            <span> Website </span>
                            <span class="menu-arrow"></span>
                        </a> --}}
<!--                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">Menu Builder</a></li>
                            <li><a href="{{url('super-admin/website/pages/')}}">Pages</a></li>
                            <li><a href="#">Team Members</a></li>
                            <li><a href="#">Landing Pages</a></li>
                            <li><a href="#">Blogs</a></li>
                            <li><a href="#">Widgets</a></li>
                        </ul>-->
                    </li>
                    <li>
                        {{-- <a href="javascript: void(0);">
                            <i class="fas fa-cogs" id="blur"></i>
                            <span> Settings </span>
                            <span class="menu-arrow"></span>
                        </a> --}}
<!--                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">Profile</a></li>
                            <li><a href="#">Staffs</a></li>
                            <li><a href="#">Templates</a></li>
                        </ul>-->
                    </li>
                    {{-- <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-globe" id="blur"></i>
                            <span> Events </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">All Events</a></li>
                            <li><a href="#">Schedule By Calendar</a></li>
                            <li><a href="#">Availability Calendar</a></li>
                        </ul>
                    </li> --}}

                    <li>
                        <a href="javascript: void(0);">
                            <i class="far fa-building"></i>
                            <span> Properties </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url('super-admin/property')}}">Residential</a></li>
<!--                            <li><a href="#">Commercial</a></li>
                            <li><a href="#">Condos</a></li>
                            <li><a href="#">Custom Properties</a></li>-->
                        </ul>
                    </li>
<!--                    <li>
                        <a href="{{url('super-admin/assignment')}}">
                            <i class="far fa-building"></i>
                            <span> Assignment Manager </span>
                            <span class="menu-arrow"></span>
                        </a>

                    </li>-->
                </ul>

            </div>
            <!-- End Sidebar -->
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
                    2021 &copy; SearchReality <a href="">#</a>
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
    <script src="{{ asset('assets') }}/superadmin/js/vendor.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script>
@yield('pageLevelJS')
<!-- App js -->
    <script src="{{ asset('assets') }}/superadmin/js/app.min.js"></script>
@yield('pageLevelScript')
@yield('scriptContent')
</body>
</html>
