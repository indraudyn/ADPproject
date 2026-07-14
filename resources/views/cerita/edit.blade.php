<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
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
    <x-loading-screen />

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

            {{-- ERROR ALERTS --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4 shadow-sm">
                    <h6 class="fw-bold mb-2">Gagal Menyimpan Cerita:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    @php
                        $contentLang = old('content_lang', $cerita->content_lang ?? 'id');
                        $langLabel   = $contentLang === 'en' ? 'English' : 'Bahasa Indonesia';
                        $langIcon    = $contentLang === 'en' ? '🇬🇧' : '🇮🇩';
                    @endphp

                    <form method="POST" action="{{ route('cerita.update', $cerita->id) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        {{-- Hidden: preserve upload language --}}
                        <input type="hidden" name="content_lang" value="{{ $contentLang }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Parwa / Buku</label>
                                <input type="text"
                                       name="book"
                                       class="form-control"
                                       value="{{ old('book', $cerita->book ?? $cerita->sumber) }}"
                                       placeholder="Contoh: Adi Parva"
                                       readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bab (Section)</label>
                                <input type="text"
                                       name="section"
                                       class="form-control"
                                       value="{{ old('section', $cerita->section ?? '') }}"
                                       placeholder="Contoh: Bab I">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sumber URL</label>
                                <input type="text"
                                       name="sumber"
                                       class="form-control"
                                       value="{{ old('sumber', $cerita->sumber ?? $cerita->url ?? '') }}"
                                       placeholder="Contoh: https://sacred-texts.com/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Cerita</label>
                                <input type="text"
                                       name="judul"
                                       class="form-control"
                                       value="{{ old('judul', $cerita->judul ?? '') }}"
                                       placeholder="Judul detail cerita">
                            </div>
                        </div>

                        {{-- CONTENT EDITOR with language label --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="form-label mb-0">Isi Cerita</label>
                                <span class="badge rounded-pill"
                                      style="background: {{ $contentLang === 'en' ? '#0d6efd' : '#198754' }}; font-size: 0.75rem; padding: 4px 10px;">
                                    {{ $langIcon }} {{ $langLabel }}
                                </span>
                                <small class="text-muted">
                                    — Mengedit konten dalam bahasa ini (sesuai saat upload)
                                </small>
                            </div>

                            <div id="editor" class="quill-editor">
                                {!! old('cerita', $cerita->cerita ?? $cerita->isi ?? '') !!}
                            </div>

                            <input type="hidden" name="cerita" id="ceritaInput">
                        </div>

                        <div class="text-end">
                            <a href="{{ route('cerita.upload') }}" class="btn btn-outline-secondary me-2">
                                Batal <i class="bi bi-x ms-1"></i>
                            </a>
                            <button type="submit" class="btn btn-upload">
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

{{-- ALERT ERROR --}}
@if ($errors->any())
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengupload',
            html: `
                <ul class="text-start mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#8b0000',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
@endif

@if (session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengupload',
            text: '{{ session('error') }}',
            confirmButtonColor: '#8b0000',
            customClass: {
                popup: 'rounded-4'
            }
        });
    </script>
@endif

</body>
</html>
