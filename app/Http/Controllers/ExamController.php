<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if ($authUser->isAdmin()) {
            $exams = Exam::withCount('questions')->get();
            return view('admin.exams.index', compact('exams'));
        }

        $registeredExamIds = $authUser->exams->pluck('id')->toArray();
        $availableExams = Exam::whereNotIn('id', $registeredExamIds)->get();
        $registeredExams = $authUser->exams;

        return view('user.exams.index', compact('availableExams', 'registeredExams'));
    }

    public function create()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:480',
            'total_questions' => 'required|integer|min:1|max:200',
        ]);

        $exam = Exam::create($validated);

        return redirect()->route('exams.index')->with('success', 'Exam created successfully. Now add questions to this exam.');
    }

    public function edit(Exam $exam)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')->with('success', 'Exam updated successfully');
    }

    public function destroy(Exam $exam)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully');
    }

    public function showAddExtraTimeForm()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        $exams = Exam::with(['users' => function($query) {
            $query->wherePivotNotNull('started_at')
                  ->wherePivotNull('completed_at');
        }])->get();

        return view('admin.extra-time.index', compact('exams'));
    }

    public function addExtraTime(Request $request)
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
            'extra_minutes' => 'required|integer|min:1|max:60'
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $user = User::findOrFail($request->user_id);

        $userExam = $user->exams()->where('exam_id', $exam->id)->first();

        if (!$userExam || !$userExam->pivot->started_at || $userExam->pivot->completed_at) {
            return redirect()->back()->with('error', 'Cannot add extra time to this user. User may not have started the exam or already completed it.');
        }

        $currentExtraTime = $userExam->pivot->extra_time ?? 0;
        $newExtraTime = $currentExtraTime + $request->extra_minutes;

        $user->exams()->updateExistingPivot($exam->id, [
            'extra_time' => $newExtraTime
        ]);

        return redirect()->back()->with('success', "Added {$request->extra_minutes} minutes extra time to {$user->name}. Total extra time: {$newExtraTime} minutes");
    }

    public function canRegister(Exam $exam)
    {
        $questionCount = $exam->questions->count();

        if ($questionCount != $exam->total_questions) {
            return response()->json([
                'can_register' => false,
                'message' => "Exam has {$questionCount} out of {$exam->total_questions} questions. Please wait for admin to complete the exam setup."
            ]);
        }

        return response()->json(['can_register' => true]);
    }
}
