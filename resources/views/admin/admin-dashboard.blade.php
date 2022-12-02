@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <!-- Start right Content here -->
        <!-- ============================================================== -->

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <!--  <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li> -->
                            <li class="breadcrumb-item active">Dashboard</li>

                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-4">
                <div class="card overflow-hidden">
                    <div class="bg-black">
                        <div class="row">
                            <div class="col-7">
                                <div class="text-primary p-3">
                                    <h5 class="text-light">Welcome Back !</h5>
                                    <p class="text-light">Glow Dashboard</p>
                                </div>
                            </div>
                            <div class="col-5 align-self-end">
                                <img src="{{asset('images/profile-img.png')}}" alt="image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="avatar-md profile-user-wid mb-4">
                                    <img src="{{ asset(Storage::url(Auth::user()->profile)) ?? '' }}" alt="admin" class="img-thumbnail rounded-circle">
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <h5 class="font-size-15 text-truncate">{{ Auth::user()->name ?? ''}}</h5>
                                <p class="text-muted mb-0 text-truncate">Admin</p>
                            </div>
                            <div class="col-sm-4">
                                <h5 class="font-size-15">{{ Auth::user()->email ?? ''}}</h5>
                                <p class="text-muted mb-0">Email</p>
                            </div>
                            <div class="col-sm-4">
                                <h5 class="font-size-15">{{ Auth::user()->mobile ?? ''}}</h5>
                                <p class="text-muted mb-0">Contact</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">

                        <div id="demo" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @if(isset($get_barbers))
                                @foreach($get_barbers as $key=>$barber)
                                @if($key == 0)
                                <div class="carousel-item active">
                                    @else
                                    <div class="carousel-item">
                                        @endif
                                        <div id="barber{{$barber['id'] ?? ''}}">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h3>Barber {{$key ?? ''}}</h3>
                                                    <h4 class="card-title mb-4">Mobile Parlour</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-7">
                                                    @if(!isset($barber['profile']))
                                                    <a href="#" style="height: 120px;display: block;overflow: hidden;" class="mb-5">
                                                        <img class="img-fluid img-responsive" style="height: 100%;object-fit: contain;" src="{{asset('no-image.jpg') ?? ''}}" alt="{{ $barber['name'] ?? ''}}" />
                                                    </a>
                                                    @else
                                                    <a href="#" style="height: 120px;display: block;overflow: hidden;" class="mb-5">
                                                        <img class="img-fluid img-responsive" style="height: 100%;object-fit: contain;" src="{{asset(Storage::url($barber['profile'])) ?? ''}}" alt="{{ $barber['name'] ?? ''}}" />
                                                    </a>
                                                    @endif
                                                    <h4 class="card-title">Parlour Name : {{$barber['name'] ?? ''}}</h4>
                                                    <h4 class="card-title">Parlour Mobile : {{$barber['mobile'] ?? ''}}</h4>
                                                </div>
                                                <div class="col-sm-5 text-center">
                                                    <div id="radialBar-chart_{{$barber['id']}}" class="apex-charts"></div>
                                                    <h4 class="card-title">${{$barber['Revenue'] ?? '0'}}</h4>
                                                    <h4 class="card-title">Total Revenue</h4>

                                                    <button type="button" onclick="onClickVan()" class="btn btn-primary waves-effect waves-light btn-sm mt-2">Next
                                                        Parlour</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Orders</p>
                                            <h4 class="mb-0">{{$total_orders_all ?? ''}}</h4>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle">
                                                <i class="bx bx-copy-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Customer</p>
                                            <h4 class="mb-0">{{$users ?? ''}}</h4>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle">
                                                <i class='bx bxs-user-detail font-size-24'></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Parlours</p>
                                            <h4 class="mb-0">{{ $barbers ?? ''}}</h4>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle">
                                                <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Revenue</p>
                                            <h4 class="mb-0">£ {{$total_revenue ?? ''}}</h4>
                                        </div>

                                        <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                                            <span class="avatar-title rounded-circle">
                                                <i class="bx bx-archive-in font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Admin Revenue</p>
                                            <h4 class="mb-0">£ {{$admin_revenue ?? ''}}</h4>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle">
                                                <i class="bx bx-money font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="media">
                                        <div class="media-body">
                                            <p class="text-muted font-weight-medium">Parlour Revenue</p>
                                            <h4 class="mb-0">£ {{$barber_revenue ?? ''}}</h4>
                                        </div>

                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                            <span class="avatar-title rounded-circle">
                                                <i class="bx bx-money font-size-24"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

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
                            <div id="stacked-column-chart_all" class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <h4 class="card-title mb-4">New Parlour Request</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 datatable-User3">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 20px;">No</th>
                                            <th>Parlour Name</th>
                                            <th>State / City</th>
                                            <th>Time</th>
                                            <th>View Details</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($new_barber_requests as $key=>$single_barber)
                                        <tr>
                                            <td data-entry-id="{{$single_barber->id ?? ''}}">
                                                {{ $key + 1 }}
                                            </td>
                                            <td>{{ $single_barber->name ?? '-' }}</td>
                                            <td>{{ $single_barber->state->name ?? '-' }} / {{ $single_barber->city->name ?? '-' }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($single_barber->created_at ?? '')->format('d M Y')}}
                                            </td>
                                            <td>
                                                <a href="{{route('barbers.show',['barber'=>$single_barber->id]) }}" target="_blank"><span class="badge badge-pill badge-soft-danger font-size-12">Details</span></a>
                                            </td>
                                            <td> <button type="button" data-toggle="toolip" title="Approve" data-type="newProfile" class="btn btn-sm taskActionBtnBarber" url="{{route('requestNewBarbers.approvedOrReject')}}" entry-id=" {{$single_barber->id}}" entry-value='1'><i class="fa fa-check font-size-16 text-success"></i></button>
                                                <button type="button" data-toggle="toolip" title="Reject" data-type="newProfile" class="btn btn-sm taskActionBtnBarber" url="{{route('requestNewBarbers.approvedOrReject')}}" entry-id=" {{$single_barber->id}}" entry-value='0'><i class="fa fa-times font-size-16 text-danger"></i></button>
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

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <h4 class="card-title mb-4">Rejected Parlour Request</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 datatable-User4">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 20px;">No</th>
                                            <th>Parlour Name</th>
                                            <th>State / City</th>
                                            <th>Time</th>
                                            <th>View Details</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rejected_barber_requests as $key=>$single_barber)
                                        <tr>
                                            <td data-entry-id="{{$single_barber->id ?? ''}}">
                                                {{ $key + 1 }}
                                            </td>
                                            <td>{{ $single_barber->name ?? '-' }}</td>
                                            <td>{{ $single_barber->state->name ?? '-' }} / {{ $single_barber->city->name ?? '-' }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($single_barber->created_at ?? '')->format('d M Y')}}
                                            </td>
                                            <td>
                                                <a href="{{route('barbers.show',['barber'=>$single_barber->id]) }}" target="_blank"><span class="badge badge-pill badge-soft-danger font-size-12">Details</span></a>
                                            </td>
                                            <td> <button type="button" data-toggle="toolip" title="Approve" data-type="rejectedProfile" class="btn btn-sm taskActionBtnBarberOld" url="{{route('requestNewBarbers.approvedOrReject')}}" entry-id=" {{$single_barber->id}}" entry-value='1'><i class="fa fa-check font-size-18 text-success"></i></button>
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


            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!--  <h4 class="card-title mb-4">Recent Orders</h4> -->
                            <div class="row justify-content-between">
                                <h4 class="card-title mb-4">Recent Order</h4>
                                <div class="btn-group btn-group-sm tab card-title mb-4" role="group" aria-label="Order">
                                    <button type="button" onclick="click_order(this,3)" class="btn btn-dark tablinks">All</button>
                                    <button type="button" onclick="click_order(this,0)" class="btn btn-outline-dark tablinks">Incomplete</button>
                                    <button type="button" onclick="click_order(this,1)" class="btn btn-outline-dark tablinks">Completed</button>
                                    <button type="button" onclick="click_order(this,2)" class="btn btn-outline-dark tablinks">Rejected</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 datatable-User2">
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
                                            <th>Discount</th>
                                            <th>Payment Method</th>
                                            <th>Order Status</th>
                                            <th>View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($all_orders as $key=>$single_order)
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
                                                {{ isset($single_order->discount) ? $single_order->discount : '0'}}%
                                            </td>
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
            <!-- end row -->

            <!-- container-fluid -->

            <!-- End Page-content -->

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

            <!-- end main content-->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <h4 class="card-title mb-4">All User</h4>
                                <div class="btn-group btn-group-sm tab card-title mb-4" role="group" aria-label="Basic example">
                                    <button type="button" onclick="click_table(this,1)" class="btn btn-dark tablinks">All</button>
                                    <button type="button" onclick="click_table(this,2)" class="btn btn-outline-dark tablinks">Parlour</button>
                                    <button type="button" onclick="click_table(this,3)" class="btn btn-outline-dark tablinks">User</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0 datatable-User1">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>
                                                No.
                                            </th>
                                            <th data-visible="false">
                                                Role_id
                                            </th>
                                            <th>
                                                Role
                                            </th>

                                            <th>
                                                User Name
                                            </th>
                                            <th>
                                                Joining Date
                                            </th>
                                            <th>
                                                Address
                                            </th>
                                            <th>
                                                Email
                                            </th>
                                            <th>
                                                Mobile
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($all_users as $key=>$user)
                                        <tr data-entry-id="{{$user->id}}">
                                            <td>
                                                {{ $key + 1}}
                                            </td>
                                            <td data-visible="false">
                                                {{$user->role_id}}
                                            </td>
                                            <td>
                                                @if($user->role_id == 2)
                                                <span class="badge badge-pill badge-soft-success font-size-12">Barber</span>
                                                @elseif($user->role_id == 3)
                                                <span class="badge badge-pill badge-soft-success font-size-12">User</span>
                                                @else
                                                <span class="badge badge-pill badge-soft-success font-size-12">Admin</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $user->name ?? ''}}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($user->created_at)) }}
                                            </td>
                                            <td>
                                                {{ $user->address_line_1 ?? ''}}
                                            </td>
                                            <td>
                                                {{ $user->email ?? ''}}
                                            </td>
                                            <td>
                                                {{ $user->mobile ?? ''}}
                                            </td>
                                            <td>
                                                @if($user->is_active == 1)
                                                <span class="badge badge-pill badge-soft-success font-size-12">Active</span>
                                                @else
                                                <span class="badge badge-pill badge-soft-danger font-size-12">Deactive</span>
                                                @endif
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
            <!-- end row -->
        </div>
        <!-- container-fluid -->

        <!-- Live Map Location -->
        <div class="row" id="world_locations">
            <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
                <div class="card flex-fill w-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Real-Time Locations <i class="fas fa-sync" style="float:right;cursor:pointer;" onclick="reload();"></i></h5>

                    </div>
                    <div class="card-body px-1 py-1">
                        <!-- <div id="world_map" style="height:350px;"></div> -->
                        <div id="map_div" style="width: 100%; height: 300px"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Live Map Location -->

    </div>
