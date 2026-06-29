<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Video (Narasumber)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-create.css') }}">
    <style>
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
            border: none;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: #8b0000;
            border-bottom: 2px solid #8b0000;
            background: transparent;
        }
    </style>
</head>
<body class="admin-cerita-page">
    <x-loading-screen />

<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-narasumber')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE TITLE --}}
        <div class="container-fluid mt-4">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('narasumber.video.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Edit Video</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form action="{{ route('narasumber.video.update', $video->id) }}" method="POST" enctype="multipart/form-data" id="videoForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" id="videoType" value="{{ $video->type }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Parwa</label>
                            <select name="parwa_id" class="form-select" required>
                                <option value="" disabled>-- Pilih Parwa --</option>
                                @foreach($parwas as $parwa)
                                    <option value="{{ $parwa->id }}" {{ $video->parwa_id == $parwa->id ? 'selected' : '' }}>{{ $parwa->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="sectionWrapper" style="display: none;">
                            <label class="form-label fw-bold">Pilih Bab / Section</label>
                            <select name="section" id="sectionSelect" class="form-select" required>
                                <option value="" disabled>-- Pilih Bab / Section --</option>
                            </select>
                            <div id="sectionLoading" class="text-muted small mt-1" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Memuat daftar bab...
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Versi (Opsional)</label>
                            <select name="version" class="form-select">
                                <option value="">-- Tanpa Versi --</option>
                                @foreach($versions as $ver)
                                    <option value="{{ $ver }}" {{ $video->version === $ver ? 'selected' : '' }}>{{ $ver }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Video</label>
                            <input type="text" name="title" class="form-control" placeholder="Masukkan judul video" value="{{ $video->title }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sumber Video</label>
                            <ul class="nav nav-tabs mb-3" id="videoTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $video->type === 'youtube' ? 'active' : '' }}" id="link-tab" data-bs-toggle="tab" data-bs-target="#link-content" type="button" role="tab" onclick="document.getElementById('videoType').value='youtube'">Link YouTube</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $video->type === 'upload' ? 'active' : '' }}" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-content" type="button" role="tab" onclick="document.getElementById('videoType').value='upload'">Upload Video</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="videoTabContent">
                                <div class="tab-pane fade {{ $video->type === 'youtube' ? 'show active' : '' }}" id="link-content" role="tabpanel">
                                     <div class="mb-3">
                                        <label for="youtubeLink" class="form-label">Link YouTube</label>
                                        <input type="url" name="url" class="form-control" id="youtubeLink" placeholder="https://youtube.com/watch?v=..." value="{{ $video->type === 'youtube' ? $video->url : '' }}">
                                     </div>
                                </div>
                                <div class="tab-pane fade {{ $video->type === 'upload' ? 'show active' : '' }}" id="upload-content" role="tabpanel">
                                     <div class="mb-3">
                                        <label for="videoFile" class="form-label">File Video (.mp4, .mpeg, .mov)</label>
                                        @if($video->type === 'upload' && $video->url)
                                            <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-play"></i>
                                                <span>File saat ini: <strong>{{ basename($video->url) }}</strong></span>
                                            </div>
                                        @endif
                                        <input type="file" name="video_file" class="form-control" id="videoFile" accept="video/*">
                                        <small class="text-muted">Maksimum 50MB. Biarkan kosong jika tidak ingin mengubah video.</small>
                                     </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-upload px-5">
                                Simpan Perubahan <i class="bi bi-check-lg ms-1"></i>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const parwaSelect = document.querySelector('select[name="parwa_id"]');
    const sectionWrapper = document.getElementById('sectionWrapper');
    const sectionSelect = document.getElementById('sectionSelect');
    const sectionLoading = document.getElementById('sectionLoading');
    const currentSection = "{{ $video->section }}";

    function loadSections(bookName, selectVal = null) {
        if (!bookName) {
            sectionWrapper.style.display = 'none';
            return;
        }

        // Show loading
        sectionWrapper.style.display = 'block';
        sectionSelect.style.display = 'none';
        sectionLoading.style.display = 'block';
        sectionSelect.innerHTML = '<option value="" disabled>-- Pilih Bab / Section --</option>';

        fetch(`/api/parwa/sections-by-book?book=${encodeURIComponent(bookName)}`)
            .then(response => response.json())
            .then(res => {
                sectionLoading.style.display = 'none';
                sectionSelect.style.display = 'block';
                
                const sections = res.data || [];
                if (sections.length > 0) {
                    sections.forEach(sec => {
                        const option = document.createElement('option');
                        option.value = sec.section;
                        option.textContent = sec.section + (sec.sub_parva && sec.sub_parva !== '-' ? ` (${sec.sub_parva})` : '');
                        if (selectVal && sec.section === selectVal) {
                            option.selected = true;
                        }
                        sectionSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = 'Bab I';
                    option.textContent = 'Bab I (Default)';
                    if (selectVal && selectVal === 'Bab I') option.selected = true;
                    sectionSelect.appendChild(option);
                }
            })
            .catch(err => {
                console.error("Error fetching sections:", err);
                sectionLoading.style.display = 'none';
                sectionSelect.style.display = 'block';
                
                const option = document.createElement('option');
                option.value = 'Bab I';
                option.textContent = 'Bab I (Fallback)';
                if (selectVal && selectVal === 'Bab I') option.selected = true;
                sectionSelect.appendChild(option);
            });
    }

    if (parwaSelect) {
        // Load initial sections
        if (parwaSelect.value) {
            const selectedOption = parwaSelect.options[parwaSelect.selectedIndex];
            loadSections(selectedOption.text, currentSection);
        }

        parwaSelect.addEventListener('change', function() {
            const selectedOption = parwaSelect.options[parwaSelect.selectedIndex];
            loadSections(selectedOption.text);
        });
    }
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
