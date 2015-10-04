@extends('master')

@section('title')
Users
@stop

@section('content')

@foreach($users as $user)
	<ul>
		<li><a href="{{ url('/users',$user->employee_no) }}">{{ $user->name }}</a></li>
	</ul>
@endforeach()

@stop