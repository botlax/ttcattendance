@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-2 col-md-offset-3">
		<a id="trade-btn-add" role="button" class="btn btn-default" href="{{ url('/holiday/add') }}">Add</a>
	</div>
	<div class="col-md-6 col-md-offset-3">
		<table class="table table-striped table-bordered">
		@foreach($holidays as $holiday)
			<tr>
				<td>{{ $holiday->holidate->format('M d,Y') }}</td>
				<td><a id="holiday-btn-edit" role="button" class="btn btn-success" href="{{ url('/holiday/'.$holiday->holidate->format('Y-m-d').'/edit') }}">Edit</a></td>
				<td><a id="holiday-btn-delete" role="button" class="btn btn-danger" href="{{ url('/holiday/'.$holiday->holidate->format('Y-m-d').'/delete') }}">Delete</a></td>
			</tr>
		@endforeach()
		</table>
	</div>
</div>
@stop