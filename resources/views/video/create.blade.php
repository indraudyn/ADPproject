<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Upload Video</title>
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
<body>
    <x-loading-screen />

<div class="d-flex">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-user')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE TITLE --}}
        <div class="container-fluid mt-4">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('video.upload') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Upload Video</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form action="{{ route('video.storeUser') }}" method="POST" enctype="multipart/form-data" id="videoForm">
                        @csrf
                        <input type="hidden" name="type" id="videoType" value="youtube">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Parwa</label>
                            <select name="parwa_id" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Parwa --</option>
                                @foreach($parwas as $parwa)
                                    <option value="{{ $parwa->id }}">{{ $parwa->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="sectionWrapper" style="display: none;">
                            <label class="form-label fw-bold">Pilih Bab / Section</label>
                            <select name="section" id="sectionSelect" class="form-select">
                                <option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>
                            </select>
                            <div id="sectionLoading" class="text-muted small mt-1" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Memuat daftar bab...
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Versi (Opsional)</label>
                            <select name="version" class="form-select">
                                <option value="" selected>-- Tanpa Versi --</option>
                                @foreach($versions as $ver)
                                    <option value="{{ $ver }}">{{ $ver }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Video</label>
                            <input type="text" name="title" class="form-control" placeholder="Masukkan judul video" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sumber Video</label>
                            <ul class="nav nav-tabs mb-3" id="videoTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="link-tab" data-bs-toggle="tab" data-bs-target="#link-content" type="button" role="tab" onclick="document.getElementById('videoType').value='youtube'">Link YouTube</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-content" type="button" role="tab" onclick="document.getElementById('videoType').value='upload'">Upload Video</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="videoTabContent">
                                <div class="tab-pane fade show active" id="link-content" role="tabpanel">
                                     <div class="mb-3">
                                        <label for="youtubeLink" class="form-label">Link YouTube</label>
                                        <input type="url" name="url" class="form-control" id="youtubeLink" placeholder="https://youtube.com/watch?v=...">
                                     </div>
                                </div>
                                <div class="tab-pane fade" id="upload-content" role="tabpanel">
                                    <div class="mb-3">
                                        <label for="videoFile" class="form-label">File Video (.mp4, .mpeg, .mov)</label>
                                        <input type="file" name="video_file" class="form-control" id="videoFile" accept="video/*">
                                        <small class="text-muted">Maksimum 50MB</small>
                                     </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-upload px-5">
                                Upload Video <i class="bi bi-camera-video ms-1"></i>
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

    if (parwaSelect) {
        parwaSelect.addEventListener('change', function() {
            const selectedOption = parwaSelect.options[parwaSelect.selectedIndex];
            const bookName = selectedOption.text;

            if (!bookName) {
                sectionWrapper.style.display = 'none';
                return;
            }

            // Show loading
            sectionWrapper.style.display = 'block';
            sectionSelect.style.display = 'none';
            sectionLoading.style.display = 'block';
            sectionSelect.innerHTML = '<option value="" selected>-- Tanpa Bab (Tampil di Detail Parwa) --</option>';

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
                            sectionSelect.appendChild(option);
                        });
                    } else {
                        // Fallback
                        const option = document.createElement('option');
                        option.value = 'Bab I';
                        option.textContent = 'Bab I (Default)';
                        sectionSelect.appendChild(option);
                    }
                })
                .catch(err => {
                    console.error("Error fetching sections:", err);
                    sectionLoading.style.display = 'none';
                    sectionSelect.style.display = 'block';
                    
                    // Fallback
                    const option = document.createElement('option');
                    option.value = 'Bab I';
                    option.textContent = 'Bab I (Fallback)';
                    sectionSelect.appendChild(option);
                });
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
