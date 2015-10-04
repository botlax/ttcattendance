<!DOCTYPE HTML>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="{{url('css/normalize.css')}}">
        <link rel="stylesheet" href="{{url('vendor/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{url('vendor/select2/select2.css')}}">
        <link rel="stylesheet" href="{{url('css/styles.css')}}">
        @yield('css')
        <script src="{{url('js/jquery.min.js')}}"></script>
        <script src="{{url('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{url('vendor/select2/select2.min.js')}}"></script>
        @yield('script')
        <title>@yield('title')</title>
        <!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
    </head>

    <body>
        <div class="container-fluid">
            <header>
                <nav class="navbar navbar-inverse navbar-fixed-top">
                  <div class="container-fluid">
                    <div class="navbar-header">
                      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                      </button>
                      <a class="navbar-brand" href="{{url('/')}}"><img id="logo" src="{{url('images/logo.png')}}" alt="Talal Logo"/></a>
                    </div>
                    <div class="collapse navbar-collapse">
                        @if(\Auth::check() && \Auth::user()->isAdmin())
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Attendance<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('attendance')}}">Admin View</a></li>
                                    @if(\Auth::check() && \Auth::user()->isNotAdmin())
                                    <li><a href="{{url('attendance/list')}}">Attendance Form</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Employees<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('employees')}}">Show All</a></li>
                                    <li><a href="{{url('employees/deleted')}}">Show Deleted Users</a></li>
                                    <li><a href="{{url('employees/add')}}">Add</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sites<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('sites')}}">Show All</a></li>
                                    <li><a href="{{url('sites/add')}}">Add</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Trades<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('trades')}}">Show All</a></li>
                                    <li><a href="{{url('trades/add')}}">Add</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Users<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{url('users')}}">Show All</a></li>
                                    <li><a href="{{url('user/register')}}">Add</a></li>
                                </ul>
                            </li>
                        </ul>
                        @endif
                        <ul class="nav navbar-nav navbar-right">
                            @if(!\Auth::check())
                            <li><a href="{{url('user/login')}}">Login</a></li>
                            @else
                            <li><a href="{{url('user/logout')}}">Logout</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                    @endif
                  </div>
                </nav>
            </header>
            
                @include('flash::message')
                @yield('content')
            
            <footer>
                <p class="text-center">Talal Attendance System {{ date('Y') }}</p>
            </footer>

        <script>
            $('div.alert').not('.alert-important').delay(3000).slideUp(300);
        </script>
        </div>
    </body>
</html>