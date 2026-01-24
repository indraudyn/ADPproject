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
    @include('layouts.sidebar-admin')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

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
