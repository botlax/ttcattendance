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
		<table id="site-table" class="table table-striped table-bordered table-condensed">
		@foreach($sites as $site)
			<tr>
				<td>{{ $site->code }} ({{ $site->name }})</td>
				<td><a role="button" class="btn btn-success site-btn-edit" href="{{ url('/sites/'.$site->code.'/edit') }}"></a></td>
				<td><a role="button" class="btn btn-danger site-btn-delete" href="{{ url('/sites/'.$site->code.'/delete') }}"></a></td>
			</tr>
		@endforeach()
		</table>
	</div>
</div>
@stop