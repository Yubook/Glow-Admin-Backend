@extends('layouts.admin')

@section('content')
<div class="container" style="margin-top: 6%;">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">Chat</div>
             
                <div class="card-body" style="margin-top: 2%;" id="app">
                    <live-chat :user="{{auth()->user()}}"></live-chat>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection