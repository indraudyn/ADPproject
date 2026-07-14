<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parwa - Asta Dasa Parwa</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
    <style>
        /* Specific overrides for Parwa page if needed */
        .hero-parwa {
            height: 50vh !important; /* Half height */
            min-height: 400px; /* Ensure content fits */
            background-image: linear-gradient(rgba(139, 0, 0, 0.4), rgba(139, 0, 0, 0.4)), url('/images/bgparwa.png');
        }
        .hero-parwa .hero-title-img {
            max-width: 250px; /* Smaller title */
        }
    </style>
</head>
<body>
    <x-loading-screen />

    <!-- NAVBAR -->
    <x-navbar />

    <!-- HERO SECTION -->
    <section class="hero hero-parwa d-flex align-items-center justify-content-center text-center text-white">
        <div class="container">
            <div class="hero-content">
                <img src="{{ asset('images/parwa.png') }}" alt="PARWA" class="img-fluid hero-title-img">
            </div>
        </div>
    </section>

    <!-- PARWA CARDS SECTION -->
    <section class="parwa-section py-5 bg-light">
        <div class="container-fluid px-5">
            <div class="row g-4 justify-content-center">
                @foreach($parwas as $parwa)
                <div class="col-md-4">
                    <div class="card parwa-card h-100 text-center p-5">
                        <h3 class="card-title fw-bold mb-3">{{ $parwa->name }}</h3>
                        <p class="card-text text-muted">
                            {{ $parwa->description }}
                        </p>
                        <div>
                            <a href="{{ route('parwa.detail', $parwa->slug) }}" class="btn btn-danger w-100">Selengkapnya</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
