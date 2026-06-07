@extends('layouts.app')

@section('title', 'Taking Exam: ' . $exam->title)

@section('content')
<div class="max-w-5xl mx-auto px-4">
    <!-- Header with Timer and Progress -->
    <div class="bg-white rounded-lg shadow mb-4 p-4 sticky top-0 z-10">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-2 md:mb-0">
                <h2 class="text-xl font-bold">{{ $exam->title }}</h2>
                <div class="text-sm text-gray-600">
                    <span id="answered-count">0</span> of {{ $questions->count() }} questions answered
                </div>
                @if($extraTime > 0)
                    <div class="text-xs text-green-600 mt-1">
                        ⏰ Extra time: +{{ $extraTime }} minutes
                    </div>
                @endif
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600">Time Remaining</p>
                <p id="timer" class="text-3xl font-bold text-red-600"></p>
                @if($extraTime > 0)
                    <p class="text-xs text-green-600">Includes extra time</p>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-3">
            <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                <div id="progress-bar" class="bg-green-500 h-2 transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow mb-4">
        <!-- Question Navigation -->
        <div class="p-4 border-b bg-gray-50">
            <p class="text-sm font-medium mb-2">Question Navigation (Click to jump)</p>
            <div id="nav-buttons-container" class="flex flex-wrap gap-2">
                <!-- Navigation buttons will be populated by JavaScript -->
            </div>
        </div>

        <!-- Current Question -->
        <div id="question-container" class="p-6 min-h-[400px]">
            <div class="text-center text-gray-500">Loading questions...</div>
        </div>

        <!-- Navigation Buttons -->
        <div class="p-6 border-t flex justify-between bg-gray-50">
            <button id="prev-btn" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                ← Previous Question
            </button>
            <button id="next-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Next Question →
            </button>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-center">
        <button type="button" onclick="confirmSubmit()" class="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 font-bold shadow-lg transition-all">
            📝 Submit Exam
        </button>
    </div>
</div>

