<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>
    <x-loading-screen />

<div class="d-flex">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <h5 class="logo">ASTA<br>DASA PARWA</h5>

        <ul class="menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('cerita.upload') ? 'active' : '' }}">
                <a href="{{ route('cerita.upload') }}">
                    <i class="bi bi-upload"></i>
                    <span>Upload Cerita</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('cerita.index') ? 'active' : '' }}">
                <a href="{{ route('cerita.index') }}">
                    <i class="bi bi-book"></i>
                    <span>Cerita</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('forum.index') ? 'active' : '' }}">
                <a href="{{ route('forum.index') }}">
                    <i class="bi bi-chat-dots"></i>
                    <span>Forum Diskusi</span>
                </a>
            </li>

        </ul>
    </aside>

    {{-- CONTENT --}}
    <main class="content flex-fill">

        {{-- TOP BAR --}}
        <div class="topbar d-flex justify-content-between align-items-center px-3">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/') }}" class="home-link text-decoration-none">
                    <i class="bi bi-house fs-4"></i>
                </a>

                <div class="dropdown">
                    <button class="btn profile-btn dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        @if(auth()->user()->photo)
                            <img src="{{ asset('storage/'.auth()->user()->photo) }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        @else
                            <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                        @endif
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- PAGE --}}
        <div class="container-fluid p-4">

            {{-- BACK --}}
            <div class="d-flex align-items-center mb-4">
                <button onclick="window.location.href='{{ route('dashboard') }}'" class="btn btn-link back-btn p-0 text-dark me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </button>
                <h4 class="mb-0 fw-bold">Profil</h4>
            </div>

            {{-- CARD --}}
            <div class="profile-card mx-auto bg-white p-4 rounded-4 shadow-sm" style="max-width: 500px;">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')

                    {{-- PHOTO --}}
                    <div class="photo-wrapper text-center mb-4 position-relative">
                        <div class="position-relative d-inline-block">
                            <label for="photo" class="cursor-pointer">
                                <div class="photo-preview-container position-relative overflow-hidden rounded-circle border shadow-sm" style="width: 140px; height: 140px; background: #f8f9fa;">
                                    <img src="{{ auth()->user()->photo ? asset('storage/'.auth()->user()->photo) : '' }}" 
                                         id="preview" 
                                         class="w-100 h-100"
                                         style="object-fit: cover; {{ auth()->user()->photo ? '' : 'display: none;' }}">
                                    
                                    <div class="photo-placeholder w-100 h-100 d-flex align-items-center justify-content-center" id="placeholder" style="{{ auth()->user()->photo ? 'display: none;' : '' }}">
                                        <i class="bi bi-camera fs-1 text-muted"></i>
                                    </div>

                                    <div class="photo-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 opacity-0 hover-opacity-100 d-flex align-items-center justify-content-center transition-all">
                                        <i class="bi bi-pencil-fill text-white fs-4"></i>
                                    </div>
                                </div>
                            </label>

                            <button type="button" 
                                    class="edit-icon-btn position-absolute bottom-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center border border-white" 
                                    style="width: 36px; height: 36px; cursor: pointer; border: 3px solid white !important; z-index: 2;"
                                    onclick="document.getElementById('photo').click()">
                                <i class="bi bi-pencil-fill" style="font-size: 0.9rem;"></i>
                            </button>
                        </div>

                        <input type="file" id="photo" name="photo_raw" hidden accept="image/*">
                        <input type="hidden" name="photo_data" id="photoData">
                        
                        <div class="mt-3 d-flex justify-content-center gap-2">
                            @if(auth()->user()->photo)
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="confirmDeletePhoto()">
                                    <i class="bi bi-trash me-1"></i> Hapus Foto
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- NAME --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2"
                               value="{{ auth()->user()->name }}" required>
                    </div>

                    {{-- EMAIL --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 py-2"
                               value="{{ auth()->user()->email }}" required>
                    </div>

                    {{-- SAVE --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-danger w-100 py-2 rounded-3 fw-bold shadow-sm transition-all" style="background: #8b0000; border: none;">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

                {{-- Hidden Delete Form --}}
                <form id="deletePhotoForm" action="{{ route('profile.photo.destroy') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    </main>
</div>

<!-- Cropping Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Sesuaikan Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container" style="max-height: 400px; overflow: hidden;">
                    <img id="imageToCrop" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="cropButton">Gunakan</button>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/profile.js') }}"></script>

<style>
    .hover-opacity-100:hover { opacity: 1 !important; }
    .transition-all { transition: all 0.3s ease; }
    .cursor-pointer { cursor: pointer; }
    
    .profile-card input:focus {
        border-color: #8b0000;
        box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.1);
    }
    
    .btn-danger:hover {
        background: #a00000 !important;
        transform: translateY(-1px);
    }
</style>

<script>
    function confirmDeletePhoto() {
        Swal.fire({
            title: 'Hapus foto profil?',
            text: "Foto akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#8b0000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deletePhotoForm').submit();
            }
        })
    }
</script>

</body>
</html>
