@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-body">
                <div class="card-title mb-3">
                    Create reason
                </div>
                <form action="{{ route('reasons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Reason*</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason') }}">
                        @if ($errors->has('reason'))
                        <div class="error">
                            {{ $errors->first('reason') }}
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