<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <x-loading-screen />

<div class="d-flex" id="wrapper">

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

            <li class="{{ request()->routeIs('video.upload') ? 'active' : '' }}">
                <a href="{{ route('video.upload') }}">
                    <i class="bi bi-camera-video"></i>
                    <span>Upload Video</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('audio.upload') ? 'active' : '' }}">
                <a href="{{ route('audio.upload') }}">
                    <i class="bi bi-music-note-beamed"></i>
                    <span>Upload Audio</span>
                </a>
            </li>

        </ul>
    </aside>

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- NAVBAR --}}
        <div class="topbar d-flex justify-content-between align-items-center px-3">
            <button class="btn btn-light" id="menu-toggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-4">
                <a href="{{ url('/') }}" class="home-icon text-decoration-none text-center">
                    <i class="bi bi-house"></i>
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
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Profil</a></li>
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

        {{-- DASHBOARD --}}
        <div class="container-fluid mt-4">
            <x-content-loader />

            <h3 class="mb-4">Dashboard</h3>

            {{-- STAT CARD --}}
            <div class="row g-3">
                <div class="col-md-4 col-sm-12">
                    <div class="stat-card approved">
                        <h6>Approved</h6>
                        <h2>{{ $approvedCount }}</h2>
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="stat-card pending">
                        <h6>Pending</h6>
                        <h2>{{ $pendingCount }}</h2>
                        <i class="bi bi-clock"></i>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="stat-card rejected">
                        <h6>Unapproved</h6>
                        <h2>{{ $unapprovedCount }}</h2>
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            {{-- TABLE CERITA SAYA --}}
            <div class="card mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Cerita</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Sumber</th>
                                    <th>Status</th>
                                    <th class="text-end"></th>
                                </tr>
                            </thead>
                           <tbody id="ceritaTable">
                                @forelse ($ceritas as $cerita)
                                    <tr>
                                        <td>{{ $cerita->judul }}</td>
                                        <td>{{ $cerita->sumber }}</td>
                                        <td>
                                            @if($cerita->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($cerita->status == 'unapproved' || $cerita->status == 'rejected')
                                                <span class="badge bg-danger">Unapproved</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('cerita.show', $cerita->id) }}" class="detail-btn">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada cerita
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- TABLE VIDEO SAYA --}}
            <div class="card mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Video</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                           <tbody id="videoTable">
                                @forelse ($videos as $video)
                                    <tr>
                                        <td>{{ $video->title }}</td>
                                        <td>{{ $video->type === 'youtube' ? 'YouTube' : 'Upload File' }}</td>
                                        <td>
                                            @if($video->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($video->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Belum ada video
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TABLE AUDIO SAYA --}}
            <div class="card mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-2">
                        <h5 class="mb-0">Audio</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                           <tbody id="audioTable">
                                @forelse ($audios as $audio)
                                    <tr>
                                        <td>{{ $audio->title }}</td>
                                        <td>{{ $audio->type === 'link' ? 'Link/URL' : 'Upload File' }}</td>
                                        <td>
                                            @if($audio->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($audio->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Belum ada audio
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