</div>
<!-- End Page-content -->
@endsection

@section('scripts')

<script type="text/javascript">
    var _token = $('meta[name="csrf-token"]').attr('content');

    var ids = @json($get_barbers);

    ids.forEach((item, index) => {
        var progressbar = (parseInt(item.completed_orders) * 100) / parseInt(item.total_orders);
        if (isNaN(progressbar)) {
            progressbar = 0;
        }

        var options = {
            series: [progressbar.toFixed()],
            labels: ['Progress'],
            fill: {
                colors: ['#000000']
            },
            chart: {
                height: 170,
                type: 'radialBar',
                offsetY: -10
            },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                }
            },
            stroke: {
                dashArray: 4
            },
        };

        var chart = new ApexCharts(document.querySelector("#radialBar-chart_" + item.id), options);
        chart.render();
    });

    var total_completed_orders = <?php echo $count_completed_orders; ?>;
    var total_cancelled_orders = <?php echo $count_cancelled; ?>;

    var options = {
            chart: {
                height: 420,
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
                name: "Cancelled : " + total_cancelled_orders,
                data: [<?php echo $cancelled_order_data; ?>]
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
        chart = new ApexCharts(document.querySelector("#stacked-column-chart_all"), options);
    chart.render();


    var table1 = $('.datatable-User1').DataTable({
        responsive: {
            details: true
        }
    });

    $(".btn-group[role='group'] button").on('click', function() {
        $(this).siblings().removeClass('btn-dark');
        $(this).siblings().addClass('btn-outline-dark');
        $(this).removeClass('btn-outline-dark');
        $(this).addClass('btn-dark');
    })

    function click_table(e, id) {
        if (id == 1) {
            table1.search('').columns().search('').draw();
        } else {
            table1.columns(1).search(id).draw();
        }
    };

    var table2 = $('.datatable-User2').DataTable({
        responsive: {
            details: true
        }
    });

    function click_order(e, id) {
        if (id == 3) {
            table2.search('').columns().search('').draw();
        } else {
            table2.columns(1).search(id).draw();
        }
    };

    var table3 = $('.datatable-User3').DataTable();
    var table4 = $('.datatable-User4').DataTable();
    $('body').on('click', '.taskActionBtnBarber,.taskActionBtnBarberOld', function(e) {
        e.preventDefault();
        var current = $(this);
        var id = $(this).attr('entry-id');
        var table_type = $(this).attr('data-type');
        var url = $(this).attr('url')
        var type = $(this).attr('entry-value');
        if (type == 1) {
            var title = "Are you sure approve this parlour?";
        } else {
            var title = "Are you sure reject this parlour?";
        }
        Swal.fire({
            title: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#125c4f",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, change it!",
        }).then((result) => {
            if (result.value) {
                $.ajax({
                        headers: {
                            'x-csrf-token': _token
                        },
                        method: 'POST',
                        url: url,
                        data: {
                            id: id,
                            type: type,
                            _method: 'POST'
                        }
                    })
                    .done(function(result) {
                        console.log(result.status);
                        if (result.status == 200) {
                            Swal.fire({
                                confirmButtonColor: "#125c4f",
                                title: result.message + ' :)',
                                icon: "success",
                            });
                            if (table_type == 'rejectedProfile') {
                                table4.row(current.parents('tr'))
                                    .remove()
                                    .draw();
                            } else {
                                table3.row(current.parents('tr'))
                                    .remove()
                                    .draw();
                            }
                            if (type == 0) {
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500)
                            }
                        }

                    })
            } else {
                Swal.fire({
                    confirmButtonColor: "#125c4f",
                    title: 'Status revert back :)',
                    icon: "error",
                })
            }
        });
    });

    $('body').on('click', '#orderModal', function() {
        $('#orderModal').modal({
            show: false
        });
        var order_id = $(this).attr('data-entry-id');
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
                $(".modal-body").html(data);
                $('#orderModal').modal({
                    show: true
                });
            })
    });
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    //  document.addEventListener("DOMContentLoaded", function() {

    window.addEventListener("load", function() {
        updateChart();
    });

    // var data_init = @json($worldData);

    /* var map = new jsVectorMap({
        map: "world",
        selector: "#world_map",
        zoomButtons: true,
        markers: data_init,
        regionsSelectable: true,
        markersSelectable: true,
        //backgroundColor: '#383f47',
        markerStyle: {
            initial: {
                r: 9,
                strokeWidth: 7,
                stokeOpacity: .4,
                fill: '#262335'
            },
            hover: {
                fill: 'black',
                stroke: '#F19335'
            }
        },
        regionStyle: {
            hover: {
                fill: '#484140',
                "fill-opacity": 0.8,
                cursor: 'pointer'
            },
        },
        zoomOnScroll: true
    }); */

    /* window.addEventListener("resize", () => {
        map.updateSize();
    }); */

    var data_google = @json($worldData);

    $('body').on('click', '#map_div', function() {
        clearTimeout(interval);
    });

    function updateChart() {
        $.ajax({
            url: "{{ route('world.chart') }}",
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                // data_init = [];
                // data_init = data;

                drawChart(data);
            },
            error: function(data) {

            }
        });
    }
    var interval = setInterval(() => {
        updateChart();
    }, 15000);

    //  });

    google.charts.load("current", {
        "packages": ["map"],
        "mapsApiKey": "AIzaSyC7kiVlXV3vw-HjT_9UmKEoJ5su1KXJrBA"
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart(data_google) {
        var data = [];

        var Header = ['Lat', 'Long', 'Name'];

        data.push(Header);

        for (var i = 0; i < data_google.length; i++) {
            var role;
            if (data_google[i].role_id == 2) {
                role = 'Parlour';
            } else {
                role = 'User';
            }
            var temp = [];
            temp.push(parseFloat(data_google[i].latitude));
            temp.push(parseFloat(data_google[i].longitude));
            temp.push(data_google[i].name + ' - ' + role); //this will change based on value of 'i' variable
            data.push(temp);
        }

        var data = google.visualization.arrayToDataTable(data);
        var map = new google.visualization.Map(document.getElementById('map_div'));
        map.draw(data, {
            mapType: 'styledMap',
            mapTypeId: "coordinate",
            mapTypeControlOptions: {
                mapTypeIds: ["coordinate", "roadmap"],
            },
            showTooltip: true,
            showInfoWindow: true,
            useMapTypeControl: true,
            styledMap: {
                name: 'Styled Map', // This name will be displayed in the map type control.
                styles: [{
                        featureType: 'poi.attraction',
                        stylers: [{
                            color: '#fce8b2'
                        }]
                    },
                    {
                        featureType: 'road.highway',
                        stylers: [{
                            hue: '#0277bd'
                        }, {
                            saturation: -50
                        }]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'labels.icon',
                        stylers: [{
                            hue: '#000'
                        }, {
                            saturation: 100
                        }, {
                            lightness: 50
                        }]
                    },
                    {
                        featureType: 'landscape',
                        stylers: [{
                            hue: '#259b24'
                        }, {
                            saturation: 10
                        }, {
                            lightness: -22
                        }]
                    }
                ]
            },

            icons: {
                default: {
                    normal: 'https://img.icons8.com/office/50/000000/marker.png',
                    selected: 'https://img.icons8.com/office/50/000000/marker.png'
                }
            }
        });
    }


    function reload() {
        location.reload();
        // $("#world_locations").load(location.href + " #world_locations>*", "");
        //updateChart();
    };
</script>

<script src="https://www.gstatic.com/firebasejs/8.6.8/firebase.js"></script>

<script>
    /*  window.onload = function() {
        initFirebaseMessagingRegistration();
    };
    var firebaseConfig = {
        apiKey: "AIzaSyAvIhqc2ddzLBgsi2XcWJMrzl8TrAG0sKE",
        authDomain: "fade-16089.firebaseapp.com",
        projectId: "fade-16089",
        storageBucket: "fade-16089.appspot.com",
        messagingSenderId: "93461071428",
        appId: "1:93461071428:web:d8e4c7e3e542f576309e11",
        measurementId: "G-T5ZY0EW9T6"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken()
            })
            .then(function(token) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("save_token") }}',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function(response) {

                       
                    },
                    error: function(err) {
                       
                    },
                });

            }).catch(function(err) {
               
            });
    }

    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            //icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    }); */
</script>

@endsection