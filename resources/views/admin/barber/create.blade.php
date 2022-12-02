@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Add New Parlour</h4>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">Basic Parlour Information</h4>
                                <p class="card-title-desc">Fill all information below *</p>

                                <form action="{{ route('barbers.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Parlour Name *</label>
                                                <input type="text" class="form-control" name="name" value="{{ old('name')}}" required>
                                                @if ($errors->has('name'))
                                                <div class="error">
                                                    {{ $errors->first('name') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Parlour Address line 1 *</label>
                                                <input type="text" class="form-control" name="address_line_1" value="{{ old('address_line_1')}}" required>
                                                @if ($errors->has('address_line_1'))
                                                <div class="error">
                                                    {{ $errors->first('address_line_1') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Parlour Address line 2</label>
                                                <input type="text" class="form-control" name="address_line_2" value="{{ old('address_line_2')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Postal Code</label>
                                                <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code')}}">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone Number</label><br>
                                                <b>Don't add (+44)</b>
                                                <input type="tel" class="form-control" pattern="^\d{10}$" name="mobile" value="{{ old('mobile')}}" required>
                                                @if ($errors->has('mobile'))
                                                <div class="error">
                                                    {{ $errors->first('mobile') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="{{ old('email')}}" required>
                                                @if ($errors->has('email'))
                                                <div class="error">
                                                    {{ $errors->first('email') }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-group text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="male" checked>
                                                    <label class="form-check-label" for="inlineRadio1">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="female">
                                                    <label class="form-check-label" for="inlineRadio2">Female</label>
                                                </div>
                                            </div>
                                            <div class="row mt-1">
                                                <div class="col-xl-12 col-lg-12 col-md-12 mt-2">
                                                    <input type="text" id="address" name="search_address" class="form-control" placeholder="Start to search on map" value="{{ old('search_address') }}">
                                                    <div id="map" style="width:100%;height:300px"></div>
                                                    <label style="color: red;">Note : Every time when page reload please search on map</label>
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

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <label>Parlour Profile</label>
                                                    <div class="file">
                                                        <img id="profile" src="{{ asset('images/cloud.png') }}" onclick="passFileUrl('profile')" class="img-fluid img-thumbnail" role="button" style="width: 100%;height:100%;">
                                                        <input id="profile_file" name="profile" type="file" style="height: 0px;width: 0px;" onchange="fileSelected(this)" accept="image/x-png,image/gif,image/jpeg" />
                                                    </div>
                                                    @if ($errors->has('profile'))
                                                    <div class="error">
                                                        {{ $errors->first('profile') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <label>Parlour Owner Document Name *</label>
                                                    <input type="text" class="form-control" name="document_1_name" value="{{ old('document_1_name')}}">
                                                    <div class="file">
                                                        <img id="document_1" src="{{ asset('images/cloud.png') }}" onclick="passFileUrl('document_1')" class="img-fluid img-thumbnail" role="button" style="width: 100%;height:100%;">
                                                        <input id="document_1_file" name="document_1" type="file" style="height: 0px;width: 0px;" onchange="fileSelected(this)" accept="image/x-png,image/gif,image/jpeg" />
                                                    </div>
                                                    @if ($errors->has('document_1'))
                                                    <div class="error">
                                                        {{ $errors->first('document_1') }}
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6">
                                                    <label>Parlour Owner Document Name</label>
                                                    <input type="text" class="form-control" name="document_2_name" value="{{ old('document_2_name')}}">
                                                    <div class="file">
                                                        <img id="document_2" src="{{ asset('images/cloud.png') }}" onclick="passFileUrl('document_2')" class="img-fluid img-thumbnail" role="button">
                                                        <input id="document_2_file" name="document_2" type="file" style="height: 0px;width: 0px;" onchange="fileSelected(this)" accept="image/x-png,image/gif,image/jpeg" />
                                                    </div>
                                                    @if ($errors->has('document_2'))
                                                    <div class="error">
                                                        {{ $errors->first('document_2') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Latitude</label>
                                                        <input type="text" class="form-control" id="lat" name="latitude" value="{{ old('latitude') }}" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Longitude</label>
                                                        <input type="text" class="form-control" id="long" name="longitude" value="{{ old('longitude') }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Country</label>
                                                        <select id="country" name="country" class="form-control" required>
                                                            <option value="" disabled>Select Country</option>
                                                            @foreach ($countries as $key => $value)
                                                            @if (old('country') == $value->id)
                                                            <option value="{{ $value->id }}" selected>
                                                                {{ $value->name ?? '' }}
                                                            </option>
                                                            @else
                                                            <option value="{{ $value->id ?? '' }}">
                                                                {{ $value->name ?? '' }}
                                                            </option>
                                                            @endif
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('country'))
                                                        <div class="error">
                                                            {{ $errors->first('country') }}
                                                        </div>
                                                        @endif
                                                    </div>

                                                    <div class="form-group">
                                                        <label>City</label>
                                                        <select id="city" name="city" class="form-control" required>
                                                            <option value="" disabled>Select Country</option>
                                                        </select>
                                                        @if ($errors->has('city'))
                                                        <div class="error">
                                                            {{ $errors->first('city') }}
                                                        </div>
                                                        @endif
                                                    </div>

                                                    {{-- <div class="form-group">
                                                        <label>State</label>
                                                        <select id="state" name="state" class="form-control" required>
                                                            <option value="" disabled>Select State</option>
                                                            @foreach ($states as $key => $value)
                                                            @if (old('state') == $value->id)
                                                            <option value="{{ $value->id }}" selected>
                                                    {{ $value->name ?? '' }}
                                                    </option>
                                                    @else
                                                    <option value="{{ $value->id ?? '' }}">
                                                        {{ $value->name ?? '' }}
                                                    </option>
                                                    @endif
                                                    @endforeach
                                                    </select>
                                                    @if ($errors->has('state'))
                                                    <div class="error">
                                                        {{ $errors->first('state') }}
                                                    </div>
                                                    @endif
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Submit</button>
                                <!-- <button type="reset" class="btn btn-danger waves-effect">Cancel</button> -->
                                <a class="btn btn-danger waves-effect" href="{{ url()->previous() }}">
                                    Cancel
                                </a>
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
@endsection

@section('scripts')

<script>
    var globalid;

    function passFileUrl(data) {
        globalid = data;
        console.log(globalid);
        document.getElementById(data + '_file').click();
    }

    function fileSelected(inputData) {
        console.log(inputData.files[0]);
        document.getElementById(globalid).src = window.URL.createObjectURL(inputData.files[0])
    }

    $('#country').on('change', function() {
        var country_id = $(this).val();
        getCity(country_id, old_city_id = '')
    });

    function getCity(country_id, old_city_id = '') {
        var _token = `{{ csrf_token() }}`;
        if (country_id == "") {
            alert('please select country');
            return false;
        }
        $.ajax({
                headers: {
                    'x-csrf-token': _token
                },
                method: 'POST',
                url: "{{ route('ajax.city') }}",
                data: {
                    country_id: country_id,
                    old_city_id: old_city_id,
                    _method: 'POST'
                }
            })
            .done(function($result) {
                $('#city').html($result);
            })
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
        var input = document.getElementById('address');
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