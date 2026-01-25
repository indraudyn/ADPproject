// ===============================
// LOAD DATA
// ===============================

let questions = window.quizData;
let index = 0;
let answers = {};

console.log("Questions:", questions);

// ===============================
// TIMER (10 MENIT)
// ===============================

let totalSeconds = 600;
const timerEl = document.getElementById("timer");

setInterval(() => {
    let minutes = Math.floor(totalSeconds / 60);
    let seconds = totalSeconds % 60;

    timerEl.innerText =
        String(minutes).padStart(2, "0") +
        ":" +
        String(seconds).padStart(2, "0");

    if (totalSeconds <= 0) {
        finishQuiz();
    }

    totalSeconds--;
}, 1000);

// ===============================
// LOAD QUESTION
// ===============================

function loadQuestion() {
    if (!questions.length) {
        document.getElementById("questionText").innerText =
            "Soal tidak tersedia";
        return;
    }

    let q = questions[index];

    // tampil soal
    document.getElementById("questionText").innerText = q.question;

    // counter
    document.getElementById("counterText").innerText =
        `Question ${index + 1} of ${questions.length}`;

    let percent = Math.round(((index + 1) / questions.length) * 100);
    document.getElementById("percentText").innerText = percent + "% Complete";
    document.getElementById("progressBar").style.width = percent + "%";

    // opsi
    let html = "";

    html += optionHTML("A", q.option_a);
    html += optionHTML("B", q.option_b);
    html += optionHTML("C", q.option_c);
    html += optionHTML("D", q.option_d);

    document.getElementById("optionsBox").innerHTML = html;

    // tombol
    document.getElementById("backBtn").classList.toggle("d-none", index === 0);

    document.getElementById("nextBtn").innerText =
        index === questions.length - 1 ? "Finish" : "Next";
}

// ===============================
// OPTION TEMPLATE
// ===============================

function optionHTML(letter, text) {
    let active = answers[index] === letter ? "active" : "";

    return `
        <div class="option ${active}"
             onclick="selectOption('${letter}')">
            ${letter}. ${text}
        </div>
    `;
}

// ===============================
// SELECT OPTION
// ===============================

function selectOption(letter) {
    answers[index] = letter;

    document
        .querySelectorAll(".option")
        .forEach((o) => o.classList.remove("active"));

    event.target.classList.add("active");
}

// ===============================
// BUTTON NEXT
// ===============================

document.getElementById("nextBtn").onclick = function () {
    if (index < questions.length - 1) {
        index++;
        loadQuestion();
    } else {
        finishQuiz();
    }
};

// ===============================
// BUTTON BACK
// ===============================

document.getElementById("backBtn").onclick = function () {
    index--;
    loadQuestion();
};

// ===============================
// FINISH
// ===============================

function finishQuiz() {
    let correct = 0;

    questions.forEach((q, i) => {
        if (answers[i] === q.correct_option) {
            correct++;
        }
    });

    let score = correct * 10; // skor dikali 10

    localStorage.setItem("quiz_score", score);
    localStorage.setItem("quiz_correct", correct);
    localStorage.setItem("quiz_total", questions.length);

    window.location.href = "/quiz/result";
}

// ===============================
// START FIRST QUESTION
// ===============================

loadQuestion();
