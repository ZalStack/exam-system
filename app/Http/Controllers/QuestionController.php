<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{

    public function index(Exam $exam)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $questions = $exam->questions;
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        return view('admin.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('questions', 'public');
            $validated['image'] = $path;
        }

        $validated['options'] = json_encode($request->options);
        $exam->questions()->create($validated);

        return redirect()->route('exams.questions.index', $exam)->with('success', 'Question added successfully');
    }

    public function edit(Exam $exam, Question $question)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        return view('admin.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1'
        ]);

        if ($request->hasFile('image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $path = $request->file('image')->store('questions', 'public');
            $validated['image'] = $path;
        }

        $validated['options'] = json_encode($request->options);
        $question->update($validated);

        return redirect()->route('exams.questions.index', $exam)->with('success', 'Question updated successfully');
    }

    public function destroy(Exam $exam, Question $question)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();
        return redirect()->route('exams.questions.index', $exam)->with('success', 'Question deleted successfully');
    }
}
