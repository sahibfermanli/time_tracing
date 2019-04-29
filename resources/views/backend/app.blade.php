<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/libs/css/style.css">
    <link rel="stylesheet" href="/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="/assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="/assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="/assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" href="/css/main.css">
    @yield('css')
    <title>Time tracer</title>
</head>

<body>
<!-- ============================================================== -->
<!-- main wrapper -->
<!-- ============================================================== -->
<div class="dashboard-main-wrapper">
    <!-- ============================================================== -->
    <!-- navbar -->
    <!-- ============================================================== -->
    <div class="dashboard-header">
        <nav class="navbar navbar-expand-lg bg-white fixed-top">
            <a class="navbar-brand" href="/">Time tracer</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-right-top">
                    <li class="nav-item">
                        <div id="custom-search" class="top-search-bar">
                            @yield('buttons')
                        </div>
                    </li>
                    {{--<li class="nav-item dropdown notification">--}}
                        {{--<a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown"--}}
                           {{--aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-bell"></i> <span--}}
                                    {{--class="indicator"></span></a>--}}
                        {{--<ul class="dropdown-menu dropdown-menu-right notification-dropdown">--}}
                            {{--<li>--}}
                                {{--<div class="notification-title"> Notification</div>--}}
                                {{--<div class="notification-list">--}}
                                    {{--<div class="list-group">--}}
                                        {{--<a href="#" class="list-group-item list-group-item-action active">--}}
                                            {{--<div class="notification-info">--}}
                                                {{--<div class="notification-list-user-img"><img--}}
                                                            {{--src="assets/images/avatar-2.jpg" alt=""--}}
                                                            {{--class="user-avatar-md rounded-circle"></div>--}}
                                                {{--<div class="notification-list-user-block"><span--}}
                                                            {{--class="notification-list-user-name">Jeremy Rakestraw</span>accepted--}}
                                                    {{--your invitation to join the team.--}}
                                                    {{--<div class="notification-date">2 min ago</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</a>--}}
                                        {{--<a href="#" class="list-group-item list-group-item-action">--}}
                                            {{--<div class="notification-info">--}}
                                                {{--<div class="notification-list-user-img"><img--}}
                                                            {{--src="assets/images/avatar-3.jpg" alt=""--}}
                                                            {{--class="user-avatar-md rounded-circle"></div>--}}
                                                {{--<div class="notification-list-user-block"><span--}}
                                                            {{--class="notification-list-user-name">John Abraham </span>is--}}
                                                    {{--now following you--}}
                                                    {{--<div class="notification-date">2 days ago</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<div class="list-footer"><a href="#">View all notifications</a></div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    {{--<li class="nav-item dropdown connection">--}}
                        {{--<a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"--}}
                           {{--aria-haspopup="true" aria-expanded="false"> <i class="fas fa-fw fa-th"></i> </a>--}}
                        {{--<ul class="dropdown-menu dropdown-menu-right connection-dropdown">--}}
                            {{--<li class="connection-list">--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/github.png" alt="">--}}
                                            {{--<span>Github</span></a>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/dribbble.png"--}}
                                                                                 {{--alt=""> <span>Dribbble</span></a>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/dropbox.png" alt="">--}}
                                            {{--<span>Dropbox</span></a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/bitbucket.png"--}}
                                                                                 {{--alt=""> <span>Bitbucket</span></a>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/mail_chimp.png"--}}
                                                                                 {{--alt=""><span>Mail chimp</span></a>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">--}}
                                        {{--<a href="#" class="connection-item"><img src="assets/images/slack.png" alt="">--}}
                                            {{--<span>Slack</span></a>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<div class="conntection-footer"><a href="#">More</a></div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <li class="nav-item dropdown nav-user">
                        <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false"><img src="/assets/images/avatar-1.jpg" alt=""
                                                                           class="user-avatar-md rounded-circle"></a>
                        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                             aria-labelledby="navbarDropdownMenuLink2">
                            <div class="nav-user-info">
                                <h5 class="mb-0 text-white nav-user-name">{{Auth::user()->name}} {{Auth::user()->surname}}</h5>
                                @if(Auth::user()->role() == 1)
                                    {{--Chief (Manager)--}}
                                    <span class="status"></span><span class="ml-2">Manager</span>
                                @elseif(Auth::user()->role() == 2)
                                    {{--User--}}
                                    <span class="status"></span><span class="ml-2">User</span>
                                @elseif(Auth::user()->role() == 3)
                                    {{--Admin--}}
                                    <span class="status"></span><span class="ml-2">Admin</span>
                                @elseif(Auth::user()->role() == 4)
                                    {{--ProjectManager--}}
                                    <span class="status"></span><span class="ml-2">Project Manager</span>
                                @endif
                            </div>
                            {{--<a class="dropdown-item" href="/account"><i class="fas fa-user mr-2"></i>Account</a>--}}
                            {{--<a class="dropdown-item" href="/settings"><i class="fas fa-cog mr-2"></i>Setting</a>--}}
                            <a class="dropdown-item" href="/logout"><i class="fas fa-power-off mr-2"></i>Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <!-- ============================================================== -->
    <!-- end navbar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- left sidebar -->
    <!-- ============================================================== -->
    <div class="nav-left-sidebar sidebar-dark">
        <div class="menu-list">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav flex-column">
                        <li class="nav-divider">
                            Menu
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/"><i class="fa fa-home"></i> Home</a>
                        </li>

                        @if(Auth::user()->role() == 1)
                            {{--Chief--}}
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/tracer"><i class="fa fa-tasks"></i> Tracer</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/client-roles"><i class="fa fa-globe"></i> Client roles</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/clients"><i class="fa fa-user-secret"></i> Clients</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/projects"><i class="fa fa-list"></i> Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/tasks"><i class="fa fa-tasks"></i> Tasks</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/categories"><i class="fa fa-list-alt"></i> Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/users"><i class="fa fa-users"></i> Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/levels"><i class="fa fa-level-down-alt"></i> Levels</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/currencies"><i class="fa fa-money-bill-alt"></i> Currencies</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/chief/projects-library"><i class="fa fa-list-ol"></i> Projects library</a>
                            </li>
                        @elseif(Auth::user()->role() == 2)
                            {{--User--}}
                            <li class="nav-item">
                                <a class="nav-link" href="/user/tracer"><i class="fa fa-tasks"></i> Tracer</a>
                            </li>
                        @elseif(Auth::user()->role() == 3)
                            {{--Admin--}}
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/non-billable-codes"><i class="fa fa-list"></i> Non billable codes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/form-of-business"><i class="fa fa-list"></i> Form of business</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/currencies"><i class="fa fa-money-bill-alt"></i> Currencies</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/roles"><i class="fa fa-globe"></i> Roles</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/levels"><i class="fa fa-level-down-alt"></i> Levels</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/fields"><i class="fa fa-list-ul"></i> Fields</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/categories"><i class="fa fa-list-alt"></i> Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/users"><i class="fa fa-users"></i> Users</a>
                            </li>
                        @elseif(Auth::user()->role() == 4)
                            {{--ProjectManager--}}
                            <li class="nav-item">
                                <a class="nav-link" href="/project-manager/time-tracer"><i class="fa fa-tasks"></i> Time tracer</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/project-manager/tracer"><i class="fa fa-tasks"></i> Tracer</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/project-manager/projects"><i class="fa fa-list"></i> Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/project-manager/tasks"><i class="fa fa-tasks"></i> Tasks</a>
                            </li>
                        @endif

                    </ul>
                </div>
            </nav>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end left sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- wrapper  -->
    <!-- ============================================================== -->
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                @yield('content')
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        {{--<div class="footer" style="position: absolute; bottom: 0 !important;">--}}
            {{--<div class="container-fluid">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">--}}
                        {{--Copyright © 2019. All rights reserved. By <a target="_blank" href="https://facebook.com/sahib.fermanli">Sahib Farmanli</a>.--}}
                    {{--</div>--}}
                    {{--<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">--}}
                        {{--<div class="text-md-right footer-links d-none d-sm-block">--}}
                            {{--<a href="javascript: void(0);">About</a>--}}
                            {{--<a href="javascript: void(0);">Support</a>--}}
                            {{--<a href="javascript: void(0);">Contact Us</a>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        <!-- ============================================================== -->
        <!-- end footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end wrapper  -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- end main wrapper  -->
<!-- ============================================================== -->
<!-- Optional JavaScript -->
<!-- jquery 3.3.1 -->
<script src="/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<!-- bootstap bundle js -->
<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<!-- slimscroll js -->
<script src="/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<!-- main js -->
<script src="/assets/libs/js/main-js.js"></script>
<!-- chart chartist js -->
<script src="/assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
<!-- sparkline js -->
<script src="/assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
<!-- morris js -->
<script src="/assets/vendor/charts/morris-bundle/raphael.min.js"></script>
<script src="/assets/vendor/charts/morris-bundle/morris.js"></script>
<!-- chart c3 js -->
<script src="/assets/vendor/charts/c3charts/c3.min.js"></script>
<script src="/assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
<script src="/assets/vendor/charts/c3charts/C3chartjs.js"></script>
<script src="/assets/libs/js/dashboard-ecommerce.js"></script>

@yield('js')
</body>

</html>