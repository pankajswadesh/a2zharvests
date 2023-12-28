<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$seo_data->title}}</title>
    <meta name=title content="{{$seo_data->title}}">
    <meta name=description content="{{$seo_data->description}}">
    <meta name=keywords content="{{$seo_data->keywords}}">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0"/>
    <!-- favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/')}}frontendtheme/images/favicon.ico">
    <link rel="apple-touch-icon" href="{{asset('/')}}frontendtheme/apple-touch-icon.png">
    <!-- bootstrap css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/bootstrap.min.css" rel="stylesheet">
    <!-- fontawesome css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/fontawesome.min.css" rel="stylesheet">
    <!-- animate css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/animate.css" rel="stylesheet">
    <!--    exzoom-->
    <link rel="stylesheet" href="{{asset('/')}}frontendtheme/exzoom/css/jquery.exzoom.css">
    <!-- slick css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/slick.css" rel="stylesheet">
    <link href="{{asset('/')}}frontendtheme/css/plugins/slick-theme.css" rel="stylesheet">
    <!-- google font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">    <!-- custom style css -->
    <link href="{{asset('/')}}frontendtheme/css/style.css" rel="stylesheet">
    <!-- responsive css -->
    <link href="{{asset('/')}}frontendtheme/css/responsive.css" rel="stylesheet">
    <!-- modernizr js -->
    <link href="{{asset('/')}}frontendtheme/css/custom.css" rel="stylesheet">
    <script src="{{asset('/')}}frontendtheme/js/plugins/modernizr.js"></script>

    <link rel="stylesheet" href="{{asset('/')}}frontendtheme/exzoom/css/jquery.exzoom.css">
    <link href="{{asset('/')}}frontendtheme/css/plugins/nouislider.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <style>
        .toast-top-center {
            max-width: 1200px !important;
            width: 90% !important;
        }
    </style>
    @stack('css')
