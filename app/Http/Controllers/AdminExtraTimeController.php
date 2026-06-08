<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminExtraTimeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        if (!$authUser->isAdmin()) {
            abort(403);
        }

        // Get all exams with users who have started but not completed
        $exams = Exam::with(['users' => function($query) {
            $query->wherePivotNotNull('started_at')
                  ->wherePivotNull('completed_at');
        }])->get();

        return view('admin.extra-time.index', compact('exams'));
    }

    public function addTime(Request $request)
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
            return redirect()->back()->with('error', 'Cannot add extra time - User has not started or already completed the exam');
        }

        $currentExtraTime = $userExam->pivot->extra_time ?? 0;
        $newExtraTime = $currentExtraTime + $request->extra_minutes;

        $user->exams()->updateExistingPivot($exam->id, [
            'extra_time' => $newExtraTime
        ]);

        return redirect()->back()->with('success', "Added {$request->extra_minutes} minutes extra time to {$user->name}");
    }
}
