<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Audio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/upload-cerita.css') }}">
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

        {{-- PAGE CONTENT --}}
        <div class="container-fluid mt-4">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h3 class="mb-0">Upload Audio</h3>

                <div class="d-flex align-items-center gap-3">
                    {{-- Search (UI only) --}}
                    <div class="input-group search-box">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search">
                    </div>

                    {{-- Create New --}}
                    <a href="{{ route('audio.create') }}" class="btn btn-create d-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-plus-lg"></i>
                        <span>Upload Audio</span>
                    </a>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="card table-card">
                <div class="card-body">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse ($audios as $index => $audio)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $audio->title }}</td>
                                <td>{{ $audio->type === 'link' ? 'Link/URL' : 'Upload File' }}</td>
                                <td>{{ $audio->created_at->format('Y - m - d') }}</td>
                                <td>
                                    @if ($audio->status === 'approved')
                                        <span class="badge approved">Approved</span>
                                    @elseif ($audio->status === 'rejected')
                                        <span class="badge rejected">Rejected</span>
                                    @else
                                        <span class="badge pending">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('audio.edit', $audio->id) }}" class="btn btn-sm btn-outline-primary me-1" style="border-radius: 8px;">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('audio.destroy', $audio->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px;" onclick="return confirm('Yakin hapus audio ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada audio yang diupload
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
