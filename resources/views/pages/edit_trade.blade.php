@extends('master')

@section('title')
Users
@stop

@section('content')


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Edit {{ $trade->name }}</div>
				<div class="panel-body">
				@include('partials._error')
					{!! Form::model($trade,['route' => ['updateTrade',$trade->name]]) !!}

						<div class="form-group">
								{!! Form::label('name','Name: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('name',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Update
								</button>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@stop