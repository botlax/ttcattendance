@extends('master')

@section('title')
Users
@stop

@section('content')


<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">Edit {{ $site->name }}</div>
				<div class="panel-body">
				@include('partials._error')
					{!! Form::model($site,['route' => ['updateSite',$site->code],'class'=>'form-horizontal']) !!}

						<div class="form-group">
								{!! Form::label('code','Code: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('code',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('name','Name: ',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::text('name',null,['class'=>'form-control']) !!}
							</div>
						</div>

						<div class="form-group">
								{!! Form::label('user_id','In-charge:',['class'=>'col-md-4 control-label']) !!}
							<div class="col-md-6">
								{!! Form::select('user_id',$users,null,['class'=>'form-control']) !!}
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