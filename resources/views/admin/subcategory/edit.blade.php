@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Edit Sub Category
            </div>
            <div class="card-body">
                <form action="{{ route('subcategories.update', [$subcategory->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="parent_id">Category*</label>
                        <select id="parent_id" name="category_id" class="form-control">
                            <option value="" selected>Please select category</option>
                            @foreach($categories as $key=>$value)
                            @if ((old('category_id') == $value->id) || ($subcategory->category_id == $value->id))
                            <option value="{{$value->id}}" selected>{{ $value->name ?? '' }}</option>
                            @else
                            <option value="{{$value->id ?? ""}}"> {{$value->name ?? ""}} </option>
                            @endif
                            @endforeach
                        </select>
                        @if ($errors->has('category_id'))
                        <div class="error">
                            {{ $errors->first('category_id') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Name*</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', isset($subcategory->name) ? $subcategory->name : '') }}">
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