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
Add Attendance
@stop

@section('content')

<h1 class="text-center"> <small>Attendance for {{date('M d, Y')}} </small></h1>
@include('partials._error')
<div class="row">
	<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
		{!! Form::open(['id'=>'search-form']) !!}
			
			{!! Form::text('id',null,['class'=>'form-control','placeholder'=>'Emp. ID...','id'=>'labor-search']) !!}
		
			{!! Form::submit('Go',['class'=>'btn btn-default','id'=>'att-list-btn-search','style'=>'display:none']) !!}

		{!! Form::close() !!}
	</div>
	
	<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3" id="tabs">
		<ul>
		    <li><a id="unfilled-tab" href="#unfilled"><img src="/images/glyph-group.png"></a></li>
		    <li><a id="filled-tab" href="#filled"><img src="/images/glyph-add.png"></a></li>
	  	</ul>
		<div id="unfilled">
			<div id="unfilled-options">
			<a id="view-all-unfilled" href="#"><img src="/images/glyph-eye.png"></a>
			<a id="hide-unfilled" href="#"><img src="/images/glyph-eye-remove.png"></a>
			</div>
	    </div>
		<div id="filled">
			<div id="filled-options">
			<a id="view-all-filled" href="#"><img src="/images/glyph-eye.png"></a>
			<a id="hide-filled" href="#"><img src="/images/glyph-eye-remove.png"></a>
			<a id="lock" href="{{url('attendance/list/'.$site.'/lock')}}">Submit <img src="/images/glyph-lock.png"></a>
		</div>
		</div>
	</div>
</div>

<div id="lock-dialog" title="Edit">Are you sure you want to submit and <strong>LOCK</strong> this attendance?</div>
	



