@extends('layouts.app')

@section('title', 'Add Question')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Add Question to: {{ $exam->title }}</h2>

        <form action="{{ route('exams.questions.store', $exam) }}" method="POST" enctype="multipart/form-data" id="questionForm">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text (Supports LaTeX: $formula$ or $$formula$$)</label>
                <textarea name="question_text" id="question_text" rows="4" required class="w-full border rounded px-3 py-2 font-mono"></textarea>
                <p class="text-xs text-gray-500 mt-1">Tip: Use $...$ for inline math or $$...$$ for display math. Example: $E = mc^2$ or $$\int_0^1 x^2 dx$$</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Quick Math Templates</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2">
                    <button type="button" onclick="insertMath('\\frac{a}{b}')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Fraction \frac{a}{b}</button>
                    <button type="button" onclick="insertMath('\\sqrt{x}')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Square Root √x</button>
                    <button type="button" onclick="insertMath('x^2')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Power x²</button>
                    <button type="button" onclick="insertMath('\\int_{a}^{b}')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Integral ∫</button>
                    <button type="button" onclick="insertMath('\\sum_{i=1}^{n}')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Summation Σ</button>
                    <button type="button" onclick="insertMath('\\lim_{x \\to 0}')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Limit lim</button>
                    <button type="button" onclick="insertMath('\\alpha')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Greek α</button>
                    <button type="button" onclick="insertMath('\\pi')" class="bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded text-sm">Pi π</button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Upload image for diagram or illustration (max 2MB)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Options (A, B, C, D)</label>
                <div id="options-container" class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">A.</span>
                        <input type="text" name="options[A]" placeholder="Option A" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">B.</span>
                        <input type="text" name="options[B]" placeholder="Option B" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">C.</span>
                        <input type="text" name="options[C]" placeholder="Option C" required class="flex-1 border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold w-8">D.</span>
                        <input type="text" name="options[D]" placeholder="Option D" required class="flex-1 border rounded px-3 py-2">
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

            <div class="mb-4">
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm font-medium mb-2">Preview:</p>
                    <div id="preview" class="prose max-w-none"></div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add Question
            </button>
            <a href="{{ route('exams.questions.index', $exam) }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function insertMath(formula) {
        let textarea = document.getElementById('question_text');
        let start = textarea.selectionStart;
        let end = textarea.selectionEnd;
        let text = textarea.value;
        let insertion = `$${formula}$`;
        textarea.value = text.substring(0, start) + insertion + text.substring(end);
        textarea.focus();
        textarea.setSelectionRange(start + insertion.length, start + insertion.length);
        updatePreview();
    }

    function updatePreview() {
        let text = document.getElementById('question_text').value;
        let preview = document.getElementById('preview');
        preview.innerHTML = text;
        if (window.MathJax) {
            MathJax.typesetPromise([preview]).catch(err => console.log(err));
        }
    }

    document.getElementById('question_text').addEventListener('input', updatePreview);
    updatePreview();
</script>
@endpush
@endsection
