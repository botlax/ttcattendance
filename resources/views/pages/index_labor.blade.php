@extends('master')

@section('title')
Users
@stop

@section('content')
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">
		<a id="labor-btn-add" role="button" class="btn btn-default" href="{{ url('/employees/add') }}">Add</a>
		<a id="labor-btn-show" role="button" class="btn btn-default" href="{{ url('/employees') }}">Show all</a>
		<a id="labor-btn-loan" role="button" class="btn btn-default" href="{{ url('/employees/with-loan') }}">With Loan</a>
		<a id="labor-btn-loan" role="button" class="btn btn-default" href="{{ url('/employees/fix') }}">Fix</a>
	</div>
@include('partials._error')
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-xs-12">
	{!! Form::open(['url'=>'/employees']) !!}
			{!! Form::text('id',null,['id'=>'labor-form-text-id']) !!}
			<button type="submit" id="labor-form-btn-search" class="btn btn-primary">search</button>
	{!! Form::close() !!}
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
		<table id="labor-table" class="table table-striped table-bordered table-hover table-condensed">
			<thead>
				<tr>
					<th></th>
					<th>Employee ID</th>
					<th>Name</th>
					<th>Trade</th>
					<th>Site</th>
					<th>Basic Salary</th>
					<th>Allowance</th>
					<th colspan="2">Options</th>
				</tr>
			</thead>
		@if($labors->toArray()['total'] != 0)
			<tbody>
			@foreach($labors as $labor)
				<tr>
					<td><a href="{{url('/images/'.$labor->employee_no.'.jpg')}}" target="_blank"><img src="/images/{{$labor->employee_no}}.jpg" alt="Photo" width="70px" height="70px"></a></td>
					<td>{{ $labor->employee_no }}</td>
					<td>{{ $labor->name }}</td>
					<td>{{ $labor->trade->name }}</td>
					<td>{{ $labor->site->code }}</td>
					<td>QAR {{ $labor->basic_salary }}</td>
					<td>QAR {{ $labor->allowance }}</td>
					<td><a class="labor-btn-edit btn btn-success" href="{{ url('/employees/'.$labor->employee_no.'/edit') }}"></a></td>
					<td><a class="labor-btn-delete btn btn-danger" href="{{ url('/employees/'.$labor->id.'/delete') }}"></a></td>
				</tr>
			@endforeach()
			</tbody>
		@else
			<tr>
				<th colspan="3">No results found.</th>
			</tr>
		@endif
		</table>
	</div>
</div>
@if(!empty($labors->toArray()) && count($labors->toArray()) != 1)
	{!! $labors->render() !!}
@endif
@stop