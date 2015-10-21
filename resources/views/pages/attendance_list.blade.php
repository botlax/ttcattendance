@extends('master')

@section('title')
Users
@stop

@section('content')

<h1 class="text-center"> <small>Attendance for {{date('M d, Y')}} </small></h1>
@include('partials._error')
<div class="row">
	@foreach($sites as $site)
	<div class="text-center col-xs-12 col-sm-6 col-sm-offset-3 site-wrap">
		<a href="{{url('attendance/list/'.$site->code)}}" class="btn btn-primary">{{$site->code}} ({{$site->name}})</a>
	</div>
	
	@endforeach
</div>

@stop

