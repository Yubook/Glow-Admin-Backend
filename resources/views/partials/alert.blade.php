@if(Session::has('message'))
<div class="row mb-2">
    <div class="col-lg-12">
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="X">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('message') }}
        </div>
    </div>
</div>
@endif

@if(Session::has('error'))
<div class="row mb-2">
    <div class="col-lg-12">
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="X">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session('error') }}
        </div>
    </div>
</div>
@endif
