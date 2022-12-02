@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Edit City
            </div>
            <div class="card-body">
                <form action="{{ route('cities.update', [$city->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Name*</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', isset($city->name) ? $city->name : '') }}">
                        @if ($errors->has('name'))
                        <div class="error">
                            {{ $errors->first('name') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group finalSubmitBtn">
                        <input class="btn btn-primary btn-md" type="submit" value="Update">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection