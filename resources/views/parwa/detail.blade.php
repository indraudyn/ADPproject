<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $parwa->name }} - Cerita</title>

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
            color: #ffd700; /* Gold */
            transform: translateX(-5px);
        }

        .parwa-title {
            font-family: 'Oleo Script', cursive;
            font-size: 4rem; /* Slightly larger for script font */
            font-weight: 700;
            color: #8b0000; /* Dark Red Text */
            -webkit-text-stroke: 1.5px white; /* White Stroke */
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3); /* Subtle shadow for depth */
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

        /* Grid Section */
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }

        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .section-title {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .btn-baca {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: transparent;
            color: #8b1e1e; /* Dark Red */
            border: 2px solid #8b1e1e;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-baca:hover {
            background-color: #8b1e1e;
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
            <a href="#" class="parwa-tab active">CERITA</a>
            <a href="{{ route('parwa.video', $parwa->slug) }}" class="parwa-tab">VIDEO</a>
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
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill form-control-search" placeholder="Search Section...">
            </div>
        </div>

        <!-- Grid -->
        <div class="row g-4" id="sectionGrid">
            @forelse($ceritas as $cerita)
            <div class="col-md-3 col-sm-6 section-item">
                <div class="section-card">
                    <h2 class="section-title">{{ $cerita->sub_parwa ?? 'Cerita' }}</h2>
                    <!-- Optional: Show excerpt if needed, or just title as requested -->
                    <a href="{{ route('cerita.show', $cerita->id) }}" class="btn-baca">Baca Selengkapnya</a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p class="text-muted">Belum ada cerita di Parwa ini.</p>
            </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search Functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let grid = document.getElementById('sectionGrid');
            let items = grid.getElementsByClassName('section-item');

            for (let i = 0; i < items.length; i++) {
                let title = items[i].getElementsByClassName('section-title')[0];
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
