@extends('master')

@section('title')
Users
@stop

@section('content')


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Add Employee</div>
				<div class="panel-body">
				@include('partials._error')
					{!! Form::open(['route' => 'storeLabor','class'=>'form-horizontal','files' => true]) !!}
						{!! Form::hidden('deleted','false') !!}
						<div class="form-group">
								{!! Form::label('employee_no','Employee ID: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('employee_no',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('name','Name: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('name',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('trade_id','Trade:',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::select('trade_id',$trades,null,['id'=>'employee-trade','style'=>'width: 130px']) !!}
							</div>						
						</div>

						<div class="form-group">
								{!! Form::label('site_id','Site:',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::select('site_id',$sites,null,['id'=>'employee-site','style'=>'width: 130px']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('labor_photo','Photo:',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::file('labor_photo',['class'=>'form-control','enctype'=>'multipart/form-data']) !!}
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
		$("#employee-site").select2();
		$("#employee-trade").select2();  
	});
</script>
@stop