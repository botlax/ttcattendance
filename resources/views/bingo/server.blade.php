@extends('master2')

@section('meta')
<meta name="_token" content="{!! csrf_token() !!}"/>
@stop

@section('css')
<link rel="stylesheet" href="{{url('css/jquery-ui.min.css')}}">
@stop

@section('options')
							<ul>
								<li><a id="options" class="skel-layers-ignoreHref"><span class="icon fa-cog">Options</span></a>
									
									<ul>
									@if($server->mode == '' || $server->mode == '0')
										@if(session('status') == 'admin')
										<li><a href="{{url('bingo/server/'.$id.'/close')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>Cancel Server</span></a></li>
										<li><a href="{{url('/bingo/server/'.$id.'/new-game')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>New Game</span></a></li>
										@else
										<li><a href="{{url('bingo/server/'.$id.'/leave')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>Leave Server</span></a></li>
										@endif
									@else
										@if(session('status') == 'admin')
										<li><a href="{{url('bingo/server/'.$id.'/close')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>Cancel Server</span></a></li>
										<li><a href="{{url('bingo/server/'.$id.'/cancel')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>Cancel Game</span></a></li>
										@else
										<li><a href="{{url('bingo/server/'.$id.'/leave')}}" id="portfolio-link" class="skel-layers-ignoreHref"><span>Leave Server</span></a></li>
										@endif
									@endif
									</ul>
								
								</li>
							</ul>
@stop

@section('content')
	<section id="game-opt" class="two small-padding">
		<div class="container">
			<div class="row">
				<div class="12u">
					<section id="ball-list-wrap">
						<ul id="ball-list"></ul>
					</section>
				</div>
			</div>
		</div>
	</section>
	<section id="game-proper" class="one dark">
		<div class="container">
			@if($server->mode == '' || $server->mode == '0')
				
					<div class="row">
						<div class="4u -1u 6u(narrower) full-mobile">
							<section>
								<h3>Normal Mode</h3>
								<img src="/images/normal.gif" class="flex">
							</section>
						</div>
						<div class="4u -2u 6u(narrower) full-mobile">
							<section>
								<h3>Jackpot Mode</h3>
								<img src="/images/jackpot.gif" class="flex">
							</section>
						</div>
					</div>
				
			@else
				@if(session('status') == 'baller' || session('status') == 'admin')
					<button id="ball">ball</button>
				@endif
				@if(session('status') == 'admin' && $server->start == 'pending')
				
					<div id="card-opt">
						<p>How many Cards?</p>
						<button>1</button>
						<button>2</button>
						<button>3</button>
						<button>4</button>
					</div>
				@else
				
				@endif
			@endif

		</div>
	</section>

	<div id="card-dialog" title="Server Message">The game is about to start. How many cards do you want to use?</div>
	<div id="late-dialog" title="Server Message">The game is currently ongoing. Kindly wait for the next round.</div>
	<div id="cancel-dialog" title="Server Message">The game has been cancelled by the admin.</div>
	<div id="incomplete-dialog" title="Server Message">Some players haven't picked a card yet. Are you sure you want to start the game?</div>
	<div id="gameover-dialog" title="Server Message">GAME OVER!</div>
@stop

