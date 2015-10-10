@extends('master')

@section('css')
<link rel="stylesheet" href="{{url('css/jquery-ui.min.css')}}">
@stop

@section('script')
<script src="{{url('js/jquery-ui.min.js')}}"></script>
<script src="{{url('js/jquery.validate.min.js')}}"></script>
<meta name="_token" content="{!! csrf_token() !!}"/>
@stop

@section('title')
Attendance
@stop

@section('content')
<div id="filter-attendance-wrap">
{!! Form::open(['method'=>'GET','route' => 'filterAttendance','id'=>'filter-form','class' => 'form-inline']) !!}
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
		<button type="submit" id="btn-filter" class="btn btn-primary">Filter</button>
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
	<div class="text-left" style="height:50px">
		<a role="button" class="btn btn-success" id="btn-make-xls" href="{{$_SERVER['REQUEST_URI']}}&makesheet=1">Download Spreadsheet</a>
	</div>
		<table class='table table-bordered table-condensed' id="attendance-table">
			<tr>
				<th class="bordered-bottom">ID</th>
				<th class="bordered-bottom">Name</th>
				<th class="bordered-bottom">Trade</th>
				<th class="bordered-bottom">Date</th>
			@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<th class="bordered-bottom">{{$dateFrom->format('d')}}</th>
			@endfor
			<?php $dateFrom = Carbon\Carbon::parse('1-'.$month.'-'.$year) ?>
				<th class="bordered-bottom">Total</th>
				<th class="bordered-bottom">Salary</th>
			</tr>
		@foreach($labors as $labor)
			<tr>
				<td class="bordered-bottom" rowspan="5">{{ $labor->employee_no }}</td>
				<td class="bordered-bottom" rowspan="5" class="text-center">{{ $labor->name }}</td>
				<td class="bordered-bottom" rowspan="5">{{ $labor->trade->name }}</td>
			</tr>
			<tr>
				<td>Attended</td>
				<?php $attendance_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['attended'] as $key => $attended)
				<td>
					<a class="att_entry_select" data-field="attended" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/attended')}}">{{$attended}}</a>		
				</td>
				@endforeach
				<td>{{ $total[$labor->employee_no]['attended']}}</td>
				<td>{{ $salary[$labor->employee_no]['attended']}}</td>
			</tr>
			<tr>
				<td>Overtime(OT)</td>
				<?php $ot_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['ot'] as $key => $ot)
				<td>
					<a class="att_entry_text" data-field="ot" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/ot')}}">{{$ot}}</a>
				</td>
				@endforeach
				<td>{{$total[$labor->employee_no]['ot']}}</td>
				<td>{{ $salary[$labor->employee_no]['ot']}}</td>
			</tr>
			<tr>
				<td>Bonus OT</td>
				<?php $bot_total = 0; ?>
				@foreach($labor_att[$labor->employee_no]['bot'] as $key => $bot)
				<td>
					<a class="att_entry_text" data-field="bot" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/bot')}}">{{$bot}}</a>
				</td>
				@endforeach
				<td>{{$total[$labor->employee_no]['bot']}}</td>
				<td>{{ $salary[$labor->employee_no]['bot']}}</td>
			</tr>
			<tr>
				<td class="bordered-bottom">Site</td>
				@foreach($labor_att[$labor->employee_no]['site'] as $key => $site)
				<td class="site-row bordered-bottom">
					<a class="att_entry_select" data-field="site" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/site')}}">{{$site}}</a>
				</td>
				@endforeach
				<td class="bordered-bottom"></td>
				<td class="bordered-bottom cell-bold">{{ $salary[$labor->employee_no]['total']}}</td>
			</tr>			
		@endforeach
		</table>
@endif


<div id="dialog-form-text" title="Edit">
 
	{!! Form::open(['id' =>'text-form','class'=>'form-inline']) !!}
			{!! Form::hidden('date',null) !!}
			{!! Form::hidden('id',null) !!}
			{!! Form::hidden('field',null) !!}
			{!! Form::text('text-entry',null,['class'=>'form-control']) !!}		
			{!! Form::submit('Go',['class'=>'btn btn-default']) !!}		
	{!! Form::close() !!}

</div>

<div id="dialog-form-select" title="Edit">
 
	{!! Form::open(['id' =>'select-form','class'=>'form-inline']) !!}
			{!! Form::hidden('date',null) !!}
			{!! Form::hidden('id',null) !!}
			{!! Form::hidden('field',null) !!}
			{!! Form::select('select-entry',[],null,['class'=>'form-control']) !!}
			{!! Form::submit('Go',['class'=>'btn btn-default']) !!}
	{!! Form::close() !!}

