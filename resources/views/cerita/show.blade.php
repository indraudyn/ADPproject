<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-show.css') }}">
</head>
<body class="cerita-show-page">

<div id="wrapper">

    {{-- SIDEBAR --}}
    {{-- @include('layouts.sidebar-user') --}}

    {{-- CONTENT --}}
    <div class="content">

        {{-- TOPBAR --}}
        @include('layouts.topbar-user')

        {{-- PAGE WRAPPER --}}
        <div class="page-wrapper">

            {{-- PAGE HEADER --}}
            <div class="page-header">
                <a href="{{ url()->previous() }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3>Cerita</h3>
            </div>

            {{-- CERITA CARD --}}
            <div class="card cerita-card">
                <div class="card-body">

                    <div class="page-header">
                        <div class="avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <strong>{{ $cerita->user->name }}</strong>
                            <div class="text-muted small">
                                Sumber : {{ $cerita->sumber }}
                            </div>
                        </div>
                    </div>

                    <div class="cerita-content">
                        {!! $cerita->cerita !!}
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/cerita-show.js') }}"></script>

</body>
</html>
