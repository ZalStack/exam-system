Saya akan lengkapi **README.md** dengan panduan instalasi lengkap untuk **Linux Fedora** dan **Windows**.

---

## **📚 Updated README.md**

```markdown
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
| MySQL | 8.4.9+ | Database |
| TailwindCSS | 3.4.0 | CSS Framework |
| MathJax | 3.x | Render formula matematika |
| SweetAlert2 | 11.x | Alert/Notifikasi |
| Vite | 5.x | Build tool |

## Persyaratan Sistem

- PHP >= 8.4 +
- Composer >= 2.9.4
- MySQL >= 8.4.9 
- Node.js >= 22.22.2 (versi LTS)
- NPM >= 10.9.7 (versi LTS)

---

## 🐧 Instalasi di Linux Fedora

### Langkah 1: Install PHP 8.4

```bash
# Update system
sudo dnf update -y

# Install PHP 8.4 dan ekstensi yang diperlukan
sudo dnf install -y php php-cli php-common php-fpm php-mysqlnd php-pdo php-xml php-json php-mbstring php-zip php-gd php-curl php-bcmath php-tokenizer

# Cek versi PHP
php -v
```

### Langkah 2: Install Composer

```bash
# Download Composer installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Install Composer
php composer-setup.php

# Move to global directory
sudo mv composer.phar /usr/local/bin/composer

# Set permission
sudo chmod +x /usr/local/bin/composer

# Remove installer
php -r "unlink('composer-setup.php');"

# Cek Composer
composer --version
```

### Langkah 3: Install MySQL 8.4

```bash
# Install MySQL server
sudo dnf install -y mysql-server mysql

# Start MySQL service
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Secure MySQL installation (set root password)
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Create database untuk project
CREATE DATABASE exam_system;
EXIT;
```

### Langkah 4: Install Node.js & NPM

```bash
# Install Node.js 22.x LTS
curl -fsSL https://rpm.nodesource.com/setup_22.x | sudo bash -
sudo dnf install -y nodejs

# Cek versi
node -v
npm -v
```

### Langkah 5: Install Git

```bash
sudo dnf install -y git
git --version
```

### Langkah 6: Clone dan Setup Project

```bash
# Clone repository
git clone https://github.com/ZalStack/exam-system.git
cd exam-system

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Edit .env file (gunakan nano atau vim)
nano .env
```

### Langkah 7: Konfigurasi .env di Linux Fedora

```env
APP_NAME="Exam System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=root
DB_PASSWORD=password_anda

# Session Configuration
SESSION_DRIVER=database

# Cache Configuration
CACHE_STORE=database
```

**Save file**: Tekan `Ctrl+O`, Enter, lalu `Ctrl+X`

### Langkah 8: Generate Key dan Setup Database

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Run seeder untuk akun default
php artisan db:seed --class=AdminUserSeeder

# Create storage link
php artisan storage:link
```

### Langkah 9: Build Assets dan Jalankan

