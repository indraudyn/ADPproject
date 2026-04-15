<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cerita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script:wght@400;700&display=swap" rel="stylesheet">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cerita-show.css') }}">
</head>
<body class="cerita-show-page">
    <x-loading-screen />

{{-- HEADER MERAH --}}
<header class="story-header">
    <a href="{{ route('parwa.detail', $cerita->parwa->slug) }}" class="back-btn-float">
        <i class="bi bi-chevron-left"></i>
    </a>
    
    <h1 class="parwa-main-title">{{ $cerita->parwa->nama ?? 'Adi Parwa' }}</h1>
    <p class="sub-parwa-subtitle">{{ $cerita->sub_parwa ?? 'Section I' }}</p>
</header>

{{-- CONTENT WRAPPER --}}
<main class="story-detail-container">
    <div class="container">
        <article class="story-content-card">
            
            <h2 class="story-title-heading">{{ $cerita->judul }}</h2>

            <div class="story-text-body">
                {!! $cerita->cerita !!}
            </div>

            <hr class="my-5">

            <div class="d-flex align-items-center gap-2 text-muted small">
                <i class="bi bi-person-circle"></i>
                <span>Oleh: <strong>{{ $cerita->user->name }}</strong></span>
                <span class="mx-2">•</span>
                <span>Sumber: {{ $cerita->sumber }}</span>
            </div>

        </article>
    </div>

    {{-- CERITA LAINNYA DI SUB PARWA INI --}}
    @if(isset($relatedStories) && $relatedStories->count() > 0)
    <div class="mt-5 w-100" style="background-color: transparent; padding: 3rem 0; border-top: 1px solid #e2e8f0;">
        <div class="container mb-4">
            <h2 class="fw-bolder text-dark" style="font-family: 'Figtree', sans-serif; font-size: 1.5rem; letter-spacing: -0.2px;">Lainnya :</h2>
        </div>
        
        <div class="position-relative w-100 mx-auto" style="max-width: 1440px; overflow: hidden;">
            <!-- Left Gradient & Arrow -->
            <div id="nav-left" class="position-absolute top-0 bottom-0 start-0 d-flex align-items-center justify-content-start" style="width: 150px; background: linear-gradient(to right, rgba(248,250,252,1) 40%, rgba(248,250,252,0) 100%); z-index: 5; transition: opacity 0.3s ease; opacity: 0; pointer-events: none;">
                <button onclick="document.getElementById('relatedScroll').scrollBy({left: -350, behavior: 'smooth'})" class="btn border-0 p-0 ms-4" style="background: transparent;">
                    <i class="bi bi-chevron-left" style="color: #8b0000; font-size: 5rem; font-weight: 900; line-height: 1; filter: drop-shadow(0 0 10px rgba(255,255,255,0.8));"></i>
                </button>
            </div>

            <div class="d-flex overflow-auto" id="relatedScroll" style="scroll-snap-type: x mandatory; gap: 2.5rem; padding: 2rem 8rem; scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    #relatedScroll::-webkit-scrollbar { display: none; }
                    .related-card {
                        flex: 0 0 340px;
                        scroll-snap-align: center;
                        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    }
                    .related-card:hover {
                        transform: translateY(-10px) scale(1.02);
                    }
                    .related-card .card {
                        border-radius: 20px;
                        background: white;
                        border: 1px solid rgba(0,0,0,0.03);
                    }
                </style>
                
                @foreach($relatedStories as $related)
                <div class="related-card">
                    <div class="card h-100 shadow-lg border-0">
                        <div class="card-body p-4 d-flex flex-column justify-content-between text-start">
                            <div>
                                <h5 class="fw-bold mb-3" style="color: #1a1a1a; font-family: 'Figtree', sans-serif; font-size: 1.25rem; line-height: 1.5; min-height: 3.8rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $related->judul }}</h5>
                                <div class="d-flex align-items-center mb-2 text-dark opacity-75" style="font-size: 0.95rem; font-weight: 500;">
                                    <div class="bg-dark rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 28px; height: 28px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <span>Oleh : {{ $related->user->name }}</span>
                                </div>
                                <div class="text-muted small ms-4 ps-2" style="font-size: 0.85rem; border-left: 2px solid #e2e8f0; margin-top: 0.5rem;">
                                    Sumber : {{ $related->sumber }}
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('cerita.show', $related->id) }}" class="btn w-100 text-white py-2 shadow-sm" style="background: linear-gradient(135deg, #8b1e1e 0%, #a52a2a 100%); border-radius: 12px; font-weight: 700; font-size: 1rem; transition: all 0.3s ease;">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Right Gradient & Arrow -->
            <div id="nav-right" class="position-absolute top-0 bottom-0 end-0 d-flex align-items-center justify-content-end" style="width: 150px; background: linear-gradient(to left, rgba(248,250,252,1) 40%, rgba(248,250,252,0) 100%); z-index: 5; transition: opacity 0.3s ease; opacity: 0; pointer-events: none;">
                <button onclick="document.getElementById('relatedScroll').scrollBy({left: 350, behavior: 'smooth'})" class="btn border-0 p-0 me-4" style="background: transparent;">
                    <i class="bi bi-chevron-right" style="color: #8b0000; font-size: 5rem; font-weight: 900; line-height: 1; filter: drop-shadow(0 0 10px rgba(255,255,255,0.8));"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollContainer = document.getElementById('relatedScroll');
            const navLeft = document.getElementById('nav-left');
            const navRight = document.getElementById('nav-right');

            function updateNav() {
                const scrollLeft = scrollContainer.scrollLeft;
                const scrollWidth = scrollContainer.scrollWidth;
                const clientWidth = scrollContainer.clientWidth;

                // Show left arrow if we have scrolled
                if (scrollLeft > 20) {
                    navLeft.style.opacity = '1';
                    navLeft.style.pointerEvents = 'auto';
                } else {
                    navLeft.style.opacity = '0';
                    navLeft.style.pointerEvents = 'none';
                }

                // Show right arrow if there is more to scroll
                if (scrollLeft + clientWidth < scrollWidth - 20) {
                    navRight.style.opacity = '1';
                    navRight.style.pointerEvents = 'auto';
                } else {
                    navRight.style.opacity = '0';
                    navRight.style.pointerEvents = 'none';
                }
            }

            // Initial check
            setTimeout(updateNav, 100);

            // Update on scroll
            scrollContainer.addEventListener('scroll', updateNav);

            // Update on window resize
            window.addEventListener('resize', updateNav);
        });
    </script>
    @endif
</main>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/cerita-show.js') }}"></script>

</body>
</html>
