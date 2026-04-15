<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Video (Admin)</title>
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
                <a href="{{ route('admin.video.index') }}" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="mb-0">Upload Video</h3>
            </div>

            {{-- FORM CARD --}}
            <div class="card create-card">
                <div class="card-body">

                    <form action="{{ route('admin.video.store') }}" method="POST" enctype="multipart/form-data" id="videoForm">
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

                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Video</label>
                            <input type="text" name="title" class="form-control" placeholder="Masukkan judul video untuk publik" required>
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
                                Terbitkan Video <i class="bi bi-send ms-1"></i>
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

</body>
</html>
