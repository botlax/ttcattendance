@extends('master')

@section('title')
Users
@stop

@section('content')

<div class="row">
	<h1 class="text-center"><small>Attendance for {{ date('M d, Y') }}</small></h1>
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
		<table class="table">
			<tr>
				<th class="text-right">Project</th> 
				<td>{{ $labor->site->code }} ({{ $labor->site->name }})</td>
			</tr>
			<tr>
				<th class="text-right">Employee No</th>
				<td>{{ $labor->employee_no }}</td>
			</tr>
			<tr>
				<th class="text-right">Name</th>
				<td>{{ $labor->name }}</td>
			</tr>
		</table>
	</div>

	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
	@include('partials._error')
	{!! Form::open(['route'=>['updateEntry',$labor->id]]) !!}
		
		{!! Form::hidden('date',Carbon\Carbon::parse($dateF)) !!}
		@if($field == 'attended')
		<div class="form-group">
			{!! Form::label('attended','Present:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::select('attended',['1'=>'Yes','0'=>'No'],$entry->attended,['id'=>'present']) !!}
			</div>
		</div>
		@elseif($field == 'ot')
		<div class="form-group conditional">
			{!! Form::label('ot','Overtime:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::text('ot',$entry->ot,['class'=>'form-control']) !!}
			</div>
		</div>
		@elseif($field == 'bot')
		<div class="form-group conditional">
			{!! Form::label('bot','Bonus Overtime:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::text('bot',$entry->bot,['class'=>'form-control']) !!}
			</div>
		</div>
		@elseif($field == 'site')
		<div class="form-group conditional">
			{!! Form::label('site','Site:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::select('site',$sites,$entry->site,['class'=>'form-control']) !!}
			</div>
		</div>
		@endif
		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
				<button type="submit" class="btn btn-primary">
					Update
				</button>
			</div>
		</div>
	{!! Form::close() !!}
	</div>
</div>
@stop
