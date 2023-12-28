<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>A2Z Harvests Admin | Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/css/animate.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/css/style.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/css/style-responsive.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/css/theme/default.css" rel="stylesheet" id="theme" />
    <!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
    <link href="{{url('/')}}/admintheme/assets/plugins/jquery-jvectormap/jquery-jvectormap.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Responsive/css/responsive.bootstrap.min.css" rel="stylesheet" />
    <!-- ================== END PAGE LEVEL STYLE ================== -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="{{url('/')}}/admintheme/assets/plugins/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
    <style type="text/css">
        @media print
        {
            .non-printable { display: none; }
        }
    </style>
</head>
<body>
<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->

<!-- begin #page-container -->
<div id="page-container" class="fade page-sidebar-fixed page-header-fixed">
    <!-- begin #header -->
    <div id="header" class="header navbar navbar-default navbar-fixed-top">
        <!-- begin container-fluid -->
        <div class="container-fluid">
            <!-- begin mobile sidebar expand / collapse button -->
            <div class="navbar-header">
                <a href="{{route('admin::dashboard')}}" class="navbar-brand"><span class="navbar-logo"></span>A2Z Harvests </a>
                <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- end mobile sidebar expand / collapse button -->

            <!-- begin header navigation right -->
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown navbar-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{url('/')}}/frontendtheme/images/logo.png" alt="" />
                        <span class="hidden-xs">{{ ucfirst(Auth::user()->user_name)}}</span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu animated fadeInLeft">
                        <li class="arrow"></li>
                        <li><a class="fancybox fancybox.iframe" href="{{route('admin::profile',['id'=>Auth::user()->id])}}">Profile</a></li>
                        <li><a class="fancybox fancybox.iframe" href="{{route('admin::changePassForm')}}">Change Password</a></li>
                        <li><a href="{{url('logout')}}">Log Out</a></li>
                    </ul>
                </li>
            </ul>
            <!-- end header navigation right -->
        </div>
        <!-- end container-fluid -->
    </div>
    <!-- end #header -->

    <!-- begin #sidebar -->
    @include('admin/layouts/leftmenu');
    <div class="sidebar-bg"></div>
    <!-- end #sidebar -->

    <!-- begin #content -->
@yield('content')
    <!-- end #content -->

    <!-- begin scroll to top btn -->
    <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
    <!-- end scroll to top btn -->
</div>
<div class="modal fade" id="confirmDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Delete Parmanently</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure about this ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="" class="btn btn-danger" id="confirm_del">Delete</a>
            </div>
        </div>
    </div>
</div>
<!-- end page container -->

<!-- ================== BEGIN BASE JS ================== -->
{{--<script src="{{url('/')}}/admintheme/assets/plugins/jquery/jquery-1.9.1.min.js"></script>--}}
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="{{url('/')}}/admintheme/assets/crossbrowserjs/html5shiv.js"></script>
<script src="{{url('/')}}/admintheme/assets/crossbrowserjs/respond.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/crossbrowserjs/excanvas.min.js"></script>
<![endif]-->
<script src="{{url('/')}}/admintheme/assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/jquery-cookie/jquery.cookie.js"></script>
<!-- ================== END BASE JS ================== -->

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{url('/')}}/admintheme/assets/plugins/gritter/js/jquery.gritter.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/flot/jquery.flot.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/flot/jquery.flot.time.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/sparkline/jquery.sparkline.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/jquery-jvectormap/jquery-jvectormap.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/jquery-jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/media/js/jquery.dataTables.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/js/table-manage-default.demo.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/js/dashboard.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/js/apps.min.js"></script>


<!-- ================== END PAGE LEVEL JS ================== -->
<!-- jQuery 2.1.4 -->

<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!---------------------------------------------------------------------------  Fancybox Scripts Start--------------------------------------------------------------------------->
<script src="{{ url('/') }}/admintheme/plugins/fancybox/jquery.fancybox.js"></script>
<link rel="stylesheet" href="{{ url('/') }}/admintheme/plugins/fancybox/jquery.fancybox.css">
<script type="text/javascript">
    $(document).ready(function() {
        $('.fancybox').fancybox({
            helpers: {title: null},
            width: 800,
            height: 600,
            fitToView : true,
            autoSize : true,
            padding: 0,
            openEffect: 'elastic',
            afterClose : function() {
                window.location.reload();
            }
        });
    });
    $(document).on('click','.sorting_1',function(){
        $(document).find('.fancybox').fancybox({
            helpers: {title: null},
            width: 800,
            height: 600,
            fitToView : true,
            autoSize : true,
            padding: 0,
            openEffect: 'elastic',
            afterClose : function() {
                window.location.reload();
            }
        });
    });
</script>
<!---------------------------------------------------------------------------  Fancybox Scripts Start--------------------------------------------------------------------------->
<script>
    $(document).ready(function() {
        App.init();
       // Dashboard.init();
        TableManageDefault.init();
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            todayHighlight:'true',
            autoclose:true,
            maxDate: 0,
            defaultDate: '-1',
        });
        $('.datepicker1').datepicker({
            dateFormat: 'yy-mm-dd',
            todayHighlight:'true',
            autoclose:true,
            maxDate: 0,
            defaultDate: '-1',
        });
    });
</script>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-53034621-1', 'auto');
    ga('send', 'pageview');

</script>
<script>
    function getDeleteRoute($route)
    {
        $('#confirm_del').attr('href',$route);
    }
</script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/dataTables.buttons.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/buttons.bootstrap.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/buttons.flash.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/jszip.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/pdfmake.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/vfs_fonts.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/buttons.html5.min.js"></script>
<script src="{{url('/')}}/admintheme/assets/plugins/DataTables/extensions/Buttons/js/buttons.print.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&libraries=places"
        async defer>
</script>
@stack('scripts')
</body>
</html>
