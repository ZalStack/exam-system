## **JAWABAN SOAL TAMBAHAN - KEAMANAN WEBSITE**

Berikut adalah penjelasan lengkap tentang metode keamanan yang saya terapkan dalam mini project ini untuk memaksimalkan perlindungan website dari peretas:

---

## **PENDAHULUAN**

Dalam pengembangan sistem ujian online ini, saya menerapkan **12 metode keamanan berlapis (Defense in Depth)** untuk melindungi website dari berbagai jenis serangan siber. Berikut adalah penjelasan detailnya:

---

## **1. PENCEGAHAN SQL INJECTION**

### **Apa itu SQL Injection?**
Serangan di mana peretas menyisipkan kode SQL berbahaya melalui input form untuk mengakses atau merusak database.

### **Metode yang Saya Terapkan:**
- **Menggunakan Eloquent ORM** - Semua query database menggunakan Laravel Eloquent yang secara otomatis melakukan parameter binding
- **Tidak ada query string manual** - Menghindari raw query seperti `DB::select("SELECT * FROM users WHERE id = $id")`
- **Validasi semua input** - Setiap input dari user divalidasi sebelum diproses

### **Contoh Implementasi di Kode Saya:**
```php
// AMAN - Menggunakan Eloquent (implementasi di ExamController.php)
$exam = Exam::where('id', $request->exam_id)->first();

// AMAN - Mass assignment dengan fillable
Exam::create($validated);
```

### **Mengapa Ini Efektif:**
Parameter binding memastikan input user diperlakukan sebagai DATA, bukan sebagai KODE SQL yang bisa dieksekusi.

---

## **2. PENCEGAHAN CROSS-SITE SCRIPTING (XSS)**

### **Apa itu XSS?**
Serangan di mana peretas menyisipkan script JavaScript berbahaya ke dalam website yang akan dieksekusi di browser pengguna lain.

### **Metode yang Saya Terapkan:**
- **Blade Auto-Escaping** - Menggunakan `{{ $variable }}` bukan `{!! $variable !!}`
- **Sanitasi Output** - Semua output yang ditampilkan ke user di-escape secara otomatis
- **Fungsi escapeHtml()** - Fungsi custom untuk membersihkan teks sebelum ditampilkan

### **Contoh Implementasi di Kode Saya:**
```javascript
// Di file take.blade.php - Fungsi escape HTML
function escapeHtml(text) {
    if (!text) return '';
    let div = document.createElement('div');
    div.textContent = text;  // Browser otomatis escape HTML
    return div.innerHTML;
}

// Semua tampilan menggunakan Blade escaping
<p>{{ $question->question_text }}</p>  // Otomatis di-escape
```

### **Mengapa Ini Efektif:**
Laravel Blade secara otomatis mengkonversi karakter berbahaya seperti `<script>` menjadi `&lt;script&gt;` sehingga tidak dapat dieksekusi sebagai kode JavaScript.

---

## **3. PENCEGAHAN CROSS-SITE REQUEST FORGERY (CSRF)**

### **Apa itu CSRF?**
Serangan yang memanfaatkan session user yang sudah login untuk melakukan aksi tidak sah.

### **Metode yang Saya Terapkan:**
- **CSRF Token pada setiap form** - Token unik per session
- **Verifikasi token otomatis** - Laravel memverifikasi setiap request POST/PUT/DELETE
- **SameSite Cookie Attribute** - Cookie tidak dikirim ke domain berbeda

### **Contoh Implementasi di Kode Saya:**
```blade
<!-- Di setiap form, contoh di create exam -->
<form method="POST" action="{{ route('exams.store') }}">
    @csrf  <!-- Token CSRF otomatis ditambahkan -->
    <!-- input fields -->
</form>

<!-- Di JavaScript fetch request -->
fetch(url, {
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Token dikirim di header
    }
})
```

### **Mengapa Ini Efektif:**
Token CSRF yang unik per session memastikan bahwa request yang masuk berasal dari form yang legitimate di website kita, bukan dari situs jahat eksternal.

---

## **4. AUTENTIKASI DAN MANAJEMEN SESSION**

### **Metode yang Saya Terapkan:**
- **Laravel Breeze** - Sistem autentikasi yang sudah teruji keamanannya
- **Password Hashing dengan Bcrypt** - Algoritma hashing yang kuat
- **HTTP-Only Cookies** - Cookie tidak dapat diakses oleh JavaScript
- **Session Expiration** - Session akan berakhir setelah tidak aktif
- **Secure Cookie Flag** - Cookie hanya dikirim melalui HTTPS

