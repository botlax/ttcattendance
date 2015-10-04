@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-2 col-md-offset-3">
		<a id="site-btn-add" role="button" class="btn btn-default" href="{{ url('/trades/add') }}">Add</a>
	</div>
	<div class="col-md-6 col-md-offset-3">
		<table class="table table-striped table-bordered table-condensed">
		@foreach($trades as $trade)
			<tr>
				<td>{{ $trade->name }}</td>
				<td class="trade-tools"><a id="trade-btn-edit" role="button" class="btn btn-success" href="{{ url('/trades/'.$trade->name.'/edit') }}">Edit</a></td>
				<td class="trade-tools"><a id="trade-btn-delete" role="button" class="btn btn-danger" href="{{ url('/trades/'.$trade->name.'/delete') }}">Delete</a></td>
			</tr>
		@endforeach()
		</table>
	</div>
</div>

@stop