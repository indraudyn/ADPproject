<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
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
    <x-loading-screen />

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
                <h3>Management Cerita</h3>

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
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($ceritas as $cerita)
                        <tr>
                            <td>{{ $cerita->id }}</td>
                            <td>{{ $cerita->user->name ?? 'User' }}</td>
                            <td>{{ $cerita->sumber }}</td>
                            <td>{{ $cerita->created_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('admin.cerita.updateStatus', $cerita->id) }}" method="POST" class="status-form">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select status-select" onchange="this.form.submit()">
                                        <option value="pending" {{ $cerita->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $cerita->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="unapproved" {{ $cerita->status === 'unapproved' || $cerita->status === 'rejected' ? 'selected' : '' }}>Unapproved</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.cerita.edit', $cerita->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="{{ route('cerita.show', $cerita->id) }}" class="btn btn-sm btn-outline-secondary me-2" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form id="delete-form-{{ $cerita->id }}" action="{{ route('admin.cerita.destroy', $cerita->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $cerita->id }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{-- PAGINATION --}}
                    <div class="pagination-wrapper mt-3 d-flex justify-content-between align-items-center">
                        <div class="pagination-info text-muted small">
                            Showing {{ $ceritas->firstItem() ?? 0 }}
                            to {{ $ceritas->lastItem() ?? 0 }}
                            of {{ $ceritas->total() }} entries
                        </div>
                        <div>
                            {{ $ceritas->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SWEETALERT DELETE --}}
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;
        Swal.fire({
            title: 'Hapus Cerita?',
            text: 'Cerita yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#8b0000',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });
});
</script>

{{-- ALERT SUKSES --}}
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session('success') }}',
    timer: 1500,
    showConfirmButton: false,
    customClass: {
        popup: 'rounded-4'
    }
});
</script>
@endif
</body>
</html>
