<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $questions = Quiz::latest()->paginate(10);
        return view('admin.quiz.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.quiz.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_option' => 'required'
        ]);

        Quiz::create($request->all());

        return redirect()->route('admin.quiz.index')
            ->with('success','Soal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        return view('admin.quiz.edit', compact('quiz'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_option' => 'required'
        ]);

        $quiz = Quiz::findOrFail($id);
        $quiz->update($request->all());

        return redirect()->route('admin.quiz.index')
            ->with('success','Soal berhasil diupdate');
    }

    public function destroy($id)
    {
        Quiz::destroy($id);
        return back()->with('success','Soal berhasil dihapus');
    }
}
