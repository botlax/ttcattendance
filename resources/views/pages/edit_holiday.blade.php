@extends('master')

@section('title')
Add Holiday
@stop

@section('content')


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Add Holiday</div>
				<div class="panel-body">
				@include('partials._error')
					{!! Form::model($holiday,['route' => ['updateHoliday',$holiday->id],'class'=>'form-horizontal']) !!}

						<div class="form-group">
								{!! Form::label('holidate','Date: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::date('holidate',$holiday->holidate->format('Y-m-d'),['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Update
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@stop