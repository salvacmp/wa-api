<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>DSMG POS - DSMG POS</title>
    <!-- CSS files -->
    @include('layouts.head')
    @livewireStyles
    @yield('header')
</head>

<body class="antialiased">
    <div class="page">

        {{$slot}}

        @include('layouts.footer')
    </div>
</div>
@include('layouts.footerscript')
@livewireScripts
@yield('footer')
</body>

</html>
