<link rel="stylesheet" href="{{ asset('css/navbar.css') }}?v={{ time() }}">
<nav class="navbar navbar-expand-lg fixed-top navbar-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-4 text-center">
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
            
            <div class="auth-buttons d-flex align-items-center gap-3 justify-content-center mt-3 mt-lg-0">
                {{-- Language Switcher --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold d-flex align-items-center gap-1 rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(session('locale', 'id') === 'en')
                            English
                        @else
                            Indonesia
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item fw-bold" href="{{ route('set-locale', 'id') }}">Bahasa Indonesia</a></li>
                        <li><a class="dropdown-item fw-bold" href="{{ route('set-locale', 'en') }}">English</a></li>
                    </ul>
                </div>

                @if (Route::has('login'))
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle fw-bold rounded-pill px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li>
                                    @if(Auth::user()->role === 'admin')
                                        <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                                    @elseif(Auth::user()->role === 'narasumber')
                                        <a class="dropdown-item py-2" href="{{ route('narasumber.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                                    @else
                                        <a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                                    @endif
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- Ensure navbar styling scroll effect works -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navbar = document.querySelector('.navbar');
        const navbarCollapse = document.getElementById('navbarNav');

        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }

        if (navbarCollapse && navbar) {
            navbarCollapse.addEventListener('show.bs.collapse', function () {
                navbar.classList.add('menu-open');
            });
            navbarCollapse.addEventListener('hide.bs.collapse', function () {
                navbar.classList.remove('menu-open');
            });
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Log Backend Data Load Time
        @if(defined('LARAVEL_START'))
            const backendTime = {{ round((microtime(true) - LARAVEL_START) * 1000, 2) }};
            console.log("%c⚡ Backend Data Load Time: " + backendTime + " ms", "color: #4CAF50; font-weight: bold; font-size: 14px;");
        @endif

        // Log Frontend Rendering & Asset Load Time
        window.addEventListener('load', function() {
            const perfData = window.performance.timing;
            const frontendTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log("%c⚡ Total Page Load Time: " + frontendTime + " ms", "color: #2196F3; font-weight: bold; font-size: 14px;");
        });
    });
</script>
