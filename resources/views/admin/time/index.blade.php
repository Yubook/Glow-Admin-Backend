@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-lg-12 mb-3 text-right">
                <a class="btn btn-primary" href="{{ route('timings.create') }}">
                    Add Timing
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Timing List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover datatable datatable-User" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    No
                                </th>
                                <th>
                                    Timing
                                </th>
                                <th>
                                    Type
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timings as $key => $value)
                            <tr data-entry-id="{{ $value->id }}">
                                <td>
                                    {{ $key + 1 }}
                                </td>
                                <td>
                                    {{ $value->time ?? '' }}
                                </td>
                                <td>
                                    @if($value->type == 1)
                                    <span class="badge badge-danger">Extra Time</span>
                                    @else
                                    <span class="badge badge-info">Regular</span>
                                    @endif
                                </td>
                                <td>
                                    @include('partials.switch', ['id'=>
                                    $value->id,'is_active'=>$value->is_active])
                                </td>
                                <td>@include('partials.actions', ['id' => $value->id])</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection