<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Cerita - Narasumber</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body class="admin-cerita-page">
    <x-loading-screen />

<div id="wrapper">

    {{-- SIDEBAR --}}
    @include('layouts.sidebar-narasumber')

    {{-- CONTENT --}}
    <div class="content flex-fill">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE CONTENT --}}
        <div class="container mt-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-danger">Edit Cerita</h4>
                    <a href="{{ route('narasumber.cerita.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('narasumber.cerita.update', $cerita->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="judul" class="form-label fw-semibold">Judul Cerita</label>
                            <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $cerita->judul) }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="parwa_id" class="form-label fw-semibold">Parwa</label>
                                <select name="parwa_id" id="parwa_id" class="form-select @error('parwa_id') is-invalid @enderror" required>
                                    @foreach($parwas as $parwa)
                                        <option value="{{ $parwa->id }}" {{ $cerita->parwa_id == $parwa->id ? 'selected' : '' }}>
                                            {{ $parwa->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parwa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="sub_parwa" class="form-label fw-semibold">Sub Parwa</label>
                                <input type="text" name="sub_parwa" id="sub_parwa" class="form-control @error('sub_parwa') is-invalid @enderror" value="{{ old('sub_parwa', $cerita->sub_parwa) }}" required>
                                @error('sub_parwa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sumber" class="form-label fw-semibold">Sumber Cerita</label>
                            <input type="text" name="sumber" id="sumber" class="form-control @error('sumber') is-invalid @enderror" value="{{ old('sumber', $cerita->sumber) }}" required>
                            @error('sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cerita" class="form-label fw-semibold">Isi Cerita</label>
                            <textarea name="cerita" id="cerita" rows="12" class="form-control @error('cerita') is-invalid @enderror" required>{{ old('cerita', $cerita->cerita) }}</textarea>
                            @error('cerita')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold">Update Cerita</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
