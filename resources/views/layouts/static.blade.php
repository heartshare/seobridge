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

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="static font-size" id="app">
        <nav id="navbar">
            <div class="limiter">
                <a id="logo" href="{{ url('/') }}">
                    <img src="/images/app/logo.svg" alt="SEO Bridge logo">
                </a>

                <ul class="center-nav-container">
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'home') active @endif" href="{{url('/')}}">Home</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'blog') active @endif" href="{{url('/blog')}}">Blog</a>
                    </li>
                    <li>
                        <a class="underline @if (Route::currentRouteName() == 'pricing') active @endif" href="{{url('/pricing')}}">Pricing</a>
                    </li>
                </ul>

                <ul class="side-nav-container">
                    @guest
                        @if (Route::has('register'))
                            <li>
                                <a class="solid" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif

                        @if (Route::has('login'))
                            <li>
                                <a class="light" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif
                    @else
                        <li>
                            <a href="{{ route('dashboard') }}">
                                {{ Auth::user()->username }}
                            </a>
                        </li>

                        <ui-icon-button title="{{ __('Logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">&#984573;</ui-icon-button>

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
                        <li>
                            <a href="{{url('/terms-of-service')}}">Terms of Service</a>
                        </li>
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
