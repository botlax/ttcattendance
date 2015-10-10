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
			<tr><td colspan="2" class="text-center"><img src="{{url('images/'.$labor->employee_no.'.jpg')}}" width="100px" height="100px"></td></tr>
		</table>
	</div>

	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
	@include('partials._error')
	{!! Form::open(['route' => ['storeAttendance',$labor->id],'class'=>'form-horizontal']) !!}
		
		@if($holiday)
			{!! Form::hidden('present',1) !!}
		@else
			<div class="form-group">
				{!! Form::label('present','Present:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
				<div class="col-md-6 col-sm-6 col-xs-6">
				{!! Form::select('present',['1'=>'Yes','0'=>'No'],null,['id'=>'present-add']) !!}
				</div>
			</div>
		@endif

		<div class="form-group conditional">
			{!! Form::label('overtime','Overtime:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::text('overtime',null,['class'=>'form-control']) !!}
			</div>
		</div>

		<div class="form-group conditional">
			{!! Form::label('bonus_ot','Bonus Overtime:',['class'=>'col-md-4 col-sm-4 col-xs-4 control-label']) !!}
			<div class="col-md-6 col-sm-6 col-xs-6">
			{!! Form::text('bonus_ot',null,['class'=>'form-control']) !!}
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
				<button type="submit" class="btn btn-primary">
					Add
				</button>
			</div>
		</div>
	{!! Form::close() !!}
	</div>
</div>

<script>
	$( document ).ready(function() {
        
        if($('#present-add').val() == 0){
        	$('.conditional').hide();
        }
	    $('#present-add').change(function(){
	        $('.conditional').hide();
	        if($(this).val() == '1'){
	        	$('.conditional').show();
	        }
	    });
	    $("#present-add").select2(); 
	});	
</script>
@stop
