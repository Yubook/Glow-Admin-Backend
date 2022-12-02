@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Edit Timing
            </div>
            <div class="card-body">
                <form action="{{ route('timings.update', [$timing->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Edit Time Slot* </label><br>
                        <label>This Format Ex. (09:00)</label>
                        <input type="text" name="time" class="form-control" value="{{ old('time',isset($timing->time) ? $timing->time : '') }}" required>
                        @if ($errors->has('time'))
                        <div class="error">
                            {{ $errors->first('time') }}
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