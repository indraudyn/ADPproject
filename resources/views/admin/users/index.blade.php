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
                <h3>User</h3>

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Search">
                </div>
            </div>

            {{-- CARD --}}
            <div class="user-card">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th class="text-end">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
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
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Tidak ada data user
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

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
