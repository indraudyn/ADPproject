<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Cerita - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
        <div class="container mt-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-danger">Edit Cerita</h4>
                    <a href="{{ route('admin.cerita.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4">

                    {{-- ERROR --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $contentLang = old('content_lang', $cerita->content_lang ?? 'id');
                        $langLabel   = $contentLang === 'en' ? 'English' : 'Bahasa Indonesia';
                        $langIcon    = $contentLang === 'en' ? '🇬🇧' : '🇮🇩';
                    @endphp

                    <form action="{{ route('admin.cerita.update', $cerita->id) }}" method="POST" id="editAdminForm">
                        @csrf
                        @method('PUT')

                        {{-- Hidden: preserve upload language --}}
                        <input type="hidden" name="content_lang" value="{{ $contentLang }}">

                        {{-- JUDUL --}}
                        <div class="mb-3">
                            <label for="judul" class="form-label fw-semibold">Judul Cerita</label>
                            <input type="text"
                                   name="judul"
                                   id="judul"
                                   class="form-control @error('judul') is-invalid @enderror"
                                   value="{{ old('judul', $cerita->judul) }}"
                                   required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            {{-- PARWA (book name sebagai value) --}}
                            <div class="col-md-6">
                                <label for="parwa_book" class="form-label fw-semibold">Parwa</label>
                                <select name="parwa_book" id="parwa_book"
                                        class="form-select @error('parwa_book') is-invalid @enderror"
                                        required>
                                    <option value="" disabled>-- Pilih Parwa --</option>
                                    @foreach($parwas as $parwa)
                                        @php $bookVal = $parwa->book ?? $parwa->name ?? '' @endphp
                                        <option value="{{ $bookVal }}"
                                            {{ (old('parwa_book', $cerita->book ?? '') == $bookVal) ? 'selected' : '' }}>
                                            {{ $bookVal }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parwa_book')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SUB PARWA --}}
                            <div class="col-md-6">
                                <label for="sub_parwa" class="form-label fw-semibold">Sub Parwa</label>
                                <input type="text"
                                       name="sub_parwa"
                                       id="sub_parwa"
                                       class="form-control @error('sub_parwa') is-invalid @enderror"
                                       value="{{ old('sub_parwa', $cerita->sub_parva ?? '') }}"
                                       placeholder="Contoh: Section I">
                                @error('sub_parwa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- SECTION / BAB --}}
                            <div class="col-md-6">
                                <label for="section" class="form-label fw-semibold">Bab (Section)</label>
                                <input type="text"
                                       name="section"
                                       id="section"
                                       class="form-control"
                                       value="{{ old('section', $cerita->section ?? '') }}"
                                       placeholder="Contoh: Bab I">
                            </div>

                            {{-- SUMBER URL --}}
                            <div class="col-md-6">
                                <label for="sumber" class="form-label fw-semibold">Sumber URL</label>
                                <input type="text"
                                       name="sumber"
                                       id="sumber"
                                       class="form-control @error('sumber') is-invalid @enderror"
                                       value="{{ old('sumber', $cerita->url ?? $cerita->sumber ?? '') }}"
                                       placeholder="Contoh: https://sacred-texts.com/...">
                                @error('sumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ISI CERITA (Quill) --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <label class="form-label fw-semibold mb-0">Isi Cerita</label>
                                <span class="badge rounded-pill"
                                      style="background: {{ $contentLang === 'en' ? '#0d6efd' : '#198754' }}; font-size:0.75rem; padding:4px 10px;">
                                    {{ $langIcon }} {{ $langLabel }}
                                </span>
                                <small class="text-muted">— Mengedit bahasa saat upload</small>
                            </div>
                            <div id="quillEditor" style="min-height: 300px;">
                                {!! old('cerita', $cerita->cerita ?? $cerita->isi ?? '') !!}
                            </div>
                            <input type="hidden" name="cerita" id="ceritaHidden">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.cerita.index') }}" class="btn btn-outline-secondary px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold">
                                <i class="bi bi-save me-1"></i> Update Cerita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#quillEditor', {
        theme: 'snow',
        placeholder: 'Tulis isi cerita di sini...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ header: [1, 2, 3, false] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ align: [] }],
                ['link'],
                ['clean']
            ]
        }
    });

    document.getElementById('editAdminForm').addEventListener('submit', function () {
        document.getElementById('ceritaHidden').value = quill.root.innerHTML;
    });
</script>

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
