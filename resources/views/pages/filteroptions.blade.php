@extends('master')
@section('css')
<link rel="stylesheet" href="{{url('css/att_print.css')}}">
@stop
@section('title')
Attendance
@stop

@section('content')
<div id="filter-attendance-wrap">
{!! Form::open(['method'=>'GET','route' => 'filterAttendance','class' => 'form-inline']) !!}
<!--
	<div class="form-group">
		{!! Form::label('date_from','From: ') !!}
		{!! Form::input('date','date_from',null) !!}
	</div>

	<div class="form-group">
		{!! Form::label('date_to','To: ') !!}
		{!! Form::input('date','date_to',null) !!}
	</div>
-->
	<div class="form-group">
		{!! Form::label('employee_no','Employee ID: ') !!}
		{!! Form::text('employee_no',null) !!}
	</div>

	<div class="form-group">
		{!! Form::label('month','Month:') !!}
		{!! Form::select('month',$months,null,['id' => 'filter-months','style'=>'width: 130px']) !!}
	</div>

	<div class="form-group">
		{!! Form::label('year','Year:') !!}
		{!! Form::select('year',$years,null,['id' => 'filter-years','style'=>'width: 90px']) !!}
	</div>

	<div class="form-group">
		{!! Form::label('site_list[]','Site:') !!}
		{!! Form::select('site_list[]',$sites,null,['multiple','id' => 'filter-sites','style'=>'width: 100px']) !!}
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-primary">Filter</button>
	</div>

	<div class="form-group">
		{!! Form::checkbox('view-deleted',1) !!}
		{!! Form::label('view-deleted','Include Deleted Employees') !!}
	</div>
	<div class="form-group">
		{!! Form::checkbox('view-absent',1) !!}
		{!! Form::label('view-absent','View Absentees Only') !!}
	</div>

{!! Form::close() !!}
</div>

@include('partials._error')

@if(isset($labors))
	<div class="text-center">
		<h1><small>Attendance for the month of </small><mark>{{ $month }} {{ $year }}</mark></h1>
	</div>
		<table class='table table-bordered table-condensed' id="attendance-table">
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Trade</th>
				<th>Date</th>
			@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<th>{{$dateFrom->format('d')}}</th>
			@endfor
			<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year) ?>
				<th>Total</th>
			</tr>
		@foreach($labors as $labor)
			<tr>
				<td rowspan="5">{{ $labor->employee_no }}</td>
				<td rowspan="5" class="text-center">{{ $labor->name }}</td>
				<td rowspan="5">{{ $labor->trade->name }}</td>
			</tr>
			<tr>
				<td>Attended</td>
				<?php $attendance_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['attended'] as $key => $attended)
				<td>
					<a href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/attended')}}">{{$attended}}</a>
				</td>
				@endforeach
				<td>{{ $attendance_total }}</td>
			</tr>
			<tr>
				<td>Overtime(OT)</td>
				<?php $ot_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['ot'] as $key => $ot)
				<td>
					<a href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/ot')}}">{{$ot}}</a>
				</td>
				@endforeach
				<td>{{$ot_total}}</td>
				</tr>
			<tr>
				<td>Bonus OT</td>
				<?php $bot_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['bot'] as $key => $bot)
				<td>
					<a href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/bot')}}">{{$bot}}</a>
				</td>
				@endforeach
				<td>{{$bot_total}}</td>
				</tr>
			<tr>
				<td>Site</td>
				@foreach($labor_att[$labor->employee_no]['site'] as $key => $site)
				<td class="site-row">
					<a href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/site')}}">{{$site}}</a>
				</td>
				@endforeach
				<td></td>
				</tr>			
		@endforeach
		</table>
@endif

<script>
	$(document).ready(function() { 
		$("#filter-sites").select2({
			placeholder: 'Select site'
		}); 
		$("#filter-months").select2({
			placeholder: 'Select month'
		});
		$("#filter-years").select2({
			placeholder: 'Select year'
		});
	});
</script>
@stop