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
		<table id="holiday-table" class="table table-striped table-bordered table-condensed">
		@foreach($holidays as $holiday)
			<tr>
				<td>{{ $holiday->holidate->format('M d,Y') }}</td>
				<td><a role="button" class="btn btn-success holiday-btn-edit" href="{{ url('/holiday/'.$holiday->holidate->format('Y-m-d').'/edit') }}"></a></td>
				<td><a role="button" class="btn btn-danger holiday-btn-delete" href="{{ url('/holiday/'.$holiday->holidate->format('Y-m-d').'/delete') }}"></a></td>
			</tr>
		@endforeach()
		</table>
	</div>
</div>
@stop