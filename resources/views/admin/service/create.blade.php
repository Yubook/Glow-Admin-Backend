@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-body">
                <div class="card-title mb-3">
                    Create Service
                </div>
                <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Service Name*</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                        <div class="error">
                            {{ $errors->first('name') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Service Time (Minutes)*</label>
                        <input type="number" name="time" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" min="0" class="form-control" value="{{ old('time') }}" required>
                        @if ($errors->has('time'))
                        <div class="error">
                            {{ $errors->first('time') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category*</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="" selected disabled>Please select category</option>
                            @foreach($categories as $key=>$value)
                            @if (old('category_id') == $value->id)
                            <option value="{{$value->id}}" selected>{{ $value->name ?? '' }} </option>
                            @else
                            <option value="{{$value->id ?? ''}}"> {{$value->name ?? ""}}</option>
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
                        <label for="subcategory_id">SubCategory*</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-control" required>
                            <option value="" selected disabled>Please select category</option>
                        </select>
                        @if ($errors->has('subcategory_id'))
                        <div class="error">
                            {{ $errors->first('subcategory_id') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="image">Service Image*</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/x-png,image/gif,image/jpeg" value="{{ old('image') }}">
                        @if ($errors->has('image'))
                        <div class="error">
                            {{ $errors->first('image') }}
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

@section('scripts')
<script>
    $(document).ready(function() {
        var _token = $('meta[name="csrf-token"]').attr('content');
        $('body').on('change', '#category_id', function() {
            var category_id = $('#category_id').val();
            $.ajax({
                headers: {
                    'x-csrf-token': _token
                },
                method: 'POST',
                url: "{{ route('getSubcategory') }}",
                data: {
                    category_id: category_id,
                    _method: 'POST'
                },
                success: function(result) {
                    if (result.success) {
                        $("#subcategory_id option[value='']").remove();
                        $('#subcategory_id').append(result.html);
                    }
                }
            })
        })
    });
</script>
@endsection