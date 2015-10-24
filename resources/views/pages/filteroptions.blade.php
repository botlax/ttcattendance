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

	<div class="form-group">
		{!! Form::label('employee_no','Employee ID: ') !!}
		{!! Form::text('employee_no',null) !!}
	</div>

	<div class="form-group">
		<label for="date-from">From: </label>
		<input type="text" name="date-from" id="date-from" value="{{date('Y-m-d')}}" size="15">
	</div>

	<div class="form-group">
		<label for="date-from">To: </label>
		<input type="text" name="date-to" id="date-to" value="{{date('Y-m-d')}}" size="15">
	</div>

	<div class="form-group">
		{!! Form::label('trade_list[]','Trade:') !!}
		{!! Form::select('trade_list[]',$trades,null,['multiple','id' => 'filter-trades','style'=>'width: 150px']) !!}
	</div>

	<div class="form-group">
		{!! Form::label('site_list[]','Site:') !!}
		{!! Form::select('site_list[]',$sites,null,['multiple','id' => 'filter-sites','style'=>'width: 150px']) !!}
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
	<!--
		<table id="attendance-table">
			<tr>
				<th class="bordered-bottom">ID</th>
				<th class="bordered-bottom">Name</th>
				<th class="bordered-bottom">Trade</th>
				<th class="bordered-bottom"></th>
			@for($dateFrom;$dateFrom<$dateTo;$dateFrom->addDay())
				<th class="bordered-bottom">{{$dateFrom->format('d')}}</th>
			@endfor
			
				<th class="bordered-bottom total-head">Total</th>
				<th class="bordered-bottom salary-head">Salary</th>
			</tr>
		@foreach($labors as $labor)
			<tr class="labor-stripe">
				<td class="bordered-bottom" rowspan="5">{{ $labor->employee_no }}</td>
				<td class="bordered-bottom" rowspan="5" class="text-center">{{ $labor->name }}</td>
				<td class="bordered-bottom" rowspan="5">{{ $labor->trade->name }}</td>
			</tr>
			<tr>
				<td>Attended</td>
				
				@foreach($labor_att[$labor->employee_no]['attended'] as $key => $attended)
				<td>
					<a class="att_entry_select" data-field="attended" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/attended')}}">{{$attended}}</a>		
				</td>
				@endforeach
				<td>{{ $total[$labor->employee_no]['attended']}}</td>
				<td>{{ $salary[$labor->employee_no]['attended']}}</td>
			</tr>
			<tr class="table-stripe">
				<td>Overtime(OT)</td>
				
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

				@foreach($labor_att[$labor->employee_no]['bot'] as $key => $bot)
				<td>
					<a class="att_entry_text" data-field="bot" data-date="{{$key}}" data-id="{{$labor->id}}" href="{{url('attendance/'.$key.'/'.$labor->employee_no.'/bot')}}">{{$bot}}</a>
				</td>
				@endforeach
				<td>{{$total[$labor->employee_no]['bot']}}</td>
				<td>{{ $salary[$labor->employee_no]['bot']}}</td>
			</tr>
			<tr class="table-stripe">
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
		-->
@endif
	<div class="text-left" style="height:50px">
		<a role="button" class="btn btn-default" id="btn-make-xls" href="{{$_SERVER['REQUEST_URI']}}&makesheet=1"></a>
	</div>

	<div id="attendance-table-wrap">
		<table id="attendance-table">
			
		</table>
	</div>

<div id="dialog-form-option" title="Filter">
 

