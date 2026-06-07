@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Available Exams</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($availableExams as $exam)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-2">{{ $exam->title }}</h3>
            <p class="text-gray-600 mb-4">{{ $exam->description ?? 'No description' }}</p>
            <div class="space-y-2 mb-4">
                <p class="text-sm">Duration: {{ $exam->duration }} minutes</p>
                <p class="text-sm">Total Questions: {{ $exam->total_questions }}</p>
            </div>
            <form action="{{ route('exams.register', $exam) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Register for Exam
                </button>
            </form>
        </div>
        @endforeach
    </div>

    @if($registeredExams->count() > 0)
    <h2 class="text-2xl font-bold mb-6">My Registered Exams</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($registeredExams as $exam)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-2">{{ $exam->title }}</h3>
            <p class="text-gray-600 mb-4">{{ $exam->description ?? 'No description' }}</p>
            <div class="space-y-2 mb-4">
                <p class="text-sm">Duration: {{ $exam->duration }} minutes</p>
                <p class="text-sm">Total Questions: {{ $exam->total_questions }}</p>
                @if($exam->pivot->completed_at)
                    <p class="text-green-600">Completed: {{ $exam->pivot->score }}%</p>
                    <a href="{{ route('exams.results', $exam) }}" class="text-blue-600">View Results</a>
                @elseif($exam->pivot->started_at)
                    <a href="{{ route('exams.take', $exam) }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Continue Exam</a>
                @else
                    <a href="{{ route('exams.take', $exam) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Start Exam</a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
