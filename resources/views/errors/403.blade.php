<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;800;900&family=Oleo+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-red: #8b0000;
            --gradient-red: linear-gradient(135deg, #a50000 0%, #8b0000 100%);
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: var(--gradient-red);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            overflow: hidden;
            position: relative;
        }

        /* Subtle background accents */
        .bg-accent {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            filter: blur(60px);
            z-index: 0;
        }

        .accent-1 {
            width: 500px;
            height: 500px;
            top: -150px;
            left: -150px;
        }

        .accent-2 {
            width: 400px;
            height: 400px;
            bottom: -100px;
            right: -100px;
        }

        .error-container {
            text-align: center;
            z-index: 10;
            padding: 50px 40px;
            background: var(--bg-light);
            color: var(--text-dark);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 90%;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(40px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-family: 'Oleo Script', cursive;
            font-size: 9rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 20px;
            color: var(--primary-red);
            background: linear-gradient(to bottom, #cf0000, #8b0000);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 5px 15px rgba(139, 0, 0, 0.15);
            animation: pulseText 3s infinite alternate;
        }

        @keyframes pulseText {
            0% { transform: scale(1); text-shadow: 2px 5px 15px rgba(139, 0, 0, 0.15); }
            100% { transform: scale(1.03); text-shadow: 5px 10px 25px rgba(139, 0, 0, 0.25); }
        }

        .error-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
            color: var(--text-dark);
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 35px;
            padding: 15px 25px;
            background: #f1f5f9;
            border-radius: 12px;
            border-left: 4px solid var(--primary-red);
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--gradient-red);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(139, 0, 0, 0.2);
        }

        .btn-home:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(139, 0, 0, 0.3);
            color: white;
        }

        .btn-home i {
            transition: transform 0.3s;
        }

        .btn-home:hover i {
            transform: translateX(-4px);
        }

        /* Responsive */
        @media (max-width: 600px) {
            .error-code { font-size: 6.5rem; }
            .error-title { font-size: 1.8rem; }
            .error-container { padding: 40px 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
            .error-message { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <x-loading-screen />

    <div class="bg-accent accent-1"></div>
    <div class="bg-accent accent-2"></div>

    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">Akses Ditolak!</h1>
        
        <div class="error-message">
            Ups! Maaf, Anda tidak memiliki izin untuk mengakses halaman atau melakukan tindakan ini. 
            <strong>{{ $exception->getMessage() ?: 'Halaman ini dikhususkan untuk pengguna dengan hak akses tertentu.' }}</strong>
        </div>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="btn-home">
            <i class="bi bi-arrow-left"></i> Kembali ke Halaman Sebelumnya
        </a>
    </div>

</body>
</html>