</head>
<body>
<!-- topbar area starts  -->
<section id="topbar-area">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="logo-search">
                    <ul>
                        <li class="logo-pic1"><a href="{{url('/')}}"><img src="{{asset('/')}}frontendtheme/images/logo.png" alt=""></a></li>
                        <li class="srch-frm1">
                            <form class="search_form" method="get" action="{{route('products',['search',''])}}">
                                <div class="form-group">
                                    <input type="text" name="search_query" class="form-control search_query" placeholder="What are you looking for?" autocomplete="off">
                                    <button type="submit"><img src="{{asset('/')}}frontendtheme/images/search.svg" alt=""></button>
                                    <div class="autocomplete-list" id="suggestion_list"></div>
                                </div>
                            </form>
                        </li>
                        <li class="iconsmall">
                            <ul class="cart-box box2">
                                <li class="cart-login search">
                                    <a href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                             class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </a>
                                </li>
                                <li class="cart-login">
                                    <a href="tel:{{App\Model\ContactUsModel::first()->phone}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                             class="bi bi-telephone" viewBox="0 0 16 16">
                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/>
                                        </svg>
                                        <span>{{App\Model\ContactUsModel::first()->phone}}</span>
                                    </a>
                                </li>
                                @php use App\repo\datavalue;$lat_long = datavalue::getLatLong();@endphp
                                <li class="cart-login">
                                    <a href="#" data-toggle="modal" data-target="#locationSet">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                             class="bi bi-geo-alt" viewBox="0 0 16 16">
                                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                        </svg>
                                        <span>{{implode(' ', array_slice(explode(' ', $lat_long["address"]), 0, 3))}}</span>
                                    </a>
                                </li>
                                <li class="cart-login area2">
                                    @if(Auth::check() && Auth::user()->hasRole('user'))
                                        <a href="{{route('user::my_account')}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                                 class="bi bi-person" viewBox="0 0 16 16">
                                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                            </svg>
                                            <span>My Account</span></a>
                                    @else
                                        <a href="#" data-toggle="modal" data-target="#myModal1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                                 class="bi bi-person" viewBox="0 0 16 16">
                                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                            </svg>
                                            <span>Login / Signup</span>
                                        </a>
                                    @endif
                                </li>
                                @php
                                    if(Auth::check()){
                                       $cart_count = \App\Model\CartModel::where('user_id',Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->count();
                                    }else{
                                       $cart_count = 0;
                                    }
                                @endphp
                                <li class="cart-login">
                                    <a href="{{route("cart")}}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff"
                                             class="bi bi-cart2" viewBox="0 0 16 16">
                                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                        </svg>
                                        <span id="cart_count">{{$cart_count}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- -------------login signup area start------------ -->
<div class="login-signup-sec">
    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="login-signup">
                        <div class="login-area">
                            <div class="form">
                                <div class="line-head">
                                    <h3>Login/SignUp</h3>
                                </div>
                                <div id="sign_up_msg"></div>
                                <form id="sign_up">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <input type="tel" name="phone" class="form-control" placeholder="Phone No.">
                                        <label>Phone No.</label>
                                    </div>
                                    <div class="form-group">
                                        <input type="tel" name="referal_code" class="form-control" placeholder="Referral Code(Optional)">
                                        <label>Referral Code(Optional)</label>
                                    </div>
                                    <div class="form-group" style="position: relative;display: none;" id="otp_part">
                                        <input type="text" class="form-control" name="otp"  placeholder="Please Enter OTP">
                                        <input type="hidden" name="token" id="token" class="form-control">
                                        <label>Please Enter OTP</label>
                                    </div>
                                    <div class="button-area">
                                        <div class="btn-type-1">
                                            <button type="submit" id="get_otp">Get Otp</button>
                                            <button type="submit" id="verify_submit" style="display: none;">Submit</button><br>
                                            <button type="submit" class="mt-3" id="re_get_otp" style="display: none;">Resend Otp</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- -----------login signup area close-------------- -->
<div class="login-signup-sec">
    <div class="modal fade" id="locationSet" tabindex="-1" role="dialog" aria-labelledby="locationSet" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="login-signup">
                        <div class="login-area">
                            <div class="form">
                                <div class="line-head">
                                    <h3>Select Your Location</h3>
                                </div>
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
                                <form id="location_set" method="get" action="{{route('locationSet')}}">
                                    {{csrf_field()}}
                                    <input type="hidden" name="latitude" id="latitude" class="form-control">
                                    <input type="hidden" name="longitude" id="longitude" class="form-control">
                                    <div class="button-area">
                                        <div class="btn-type-1">
                                            <button type="button" onclick="getLocation();">Use Your Current Location</button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="address" placeholder="Enter manual location..." id="address" class="form-control">
                                        <div class="button-area">
                                            <div class="btn-type-1">
                                                <button type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- topbar area ends  -->
<!----category area start----->
<!----category area close----->
<section class="slide-menu" id="menu-slider">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="slide-menuhead">
                <h5 class="spctg">Shop By Category</h5>
                <div id="menu-carousel" class="slidemenu-main">
                    @foreach($categories as $row)
                    <div class="item">
                        <div class="product-details">
                            <div class="product-details-inner">
                                <div class="product-image">
                                    <a href="{{route('sub_categories',$row->url)}}"><img src="{{$row->category_image}}" alt=""></a>
                                </div>
                            </div>
                            <div class="text-content">
                                <h5><a href="{{route('sub_categories',$row->url)}}">{{$row->category_name}}</a></h5>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- slider area starts -->
@yield('content')

<!-- footer area starts -->
<footer id="footer-area">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="footer-area-inner-content">
                    <div class="logo-area">
                        <a href="{{url('/')}}"><img src="{{asset('/')}}frontendtheme/images/logo.png" alt=""></a>
                    </div>
                    <p>{{$footer_description}}</p>
                    <h5>Follow us on</h5>
                    <ul class="social-links">
                        <li><a href="{{$webData["facebook"]}}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="{{$webData["twitter"]}}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="{{$webData["linkedin"]}}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                        <li><a href="{{$webData["instagram"]}}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <div class="footer-area-inner-content">
                    <h4>need help</h4>
                    <ul class="quick-links">
                        <li><a href="{{url("/")}}">Home</a></li>
                        <li><a href="{{url("about-us")}}">About us</a></li>
                        <li><a href="{{url("contact-us")}}">Contact Us</a></li>
                        <li><a href="{{url("faq")}}">FAQ</a></li>
                        <li><a href="{{url("terms-and-conditions")}}">Terms & Condition</a></li>
                        <li><a href="{{url("become-a-seller")}}">Become a seller</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3">
                <div class="footer-area-inner-content">
                    <h4>categories</h4>
                    <ul class="quick-links">
                        @foreach($footer_categories as $row)
                            <li><a href="{{route('sub_categories',$row->url)}}">{{$row->category_name}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-3">
                <div class="footer-area-inner-content">
                    <div id="newsletter_subscribe_msg"></div>
                    <form id="newsletter_subscribe">
                        {{csrf_field()}}
                        <div class="form-group">
                            <input placeholder="Email" value="" name="email" id="email" class="form-control"
                                   aria-required="true" type="text">
                            <button type="submit" class="submit-btn">subscribe</button>
                        </div>
                    </form>
                    <h4>newsletter</h4>
                    <p>Join our newsletter to be informed about offers and news.</p>
                    <h5>Download app</h5>
                    <div class="app-logo">
                        <a href="#"><img src="{{asset('/')}}frontendtheme/images/google-play.png" alt=""></a>
                        <a href="#"><img src="{{asset('/')}}frontendtheme/images/app-store.png" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="payment-mode">
        <ul>
            <li><img src="{{asset('/')}}frontendtheme/images/paytmlogo.jpg" alt="" loading="lazy"></li>
            <li><img src="{{asset('/')}}frontendtheme/images/visalogo.jpg" alt="" loading="lazy"></li>
            <li><img src="{{asset('/')}}frontendtheme/images/mastercard.jpg" alt="" loading="lazy"></li>
            <li><img src="{{asset('/')}}frontendtheme/images/mestrologo.jpg" alt="" loading="lazy"></li>
            <li><img src="{{asset('/')}}frontendtheme/images/rupaylogo.jpg" alt="" loading="lazy"></li>
            <li><img src="{{asset('/')}}frontendtheme/images/bhimlogo.jpg" alt="" loading="lazy"></li>
            <li>Net Banking</li>
            <li>Cash On Delivery</li>
            <li>Card On Delivery</li>

        </ul>
    </div>
    <div class="footer-bottom">
        <div class="row">
            <div class="col-md-12">
                <div class="copy-right">
                    <p>&copy; 2021 A2Z Harvests / All Rights Reserved</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- footer area ends -->
<!-- jquery -->
<!--<script src="{{asset('/')}}frontendtheme/js/plugins/jquery-3.4.1.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- popper js -->
<script src="{{asset('/')}}frontendtheme/js/plugins/popper.min.js"></script>
<!-- bootstrap js -->
<script src="{{asset('/')}}frontendtheme/js/plugins/bootstrap.min.js"></script>
<!-- slick js -->
<script src="{{asset('/')}}frontendtheme/js/plugins/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- main js -->
<script src="{{asset('/')}}frontendtheme/js/main.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&libraries=places"></script>
<script>
    $(document).ready(function () {
        toastr.options = {
            tapToDismiss : true,
            positionClass : "toast-top-center"
        };
    });
    
    
    $("#login").on('submit',function (e) {
        e.preventDefault();
        var url="{{route('user_login')}}";
        $.ajax({
            url: url,
            type: "post",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data)
            {
                console.log(data);
                if(data.status=="success"){
                    var html = '<div class="alert alert-success alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Success! </strong>' + data.msg+
                        '</div>';
                    $("#login_msg").html(html);
                    $("#login").find('input').not("input[name=_token]").val('');
                    setTimeout(function () {
                        $("#login_msg").html('');
                        $("#exampleModal").modal('toggle');
                        window.location.reload();
                    },1000);
                }else{
                    var html = '<div class="alert alert-danger alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Fail! </strong>' + data.msg+
                        '</div>';
                    $("#login_msg").html(html);
                }
            }
        });
    });
    $("#get_otp").on('click',function (e) {
         
        e.preventDefault();
        var url="{{route('user_signup')}}";
        var myForm = document.getElementById('sign_up');
        var phone = $("input[name=phone]").val();
        $.ajax({
            url: url,
            type: "post",
            data: new FormData(myForm),
            processData: false,
            contentType: false,
            success: function (data)
            {
                console.log(data);
                if(data.status=="success"){
                    var html = '<div class="alert alert-success alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Success! </strong>' + data.msg+
                        '</div>';
                    $("#sign_up_msg").html(html);
                    $("#token").val(data.data.api_token);
                    setTimeout(function () {
                        $("#get_otp").hide();
                        $("#otp_part").show();
                        $("#verify_submit").show();
                        $("#re_get_otp").show();
                    },1000);
                }else{
                    var html = '<div class="alert alert-danger alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Fail! </strong>' + data.msg+
                        '</div>';
                    $("#sign_up_msg").html(html);
                }
            }
        });
    });
    $("#re_get_otp").on('click',function (e) {
        e.preventDefault();
        var url="{{route('user_signup')}}";
        var myForm = document.getElementById('sign_up');
        $.ajax({
            url: url,
            type: "post",
            data: new FormData(myForm),
            processData: false,
            contentType: false,
            success: function (data)
            {
                console.log(data);
                if(data.status=="success"){
                    var html = '<div class="alert alert-success alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Success! </strong>Opt re-sent to your mobile.'
                    '</div>';
                    $("#sign_up_msg").html(html);
                    $("#token").val(data.data.api_token);
                    setTimeout(function () {
                        $("#get_otp").hide();
                        $("#otp_part").show();
                        $("#verify_submit").show();
                    },1000);
                }else{
                    var html = '<div class="alert alert-danger alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Fail! </strong>' + data.msg+
                        '</div>';
                    $("#sign_up_msg").html(html);
                }
            }
        });
    });
    $("#verify_submit").on('click',function (e) {
        e.preventDefault();
        var url="{{route('verify_account')}}";
        var myForm = document.getElementById('sign_up');
        $.ajax({
            url: url,
            type: "post",
            data: new FormData(myForm),
            processData: false,
            contentType: false,
            success: function (data)
            {
                console.log(data);
                if(data.status=="success"){
                    var html = '<div class="alert alert-success alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Success! </strong>' + data.msg+
                        '</div>';
                    $("#sign_up_msg").html(html);
                    $("#verify_submit").find('input').not("input[name=_token]").val('');
                    setTimeout(function () {
                        $("#sign_up_msg").html('');
                        $("#exampleModal").modal('toggle');
                        window.location.reload();
                    },1000);
                }else{
                    var html = '<div class="alert alert-danger alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Fail! </strong>' + data.msg+
                        '</div>';
                    $("#sign_up_msg").html(html);
                }
            }
        });
    });
    $("#newsletter_subscribe").on('submit',function (e) {
        e.preventDefault();
        var url="{{route('newsletter_subscribe')}}";
        $.ajax({
            url: url,
            type: "post",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (data)
            {
                console.log(data);
                if(data.status=="success"){
                    var html = '<div class="alert alert-success alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Success! </strong>' + data.msg+
                        '</div>';
                    $("#newsletter_subscribe_msg").html(html);
                    $("#newsletter_subscribe").find('input').not("input[name=_token]").val('');
                }else{
                    var html = '<div class="alert alert-danger alert-dismissible">\n' +
                        '  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>\n' +
                        '  <strong>Fail! </strong>' + data.msg+
                        '</div>';
                    $("#newsletter_subscribe_msg").html(html);
                }
            }
        });
    });
</script>

<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '577510706957781');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1"
         src="https://www.facebook.com/tr?id=577510706957781&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
<script>
    function add_cart(supplier_id,product_id){
        $.ajax({
            url: "{{route('add_to_cart')}}",
            type: "post",
            data: {_token:'{{csrf_token()}}',supplier_id:supplier_id,product_id:product_id,quantity:1},
            success: function (resp)
            {
                console.log(resp);
                if(resp.status==="success"){
                    var count = resp.data.total_qty;
                    toastr.success(resp.msg, {timeOut: 10000});
                    $("#cart_count").text(count);
                }else{
                    toastr.warning(resp.msg, {timeOut: 10000});
                }
            }
        });
    }
    $(".search_query").on("keyup",function () {
        var search_query = $(this).val();
        if(search_query!=''){
            $.ajax({
                url: "{{route('get_suggestion')}}",
                type: "post",
                data: {_token:'{{csrf_token()}}',search_query:search_query},
                success: function (resp)
                {
                    var selector = $("#suggestion_list");
                    selector.html(resp.html);
                    if(resp.status=="success"){
                        selector.show();
                    }else{
                        selector.hide();
                    }
                }
            });
        }else{
            $("#suggestion_list").hide();
        }

    });
    $(document).on("click",".product_text",function () {
        var search_query = $(this).text();
        $(".search_query").val(search_query);
        $(".search_form").submit();
    })
</script>

 <script>
    var input = document.getElementById('address');
    var address = new google.maps.places.Autocomplete(input);
    address.addListener('place_changed', function() {
        var place = address.getPlace();
        $("#address").val(place.formatted_address);
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'address': place.formatted_address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var lat = results[0].geometry.location.lat().toString().substr(0, 12);
                var lng = results[0].geometry.location.lng().toString().substr(0, 12);
                $("#latitude").val(lat);
                $("#longitude").val(lng);
            }
            else{
                alert('error: ' + status);
            }
        });
    });
    function getLocation() {
        if (navigator.geolocation) {
             navigator.geolocation.getCurrentPosition(showPosition, showError, { enableHighAccuracy: true });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        $("#latitude").val(position.coords.latitude);
        $("#longitude").val(position.coords.longitude);
        var latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                console.log(results[0].formatted_address);
                $("#address").val(results[0].formatted_address);
                $("#location_set").submit();
            }
            else{
                alert('error: ' + status);
            }
        });
    }
    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }
