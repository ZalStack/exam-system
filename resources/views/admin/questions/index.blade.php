@extends('layouts.app')

@section('title', 'Manage Questions')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Questions for: {{ $exam->title }}</h2>
        <a href="{{ route('exams.questions.create', $exam) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Question
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @foreach($questions as $index => $question)
        <div class="border-b p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-bold">Question {{ $index + 1 }}</h3>
                <div>
                    <a href="{{ route('exams.questions.edit', [$exam, $question]) }}" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                    <form action="{{ route('exams.questions.destroy', [$exam, $question]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
            <p class="mb-3">{{ $question->question_text }}</p>
            @if($question->image)
                <img src="/storage/{{ $question->image }}" class="max-w-xs mb-3">
            @endif
            <div class="space-y-1">
                @foreach(json_decode($question->options, true) as $key => $option)
                    <p class="{{ $option == $question->correct_answer ? 'text-green-600 font-bold' : '' }}">
                        {{ $key }}. {{ $option }}
                        @if($option == $question->correct_answer)
                            <span class="ml-2 text-xs bg-green-100 px-2 py-1 rounded">Correct Answer</span>
                        @endif
                    </p>
                @endforeach
            </div>
            <p class="mt-2 text-sm text-gray-600">Points: {{ $question->points }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
