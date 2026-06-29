    <aside class="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li class="{{ request()->routeIs('narasumber.dashboard') ? 'active' : '' }}">
                <a href="{{ route('narasumber.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('narasumber.cerita.*') ? 'active' : '' }}">
                <a href="{{ route('narasumber.cerita.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Cerita</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('narasumber.video.*') ? 'active' : '' }}">
                <a href="{{ route('narasumber.video.index') }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Video</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('narasumber.audio.*') ? 'active' : '' }}">
                <a href="{{ route('narasumber.audio.index') }}">
                    <i class="bi bi-music-note-beamed"></i>
                    <span>Audio</span>
                </a>
            </li>
        </ul>
    </aside>