</div>

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
		$("#filter-trades").select2({
			placeholder: 'Select trade'
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
		      url: 'attendance/update',
		      dataType:'json',
		      type: "POST",
		      data: {'entry':$('select[name=select-entry]').val(),'date':$('input[name=date]').val(),'id':$('input[name=id]').val(),'field':$('input[name=field]').val()},
		      success: function(data){
		      	//alert(data);
		      	if(data.result != 5){
			      	$('a[data-date='+data.date+'][data-field='+data.field+'][data-id='+data.en+']').html(data.entry);
			      	if(data.result == 0){
				      	$('a[data-date='+data.date+'][data-field=ot][data-id='+data.en+']').html('—');
				      	$('a[data-date='+data.date+'][data-field=bot][data-id='+data.en+']').html('—');
				      	$('a[data-date='+data.date+'][data-field=site][data-id='+data.en+']').html('—');
				    }
				    else if(data.result == 3){
				      	$('a[data-date='+data.date+'][data-field=site][data-id='+data.en+']').html('—');
				    }
				    else if(data.result == 2){
				    	$('a[data-date='+data.date+'][data-field=ot][data-id='+data.en+']').html('0');
				      	$('a[data-date='+data.date+'][data-field=bot][data-id='+data.en+']').html('0');
				    }
				    else if(data.result == 6){
				    	$('a[data-date='+data.date+'][data-field=site][data-id='+data.en+']').html(data.entry);
				    }
				}
			    else{
			      	alert('You cannot update a field beyond today date.');
			    }
		      },
		       error: function(ts) { var win = window.open('', '_self');
					win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
		    });   
		    $( "#dialog-form-select" ).dialog("close");
		    $('select[name=select-entry]').empty();
		});
		$('#text-form').submit(function(e){
			e.preventDefault();
			if($('#text-form').valid()){
	            $.ajax({
			      url: '{{url("attendance/update")}}',
			      dataType:'json',
			      type: "POST",
			      data: {'entry':$('input[name=text-entry]').val(),'date':$('input[name=date]').val(),'id':$('input[name=id]').val(),'field':$('input[name=field]').val()},
			      success: function(data){
			      	if(data.result != 5){
				      	$('a[data-date='+data.date+'][data-field='+data.field+'][data-id='+data.en+']').html(data.entry);
				      	if(data.result == 1){
				      		$('a[data-date='+data.date+'][data-field=attended][data-id='+data.en+']').html('1');
				      	}
				    }
				    else{
				    	alert('You cannot update a field beyond today date.');
				    }
			      },
			       error: function(ts) { var win = window.open('', '_self');
					win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
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
		
		$(document).on('click',"a[class^='att_entry']",function(evt) {
			evt.preventDefault();
			if($(this).attr('class') == 'att_entry_select'){
				var entry = $(this).html();
				var entry = entry.replace(/\s/g, "") 
				var date = $(this).attr('data-date');
				var id = $(this).attr('data-id');
				var field = $(this).attr('data-field');
				$.ajax({
					url: 'attendance/getselect',
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


		$("#date-from").datepicker({
			dateFormat: 'yy-mm-dd',
	        numberOfMonths: 2,
	        onSelect: function(selected) {
	        	$("#date-to").datepicker("option","minDate", selected);
	        }
   		});
	    $("#date-to").datepicker({
	    	dateFormat: 'yy-mm-dd',
	        numberOfMonths: 2,
	        onSelect: function(selected) {
	            $("#date-from").datepicker("option","maxDate", selected);
	        }
	    });

	    var skip,take,filterComplete,view_deleted,view_absent,date_from,date_to,employee_no,site_list,trade_list;
		$('#filter-form').submit(function(e){
			e.preventDefault();
			skip = 0; take = 20; filterComplete = false;

			if($('input[name=view-deleted').is(':checked')){
				view_deleted = $('input[name=view-deleted').val();
			}
			else{
				view_deleted = 0;
			}
			if($('input[name=view-absent').is(':checked')){
				view_absent = $('input[name=view-absent').val();
			}
			else{
				view_absent = 0;
			}
			date_from = $('#date-from').val();
			date_to = $('#date-to').val();
			employee_no = $('#employee_no').val();
			site_list = $('#filter-sites').val();
			trade_list = $('#filter-trades').val();
			//alert(view_absent);

			$.ajax({
				url: 'attendance/filter',
			    dataType:'json',
			    type: "POST",
			    data: {'view_deleted':view_deleted,'view_absent':view_absent,'date_from':date_from,'date_to':date_to,'employee_no':employee_no,'site_list':site_list,'trade_list':trade_list,'skip':skip,'take':take},
			    success: function(data){
			    	$('#attendance-table').html('');
			    	//$('body').append(data);

			    	var dateFrom = new Date(data.dateFrom);
					var dateTo = new Date(data.dateTo);

					$('#attendance-table').append('<tr id="table-head"></tr>');

					$('#table-head').append('<th class="bordered-bottom">ID</th><th class="bordered-bottom">Name</th><th class="bordered-bottom">Trade</th><th class="bordered-bottom"></th>');

					for (dateFrom; dateFrom <= dateTo; dateFrom.setDate(dateFrom.getDate() + 1)) {
					    $('#table-head').append('<th class="bordered-bottom">'+("0" + dateFrom.getDate()).slice(-2)+'</th>');
					}
					$('#table-head').append('<th class="bordered-bottom total-head">Total</th><th class="bordered-bottom salary-head">Salary</th>');

			    	//a complicated process---------------------------------------------

			    	for(var i in data.labor){

			    		$('#attendance-table').append('<tr class="labor-stripe" id="labor'+data.labor[i].employee_no+'"></tr>');

			    		$('#labor'+data.labor[i].employee_no).append('<td class="bordered-bottom" rowspan="5">'+data.labor[i].employee_no+'</td><td class="bordered-bottom" rowspan="5" class="text-center">'+data.labor[i].name+'</td><td class="bordered-bottom" rowspan="5">'+data.trade[i]+'</td>');
			    		
			    		//attended
			    		$('#attendance-table').append('<tr id="att'+data.labor[i].employee_no+'"></tr>');

			    		$('#att'+data.labor[i].employee_no).append('<td>Attended</td>');

			    		for(var att in data.labor_att[data.labor[i].employee_no]['attended']){
			    			$('#att'+data.labor[i].employee_no).append('<td><a class="att_entry_select" data-field="attended" data-date="'+att+'" data-id="'+data.labor[i].id+'" href="attendance/'+att+'/'+data.labor[i].employee_no+'/attended">'+data.labor_att[data.labor[i].employee_no]['attended'][att]+'</a></td>');
			    		}
			    		$('#att'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['attended']+'</td>');
			    		$('#att'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['attended']+'</td>');

			    		//overtime
			    		$('#attendance-table').append('<tr class="labor-stripe" id="ot'+data.labor[i].employee_no+'"></tr>');

			    		$('#ot'+data.labor[i].employee_no).append('<td>Overtime (OT)</td>');

			    		for(var ot in data.labor_att[data.labor[i].employee_no]['ot']){
			    			$('#ot'+data.labor[i].employee_no).append('<td><a class="att_entry_text" data-field="ot" data-date="'+ot+'" data-id="'+data.labor[i].id+'" href="attendance/'+ot+'/'+data.labor[i].employee_no+'/ot">'+data.labor_att[data.labor[i].employee_no]['ot'][ot]+'</a></td>');
			    		}
			    		$('#ot'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['ot']+'</td>');
			    		$('#ot'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['ot']+'</td>');

			    		//bonus overtime
			    		$('#attendance-table').append('<tr id="bot'+data.labor[i].employee_no+'"></tr>');

			    		$('#bot'+data.labor[i].employee_no).append('<td>Bonus OT</td>');

			    		for(var bot in data.labor_att[data.labor[i].employee_no]['bot']){
			    			$('#bot'+data.labor[i].employee_no).append('<td><a class="att_entry_text" data-field="bot" data-date="'+bot+'" data-id="'+data.labor[i].id+'" href="attendance/'+bot+'/'+data.labor[i].employee_no+'/bot">'+data.labor_att[data.labor[i].employee_no]['bot'][bot]+'</a></td>');
			    		}
			    		$('#bot'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['bot']+'</td>');
			    		$('#bot'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['bot']+'</td>');

			    		//site
			    		$('#attendance-table').append('<tr class="labor-stripe" id="site'+data.labor[i].employee_no+'"></tr>');

			    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom">Site</td>');

			    		for(var site in data.labor_att[data.labor[i].employee_no]['site']){
			    			$('#site'+data.labor[i].employee_no).append('<td class="site-row bordered-bottom"><a class="att_entry_select" data-field="site" data-date="'+site+'" data-id="'+data.labor[i].id+'" href="attendance/'+site+'/'+data.labor[i].employee_no+'/site">'+data.labor_att[data.labor[i].employee_no]['site'][site]+'</a></td>');
			    		}
			    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom"></td>');
			    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom cell-bold">'+data.salary[data.labor[i].employee_no]['total']+'</td>');
			    	}
			    	//end of a really complicated process-------------------------------
			    	skip = skip+20;
			    },
			    error: function(ts) { var win = window.open('', '_self');
					win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
			});
		});

		//ajax filter
		$('#attendance-table-wrap').on('scroll', function() {

	        if($(this).scrollTop() + $(this).innerHeight()-15 >= this.scrollHeight) {
	        	//alert(date_from);
	        	if(!filterComplete){
		            
			        $.ajax({
						url: 'attendance/filter',
					    dataType:'json',
					    type: "POST",
					    data: {'view_deleted':view_deleted,'view_absent':view_absent,'date_from':date_from,'date_to':date_to,'employee_no':employee_no,'site_list':site_list,'trade_list':trade_list,'skip':skip,'take':take},
					    beforeSend: function ( xhr ) {
			               //Add your image loader here
			            },
					    success: function(data){
					    	if(data.filterComplete != 'true'){
					    		//alert(data.filterComplete);
					      		skip = skip+20;

					      		//another complicated process------------------------------------
					      		for(var i in data.labor){

						    		$('#attendance-table').append('<tr class="labor-stripe" id="labor'+data.labor[i].employee_no+'"></tr>');

						    		$('#labor'+data.labor[i].employee_no).append('<td class="bordered-bottom" rowspan="5">'+data.labor[i].employee_no+'</td><td class="bordered-bottom" rowspan="5" class="text-center">'+data.labor[i].name+'</td><td class="bordered-bottom" rowspan="5">'+data.labor[i].trade_id+'</td>');
						    		
						    		//attended
						    		$('#attendance-table').append('<tr id="att'+data.labor[i].employee_no+'"></tr>');

						    		$('#att'+data.labor[i].employee_no).append('<td>Attended</td>');

						    		for(var att in data.labor_att[data.labor[i].employee_no]['attended']){
						    			$('#att'+data.labor[i].employee_no).append('<td><a class="att_entry_select" data-field="attended" data-date="'+att+'" data-id="'+data.labor[i].id+'" href="attendance/'+att+'/'+data.labor[i].employee_no+'/attended">'+data.labor_att[data.labor[i].employee_no]['attended'][att]+'</a></td>');
						    		}
						    		$('#att'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['attended']+'</td>');
						    		$('#att'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['attended']+'</td>');

						    		//overtime
						    		$('#attendance-table').append('<tr class="labor-stripe" id="ot'+data.labor[i].employee_no+'"></tr>');

						    		$('#ot'+data.labor[i].employee_no).append('<td>Overtime (OT)</td>');

						    		for(var ot in data.labor_att[data.labor[i].employee_no]['ot']){
						    			$('#ot'+data.labor[i].employee_no).append('<td><a class="att_entry_text" data-field="ot" data-date="'+ot+'" data-id="'+data.labor[i].id+'" href="attendance/'+ot+'/'+data.labor[i].employee_no+'/ot">'+data.labor_att[data.labor[i].employee_no]['ot'][ot]+'</a></td>');
						    		}
						    		$('#ot'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['ot']+'</td>');
						    		$('#ot'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['ot']+'</td>');

						    		//bonus overtime
						    		$('#attendance-table').append('<tr id="bot'+data.labor[i].employee_no+'"></tr>');

						    		$('#bot'+data.labor[i].employee_no).append('<td>Bonus OT</td>');

						    		for(var bot in data.labor_att[data.labor[i].employee_no]['bot']){
						    			$('#bot'+data.labor[i].employee_no).append('<td><a class="att_entry_text" data-field="bot" data-date="'+bot+'" data-id="'+data.labor[i].id+'" href="attendance/'+bot+'/'+data.labor[i].employee_no+'/bot">'+data.labor_att[data.labor[i].employee_no]['bot'][bot]+'</a></td>');
						    		}
						    		$('#bot'+data.labor[i].employee_no).append('<td>'+data.total[data.labor[i].employee_no]['bot']+'</td>');
						    		$('#bot'+data.labor[i].employee_no).append('<td>'+data.salary[data.labor[i].employee_no]['bot']+'</td>');

						    		//site
						    		$('#attendance-table').append('<tr class="labor-stripe" id="site'+data.labor[i].employee_no+'"></tr>');

						    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom">Site</td>');

						    		for(var site in data.labor_att[data.labor[i].employee_no]['site']){
						    			$('#site'+data.labor[i].employee_no).append('<td class="site-row bordered-bottom"><a class="att_entry_select" data-field="site" data-date="'+site+'" data-id="'+data.labor[i].id+'" href="attendance/'+site+'/'+data.labor[i].employee_no+'/site">'+data.labor_att[data.labor[i].employee_no]['site'][site]+'</a></td>');
						    		}
						    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom"></td>');
						    		$('#site'+data.labor[i].employee_no).append('<td class="bordered-bottom cell-bold">'+data.salary[data.labor[i].employee_no]['total']+'</td>');
						    	}
						    	//end of a really complicat

					      	}
					      	else{
					      		filterComplete = true;
					      		//alert(filterComplete);
					      	}
					    },
					    error: function(ts) { var win = window.open('', '_self');
						win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
					});
			    
			    	//alert('yes!');
			    }
	        }
	    })

	});
</script>
@stop
