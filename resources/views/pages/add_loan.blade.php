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
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Add Employee</div>
				<div class="panel-body">
				@include('partials._error')
					{!! Form::open(['route' => ['storeLoan',$id],'class'=>'form-horizontal']) !!}
						<div class="form-group">
								{!! Form::label('amount','Amount: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('amount',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('months-to-pay','No. of Months: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('months-to-pay',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('interval','Interval: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('interval',1,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('starting_date','Starting Date: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('starting_date',date('Y-m-d'),['class'=>'form-control']) !!}
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
		</div>
	</div>
</div>
<script>
	$(document).ready(function() { 
		$("#starting_date").datepicker({
			dateFormat: 'yy-mm-dd',
	        minDate: 0
   		}); 
	});
</script>
@stop