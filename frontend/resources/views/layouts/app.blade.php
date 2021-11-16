<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>DSMG POS Client</title>
    <!-- CSS files -->
    @include('layouts.head')
    @livewireStyles
    @yield('header')
</head>

<body class="antialiased">
    <div class="page" id="page">
        <header class="navbar navbar-expand-md navbar-light d-print-none">
            <div class="container-xl">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal ">
                    <a href=".">
                        <img src="/dist/img/logo.png" width="110px" alt="DSMG POS Logo" class="navbar-brand-image">
                    </a>
                </h1>
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                            aria-label="Open user menu">
                            <div class="d-none d-xl-block ps-2">
                                <div>
                                    {{ Auth::user()->name }}
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="/signout" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar navbar-light">
                    <div class="container-xl">
                        <ul class="navbar-nav">
                            @include('layouts.menu')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div id="loader-wrapper">
			<div id="loader"></div>

			<div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>

		</div>
        <div class="content" id="content">
            {{$slot}}
            @include('layouts.footer')
        </div>
    </div>
    @include('layouts.footerscript')
    @livewireScripts
    @yield('footer')
</body>

</html>
