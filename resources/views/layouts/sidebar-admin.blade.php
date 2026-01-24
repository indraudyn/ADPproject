    <aside class="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>User</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.cerita.index') ? 'active' : '' }}">
                <a href="{{ route('admin.cerita.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Cerita</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.forum.index') ? 'active' : '' }}">
                <a href="{{ route('admin.forum.index') }}">
                    <i class="bi bi-chat-dots"></i>
                    <span>Forum Diskusi</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-question-circle"></i>
                    <span>Kuis</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </aside>