<script>
	$(document).ready(function(){

		var mode = 'unfilled';
		$( "#tabs" ).tabs();
		$('#unfilled-tab').click(function(){ mode = 'unfilled' });
		$('#filled-tab').click(function(){ mode = 'filled' });

		// search function
		var typingTimer;                
		var doneTypingInterval = 1000;
		var input = $('#labor-search');

		input.on('keyup', function () {
		  clearTimeout(typingTimer);
		  typingTimer = setTimeout(doneTyping, doneTypingInterval);
		});

		input.on('keydown', function () {
		  clearTimeout(typingTimer);
		});

		$.ajaxSetup({
		   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
		});

		function doneTyping () {
			input = $('#labor-search').val();

			if(mode == 'unfilled'){
				if($.trim(input) != ''){
					$.ajax({
					      url: '{{url("attendance/searchUnfilled")}}',
					      dataType:'json',
					      type: "POST",
					      data: {'input':input},
					      success: function(data){
					      		$('#unfilled').children().not('#unfilled-options').remove();
					      	for(var i in data){
					      		$('#unfilled').append('<div data-id="'+i+'"> <a role="button" data-site="{{$site}}" data-id="'+i+'" class="btn btn-default btn-att">'+i+' '+data[i]+'</a> <input disabled="true" type="text" name="ot" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button disabled="true" data-id="'+i+'" data-site="{{$site}}" role="submit" class="btn btn-primary submit-att"><img src="/images/glyph-check.png"></button> </div>');
					      		//alert(i+'-'+data[i]);
					      	}
					      },
					       error: function(ts) { alert('whoops!'); }
					});
				}
				else{
					$('#unfilled').children().not('#unfilled-options').remove();
				}
			}
			else{
				if($.trim(input) != ''){
					$.ajax({
					      url: '{{url("attendance/searchFilled")}}',
					      dataType:'json',
					      type: "POST",
					      data: {'input':input,'site':'{{$site}}'},
					      success: function(data){
					      		$('#filled').children().not('#filled-options').remove();
						      	for(var i in data){
						      		$('#filled').append('<div data-id="'+i+'"> <span class="labor-name">'+i+'('+data[i].name+')</span> <input disabled="true" type="text" name="ot" value="'+data[i].ot+'" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" value="'+data[i].bot+'" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button data-id="'+i+'" class="btn btn-default btn-edit"><img src="/images/glyph-edit.png"></button> <button data-id="'+i+'" class="btn btn-default btn-remove"><img src="/images/glyph-remove.png"></button> </div>');
						      		//alert(i+'-'+data[i]);
						      	}
					      },
					       error: function(ts) { alert('whoops!'); }
					});
				}
				else{
					$('#filled').children().not('#filled-options').remove();
				}
			}
		}

		//submit search string
		$('#search-form').submit(function(e){
			e.preventDefault();
			if(mode == 'unfilled'){
				if($.trim(input) != ''){
					$.ajax({
					      url: '{{url("attendance/searchUnfilled")}}',
					      dataType:'json',
					      type: "POST",
					      data: {'input':input},
					      success: function(data){
					      		$('#unfilled').children().not('#unfilled-options').remove();
					      	for(var i in data){
					      		$('#unfilled').append('<div data-id="'+i+'"> <a role="button" data-site="{{$site}}" data-id="'+i+'" class="btn btn-default btn-att">'+i+' '+data[i]+'</a> <input disabled="true" type="text" name="ot" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button data-id="'+i+'" data-site="{{$site}}" role="submit" class="btn btn-primary submit-att"><img src="/images/glyph-check.png"></button> </div>');
					      		//alert(i+'-'+data[i]);
					      	}
					      },
					       error: function(ts) { alert('whoops!'); }
					});
				}
				else{
					$('#unfilled').children().not('#unfilled-options').remove();
				}
			}
			else{
				if($.trim(input) != ''){
					$.ajax({
					      url: '{{url("attendance/searchFilled")}}',
					      dataType:'json',
					      type: "POST",
					      data: {'input':input},
					      success: function(data){
					      		$('#filled').children().not('#filled-options').remove();
						      	for(var i in data){
						      		$('#filled').append('<div data-id="'+i+'"> <span class="labor-name">'+i+' '+data[i].name+'</span> <input disabled="true" type="text" name="ot" value="'+data[i].ot+'" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" value="'+data[i].bot+'" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button data-id="'+i+'" class="btn btn-default btn-edit"><img src="/images/glyph-edit.png"></button> <button data-id="'+i+'" class="btn btn-default btn-remove"><img src="/images/glyph-remove.png"></button> </div>');
						      		//alert(i+'-'+data[i]);
						      	}
					      },
					       error: function(ts) { alert('whoops!'); }
					});
				}
				else{
					$('#filled').children().not('#filled-options').remove();
				}
			}
		});

		/////////////////////////////
		(function($) {
		    $.fn.toggleDisabled = function(){
		        return this.each(function(){
		            this.disabled = !this.disabled;
		        });
		    };
		})(jQuery);
		////////////////////////////


		//view all unfilled employees
		$('#view-all-unfilled').click(function(){
			$.ajax({
			      url: '{{url("attendance/viewallunfilled")}}',
			      dataType:'json',
			      type: "POST",
			      success: function(data){
				      		$('#unfilled').children().not('#unfilled-options').remove();
				      	for(var i in data){
				      		$('#unfilled').append('<div data-id="'+i+'"> <a role="button" data-site="{{$site}}" data-id="'+i+'" class="btn btn-default btn-att">'+i+' '+data[i]+'</a> <input disabled="true" type="text" name="ot" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button disabled="true" data-id="'+i+'" data-site="{{$site}}" role="submit" class="btn btn-primary submit-att"><img src="/images/glyph-check.png"></button> </div>');
				      		//alert(i+'-'+data[i]);
				      	}
				  },
			       error: function(ts) { alert('whoops!'); }
			});
		});

		//view all filled employees
		$('#view-all-filled').click(function(){
			$.ajax({
			      url: '{{url("attendance/viewallfilled")}}',
			      dataType:'json',
			      type: "POST",
			      success: function(data){
				      		$('#filled').children().not('#filled-options').remove();
				      	for(var i in data){
				      		$('#filled').append('<div data-id="'+i+'"> <span class="labor-name">'+i+'('+data[i].name+')</span> <input disabled="true" type="text" name="ot" value="'+data[i].ot+'" placeholder="ot" data-id="'+i+'" data-site="{{$site}}"/> <input disabled="true" type="text" name="bot" value="'+data[i].bot+'" placeholder="bot" data-id="'+i+'" data-site="{{$site}}"/> <button data-id="'+i+'" class="btn btn-default btn-edit"><img src="/images/glyph-edit.png"></button> <button data-id="'+i+'" class="btn btn-default btn-remove"><img src="/images/glyph-remove.png"></button> </div>');
				      		//alert(i+'-'+data[i]);
				      	}
				  },
			       error: function(ts) { alert('whoops!'); }
			});
		});

		//hide unfilled
		$('#hide-unfilled').click(function(){
			$('#unfilled').children().not('#unfilled-options').remove();
		});

		//hide filled
		$('#hide-filled').click(function(){
			$('#filled').children().not('#filled-options').remove();
		});

		//toggle text input and button
		$(document).on('click','a.btn-att',function(){
			var id = $(this).attr('data-id');

			$(this).toggleClass('btn-primary');
			$('input[name=ot][data-id='+id+']').toggleDisabled();
			$('input[name=bot][data-id='+id+']').toggleDisabled();
			$('button[data-id='+id+']').toggleDisabled();

			//alert($(this).attr('data-present'));
		});

		//edit or remove
		var otEntry;
		var botEntry;

		//enable editing
		$(document).on('click','.btn-edit',function(){
			var id = $(this).attr('data-id');

			$('.btn-edit,.btn-remove').attr('disabled','true');

			$('input[name=ot][data-id='+id+']').toggleDisabled();
			$('input[name=ot][data-id='+id+']').focus();
			$('input[name=bot][data-id='+id+']').toggleDisabled();

			otEntry = $('input[name=ot][data-id='+id+']').val()
			botEntry = $('input[name=bot][data-id='+id+']').val()

			$(this).next().remove();
			$(this).remove();

			$('div[data-id='+id+']').append('<button data-id="'+id+'" class="btn btn-success done"><img src="/images/glyph-check.png"></button> <button data-id="'+id+'" class="btn btn-danger cancel"><img src="/images/glyph-close.png"></button>');

		});

		//cancel edit
		$(document).on('click','.cancel', function(){
			var id = $(this).attr('data-id');

			$('input[name=ot][data-id='+id+']').val(otEntry);
			$('input[name=bot][data-id='+id+']').val(botEntry);
			$('input[name=ot][data-id='+id+']').attr('disabled','true');
			$('input[name=bot][data-id='+id+']').attr('disabled','true');
			
			$('.done[data-id='+id+']').remove();
			$('.cancel[data-id='+id+']').remove();

			$('div[data-id='+id+']').append('<button data-id="'+id+'" class="btn btn-default btn-edit"><img src="/images/glyph-edit.png"></button> <button data-id="'+id+'" class="btn btn-default btn-remove"><img src="/images/glyph-remove.png"></button>');
			
			$('.btn-edit,.btn-remove').attr('disabled',false);
		});

		//done editing
		$(document).on('click','.done', function(){
			var id = $(this).attr('data-id');
			var ot = $('input[name=ot][data-id='+id+']').val();
			var bot = $('input[name=bot][data-id='+id+']').val();
			if((ot == '' && bot == '') || (($.isNumeric(ot) && ot != '') && ($.isNumeric(bot) && bot != ''))){
				$.ajax({
				      url: '{{url("attendance/editattendance")}}',
				      dataType:'json',
				      type: "POST",
				      data: {'id':id,'ot':ot,'bot':bot},
				      success: function(data){
				      },
				      error: function(ts) { $('#labor-search').val(ts.responseText); }
				});

				$('input[name=ot][data-id='+id+']').attr('disabled','true');
				$('input[name=bot][data-id='+id+']').attr('disabled','true');
				
				$('.done[data-id='+id+']').remove();
				$('.cancel[data-id='+id+']').remove();

				$('div[data-id='+id+']').append('<button data-id="'+id+'" class="btn btn-default btn-edit"><img src="/images/glyph-edit.png"></button> <button data-id="'+id+'" class="btn btn-default btn-remove"><img src="/images/glyph-remove.png"></button>');
				
				$('.btn-edit,.btn-remove').attr('disabled',false);
			}
			else{
				alert('Please enter a number.');
			}
		});

		//remove entry
		$(document).on('click','.btn-remove', function(){
			var id = $(this).attr('data-id');
			$.ajax({
			      url: '{{url("attendance/deleteattendance")}}',
			      dataType:'text',
			      type: "POST",
			      data: {'id':id},
			      success: function(data){
			      },
			      error: function(ts) { $('#labor-search').val(ts.responseText); }
			});

			$('div[data-id='+id+']').remove();
		});

		//add attendance
		$(document).on('click','button.submit-att',function(){
			var employeeId = $(this).attr('data-id');
			var ot = $.trim($('input[name=ot][data-id='+employeeId+']').val());
			var bot = $.trim($('input[name=bot][data-id='+employeeId+']').val());
			var site = $(this).attr('data-site');
			var att = $('a[data-id='+employeeId+']').attr('data-present');
			if((ot == '' && bot == '') || (($.isNumeric(ot) && ot != '') && ($.isNumeric(bot) && bot != ''))){
				$.ajax({
				      url: '{{url("attendance/addattendance")}}',
				      dataType:'text',
				      type: "POST",
				      data: {'id':employeeId,'site':site,'ot':ot,'bot':bot,'att':att},
				      success: function(data){
				      		$('div[data-id='+data+']').remove();
				      },
				      error: function(ts) { $('#labor-search').val(ts.responseText); }
				});
			}
			else{
				alert('Please enter a number.');
			}
		});


		$( "#lock-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		    	'Yes': function(){
		    		window.location.replace('{{url("attendance/list/".$site."/lock")}}');
		    	},
		        Cancel: function() {
		            $( this ).dialog( "close" );
		        }
	      	}
		});

		$('#lock').click(function(e){
			e.preventDefault();
			$( "#lock-dialog" ).dialog( "open" );

		});

	});
</script>

@stop

