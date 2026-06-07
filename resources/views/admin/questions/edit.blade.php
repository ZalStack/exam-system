@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Question</h2>

        <form action="{{ route('exams.questions.update', [$exam, $question]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text</label>
                <textarea name="question_text" rows="3" required class="w-full border rounded px-3 py-2">{{ $question->question_text }}</textarea>
            </div>

            @if($question->image)
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Current Image</label>
                    <img src="/storage/{{ $question->image }}" class="max-w-xs mb-2">
                    <p class="text-sm text-gray-500">Upload new image to replace</p>
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <div id="options-container">
                    @php $options = json_decode($question->options, true); @endphp
                    <div class="mb-2">
                        <input type="text" name="options[A]" value="{{ $options['A'] ?? '' }}" placeholder="Option A" required class="w-full border rounded px-3 py-2 mb-2">
                    </div>
                    <div class="mb-2">
                        <input type="text" name="options[B]" value="{{ $options['B'] ?? '' }}" placeholder="Option B" required class="w-full border rounded px-3 py-2 mb-2">
                    </div>
                    <div class="mb-2">
                        <input type="text" name="options[C]" value="{{ $options['C'] ?? '' }}" placeholder="Option C" required class="w-full border rounded px-3 py-2 mb-2">
                    </div>
                    <div class="mb-2">
                        <input type="text" name="options[D]" value="{{ $options['D'] ?? '' }}" placeholder="Option D" required class="w-full border rounded px-3 py-2 mb-2">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Correct Answer</label>
                <select name="correct_answer" required class="w-full border rounded px-3 py-2">
                    <option value="">Select correct answer</option>
                    <option value="A" {{ $question->correct_answer == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ $question->correct_answer == 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ $question->correct_answer == 'C' ? 'selected' : '' }}>C</option>
                    <option value="D" {{ $question->correct_answer == 'D' ? 'selected' : '' }}>D</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Points</label>
                <input type="number" name="points" value="{{ $question->points }}" min="1" required class="w-full border rounded px-3 py-2">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Question
            </button>
            <a href="{{ route('exams.questions.index', $exam) }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </form>
    </div>
</div>
@endsection
