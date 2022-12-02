@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-lg-12 mb-3 text-right">
                <a class="btn btn-primary" href="{{ url()->previous() }}">
                    Back
                </a>
            </div>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">
                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                    <span class="d-none d-sm-block">Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#timingSlots" role="tab">
                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                    <span class="d-none d-sm-block">Timing Slots</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#ordersell" role="tab">
                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                    <span class="d-none d-sm-block">Order</span>
                </a>
            </li>
        </ul>

        <div class="tab-content p-1 text-muted">
            <div class="tab-pane active" id="profile" role="tabpanel">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="card-title mb-3">Parlour Profile</div>
                                <div class="media">
                                    <div class="mr-3">
                                        @if(isset($barberProfile->profile))
                                        <img src="{{ asset(Storage::url($barberProfile->profile))}}" alt="Profile" class="avatar-xl rounded-lg img-fluid" style="height: 70px;width:70px;border-radius: 50px!important;">
                                        @else
                                        <img class="avatar-xl rounded-lg img-fluid" src="{{ asset(Storage::url('storage/no-image.jpg')) }}" alt="{{ $barberProfile->name ?? ''}}" style="height: 70px;width:70px;border-radius: 50px!important;">
                                        @endif
                                    </div>
                                    <div class="media-body align-self-center">
                                        <div>Parlour Name</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->name ?? '-'}}</h5>
                                        <div>Phone No.</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->mobile ?? '-'}}</h5>
                                        <div>Email</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->email ?? '-'}}</h5>
                                        <div>Address Line 1</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->address_line_1 ?? '-'}}</h5>
                                        <div>Address Line 2</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->address_line_2 ?? '-'}}</h5>
                                        <div>User Wallet</div>
                                        <h5 class="h6 font-weight-bold">&#163; {{$barberProfile->wallet->save_amount ?? ''}}</h5>
                                        <div>Gender</div>
                                        <h5 class="h6 font-weight-bold">{{$barberProfile->gender ?? '-'}}</h5>
                                        <div>State</div>
                                        <h5 class="h6 font-weight-bold">{{ isset($barberProfile->state) ?  $barberProfile->state->name : '-' }}</h5>
                                        <div>City</div>
                                        <h5 class="h6 font-weight-bold">{{ isset($barberProfile->city) ?  $barberProfile->city->name : '-' }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4 float-sm-left">Total Order</h4>
                                <div class="float-sm-right">
                                    <!-- <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Month</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Year</a>
                                </li>
                            </ul> -->
                                </div>
                                <div class="clearfix"></div>
                                <div id="stacked-column-chart_driver" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row ml-2">
                    @foreach($barberProfile->documents as $key=>$value)
                    <div class="card ml-2 mr-2" style="width: 11rem;">
                        @if(isset($value->path))
                        <img src="{{ asset(Storage::url($value->path)) ?? '' }}" class="card-img-top" alt="{{ $value->document_name ?? ''}}" width="110px" height="110px">

                        <div class="card-body text-center">
                            <h5 class="card-title">
                                <a href="{{ asset(Storage::url($value->path)) ?? ''}}" target="_blank" style="color:#495062 !important;">
                                    {{$value->document_name ?? '-'}} <i class="fas fa-id-card"></i>
                                </a>
                            </h5>
                        </div>
                        @else
                        <img src="{{ asset(Storage::url('no-image.jpg')) ?? '' }}" class="card-img-top" alt="No-Image" width="110px" height="110px">

                        <div class="card-body text-center">
                            <h5 class="card-title">
                                <a href="#" style="color:#495062 !important;">
                                    Not Available <i class="fas fa-times"></i>
                                </a>
                            </h5>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    <div class="ml-2 mr-2" style="width: 26rem;">
                        <div id="map" style="width:100%;height:90%"></div>
                        <input type="hidden" class="form-control" id="lat" name="latitude" value="{{ $barberProfile->latitude  ?? '' }}">
                        <input type="hidden" class="form-control" id="long" name="longitude" value="{{ $barberProfile->longitude  ?? '' }}">
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="timingSlots" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class=" table table-bordered table-striped table-hover datatable datatable-User1" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>
                                            No
                                        </th>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Slot Time
                                        </th>

                                        <th>
                                            Slot Status
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timingslots as $key=>$value)
                                    <tr data-entry-id="{{ $value->id }}">
                                        <td>
                                            {{ $key + 1 ?? "" }}
                                        </td>
                                        <td>
                                            {{ $value->date ?? "" }}
                                        </td>
                                        <td>
                                            {{ $value->time->time ?? "" }}
                                        </td>

                                        <td>
                                            @if($value->is_booked == 0)
                                            <span class="badge badge-pill badge-soft-success font-size-12">Open</span>
                                            @else
                                            <span class="badge badge-pill badge-soft-danger font-size-12">Booked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input switch_timing_slot" id="customSwitchForSlot{{$value->id}}" {{ ($value->is_active == 1) ? 'checked': ''}} entry-id="{{ $value->id }}">
                                                <label class="custom-control-label" for="customSwitchForSlot{{$value->id}}"></label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="ordersell" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body">
                            <!--  <h4 class="card-title mb-4">Recent Orders</h4> -->
                            <div class="row justify-content-between">
                                <h4 class="card-title mb-4">Recent Order</h4>
                                <div class="btn-group btn-group-sm tab card-title mb-4" role="group" aria-label="Order">
                                    <button type="button" onclick="click_barber_order(this,3)" class="btn btn-dark tablinks">All</button>
                                    <button type="button" onclick="click_barber_order(this,0)" class="btn btn-outline-dark tablinks">Incomplete</button>
                                    <button type="button" onclick="click_barber_order(this,1)" class="btn btn-outline-dark tablinks">Completed</button>
                                    <button type="button" onclick="click_barber_order(this,2)" class="btn btn-outline-dark tablinks">Rejected</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 datatable-User3">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 20px;">
                                                No
                                            </th>
                                            <th data-visible="false">
                                                Order_Status_Id
                                            </th>
                                            <th>Order ID</th>
                                            <th>Customer Name / Mobile</th>
                                            <th>Parlour Name / Mobile</th>
                                            <th>Order Date</th>
                                            <th>Payment</th>
                                            <th>Offer</th>
                                            <th>Stripe Fee</th>
                                            <th>Admin Commission</th>
                                            <th>Parlour Amount</th>
                                            <th>Payment Method</th>
                                            <th>Order Status</th>
                                            <th>View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($total_orders as $key=>$single_order)
                                        <tr>
                                            <td data-entry-id="{{$single_order->id ?? ''}}">
                                                {{ $key + 1 }}
                                            </td>
                                            <td data-visible="false">
                                                {{ $single_order->is_order_complete ?? ''}}
                                            </td>
                                            <td><a href="javascript: void(0);" class="text-body font-weight-bold">#{{ $single_order->id ?? ''}}</a> </td>
                                            <td>{{ $single_order->user->name ?? '-' }} / {{ $single_order->user->mobile ?? '' }}</td>
                                            <td>{{ $single_order->barber->name ?? '-' }} / {{ $single_order->barber->mobile ?? '' }}</td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($single_order->created_at ?? '')->format('d M Y')}}
                                            </td>

                                            <td>
                                                £ {{ $single_order->amount ?? '' }}
                                            </td>
                                            <td>
                                                {{ $single_order->discount }} %
                                            </td>
                                            <td>
                                                £ {{ $single_order->stripe_fee ?? '' }}
                                            </td>
                                            <td>
                                                £ {{ $single_order->admin_fee ?? '' }}
                                            </td>
                                            <td>
                                                £ {{ $single_order->barber_amount ?? '' }}
                                            </td>

                                            {{-- <td>
                                                @foreach($single_order->service_timings as $ke1=>$singleService)
                                                <span class="badge badge-pill badge-soft-primary font-size-12 mr-1"> {{ $singleService->service->name ?? '' }}</span>
                                            @endforeach
                                            </td>
                                            <td>
                                                @foreach($single_order->service_timings as $ke=>$singleTime)
                                                <span class="badge badge-pill badge-secondary font-size-12 mr-1"> {{ $singleTime->slot->time->time ?? '' }}</span>
                                                @endforeach
                                            </td> --}}
                                            <td>
                                                @if(isset($single_order->stripe_key))
                                                <i class="fab fa-cc-stripe"></i> &nbsp; <span class="badge badge-pill badge-soft-success font-size-12">{{ $single_order->transaction_number ?? ''}}</span>
                                                @else
                                                <span class="badge badge-pill badge-soft-danger font-size-12">By Wallet</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($single_order->is_order_complete == 0)
                                                <span class="badge badge-pill badge-soft-danger font-size-12">Incomplete</span>
                                                @elseif($single_order->is_order_complete == 1)
                                                <span class="badge badge-pill badge-soft-success font-size-12">Completed</span>
                                                @else
                                                <span class="badge badge-pill badge-soft-danger font-size-12">Rejected</span>
                                                @endif
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm btn-rounded waves-effect waves-light" id="orderModal" data-toggle="modal" data-target=".exampleModal" data-entry-id="{{$single_order->id ?? ''}}">
                                                    View Details
                                                </button>
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end modal -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var total_completed_orders = <?php echo $count_completed_orders; ?>;
    var total_cancel_orders = <?php echo $count_cancel; ?>;

    var options = {
            chart: {
                height: 300,
                type: "bar",
                stacked: !0,
                toolbar: {
                    show: !1
                },
                zoom: {
                    enabled: !0
                }
            },
            plotOptions: {
                bar: {
                    horizontal: !1,
                    columnWidth: "15%",
                    endingShape: "rounded"
                }
            },
            dataLabels: {
                enabled: !1
            },
            series: [{
                name: "Completed : " + total_completed_orders,
                data: [<?php echo $completed_order_data; ?>]
            }, {
                name: "Cancel : " + total_cancel_orders,
                data: [<?php echo $cancel_order_data; ?>]
            }],
            xaxis: {
                categories: [<?php echo $completed_month_data; ?>]
            },
            colors: ["#000", "#F1B44C"],
            legend: {
                position: "bottom"
            },
            fill: {
                opacity: 1
            }
        },
        chart = new ApexCharts(document.querySelector("#stacked-column-chart_driver"), options);
    chart.render();

    $('body').on('click', '#orderModal', function() {
        $('#orderModal').modal({
            show: false
        });
        var order_id = $(this).attr('data-entry-id');
        // console.log(order_id);
        if (order_id == "") {
            return false;
        }

        $.ajax({
                headers: {
                    'x-csrf-token': _token
                },
                method: 'POST',
                url: "{{ route('home.ajaxOrderModal') }}",
                data: {
                    order_id: order_id,
                    _method: 'POST'
                }
            })
            .done(function(data) {
                // console.log(data);
                $(".modal-body").html(data);
                $('#orderModal').modal({
                    show: true
                });
            })
    });

    $(".btn-group[role='group'] button").on('click', function() {
        $(this).siblings().removeClass('btn-dark');
        $(this).siblings().addClass('btn-outline-dark');
        $(this).removeClass('btn-outline-dark');
        $(this).addClass('btn-dark');
    });

    var table = $('.datatable-User1').DataTable({
        responsive: {
            details: true
        },
    });

    var table3 = $('.datatable-User3').DataTable({
        responsive: {
            details: true
        },
        /*  dom: 'Bfrtip',
         buttons: [{
                 extend: 'copy',
                 className: 'btn btn-outline-dark glyphicon glyphicon-duplicate'
             },
             {
                 extend: 'csv',
                 className: 'btn btn-outline-dark glyphicon glyphicon-save-file'
             },
             {
                 extend: 'excel',
                 className: 'btn btn-outline-dark glyphicon glyphicon-list-alt'
             },
             {
                 extend: 'pdf',
                 className: 'btn btn-outline-dark glyphicon glyphicon-file'
             },
             {
                 extend: 'print',
                 className: 'btn btn-outline-dark glyphicon glyphicon-print'
             }
         ] */
    });

    function click_barber_order(e, id) {
        if (id == 3) {
            table3.search('').columns().search('').draw();
        } else {
            table3.columns(1).search(id).draw();
        }
    };

    var _token = $('meta[name="csrf-token"]').attr('content');
    $('body').on('click', '.switch_timing_slot', function() {
        var ids = $(this).attr('entry-id');
        jQuery('.main-loader').show();
        $.ajax({
                headers: {
                    'x-csrf-token': _token
                },
                method: 'POST',
                url: "{{ route('slot.switchUpdate') }}",
                data: {
                    ids: ids,
                    _method: 'POST'
                }
            })
            .done(function() {
                jQuery('.main-loader').hide();
            })
    });

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
            zoom: 10,
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