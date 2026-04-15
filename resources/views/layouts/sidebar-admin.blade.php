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
            <li class="{{ request()->routeIs('admin.video.*') ? 'active' : '' }}">
                <a href="{{ route('admin.video.index') }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Video</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.quiz.index') ? 'active' : '' }}">
                <a href="{{ route('admin.quiz.index') }}">
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
