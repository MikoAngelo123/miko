<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MIKO STORE')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-size: .875rem; }
        .feather { width: 16px; height: 16px; vertical-align: text-bottom; }
        .sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; padding: 48px 0 0; box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1); }
        @media (max-width: 767.98px) { .sidebar { top: 56px; } }
        .sidebar-sticky { position: relative; top: 0; height: calc(100vh - 48px); padding-top: .5rem; overflow-x: hidden; overflow-y: auto; }
        .sidebar .nav-item { border-bottom: 1px solid #ddd; }
        .sidebar .nav-link { font-weight: 500; color: #333; }
        .sidebar .nav-link .feather { margin-right: 4px; color: #727272; }
        .sidebar .nav-link.active { color: #2470dc; background-color: #e9ecef; }
        .sidebar .nav-link:hover { background-color: #f8f9fa; }
        .sidebar .nav-link:hover .feather, .sidebar .nav-link.active .feather { color: inherit; }
        .navbar-brand { padding-top: .75rem; padding-bottom: .75rem; font-size: 1rem; background-color: rgba(0, 0, 0, .25); box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25); }
        .navbar .navbar-toggler { top: .25rem; right: 1rem; }
    </style>
</head>
<body>
    <div id="app">
        <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="/">MIKO STORE</a>
            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            @auth
            <div class="navbar-nav ms-auto">
                <div class="nav-item text-nowrap">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link px-3 bg-dark border-0">Logout</button>
                    </form>
                </div>
            </div>
            @endauth
        </header>

        @auth
            <div class="container-fluid">
                <div class="row">
                    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                        <div class="position-sticky pt-3 sidebar-sticky">
                            @if(Auth::user()->role === 'admin')
                                @include('layouts.admin-sidebar')
                            @elseif(Auth::user()->role === 'customer')
                                @include('layouts.customer-sidebar')
                            @endif
                        </div>
                    </nav>

                    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h1 class="h2">@yield('title')</h1>
                        </div>

                        @yield('content')
                    </main>
                </div>
            </div>
        @else
            <main class="py-4">
                @yield('content')
            </main>
        @endauth
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
</body>
</html>
