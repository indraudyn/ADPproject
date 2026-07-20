<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-adp.png') }}">
    <meta charset="UTF-8">
    <title>{{ $section }} - {{ $book }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;800&family=Oleo+Script:wght@400;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/cerita-show.css') }}">

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Hero Video Banner Section */
        .hero-banner {
            position: relative;
            width: 100%;
            height: 60vh;
            min-height: 400px;
            background-color: #000;
            overflow: hidden;
            border-bottom: 5px solid #ffd700;
        }

        .hero-carousel, .carousel-inner, .carousel-item {
            height: 100%;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.4);
            transition: filter 0.3s ease;
        }

        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 10%;
            z-index: 2;
        }

        .back-btn-float {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            transition: transform 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            z-index: 100; /* Increased to ensure it floats above carousel */
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
            color: #ffffff;
            /* Red color with white stroke */
            -webkit-text-stroke: 2px white;
            color: #b01c1c;
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

        /* Carousel controls */
        .carousel-control-prev, .carousel-control-next {
            width: 8%;
            opacity: 0.7;
            z-index: 10; /* Ensure arrows are clickable over hero-content */
        }
        
        .carousel-control-prev-icon, .carousel-control-next-icon {
            width: 3rem;
            height: 3rem;
        }

        /* Audio Carousel Section */
        .audio-section {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 2.5rem 0;
            border-bottom: 1px solid #cbd5e1;
        }

        .audio-scroll-container {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem 0.5rem;
            scroll-behavior: smooth;
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }

        .audio-scroll-container::-webkit-scrollbar {
            display: none;
        }

        .audio-card {
            min-width: 380px;
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: transform 0.2s;
            border: 1px solid rgba(0,0,0,0.03);
            flex-shrink: 0;
        }

        .audio-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .audio-icon-wrapper {
            background-color: #fce8e8;
            border-radius: 50%;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .audio-icon-wrapper i {
            font-size: 1.8rem;
            color: #e53e3e;
        }

        .audio-info {
            flex-grow: 1;
            min-width: 0;
        }

        .audio-title {
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .audio-uploader {
            color: #64748b;
            font-size: 0.85rem;
            margin: 0 0 0.5rem 0;
        }

        .audio-card audio {
            width: 100%;
            height: 30px;
            outline: none;
        }
        
        .audio-card audio::-webkit-media-controls-panel {
            background-color: #f8fafc;
        }

        .version-badge {
            background-color: #8b1e1e;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .scroll-btn {
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            color: #8b1e1e;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: all 0.2s;
        }

        .scroll-btn:hover {
            background: #f8fafc;
            transform: scale(1.05);
        }

        .audio-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        /* Reading Mode Selector */
        .reading-mode-selector {
            background-color: #f1f5f9;
            border-radius: 30px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            margin: 0 auto 2rem auto;
            gap: 1rem;
        }

        .mode-label {
            font-weight: 600;
        }

        .premium-toggle .btn {
            border-radius: 20px;
            border: none;
            padding: 0.3rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s;
        }

        .premium-toggle .btn.active {
            background-color: #8b1e1e;
            color: white;
            box-shadow: 0 2px 8px rgba(139, 30, 30, 0.3);
        }

        .premium-toggle .btn:hover:not(.active) {
            background-color: #e2e8f0;
        }
    </style>
</head>
<body class="cerita-show-page">
    <x-loading-screen />

    @php
        $bookImageMap = [
            'Adi Parva' => 1, 'Sabha Parva' => 2, 'Vana Parva' => 3, 'Virata Parva' => 4,
            'Udyoga Parva' => 5, 'Bhishma Parva' => 6, 'Drona Parva' => 7, 'Karna Parva' => 8,
            'Shalya Parva' => 9, 'Sauptika Parva' => 10, 'Stri Parva' => 11, 'Shanti Parva' => 12,
            'Anushasana Parva' => 13, 'Ashvamedhika Parva' => 14, 'Ashramavasika Parva' => 15,
            'Mausala Parva' => 16, 'Mahaprasthanika Parva' => 17, 'Swargarohanika Parva' => 18
        ];
        $fallbackImageId = $bookImageMap[$book] ?? 1;
        $fallbackImage = asset("images/{$fallbackImageId}.png");
    @endphp

    <!-- Hero Banner -->
    <section class="hero-banner" id="videoBanner">
        @if($parwa)
            <a href="{{ route('parwa.detail', $parwa->slug) }}" class="back-btn-float">
                <i class="bi bi-chevron-left"></i>
            </a>
        @else
            <a href="{{ route('parwa.index') }}" class="back-btn-float">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        @if($videos->count() > 0)
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
                                <h1 class="parwa-main-title">{{ $book }}</h1>
                                <p class="sub-parwa-subtitle">{{ $section }}</p>
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
            <div class="hero-bg" style="background-image: url('{{ $fallbackImage }}');"></div>
            <div class="hero-content">
                <h1 class="parwa-main-title">{{ $book }}</h1>
                <p class="sub-parwa-subtitle">{{ $section }}</p>
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
    @if($audios->count() > 0)
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
                                        <small class="text-danger">Link tidak valid</small>
                                    @endif
                                @elseif($audio->type === 'video' || $audio->type === 'upload')
                                    <audio controls>
                                        <source src="{{ asset('storage/' . $audio->url) }}">
                                        Browser tidak mendukung tag audio.
                                    </audio>
                                @else
                                    <audio controls>
                                        <source src="{{ $audio->url }}">
                                        Browser tidak mendukung tag audio.
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

    <!-- Main Content -->
    <main class="story-detail-container">
        <div class="container" style="max-width: 900px;">
            <x-content-loader />

            <!-- Reading Mode Selector -->
            <div class="reading-mode-selector shadow-sm" style="display: none;">
                <span class="mode-label text-secondary opacity-75 small"><i class="bi bi-book-half"></i> Mode Baca:</span>
                <div class="btn-group premium-toggle" role="group" aria-label="Mode Membaca">
                    <button type="button" class="btn btn-outline-premium btn-mode-split">
                        <i class="bi bi-file-earmark-text"></i> Pisah
                    </button>
                    <button type="button" class="btn btn-outline-premium btn-mode-full">
                        <i class="bi bi-file-earmark-richtext"></i> Langsung
                    </button>
                </div>
            </div>

            <!-- Main Story Area -->
            <div class="mb-4">
                <article class="story-content-card">
                    @if(!empty($content))
                        @php $item = $content[0]; @endphp
                        <div class="version-content" id="version-0">
                            <div class="story-title-container text-center mb-5 pb-4 border-bottom">
                                <h2 class="story-title-heading fw-bold m-0" style="font-family: 'Cinzel', serif; color: #2c3e50; font-size: 1.85rem; line-height: 1.4; letter-spacing: 0.5px;">
                                    {{ $item['judul'] }}
                                </h2>
                            </div>
                            
                            <div class="story-text-body mt-4">
                                {!! $item['isi'] !!}
                            </div>

                            <!-- Pagination Controls for this version -->
                            <div class="story-pagination mt-3" id="story-pagination-0" style="display: none;">
                                <nav aria-label="Halaman Cerita">
                                    <ul class="pagination premium-pagination justify-content-center">
                                        <li class="page-item page-prev-li">
                                            <button class="page-link btn-page-prev" data-version="0" aria-label="Sebelumnya">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>
                                        </li>
                                        <!-- Dynamic page list will be rendered by JS -->
                                        <li class="page-item page-next-li">
                                            <button class="page-link btn-page-next" data-version="0" aria-label="Selanjutnya">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="text-center mt-2">
                                    <span class="text-muted small page-indicator">Halaman 1 dari 1</span>
                                </div>
                            </div>

                            @if(!empty($item['url']))
                                <div class="mt-4 pt-4 border-top">
                                    <a href="{{ $item['url'] }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                        <i class="bi bi-link-45deg me-1"></i> Lihat Sumber Resmi (Sacred-Texts)
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </article>
            </div>

            <!-- Chapter Navigation (Outside the box) -->
            <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                @if(isset($prevSection) && $prevSection)
                    <a href="{{ route('parwa.read', ['book' => $bookSlug, 'section' => $prevSection]) }}{{ request()->query('version') ? '?version=' . urlencode(request()->query('version')) : '' }}" class="btn btn-premium-red py-2 px-4 shadow-sm" style="border-radius: 12px; font-weight: 700;">
                        <i class="bi bi-chevron-left"></i> Bab Sebelumnya
                    </a>
                @else
                    <button class="btn btn-secondary py-2 px-4 opacity-50" style="border-radius: 12px;" disabled>
                        <i class="bi bi-chevron-left"></i> Bab Sebelumnya
                    </button>
                @endif

                @if(isset($nextSection) && $nextSection)
                    <a href="{{ route('parwa.read', ['book' => $bookSlug, 'section' => $nextSection]) }}{{ request()->query('version') ? '?version=' . urlencode(request()->query('version')) : '' }}" class="btn btn-premium-red py-2 px-4 shadow-sm" style="border-radius: 12px; font-weight: 700;">
                        Bab Selanjutnya <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <button class="btn btn-secondary py-2 px-4 opacity-50" style="border-radius: 12px;" disabled>
                        Bab Selanjutnya <i class="bi bi-chevron-right"></i>
                    </button>
                @endif
            </div>

        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let parwaHasPaginator = false;

        function openVideoModal(type, url) {
            const modalBody = document.getElementById('videoModalBody');
            modalBody.innerHTML = ''; // Clear previous

            if(type === 'youtube') {
                modalBody.innerHTML = `<div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/${url}?autoplay=1" allow="autoplay; fullscreen" allowfullscreen></iframe>
                </div>`;
            } else {
                modalBody.innerHTML = `<div class="ratio ratio-16x9">
                    <video controls autoplay class="w-100 h-100">
                        <source src="${url}" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                </div>`;
            }

            const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
            videoModal.show();
        }

        // Stop video when modal is closed
        document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('videoModalBody').innerHTML = '';
        });

        document.addEventListener("DOMContentLoaded", function() {
            const versions = document.querySelectorAll('.version-content');
            const versionPaginators = [];

            const globalModeSelector = document.querySelector('.reading-mode-selector');
            const globalBtnModeSplit = document.querySelector('.btn-mode-split');
            const globalBtnModeFull = document.querySelector('.btn-mode-full');

            let hasPaginator = false;

            versions.forEach((versionEl, index) => {
                const storyTextBody = versionEl.querySelector('.story-text-body');
                const paginationContainer = versionEl.querySelector('.story-pagination');
                const pagePrevLi = versionEl.querySelector('.page-prev-li');
                const pageNextLi = versionEl.querySelector('.page-next-li');
                const btnPagePrev = versionEl.querySelector('.btn-page-prev');
                const btnPageNext = versionEl.querySelector('.btn-page-next');
                const pageIndicator = versionEl.querySelector('.page-indicator');

                if (!storyTextBody) return;

                const allChildren = Array.from(storyTextBody.children);
                let paragraphs = [];
                if (allChildren.length === 0) {
                    const rawHTML = storyTextBody.innerHTML;
                    const paragraphsHTML = rawHTML.split(/\n\s*\n|<br\s*\/?>\s*<br\s*\/?>/i);
                    storyTextBody.innerHTML = '';
                    paragraphsHTML.forEach(html => {
                        if (html.trim()) {
                            const p = document.createElement('p');
                            p.innerHTML = html;
                            storyTextBody.appendChild(p);
                            paragraphs.push(p);
                        }
                    });
                } else {
                    paragraphs = allChildren;
                }

                if (paragraphs.length === 0) return;

                let pages = [];
                let currentPage = [];

                function isPageBreakElement(el) {
                    if (el.tagName === "HR") return true;
                    const text = el.textContent || "";
                    return text.includes("[pagebreak]") || text.includes("[halaman]");
                }

                paragraphs.forEach(child => {
                    if (isPageBreakElement(child)) {
                        if (currentPage.length > 0) {
                            pages.push(currentPage);
                            currentPage = [];
                        }
                    } else {
                        currentPage.push(child);
                    }
                });
                if (currentPage.length > 0) {
                    pages.push(currentPage);
                }

                const autoSplitLimit = 5;
                if (pages.length <= 1 && paragraphs.length > autoSplitLimit) {
                    pages = [];
                    currentPage = [];
                    let count = 0;
                    paragraphs.forEach(child => {
                        currentPage.push(child);
                        count++;
                        if (count >= autoSplitLimit) {
                            pages.push(currentPage);
                            currentPage = [];
                            count = 0;
                        }
                    });
                    if (currentPage.length > 0) {
                        pages.push(currentPage);
                    }
                }

                if (pages.length <= 1) {
                    if (paginationContainer) paginationContainer.style.display = 'none';
                    return;
                }

                hasPaginator = true;

                let currentPageIndex = 0;
                let readingMode = localStorage.getItem("reading_mode") || "split";

                const pageElements = [];
                storyTextBody.innerHTML = '';

                pages.forEach((pageNodes, idx) => {
                    const pageDiv = document.createElement("div");
                    pageDiv.className = "story-page";
                    pageDiv.id = `story-page-v${index}-${idx}`;
                    pageNodes.forEach(node => {
                        pageDiv.appendChild(node.cloneNode(true));
                    });
                    storyTextBody.appendChild(pageDiv);
                    pageElements.push(pageDiv);
                });

                function renderPaginationControls() {
                    const listItems = Array.from(paginationContainer.querySelectorAll(".page-item"));
                    listItems.forEach(item => {
                        if (!item.classList.contains("page-prev-li") && !item.classList.contains("page-next-li")) {
                            item.remove();
                        }
                    });

                    pages.forEach((_, idx) => {
                        const li = document.createElement("li");
                        li.className = `page-item ${idx === currentPageIndex ? 'active' : ''}`;
                        
                        const btn = document.createElement("button");
                        btn.className = "page-link";
                        btn.innerText = idx + 1;
                        btn.addEventListener("click", () => showPage(idx));
                        
                        li.appendChild(btn);
                        pageNextLi.parentNode.insertBefore(li, pageNextLi);
                    });

                    if (currentPageIndex === 0) {
                        pagePrevLi.classList.add("disabled");
                    } else {
                        pagePrevLi.classList.remove("disabled");
                    }

                    if (currentPageIndex === pages.length - 1) {
                        pageNextLi.classList.add("disabled");
                    } else {
                        pageNextLi.classList.remove("disabled");
                    }

                    pageIndicator.innerText = `Halaman ${currentPageIndex + 1} dari ${pages.length}`;
                }

                function showPage(pageIdx, scroll = true) {
                    if (pageIdx < 0 || pageIdx >= pages.length) return;

                    const currentVisiblePage = pageElements[currentPageIndex];
                    const targetPage = pageElements[pageIdx];

                    if (scroll && currentPageIndex !== pageIdx) {
                        const contentCard = versionEl.closest(".story-content-card");
                        if (contentCard) {
                            contentCard.scrollIntoView({ behavior: "smooth", block: "start" });
                        }
                    }

                    if (currentVisiblePage && currentVisiblePage !== targetPage) {
                        currentVisiblePage.classList.add("fade-out");
                        setTimeout(() => {
                            pageElements.forEach((el, idx) => {
                                el.style.display = idx === pageIdx ? "block" : "none";
                            });
                            currentPageIndex = pageIdx;
                            targetPage.classList.add("fade-out");
                            targetPage.offsetHeight;
                            targetPage.classList.remove("fade-out");
                            renderPaginationControls();
                        }, 200);
                    } else {
                        pageElements.forEach((el, idx) => {
                            el.style.display = idx === pageIdx ? "block" : "none";
                        });
                        currentPageIndex = pageIdx;
                        if (targetPage) targetPage.classList.remove("fade-out");
                        renderPaginationControls();
                    }
                }

                const paginatorInstance = {
                    setReadingMode: function(mode) {
                        readingMode = mode;
                        if (mode === "split") {
                            if (paginationContainer) paginationContainer.style.display = "block";
                            showPage(currentPageIndex, false);
                        } else {
                            if (paginationContainer) paginationContainer.style.display = "none";
                            pageElements.forEach(el => {
                                el.style.display = "block";
                                el.classList.remove("fade-out");
                            });
                        }
                    },
                    prevPage: () => showPage(currentPageIndex - 1),
                    nextPage: () => showPage(currentPageIndex + 1)
                };

                versionPaginators.push(paginatorInstance);

                btnPagePrev.addEventListener("click", (e) => {
                    e.preventDefault();
                    paginatorInstance.prevPage();
                });

                btnPageNext.addEventListener("click", (e) => {
                    e.preventDefault();
                    paginatorInstance.nextPage();
                });
            });

            if (globalBtnModeSplit && globalBtnModeFull) {
                globalBtnModeSplit.addEventListener("click", () => globalSetReadingMode("split"));
                globalBtnModeFull.addEventListener("click", () => globalSetReadingMode("full"));
            }

            parwaHasPaginator = hasPaginator;
            if (globalModeSelector) {
                globalModeSelector.style.display = hasPaginator ? 'flex' : 'none';
            }

            function globalSetReadingMode(mode) {
                localStorage.setItem("reading_mode", mode);
                
                // Update active classes on all split buttons
                document.querySelectorAll('.btn-mode-split').forEach(b => {
                    if (mode === 'split') b.classList.add('active');
                    else b.classList.remove('active');
                });
                // Update active classes on all full buttons
                document.querySelectorAll('.btn-mode-full').forEach(b => {
                    if (mode === 'full') b.classList.add('active');
                    else b.classList.remove('active');
                });

                // Apply reading mode to all active paginators
                versionPaginators.forEach(paginator => {
                    paginator.setReadingMode(mode);
                });
            }

            // Initialize global mode on load
            const initialMode = localStorage.getItem("reading_mode") || "split";
            globalSetReadingMode(initialMode);
        });
    </script>
</body>
</html>
