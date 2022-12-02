@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-6 mb-2 text-left">
                Show Service
            </div>
            <div class="col-6 mb-2 text-right">
                <a class="btn btn-primary" href="{{ url()->previous() }}">
                    Back To List
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>
                                    ID
                                </th>
                                <td>
                                    {{ $service->id ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Name
                                </th>
                                <td>
                                    {{ $service->name ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Time
                                </th>
                                <td>
                                    {{ $service->time ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Category
                                </th>
                                <td>
                                    {{ $service->category->name ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Sub Category
                                </th>
                                <td>
                                    {{ $service->subcategory->name ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Image
                                </th>
                                <td>
                                    @if(!isset($service->image))
                                    <a href="#">
                                        <img src="{{asset('no-image.jpg') ?? ''}}" alt="" height=100 width=100>
                                    </a>
                                    @else
                                    <a href="#">
                                        <img src="{{asset(Storage::url($service->image)) ?? ''}}" alt="" height=100 width=100>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Status
                                </th>
                                <td>
                                    {{ ($service->is_active == 1) ? 'Active':'Deactive' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection