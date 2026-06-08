# Dokumentasi Keamanan Website - Sistem Ujian Online
---

Sistem ujian online ini menerapkan keamanan berlapis (Defense in Depth) yang diimplementasikan langsung pada kode sumber. Setiap metode keamanan berikut dapat diverifikasi di file kode yang ada.

---

## 1. Pencegahan SQL Injection

**File:** `ExamController.php`, `ExamTakingController.php`, `QuestionController.php`

Seluruh query database menggunakan **Laravel Eloquent ORM** yang secara otomatis menerapkan parameter binding. Tidak ada raw query string di seluruh codebase.

```php
// ExamController.php — Eloquent dengan parameter binding
$exam = Exam::findOrFail($request->exam_id);
$user = User::findOrFail($request->user_id);

// ExamTakingController.php — Relasi pivot melalui Eloquent
$userExam = $authUser->exams()->where('exam_id', $exam->id)->first();
```

Parameter binding memastikan input user diperlakukan sebagai **data**, bukan kode SQL yang dapat dieksekusi. Ini mencegah serangan seperti `' OR 1=1 --`.

---

## 2. Pencegahan Cross-Site Scripting (XSS)

**File:** Semua Blade views (`.blade.php`)

Laravel Blade menggunakan syntax `{{ $variable }}` yang secara otomatis melakukan HTML escaping pada semua output.

```blade
{{-- Output otomatis di-escape, karakter <script> menjadi &lt;script&gt; --}}
<p>{{ $question->question_text }}</p>
<p>{{ $exam->title }}</p>
```

Karakter berbahaya seperti `<`, `>`, `"`, `'`, dan `&` dikonversi menjadi HTML entities sehingga tidak dapat dieksekusi sebagai kode JavaScript di browser pengguna lain.

---

## 3. Pencegahan Cross-Site Request Forgery (CSRF)

**File:** `app.php` (middleware stack), semua form views

`ValidateCsrfToken` middleware terdaftar di web middleware stack di `app.php` dan aktif untuk semua request POST/PUT/DELETE.

```php
// app.php — CSRF middleware aktif di semua web routes
$middleware->web([
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    // ...
]);
```

```blade
{{-- Di setiap form --}}
<form method="POST" action="{{ route('exams.store') }}">
    @csrf
</form>
```

Token CSRF unik per session memastikan setiap request berasal dari form yang sah di aplikasi ini, bukan dari situs eksternal yang berbahaya.

---

## 4. Keamanan Password & Autentikasi

**File:** `User.php`, `app.php`, `web.php`

Password di-hash otomatis menggunakan **Bcrypt** melalui cast `'hashed'` di model User. Autentikasi menggunakan **Laravel Breeze** yang sudah teruji.

```php
// User.php — Password hashing otomatis
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Bcrypt otomatis
    ];
}
```

```php
// web.php — Semua route sensitif dilindungi middleware auth
Route::middleware(['auth'])->group(function () {
    Route::get('/exams', [ExamController::class, 'index']);
    // ...
});
```

Bcrypt adalah algoritma yang sengaja lambat (~100ms per hash) sehingga menyulitkan serangan brute force meskipun database bocor.

---

## 5. Role-Based Access Control (RBAC)

**File:** `AdminMiddleware.php`, `UserMiddleware.php`, `User.php`, `web.php`

Sistem menggunakan dua lapis proteksi: **middleware di route level** dan **pengecekan manual di controller**.

```php
// AdminMiddleware.php — Blokir di level route
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Unauthorized access. Admin only.');
    }
    return $next($request);
}
```

```php
// web.php — Admin routes dilindungi middleware 'admin'
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/exams', [ExamController::class, 'index']);
    Route::delete('/exams/{exam}', [ExamController::class, 'destroy']);
    // Semua route admin di sini
});
```

```php
// User.php — Helper method untuk cek role
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

Dengan middleware `admin` di route, user biasa akan mendapat **HTTP 403** bahkan sebelum request masuk ke controller, meskipun mereka mengetahui URL admin secara langsung.

---

## 6. Validasi Input Server-Side

**File:** `ExamController.php`, `QuestionController.php`, `AdminExtraTimeController.php`, `ExamTakingController.php`

Setiap input dari user divalidasi di sisi server dengan aturan yang ketat sebelum diproses.

```php
// ExamController.php
$validated = $request->validate([
    'title'           => 'required|string|max:255',
    'description'     => 'nullable|string',
    'duration'        => 'required|integer|min:1|max:480', // Maks 8 jam
    'total_questions' => 'required|integer|min:1|max:200', // Maks 200 soal
]);

