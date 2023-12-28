@extends('frontend.layouts.frontendlayout')
@section('title')
<title>Mandi Ghar : My Account</title>
@endsection
@section('content')
<section id="my-account">
    <div class="container">
        <div class="head-line">
            <h3>My Account</h3>
        </div>
        <div class="account-info">
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#profile">Update Profile <span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a></li>
                        <li><a data-toggle="tab" href="#pwd-change">Change Password <span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a></li>
                        <li><a data-toggle="tab" href="#wallet">My Wallet <span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
                        </li>
                        <li><a data-toggle="tab" href="#shipping_address">Shipping Address <span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a></li>
                        <li><a data-toggle="tab" href="#my-order">My Orders <span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
                        </li>
                        <li><a href="{{route('user_logout')}}" class="active">Logout<span><i class="fa fa-angle-right" aria-hidden="true"></i></span></a></li>
                    </ul>
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="tab-content">
                        <div id="profile" class="tab-pane show fade in active">
                            <div class="profile-area">
                                <h4>Update Profile</h4>
                                <div id="msg"></div>
                                <form id="profile_update">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label>User Name
                                            <input type="text" name="user_name" value="{{$user_details["user_name"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone No.
                                            <input type="text" name="phone" value="{{$user_details["phone"]}}" readonly class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Email
                                            <input type="email" name="email" value="{{$user_details["email"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Address
                                            <input type="text" name="location" id="location" value="{{$user_details["location"]}}" autocomplete="off" class="form-control">
                                            <input type="hidden" name="latitude" id="latitude1" value="{{$user_details["latitude"]}}" class="form-control">
                                            <input type="hidden" name="longitude" id="longitude1" value="{{$user_details["longitude"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div id="map1"></div>
                                    <button type="submit" class="btn btn-danger">Update</button>
                                </form>
                            </div>
                        </div>
                        <div id="pwd-change" class="tab-pane fade">
                            <div class="profile-area">
                                <h4>Change Password</h4>
                                <div id="msg1"></div>
                                <form id="update_password">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label>Old Password
                                            <input type="password" name="old_password" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>New Password
                                            <input type="password" name="new_password" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm New Password
                                            <input type="text" name="cnf_password" class="form-control">
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Update</button>
                                </form>
                            </div>
                        </div>
                        <div id="wallet" class="tab-pane fade">
                            <form action="{{ route('pay_to_wallet')}}" method="POST" enctype="multipart/form-data" id="add_wallet_amount_form">
                                {!! csrf_field() !!}
                                <div class="profile-area">
                                    <h4>My Wallet (Rs.{{$user_details["wallet_amount"]}})</h4>
                                    <div id="wallet_msg"></div>
                                    <div class="form-group">
                                        <label>Add Amount
                                            <input type="text" id="amount" name="amount" class="form-control">
                                        </label>
                                    </div>
                                    <img id="loader" style="display: none;" src="{{asset('/')}}frontendtheme/images/ajax-loader.gif" height="60px" width="60px" ;>
                                    <button type="submit" id="add_wallet_amount" class="btn btn-danger">Add Amount</button>
                                </div>
                            </form>
                        </div>
                        <div id="shipping_address" class="tab-pane show fade">
                            <div class="profile-area">
                                <h4>Shipping Address</h4>
                                <div id="shipping_msg"></div>
                                <form id="shipping_update">
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <label>Name
                                            <input type="text" name="name" value="{{$shipping_details["name"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Email
                                            <input type="email" name="email" value="{{$shipping_details["email"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone No.
                                            <input type="text" name="phone_no" value="{{$shipping_details["phone_no"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Address
                                            <input type="text" name="address" value="{{$shipping_details["address"]}}" id="searchInput" autocomplete="off" class="form-control">
                                            <input type="hidden" name="latitude" id="latitude" value="{{$shipping_details["latitude"]}}" class="form-control">
                                            <input type="hidden" name="longitude" id="longitude" value="{{$shipping_details["longitude"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div id="map"></div>
                                    <input type="button" onclick="getLocation();" value="Use My Location" />
                                    <div class="form-group">
                                        <label>Pincode
                                            <input type="text" name="pincode" value="{{$shipping_details["pincode"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Landmark
                                            <input type="text" name="landmark" value="{{$shipping_details["landmark"]}}" class="form-control">
                                        </label>
                                    </div>
                                    <button type="submit" class="btn btn-danger">Update</button>
                                </form>
                            </div>
                        </div>
                        <div id="my-order" class="tab-pane show fade">
                            <div class="profile-area">
                                <h4>My Orders</h4>
                                <div class="all-order">
                                    @for($i=0;$i<count($data);$i++) <ul class="profile-details">
                                        <li class="b-line">Order Id:
                                            <span>{{$data[$i]["order_details"]["order_id"]}}</span>
                                        </li>
                                        <li class="s-line">Date: {{$data[$i]["order_details"]["datetime"]}}</li>
                                        @foreach($data[$i]["item_details"] as $row)
                                        <li class="s-line">{{$row->product_name}}
                                            - {{$row->qty}}{{$row->unit}}
                                            <span>Rs: {{$row->gross_price}}</span>
                                        </li>
                                        @endforeach
                                        <li class="b-line">Payment done
                                            by:<span>{{$data[$i]["payment_details"]["payment_method"]}}</span>
                                        </li>
                                        </ul>
                                        @endfor
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="clearfix"></div>
@push('scripts')
@if(env('PAYTM_ENVIRONMENT')=='production')
<script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID') ?>.js"></script>
@else
<script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID') ?>.js"></script>
@endif
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $("#profile_update").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('user::update_profile')}}",
            type: "post",
            dataType: "JSON",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data) {
                $("#msg").html('<p style="color: #f1440e;">' + data.msg + '</p>');
            }
        });
    });
    $("#shipping_update").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('user::update_shipping')}}",
            type: "post",
            dataType: "JSON",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data) {
                $("#shipping_msg").html('<p style="color: #f1440e;">' + data.msg + '</p>');
            }
        });
    });
    $("#update_password").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('user::update_password')}}",
            type: "post",
            dataType: "JSON",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data) {
                $("#msg1").html('<p style="color: #f1440e;">' + data.msg + '</p>');
                if (data.status === "success") {
                    $("#update_password").find('input').val('');
                    console.log('success');
                }
            }
        });
    });
    
    $("#add_wallet_amount").click(function(e) {
        e.preventDefault();
        var amount = $("#amount").val();
        if (amount != "" && amount > 0) {
            $("#add_wallet_amount_form").submit();
        } else {
            $("#wallet_msg").html('<p style="color: #f1440e;">Please enter amount</p>');
        }
    });

    $(function() {
        $("#all-order").scroll(function() {
            if ($("#all-order").height() == $("#all-order").scrollTop() + $("#all-order").height()) {
                alert('I am at the bottom');
            }
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&libraries=places&callback=initMap" async defer></script>
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'));
        var input = document.getElementById('searchInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });

        var map1 = new google.maps.Map(document.getElementById('map1'));
        var input1 = document.getElementById('location');
        map1.controls[google.maps.ControlPosition.TOP_LEFT].push(input1);
        var autocomplete1 = new google.maps.places.Autocomplete(input1);
        autocomplete1.bindTo('bounds', map1);
        autocomplete1.addListener('place_changed', function() {
            var place1 = autocomplete1.getPlace();
            if (!place1.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            if (place1.geometry.viewport) {
                map1.fitBounds(place1.geometry.viewport);
            } else {
                map.setCenter(place1.geometry.location);
                map1.setZoom(17);
            }
            document.getElementById('latitude1').value = place1.geometry.location.lat();
            document.getElementById('longitude1').value = place1.geometry.location.lng();
        });
    }

    function getLocation() {
        if (navigator.geolocation) {
            var options = {
                timeout: 60000
            };
            navigator.geolocation.getCurrentPosition(showLocation, errorHandler, options);
        } else {
            alert("Sorry, browser does not support geolocation!");
        }
    }

    function showLocation(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        document.getElementById('latitude').value = latitude;
        document.getElementById('longitude').value = longitude;
        var geocoder = new google.maps.Geocoder;
        var infowindow = new google.maps.InfoWindow;
        geocodeLatLng(geocoder, infowindow, latitude, longitude);
    }

    function errorHandler(err) {
        if (err.code == 1) {
            alert("Error: Access is denied due to ssl not available.!");
        } else if (err.code == 2) {
            alert("Error: Position is unavailable!");
        }
    }

    function geocodeLatLng(geocoder, infowindow, latitude, longitude) {
        var latlng = {
            lat: latitude,
            lng: longitude
        };
        geocoder.geocode({
            'location': latlng
        }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    $("#searchInput").val(results[0].formatted_address);
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    }
</script>
<script>
    @if(Session::has('success_status'))
    toastr["success"]("{{ Session::get('success_status') }}");
    @endif
    @if(Session::has('error_status'))
    toastr["error"]("{{ Session::get('error_status') }}");
    @endif
</script>
@endpush
@endsection