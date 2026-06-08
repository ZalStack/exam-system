@extends('layouts.app')

@section('title', 'Manage Questions')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Questions for: {{ $exam->title }}</h2>
            <p class="text-gray-600 mt-1">Total questions: {{ $questions->count() }} / {{ $exam->total_questions }}</p>
        </div>
        <a href="{{ route('admin.exams.questions.create', $exam) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Question
        </a>
    </div>

    @if($questions->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No questions added yet. Click "Add Question" to start adding questions.
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @foreach($questions as $index => $question)
            <div class="border-b p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold">Question {{ $index + 1 }}</h3>
                    <div>
                        <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="text-green-600 hover:text-green-900 mr-3">✏️ Edit</a>
                        <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this question?')">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
                <p class="mb-3 text-gray-800">{{ $question->question_text }}</p>
                @if($question->image)
                    <img src="/storage/{{ $question->image }}" class="max-w-xs max-h-48 object-contain mb-3 rounded">
                @endif
                <div class="space-y-1 ml-4">
                    @php $options = json_decode($question->options, true); @endphp
                    @foreach($options as $key => $option)
                        <p class="{{ $option == $question->correct_answer ? 'text-green-600 font-bold' : 'text-gray-600' }}">
                            {{ $key }}. {{ $option }}
                            @if($option == $question->correct_answer)
                                <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded">✓ Correct Answer</span>
                            @endif
                        </p>
                    @endforeach
                </div>
                <p class="mt-2 text-sm text-gray-500">Points: {{ $question->points }}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('admin.exams.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Exams</a>
        </div>
    @endif
</div>
@endsection