<form id="submit-form" action="{{ route('exams.submit', $exam) }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    // Data from Laravel
    let questions = @json($questions);
    let savedAnswers = @json($savedAnswers);
    let timeRemaining = {{ $timeRemaining }};
    let extraTime = {{ $extraTime }};
    let currentQuestionIndex = 0;
    let timerInterval;
    let autoSaveTimeout = null;

    console.log('Questions loaded:', questions.length);
    console.log('Saved answers:', savedAnswers);
    console.log('Time remaining:', timeRemaining);

    // Initialize the exam
    document.addEventListener('DOMContentLoaded', function() {
        init();
    });

    function init() {
        buildNavigationButtons();
        updateAnsweredCount();
        loadQuestion(currentQuestionIndex);
        startTimer();
        attachEventListeners();
    }

    function buildNavigationButtons() {
        let container = document.getElementById('nav-buttons-container');
        container.innerHTML = '';

        questions.forEach((question, index) => {
            let isAnswered = savedAnswers[question.id] !== undefined;
            let button = document.createElement('button');
            button.className = `nav-question w-10 h-10 rounded-lg font-bold transition-all hover:scale-105 ${
                isAnswered ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400'
            }`;
            button.textContent = index + 1;
            button.dataset.questionId = question.id;
            button.dataset.questionIndex = index;
            button.onclick = function() {
                loadQuestion(parseInt(this.dataset.questionIndex));
            };
            container.appendChild(button);
        });
    }

    function startTimer() {
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            if (timeRemaining <= 1) {
                clearInterval(timerInterval);
                autoSubmit();
                return;
            }
            timeRemaining--;
            updateTimerDisplay();

            // Warning at 5 minutes and 1 minute
            if (timeRemaining === 300) {
                showWarning('⚠️ 5 minutes remaining!');
            }
            if (timeRemaining === 60) {
                showWarning('⚠️ 1 minute remaining!');
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        let hours = Math.floor(timeRemaining / 3600);
        let minutes = Math.floor((timeRemaining % 3600) / 60);
        let seconds = timeRemaining % 60;

        let timerElement = document.getElementById('timer');
        if (hours > 0) {
            timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Change color when time is low
        if (timeRemaining <= 60) {
            timerElement.classList.add('text-red-600', 'animate-pulse');
        }
    }

    function showWarning(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Time Warning',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function autoSubmit() {
        Swal.fire({
            icon: 'info',
            title: 'Time\'s Up!',
            text: 'Your exam will be submitted automatically.',
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            document.getElementById('submit-form').submit();
        });
    }

    function saveAnswer(questionId, answer) {
        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);

        autoSaveTimeout = setTimeout(() => {
            fetch('{{ route("exams.save-answer", $exam) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ question_id: questionId, answer: answer })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    savedAnswers[questionId] = answer;
                    updateNavigationButtons();
                    updateAnsweredCount();
                    showAutoSaveIndicator();
                } else if (data.error) {
                    console.error('Save error:', data.error);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                // Still save locally even if fetch fails
                savedAnswers[questionId] = answer;
                updateNavigationButtons();
                updateAnsweredCount();
            });
        }, 500);
    }

    function showAutoSaveIndicator() {
        let indicator = document.getElementById('auto-save-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'auto-save-indicator';
            indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transition-opacity z-50';
            indicator.innerHTML = '✓ Auto-saved';
            document.body.appendChild(indicator);
        }
        indicator.style.opacity = '1';
        setTimeout(() => {
            indicator.style.opacity = '0';
        }, 2000);
    }

    function updateNavigationButtons() {
        document.querySelectorAll('.nav-question').forEach(btn => {
            let qId = parseInt(btn.dataset.questionId);
            if (savedAnswers[qId] !== undefined) {
                btn.classList.remove('bg-gray-300', 'text-gray-700');
                btn.classList.add('bg-green-500', 'text-white');
            } else {
                btn.classList.remove('bg-green-500', 'text-white');
                btn.classList.add('bg-gray-300', 'text-gray-700');
            }
        });
    }

    function updateAnsweredCount() {
        let answered = Object.keys(savedAnswers).length;
        let total = questions.length;
        document.getElementById('answered-count').textContent = answered;
        let progress = (answered / total) * 100;
        document.getElementById('progress-bar').style.width = progress + '%';
    }

    function loadQuestion(index) {
        console.log('Loading question index:', index);

        if (index < 0 || index >= questions.length) return;

        currentQuestionIndex = index;
        let question = questions[currentQuestionIndex];

        // Parse options (they might be JSON string or already parsed)
        let options;
        if (typeof question.options === 'string') {
            options = JSON.parse(question.options);
        } else {
            options = question.options;
        }

        let currentAnswer = savedAnswers[question.id] || '';

        let html = `
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-xl font-bold">Question ${currentQuestionIndex + 1} of ${questions.length}</h3>
                    <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full">${question.points} points</span>
                </div>
                <div class="prose max-w-none mb-6">
                    <p class="text-gray-800 text-lg">${escapeHtml(question.question_text)}</p>
                </div>
        `;

        if (question.image) {
            html += `<div class="mb-6">
                        <img src="/storage/${question.image}" class="max-w-full max-h-96 object-contain rounded-lg shadow-md">
                     </div>`;
        }

        html += `<div class="space-y-3 mt-6">`;

        let optionLetters = ['A', 'B', 'C', 'D'];
        for (let i = 0; i < optionLetters.length; i++) {
            let letter = optionLetters[i];
            let optionText = options[letter];
            if (optionText) {
                let isChecked = (currentAnswer === optionText);
                let escapedOptionText = escapeHtml(optionText);
                html += `
                    <label class="flex items-start space-x-3 p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all ${isChecked ? 'bg-blue-50 border-blue-300' : ''}">
                        <input type="radio" name="answer" value="${escapedOptionText.replace(/"/g, '&quot;')}"
                               ${isChecked ? 'checked' : ''}
                               onchange="saveAnswer(${question.id}, this.value)"
                               class="form-radio h-5 w-5 text-blue-600 mt-0.5">
                        <div class="flex-1">
                            <span class="font-bold text-lg">${letter}.</span>
                            <span class="ml-2">${escapedOptionText}</span>
                        </div>
                    </label>
                `;
            }
        }

        html += `</div></div>`;
        document.getElementById('question-container').innerHTML = html;

        // Update MathJax
        if (window.MathJax) {
            MathJax.typesetPromise([document.getElementById('question-container')]).catch(console.error);
        }

        // Update navigation buttons state
        updateNavButtonsState();
    }

    function escapeHtml(text) {
        if (!text) return '';
        let div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function updateNavButtonsState() {
        let prevBtn = document.getElementById('prev-btn');
        let nextBtn = document.getElementById('next-btn');

        if (prevBtn) {
            prevBtn.disabled = (currentQuestionIndex === 0);
        }
        if (nextBtn) {
            if (currentQuestionIndex === questions.length - 1) {
                nextBtn.textContent = 'Finish Review';
                nextBtn.classList.remove('bg-blue-600');
                nextBtn.classList.add('bg-green-600');
            } else {
                nextBtn.textContent = 'Next Question →';
                nextBtn.classList.remove('bg-green-600');
                nextBtn.classList.add('bg-blue-600');
            }
        }
    }

    function nextQuestion() {
        console.log('Next clicked, current index:', currentQuestionIndex);
        if (currentQuestionIndex < questions.length - 1) {
            loadQuestion(currentQuestionIndex + 1);
        } else {
            confirmSubmit();
        }
    }

    function prevQuestion() {
        console.log('Prev clicked, current index:', currentQuestionIndex);
        if (currentQuestionIndex > 0) {
            loadQuestion(currentQuestionIndex - 1);
        }
    }

    function confirmSubmit() {
        let unanswered = questions.filter(q => savedAnswers[q.id] === undefined).length;
        let answered = questions.length - unanswered;

        Swal.fire({
            title: 'Submit Exam?',
            html: `
                <div class="text-left">
                    <p>You have answered <strong>${answered}</strong> out of <strong>${questions.length}</strong> questions.</p>
                    ${unanswered > 0 ? '<p class="text-red-600 mt-2">⚠️ You have ' + unanswered + ' unanswered question(s)!</p>' : '<p class="text-green-600 mt-2">✓ All questions answered!</p>'}
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, submit my exam!',
            cancelButtonText: 'No, continue working'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('submit-form').submit();
            }
        });
    }

    function attachEventListeners() {
        let prevBtn = document.getElementById('prev-btn');
        let nextBtn = document.getElementById('next-btn');

        if (prevBtn) {
            prevBtn.removeEventListener('click', prevQuestion);
            prevBtn.addEventListener('click', prevQuestion);
        }
        if (nextBtn) {
            nextBtn.removeEventListener('click', nextQuestion);
            nextBtn.addEventListener('click', nextQuestion);
        }
    }

    // Warning before leaving page
    window.addEventListener('beforeunload', function(e) {
        if (timeRemaining > 0) {
            e.preventDefault();
            e.returnValue = 'You have not submitted your exam. Are you sure you want to leave?';
        }
    });
</script>
@endsection
