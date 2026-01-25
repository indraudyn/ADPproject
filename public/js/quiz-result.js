function finishQuiz(timeUp = false) {
    let score = 0;

    questions.forEach((q, i) => {
        if (answers[i] === q.correct_option) {
            score++;
        }
    });

    let finalScore = Math.round((score / questions.length) * 100);

    // SIMPAN KE STORAGE
    localStorage.setItem("quiz_score", finalScore);
    localStorage.setItem("quiz_correct", score);
    localStorage.setItem("quiz_total", questions.length);

    window.location.href = "/quiz/result";
}
