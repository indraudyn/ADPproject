<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Create Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Quill CSS --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-create.css') }}">
</head>
<body>

<div class="d-flex">

    {{-- SIDEBAR --}}
    {{-- <aside class="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li onclick="location.href='{{ route('dashboard') }}'">
                <i class="bi bi-speedometer2"></i> Dashboard
            </li>
            <li class="active">
                <i class="bi bi-upload"></i> Upload Cerita
            </li>
            <li>
                <i class="bi bi-book"></i> Cerita
            </li>
            <li>
                <i class="bi bi-chat-dots"></i> Forum Diskusi
            </li>
            <li>
                <i class="bi bi-gear"></i> Settings
            </li>
        </ul>
    </aside> --}}

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE TITLE --}}
        <div class="container-fluid mt-4">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ url()->previous() }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Upload Cerita</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form action="{{ route('cerita.store') }}" method="POST" id="ceritaForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Sumber</label>
                            <input type="text" name="sumber" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cerita</label>

                            {{-- QUILL EDITOR --}}
                            <div id="editor" style="height: 300px;"></div>

                            {{-- HIDDEN INPUT --}}
                            <input type="hidden" name="cerita" id="ceritaInput">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-upload">
                                Upload <i class="bi bi-upload ms-1"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- Quill JS --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

{{-- INIT QUILL --}}
<script>
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Masukkan cerita...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['clean']
            ]
        }
    });

    // Kirim HTML ke backend
    document.getElementById('ceritaForm').addEventListener('submit', function () {
        document.getElementById('ceritaInput').value = quill.root.innerHTML;
    });
</script>

</body>
</html>