@section('script')
	<script src="{{url('js/jquery-ui.min.js')}}"></script>
	<script src="{{url('assets/js/jquery.kinetic.min.js')}}"></script>
	<script type="text/javascript">
	
	$(document).ready(function(){
		$('#ball-list-wrap').kinetic();
		$('#ball-list').kinetic();


		$.ajaxSetup({
        	headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      	});

		var cardNo;
		var fetchWinnersHolder;
    	var fetchBallsHolder;
    	var repositionHolder;

   		function rollThemBalls(){
    		for(var x = 1; x < 76;x++){
    			if(x > 0 && x <16){
  					letter = 'B';
  				}else if(x > 15 && x <31){
  					letter = 'I';
  				}else if(x > 30 && x <46){
  					letter = 'N';
  				}else if(x > 45 && x <61){
  					letter = 'G';
  				}else if(x > 60 && x <76){
  					letter = 'O';
  				}
  				$('#ball-list').append('<li class="letter'+letter.toLowerCase()+'">'+letter+x+'</li>');
    		}
    		
    		

    		repositionHolder = setInterval(function(){
    			var lipos = $('#ball-list li:first-child').offset().left;
    			var liItem = $('#ball-list li:first-child');
    			if(lipos < 500){
    				var liClone = liItem.clone();
    				liItem.remove();
    				$('#ball-list').append(liClone);
    			}
    		},500);
    	}
    	rollThemBalls();
    	


	@if(session('status') != 'admin')

		var gameMode = '';
		var gameStatus = 'nogame';
      	function getGameStatus(){
      		$.ajax({
		      	url: '{{url("bingo/server/getGameStatus")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		if(gameMode != data.mode || gameStatus != data.started){	
		      			if(data.mode != '' && data.started == 'pending'){
		      				$('#card-dialog').dialog('open');
		      			}
		      			else if(data.mode != '' && data.started == 'ongoing'){
		      				//CHECK IF HAVE CARD
		      				if(gameStatus == 'nogame'){//from previous game
		      					getCardCount();
		      				}
		      				else if(gameStatus == 'pending'){
		      					
		      					fetchBallsHolder = setInterval(fetchBalls,3000);
		      				}
		      			}
		      			else if(data.mode == '' && data.started == 'cancelled'){
		      				if(gameStatus == 'ongoing' || gameStatus == 'pending'){//from previous game
		      					$('#cancel-dialog').dialog('open');
		      					clearInterval(fetchBallsHolder);
		      					clearInterval(fetchWinnersHolder);
		      					setTimeout(function(){
									window.location.replace('{{url("bingo/server/".$id)}}');
								},5000);
		      				}
		      			}
		      			else if(data.mode == '' && data.started == 'pending'){
		      				if(gameStatus == 'ongoing'){//from previous game
		      					$('#card-dialog').dialog('open');
		      				}
		      			}
		      			else if(data.mode == '' && data.started == 'nogame'){
		      				if(gameStatus == 'ongoing'){//from previous game
		      					clearInterval(fetchBallsHolder);
		      				}
		      			}
		      		}

		      		gameMode = data.mode;
		      		gameStatus = data.started;
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
      	}

      	var getGameStatusHolder = setInterval(getGameStatus,5000);
    @else	
    	var hasCard;
    	var getPlayerStatusHolder;
    	var playerStatus = [];
    	@if($server->mode != '')
    		getCardCount();
    		@if($server->start == 'ongoing')
    			startGame();
    		@endif
    	@endif

    	$(document).on('click','button#start',function(){
    		var statusClear = true;
    		for(i in playerStatus){
    			if(playerStatus[i] == 'Pending'){
    				statusClear =false;
    				break;
    			}
    		}
    		if(statusClear){
    			startGame();
    		}
    		else{
    			$('#incomplete-dialog').dialog('open');
    		}
    	});

    	$('#card-opt button').click(function(){
    		cardNo = parseInt($(this).html());
    		setCards();
    		$('#card-opt').remove();
    		$('#game-proper .container').append('<button id="start">Start</button><div id="players"></div>');
    	});

    	//-----------functions admin
    	function getPlayerStatus(){
    		$.ajax({
		      	url: '{{url("bingo/server/playerStatus")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		$('#players').html('');
		      		for(var i in data){
		      			playerStatus[i] = data[i];
		      			$('#players').append('<p>'+i+'['+data[i]+']</p>');
		      		}
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}


    @endif
    //-------------general tasks

    $(document).on('click','button.bingo-nums',function(){
    	var cardId = $(this).parent().parent().parent().parent().attr('data-card-id');
    	var cardNum = $(this).parent().parent().parent().parent().attr('data-card-no');
    	var bingoNum = $(this).html();

    	//alert(bingoNum);
    	$.ajax({
	      	url: '{{url("bingo/server/thick")}}',
	      	dataType:'text',
	      	type: "POST",
	      	data: {'id':{{$id}},'cardId':cardId,'bingoNum':bingoNum,'cardNo':cardNum},
	      	success: function(data){
	      		if(data == 'good'){
	      			var randNum = getRandom(1,5);
	      			$('table[data-card-id='+cardId+'] button.bingo-nums[data-num='+bingoNum+']').parent().html('').addClass('thicked'+String(randNum));
	      		}else{
	      			alert(data);
	      		}
	     	},
	       	error: function(ts) { $('body').html(ts.responseText); }
	    });
    });

    $(document).on('click','#ball',function(){
    	ball();
    });

    $(document).on('click','.bingo-button',function(){
    	var cardId = $(this).parent().parent().parent().parent().attr('data-card-id');
    	var cardNo = $(this).parent().parent().parent().parent().attr('data-card-no');
    	var table = $(this).parent().parent().parent().parent().parent();
    	$.ajax({
	      	url: '{{url("bingo/server/bingo")}}',
	      	dataType:'json',
	      	type: "POST",
	      	data: {'id':{{$id}},'cardId':cardId,'cardNo':cardNo},
	      	success: function(data){
	      		if(data.bingo){
	      			
	      			table.html('<img src="/images/kyrk.png" class="half">')
	      			var audio = new Audio('/sounds/bingo.mp3');
					audio.play();
	      		}
	      		else{
	      			var audio = new Audio('/sounds/buzzer.mp3');
					audio.play();
	      		}
	      		if(data.game == 'gameover'){
	      			clearInterval(fetchWinnersHolder);
	      			$('#gameover-dialog').dialog('open');
	      			setTimeout(function(){
	      				@if(session('status') == 'admin')
	      				resetGame();
	      				@endif
						window.location.replace('{{url("bingo/server/".$id)}}');
					},4000);
	      		}

	     	},
	       	error: function(ts) { $('body').html(ts.responseText); }
	    });
    });
    //-----------functions
    	var ballLength = 0;
    	function fetchBalls(){
    		
    		$.ajax({
		      	url: '{{url("bingo/server/fetchBalls")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		if(ballLength < data.length){
		      			var diff = data.length - ballLength;
		      			var newBalls = data.slice(Math.max((data.length - diff), 0));
		      			var letter;
		      			for(var i in newBalls){
		      				if(newBalls[i] > 0 && newBalls[i] <16){
		      					letter = 'B';
		      				}else if(newBalls[i] > 15 && newBalls[i] <31){
		      					letter = 'I';
		      				}else if(newBalls[i] > 30 && newBalls[i] <46){
		      					letter = 'N';
		      				}else if(newBalls[i] > 45 && newBalls[i] <61){
		      					letter = 'G';
		      				}else if(newBalls[i] > 60 && newBalls[i] <76){
		      					letter = 'O';
		      				}
		      				$('#ball-list').prepend('<li style="opacity:0" class="letter'+letter.toLowerCase()+'">'+letter+newBalls[i]+'</li>');
		      					$('#ball-list li').animate({opacity:1},2000);
		      			}

		      			if(newBalls.length == 1){
		      				var audio = new Audio('/sounds/all'+newBalls[0]);
							audio.play();
		      			}
		      		}

		      		ballLength = data.length;
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}
    	function getRandom(min, max) {
		    return Math.floor(Math.random() * (max - min + 1)) + min;
		}
    	function fetchWinners(){
    		$.ajax({
		      	url: '{{url("bingo/server/fetchWinners")}}',
		      	dataType:'text',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		if(data == 0){//game over
		      			clearInterval(fetchWinnersHolder);
		      			clearInterval(fetchBallsHolder);
		      			clearInterval(getGameStatusHolder);
	      				$('#gameover-dialog').dialog('open');
	      				setTimeout(function(){
		      				@if(session('status') == 'admin')
		      				resetGame();
		      				@endif
							window.location.replace('{{url("bingo/server/".$id)}}');
						},4000);
		      		}
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}

    	function resetGame(){
    		$.ajax({
		      	url: '{{url("bingo/server/reset")}}',
		      	dataType:'text',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		@if(session('status') == 'admin')
		        	window.location.replace('{{url("bingo/server/".$id."/new-game")}}');
		            @else
		            window.location.replace('{{url("bingo/server/".$id)}}');
		            @endif
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}

    	function ball(){
    		$.ajax({
		      	url: '{{url("bingo/server/ball")}}',
		      	dataType:'text',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}

    	function getCardCount(){
    		$.ajax({
		      	url: '{{url("bingo/server/getCardCount")}}',
		      	dataType:'text',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		if(data == 'yes'){
		      			@if(session('status') == 'admin')
		      			$('#card-opt').remove();
			      			@if($server->start != 'ongoing')
	    					$('#game-proper .container').append('<button id="start">Start</button><div id="players"></div>');
    						@endif
    					getGameStatusHolder = setInterval(getPlayerStatus,3000);
    					@else
    					setCards();
    					@endif
		      		}else{
		      			@if(session('status') != 'admin')
		      			$('#late-dialog').dialog('open');
		      			$('#game-proper .container').append('<div class="row"><div class="4u -1u 6u(narrower) full-mobile"><section><h3>Normal Mode</h3><img src="/images/normal.gif" class="flex"></section></div><div class="4u -2u 6u(narrower) full-mobile"><section><h3>Jackpot Mode</h3><img src="/images/jackpot.gif" class="flex"></section></div></div>');
		      			@endif
		      		}
		      		
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}

    	function startGame(){
    		$.ajax({
		      	url: '{{url("bingo/server/startGame")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {'id':{{$id}}},
		      	success: function(data){
		      		$('button#start').remove();
		      		$('#players').remove();
		      		clearInterval(getPlayerStatusHolder);
		      		
		      		$('#game-proper .container').append('<div id="card-wrap" class="row"></div>');
		      		var num = 1;
		      		for(var i in data){

		      			$('#card-wrap').append('<div class="4u 6u(mobile) full-mobile"><section class="table-wrap"><table class="card-table" data-card-no='+num+' data-card-id="'+i+'"></table></section></div>');
		      			$('table[data-card-id='+i+']').html('<tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr>');
		      			var counter = 2;
		      			var arrayLength = data[i].length;
		      			for(var x = 0; x < arrayLength; x++){
		      				//alert(counter);
		      				$('table[data-card-id='+i+'] tbody tr:nth-of-type('+counter.toString()+')').append('<td><button data-num="'+data[i][x]+'" class="bingo-nums">'+data[i][x]+'</button></td>');
		      				if(counter == 6){
		      					counter = 2;
		      				}
		      				else{
		      					counter++;
		      				}
		      			}
		      			$('table[data-card-id='+i+'] tbody').append('<tr><td colspan="5"><button class="bingo-button">BINGO!!!</button></td></tr>');
		      			$('table[data-card-id='+i+'] tbody tr:first-child').append('<td>B</td><td>I</td><td>N</td><td>G</td><td>O</td>');
		      			num++;
		      		}
		      		fetchWinnersHolder = setInterval(fetchWinners,3000);
		      		fetchBallsHolder = setInterval(fetchBalls,3000);
		      		clearInterval(repositionHolder);
		      		$('#ball-list').html('');
		     	},
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
    	}

    	function setCards(){
    		$.ajax({
		      	url: '{{url("bingo/server/setCards")}}',
		      	dataType:'json',
		      	type: "POST",
		      	data: {'id':{{$id}},'cardNo':cardNo},
		      	success: function(data){
		      		@if(session('status') == 'admin')
		      		getGameStatusHolder = setInterval(getPlayerStatus,3000);
		      		@else
		      			$('#game-proper .container').html('');
		      			$('#game-proper .container').append('<div id="card-wrap" class="row"></div>');
		      			var num = 1;
			      		for(var i in data){

			      			$('#card-wrap').append('<div class="4u 6u(mobile) full-mobile"><section class="table-wrap"><table class="card-table" data-card-no='+num+' data-card-id="'+i+'"></table></section></div>');
			      			$('table[data-card-id='+i+']').html('<tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr><tr>a</tr>');
			      			var counter = 2;
			      			var arrayLength = data[i].length;
			      			for(var x = 0; x < arrayLength; x++){
			      				//alert(counter);
			      				$('table[data-card-id='+i+'] tbody tr:nth-of-type('+counter.toString()+')').append('<td><button data-num="'+data[i][x]+'" class="bingo-nums">'+data[i][x]+'</button></td>');
			      				if(counter == 6){
			      					counter = 2;
			      				}
			      				else{
			      					counter++;
			      				}
			      			}
			      			$('table[data-card-id='+i+'] tbody').append('<tr><td colspan="5"><button class="bingo-button">BINGO!!!</button></td></tr>');
			      			$('table[data-card-id='+i+'] tbody tr:first-child').append('<td>B</td><td>I</td><td>N</td><td>G</td><td>O</td>');
			      			num++;
			      		}
			      		fetchWinnersHolder = setInterval(fetchWinners,3000);
			      		clearInterval(repositionHolder);
		      			$('#ball-list').html('');
			      		$.ajax({
					      	url: '{{url("bingo/server/checkStatus")}}',
					      	dataType:'text',
					      	type: "POST",
					      	data: {'id':{{$id}}},
					      	success: function(data){
					      		if(data == 'baller'){
					      			$('#game-proper .container').append('<button id="ball">ball</button>');
					      		}
					     	},	
					       	error: function(ts) { $('body').html(ts.responseText); }
					    });
		      		@endif
		     	},	
		       	error: function(ts) { $('body').html(ts.responseText); }
		    });
			
    	}
    //-----------------------

    //----------------dialogs
    	$( "#gameover-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		    	@if(session('status') == 'admin')
		        'Start a new game': function() {
		        	resetGame();
		            $( this ).dialog( "close" );
		        },
		        @endif
		        'Close': function() {
		        	resetGame();
		            $( this ).dialog( "close" );
		        }
	      	}
		});

    	$( "#incomplete-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        'Yes': function() {
		        	startGame();
		            $( this ).dialog( "close" );
		        },
		        'No': function() {
		            $( this ).dialog( "close" );
		        }
	      	}
		});

      	$( "#cancel-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        'OK': function() {
		            $( this ).dialog( "close" );
		            resetGame();
		            window.location.replace('{{url("bingo/server/".$id)}}');
		        }
	      	}
		});

		$( "#late-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        'OK': function() {
		            $( this ).dialog( "close" );
		        }
	      	}
		});

		$( "#card-dialog" ).dialog({ 
			autoOpen: false,
			resizable: false,
		    height:200,
		    modal: true,
		    buttons: {
		        '1': function() {
		        	cardNo = 1;
		        	setCards();
		            $( this ).dialog( "close" );
		            
		        },
		        '2': function() {
		        	cardNo = 2;
		        	setCards();
		            $( this ).dialog( "close" );
		            
		        },
		        '3': function() {
		        	cardNo = 3;
		        	setCards();
		            $( this ).dialog( "close" );
		            
		        },
		        '4': function() {
		        	cardNo = 4;
		        	setCards();
		            $( this ).dialog( "close" );
		            
		        }
	      	}
		});

		var tdHeight;
		var getSizeHolder = setInterval(function(){
			if($('table.card-table').length){
				tdHeight = $('table.card-table tbody tr:first-child td:first-child').width();
				clearInterval(getSizeHolder);
				$('table.card-table tr,table.card-table button').animate({height:tdHeight-(tdHeight*.2)},500);
			}
		},1000);
		
		$(window).resize(function(){
			if($('table.card-table').length){
				tdHeight = $('table.card-table tbody tr:first-child td:first-child').width();
				$('table.card-table tr,table.card-table button').animate({height:tdHeight-(tdHeight*.2)},500);
			}
		});

		@if(true)

		@endif

	});

    </script>
@stop