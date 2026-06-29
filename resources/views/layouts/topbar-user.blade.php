        <div class="topbar d-flex justify-content-between align-items-center px-3">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" class="home-icon text-decoration-none text-center me-1">
                    <i class="bi bi-house"></i>
                </a>

                {{-- Language Switcher --}}
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle fw-bold d-flex align-items-center gap-1 rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #d1d5db; color: #4b5563;">
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

                <div class="dropdown">
                    @auth
                        <button class="btn profile-btn dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/'.auth()->user()->photo) }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                            @endif
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-danger btn-sm px-3 rounded-pill d-flex align-items-center gap-1 shadow-sm" style="font-weight: 600;">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Sembunyikan dan Munculkan Sidebar Function Global --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('menu-toggle');
                const sidebar = document.querySelector('.sidebar');
                
                // Pastikan tidak melakukan set listener dua kali
                if (toggleBtn && sidebar && typeof window.sidebarToggleBound === 'undefined') {
                    window.sidebarToggleBound = true;
                    toggleBtn.addEventListener('click', function() {
                        sidebar.classList.toggle('collapsed');
                    });
                }
            });
        </script>
