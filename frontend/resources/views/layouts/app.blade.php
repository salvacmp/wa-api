<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    {{-- @yield('title') --}}
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="/dist/css/tabler-buttons.min.css" rel="stylesheet"/>
    @livewireStyles
  </head>
  <body class="antialiased border-top-wide border-primary d-flex flex-column">
    <div class="page">
        <header class="navbar navbar-expand-md navbar-light">
          <div class="container-xl">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
              <span class="navbar-toggler-icon"></span>
            </button>
            <a href="." class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pr-0 pr-md-3">
              <img src="https://cdn.dsgroupmedia.com/logo/portbytealt1.png" alt="PB SYS" class="navbar-brand-image">
            </a>
            <div class="navbar-nav flex-row order-md-last">

              <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-toggle="dropdown">
                  <span class="avatar" style="background-image: url(https://cdn.statically.io/avatar/{{Auth::user()->name}})"></span>
                  <div class="d-none d-xl-block pl-2">
                    <div>{{Auth::user()->name}}</div>
                    <div class="mt-1 small text-muted">{{Auth::user()->email}}</div>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#"><svg xmlns="http://www.w3.org/2000/svg" class="icon dropdown-item-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z"/>
                      <line x1="12" y1="5" x2="12" y2="19" />
                      <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Logout</a>
                </div>
              </div>
            </div>
          </div>
        </header>
        <div class="navbar-expand-md">
          <div class="navbar collapse navbar-collapse navbar-light" id="navbar-menu">
            <div class="container-xl">
              <ul class="navbar-nav">
                @include('layouts.menu')

              </ul>

            </div>
          </div>
        </div>
        <div class="content">
        <div class="container-xl">
          {{$slot}}
          <footer class="footer footer-transparent">
            <div class="container">
              <div class="row text-center align-items-center flex-row-reverse">

                <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                  Copyright © 2021
                  <a href="." class="link-secondary">PortByte</a>.
                  All rights reserved.
                </div>
              </div>
            </div>
          </footer>
        </div>
      </div>

    <!-- Libs JS -->

    <script src="/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dist/libs/jquery/dist/jquery.slim.min.js"></script>
    <!-- Tabler Core -->
    <script src="/dist/js/tabler.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.1.3/socket.io.js" integrity="sha512-2RDFHqfLZW8IhPRvQYmK9bTLfj/hddxGXQAred2wNZGkrKQkLGj8RCkXfRJPHlDerdHHIzTFaahq4s/P4V6Qig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @livewireScripts
    @yield('script')
  </body>
</html>
