<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-cerita.css') }}">
</head>

<body class="admin-cerita-page">

<div id="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-admin')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE CONTENT --}}
        <div class="container-fluid mt-4">

            {{-- HEADER --}}
            <div class="page-header">
                <h3>Upload Cerita</h3>

                <div class="header-action">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text"
                            class="form-control"
                            placeholder="Search">
                    </div>

                    <a href="{{ route('cerita.create') }}"
                    class="btn btn-danger btn-create">
                        <i class="bi bi-plus-lg"></i> Create New
                    </a>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="card table-card">
                <div class="card-body">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nama</th>
                                <th>Sumber</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($ceritas as $cerita)
                        <tr>
                            <td>{{ $cerita->id }}</td>
                            <td>{{ $cerita->user->name }}</td>
                            <td>{{ $cerita->sumber }}</td>
                            <td>{{ $cerita->created_at->format('Y-m-d') }}</td>
                            <td>
                                <select class="form-select status-select">
                                    <option {{ $cerita->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option {{ $cerita->status === 'unapproved' ? 'selected' : '' }}>Unapproved</option>
                                </select>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('cerita.show', $cerita->id) }}" class="view-link">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION (SAMA DENGAN USER) --}}
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $ceritas->firstItem() }}
                        to {{ $ceritas->lastItem() }}
                        of {{ $ceritas->total() }} entries
                    </div>

                    <div>
                        {{ $ceritas->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>

        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
