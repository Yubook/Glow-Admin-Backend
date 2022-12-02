@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="row">
            <div class="col-lg-12 mb-3 text-right">
                <a class="btn btn-primary" href="{{ route('subcategories.create') }}">
                    Add Sub Category
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Sub Category List
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
                                    Sub Category Name
                                </th>
                                <th>
                                    Category Name
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
                            @foreach ($categories as $key => $value)
                            <tr data-entry-id="{{ $value->id }}">
                                <td>
                                    {{ $key + 1 }}
                                </td>
                                <td>
                                    {{ $value->name ?? '' }}
                                </td>
                                <td>
                                    {{ $value->category->name ?? '' }}
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