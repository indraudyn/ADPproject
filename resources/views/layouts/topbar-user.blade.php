        <div class="topbar d-flex justify-content-between align-items-center px-3">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-4">
                <a href="{{ url('/') }}" class="home-icon text-decoration-none text-center">
                    <i class="bi bi-house"></i>
                </a>

                <div class="dropdown">
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
