<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizPlayController extends Controller
{
    public function start()
    {
        $questions = Quiz::select(
            'id',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_option'
        )->inRandomOrder()
        ->limit(10)
        ->get();

        return view('quiz.play', compact('questions'));
    }

    public function submit(Request $request)
    {
        $answers = json_decode($request->answers_json, true) ?? [];

        $questions = session('quiz_questions');

        $correct = 0;

        foreach ($questions as $q) {
            if (
                isset($answers[$q->id]) &&
                $answers[$q->id] == $q->correct_answer
            ) {
                $correct++;
            }
        }

        $score = $correct * 10;

        return redirect()->route('quiz.result')->with([
            'score'   => $score,
            'correct' => $correct,
            'total'   => count($questions)
        ]);
    }

    public function result()
    {
        return view('quiz.result', [
            'score'   => session('score', 0),
            'correct' => session('correct', 0),
            'total'   => session('total', 0)
        ]);
    }

}
