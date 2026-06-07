@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title)

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold mb-2">{{ $exam->title }} - Results</h2>
            <p class="text-gray-600">Thank you for completing the exam!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-4 rounded-lg text-white text-center">
                <p class="text-sm opacity-90">Your Score</p>
                <p class="text-3xl font-bold">{{ round($userExam->pivot->score, 1) }}%</p>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-lg text-white text-center">
                <p class="text-sm opacity-90">Time Spent</p>
                <p class="text-3xl font-bold">
                    {{ floor($userExam->pivot->time_spent / 60) }}:{{ str_pad($userExam->pivot->time_spent % 60, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-lg text-white text-center">
                <p class="text-sm opacity-90">Correct Answers</p>
                @php
                    $correctCount = 0;
                    foreach($questions as $question) {
                        $userAnswer = $answers[$question->id] ?? null;
                        $options = json_decode($question->options, true);
                        $correctAnswerValue = $options[$question->correct_answer] ?? null;
                        if ($userAnswer && $correctAnswerValue && $userAnswer === $correctAnswerValue) {
                            $correctCount++;
                        }
                    }
                @endphp
                <p class="text-3xl font-bold">{{ $correctCount }}/{{ $questions->count() }}</p>
            </div>
        </div>
    </div>

    <h3 class="text-xl font-bold mb-4">Answer Review</h3>

    @foreach($questions as $index => $question)
    <div class="bg-white rounded-lg shadow p-6 mb-4 hover:shadow-md transition">
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center space-x-3">
                <span class="bg-gray-200 text-gray-700 w-8 h-8 rounded-full flex items-center justify-center font-bold">
                    {{ $index + 1 }}
                </span>
                <h4 class="font-bold text-lg">{{ Str::limit($question->question_text, 100) }}</h4>
            </div>
            @php
                $userAnswer = $answers[$question->id] ?? null;
                $options = json_decode($question->options, true);
                $correctAnswerValue = $options[$question->correct_answer] ?? null;
                $isCorrect = $userAnswer && $correctAnswerValue && $userAnswer === $correctAnswerValue;
            @endphp
            @if($isCorrect)
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                    ✓ Correct
                </span>
            @else
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                    ✗ Incorrect
                </span>
            @endif
        </div>

        <div class="ml-11">
            @if($question->image)
                <img src="/storage/{{ $question->image }}" class="max-w-full max-h-48 object-contain mb-4 rounded">
            @endif

            <div class="space-y-2">
                @foreach($options as $key => $option)
                    <div class="p-3 rounded-lg {{ $option == $correctAnswerValue ? 'bg-green-50 border border-green-200' : '' }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if($option == $correctAnswerValue)
                                    <span class="text-green-600 font-bold">✓</span>
                                @elseif($userAnswer == $option)
                                    <span class="text-red-600 font-bold">✗</span>
                                @else
                                    <span class="text-gray-400">○</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="{{ $option == $correctAnswerValue ? 'font-semibold text-green-800' : ($userAnswer == $option ? 'text-red-800' : 'text-gray-700') }}">
                                    <strong>{{ $key }}.</strong> {{ $option }}
                                </p>
                                @if($option == $correctAnswerValue)
                                    <p class="text-xs text-green-600 mt-1">Correct answer</p>
                                @endif
                                @if($userAnswer == $option && $option != $correctAnswerValue)
                                    <p class="text-xs text-red-600 mt-1">Your answer (incorrect)</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 text-sm text-gray-500">
                Points: {{ $isCorrect ? $question->points : 0 }} / {{ $question->points }}
            </div>
        </div>
    </div>
    @endforeach

    <div class="text-center mt-6 mb-12">
        <a href="{{ route('exams.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition inline-block">
            ← Back to Exams Dashboard
        </a>
    </div>
</div>
@endsection
