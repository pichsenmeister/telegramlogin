<!DOCTYPE html>
<html>

<head>
    <title>TelegramLogin - Authenticate users via Telegram Messenger</title>

    <link href="{{ asset('/bower_components/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/bower_components/fontawesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/bower_components/sweetalert/dist/sweetalert.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/css/style.css')}}" rel="stylesheet" type="text/css">

    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="icon" type="image/png" href="{{ asset('/img/logo.png') }}">
    <meta name="description" content="Authenticate users via Telegram Messengers with Telegram Login">
    <meta property="og:title" content="TelegramLogin - Authenticate users via Telegram Messenger.">
    <meta property="og:description" content="">
    <meta property="og:image" content="{{ asset('/img/logo-white.png') }}">
    <meta property="og:image:url_secure" content="{{ asset('/img/logo-white.png') }}">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                <!-- <a class="navbar-brand" href="/">Telegram Login</a> -->
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        @yield('navbar')
                    </ul>
                    <ul class="nav navbar-nav pull-right">
                        @if(Auth::user())
                            <li>
                                <a href="/profile" >
                                    {{ Auth::user()->name }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ config('app.url').'/token/314159265?state='.random_str(20) }}" >
                                    <!-- <button class="btn btn-primary logo-button"> -->
                                        <i class="fa fa-key logo-key"></i> Login with Telegram
                                    <!-- </button> -->
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>

    <footer class="footer">
        <div class="container">
            <p class="text-muted">
                <span class="pull-left">
                    <a href="https://telegram.me/tglogin" target="_blank"><i class="fa fa-bullhorn text-black"></i> Telegram Login</a>
                    <br>
                    made by <a href="https://pichsenmeister.com" target="_blank">david pichsenmeister</a>
                    (<a href="https://twitter.com/3x14159265">@3x14159265</a>)
                </span>
                <span class="pull-right">
                    powered by <a href="https://orat.io" target="_blank">orat.io</a> -
                    <br>
                    Connecting businesses and customers through messengers
                </span>
            </p>
        </div>
    </footer>

    <script type="text/javascript" src="{{ asset('/bower_components/sweetalert/dist/sweetalert.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
