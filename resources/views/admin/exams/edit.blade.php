@extends('layouts.app')

@section('title', 'Edit Exam')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Exam: {{ $exam->title }}</h2>

        <form action="{{ route('exams.update', $exam) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Exam Title</label>
                <input type="text" name="title" value="{{ $exam->title }}" required class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ $exam->description }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Duration (minutes)</label>
                <input type="number" name="duration" value="{{ $exam->duration }}" required min="1" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Total Questions</label>
                <input type="number" name="total_questions" value="{{ $exam->total_questions }}" required min="1" class="w-full border rounded px-3 py-2">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update Exam
            </button>
            <a href="{{ route('exams.index') }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </form>
    </div>
</div>
@endsection
