@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
		<table class="table table-striped">
			<tr><td colspan="2">
				<a id="user-btn-edit" role="button" class="btn btn-success" href="{{ url('/users/'.$user->employee_no.'/edit') }}">Edit</a>
				<a id="user-btn-delete" role="button" class="btn btn-danger" href="{{ url('/users/'.$user->id.'/delete') }}">Delete</a>
			</td></tr>
			<tr>
				<th>Name</th>
				<td>{{ $user->name }}</td>
			</tr>
			<tr>
				<th>Employee ID</th>
				<td>{{ $user->employee_no }}</td>
			</tr>
			<tr>
				<th>Role</th>
				<td>{{ $user->role->role }}</td>
			</tr>
		</table>
	</div>
</div>

@stop