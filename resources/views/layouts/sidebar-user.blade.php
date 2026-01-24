    <aside class="sidebar" id="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('cerita.upload') ? 'active' : '' }}">
                <a href="{{ route('cerita.upload') }}">
                    <i class="bi bi-upload"></i>
                    <span>Upload Cerita</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('cerita.index') ? 'active' : '' }}">
                <a href="{{ route('cerita.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Cerita</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('forum.index') ? 'active' : '' }}">
                <a href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots"></i>
                    <span>Forum Diskusi</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('settings') ? 'active' : '' }}">
                <a href="{{ route('settings') }}">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </aside>
