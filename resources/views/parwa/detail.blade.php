<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $parwa->name }} - Cerita</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|cinzel:400,700|oleo-script:400,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        *,
        *::before,
        *::after { box-sizing: border-box; }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #e8eaef 100%);
            margin: 0;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════
           HEADER
           ═══════════════════════════════════════ */
        .parwa-header {
            background: linear-gradient(135deg, #7a1212 0%, #a52020 50%, #8b0000 100%);
            color: white;
            padding: 3rem 2rem 4.5rem;
            position: relative;
            text-align: center;
            overflow: hidden;
        }

        /* Decorative pattern overlay */
        .parwa-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .back-button {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: rgba(255,255,255,0.8);
            font-size: 1.5rem;
            text-decoration: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(4px);
            transition: all 0.3s;
            z-index: 2;
        }
        
        .back-button:hover {
            color: #fff;
            background: rgba(255,255,255,0.2);
            transform: translateX(-3px);
        }

        .parwa-title {
            font-family: 'Oleo Script', cursive;
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 700;
            color: #8b0000;
            -webkit-text-stroke: 1.5px white;
            text-shadow: 2px 3px 6px rgba(0,0,0,0.35);
            margin-bottom: 0;
            letter-spacing: 2px;
            position: relative;
            z-index: 1;
        }

        /* ═══════════════════════════════════════
           CONTENT AREA
           ═══════════════════════════════════════ */
        .content-area {
            padding: 2.5rem 3rem 4rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ═══════════════════════════════════════
           VERSION SELECTOR
           ═══════════════════════════════════════ */
        .version-select-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .version-select-container {
            background: #fff;
            padding: 0.5rem;
            border-radius: 50px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 0;
        }

        .premium-version-toggle {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 30px;
            display: inline-flex;
            border: none;
        }

        .premium-version-toggle .btn-outline-premium-version {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 0.45rem 1.35rem;
            border-radius: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            white-space: nowrap;
        }

        .premium-version-toggle .btn-outline-premium-version.active {
            background: #8b1e1e;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 30, 30, 0.25);
        }

        .premium-version-toggle .btn-outline-premium-version:hover:not(.active) {
            color: #8b1e1e;
            background: rgba(139, 30, 30, 0.08);
        }

        /* ═══════════════════════════════════════
           SEARCH BAR
           ═══════════════════════════════════════ */
        .search-container {
            max-width: 280px;
            margin-left: auto;
            margin-bottom: 2rem;
        }

        .search-container .input-group {
            background: #fff;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: box-shadow 0.3s;
        }

        .search-container .input-group:focus-within {
            box-shadow: 0 2px 12px rgba(139, 30, 30, 0.15);
            border-color: #c9a0a0;
        }

        .search-container .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 1rem;
        }

        .search-container .form-control {
            border: none;
            box-shadow: none;
            font-size: 0.9rem;
            padding-right: 1rem;
        }

        .search-container .form-control:focus {
            box-shadow: none;
        }

        /* ═══════════════════════════════════════
           SUB PARWA GROUP
           ═══════════════════════════════════════ */
        .sub-parwa-group {
            margin-bottom: 2.75rem;
        }

        .sub-parwa-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 1.35rem;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-left: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sub-parwa-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 1.4em;
            background: linear-gradient(180deg, #8b1e1e, #c0392b);
            border-radius: 2px;
        }

        /* ═══════════════════════════════════════
           HORIZONTAL SCROLL CAROUSEL
           ═══════════════════════════════════════ */
        .scroll-wrapper {
            position: relative;
            padding: 0 2.75rem; /* Space for arrows */
        }

        .scroll-track {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 0.5rem 0.25rem 1rem;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scroll-track::-webkit-scrollbar {
            display: none;
        }

        /* ── Nav Arrows ── */
        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            color: #8b1e1e;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.25s ease;
            opacity: 1;
        }

        .scroll-arrow:hover {
            background: #8b1e1e;
            color: #fff;
            border-color: #8b1e1e;
            box-shadow: 0 4px 14px rgba(139, 30, 30, 0.3);
            transform: translateY(-50%) scale(1.08);
        }

        .scroll-arrow.left  { left: 0; }
        .scroll-arrow.right { right: 0; }

        /* Hidden state for arrows when scroll isn't possible */
        .scroll-arrow.hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateY(-50%) scale(0.8);
        }

        /* ═══════════════════════════════════════
           SECTION CARD
           ═══════════════════════════════════════ */
        .section-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.5rem 1.35rem 1.35rem;
            min-width: 210px;
            max-width: 230px;
            height: 150px;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            border-left: 4px solid #8b1e1e;
            position: relative;
            overflow: hidden;
        }

        /* Subtle gradient accent on hover */
        .section-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle at top right, rgba(139,30,30,0.06), transparent 70%);
            border-radius: 0 14px 0 0;
            pointer-events: none;
            transition: opacity 0.3s;
            opacity: 0;
        }

        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 28px rgba(139, 30, 30, 0.12);
        }

        .section-card:hover::after {
            opacity: 1;
        }

        .section-card-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 1.15rem;
            color: #1e293b;
            margin: 0;
            line-height: 1.3;
        }

        .btn-baca {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 1.1rem;
            background: linear-gradient(135deg, #8b1e1e, #a52828);
            color: #fff;
            border: none;
            border-radius: 7px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.2px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-baca:hover {
            background: linear-gradient(135deg, #a52828, #c0392b);
            color: white;
            box-shadow: 0 3px 10px rgba(139, 30, 30, 0.3);
            transform: translateY(-1px);
        }

        .btn-baca i {
            font-size: 0.7rem;
            transition: transform 0.2s;
        }

        .btn-baca:hover i {
            transform: translateX(2px);
        }

        /* ═══════════════════════════════════════
           EMPTY STATE
           ═══════════════════════════════════════ */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* ═══════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════ */
        @media (max-width: 768px) {
            .content-area {
                padding: 1.5rem 1rem 3rem;
            }
            .scroll-wrapper {
                padding: 0 2.25rem;
            }
            .section-card {
                min-width: 175px;
                max-width: 195px;
                height: 140px;
                padding: 1.25rem 1.15rem 1.15rem;
            }
            .section-card-title { font-size: 1rem; }
            .back-button { top: 1.5rem; left: 1rem; }
            .search-container { max-width: 100%; }
        }

        /* ═══════════════════════════════════════
           FADE-IN ANIMATION
           ═══════════════════════════════════════ */
        .sub-parwa-group {
            animation: fadeSlideUp 0.5s ease forwards;
            opacity: 0;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger the animation for each group */
        .sub-parwa-group:nth-child(1) { animation-delay: 0.05s; }
        .sub-parwa-group:nth-child(2) { animation-delay: 0.12s; }
        .sub-parwa-group:nth-child(3) { animation-delay: 0.19s; }
        .sub-parwa-group:nth-child(4) { animation-delay: 0.26s; }
        .sub-parwa-group:nth-child(5) { animation-delay: 0.33s; }
        .sub-parwa-group:nth-child(6) { animation-delay: 0.4s; }
        .sub-parwa-group:nth-child(7) { animation-delay: 0.47s; }
        .sub-parwa-group:nth-child(8) { animation-delay: 0.54s; }
        .sub-parwa-group:nth-child(9) { animation-delay: 0.61s; }
        .sub-parwa-group:nth-child(10) { animation-delay: 0.68s; }

        /* ═══════════════════════════════════════
           HERO BANNER & VIDEO CAROUSEL
           ═══════════════════════════════════════ */
        .hero-banner {
            position: relative;
            width: 100%;
            height: 60vh;
            min-height: 400px;
            background-color: #000;
            overflow: hidden;
            border-bottom: 5px solid #ffd700;
        }

        .hero-carousel, .carousel-inner, .carousel-item { height: 100%; }

        .hero-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.4);
            transition: filter 0.3s ease;
        }

        .hero-content {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 10%;
            z-index: 2;
        }

        .back-btn-float {
            position: absolute;
            top: 2rem; left: 2rem;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            transition: transform 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            width: 45px; height: 45px;
            border-radius: 50%;
            z-index: 100;
        }
        
        .back-btn-float:hover {
            color: #ffd700;
            transform: translateX(-5px);
            background: rgba(255,255,255,0.2);
        }

        .parwa-main-title {
            font-family: 'Oleo Script', cursive;
            font-size: 4.5rem;
            font-weight: 700;
            color: #b01c1c;
            -webkit-text-stroke: 2px white;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.6);
            margin-bottom: 0rem;
            line-height: 1.2;
        }

        .sub-parwa-subtitle {
            font-family: 'Figtree', sans-serif;
            font-size: 1.8rem;
            color: #ffd700;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.6);
            margin-bottom: 1.5rem;
        }

        .video-title {
            font-family: 'Figtree', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.6);
            margin-bottom: 0.2rem;
        }

        .video-uploader {
            font-size: 1.1rem;
            color: #e2e8f0;
            margin-bottom: 1.5rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
        }

        .btn-watch {
            background-color: #8b1e1e;
            color: white;
            font-weight: 700;
            padding: 0.6rem 2.5rem;
            border-radius: 5px;
            border: none;
            font-size: 1.1rem;
            letter-spacing: 1px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            display: inline-block;
            text-decoration: none;
            width: fit-content;
        }

        .btn-watch:hover {
            background-color: #a32222;
            color: #ffd700;
            transform: translateY(-2px);
        }

        .carousel-control-prev, .carousel-control-next {
            width: 8%; opacity: 0.7; z-index: 10;
        }
        .carousel-control-prev-icon, .carousel-control-next-icon {
            width: 3rem; height: 3rem;
        }

        /* ═══════════════════════════════════════
           AUDIO SECTION
           ═══════════════════════════════════════ */
        .audio-section {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2.5rem 0;
            border-bottom: 1px solid #cbd5e1;
        }

        .audio-scroll-container {
            display: flex; gap: 1.5rem; overflow-x: auto;
            padding: 1rem 0.5rem; scroll-behavior: smooth;
            -ms-overflow-style: none; scrollbar-width: none;
        }
        .audio-scroll-container::-webkit-scrollbar { display: none; }

        .audio-card {
            min-width: 380px; background: white;
            border-radius: 15px; padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 1.2rem;
            transition: transform 0.2s; border: 1px solid rgba(0,0,0,0.03);
            flex-shrink: 0;
        }

        .audio-card:hover {
            transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .audio-icon-wrapper {
            background-color: #fce8e8; border-radius: 50%;
            width: 55px; height: 55px; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .audio-icon-wrapper i { font-size: 1.8rem; color: #e53e3e; }

        .audio-info { flex-grow: 1; min-width: 0; }
        .audio-title { font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .audio-uploader { color: #64748b; font-size: 0.85rem; margin: 0 0 0.5rem 0; }
        .audio-card audio { width: 100%; height: 30px; outline: none; }
        .audio-card audio::-webkit-media-controls-panel { background-color: #f8fafc; }
        .version-badge { background-color: #8b1e1e; color: white; font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-weight: 600; }
        
        .scroll-btn {
            background: white; border: none; width: 40px; height: 40px;
            border-radius: 50%; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            color: #8b1e1e; font-size: 1.2rem; display: flex;
            align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer; z-index: 10;
        }
        .scroll-btn:hover { background: #8b1e1e; color: white; }
    </style>
</head>
<body>
    <x-loading-screen />

    <!-- Hero Banner / Video Carousel -->
    @php
        // Fallback image using parwa ID (1.png to 18.png)
        $fallbackImage = asset('images/' . $parwa->id . '.png');
    @endphp

    <section class="hero-banner">
        <!-- Back Button -->
        <a href="{{ route('parwa.index') }}" class="back-btn-float" title="Kembali">
            <i class="bi bi-chevron-left"></i>
        </a>

        @if(isset($videos) && $videos->count() > 0)
            <div id="videoCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($videos as $index => $video)
                        @php
                            $thumbnail = $fallbackImage;
                            $youtubeId = null;
                            if($video->type === 'youtube') {
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $video->url, $match);
                                $youtubeId = $match[1] ?? null;
                                if($youtubeId) {
                                    $thumbnail = "https://img.youtube.com/vi/{$youtubeId}/maxresdefault.jpg";
                                }
                            }
                        @endphp
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            @if($video->type === 'youtube')
                                <div class="hero-bg" style="background-image: url('{{ $thumbnail }}');"></div>
                            @else
                                <video class="hero-bg" style="object-fit: cover;" src="{{ asset('storage/' . $video->url) }}#t=0.1" preload="metadata"></video>
                            @endif
                            <div class="hero-content">
                                <h1 class="parwa-main-title">{{ $parwa->name }}</h1>
                                <p class="sub-parwa-subtitle"></p>
                                <h2 class="video-title">{{ $video->title }}</h2>
                                <p class="video-uploader">Sumber: {{ $video->source }}</p>
                                <button class="btn-watch" onclick="openVideoModal('{{ $video->type }}', '{{ $video->type === 'youtube' ? $youtubeId : asset('storage/' . $video->url) }}')">
                                    WATCH
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($videos->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#videoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#videoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @else
            <!-- Fallback Static Banner if No Videos -->
            <div class="hero-bg" style="background-image: url('{{ $fallbackImage }}'); filter: brightness(0.7);"></div>
            <div class="hero-content" style="align-items: center; text-align: center;">
                <h1 class="parwa-main-title" style="font-size: clamp(3rem, 6vw, 5rem);">{{ $parwa->name }}</h1>
            </div>
        @endif
    </section>

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="videoModalBody">
                    <!-- Video iframe or html5 video will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Section -->
    @if(isset($audios) && $audios->count() > 0)
    <section class="audio-section">
        <div class="container" style="max-width: 1200px;">
            <div class="audio-wrapper position-relative">
                @if($audios->count() > 3)
                    <button class="scroll-btn position-absolute start-0 translate-middle-y" style="top: 50%; left: -20px !important;" onclick="document.getElementById('audioContainer').scrollBy({left: -350, behavior: 'smooth'})">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                @endif
                
                <div class="audio-scroll-container" id="audioContainer">
                    @foreach($audios as $audio)
                        <div class="audio-card">
                            <div class="audio-icon-wrapper">
                                <i class="bi bi-music-note-beamed"></i>
                            </div>
                            <div class="audio-info">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h4 class="audio-title" title="{{ $audio->title }}">{{ $audio->title }}</h4>
                                    @if($audio->version)
                                        <span class="version-badge ms-2">{{ $audio->version }}</span>
                                    @endif
                                </div>
                                <p class="audio-uploader">Sumber: {{ $audio->source }}</p>
                                
                                @if($audio->type === 'youtube')
                                    @php
                                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $audio->url, $match);
                                        $youtubeId = $match[1] ?? null;
                                    @endphp
                                    @if($youtubeId)
                                        <div class="mt-1 overflow-hidden rounded-3 border" style="height: 40px;">
                                            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}?controls=1&showinfo=0&rel=0" class="w-100 h-100" allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <a href="{{ $audio->url }}" target="_blank" class="btn btn-sm btn-outline-danger mt-1">Buka YouTube</a>
                                    @endif
                                @elseif($audio->type === 'link')
                                    <audio controls preload="none">
                                        <source src="{{ $audio->url }}" type="audio/mpeg">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                @else
                                    <audio controls preload="none">
                                        <source src="{{ asset('storage/' . $audio->url) }}" type="audio/mpeg">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($audios->count() > 3)
                    <button class="scroll-btn position-absolute end-0 translate-middle-y" style="top: 50%; right: -20px !important;" onclick="document.getElementById('audioContainer').scrollBy({left: 350, behavior: 'smooth'})">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- Content -->
    <div class="content-area">
        <x-content-loader />

        <!-- Version Selection Bar -->
        @if(!empty($versions))
        <div class="version-select-wrapper">
            <div class="version-select-container">
                <div class="btn-group premium-version-toggle" role="group" aria-label="Pilih Versi Cerita">
                    <button type="button" class="btn btn-outline-premium-version {{ !request()->query('version') || request()->query('version') === 'all' ? 'active' : '' }}" data-version="all">
                        Tampilkan Semua
                    </button>
                    @foreach($versions as $ver)
                        <button type="button" class="btn btn-outline-premium-version {{ request()->query('version') === $ver ? 'active' : '' }}" data-version="{{ $ver }}">
                            {{ $ver }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Search -->
        <div class="search-container">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
            </div>
        </div>

        <!-- Sections grouped by Sub Parwa -->
        <div id="sectionContainer">
            @if(!empty($sections))
                @php
                    $grouped = collect($sections)->groupBy(function ($item) {
                        return $item['sub_parva'] ?? 'Lainnya';
                    });
                @endphp

                @foreach($grouped as $subParwa => $items)
                <div class="sub-parwa-group" data-subparwa="{{ $subParwa }}">
                    <h2 class="sub-parwa-title">{{ $subParwa }}</h2>
                    <div class="scroll-wrapper">
                        <button class="scroll-arrow left hidden" aria-label="Scroll left">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="scroll-track">
                            @foreach($items as $sec)
                            <div class="section-card section-item" data-section-name="{{ $sec['section'] }}" data-sub-parwa="{{ $subParwa }}">
                                <h3 class="section-card-title">{{ $sec['section'] }}</h3>
                                <a href="{{ route('parwa.read', ['book' => $bookName, 'section' => $sec['section']]) }}" class="btn-baca section-read-link" data-base-url="{{ route('parwa.read', ['book' => $bookName, 'section' => $sec['section']]) }}">
                                    Baca Selengkapanya <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <button class="scroll-arrow right hidden" aria-label="Scroll right">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Fallback: local ceritas grouped by sub_parwa --}}
                @if($ceritas->count() > 0)
                    @php
                        $groupedCeritas = $ceritas->groupBy('sub_parwa');
                    @endphp
                    @foreach($groupedCeritas as $subParwa => $items)
                    <div class="sub-parwa-group" data-subparwa="{{ $subParwa ?: 'Lainnya' }}">
                        <h2 class="sub-parwa-title">{{ $subParwa ?: 'Lainnya' }}</h2>
                        <div class="scroll-wrapper">
                            <button class="scroll-arrow left hidden" aria-label="Scroll left">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <div class="scroll-track">
                                @foreach($items as $cerita)
                                <div class="section-card section-item" data-section-name="{{ $cerita->sub_parwa ?? 'Cerita' }}" data-sub-parwa="{{ $subParwa }}">
                                    <h3 class="section-card-title">{{ $cerita->sub_parwa ?? 'Cerita' }}</h3>
                                    <a href="{{ route('cerita.show', $cerita->id) }}" class="btn-baca">
                                        Baca Selengkapanya <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            <button class="scroll-arrow right hidden" aria-label="Scroll right">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="bi bi-journal-x"></i>
                        <p>Belum ada cerita di Parwa ini.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ════════════════════════════════════════
        // SMART ARROW VISIBILITY
        // ════════════════════════════════════════

        /**
         * Check if a scroll-track needs arrows, and show/hide them accordingly.
         * Also hides left arrow when scrolled to start, right arrow when scrolled to end.
         */
        function updateArrows(wrapper) {
            const track = wrapper.querySelector('.scroll-track');
            const leftBtn = wrapper.querySelector('.scroll-arrow.left');
            const rightBtn = wrapper.querySelector('.scroll-arrow.right');

            if (!track || !leftBtn || !rightBtn) return;

            const scrollLeft = Math.round(track.scrollLeft);
            const maxScroll = track.scrollWidth - track.clientWidth;
            const canScroll = maxScroll > 2; // small threshold for rounding

            if (!canScroll) {
                // All items fit — hide both arrows
                leftBtn.classList.add('hidden');
                rightBtn.classList.add('hidden');
            } else {
                // Show/hide based on scroll position
                leftBtn.classList.toggle('hidden', scrollLeft <= 2);
                rightBtn.classList.toggle('hidden', scrollLeft >= maxScroll - 2);
            }
        }

        /**
         * Initialize arrow logic for all scroll-wrappers.
         */
        function initAllArrows() {
            document.querySelectorAll('.scroll-wrapper').forEach(wrapper => {
                const track = wrapper.querySelector('.scroll-track');
                if (!track) return;

                // Update on scroll
                track.addEventListener('scroll', () => updateArrows(wrapper), { passive: true });

                // Initial check
                updateArrows(wrapper);

                // Wire up click handlers
                const leftBtn = wrapper.querySelector('.scroll-arrow.left');
                const rightBtn = wrapper.querySelector('.scroll-arrow.right');

                if (leftBtn) {
                    leftBtn.addEventListener('click', () => {
                        const card = track.querySelector('.section-card');
                        const amount = card ? (card.offsetWidth + 20) * 2 : 480;
                        track.scrollBy({ left: -amount, behavior: 'smooth' });
                    });
                }

                if (rightBtn) {
                    rightBtn.addEventListener('click', () => {
                        const card = track.querySelector('.section-card');
                        const amount = card ? (card.offsetWidth + 20) * 2 : 480;
                        track.scrollBy({ left: amount, behavior: 'smooth' });
                    });
                }
            });
        }

        // Re-check arrows on window resize (items may now fit / not fit)
        window.addEventListener('resize', () => {
            document.querySelectorAll('.scroll-wrapper').forEach(updateArrows);
        });

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', initAllArrows);

        // ════════════════════════════════════════
        // VERSION HELPERS
        // ════════════════════════════════════════
        function normalizeVersionName(name) {
            if (!name) return "";
            return name.toLowerCase()
                .replace(/[''\u2019]s\b/g, '')
                .replace(/,?\s*tr\.?.*$/g, '')
                .replace(/[^a-z0-9]/g, '')
                .trim();
        }

        function isVersionMatch(v1, v2) {
            let n1 = normalizeVersionName(v1);
            let n2 = normalizeVersionName(v2);
            return n1 === n2 || n1.includes(n2) || n2.includes(n1);
        }

        // ════════════════════════════════════════
        // SEARCH
        // ════════════════════════════════════════
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const groups = document.querySelectorAll('.sub-parwa-group');

            groups.forEach(group => {
                const cards = group.querySelectorAll('.section-item');
                let hasVisible = false;

                cards.forEach(card => {
                    const name = card.getAttribute('data-section-name') || '';
                    const subParwa = card.getAttribute('data-sub-parwa') || '';
                    const text = (name + ' ' + subParwa).toUpperCase();

                    if (text.indexOf(filter) > -1) {
                        card.style.display = '';
                        hasVisible = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                group.style.display = hasVisible ? '' : 'none';

                // Re-check arrows since visible cards changed
                if (hasVisible) {
                    const wrapper = group.querySelector('.scroll-wrapper');
                    if (wrapper) setTimeout(() => updateArrows(wrapper), 50);
                }
            });
        });

        // ════════════════════════════════════════
        // VERSION SELECTION
        // ════════════════════════════════════════
        document.querySelectorAll('.btn-outline-premium-version').forEach(button => {
            button.addEventListener('click', function() {
                const selectedVersion = this.getAttribute('data-version');
                fetch('{{ route("set-parwa-version") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ version: selectedVersion })
                }).then(() => {
                    if (selectedVersion === 'all') {
                        localStorage.removeItem('selected_version_judul');
                        window.location.href = window.location.pathname;
                    } else {
                        localStorage.setItem('selected_version_judul', selectedVersion);
                        window.location.href = window.location.pathname + '?version=' + encodeURIComponent(selectedVersion);
                    }
                }).catch(() => {
                    if (selectedVersion === 'all') {
                        localStorage.removeItem('selected_version_judul');
                        window.location.href = window.location.pathname;
                    } else {
                        localStorage.setItem('selected_version_judul', selectedVersion);
                        window.location.href = window.location.pathname + '?version=' + encodeURIComponent(selectedVersion);
                    }
                });
            });
        });

        // ════════════════════════════════════════
        // SYNC VERSION STATE ON LOAD
        // ════════════════════════════════════════
        const urlParams = new URLSearchParams(window.location.search);
        const queryVersion = urlParams.get('version');
        const savedVersion = localStorage.getItem('selected_version_judul');
        
        if (queryVersion) {
            localStorage.setItem('selected_version_judul', queryVersion);
        } else if (savedVersion && savedVersion !== 'all') {
            window.location.href = window.location.pathname + '?version=' + encodeURIComponent(savedVersion);
        }

        const currentActive = queryVersion || savedVersion || 'all';
        document.querySelectorAll('.btn-outline-premium-version').forEach(btn => {
            const btnVersion = btn.getAttribute('data-version');
            if (btnVersion === currentActive || (btnVersion !== 'all' && currentActive !== 'all' && isVersionMatch(btnVersion, currentActive))) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // ════════════════════════════════════════
        // UPDATE READ LINKS WITH VERSION
        // ════════════════════════════════════════
        function updateReadLinks() {
            const activeVersion = new URLSearchParams(window.location.search).get('version') || localStorage.getItem('selected_version_judul');
            document.querySelectorAll('.section-read-link').forEach(link => {
                const baseUrl = link.getAttribute('data-base-url');
                if (baseUrl && activeVersion && activeVersion !== 'all') {
                    link.href = baseUrl + '?version=' + encodeURIComponent(activeVersion);
                } else if (baseUrl) {
                    link.href = baseUrl;
                }
            });
        }
        updateReadLinks();

        // Video Modal Script
        function openVideoModal(type, url) {
            const modalBody = document.getElementById('videoModalBody');
            
            if (type === 'youtube') {
                modalBody.innerHTML = `
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/${url}?autoplay=1&rel=0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>`;
            } else if (type === 'upload') {
                modalBody.innerHTML = `
                    <div class="ratio ratio-16x9">
                        <video controls autoplay class="w-100 h-100 bg-black">
                            <source src="${url}" type="video/mp4">
                            Browser Anda tidak mendukung tag video.
                        </video>
                    </div>`;
            }

            const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
            videoModal.show();
        }

        document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('videoModalBody').innerHTML = ''; // Clear video to stop playing
        });
    </script>
</body>
</html>
