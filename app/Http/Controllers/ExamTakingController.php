<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class ExamTakingController extends Controller
{
    public function register(Exam $exam)
    {
        // Check if already registered
        if (Auth::user()->exams()->where('exam_id', $exam->id)->exists()) {
            return redirect()->route('exams.index')->with('error', 'You are already registered for this exam');
        }

        // Check if exam has questions
        if ($exam->questions->count() == 0) {
            return redirect()->route('exams.index')->with('error', 'This exam has no questions yet. Please contact admin.');
        }

        // Register for exam
        Auth::user()->exams()->attach($exam->id, [
            'started_at' => null,
            'completed_at' => null,
            'answers' => json_encode([]),
            'score' => null,
            'time_spent' => null,
            'extra_time' => 0
        ]);

        return redirect()->route('exams.index')->with('success', 'Successfully registered for the exam. You can now start the exam.');
    }

    public function start(Exam $exam)
    {
        $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();

        if (!$userExam) {
            return redirect()->route('exams.index')->with('error', 'You are not registered for this exam');
        }

        if ($userExam->pivot->completed_at) {
            return redirect()->route('exams.results', $exam)->with('error', 'You have already completed this exam');
        }

        if (!$userExam->pivot->started_at) {
            Auth::user()->exams()->updateExistingPivot($exam->id, [
                'started_at' => now(),
                'answers' => json_encode([])
            ]);
            $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();
        }

        $questions = $exam->questions;
        $extraTime = $userExam->pivot->extra_time ?? 0;
        $totalDuration = ($exam->duration + $extraTime) * 60; // in seconds
        $timeRemaining = max(0, $totalDuration - (now()->diffInSeconds($userExam->pivot->started_at)));

        if ($timeRemaining <= 0 && !$userExam->pivot->completed_at) {
            $this->autoSubmit($exam);
            return redirect()->route('exams.results', $exam)->with('error', 'Time is up! Your exam has been auto-submitted.');
        }

        $savedAnswers = json_decode($userExam->pivot->answers, true) ?? [];

        return view('user.exams.take', compact('exam', 'questions', 'timeRemaining', 'savedAnswers', 'extraTime'));
    }

    public function saveAnswer(Request $request, Exam $exam)
    {
        try {
            $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();

            if (!$userExam || $userExam->pivot->completed_at) {
                return response()->json(['error' => 'Cannot save answer'], 403);
            }

            // Check if time is still available
            $extraTime = $userExam->pivot->extra_time ?? 0;
            $totalDuration = ($exam->duration + $extraTime) * 60;
            $timeRemaining = $totalDuration - (now()->diffInSeconds($userExam->pivot->started_at));

            if ($timeRemaining <= 0) {
                $this->autoSubmit($exam);
                return response()->json(['error' => 'Time is up! Exam auto-submitted.'], 403);
            }

            $answers = json_decode($userExam->pivot->answers, true) ?? [];
            $answers[$request->question_id] = $request->answer;

            Auth::user()->exams()->updateExistingPivot($exam->id, [
                'answers' => json_encode($answers)
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submit(Exam $exam, Request $request)
    {
        $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();

        if (!$userExam || $userExam->pivot->completed_at) {
            return redirect()->route('exams.index')->with('error', 'Cannot submit exam');
        }

        // Check if time is up
        $extraTime = $userExam->pivot->extra_time ?? 0;
        $totalDuration = ($exam->duration + $extraTime) * 60;
        $timeRemaining = $totalDuration - (now()->diffInSeconds($userExam->pivot->started_at));

        if ($timeRemaining <= 0) {
            $this->autoSubmit($exam);
            return redirect()->route('exams.results', $exam)->with('error', 'Time is up! Your exam has been auto-submitted.');
        }

        $this->calculateScore($exam);

        return redirect()->route('exams.results', $exam)->with('success', 'Exam submitted successfully!');
    }

    private function autoSubmit(Exam $exam)
    {
        $this->calculateScore($exam);
    }

    private function calculateScore(Exam $exam)
    {
        $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();
        $answers = json_decode($userExam->pivot->answers, true) ?? [];
        $questions = $exam->questions;

        $score = 0;
        $totalPoints = 0;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;
            $correctAnswer = $question->correct_answer;

            $options = json_decode($question->options, true);
            $correctAnswerValue = $options[$correctAnswer] ?? null;

            if ($userAnswer && $correctAnswerValue && $userAnswer === $correctAnswerValue) {
                $score += $question->points;
            }
        }

        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
        $timeSpent = now()->diffInSeconds($userExam->pivot->started_at);

        Auth::user()->exams()->updateExistingPivot($exam->id, [
            'score' => round($percentage, 2),
            'time_spent' => $timeSpent,
            'completed_at' => now()
        ]);
    }

    public function results(Exam $exam)
    {
        $userExam = Auth::user()->exams()->where('exam_id', $exam->id)->first();

        if (!$userExam) {
            return redirect()->route('exams.index')->with('error', 'Exam not found');
        }

        $answers = json_decode($userExam->pivot->answers, true) ?? [];
        $questions = $exam->questions;

        return view('user.exams.results', compact('exam', 'userExam', 'answers', 'questions'));
    }
}
