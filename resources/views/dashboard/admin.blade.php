<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
</head>
<body>

<div class="d-flex">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li class="active">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-people"></i>
                    <span>User</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-book"></i>
                    <span>Cerita</span>
                </a>
            </li>
            <li>
                <a href="#">
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

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
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

            <h3 class="mb-4">Dashboard Admin</h3>

            {{-- STAT CARD --}}
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card total">
                        <h6>Total User</h6>
                        <h2>{{ $totalUser }}</h2>
                        <i class="bi bi-people"></i>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card approved">
                        <h6>Approved</h6>
                        <h2>{{ $approvedCount }}</h2>
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card pending">
                        <h6>Pending</h6>
                        <h2>{{ $pendingCount }}</h2>
                        <i class="bi bi-clock"></i>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="stat-card rejected">
                        <h6>Unapproved</h6>
                        <h2>{{ $unapprovedCount }}</h2>
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            {{-- USER TABLE --}}
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>User</h5>
                        <input type="text" class="form-control search-box" placeholder="Search">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th class="text-end">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.user.role', $user->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <select name="role"
                                                    class="form-select role-select"
                                                    onchange="this.form.submit()">
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                    Admin
                                                </option>
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                                    User
                                                </option>
                                                <option value="narasumber" {{ $user->role === 'narasumber' ? 'selected' : '' }}>
                                                    Narasumber
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/dashboard-admin.js') }}"></script>
</body>
</html>
