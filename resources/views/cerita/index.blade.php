<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Dashboard CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-user.css') }}">
</head>
<body class="cerita-user-page">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="page-title">Cerita</h3>

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search">
                </div>
            </div>

            {{-- TABLE CARD --}}
            <div class="card cerita-card">
                <div class="card-body p-0">

                    <table class="table cerita-table mb-0">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nama</th>
                                <th>Sumber</th>
                                <th>Tanggal Upload</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="ceritaTable">
                            @forelse ($ceritas as $cerita)
                                <tr>
                                    <td>{{ $cerita->id }}</td>
                                    <td>{{ $cerita->user->name }}</td>
                                    <td>{{ $cerita->sumber }}</td>
                                    <td>{{ $cerita->created_at->format('Y - m - d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('cerita.show', $cerita->id) }}" class="detail-btn">
                                            <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada cerita
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Showing {{ $ceritas->firstItem() }} to {{ $ceritas->lastItem() }} of {{ $ceritas->total() }} entries
                </small>

                {{ $ceritas->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/cerita-user.js') }}"></script>

</body>
</html>
