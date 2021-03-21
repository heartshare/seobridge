<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/images/app/favicon.ico" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://use.typekit.net/mpl5hxh.css">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Page Meta -->
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')">

    <!-- Twitter Meta -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="">
    <meta name="twitter:title" content="Search engine optimisation with SEO Bridge">
    <meta name="twitter:description" content="Check your site's page ranking and optimize it for search engines">
    <meta name="twitter:image" content="https://seobridge.net/images/static/og_banner.png">

    <!-- OG Meta -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="Search engine optimisation with SEO Bridge">
    <meta property="og:site_name" content="SEO Bridge">
    <meta property="og:url" content="seobridge.net">
    <meta property="og:image" content="https://seobridge.net/images/static/og_banner.png">

    <!-- Scripts -->
    <script src="{{ asset('js/static.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="static font-size @yield('page-classes')" id="app">
        <nav id="navbar">
            <div class="limiter">
                <a id="logo" href="{{ url('/') }}">
                    <img src="/images/app/logo_white.svg" alt="SEO Bridge logo">
                </a>

                <ul class="center-nav-container">
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'home') active @endif" href="{{url('/')}}">Products</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'resources') active @endif" href="{{url('/resources')}}">Resources</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'pricing') active @endif" href="{{url('/pricing')}}">Pricing</a>
                    </li>
                </ul>

                <ul class="side-nav-container">
                    @guest
                        @if (Route::has('login'))
                            <li>
                                <a class="solid" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('dashboard') }}">
                                {{ Auth::user()->username }}
                            </a>
                        </li>

                        <ui-icon-button class="logout-button" title="{{ __('Logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">&#984573;</ui-icon-button>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    @endguest
                </ul>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer>
            <div class="limiter">
                <nav>
                    <ul>
                        <li>
                            <a href="{{url('/privacy-policy')}}">Privacy Policy</a>
                        </li>
                        {{-- <li>
                            <a href="{{url('/terms-of-service')}}">Terms of Service</a>
                        </li> --}}
                        <li>
                            <a href="{{url('/legal-disclosures')}}">Legal Disclosures</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </footer>
    </div>

    <!-- Scroll Script -->
    <script>
        let setScrollClass = (offset) => {
            if (offset > 0)
            {
                document.getElementById('navbar').classList.add('scrolled')
            }
            else
            {
                document.getElementById('navbar').classList.remove('scrolled')
            }
        }

        setScrollClass(window.pageYOffset)

        window.addEventListener('scroll', () => {
            setScrollClass(window.pageYOffset)
        })
    </script>
</body>
</html>
