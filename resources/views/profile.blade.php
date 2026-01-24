<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

<div class="d-flex">

    {{-- SIDEBAR --}}
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

    {{-- CONTENT --}}
    <main class="content flex-fill">

        {{-- TOP BAR --}}
        <div class="topbar d-flex justify-content-between align-items-center">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" class="home-link">
                    <i class="bi bi-house"></i>
                </a>

                <div class="dropdown">
                    <button class="btn profile-btn dropdown-toggle" data-bs-toggle="dropdown">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/'.auth()->user()->photo) }}" class="avatar">
                        @else
                            <i class="bi bi-person-circle"></i>
                        @endif
                        {{ auth()->user()->name }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
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

        {{-- PAGE --}}
        <div class="container-fluid p-4">

            {{-- BACK --}}
            <div class="d-flex align-items-center mb-4">
                <button onclick="history.back()" class="btn btn-link back-btn">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h4 class="mb-0">Profil</h4>
            </div>

            {{-- CARD --}}
            <div class="profile-card mx-auto">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- PHOTO --}}
                    <div class="photo-wrapper">
                        <label for="photo">
                            @if(auth()->user()->photo)
                                <img src="{{ asset('storage/'.auth()->user()->photo) }}" id="preview">
                            @else
                                <div class="photo-placeholder">
                                    <i class="bi bi-camera"></i>
                                </div>
                            @endif
                        </label>
                        <input type="file" id="photo" name="photo" hidden>
                        <p class="upload-text">Upload Photo</p>
                    </div>

                    {{-- NAME --}}
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ auth()->user()->name }}">
                    </div>

                    {{-- EMAIL --}}
                    <div class="mb-4">
                        <label class="form-label">Your email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ auth()->user()->email }}">
                    </div>

                    {{-- SAVE --}}
                    <div class="text-center">
                        <button class="btn btn-save px-5">Save</button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/profile.js') }}"></script>
</body>
</html>
