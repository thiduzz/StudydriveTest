<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Studydrive Test') }}</title>
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/bower_vendors/sweetalert/dist/sweetalert.css">
    <link rel="stylesheet"  href="/bower_vendors/font-awesome/css/font-awesome.min.css"/>

    <!--Icons-->
    <script src="/js/lumino.glyphs.js"></script>

    <!--[if lt IE 9]>
    <script src="/js/html5shiv.js"></script>
    <script src="/js/respond.min.js"></script>
    <![endif]-->

</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            @if(Auth::check())
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            @endif
            <a class="navbar-brand" href="#">{{ config('app.name', 'Studydrive Test') }}</a>
            @if(Auth::check())
            <ul class="user-menu">
                <li class="dropdown pull-right">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> {{ Auth::user()->email }} <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li>

                            <a href="{{ url('/logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <svg class="glyph stroked cancel"><use xlink:href="#stroked-cancel"></use></svg> Logout
                            </a>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @endif
        </div>

    </div><!-- /.container-fluid -->
</nav>

<div class="col-sm-12 col-lg-12 main"  id="app">
    @include('partials.breadcrumbs')
    @include('partials.title')
    @yield('content')
</div>	<!--/.main-->
@include('partials.javascript_binder')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="/bower_vendors/sweetalert/dist/sweetalert.min.js"></script>
<script src="/js/app.js"></script>
</body>

</html>
