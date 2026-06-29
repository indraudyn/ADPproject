<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav gap-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parwa.*') || request()->is('parwa') ? 'active' : '' }}" href="{{ route('parwa.index') }}">PARWA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('quiz.*') ? 'active' : '' }}" href="{{ route('quiz.index') }}">KUIS</a> 
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}" href="{{ route('forum.index') }}">FORUM DISKUSI</a>
                </li>
            </ul>
        </div>
        
        <div class="auth-buttons d-flex align-items-center gap-3">
            {{-- Language Switcher --}}
            <div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold d-flex align-items-center gap-1 rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(session('locale', 'id') === 'en')
                        English
                    @else
                        Indonesia
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item fw-bold" href="{{ route('set-locale', 'id') }}">Bahasa Indonesia</a></li>
                    <li><a class="dropdown-item fw-bold" href="{{ route('set-locale', 'en') }}">English</a></li>
                </ul>
            </div>

            @if (Route::has('login'))
                @auth
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                @if(Auth::user()->role === 'admin')
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a>
                                @elseif(Auth::user()->role === 'narasumber')
                                    <a class="dropdown-item" href="{{ route('narasumber.dashboard') }}">Dashboard</a>
                                @else
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                                @endif
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light px-4 fw-bold">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-danger px-4 fw-bold">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</nav>

<!-- Ensure navbar styling scroll effect works -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }
    });
</script>
