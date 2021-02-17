<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/app/favicon.ico" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.typekit.net/mpl5hxh.css">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - {{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard" id="app">
        <header>

        </header>

        <nav>
            <a id="logo" href="{{ url('/') }}">
                <img src="/images/app/logo.svg" alt="SEO Bridge logo">
            </a>

            <div class="logo-divider"></div>

            <div class="button active">
                <div class="icon">&#984430;</div>
                <div class="text">Overview</div>
            </div>

            <div class="button">
                <div class="icon">&#983573;</div>
                <div class="text">Reports</div>
            </div>

            <div class="button">
                <div class="icon">&#985161;</div>
                <div class="text">My Team</div>
            </div>

            <div class="button">
                <div class="icon">&#983049;</div>
                <div class="text">My Profile</div>
            </div>

            <div class="button">
                <div class="icon">&#983194;</div>
                <div class="text">Notifications</div>
                {{-- <div class="notifications">200</div> --}}
            </div>
        </nav>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
