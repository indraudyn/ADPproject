<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Dashboard & Page CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-user.css') }}">
</head>

<body class="admin-user-page">
    <x-loading-screen />

<div id="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-admin')

    {{-- CONTENT --}}
    <div class="content">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        <div class="page-wrapper">

            {{-- HEADER --}}
            <div class="page-header">
                <h3 class="fw-bold">User Management</h3>

                <form action="{{ route('admin.users.index') }}" method="GET">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search user or email..." value="{{ $search }}">
                    </div>
                </form>
            </div>

            {{-- CARD --}}
            <div class="user-card">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th class="text-end">Role Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td class="text-muted fw-medium">#{{ $user->id }}</td>
                                <td class="fw-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end">
                                        @php
                                            $roleClasses = [
                                                'admin' => 'role-select-admin',
                                                'user' => 'role-select-user',
                                                'narasumber' => 'role-select-narasumber'
                                            ];
                                            $currentClass = $roleClasses[$user->role] ?? '';
                                        @endphp

                                        <form method="POST" action="{{ route('admin.users.updateRole', $user->id) }}" style="min-width: 160px;">
                                            @csrf
                                            <select name="role"
                                                    class="form-select form-select-sm role-select-premium {{ $currentClass }}"
                                                    onchange="this.form.submit()">
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="narasumber" {{ $user->role === 'narasumber' ? 'selected' : '' }}>Narasumber</option>
                                            </select>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-people d-block mb-2 fs-2"></i>
                                    Tidak ada data user ditemukan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- REAL PAGINATION --}}
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() }}
                        to {{ $users->lastItem() }}
                        of {{ $users->total() }} entries
                    </div>

                    <div>
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin-user.js') }}"></script>
</body>
</html>
