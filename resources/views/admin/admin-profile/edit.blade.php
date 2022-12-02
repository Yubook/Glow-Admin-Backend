@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Update Admin Profile
            </div>

            <div class="card-body">
                <form id="update_admin" action="{{ route('adminProfile.update',$admin->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-2">
                            @if(isset($admin->profile))
                            <img id="asgnmnt_file_img" src="{{ asset(Storage::url($admin->profile)) }}" onclick="passFileUrl()" class="img-fluid img-thumbnail" role="button">
                            @else
                            <img id="asgnmnt_file_img" src="{{ asset('/assets/images/default-avatar-male.png')  }}" onclick="passFileUrl()" class="img-fluid img-thumbnail" role="button">
                            @endif
                            <input type="file" name="image" id="asgnmnt_file" style="height: 0px;width: 0px;" onchange="fileSelected(this)" accept="image/x-png,image/gif,image/jpeg">
                            @if ($errors->has('image'))
                            <div class="error">
                                {{ $errors->first('image') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Full Name*</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', isset($admin) ? $admin->name : '') }}" required>
                                    @if ($errors->has('name'))
                                    <div class="error">
                                        {{ $errors->first('name') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mobile*</label>
                                    <input type="tel" name="mobile" pattern="^\d{10}$" class="form-control" value="{{ old('mobile', isset($admin) ? $admin->mobile : '') }}" required>
                                    @if ($errors->has('mobile'))
                                    <div class="error">
                                        {{ $errors->first('mobile') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Email*</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', isset($admin) ? $admin->email : '') }}" required>
                                    @if ($errors->has('email'))
                                    <div class="error">
                                        {{ $errors->first('email') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" value="">
                                    @if ($errors->has('password'))
                                    <div class="error">
                                        {{ $errors->first('password') }}
                                    </div>
                                    @endif

                                </div>

                                <div class="form-group col-md-6">
                                    <label>Confirm Password</label>
                                    <input type="password" name="password__confirmation" class="form-control" value="">
                                    @if ($errors->has('confirm_password'))
                                    <div class="error">
                                        {{ $errors->first('confirm_password') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" cols="10" rows="2" required>{{ old('address', isset($admin->address_line_1) ? $admin->address_line_1 : '')}}</textarea>
                                    @if ($errors->has('address'))
                                    <div class="error">
                                        {{ $errors->first('address') }}
                                    </div>
                                    @endif

                                </div>

                                <div class="form-group col-md-12">
                                    <label>Location*</label>
                                    <input type="text" id="address_find" class="form-control" placeholder="Search on map" value="">

                                    <br>
                                    <div id="map" style="width:100%;height:200px"></div>
                                    @if ($errors->has('latitude'))
                                    <div class="error">
                                        {{ $errors->first('latitude') }}
                                    </div>
                                    @endif
                                    @if ($errors->has('longitude'))
                                    <div class="error">
                                        {{ $errors->first('longitude') }}
                                    </div>
                                    @endif
                                </div>
                                <input type="hidden" class="form-control" id="lat" name="latitude" value="{{isset($admin) ? $admin->latitude : ''}}">
                                <input type="hidden" class="form-control" id="long" name="longitude" value="{{isset($admin) ? $admin->longitude : ''}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Submit</button>
                            <a class="btn btn-danger waves-effect" href="{{ url()->previous() }}">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
    function passFileUrl() {
        document.getElementById('asgnmnt_file').click();
    }

    function fileSelected(inputData) {
        document.getElementById('asgnmnt_file_img').src = window.URL.createObjectURL(inputData.files[0])
    }

    function initAutocomplete() {
        //var latLng = new google.maps.LatLng(23.01802864089624, 72.57282361409486);
        var long = $('#long').val();
        var lat = $('#lat').val();
        if (long != "") {
            long = parseFloat(long);
        } else {
            long = -1.635178;
        }

        if (lat != "") {
            lat = parseFloat(lat);
        } else {
            lat = 52.943487;
        }
        var latLng = new google.maps.LatLng(lat, long);
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: lat,
                lng: long,
            },
            zoom: 07,
            mapTypeId: 'roadmap'
        });

        var marker = new google.maps.Marker({
            position: latLng,
            title: 'Marker',
            map: map,
            draggable: true
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('address_find');
        var searchBox = new google.maps.places.SearchBox(input);
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker) {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                var marker = new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                });
                $('#lat').val(marker.position.lat());
                $('#long').val(marker.position.lng());
                markers.push(marker);

                //Click on map to make red marker and get lat and long
                google.maps.event.addListener(map, 'click', function(event) {

                    // remove previous markers and add new one by click on map
                    if (marker && marker.setMap) {
                        marker.setMap(null);
                    }
                    var ll = event.latLng;
                    map.panTo(ll); // map center on click
                    map.setZoom(08);
                    marker = new google.maps.Marker({
                        position: event.latLng,
                        map: map
                    });
                    $('#lat').val(event.latLng.lat());
                    $('#long').val(event.latLng.lng());
                    markers.push(marker);
                });
                console.log(place.geometry.location);
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7kiVlXV3vw-HjT_9UmKEoJ5su1KXJrBA&libraries=places&callback=initAutocomplete" async defer></script>
@endsection