```bash
# Build assets
npm run build

# Jalankan server
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

---

## 🪟 Instalasi di Windows 10/11

### Langkah 1: Install PHP 8.4

1. Download PHP 8.4 dari: https://windows.php.net/download/
2. Pilih **"VS16 x64 Non Thread Safe"**
3. Extract ke `C:\php`
4. Rename `php.ini-development` menjadi `php.ini`
5. Buka `php.ini` dan uncomment (hapus `;`) ekstensi berikut:
   ```ini
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=mysqli
   extension=pdo_mysql
   extension=zip
   extension=openssl
   ```
6. Tambahkan `C:\php` ke **Environment Variables**:
   - Right-click **This PC** → Properties → Advanced System Settings
   - Environment Variables → System Variables → Path → Edit
   - Add: `C:\php`
   - Click OK

7. Cek di Command Prompt:
   ```cmd
   php -v
   ```

### Langkah 2: Install Composer

1. Download Composer dari: https://getcomposer.org/Composer-Setup.exe
2. Jalankan installer dan pilih `C:\php\php.exe`
3. Selesai instalasi, cek:
   ```cmd
   composer --version
   ```

### Langkah 3: Install MySQL 8.4

**Opsi A - Menggunakan XAMPP (Lebih Mudah):**
1. Download XAMPP dari: https://www.apachefriends.org/
2. Install XAMPP di `C:\xampp`
3. Jalankan **XAMPP Control Panel**
4. Start **MySQL** service

**Opsi B - Install MySQL Standalone:**
1. Download MySQL Installer dari: https://dev.mysql.com/downloads/installer/
2. Pilih **"MySQL Server"** dan **"MySQL Workbench"**
3. Set root password
4. Buka Command Prompt:
   ```cmd
   mysql -u root -p
   CREATE DATABASE exam_system;
   EXIT;
   ```

### Langkah 4: Install Node.js & NPM

1. Download Node.js LTS dari: https://nodejs.org/
2. Jalankan installer (pilih default)
3. Cek instalasi:
   ```cmd
   node -v
   npm -v
   ```

### Langkah 5: Install Git

1. Download Git dari: https://git-scm.com/download/win
2. Jalankan installer (pilih default)
3. Cek instalasi:
   ```cmd
   git --version
   ```

### Langkah 6: Clone dan Setup Project

```cmd
# Clone repository
git clone https://github.com/ZalStack/exam-system.git
cd exam-system

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
copy .env.example .env
```

### Langkah 7: Konfigurasi .env di Windows

Buka file `.env` dengan **Notepad** atau **Visual Studio Code**:

```env
APP_NAME="Exam System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=root
DB_PASSWORD=          # Kosongkan jika pakai XAMPP, isi password jika pakai MySQL standalone

# Session Configuration
SESSION_DRIVER=database

# Cache Configuration
CACHE_STORE=database
```

**Catatan untuk XAMPP:**
- Username: `root`
- Password: ` ` (kosong)

### Langkah 8: Generate Key dan Setup Database

```cmd
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Run seeder
php artisan db:seed --class=AdminUserSeeder

# Create storage link
php artisan storage:link
```

### Langkah 9: Build Assets dan Jalankan

```cmd
# Build assets
npm run build

# Jalankan server
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

---

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@example.com | password |
| **User** | user@example.com | password |

---

## 🚀 Perintah Cepat (Setelah Instalasi)

### Di Linux Fedora
```bash
# Masuk ke folder project
cd exam-system

# Jalankan server
php artisan serve

# Build ulang assets jika ada perubahan CSS/JS
npm run build
```

### Di Windows (Command Prompt)
```cmd
cd exam-system
php artisan serve
npm run build
```

---

## 🔧 Troubleshooting

### 1. Error "Class 'PDO' not found" (Linux)
```bash
sudo dnf install php-pdo php-mysqlnd
sudo systemctl restart php-fpm
```

### 2. Error "Connection refused" MySQL (Linux)
```bash
sudo systemctl start mysqld
sudo systemctl enable mysqld
```

### 3. MySQL tidak bisa connect (Windows XAMPP)
- Jalankan **XAMPP Control Panel**
- Klik **Start** pada MySQL
- Pastikan port 3306 tidak digunakan aplikasi lain

### 4. Error "npm command not found" (Linux)
```bash
sudo dnf install nodejs npm
```

### 5. Permission denied storage (Linux)
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache
```

### 6. Vite manifest not found
```bash
npm install
npm run build
```

### 7. Error migration "Unknown database"
```bash
# Login ke MySQL
mysql -u root -p

# Buat database
CREATE DATABASE exam_system;
EXIT;

# Jalankan ulang migration
php artisan migrate
```

---

## 📁 Struktur Database

| Table | Description |
|-------|-------------|
| `users` | Data user (admin/user) |
| `exams` | Data ujian |
| `questions` | Data soal per ujian |
| `exam_user` | Pivot table untuk registrasi, jawaban, skor |

---

## 🔒 Keamanan yang Diimplementasikan

- ✅ **CSRF Protection** - Semua form dilindungi
- ✅ **XSS Prevention** - Output escaping dengan Blade
- ✅ **SQL Injection Prevention** - Eloquent ORM dengan parameter binding
- ✅ **Role-Based Access** - Pemisahan Admin/User
- ✅ **Authentication** - Laravel Breeze
- ✅ **File Upload Security** - Validasi tipe dan ukuran file

---
