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
    <meta property="og:url" content="https://seobridge.net">
    <meta property="og:image" content="https://seobridge.net/images/static/og_banner.png">

    <!-- Scripts -->
    <script src="{{ asset('js/static.js') }}" defer></script>
    <script src="https://js.stripe.com/v3/"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TGPZX3YS70"></script>
    <script>
        window.dataLayer = window.dataLayer || []
        function gtag(){dataLayer.push(arguments)}
        gtag('js', new Date())
        gtag('config', 'G-TGPZX3YS70')
    </script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="static font-size @yield('page-classes')" id="app">
        <nav id="desktop-navbar" class="navbar">
            <div class="limiter">
                <div class="navbar-wrapper left">
                    <a id="logo" href="{{ url('/') }}">
                        <img src="/images/app/logo_white.svg" alt="SEO Bridge logo">
                    </a>
                </div>

                <ul class="navbar-wrapper center">
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'tools') active @endif" href="{{url('/tools')}}">Tools</a>
                    </li>
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

                <ul class="navbar-wrapper right">
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

        <nav id="mobile-navbar" class="navbar">
            <div class="limiter">
                <div class="navbar-wrapper left">
                    <a id="logo" href="{{ url('/') }}">
                        <img src="/images/app/logo_white.svg" alt="SEO Bridge logo">
                    </a>
                </div>

                <button id="toggle-menu-button" onclick="openMenu()">&#983900;</button>
            </div>

            <div id="mobile-menu">
                <div class="menu-head">
                    <h5><a href="{{ url('/') }}">SEO Bridge</a></h5>
                    <button id="close-menu-button" onclick="closeMenu()">&#983382;</button>
                </div>
                <ul class="group">
                    <li>
                        <a class="tools @if (Route::currentRouteName() == 'tools') active @endif" href="{{url('/tools')}}">
                            <div class="icon">&#984503;</div>
                            <div class="text">Tools</div>
                        </a>
                    </li>
                    <li>
                        <a class="products @if (Route::currentRouteName() == 'home') active @endif" href="{{url('/')}}">
                            <div class="icon">&#985879;</div>
                            <div class="text">Products</div>
                        </a>
                    </li>
                    <li>
                        <a class="resources @if (Route::currentRouteName() == 'resources') active @endif" href="{{url('/resources')}}">
                            <div class="icon">&#986387;</div>
                            <div class="text">Resources</div>
                        </a>
                    </li>
                    <li>
                        <a class="pricing @if (Route::currentRouteName() == 'pricing') active @endif" href="{{url('/pricing')}}">
                            <div class="icon">&#984313;</div>
                            <div class="text">Pricing</div>
                        </a>
                    </li>
                </ul>

                @guest
                    @if (Route::has('login'))
                        <ul class="bottom-wrapper center">
                            <li>
                                <a href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        </ul>
                    @endif
                @else
                    <ul class="bottom-wrapper">
                        <li class="profile-li">
                            <img width="50" src="/images/defaults/default_profile_image.svg" alt="{{ Auth::user()->username }}'s profile image">

                            <span>{{ Auth::user()->username }}</span>
                            <a href="{{ route('dashboard') }}">Dashboard</a>

                            <ui-icon-button class="logout-button" title="{{ __('Logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">&#984573;</ui-icon-button>
                        </li>
    
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    </ul>
                @endguest
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
                document.getElementById('desktop-navbar').classList.add('scrolled')
                document.getElementById('mobile-navbar').classList.add('scrolled')
            }
            else
            {
                document.getElementById('desktop-navbar').classList.remove('scrolled')
                document.getElementById('mobile-navbar').classList.remove('scrolled')
            }
        }

        setScrollClass(window.pageYOffset)

        window.addEventListener('scroll', () => {
            setScrollClass(window.pageYOffset)
        })

        function openMenu()
        {
            document.getElementById('mobile-menu').classList.add('open')
        }

        function closeMenu()
        {
            document.getElementById('mobile-menu').classList.remove('open')
        }
    </script>
</body>
</html>
