@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-6 col-md-offset-1">
		<h1>404 <small>Woops! You're on the wrong way bro.</small></h1>
		<p>The page you requested (<strong>{{Request::url()}}</strong>)<br>
		cannot be found.</p>
	</div>
</div>
@stop