@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Edit Reason
            </div>
            <div class="card-body">
                <form action="{{ route('reasons.update', [$reason->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Reason*</label>
                        <input type="text" name="reason" class="form-control" value="{{ old('reason', isset($reason->reason) ? $reason->reason : '') }}">
                        @if ($errors->has('reason'))
                        <div class="error">
                            {{ $errors->first('reason') }}
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