<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<div class="d-flex" id="wrapper">

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
    <div class="content flex-fill">

        {{-- NAVBAR --}}
        <div class="topbar d-flex justify-content-between align-items-center px-3">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-4">
                <a href="{{ url('/') }}" class="home-icon text-decoration-none text-center">
                    <i class="bi bi-house"></i>
                </a>

                <div class="dropdown">
                    <button class="btn profile-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
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

        {{-- DASHBOARD --}}
        <div class="container-fluid mt-4">

            <h3 class="mb-4">Dashboard</h3>

            {{-- STAT CARD --}}
            <div class="row g-3">
                <div class="col-md-4 col-sm-12">
                    <div class="stat-card approved">
                        <h6>Approved</h6>
                        <h2>{{ $approvedCount }}</h2>
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="stat-card pending">
                        <h6>Pending</h6>
                        <h2>{{ $pendingCount }}</h2>
                        <i class="bi bi-clock"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="stat-card rejected">
                        <h6>Unapproved</h6>
                        <h2>{{ $unapprovedCount }}</h2>
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            {{-- TABLE CERITA APPROVED --}}
            <div class="card mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Cerita Disetujui</h5>
                        <input type="text" class="form-control w-auto" placeholder="Search">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Sumber</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ceritas as $cerita)
                                    <tr>
                                        <td>{{ $cerita->user->name }}</td>
                                        <td>{{ $cerita->sumber }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('cerita.show', $cerita->id) }}">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            Belum ada cerita
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></cript>
</body>
</html>
