<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Play Quiz</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/quiz-play.css') }}">
</head>

<body>

<!-- TITLE -->
<div class="logo-title">
    <img src="/images/textasd.png">
</div>

<div class="quiz-container">

    <!-- INFO -->
    <div class="progress-row">
        <small id="counterText">Question 1 of 10</small>
        <small id="percentText">10% Complete</small>
    </div>

    <div class="progress mb-3">
        <div id="progressBar" class="progress-bar"></div>
    </div>

    <div class="timer">
        ⏱ <span id="timer">00:00</span>
    </div>

    <!-- FORM -->
    <form id="quizForm" method="POST" action="{{ route('quiz.submit') }}">
        @csrf

        <!-- CARD -->
        <div class="question-card">

            <div id="questionText" class="question-text"></div>

            <div id="optionsBox" class="option-grid"></div>

        </div>

        <!-- HIDDEN INPUT -->
        <input type="hidden" name="answers_json" id="answersInput">

        <!-- HIDDEN SUBMIT -->
        <button type="submit" id="submitReal" hidden></button>
    </form>

    <!-- FOOTER -->
    <div class="quiz-footer">
        <button id="backBtn" class="btn-nav d-none">Back</button>
        <button id="nextBtn" class="btn-nav">Next</button>
    </div>

</div>

<script>
    window.quizData = @json($questions);
</script>

<script src="{{ asset('js/quiz-play.js') }}"></script>

</body>
</html>
