<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Kuis</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/quiz-result.css') }}">
</head>

<body>
    <x-loading-screen />

<section class="result-hero">

    <!-- TITLE -->
    <img src="{{ asset('images/textasd.png') }}" class="title-img">

    <!-- CARD -->
    <div class="result-card">

        <h3 class="mb-4">QUIZ RESULT</h3>

        <div class="result-item">
            <span>SCORE</span>
            <div id="scoreBox">{{ $score }}</div>
        </div>

        <div class="result-item">
            <span>CORRECT</span>
            <div id="correctBox">{{ $correct }} / {{ $total }}</div>
        </div>

        <a href="/" class="btn-back">
            BACK
        </a>

    </div>

</section>

<script>
    // Data dikirim dari localStorage
    let score = localStorage.getItem("quiz_score");
    let correct = localStorage.getItem("quiz_correct");
    let total = localStorage.getItem("quiz_total");

    document.getElementById("scoreBox").innerText = score;
    document.getElementById("correctBox").innerText = `${correct} / ${total}`;
</script>

</body>
</html>
