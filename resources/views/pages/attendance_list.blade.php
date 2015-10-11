@extends('master')

@section('css')
<link rel="stylesheet" href="{{url('css/jquery-ui.min.css')}}">
@stop

@section('script')
<script src="{{url('js/jquery-ui.min.js')}}"></script>
@stop

@section('title')
Users
@stop

@section('content')
@unless($locked)
<h1 class="text-center"> <small>Attendance for {{date('M d, Y')}} </small></h1>
@include('partials._error')
<div class="row">
	@foreach($sites as $site)
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
		<h3><small>{{$site->code}} ({{$site->name}})</small></h2>
		{!! Form::open(['route'=>'searchID','class'=>'form-inline']) !!}
			{!! Form::hidden('site',$site->id) !!}
			
			{!! Form::text('id'.$site->id,null,['class'=>'form-control','placeholder'=>'Emp. ID...','id'=>'att-list-text-search','style'=>'display:inline-block']) !!}
		
			{!! Form::submit('Go',['class'=>'btn btn-default','id'=>'att-list-btn-search','style'=>'display:inline-block']) !!}
		
		{!! Form::close() !!}
	</div>
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
		<a role="button" href="#" style="display:block" class="text-center show-att-btn btn btn-default">Show filled up employee attendance <span>&#11015;</span></a>
		<table class="table">
		@foreach(App\Labor::where('site_id',$site->id)->orderBy('employee_no','asc')->get() as $labor)
			<tr>
			@if($labor->attendance->where('id',$dateId)->first() != null)
			<td><a class="btn btn-success filled-up" href="{{url('attendance/list/'.$labor->employee_no.'/edit')}}">{{$labor->employee_no}} {{$labor->name}}</a></td>
			@else
			<td><a class="btn btn-default not-filled-up" href="{{url('attendance/list/'.$labor->employee_no)}}">{{$labor->employee_no}} {{$labor->name}}</a></td>
			@endif
			</tr>
		@endforeach
		</table>
	</div>
	@endforeach
	<div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
		{!! Form::open(['route'=>['lockAttendance',$userID],'id'=>'lock-form']) !!}
			<button type="submit" id="submit-attendance" class="btn btn-primary">Submit</button>
		{!! Form::close() !!}
	</div>
</div>

	

<div id="dialog-confirm" title="Submit to database">Are you sure you want to submit and lock the attendance for today?</div>

	<script>
	$(document).ready(function(){
		$('table').hide();
		$('.show-att-btn').click(function(){
			if($(this).next().is(":visible")){
				$(this).next().fadeOut(500);
				$(this).children('span').html('&#11015;');
			}else{
				$(this).next().fadeIn(500);
				$(this).children('span').html('&#11014;');
			}
		});

		$( "#dialog-confirm" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        "Yes": function() {
		            $( "#lock-form" ).submit();
		        },
		        Cancel: function() {
		            $( this ).dialog( "close" );
		        }
	      	}
		});
		$( "#submit-attendance" ).click(function(evt) {
			evt.preventDefault();
  			$( "#dialog-confirm" ).dialog( "open" );
		});
	});
</script>
@endunless
@stop