### **Contoh Implementasi:**
```php
// Di User.php - Password hashing
protected function casts(): array
{
    return [
        'password' => 'hashed',  // Laravel otomatis hash dengan Bcrypt
    ];
}

// Di middleware - Proteksi route
Route::middleware(['auth'])->group(function () {
    // Route yang memerlukan login
});
```

### **Mengapa Ini Efektif:**
Bcrypt adalah algoritma yang sengaja dibuat lambat (100ms per hash) sehingga menyulitkan serangan brute force. HTTP-only cookies mencegah XSS mencuri session cookie.

---

## **5. ROLE-BASED ACCESS CONTROL (RBAC)**

### **Apa itu RBAC?**
Sistem kontrol akses berdasarkan peran (role) user.

### **Metode yang Saya Terapkan:**
- **Middleware Kustom** - AdminMiddleware untuk proteksi route admin
- **Role di Database** - Field `role` di tabel users (admin/user)
- **Pengecekan di Controller** - Verifikasi role sebelum aksi sensitive

### **Contoh Implementasi di Kode Saya:**
```php
// AdminMiddleware.php
class AdminMiddleware {
    public function handle($request, Closure $next) {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        return $next($request);
    }
}

// Di User.php
public function isAdmin(): bool {
    return $this->role === 'admin';
}

// Di controller
if (!Auth::user()->isAdmin()) {
    abort(403);
}
```

### **Mengapa Ini Efektif:**
Menerapkan prinsip least privilege - user biasa tidak bisa mengakses fitur admin meskipun mereka mengetahui URL-nya.

---

## **6. VALIDASI INPUT DI SISI SERVER**

### **Metode yang Saya Terapkan:**
- **Form Request Validation** - Validasi terpusat sebelum data diproses
- **Aturan Validasi Ketat** - Type, format, length, range checking
- **Sanitasi Input** - Menghilangkan karakter berbahaya

### **Contoh Implementasi di Kode Saya:**
```php
// Di ExamController.php
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'duration' => 'required|integer|min:1|max:480',  // Max 8 jam
    'total_questions' => 'required|integer|min:1|max:200',
]);

// Validasi dengan pesan custom
$request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed'
]);
```

### **Mengapa Ini Efektif:**
Validasi server-side adalah lapisan keamanan terakhir - tidak bisa dilewati meskipun user mematikan JavaScript di browser.

---

## **7. KEAMANAN UPLOAD FILE**

### **Metode yang Saya Terapkan:**
- **Validasi Tipe File** - Hanya gambar yang diizinkan (jpeg, png, jpg, gif)
- **Batasan Ukuran** - Maksimal 2MB
- **Rename File Otomatis** - File disimpan dengan nama unik
- **Storage Protected** - File tidak bisa diakses langsung, harus melalui storage link

### **Contoh Implementasi di Kode Saya:**
```php
// Di QuestionController.php
if ($request->hasFile('image')) {
    $validated = $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);
    $path = $request->file('image')->store('questions', 'public');
    // File akan disimpan dengan nama unik otomatis
}
```

### **Mengapa Ini Efektif:**
Validasi mencegah upload file berbahaya seperti `.php`, `.exe`, atau `.js`. Rename otomatis mencegah path traversal attack.

---

## **8. RATE LIMITING (PEMBATASAN REQUEST)**

### **Apa itu Rate Limiting?**
Membatasi jumlah request yang bisa dilakukan user dalam periode waktu tertentu.

### **Metode yang Saya Terapkan:**
- **Throttle Middleware** - Membatasi request ke endpoint sensitif
- **Login Throttle** - Mencegah brute force login
- **API Rate Limiting** - Perlindungan untuk endpoint AJAX

### **Contoh Implementasi:**
```php
// Di routes/web.php (bawaan Laravel)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/exams/{exam}/save-answer', [ExamTakingController::class, 'saveAnswer']);
});

// Laravel bawaan untuk login
// 5 percobaan gagal, lockout 1 menit
protected $maxAttempts = 5;
protected $decayMinutes = 1;
```

### **Mengapa Ini Efektif:**
Mencegah serangan brute force dan DoS (Denial of Service) dengan membatasi jumlah request.

---

## **9. ENVIRONMENT SECURITY**

