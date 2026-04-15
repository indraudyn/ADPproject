<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $parwa->name }} - Video</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600|cinzel:400,700|oleo-script:400,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f4f6f8;
        }

        /* Header Section */
        .parwa-header {
            background-color: #8b1e1e; /* Dark Red */
            color: white;
            padding-top: 2rem;
            padding-bottom: 0;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            transition: transform 0.2s;
        }
        
        .back-button:hover {
            color: #ffd700;
            transform: translateX(-5px);
        }

        .parwa-title {
            font-family: 'Oleo Script', cursive;
            font-size: 4rem; 
            font-weight: 700;
            color: #8b0000; 
            -webkit-text-stroke: 1.5px white; 
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 2rem;
            letter-spacing: 1px;
        }

        /* Tabs */
        .parwa-tabs {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .parwa-tab {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-weight: bold;
            padding-bottom: 1rem;
            border-bottom: 3px solid transparent;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .parwa-tab.active {
            color: white;
            border-bottom-color: white;
        }

        .parwa-tab:hover {
            color: white;
        }

        /* Search Bar */
        .search-container {
            max-width: 300px;
            margin-left: auto;
            margin-bottom: 2rem;
        }

        .form-control-search {
            border-radius: 20px;
            padding-left: 1rem;
            border: 1px solid #ddd;
        }

        /* Video Card */
        .video-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            background-color: #000;
            aspect-ratio: 16/9;
            width: 100%;
            overflow: hidden;
        }

        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .video-card:hover .video-thumbnail img {
            opacity: 0.6;
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 3rem;
            opacity: 0.9;
            transition: transform 0.2s;
        }

        .video-card:hover .play-button {
            transform: translate(-50%, -50%) scale(1.1);
        }

        .video-info {
            padding: 1.5rem;
        }

        .video-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .video-source {
            font-size: 0.9rem;
            color: #666;
        }


    </style>
</head>
<body>
    <x-loading-screen />

    <!-- Header -->
    <header class="parwa-header text-center">
        <a href="{{ route('parwa.index') }}" class="back-button">
            <i class="bi bi-chevron-left"></i>
        </a>

        <h1 class="parwa-title">{{ $parwa->name }}</h1>

        <div class="parwa-tabs">
            <a href="{{ route('parwa.detail', $parwa->slug) }}" class="parwa-tab">CERITA</a>
            <a href="#" class="parwa-tab active">VIDEO</a>
        </div>
    </header>

    <!-- Content -->
    <div class="container-fluid px-5 py-5">
        
        <!-- Search -->
        <div class="search-container">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="videoSearchInput" class="form-control border-start-0 rounded-end-pill form-control-search" placeholder="Search Video...">
            </div>
        </div>

        <!-- Video Grid -->
        <div class="row g-4" id="videoGrid">
            @forelse($videos as $video)
            <div class="col-md-4 video-item">
                <div class="video-card">
                    <div class="video-thumbnail" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#videoPlayerModal" data-video-url="{{ $video->type === 'youtube' ? 'https://www.youtube.com/embed/'.$video->youtube_id.'?autoplay=1' : asset('storage/' . $video->url) }}" data-video-type="{{ $video->type }}">
                        @if($video->type == 'youtube' && $video->youtube_id)
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/maxresdefault.jpg" alt="Video Thumbnail">
                        @else
                            <video class="w-100 h-100" style="object-fit: cover;" preload="metadata" muted>
                                <source src="{{ asset('storage/' . $video->url) }}#t=0.1" type="video/mp4">
                            </video>
                        @endif
                        <div class="play-button"><i class="bi bi-play-circle-fill text-white"></i></div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title">{{ $video->title }}</h3>
                        <p class="video-source">Sumber: {{ $video->source ?? 'Uploaded' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">Belum ada video.</p>
            </div>
            @endforelse
        </div>
    </div>



    <!-- Video Player Modal -->
    <div class="modal fade" id="videoPlayerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center" id="videoPlayerContainer">
                    <!-- Video injected here -->
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Video Modal Logic
        const videoPlayerModal = document.getElementById('videoPlayerModal');
        const videoPlayerContainer = document.getElementById('videoPlayerContainer');

        videoPlayerModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-video-url');
            const type = button.getAttribute('data-video-type');

            if (type === 'youtube') {
                videoPlayerContainer.innerHTML = `<iframe class="w-100 shadow-lg rounded" style="aspect-ratio: 16/9;" src="${url}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
            } else {
                videoPlayerContainer.innerHTML = `<video class="w-100 shadow-lg rounded" style="aspect-ratio: 16/9; background: #000;" controls autoplay>
                    <source src="${url}" type="video/mp4">
                    Browser Anda tidak mendukung pemutar video.
                </video>`;
            }
        });

        videoPlayerModal.addEventListener('hidden.bs.modal', function () {
            // Clear content to stop playing when modal overlaps
            videoPlayerContainer.innerHTML = '';
        });

        // Search Functionality
        document.getElementById('videoSearchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let grid = document.getElementById('videoGrid');
            let items = grid.getElementsByClassName('video-item');

            for (let i = 0; i < items.length; i++) {
                let title = items[i].getElementsByClassName('video-title')[0];
                let txtValue = title.textContent || title.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        });
    </script>
</body>
</html>