</div>

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
		 var rules = {
         'text-entry': {
             number: true
         }
	     };
	     var messages = {
	         'text-entry': {
	             number: "Please enter a number"
	         }
	     };
	     $("#text-form").validate({
	         rules: rules,
	         messages: messages
	     });
		$.ajaxSetup({
		   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
		});
		$('#select-form').submit(function(e){
			e.preventDefault();
			$.ajax({
		      url: 'update',
		      dataType:'json',
		      type: "POST",
		      data: {'entry':$('select[name=select-entry]').val(),'date':$('input[name=date]').val(),'id':$('input[name=id]').val(),'field':$('input[name=field]').val()},
		      success: function(data){
		      	//alert(data);
		      	$('a[data-date='+data.date+'][data-field='+data.field+'][data-id='+data.en+']').html(data.entry);
		      	if(data.result == 0){
			      	$('a[data-date='+data.date+'][data-field=ot][data-id='+data.en+']').html('0');
			      	$('a[data-date='+data.date+'][data-field=bot][data-id='+data.en+']').html('0');
			      	$('a[data-date='+data.date+'][data-field=site][data-id='+data.en+']').html('0');
			    }
			    else if(data.result == 3){
			      	$('a[data-date='+data.date+'][data-field=site][data-id='+data.en+']').html('—');
			    }
		      },
		       error: function () {
			        alert('You can only edit a field under a date of the past.');
			    }
		    });   
		    $( "#dialog-form-select" ).dialog("close");
		    $('select[name=select-entry]').empty();
		});
		$('#text-form').submit(function(e){
			e.preventDefault();
			if($('#text-form').valid()){
	            $.ajax({
			      url: 'update',
			      dataType:'json',
			      type: "POST",
			      data: {'entry':$('input[name=text-entry]').val(),'date':$('input[name=date]').val(),'id':$('input[name=id]').val(),'field':$('input[name=field]').val()},
			      success: function(data){
			      	$('a[data-date='+data.date+'][data-field='+data.field+'][data-id='+data.en+']').html(data.entry);
			      	if(data.result == 1){
			      		$('a[data-date='+data.date+'][data-field=attended][data-id='+data.en+']').html('1');
			      	}
			      },
			       error: function () {
				        alert('You can only edit a field under a date of the past.');
				    }
			    });   
			    $( "#dialog-form-text" ).dialog("close");
			} 		    
		});
		$( "#dialog-form-text" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        Cancel: function() {
		            $( this ).dialog( "close" );
		        }
	      	}
		});
		$( "#dialog-form-select" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        Cancel: function() {
		            $( this ).dialog( "close" );
		            $('select[name=select-entry]').empty();
		        }
	      	}
		});
		
		$("a[class^='att_entry']").click(function(evt) {
			evt.preventDefault();
			if($(this).attr('class') == 'att_entry_select'){
				var entry = $(this).html();
				var entry = entry.replace(/\s/g, "") 
				var date = $(this).attr('data-date');
				var id = $(this).attr('data-id');
				var field = $(this).attr('data-field');
				$.ajax({
					url: 'getselect',
				    dataType:'json',
				    type: "POST",
				    data: {'field':field},
				    success: function(data){
				      	for(var i in data){
				      		if(i == entry){
				      			$('select[name=select-entry]').append("<option selected value='"+i+"'>"+data[i]+"</option>");
				      		}
				      		else{
				      			$('select[name=select-entry]').append("<option value='"+i+"'>"+data[i]+"</option>");
				      		}
				      	}
				    }
				});
				$( "#dialog-form-select" ).dialog( "open" );
				$('input[name=date]').val(date);
				$('input[name=id]').val(id);
				$('input[name=field]').val(field);
			}
			else if($(this).attr('class') == 'att_entry_text'){
				var entry = $(this).html();
				var entry = entry.replace(/\s/g, "") 
				var date = $(this).attr('data-date');
				var id = $(this).attr('data-id');
				var field = $(this).attr('data-field');
				$( "#dialog-form-text" ).dialog( "open" );
				$('input[name=text-entry]').val(entry);
				$('input[name=date]').val(date);
				$('input[name=id]').val(id);
				$('input[name=field]').val(field);
			}
		});
		$('#filter-form').submit(function(){
			$(".container-fluid").fadeOut('1500');
			$('body').html('<div id="dialog-loading" class="text-center"><img src="https://d13yacurqjgara.cloudfront.net/users/12755/screenshots/1037374/hex-loader2.gif"/></div>');
		});
		var fewSeconds = 20;
		$('#btn-make-xls').click(function(){
		    var btn = $(this);
		    btn.before('<img id="exl-status" src="https://pt.ontests.me/static/img/loading.gif" width="200px"/>')
		    btn.hide();
		    setTimeout(function(){
		    	$('#exl-status').hide();
		    	$('#exl-status').remove();
		        btn.fadeIn(300);
		    }, fewSeconds*1000);
		});
	});
</script>
@stop
