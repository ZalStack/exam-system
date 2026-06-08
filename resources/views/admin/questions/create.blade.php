@extends('layouts.app')

@section('title', 'Add Question')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Add Question to: {{ $exam->title }}</h2>

        <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text</label>
                <textarea name="question_text" rows="4" required class="w-full border rounded px-3 py-2" placeholder="Enter your question here..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Upload image for diagram or illustration (max 2MB)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">A.</span>
                        <input type="text" name="options[A]" required class="flex-1 border rounded px-3 py-2" placeholder="Option A">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">B.</span>
                        <input type="text" name="options[B]" required class="flex-1 border rounded px-3 py-2" placeholder="Option B">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">C.</span>
                        <input type="text" name="options[C]" required class="flex-1 border rounded px-3 py-2" placeholder="Option C">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">D.</span>
                        <input type="text" name="options[D]" required class="flex-1 border rounded px-3 py-2" placeholder="Option D">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Correct Answer</label>
                <select name="correct_answer" required class="w-full border rounded px-3 py-2">
                    <option value="">Select correct answer</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Points</label>
                <input type="number" name="points" value="1" min="1" required class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Question
                </button>
                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
