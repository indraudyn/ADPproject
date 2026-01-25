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

<div id="wrapper">

@include('layouts.sidebar-admin')

<div class="content">

@include('layouts.topbar-user')

<div class="page-wrapper">

{{-- ================= HEADER ================= --}}
<div class="page-header">
    <h3>Kuis</h3>

    <div class="header-action">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search">
        </div>

        <a href="{{ route('admin.quiz.create') }}" class="btn btn-danger btn-create">
            Create New <i class="bi bi-plus-lg"></i>
        </a>
    </div>
</div>

{{-- ================= CARD ================= --}}
<div class="quiz-card">

<table class="table align-middle">
<thead>
<tr>
    <th>Id</th>
    <th>Soal</th>
    <th>Tanggal Upload</th>
    <th class="text-end">Aksi</th>
</tr>
</thead>

<tbody>
@forelse ($questions as $q)
<tr>
    <td>{{ $q->id }}</td>
    <td>{{ $q->question }}</td>
    <td>{{ $q->created_at->format('Y-m-d') }}</td>

    <td class="text-end">

        {{-- EDIT --}}
        <a href="{{ route('admin.quiz.edit',$q->id) }}" class="btn-icon">
            <i class="bi bi-pencil"></i>
        </a>

        {{-- DELETE --}}
        <button type="button"
                class="btn-icon text-danger btn-delete"
                data-id="{{ $q->id }}">
            <i class="bi bi-trash"></i>
        </button>

        <form id="delete-form-{{ $q->id }}"
              action="{{ route('admin.quiz.destroy',$q->id) }}"
              method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

    </td>
</tr>
@empty
<tr>
<td colspan="4" class="text-center text-muted py-4">
    Belum ada soal kuis
</td>
</tr>
@endforelse
</tbody>
</table>

{{-- ================= FOOTER ================= --}}
<div class="table-footer">
    <div>
        Showing {{ $questions->firstItem() }}
        to {{ $questions->lastItem() }}
        of {{ $questions->total() }} entries
    </div>

    {{ $questions->links('pagination::bootstrap-5') }}
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
            cancelButtonText: 'Cancel'
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
    showConfirmButton: false
});
</script>
@endif

</body>
</html>
