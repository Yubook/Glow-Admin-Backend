@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")

        <div class="row">
            <div class="col-6 mb-2 text-left">
                Show Term & Condition
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
                                    {{ $termspolicy->id ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                Name
                                </th>
                                <td>
                                    {{ $termspolicy->selection ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Description
                                </th>
                                <td>
                                    {{ strip_tags($termspolicy->description ?? '')}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    For
                                </th>
                                <td>
                                    @if($termspolicy->for == 3)
                                    <span class="badge badge-pill badge-soft-success font-size-14">User</span>
                                    @else
                                    <span class="badge badge-pill badge-soft-primary font-size-14">Driver</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Status
                                </th>
                                <td>
                                    {{ ($termspolicy->is_active == 1) ? 'Active':'Deactive' }}
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