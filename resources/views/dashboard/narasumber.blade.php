<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Narasumber</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
</head>
<body>
    <x-loading-screen />

<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-narasumber')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- DASHBOARD --}}
        <div class="container-fluid mt-4">

            <h3 class="mb-4">Dashboard Narasumber</h3>

            {{-- STAT CARD --}}
            <div class="row g-4 mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card approved">
                        <h6>Approved</h6>
                        <h2>{{ $approvedCount }}</h2>
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="stat-card pending">
                        <h6>Pending</h6>
                        <h2>{{ $pendingCount }}</h2>
                        <i class="bi bi-clock"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="stat-card rejected">
                        <h6>Unapproved</h6>
                        <h2>{{ $unapprovedCount }}</h2>
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="card table-card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Cerita</h5>
                        <div class="d-flex gap-2">
                            <form action="{{ route('narasumber.dashboard') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control search-box" 
                                       placeholder="Search..." value="{{ request('search') }}"
                                       style="max-width: 250px;">
                            </form>
                            <a href="{{ route('cerita.create') }}" class="btn btn-danger btn-create shadow-sm">
                                <i class="bi bi-plus-lg"></i> Create New
                            </a>
                        </div>
                    </div>

                    <table class="table table-hover align-middle custom-admin-table">
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
                        @forelse ($ceritas as $cerita)
                        <tr>
                            <td>{{ $cerita->id }}</td>
                            <td>{{ $cerita->user->name ?? '-' }}</td>
                            <td>{{ $cerita->sumber }}</td>
                            <td>{{ $cerita->created_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('narasumber.cerita.updateStatus', $cerita->id) }}" method="POST" class="status-form">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm status-select" onchange="this.form.submit()" style="max-width: 140px;">
                                        <option value="approved" {{ $cerita->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="unapproved" {{ $cerita->status === 'unapproved' ? 'selected' : '' }}>Unapproved</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('narasumber.cerita.edit', $cerita->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="{{ route('cerita.show', $cerita->id) }}" class="btn btn-sm btn-outline-secondary mx-1" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form id="delete-form-{{ $cerita->id }}" action="{{ route('narasumber.cerita.destroy', $cerita->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $cerita->id }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada cerita.</td>
                        </tr>
                        @endforelse
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
<script src="{{ asset('js/dashboard-admin.js') }}"></script>
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
