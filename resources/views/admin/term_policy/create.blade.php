@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        @include("partials.alert")
        <div class="card">
            <div class="card-body">
                <div class="card-title mb-3">
                    Create Term & Condition
                </div>
                <form action="{{ route('terms.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Selection*</label>
                        <select id="selection" name="selection" class="form-control" required>
                            <option value="Terms" selected>
                                Terms
                            </option>
                            <option value="Privacy">
                                Privacy
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description*</label>
                        <textarea id="description" name="description" class="form-control" required>{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                        <div class="error">
                            {{ $errors->first('description') }}
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>For*</label>
                        <select id="for" name="for" class="form-control" required>
                            <option value="" selected disabled>Select For</option>
                            <option value="2">
                                Driver
                            </option>
                            <option value="3">
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
    CKEDITOR.replace('description');
</script>

@endsection