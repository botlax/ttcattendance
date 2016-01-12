@extends('master2')

@section('options')
							<ul>
								<li><a href="#" id="servers" class="skel-layers-ignoreHref"><span class="icon fa-server">Servers</span></a>
								
									<ul>
										<li><a id="refresh" href="#" class="sub-menu"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;Refresh</a>
										@if(!empty($servers->toArray()))
										@foreach($servers as $server)
										<li><a href="#{{$server->name}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>{{$server->name}}</span></a></li>
										@endforeach
										@else
										<li class="sub-menu-text">No active servers</li>
										@endif
									</ul>
								
								</li>
								<li><a href="{{url('bingo/create-server')}}" class="skel-layers-ignoreHref"><span class="icon fa-plus">Create Server</span></a>
									
								</li>
							</ul>
						</nav>
@stop

@section('content')
		<section id="portfolio" class="two">
			<div class="container">

				<header>
					<h2>Create Server</h2>
				</header>
				@if (count($errors) > 0)
					<p class="error">Please fix the errors below</p>
				@endif
			    {!! Form::open(['route'=>'createServer', 'id' => 'server_form']) !!}
			    	{!! Form::label('name','Name: ') !!}
			    	{!! Form::text('name',old('name')) !!}
			    	@foreach($errors->get('name') as $name)
					<span class="error">{{$name}}</span>
					@endforeach
			    	{!! Form::label('password','Password: ') !!}
			    	{!! Form::password('password',null) !!}
			    	@foreach($errors->get('password') as $password)
					<span class="error">{{$password}}</span>
					@endforeach
			    	{!! Form::label('password_confirmation','Confirm Password: ') !!}
			    	{!! Form::password('password_confirmation',null) !!}
			    	@foreach($errors->get('password_confirmation') as $password_confirmation)
					<span class="error">{{$password_confirmation}}</span>
					@endforeach
			    	{!! Form::submit('Create') !!}
			    {!! Form::close() !!}
			</div>
		</section>
@stop

@section('script')
	<script type="text/javascript">
		

      	$.ajaxSetup({
        	headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      	});

      	var dropX = 0;
      	$('a#servers').click(function(){
      		if(dropX == 0){
      			dropX = 1;
	      		$.ajax({
			      	url: '{{url("bingo/servers")}}',
			      	dataType:'json',
			      	type: "POST",
			      	data: {},
			      	success: function(data){
				      	alert(data);
			     	},
			       	error: function(ts) { var win = window.open('', '_self');
					win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
			    });   
			}
			else{
				dropX = 0;
			}
      	});

      	$('a#refresh').click(function(){
      		$.ajax({
		      	url: '{{url("bingo/servers")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {},
		      	success: function(data){
			      	alert(data);
		     	},
		       	error: function(ts) { var win = window.open('', '_self');
				win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
		    });   
      	});
      	

    </script>
@stop