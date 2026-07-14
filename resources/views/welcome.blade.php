<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asta Dasa Parwa</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
</head>
<body>
    <x-loading-screen />

    <!-- NAVBAR -->
    <x-navbar />
    <!-- HERO SECTION -->
    <section class="hero d-flex align-items-center justify-content-center text-center text-white">
        <div class="container">
            <div class="hero-content">
                <!-- Title Image or Text -->
                <!-- Use the image if it contains the stylized text, otherwise fallback to text -->
                <img src="{{ asset('images/astadasaparwa.png') }}" alt="Asta Dasa Parwa" class="img-fluid mb-3 hero-title-img">
                
                <p class="hero-description mx-auto">
                    Asta Dasa Parwa adalah sebutan untuk delapan belas kitab (bagian) yang menyusun epos besar Mahabharata. 
                    Berasal dari bahasa Sanskerta, Asta berarti delapan, Dasa berarti sepuluh, dan Parwa berarti kitab atau bagian.
                </p>
            </div>
        </div>
    </section>

    <!-- PARWA SECTION -->
    <section id="parwa" class="parwa-section py-5">
        <div class="container-fluid px-5">
            <h2 class="text-center section-title mb-5">PARWA</h2>
            
            <div class="row g-4 justify-content-center">
                @foreach($parwas as $parwa)
                <div class="col-md-4">
                    <div class="card parwa-card h-100 text-center p-5">
                        <h3 class="card-title fw-bold mb-3">{{ $parwa->name }}</h3>
                        <p class="card-text text-muted">
                            {{ \Illuminate\Support\Str::limit($parwa->description, 100) }}
                        </p>
                        <div>
                            <a href="{{ route('parwa.detail', $parwa->slug) }}" class="btn btn-danger w-100">Selengkapnya</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('parwa.index') }}" class="btn btn-danger btn-lg px-5">Lihat Selengkapnya</a>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>
