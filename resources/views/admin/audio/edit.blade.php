<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Edit Audio (Admin)</title>
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
    @include('layouts.sidebar-admin')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE TITLE --}}
        <div class="container-fluid mt-4">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.audio.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Edit Audio</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form action="{{ route('admin.audio.update', $audio->id) }}" method="POST" enctype="multipart/form-data" id="audioForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="type" id="audioType" value="{{ $audio->type }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Parwa</label>
                            <select name="parwa_id" class="form-select" required>
                                <option value="" disabled>-- Pilih Parwa --</option>
                                @foreach($parwas as $parwa)
                                    <option value="{{ $parwa->id }}" {{ $audio->parwa_id == $parwa->id ? 'selected' : '' }}>{{ $parwa->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="sectionWrapper" style="display: none;">
                            <label class="form-label fw-bold">Pilih Bab / Section</label>
                            <select name="section" id="sectionSelect" class="form-select">
                                <option value="">-- Tanpa Bab (Tampil di Detail Parwa) --</option>
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
                                    <option value="{{ $ver }}" {{ $audio->version === $ver ? 'selected' : '' }}>{{ $ver }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Audio</label>
                            <input type="text" name="title" class="form-control" placeholder="Masukkan judul audio" value="{{ $audio->title }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sumber Audio</label>
                            <ul class="nav nav-tabs mb-3" id="audioTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ in_array($audio->type, ['upload', 'video']) ? 'active' : '' }}" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-content" type="button" role="tab" onclick="setAudioType('upload')">Upload Audio File</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ in_array($audio->type, ['link', 'youtube']) ? 'active' : '' }}" id="link-tab" data-bs-toggle="tab" data-bs-target="#link-content" type="button" role="tab" onclick="setAudioType('link')">Link / URL Audio</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="audioTabContent">
                                <div class="tab-pane fade {{ in_array($audio->type, ['upload', 'video']) ? 'show active' : '' }}" id="upload-content" role="tabpanel">
                                     <div class="mb-3">
                                        <label for="audioFile" class="form-label">File Audio (.mp3, .wav, .ogg, .m4a)</label>
                                        @if(in_array($audio->type, ['upload', 'video']) && $audio->url)
                                            <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center gap-2">
                                                <i class="bi bi-music-note-beamed"></i>
                                                <span>File saat ini: <strong>{{ basename($audio->url) }}</strong></span>
                                            </div>
                                        @endif
                                        <input type="file" name="audio_file" class="form-control" id="audioFile" accept="audio/*">
                                        <small class="text-muted">Maksimum 20MB. Biarkan kosong jika tidak ingin mengubah audio.</small>
                                     </div>
                                </div>
                                <div class="tab-pane fade {{ in_array($audio->type, ['link', 'youtube']) ? 'show active' : '' }}" id="link-content" role="tabpanel">
                                     <div class="mb-3">
                                        <label for="audioLink" class="form-label">URL Link Audio</label>
                                        <input type="url" name="url" class="form-control" id="audioLink" placeholder="https://example.com/audio.mp3" value="{{ in_array($audio->type, ['link', 'youtube']) ? $audio->url : '' }}">
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
function setAudioType(type) {
    document.getElementById('audioType').value = type;
    
    const audioFileInput = document.getElementById('audioFile');
    const audioLinkInput = document.getElementById('audioLink');
    
    if (audioFileInput) audioFileInput.disabled = (type !== 'upload');
    if (audioLinkInput) {
        audioLinkInput.disabled = (type !== 'link');
        if (type === 'link') {
            audioLinkInput.setAttribute('name', 'url');
        } else {
            audioLinkInput.removeAttribute('name');
        }
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let initialType = "{{ $audio->type }}";
    if (initialType === 'youtube') {
        initialType = 'link';
    } else if (initialType === 'video') {
        initialType = 'upload';
    }
    setAudioType(initialType);

    const parwaSelect = document.querySelector('select[name="parwa_id"]');
    const sectionWrapper = document.getElementById('sectionWrapper');
    const sectionSelect = document.getElementById('sectionSelect');
    const sectionLoading = document.getElementById('sectionLoading');
    const currentSection = "{{ $audio->section }}";

    function loadSections(bookName, selectVal = null) {
        if (!bookName) {
            sectionWrapper.style.display = 'none';
            return;
        }

        // Show loading
        sectionWrapper.style.display = 'block';
        sectionSelect.style.display = 'none';
        sectionLoading.style.display = 'block';
        sectionSelect.innerHTML = '<option value="">-- Tanpa Bab (Tampil di Detail Parwa) --</option>';

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
