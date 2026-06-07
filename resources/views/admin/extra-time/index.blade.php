@extends('layouts.app')

@section('title', 'Add Extra Time to Users')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Add Extra Time to Users</h2>
        <p class="text-gray-600">Give additional time to users who are currently taking exams</p>
    </div>

    @if($exams->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No active exams with users currently taking them.
        </div>
    @else
        @foreach($exams as $exam)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b px-6 py-4">
                <h3 class="text-xl font-bold">{{ $exam->title }}</h3>
                <p class="text-sm text-gray-600">Duration: {{ $exam->duration }} minutes</p>
            </div>

            <div class="p-6">
                @if($exam->users->count() > 0)
                <form action="{{ route('admin.add-extra-time') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="exam_id" value="{{ $exam->id }}">

                    <div>
                        <label class="block text-sm font-medium mb-2">Select User</label>
                        <select name="user_id" required class="w-full md:w-1/2 border rounded-lg px-3 py-2">
                            <option value="">-- Select a user --</option>
                            @foreach($exam->users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }}) -
                                Started: {{ \Carbon\Carbon::parse($user->pivot->started_at)->diffForHumans() }}
                                @if($user->pivot->extra_time > 0)
                                    | Extra time: +{{ $user->pivot->extra_time }} min
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Extra Minutes (1-60 minutes)</label>
                        <input type="number" name="extra_minutes" min="1" max="60" required
                               class="w-full md:w-1/3 border rounded-lg px-3 py-2"
                               placeholder="e.g., 5, 10, 15">
                        <p class="text-xs text-gray-500 mt-1">Maximum 60 minutes per addition</p>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Add Extra Time
                    </button>
                </form>
                @else
                <p class="text-gray-500 italic">No users currently taking this exam</p>
                @endif
            </div>
        </div>
        @endforeach
    @endif

    <div class="mt-6">
        <a href="{{ route('exams.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Dashboard</a>
    </div>
</div>
@endsection
