@extends('layouts.app')

@section('title', 'Manage Exams')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Manage Exams</h2>
        <a href="{{ route('exams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Create New Exam
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration (mins)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($exams as $exam)
                <tr>
                    <td class="px-6 py-4">{{ $exam->title }}</td>
                    <td class="px-6 py-4">{{ $exam->duration }}</td>
                    <td class="px-6 py-4">{{ $exam->questions_count }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('exams.questions.index', $exam) }}" class="text-blue-600 hover:text-blue-900 mr-3">Questions</a>
                        <a href="{{ route('exams.edit', $exam) }}" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
