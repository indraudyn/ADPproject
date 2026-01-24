<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asta Dasa Parwa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-light bg-white px-4">
    <div class="ms-auto d-flex align-items-center gap-2">

        @guest
            <!-- BELUM LOGIN -->
            <a href="{{ route('login') }}" class="btn btn-light">Login</a>
            <a href="{{ route('register') }}" class="btn btn-danger">Register</a>
        @endguest

        @auth
            <!-- SUDAH LOGIN -->
            <div class="dropdown">
                <button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ auth()->user()->name }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        @if(auth()->user()->role === 'admin')
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                Dashboard Admin
                            </a>
                        @else
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        @endif
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth

    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <h1 class="title">ASTA DASA PARWA</h1>

    @auth
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn-start">
                DASHBOARD
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="btn-start">
                DASHBOARD
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn-start">
            START
        </a>
    @endauth
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/welcome.js') }}"></script>

</body>
</html>
