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
				@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<td>
				@if($labor->attendance()->where('att_date',$dateFrom)->first() != null && 
					$labor->attendance()->where('att_date',$dateFrom)->first()->pivot->locked == 'true')
				<?php 
				$attendance = $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->attended;
				$attendance_total +=  intval($attendance);
				?>
					@if($showAbsent && $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->attended == '0')
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/attended') }}">{{$attendance}}</a>
					@elseif(!$showAbsent)
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/attended') }}">{{$attendance}}</a>
					@endif
				@endif
				</td>
				@endfor
				<td>{{ $attendance_total }}</td>
				<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year); ?>
			</tr>
			<tr>
				<td>Overtime(OT)</td>
				<?php $ot_total = 0; ?>
				@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<td>
				@if($labor->attendance()->where('att_date',$dateFrom)->first() != null && 
					$labor->attendance()->where('att_date',$dateFrom)->first()->pivot->locked == 'true')
				<?php 
				$overtime = $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->ot;
				$ot_total +=  intval($overtime);
				?>
					@if($showAbsent && $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->attended == '0')
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/ot') }}">{{$overtime}}</a>
					@elseif(!$showAbsent)
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/ot') }}">{{$overtime}}</a>
					@endif
				@endif
				</td>
				@endfor
				<td>{{$ot_total}}</td>
				<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year); ?>
				</tr>
			<tr>
				<td>Bonus OT</td>
				<?php $bot_total = 0; ?>
				@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<td>
				@if($labor->attendance()->where('att_date',$dateFrom)->first() != null && 
					$labor->attendance()->where('att_date',$dateFrom)->first()->pivot->locked == 'true')
				<?php 
				$bot = $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->bot;
				$bot_total +=  intval($bot);
				?>
					@if($showAbsent && $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->attended == '0')
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/bot') }}">{{$bot}}</a>
					@elseif(!$showAbsent)
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/bot') }}">{{$bot}}</a>
					@endif
				@endif
				</td>
				@endfor
				<td>{{$bot_total}}</td>
				<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year); ?>
				</tr>
			<tr>
				<td>Site</td>
				@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<td class="site-row">
				@if($labor->attendance()->where('att_date',$dateFrom)->first() != null && 
					$labor->attendance()->where('att_date',$dateFrom)->first()->pivot->locked == 'true')
				<?php 
				$site = $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->site;
				?>
					@if($showAbsent && $labor->attendance()->where('att_date',$dateFrom)->first()->pivot->attended == '0')
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/site') }}">{{$site}}</a>
					@elseif(!$showAbsent)
					<a href="{{ url('attendance/'.$dateFrom->format('Y-m-d').'/'.$labor->employee_no.'/site') }}">{{$site}}</a>
					@endif
				@endif
				</td>
				@endfor
				<td></td>
				<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year); ?>
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