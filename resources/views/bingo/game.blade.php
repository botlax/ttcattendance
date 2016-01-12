@extends('master2')

@section('options')
							<ul>
								<li><a id="options" class="skel-layers-ignoreHref"><span class="icon fa-cog">Options</span></a>
									
									<ul>
										@if(session('status') == 'admin')
										<li><a href="#" id="portfolio-link" class="skel-layers-ignoreHref"><span>Cancel Server</span></a></li>
										<li><a href="{{url('/bingo/server/'.$id.'/new-game')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>New Game</span></a></li>
										@else
										<li><a href="#" id="portfolio-link" class="skel-layers-ignoreHref"><span>Leave Server</span></a></li>
										@endif
									</ul>
								
								</li>
								<li><a href="{{url('bingo/create-server')}}" class="skel-layers-ignoreHref"><span class="icon fa-plus">Create Server</span></a>
									
								</li>
							</ul>
@stop

@section('content')
		<section id="portfolio" class="two">
			<div class="container">

				<header>
					<h2>Portfolio</h2>
				</header>
				@if (count($errors) > 0)
					<p class="error">Please fix the errors below</p>
				@endif
			    {!! Form::open(['route'=>['postNewGame',$id], 'id' => 'server_form']) !!}
			    	{!! Form::label('mode','Mode: ') !!}
					{!! Form::select('mode',['Normal','Jackpot'],null) !!}
					@foreach($errors->get('password_confirmation') as $password_confirmation)
					<span class="error">{{$password_confirmation}}</span>
					@endforeach
					{!! Form::label('winners','No. of winner/s: ') !!}
					{!! Form::select('winners',$winners,null) !!}
					@foreach($errors->get('password_confirmation') as $password_confirmation)
					<span class="error">{{$password_confirmation}}</span>
					@endforeach
					{!! Form::label('baller','Baller: ') !!}
					{!! Form::select('baller',$players,null) !!}
					@foreach($errors->get('password_confirmation') as $password_confirmation)
					<span class="error">{{$password_confirmation}}</span>
					@endforeach
			    	{!! Form::submit('Create') !!}
			    {!! Form::close() !!}
			</div>
		</section>
@stop

@section('script')
	
@stop