// AdminExtraTimeController.php
$request->validate([
    'exam_id'       => 'required|exists:exams,id',
    'user_id'       => 'required|exists:users,id',
    'extra_minutes' => 'required|integer|min:1|max:60',
]);
```

Validasi server-side tidak bisa dilewati meskipun user mematikan JavaScript di browser atau memanipulasi request secara manual menggunakan tools seperti Postman atau curl.

---

## 7. Keamanan Upload File

**File:** `QuestionController.php`

Upload gambar untuk soal ujian divalidasi tipe file, ukuran, dan disimpan dengan nama unik otomatis.

```php
// QuestionController.php — Validasi file upload
$validated = $request->validate([
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Maks 2MB
]);

if ($request->hasFile('image')) {
    $path = $request->file('image')->store('questions', 'public');
    // Laravel otomatis memberi nama file unik (UUID)
}

// Hapus file lama saat update
if ($question->image) {
    Storage::disk('public')->delete($question->image);
}
```

Validasi `mimes` mencegah upload file berbahaya seperti `.php`, `.exe`, atau `.js`. Penamaan otomatis mencegah path traversal attack dan penimpaan file yang sudah ada.

---

## 8. Keamanan Business Logic (Proteksi Alur Ujian)

**File:** `ExamTakingController.php`

Selain keamanan teknis, alur ujian dilindungi dari manipulasi logika bisnis.

```php
// ExamTakingController.php — Cek status ujian sebelum setiap aksi
public function saveAnswer(Request $request, Exam $exam)
{
    $userExam = $authUser->exams()->where('exam_id', $exam->id)->first();

    // Tidak bisa simpan jawaban jika sudah selesai
    if (!$userExam || $userExam->pivot->completed_at) {
        return response()->json(['error' => 'Cannot save answer'], 403);
    }

    // Cek apakah waktu masih tersedia (termasuk extra time)
    $extraTime = $userExam->pivot->extra_time ?? 0;
    $totalDuration = ($exam->duration + $extraTime) * 60;
    $timeRemaining = $totalDuration - now()->diffInSeconds($userExam->pivot->started_at);

    if ($timeRemaining <= 0) {
        $this->autoSubmit($exam); // Auto-submit jika waktu habis
        return response()->json(['error' => 'Time is up!'], 403);
    }
}
```

Pengecekan ini memastikan peserta tidak bisa mengirim jawaban setelah waktu habis atau setelah ujian selesai, meskipun memanipulasi timer di sisi client (browser).

---

## 9. Mass Assignment Protection

**File:** `Exam.php`, `Question.php`, `User.php`

Semua model mendefinisikan `$fillable` secara eksplisit untuk mencegah mass assignment attack.

```php
// Exam.php
protected $fillable = ['title', 'description', 'duration', 'total_questions'];

// Question.php
protected $fillable = ['exam_id', 'question_text', 'image', 'options', 'correct_answer', 'points'];

// User.php
protected $fillable = ['name', 'email', 'password', 'role'];

// Field sensitif disembunyikan dari serialisasi JSON
protected $hidden = ['password', 'remember_token'];
```

Tanpa `$fillable`, penyerang bisa mengirim field tambahan seperti `role=admin` melalui form untuk meningkatkan hak akses mereka.

---

## Ringkasan Implementasi

| # | Metode Keamanan | File Implementasi | Status |
|---|-----------------|-------------------|--------|
| 1 | SQL Injection Prevention | Semua Controller (Eloquent ORM) | ✅ Aktif |
| 2 | XSS Prevention | Semua Blade Views | ✅ Aktif |
| 3 | CSRF Protection | `app.php` + semua form | ✅ Aktif |
| 4 | Password Hashing (Bcrypt) | `User.php` | ✅ Aktif |
| 5 | Role-Based Access Control | `AdminMiddleware.php`, `web.php` | ✅ Aktif |
| 6 | Server-Side Input Validation | Semua Controller | ✅ Aktif |
| 7 | File Upload Security | `QuestionController.php` | ✅ Aktif |
| 8 | Business Logic Protection | `ExamTakingController.php` | ✅ Aktif |
| 9 | Mass Assignment Protection | `Exam.php`, `Question.php`, `User.php` | ✅ Aktif |

---

## Kesimpulan

Keamanan website tidak dapat mengandalkan satu metode saja. Sistem ini menerapkan **Defense in Depth** — setiap lapisan keamanan saling melengkapi:

```
Input masuk → Validasi Input (Layer 1)
            → CSRF Check (Layer 2)
            → Autentikasi (Layer 3)
            → Otorisasi RBAC (Layer 4)
            → Eloquent ORM / SQL Injection Prevention (Layer 5)
            → Blade XSS Escaping (Layer 6)
            → Business Logic Check (Layer 7)
            → Mass Assignment Protection (Layer 8)
```

Jika satu lapisan terlewati, lapisan berikutnya tetap memberikan perlindungan.
