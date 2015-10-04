@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-2 col-md-offset-3">
		<a id="trade-btn-add" role="button" class="btn btn-default" href="{{ url('/sites/add') }}">Add</a>
	</div>
	<div class="col-md-6 col-md-offset-3">
		<table class="table table-striped table-bordered">
		@foreach($sites as $site)
			<tr>
				<td>{{ $site->code }} ({{ $site->name }})</td>
				<td><a id="site-btn-edit" role="button" class="btn btn-success" href="{{ url('/sites/'.$site->code.'/edit') }}">Edit</a></td>
				<td><a id="site-btn-delete" role="button" class="btn btn-danger" href="{{ url('/sites/'.$site->code.'/delete') }}">Delete</a></td>
			</tr>
		@endforeach()
		</table>
	</div>
</div>
@stop