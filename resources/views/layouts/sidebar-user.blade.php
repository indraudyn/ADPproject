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

            <li class="{{ request()->routeIs('video.*') ? 'active' : '' }}">
                <a href="{{ route('video.upload') }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Upload Video</span>
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
