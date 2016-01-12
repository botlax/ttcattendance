<!DOCTYPE HTML>

<html>
	<head>
		<title>Bingo Botlax</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		@yield('meta')
		<!--[if lte IE 8]><script src="{{url('/assets/')}}/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="{{url('/assets/')}}/css/main.css" />
		@yield('css')
		<!--[if lte IE 8]><link rel="stylesheet" href="{{url('/assets/')}}/css/ie8.css" /><![endif]-->
		<!--[if lte IE 9]><link rel="stylesheet" href="{{url('/assets/')}}/css/ie9.css" /><![endif]-->

	</head>
	<body>

		<!-- Header -->
			<div id="header">

				<div class="top">

					<!-- Logo -->
						<div id="logo">
							<span class="image avatar48"><img src="/images/thick1.png" alt="" /></span>
							<h1 id="title">Bingo</h1>
							<p>Botlax</p>
						</div>

					<!-- Nav -->
						<nav id="nav">
							<!--

								Prologue's nav expects links in one of two formats:

								1. Hash link (scrolls to a different section within the page)

								   <li><a href="#foobar" id="foobar-link" class="icon fa-whatever-icon-you-want skel-layers-ignoreHref"><span class="label">Foobar</span></a></li>

								2. Standard link (sends the user to another page/site)

								   <li><a href="http://foobar.tld" id="foobar-link" class="icon fa-whatever-icon-you-want"><span class="label">Foobar</span></a></li>

							-->
							@yield('options')
						</nav>
				</div>
			</div>

		<!-- Main -->
			<div id="main">
			@yield('content')
				

			</div>

		<!-- Scripts -->
			<script src="{{url('/assets/')}}/js/jquery.min.js"></script>
			<script src="{{url('/assets/')}}/js/skel.min.js"></script>
			<script src="{{url('/assets/')}}/js/util.js"></script>
			<!--[if lte IE 8]><script src="{{url('/assets/')}}/js/ie/respond.min.js"></script><![endif]-->
			<script src="{{url('/assets/')}}/js/main.js"></script>
			@yield('script')
	</body>
</html>