@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Question</h2>

        <form action="{{ route('admin.exams.questions.update', [$exam, $question]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text</label>
                <textarea name="question_text" rows="4" required class="w-full border rounded px-3 py-2">{{ $question->question_text }}</textarea>
            </div>

            @if($question->image)
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Current Image</label>
                    <img src="/storage/{{ $question->image }}" class="max-w-xs max-h-48 object-contain mb-2 rounded">
                    <p class="text-xs text-gray-500">Upload new image to replace current one</p>
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">New Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <div class="space-y-2">
                    @php $options = json_decode($question->options, true); @endphp
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">A.</span>
                        <input type="text" name="options[A]" value="{{ $options['A'] ?? '' }}" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">B.</span>
                        <input type="text" name="options[B]" value="{{ $options['B'] ?? '' }}" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">C.</span>
                        <input type="text" name="options[C]" value="{{ $options['C'] ?? '' }}" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">D.</span>
                        <input type="text" name="options[D]" value="{{ $options['D'] ?? '' }}" required class="flex-1 border rounded px-3 py-2">
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

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Question
                </button>
                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
