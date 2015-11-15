@extends('master')

@section('title')
Users
@stop
@section('css')
<link rel="stylesheet" href="{{url('css/jquery-ui.min.css')}}">
@stop
@section('script')
<script src="{{url('js/jquery-ui.min.js')}}"></script>
@stop
@section('content')


<div class="container-fluid">
	<div class="row">
	@include('partials._error')
	@if(empty($loans->toArray()))
		<p class="text-center"><strong>Employee has no loan.</strong></p>
	@endif
		@foreach($loans as $loan)
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Add Employee<a role="button" href="{{url('employees/'.$id.'/loan/'.$loan->id.'/delete')}}" class="btn btn-danger pull-right">Cancel Loan</a><button id="btn-loan-edit{{$loan->id}}" class="btn btn-success pull-right">Edit</button></div>
				<div class="panel-body">
					{!! Form::model($loan,['route' => ['updateLoan',$id],'class'=>'form-horizontal','id'=>'loan-form'.$loan->id]) !!}
						{!! Form::hidden('id',$loan->id) !!}
						<div class="form-group">
								{!! Form::label('amount','Amount: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('amount',null,['class'=>'form-control','disabled']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('months-to-pay','No. of Months: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('months-to-pay',null,['class'=>'form-control','disabled']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('interval','Interval: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('interval',null,['class'=>'form-control','disabled']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('starting_date','Starting Date: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('starting_date',$loan->starting_date->format('Y-m-d'),['class'=>'form-control','id'=>'starting_date'.$loan->id,'disabled']) !!}
							</div>
						</div>	
						
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button disabled type="submit" id="btn-loan-update" class="btn btn-primary">
									Update
								</button>
							</div>
						</div>
					{!! Form::close() !!}
					<div class="form-group">
							<p class="col-md-4 text-right"><b>Months left:</b></p>
						<div class="col-md-6">
							<p>{{$loanDetails[$loan->id]['months_left']}}</p>
						</div>
					</div>
					<div class="form-group">
							<p class="col-md-4 text-right"><b>Amount left:</b></p>
						<div class="col-md-6">
							<p>{{$loanDetails[$loan->id]['amount_left']}}</p>
						</div>
					</div>
					<div class="form-group">
							<p class="col-md-4 text-right"><b>Total deducted:</b></p>
						<div class="col-md-6">
							<p>{{$loanDetails[$loan->id]['deducted']}}</p>
						</div>
					</div>
					<div class="form-group">
							<p class="col-md-4 text-right"><b>Next deduction:</b></p>
						<div class="col-md-6">
							<p>{{$loanDetails[$loan->id]['next_month']}}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>
<script>
	$(document).ready(function() { 
		@foreach($loans as $loan)
		$("#starting_date{{$loan->id}}").datepicker({
			dateFormat: 'yy-mm-dd',
	        minDate: 0
   		}); 
		@endforeach

		//-------------------------
		(function($) {
		    $.fn.toggleDisabled = function(){
		        return this.each(function(){
		            this.disabled = !this.disabled;
		        });
		    };
		})(jQuery);
		//-------------------------

		
		@foreach($loans as $loan)
		$('#btn-loan-edit{{$loan->id}}').click(function(){
			$(this).toggleClass('btn-danger');
			if($(this).html() == 'Edit'){
				$(this).html('Cancel');
			}else{
				$(this).html('Edit');
			}
			$('#loan-form{{$loan->id}} input').toggleDisabled();
			$('#loan-form{{$loan->id}} #btn-loan-update').toggleDisabled();
			$('#loan-form{{$loan->id}} input[name=amount]').focus().select();
		});
		@endforeach
	});
</script>
@stop