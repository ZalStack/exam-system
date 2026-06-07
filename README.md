# 📚 Sistem Ujian Online (Online Examination System)

## Deskripsi Proyek

Sistem ujian online lengkap yang dibangun dengan **Laravel 13**, **TailwindCSS**, dan **MySQL**. Aplikasi ini memungkinkan admin untuk membuat dan mengelola ujian, serta memungkinkan pengguna untuk mendaftar, mengerjakan ujian, dan melihat hasil ujian.

## Fitur Lengkap

### 👑 Fitur Admin
- ✅ **Membuat Ujian** - Admin dapat membuat ujian baru dengan mengatur judul, deskripsi, durasi, dan jumlah soal
- ✅ **Menambahkan Soal** - Admin dapat menambahkan soal dengan:
  - Teks soal (mendukung formula matematika LaTeX: $E=mc^2$)
  - Upload gambar untuk diagram/ilustrasi
  - Pilihan ganda (A, B, C, D)
  - Sistem poin per soal
- ✅ **Manajemen Waktu Tambahan** - Admin dapat memberikan waktu tambahan (1-60 menit) kepada user yang sedang mengerjakan ujian

### 👤 Fitur User
- ✅ **Daftar Ujian** - User dapat melihat dan mendaftar ke ujian yang tersedia
- ✅ **Mengerjakan Ujian** - Fitur lengkap pengerjaan ujian:
  - Navigasi soal (Next/Previous dan klik langsung nomor soal)
  - Timer hitung mundur dengan peringatan
  - Auto-save jawaban (tetap tersimpan meskipun device mati/browser tertutup)
  - Progress bar menunjukkan jumlah soal terjawab
  - Mendukung formula matematika (LaTeX)
- ✅ **Review Hasil Ujian** - User dapat melihat:
  - Skor akhir dalam persentase
  - Waktu pengerjaan
  - Review jawaban per soal (mana yang benar/salah)

## Teknologi yang Digunakan

| Teknologi | Versi | Keterangan |
|-----------|-------|-------------|
| Laravel | 13.0.0 | Framework PHP |
| PHP | 8.4.21 | Bahasa pemrograman |
| MySQL | 5.7+ | Database |
| TailwindCSS | 3.4.0 | CSS Framework |
| MathJax | 3.x | Render formula matematika |
| SweetAlert2 | 11.x | Alert/Notifikasi |
| Vite | 5.x | Build tool |

## Persyaratan Sistem

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 5.7 atau MariaDB >= 10.2
- Node.js >= 18.x
- NPM >= 9.x

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/ZalStack/exam-system.git
cd exam-system
composer install 
npm install 
npm run build 
php artisan migrate 
php artisan db:seed
php artisan storage:link
composer run dev 
