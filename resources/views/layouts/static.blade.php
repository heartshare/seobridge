<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/images/app/favicon.ico" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined|Material+Icons+Round" rel="stylesheet">
    
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
    <div class="static @yield('page-classes')" id="app">
        <nav id="desktop-navbar" class="navbar">
            <div class="limiter">
                <div class="navbar-wrapper left">
                    <a id="logo" href="{{ url('/') }}">
                        <img src="/images/app/logo.svg" alt="SEO Bridge logo">
                    </a>
                </div>

                <ul class="navbar-wrapper center">
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'home') active @endif" href="{{url('/')}}">Home</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'tools') active @endif" href="{{url('/tools')}}">Tools</a>
                    </li>
                    {{-- <li>
                        <a class="underline @if (Route::currentRouteName() == 'products') active @endif" href="{{url('/products')}}">Products</a>
                    </li> --}}
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'resources') active @endif" href="{{url('/resources')}}">Blog</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'pricing') active @endif" href="{{url('/pricing')}}">Pricing</a>
                    </li>
                </ul>

                <ul class="navbar-wrapper right">
                    @guest
                        <li>
                            <a class="button login" href="{{ route('login') }}">
                                <span class="text">{{ __('Login') }}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="button account" href="{{ route('dashboard') }}">
                                <span class="text">{{ Auth::user()->username }}</span>
                                <img src="/images/defaults/default_profile_image.svg" alt="{{ Auth::user()->username }}" class="image">
                            </a>

                            <ul>
                                <li>
                                    <a href="/dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</button>
                                </li>
                            </ul>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                    @endguest
                </ul>
            </div>
        </nav>

        <nav id="mobile-navbar" class="navbar">
            <div class="limiter">
                <div class="navbar-wrapper left">
                    <a id="logo" href="{{ url('/') }}">
                        <img src="/images/app/logo.svg" alt="SEO Bridge logo">
                    </a>
                </div>

                <button id="toggle-menu-button" onmousedown="toggleMenu()">
                    <svg class="svg-wrapper" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="hamburger-path" d="M5 9C5 9 17.5 9 19 9C20.5 9 22.5 7.5 21.5 6C20.5 4.5 18 6 17 7C16 8 7 17 7 17" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path class="hamburger-path" d="M5 15.0054C5 15.0054 17.5 15.0054 19 15.0054C20.5 15.0054 22.5 16.5054 21.5 18.0054C20.5 19.5054 18 18.0054 17 17.0054C16 16.0054 7 7.00542 7 7.00542" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div id="mobile-menu">
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

                <ul>
                    <li>
                        <a class="home @if (Route::currentRouteName() == 'home') active @endif" href="{{url('/')}}">
                            <div class="icon">&#983772;</div>
                            <div class="text">Home</div>
                        </a>
                    </li>
                    <li>
                        <a class="tools @if (Route::currentRouteName() == 'tools') active @endif" href="{{url('/tools')}}">
                            <div class="icon">&#985302;</div>
                            <div class="text">Tools</div>
                        </a>
                    </li>
                    <li>
                        <a class="resources @if (Route::currentRouteName() == 'resources') active @endif" href="{{url('/resources')}}">
                            <div class="icon">&#984808;</div>
                            <div class="text">Blog</div>
                        </a>
                    </li>
                    <li>
                        <a class="pricing @if (Route::currentRouteName() == 'pricing') active @endif" href="{{url('/pricing')}}">
                            <div class="icon">&#984315;</div>
                            <div class="text">Pricing</div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer>
            <div class="limiter">
                <div class="spacer"></div>
                <nav>
                    <ul>
                        <strong>Support</strong>
                        <li>
                            <a href="{{url('/contact')}}">Contact</a>
                        </li>
                    </ul>

                    <ul>
                        <strong>Resources</strong>
                        <li>
                            <a href="{{url('/blog')}}">Blog</a>
                        </li>
                        <li>
                            <a href="{{url('/branding')}}">Branding</a>
                        </li>
                    </ul>

                    <ul>
                        <strong>Policies</strong>
                        <li>
                            <a href="{{url('/privacy-policy')}}">Privacy</a>
                        </li>
                        <li>
                            <a href="{{url('/terms-of-service')}}">Terms</a>
                        </li>
                        <li>
                            <a href="{{url('/legal-disclosures')}}">Disclosures</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="limiter">
                <div class="spacer horizontal-line"></div>
            </div>
            <div class="limiter">
                <div class="project-by">
                    <div class="text">A project by</div>
                    <a href="https://freuwort.com" target="_blank" rel="noopener noreferrer">
                        <img src="/images/branding/freuwort_logo_purple.svg" alt="Freuwort Logo">
                    </a>
                </div>

                <div class="spacer"></div>

                <div class="button-bar">
                    <a href="https://discord.gg/VbbRgcjuWH" target="_blank" class="social-link">&#984687;</a>
                    <a href="https://www.instagram.com/maurice.freuwoert" target="_blank" class="social-link">&#983806;</a>
                </div>
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

        function toggleMenu()
        {
            if (document.getElementById('mobile-navbar').classList.contains('open'))
            {
                document.getElementById('mobile-navbar').classList.remove('open')
            }
            else
            {
                document.getElementById('mobile-navbar').classList.add('open')
            }
        }
    </script>
</body>
</html>
