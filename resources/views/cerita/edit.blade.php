<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-edit.css') }}">
</head>

<body class="cerita-edit-page">

<div id="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-user')

    {{-- CONTENT --}}
    <div class="content">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE --}}
        <div class="page-wrapper">

            {{-- HEADER --}}
            <div class="page-header">
                <a href="{{ route('cerita.upload') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3>Edit Cerita</h3>
            </div>

            {{-- CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form method="POST" action="{{ route('cerita.update', $cerita->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Sumber</label>
                            <input type="text"
                                   name="sumber"
                                   class="form-control"
                                   value="{{ $cerita->sumber }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cerita</label>

                            <div id="editor" class="quill-editor">
                                {!! $cerita->cerita !!}
                            </div>

                            <input type="hidden" name="cerita" id="ceritaInput">
                        </div>

                        <div class="text-end">
                            <button class="btn btn-upload">
                                Simpan Perubahan
                                <i class="bi bi-save ms-1"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="{{ asset('js/cerita-edit.js') }}"></script>

</body>
</html>
