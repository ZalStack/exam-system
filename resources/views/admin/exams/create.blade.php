@extends('layouts.app')

@section('title', 'Create Exam')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Create New Exam</h2>

        <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Exam Title</label>
                <input type="text" name="title" required class="w-full border rounded px-3 py-2" placeholder="e.g., Mathematics Final Exam">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2" placeholder="Describe the exam..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Duration (minutes)</label>
                <input type="number" name="duration" required min="1" max="480" class="w-full border rounded px-3 py-2" placeholder="e.g., 60">
                <p class="text-xs text-gray-500 mt-1">Maximum 480 minutes (8 hours)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Total Questions</label>
                <input type="number" name="total_questions" required min="1" max="200" class="w-full border rounded px-3 py-2" placeholder="e.g., 50">
                <p class="text-xs text-gray-500 mt-1">Maximum 200 questions</p>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Create Exam
                </button>
                <a href="{{ route('admin.exams.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
