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
    <link href="https://fonts.googleapis.com/css?family=Hind+Siliguri:300,400,500,600,700" rel="stylesheet">
    <!-- custom style css -->
    <link href="{{asset('/')}}frontendtheme/css/style.css" rel="stylesheet">
    <!-- responsive css -->
    <link href="{{asset('/')}}frontendtheme/css/responsive.css" rel="stylesheet">
    <link href="{{asset('/')}}frontendtheme/css/plugins/nouislider.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <!-- modernizr js -->
    <script src="{{asset('/')}}frontendtheme/js/plugins/modernizr.js"></script>
    <style>
        input {
            height: 30px;
            padding-left: 10px;
            border-radius: 4px;
            border: 1px solid rgb(186, 178, 178);
            box-shadow: 0px 0px 12px #EFEFEF;
        }
    </style>
</head>
<body>
<!-- loader area -->
<div id="loading-2">
    <div class="location-layer">
        <div class="logo-area">
            <a href="{{url('/')}}"><img src="{{asset('/')}}frontendtheme/images/logo.png" style="height: 130px;width: 180px;" alt=""></a>
        </div>
        <div class="location-info">
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
                <p>Where do you want Delivery?</p>
                <div class="button-area">
                    <a href="javascript:void(0);" onclick="getLocation();" class="btn">Use Current Location via GPS</a>
                </div>
                <p>OR</p>
                <div class="button-area">
                    <a class="btn set">Set location manually</a>
                </div>
                <div class="form-group">
                     <input type="text" name="address" placeholder="Enter location..." id="address" class="form-control">
                    <div class="button-area2">
                        <button type="submit" class="submit-location">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- loader area -->
<!-- jquery -->
<script src="{{asset('/')}}frontendtheme/js/plugins/jquery-3.4.1.min.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&libraries=places"></script>
<script>
    // home page location
    $(".location-layer .location-info .button-area .set").click(function(){
        $(".location-layer .location-info .form-group").addClass("active");
    });

    $(".location-layer .location-info .submit-location").click(function(){
        $(".location-layer .location-info .form-group").removeClass("active");
    });
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

// </script>
</body>
</html>
