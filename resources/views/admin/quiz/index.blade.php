<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Kuis</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-quiz.css') }}">
</head>

<body class="admin-quiz-page">
    <x-loading-screen />

<div id="wrapper">

@include('layouts.sidebar-admin')

<div class="content">

@include('layouts.topbar-user')

<div class="page-wrapper">

{{-- ================= HEADER ================= --}}
<div class="page-header">
    <h3>
        <i></i> Manajemen Kuis
    </h3>

    <div class="header-action">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Search">
        </div>

        <a href="{{ route('admin.quiz.create') }}" class="btn-create">
            <i class="bi bi-plus-circle-fill"></i> Create New
        </a>
    </div>
</div>

{{-- ================= CARD ================= --}}
<div class="quiz-card">

    <table class="table">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="50%">Pertanyaan Soal</th>
                <th width="20%">Dibuat Pada</th>
                <th width="20%" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($questions as $q)
            <tr>
                <td><span class="badge bg-light text-secondary border">#{{ $q->id }}</span></td>
                <td>
                    <span class="question-text" title="{{ $q->question }}">
                        {{ $q->question }}
                    </span>
                </td>
                <td>
                    <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $q->created_at->format('d M Y') }}</span>
                </td>

                <td>
                    <div class="action-buttons">
                        {{-- EDIT --}}
                        <a href="{{ route('admin.quiz.edit', $q->id) }}" class="btn-icon edit" title="Edit Soal">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        {{-- DELETE --}}
                        <button type="button" class="btn-icon delete btn-delete" data-id="{{ $q->id }}" title="Hapus Soal">
                            <i class="bi bi-trash3-fill"></i>
                        </button>

                        <form id="delete-form-{{ $q->id }}" action="{{ route('admin.quiz.destroy', $q->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada soal kuis yang ditambahkan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ================= FOOTER ================= --}}
    <div class="table-footer">
        <div class="text-muted">
            Menampilkan <strong>{{ $questions->firstItem() ?? 0 }}</strong> 
            hingga <strong>{{ $questions->lastItem() ?? 0 }}</strong> 
            dari <strong>{{ $questions->total() }}</strong> soal
        </div>

        <div>
            {{ $questions->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

</div>
</div>
</div>

{{-- ================= SCRIPT ================= --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/admin-quiz.js') }}"></script>

{{-- SWEETALERT DELETE --}}
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {

        let id = this.dataset.id;

        Swal.fire({
            title: 'Hapus Soal?',
            text: 'Soal yang dihapus tidak bisa dikembalikan!',
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
