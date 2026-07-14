<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Asta Dasa Parwa</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600|cinzel:400,700|oleo-script:400,700&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS (Navbar styles) -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ time() }}">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Figtree', sans-serif;
            background-color: #000;
        }

        .quiz-landing {
            position: relative;
            height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        /* Background Image with Overlay */
        .quiz-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(50, 0, 0, 0.6), rgba(50, 0, 0, 0.6)), /* Dark Red Overlay */
                url("{{ asset('images/bghome.png') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        /* Content */
        .quiz-content {
            z-index: 1;
            transform: translateY(-20px); /* Slight visual lift */
        }

        .quiz-subtitle {
            font-family: 'Cinzel', serif;
            font-size: 2.5rem;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: #ffd700; /* Gold */
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
            font-weight: bold;
        }

        .quiz-title {
            font-family: 'Oleo Script', cursive;
            font-size: 9rem; /* Increased size */
            color: #8b0000; /* Dark Red */
            -webkit-text-stroke: 3px white;
            text-shadow: 
                5px 5px 0px black,
                0 0 20px rgba(139, 0, 0, 0.8);
            margin-bottom: 60px;
            line-height: 1;
        }

        .btn-start {
            font-family: 'Cinzel', serif;
            font-size: 2.5rem;
            font-weight: 800;
            padding: 15px 80px;
            border-radius: 60px;
            background: linear-gradient(45deg, #8b0000, #a00000);
            color: white;
            border: 3px solid #ffd700;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            text-shadow: 0 2px 2px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .btn-start:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
            color: #ffd700;
        }

        /* Decorative Elements */
        .ornament {
            width: 100px; /* Adjust based on available assets */
            margin-bottom: 20px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }

        /* RESPONSIVE MEDIA QUERIES */
        @media (max-width: 991px) {
            .quiz-title {
                font-size: 7rem;
            }
        }

        @media (max-width: 768px) {
            .quiz-title {
                font-size: 5rem;
                margin-bottom: 40px;
                -webkit-text-stroke: 1.5px white;
            }
            .quiz-subtitle {
                font-size: 1.5rem;
                letter-spacing: 4px;
            }
            .btn-start {
                font-size: 1.5rem;
                padding: 10px 50px;
            }
        }
        
        @media (max-width: 480px) {
            .quiz-title {
                font-size: 4rem;
                margin-bottom: 30px;
                -webkit-text-stroke: 1px white;
            }
            .quiz-subtitle {
                font-size: 1.2rem;
                letter-spacing: 2px;
            }
            .btn-start {
                font-size: 1.2rem;
                padding: 10px 40px;
            }
        }
    </style>
</head>
<body>
    <x-loading-screen />

    <div class="quiz-landing">
        <!-- Background -->
        <div class="quiz-bg"></div>

        <!-- Navbar -->
        <x-navbar />

        <div class="quiz-content mt-5">
            <div class="quiz-subtitle">Quiz</div>
            <h1 class="quiz-title">Asta Dasa Parwa</h1>
            
            <a href="{{ route('quiz.play') }}" class="btn-start">Start</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
