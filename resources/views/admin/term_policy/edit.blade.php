@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-header">
                Edit Term & Condition
            </div>
            <div class="card-body">
                <form action="{{ route('terms.update', [$termspolicy->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Selection*</label>
                        <select id="selection" name="selection" class="form-control" required>
                            <option value="Terms" @if($termspolicy->selection == 'Terms') selected @endif>
                                Terms
                            </option>
                            <option value="Privacy" @if($termspolicy->selection == 'Privacy') selected @endif>
                            Privacy
                            </option>
                        </select> 
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" class="form-control" required>{{ old('description', isset($termspolicy->description) ? $termspolicy->description : '') }}</textarea>
                        @if ($errors->has('description'))
                        <div class="error">
                            {{ $errors->first('description') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>For*</label>
                        <select id="for" name="for" class="form-control" required>
                            <option value="" disabled>Select For</option>
                            <option value="2" @if($termspolicy->for == 2) selected @endif>
                                Driver
                            </option>
                            <option value="3" @if($termspolicy->for == 3) selected @endif>
                                User
                            </option>
                        </select>
                        @if ($errors->has('for'))
                        <div class="error">
                            {{ $errors->first('for') }}
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

@section('scripts')

<script>
    CKEDITOR.replace('description');
</script>

@endsection