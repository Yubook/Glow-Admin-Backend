@extends('layouts.admin')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-lg-6 text-left page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">List of Parlours</h4>

            </div>
            <div class="col-lg-6 mb-3 text-right">
                <a class="btn btn-primary" href="{{ route('barbers.create') }}">
                    Add Parlour
                </a>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <input type="hidden" id="total_barber" value="{{ $barbers }}">
            @forelse($allBarbers as $key=>$barber)

            <div class="col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-sm-4">
                                <h3>Parlour {{ $key + 1 }}</h3>
                                <h4 class="card-title mb-4">Mobile Parlour </h4>
                                @if(!isset($barber['profile']))
                                <a href="#" style="height: 120px;display: block;overflow: hidden;" class="mb-5">
                                    <img class="img-fluid img-responsive" style="height: 100%;object-fit: contain;" src="{{asset(Storage::url('no-image.jpg')) ?? ''}}" alt="{{ $barber['name'] ?? ''}}" />
                                </a>
                                @else
                                <a href="#" style="height: 120px;display: block;overflow: hidden;" class="mb-5">
                                    <img class="img-fluid img-responsive" style="height: 100%;object-fit: contain;" src="{{asset(Storage::url($barber['profile'])) ?? ''}}" alt="{{ $barber['name'] ?? ''}}" />
                                </a>
                                @endif
                            </div>
                            <div class="col-sm-5">
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Name</span>:<span class="pl-2">{{$barber['name'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Email</span>:<span class="pl-2">{{$barber['email'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Contact</span>:<span class="pl-2">{{$barber['mobile'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Address 1</span>:<span class="pl-2">{{$barber['address_line_1'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Address 2</span>:<span class="pl-2">{{$barber['address_line_2'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">Postal Code</span>:<span class="pl-2">{{$barber['postal_code'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">State</span>:<span class="pl-2">{{$barber['state']['name'] ?? ''}}</span>
                                </p>
                                <p class="mb-1 font-weight-bold">
                                    <span class="d-inline-block" style="min-width: 70px !important;">City</span>:<span class="pl-2">{{$barber['city']['name'] ?? ''}}</span>
                                </p>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div id="radialBar-chart_{{$barber['id']}}" class="apex-charts"></div>
                                <div class="mt-3">
                                    <label>Status</label>
                                    @include('partials.switch', ['id'=>
                                    $barber['id'],'is_active'=>$barber['is_active']])
                                </div>
                                <div class="mt-2">
                                    <label>Edit</label>
                                    <div> @include('partials.actions', ['id' => $barber['id']])
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row align-items-center text-center">
                            <div class="col-sm-3">
                                <h5 class="font-size-15 text-truncate">{{$barber['total_users'] ?? ''}}</h5>
                                <p class="text-muted mb-0 text-truncate">Total Customer</p>
                            </div>
                            <div class="col-sm-3">
                                <h5 class="font-size-15">{{$barber['total_orders'] ?? ''}}</h5>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                            <div class="col-sm-3">
                                <h5 class="font-size-15">£ {{$barber->wallet->save_amount ?? '0'}}</h5>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                            <div class="col-sm-3">
                                <a href="{{route('barbers.show',['barber'=>$barber['id']]) }}" class="btn btn-primary waves-effect waves-light btn-sm">View
                                    Details </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @empty
            <div class="col-md-12 text-center">
                <h4 style="text-decoration: underline;"> No data Found </h4>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    var barber = document.querySelector("#total_barber").value;
    var ids = @json($allBarbers);
    console.log(ids);

    ids.forEach((item, index) => {
        var progressbar = (parseInt(item.completed_orders) * 100) / parseInt(item.total_orders);
        if (isNaN(progressbar)) {
            progressbar = 0;
        }
        console.log(progressbar);
        /*  var options = {
             chart: {
                 height: 200,
                 type: 'radialBar',
                 offsetY: -10
             },
             series: [progressbar],
             labels: ['Progress'],
         } */

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
</script>

@endsection