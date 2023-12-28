<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->

<!--[if !IE]><!-->

<html lang="en">

<!--<![endif]-->

<head>

    <meta charset="utf-8" />

    <title>A2Z Harvests @if(Request::segment(1)=='supplier') Supplier @else Admin @endif | Login Page</title>

    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />

    <meta content="" name="description" />

    <meta content="" name="author" />



    <!-- ================== BEGIN BASE CSS STYLE ================== -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <link href="{{url('/')}}/admintheme/assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/css/animate.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/css/style.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/css/style-responsive.min.css" rel="stylesheet" />

    <link href="{{url('/')}}/admintheme/assets/css/theme/default.css" rel="stylesheet" id="theme" />

    <!-- ================== END BASE CSS STYLE ================== -->



    <!-- ================== BEGIN BASE JS ================== -->

    <script src="{{url('/')}}/admintheme/assets/plugins/pace/pace.min.js"></script>

    <!-- ================== END BASE JS ================== -->

</head>

<body class="pace-top">

<!-- begin #page-loader -->

<div id="page-loader" class="fade in"><span class="spinner"></span></div>

<!-- end #page-loader -->

<div class="login-cover">

    <div class="login-cover-image"><img src="{{url('/')}}/admintheme/assets/img/slider-img-3.jpg" data-id="login-cover-image" alt="" /></div>

    <div class="login-cover-bg"></div>

</div>

<!-- begin #page-container -->

<div id="page-container" class="fade">

    <!-- begin login -->

    <div class="login login-v2" data-pageload-addclass="animated fadeIn">

        <!-- begin brand -->

        <div class="login-header" style="text-align: center">

            <div class="brand">

                <span class="logo"></span> A2Z Harvests @if(Request::segment(1)=='supplier') Supplier @else Admin @endif

                {{--<small>Manage your website content after login</small>--}}

            </div>

        </div>

        <!-- end brand -->

        <div class="login-content">

            <form action="{{route('admin_login')}}" method="post" class="margin-bottom-0">

                {{csrf_field()}}

                @if(Session::has('error'))

                    <div class="alert alert-danger">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <strong>{{Session::get('error')}}</strong>

                    </div>

                @endif

                @if(Session::has('logout'))

                    <div class="alert alert-success">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <strong>{{Session::get('logout')}}</strong>

                    </div>

                @endif

                @if ($errors->any())

                    <div class="alert alert-danger">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div><br />

                @endif

                <div class="form-group m-b-20">

                    <input type="text" class="form-control input-lg inverse-mode no-border" name="email" placeholder="Email Address"  />

                </div>

                <div class="form-group m-b-20">

                    <input type="password" class="form-control input-lg inverse-mode no-border" name="password" placeholder="Password"  />

                </div>

                <div class="checkbox m-b-20">

                    <label>

                        {{--<input type="checkbox" /> Remember Me--}}

                    </label>

                </div>

                <div class="login-buttons">

                    <button type="submit" class="btn btn-success btn-block btn-lg">Sign me in</button>

                </div>

            </form>

        </div>

    </div>

    <!-- end login -->

</div>

</div>

<!-- end theme-panel -->

</div>

<!-- end page container -->



<!-- ================== BEGIN BASE JS ================== -->

<script src="{{url('/')}}/admintheme/assets/plugins/jquery/jquery-1.9.1.min.js"></script>

<script src="{{url('/')}}/admintheme/assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>

<script src="{{url('/')}}/admintheme/assets/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>

<script src="{{url('/')}}/admintheme/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!--[if lt IE 9]>

<script src="{{url('/')}}/admintheme/assets/crossbrowserjs/html5shiv.js"></script>

<script src="assets/crossbrowserjs/respond.min.js"></script>

<script src="{{url('/')}}/admintheme/assets/crossbrowserjs/excanvas.min.js"></script>

<![endif]-->

<script src="{{url('/')}}/admintheme/assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="{{url('/')}}/admintheme/assets/plugins/jquery-cookie/jquery.cookie.js"></script>

<!-- ================== END BASE JS ================== -->



<!-- ================== BEGIN PAGE LEVEL JS ================== -->

<script src="{{url('/')}}/admintheme/assets/js/apps.min.js"></script>

<!-- ================== END PAGE LEVEL JS ================== -->



<script>

    $(document).ready(function() {

        App.init();

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

</body>

</html>

