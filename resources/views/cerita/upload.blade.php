<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/upload-cerita.css') }}">
</head>
<body>
    <x-loading-screen />

<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-user')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE CONTENT --}}
        <div class="container-fluid mt-4">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h3 class="mb-0">Upload Cerita</h3>

                <div class="d-flex align-items-center gap-3">
                    {{-- Search (UI only) --}}
                    <div class="input-group search-box">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search">
                    </div>

                    {{-- Create New --}}
                    <a href="{{ route('cerita.create') }}" class="btn btn-create d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Create New</span>
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
                                <th>Sumber</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse ($ceritas as $index => $cerita)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>{{ $cerita->sumber }}</td>

                                <td>{{ $cerita->created_at->format('Y - m - d') }}</td>

                                <td>
                                    @if ($cerita->status === 'approved')
                                        <span class="badge approved">Approved</span>
                                    @elseif ($cerita->status === 'unapproved')
                                        <span class="badge rejected">Unapproved</span>
                                    @else
                                        <span class="badge pending">Pending</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('cerita.edit', $cerita->id) }}" class="icon-btn">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a href="{{ route('cerita.show', $cerita->id) }}" class="icon-btn">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <form id="delete-form-{{ $cerita->id }}"
                                          action="{{ route('cerita.destroy', $cerita->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="icon-btn delete btn-delete" data-id="{{ $cerita->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada cerita yang diupload
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
            confirmButtonColor: '#8b1e1e', // Dark Red matching theme
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
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

{{-- ALERT ERROR --}}
@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '{{ session('error') }}',
    confirmButtonColor: '#8b1e1e',
    customClass: {
        popup: 'rounded-4'
    }
});
</script>
@endif
</body>
</html>
