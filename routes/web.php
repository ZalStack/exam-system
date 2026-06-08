<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamTakingController;
use App\Http\Controllers\AdminExtraTimeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

// Protected routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('exams.index');
    })->name('dashboard');

    // Exam routes - user facing
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::post('/exams/{exam}/register', [ExamTakingController::class, 'register'])->name('exams.register');
    Route::get('/exams/{exam}/take', [ExamTakingController::class, 'start'])->name('exams.take');
    Route::post('/exams/{exam}/save-answer', [ExamTakingController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('/exams/{exam}/submit', [ExamTakingController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/{exam}/results', [ExamTakingController::class, 'results'])->name('exams.results');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin only routes — middleware 'admin' memblokir non-admin di level route
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Exam management
    Route::get('/exams', [ExamController::class, 'index'])->name('admin.exams.index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('admin.exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('admin.exams.store');
    Route::get('/exams/{exam}/edit', [ExamController::class, 'edit'])->name('admin.exams.edit');
    Route::put('/exams/{exam}', [ExamController::class, 'update'])->name('admin.exams.update');
    Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('admin.exams.destroy');

    // Question management
    Route::get('/exams/{exam}/questions', [QuestionController::class, 'index'])->name('admin.exams.questions.index');
    Route::get('/exams/{exam}/questions/create', [QuestionController::class, 'create'])->name('admin.exams.questions.create');
    Route::post('/exams/{exam}/questions', [QuestionController::class, 'store'])->name('admin.exams.questions.store');
    Route::get('/exams/{exam}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('admin.exams.questions.edit');
    Route::put('/exams/{exam}/questions/{question}', [QuestionController::class, 'update'])->name('admin.exams.questions.update');
    Route::delete('/exams/{exam}/questions/{question}', [QuestionController::class, 'destroy'])->name('admin.exams.questions.destroy');

    // Extra time management
    Route::get('/extra-time', [AdminExtraTimeController::class, 'index'])->name('admin.extra-time');
    Route::post('/add-extra-time', [AdminExtraTimeController::class, 'addTime'])->name('admin.add-extra-time');
});
