@php  
	$routeName = Route::currentRouteName(); 
	$route = str_replace(".show","",$routeName)
@endphp
<a style="margin-top:20px;" class="btn btn-primary" href="{{ url()->previous() }}">
    Back to List
</a>
<a style="margin-top:20px;" class="btn btn-success" href="{{ route($route.'.edit', $id) }}">
    Edit
</a>