### **Metode yang Saya Terapkan:**
- **.env File Protection** - Tidak di-commit ke version control
- **Debug Mode Non-Aktif** - `APP_DEBUG=false` di production
- **HTTPS Enforcement** - Konfigurasi untuk memaksa HTTPS
- **Production Environment** - `APP_ENV=production`

### **Konfigurasi di .env:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domainanda.com

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### **Mengapa Ini Efektif:**
Debug mode yang non-aktif mencegah kebocoran informasi sensitif seperti struktur database, query SQL, dan path file.

---

## **10. LOGGING DAN MONITORING**

### **Metode yang Saya Terapkan:**
- **Laravel Logging** - Semua error dan warning tercatat
- **Activity Logging** - Mencatat aktivitas penting
- **Failed Login Attempts** - Tercatat untuk deteksi brute force

### **Contoh Implementasi:**
```php
use Illuminate\Support\Facades\Log;

// Mencoba login gagal
Log::warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);

// Aktivitas admin
Log::info('Admin added extra time', [
    'admin' => Auth::user()->email,
    'user' => $user->email,
    'minutes' => $request->extra_minutes
]);
```

### **Mengapa Ini Efektif:**
Logging memungkinkan deteksi dini serangan dan investigasi forensik jika terjadi insiden keamanan.

---

## **11. HEADERS KEAMANAN**

### **Metode yang Saya Terapkan:**
- **X-Frame-Options** - Mencegah clickjacking
- **X-Content-Type-Options** - Mencegah MIME type sniffing
- **Referrer-Policy** - Mengontrol informasi referer

### **Implementasi di .htaccess atau Middleware:**
```php
// Di middleware
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

---

## **12. DEPENDENCIES UPDATE**

### **Metode yang Saya Terapkan:**
- **Regular Updates** - Selalu update package ke versi terbaru
- **Composer Audit** - Memeriksa vulnerability di package PHP
- **NPM Audit** - Memeriksa vulnerability di package JavaScript

### **Perintah yang Sering dijalankan:**
```bash
composer update           # Update PHP dependencies
composer audit           # Cek vulnerability
npm update              # Update Node dependencies
npm audit              # Cek vulnerability npm
```

### **Mengapa Ini Efektif:**
Package yang outdated sering menjadi target serangan karena vulnerability yang sudah diketahui publik.

---

## **RINGKASAN CHECKLIST KEAMANAN**

| No | Metode Keamanan | Implementasi di Proyek | Status |
|----|----------------|------------------------|--------|
| 1 | SQL Injection | Eloquent ORM dengan parameter binding | ✅ |
| 2 | XSS Prevention | Blade auto-escaping + escapeHtml() | ✅ |
| 3 | CSRF Protection | CSRF tokens di semua form & fetch | ✅ |
| 4 | Password Security | Bcrypt hashing (Laravel default) | ✅ |
| 5 | Role-Based Access | Middleware AdminMiddleware & UserMiddleware | ✅ |
| 6 | Input Validation | Server-side validation semua input | ✅ |
| 7 | File Upload Security | Validasi type, size, rename otomatis | ✅ |
| 8 | Rate Limiting | Throttle middleware untuk endpoint sensitif | ✅ |
| 9 | Session Security | HTTP-only, Secure, SameSite cookies | ✅ |
| 10 | Environment | APP_DEBUG=false, .env protection | ✅ |
| 11 | Logging | Activity & error logging | ✅ |
| 12 | HTTPS | Konfigurasi ready untuk production | ✅ |

---

## **KESIMPULAN**

Keamanan website tidak bisa mengandalkan **SATU** metode saja. Saya menerapkan **DEFENSE IN DEPTH** - 12 lapisan keamanan yang saling melengkapi:

```
Lapisan 1: Input Validation
Lapisan 2: CSRF Protection
Lapisan 3: SQL Injection Prevention
Lapisan 4: XSS Prevention
Lapisan 5: Authentication
Lapisan 6: Authorization (RBAC)
Lapisan 7: File Upload Security
Lapisan 8: Rate Limiting
Lapisan 9: Session Security
Lapisan 10: Environment Security
Lapisan 11: Logging & Monitoring
Lapisan 12: Regular Updates
```

---

**Dibuat oleh:** Muhammad Fakhrizal Garnindyo
**Tanggal:** 8 Juni 2026
**Proyek:** Mini Project IT Support - KPM
