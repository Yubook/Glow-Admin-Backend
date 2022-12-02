@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")

        <div class="row">
            <div class="col-6 mb-2 text-left">
                Show State
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
                                    {{ $state->id ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    State Name
                                </th>
                                <td>
                                    {{ $state->name ?? ''}}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Status
                                </th>
                                <td>
                                    {{ ($state->is_active == 1) ? 'Active':'Deactive' }}
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