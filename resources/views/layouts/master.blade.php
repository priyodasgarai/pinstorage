<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="content">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    {{-- favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/admin/img/favicon.ico') }}">
    {{-- <link href="{{asset('assets/admin/img/favicon.ico')}}"> --}}
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/chartist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/blueimp/css/blueimp-gallery.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/jquery-confirm.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/js/plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/admin/js/jquery-3.1.1.min.js') }}"></script>
    <script type="text/javascript">
        let baseUrl = "{{ url('/') }}/";
        let _token = "{{ csrf_token() }}";
    </script>
</head>

<body class="">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span>
                                <img alt="image" class="img-circle" src="{{ asset('assets/images/logo.png') }}"
                                    style="height:80px;width:80px;" />
                            </span>

                        </div>
                        <div class="logo-element">
                            <a href="#">WT</a>
                        </div>
                    </li>
                    <li class="@if (Request::segment(1) == 'dashboard') active @endif">
                        <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i> <span
                                class="nav-label">Dashboard</span></a>
                    </li>
                    <li class="@if (Request::segment(1) == 'admin/customer-management' && Request::segment(2) == 'list') active @endif">
                        <a href="{{ route('admin.customer-management.list') }}"><i class="fa fa-users" aria-hidden="true"></i>
                            <span class="nav-label">Customer Management</span></a>
                    </li>
                    <li class="@if (Request::segment(1) == 'admin/slider-management' && Request::segment(2) == 'list') active @endif">
                        <a href="{{ route('admin.slider-management.list') }}"><i class="fa fa-picture-o"
                                aria-hidden="true"></i><span class="nav-label">Slider Management</span></a>
                    </li>
                    <li class="@if (Request::segment(1) == 'admin/page-management' && Request::segment(2) == 'list') active @endif">
                        <a href="{{ route('admin.page-management.list') }}"><i class="fa fa-picture-o"
                                aria-hidden="true"></i><span class="nav-label">Page Management</span></a>
                    </li>

                    </li>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#"><span
                                    class="text-muted text-xs block"><b>Welcome, Super Admin </b><b
                                        class="caret"></b></span> </span></a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="{{ route('admin.edit-profile') }}">Profile</a></li>
                                <li><a href="{{ route('admin.change-password') }}">Change Password</a></li>

                                <li><a href="{{ route('admin.logout') }}">Logout</a></li>
                            </ul>

                        </li>
                        <li>
                            <a href="{{ route('admin.logout') }}">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @yield('content')
            <div class="footer">
                <div class="pull-right">

                </div>
                <div>
                    <strong>Copyright</strong> {{ env('APP_NAME') }} &copy; <?php echo date('Y'); ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/blueimp/jquery.blueimp-gallery.min.js') }}"></script>
    <!-- Custom and plugin javascript -->
    <script src="{{ asset('assets/admin/js/inspinia.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/pace/pace.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/chartist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery-confirm.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin-common.js') }}"></script>
    <!-- Dual Listbox -->
    <script src="{{ asset('assets/admin/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js') }}"></script>
    @stack('scripts')
</body>

</html>