</script>


// <script>
//     var input = document.getElementById('address');
//     var address = new google.maps.places.Autocomplete(input);
//     address.addListener('place_changed', function() {
//         var place = address.getPlace();
//         $("#address").val(place.formatted_address);
//         var geocoder = new google.maps.Geocoder();
//         geocoder.geocode({ 'address': place.formatted_address }, function (results, status) {
//             if (status == google.maps.GeocoderStatus.OK) {
//                 var lat = results[0].geometry.location.lat().toString().substr(0, 12);
//                 var lng = results[0].geometry.location.lng().toString().substr(0, 12);
//                 $("#latitude").val(lat);
//                 $("#longitude").val(lng);
//             }
//             else{
//                 alert('error: ' + status);
//             }
//         });
//     });
//     function getLocation() {
//         if (navigator.geolocation) {
//             navigator.geolocation.getCurrentPosition(showPosition);
//         } else {
//             alert("Geolocation is not supported by this browser.");
//         }
//     }

//     function showPosition(position) {
//         $("#latitude").val(position.coords.latitude);
//         $("#longitude").val(position.coords.longitude);
//         var latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
//         var geocoder = new google.maps.Geocoder();
//         geocoder.geocode({'location': latlng}, function(results, status) {
//             if (status == google.maps.GeocoderStatus.OK) {
//                 console.log(results[0].formatted_address);
//                 $("#address").val(results[0].formatted_address);
//                 $("#location_set").submit();
//             }
//             else{
//                 alert('error: ' + status);
//             }
//         });
//     }

<!--</script>-->







</body>
</html>


</body>
</html>






@stack('scripts')
</body>
</html>
