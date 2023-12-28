@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">

        <!-- begin page-header -->
        <h1 class="page-header">Add Manager</h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-6 -->
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                        </div>
                        <h4 class="panel-title">Manager</h4>
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
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{route('admin::saveManager')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-md-3 control-label">Manager Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="user_name" placeholder="Enter Your Name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Manager Email</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="email" placeholder="Enter Your Email" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Manager Phone</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="phone" placeholder="Enter Your Phone" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Manager Location</label>
                                <div class="col-md-6">
                                    <input type="text" id="searchInput" class="form-control" name="location" placeholder="Enter Your Location" />
                                    <input type="hidden" id="lat" name="latitude"/>
                                    <input type="hidden" id="lon" name="longitude"/>
                                </div>
                            </div>
                            <div id="map"></div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-6 -->
        </div>
    </div>
    @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&libraries=places&callback=initMap"
                async defer>
        </script>
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: -33.8688, lng: 151.2195},
                    zoom: 13
                });
                var input = document.getElementById('searchInput');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });

                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    marker.setIcon(({
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(35, 35)
                    }));
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                    infowindow.open(map, marker);

                    //Location details

                    document.getElementById('lat').value = place.geometry.location.lat();
                    document.getElementById('lon').value = place.geometry.location.lng();
                });
            }
        </script>
    @endpush
@endsection
