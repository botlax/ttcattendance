@extends('master2')

@section('meta')
<meta name="_token" content="{!! csrf_token() !!}"/>
@stop

@section('options')
							<ul>
								<li><a href="#" id="servers" class="skel-layers-ignoreHref"><span class="icon fa-server">Servers</span></a>
								
									<ul>
										<li><a id="refresh" href="#" class="sub-menu"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
										@if(!empty($servers->toArray()))
										@foreach($servers as $server)
										<li><a href="{{url('/bingo/server/'.$server->id)}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>{{$server->name}}</span></a></li>
										@endforeach
										@endif
									</ul>
								
								</li>
								<li><a href="{{url('bingo/create-server')}}" class="skel-layers-ignoreHref"><span class="icon fa-plus">Create Server</span></a>
									
								</li>
							</ul>
@stop

@section('content')
	<img src="/images/arrow.png" id="arrow">
	<section id="top" class="one dark cover">
		<div class="container">
			<div class="row">
				<section class="8u mobile-padding" id="welcome">
					<header>
						<h2 class="alt">Hi! welcome to <strong>Botlax Bingo</strong>, an online bingo game created by <a href="mailto:lacsinapaul@gmail.com">Botlax</a></h2>
						<p>To start playing, please join to one of the servers on the left pane or create one.</p>
					</header>
				</section>
				<section class="4u" id="kyrkie">
					<img class="flex" src="/images/kyrkie.png">
				</section>
		</div>
	</section>

@stop

@section('script')
	<script type="text/javascript">
		
	$(document).ready(function(){
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
			      		$('#servers').parent().find('ul').html('');
			      		$('#servers').parent().find('ul').append('<li><a id="refresh" href="#" class="sub-menu"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>');
			      		for(var i in data){
				      		$('#servers').parent().find('ul').append('<li><a href="/bingo/server/'+data[i].id+'" class="skel-layers-ignoreHref"><span>'+data[i].name+'</span></a></li>');
				      	}
			     	},
			       	error: function(ts) { var win = window.open('', '_self');
					win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
			    });   
			}
			else{
				dropX = 0;
			}
      	});

      	$(document).on('click','a#refresh',function(){
      		$.ajax({
		      	url: '{{url("bingo/servers")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {},
		      	success: function(data){
		      		$('#servers').parent().find('ul').html('');
		      		$('#servers').parent().find('ul').append('<li><a id="refresh" href="#" class="sub-menu"><i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>');
		      		for(var i in data){
			      		$('#servers').parent().find('ul').append('<li><a href="/bingo/server/'+data[i].id+'" class="skel-layers-ignoreHref"><span>'+data[i].name+'</span></a></li>');
			      	}
				},
		       	error: function(ts) { var win = window.open('', '_self');
				win.document.getElementsByTagName('Body')[0].innerText = ts.responseText; }
		    });   
      	});
	});
      	
      	

    </script>
@stop