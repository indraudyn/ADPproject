<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Manajemen Audio (Narasumber)</title>
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
    @include('layouts.sidebar-narasumber')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE CONTENT --}}
        <div class="container-fluid mt-4">

            {{-- HEADER --}}
            <div class="page-header">
                <h3>Management Audio (Narasumber)</h3>

                <div class="header-action">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text"
                            class="form-control"
                            placeholder="Search audio...">
                    </div>

                    <a href="{{ route('narasumber.audio.create') }}" class="btn btn-danger btn-create d-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-plus-lg"></i> Upload Audio
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
                                <th>Uploader</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($audios as $audio)
                        <tr>
                            <td>{{ $audio->id }}</td>
                            <td>{{ $audio->user->name ?? 'Admin/Narasumber' }}</td>
                            <td>{{ $audio->title }}</td>
                            <td>{{ $audio->type === 'link' ? 'Link/URL' : 'Upload File' }}</td>
                            <td>{{ $audio->created_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('narasumber.audio.updateStatus', $audio->id) }}" method="POST" class="status-form">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select status-select" onchange="this.form.submit()">
                                        <option value="pending" {{ $audio->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $audio->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $audio->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('narasumber.audio.edit', $audio->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form id="delete-form-{{ $audio->id }}" action="{{ route('narasumber.audio.destroy', $audio->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $audio->id }}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        Showing {{ $audios->firstItem() ?? 0 }}
                        to {{ $audios->lastItem() ?? 0 }}
                        of {{ $audios->total() }} entries
                    </div>

                    <div>
                        {{ $audios->links('pagination::bootstrap-5') }}
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
            title: 'Hapus Audio?',
            text: 'Audio yang dihapus tidak bisa dikembalikan!',
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

</body>
</html>
