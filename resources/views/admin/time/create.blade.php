@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-body">
                <div class="card-title mb-3">
                    Create Time
                </div>
                <form action="{{ route('timings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Add New Time Slot* </label><br>
                        <label>This Format Ex. (09:00)</label>
                        <input type="text" name="time" class="form-control" value="{{ old('time') }}" required>
                        @if ($errors->has('time'))
                        <div class="error">
                            {{ $errors->first('time') }}
                        </div>
                        @endif
                    </div>

                    <div class="form-group finalSubmitBtn">
                        <input class="btn btn-primary btn-md" type="submit" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection