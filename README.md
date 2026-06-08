# Sistem Ujian Online

Aplikasi ujian online berbasis web yang dibangun dengan **Laravel 13**, **TailwindCSS**, dan **MySQL**.

## Fitur

**Admin**
- Membuat dan mengelola ujian (judul, durasi, jumlah soal)
- Menambahkan soal pilihan ganda dengan dukungan LaTeX dan upload gambar
- Memberikan waktu tambahan kepada peserta yang sedang mengerjakan ujian

**User**
- Mendaftar dan mengerjakan ujian
- Timer hitung mundur, navigasi soal, dan auto-save jawaban
- Melihat hasil ujian beserta review jawaban per soal

## Teknologi

| Teknologi | Versi |
|-----------|-------|
| PHP | 8.4+ |
| Laravel | 13 |
| MySQL | 8.4+ |
| Node.js | 22 LTS |
| TailwindCSS | 3.4 |

---

## Instalasi

### Persyaratan
- PHP >= 8.4
- Composer >= 2.x
- MySQL >= 8.4
- Node.js >= 22 LTS

---

### Linux (Fedora)

**1. Install dependensi sistem**
```bash
sudo dnf update -y
sudo dnf install -y php php-cli php-mysqlnd php-pdo php-xml php-mbstring php-zip php-gd php-curl php-bcmath
sudo dnf install -y mysql-server mysql git

# Node.js 
sudo dnf install -y nodejs
```

**2. Install Composer**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
php -r "unlink('composer-setup.php');"
```

**3. Setup MySQL**
```bash
sudo systemctl start mysqld && sudo systemctl enable mysqld
sudo mysql_secure_installation

# Buat database
sudo mysql -u root -p -e "CREATE DATABASE exam_system;"
```

**4. Clone dan setup project**
```bash
git clone https://github.com/ZalStack/exam-system.git
cd exam-system

composer install
npm install
cp .env.example .env
```

**5. Konfigurasi `.env`**
```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=root
DB_PASSWORD=password_anda

SESSION_DRIVER=database
CACHE_STORE=database
```

**6. Jalankan aplikasi**
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link
npm run build
php artisan serve
```

---

### Windows

**1. Install PHP 8.4**
- Download dari https://windows.php.net/download/ (VS16 x64 Non Thread Safe)
- Extract ke `C:\php` dan tambahkan ke PATH
- Rename `php.ini-development` → `php.ini`, lalu uncomment ekstensi: `curl`, `gd`, `mbstring`, `mysqli`, `pdo_mysql`, `zip`, `openssl`

**2. Install Composer**
- Download dan jalankan installer dari https://getcomposer.org/Composer-Setup.exe

**3. Install MySQL**
- Gunakan **XAMPP** (https://www.apachefriends.org/) — lebih mudah
- Atau **MySQL Standalone** dari https://dev.mysql.com/downloads/installer/
- Buat database `exam_system`

**4. Install Node.js**
- Download LTS dari https://nodejs.org/

**5. Clone dan setup project**
```cmd
git clone https://github.com/ZalStack/exam-system.git
cd exam-system

composer install
npm install
copy .env.example .env
```

**6. Konfigurasi `.env`**

Buka `.env` dengan Notepad/VSCode:
```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exam_system
DB_USERNAME=root
DB_PASSWORD=        # Kosong jika pakai XAMPP

SESSION_DRIVER=database
CACHE_STORE=database
```

**7. Jalankan aplikasi**
```cmd
php artisan key:generate
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link
npm run build
php artisan serve
```

Akses di **http://localhost:8000**

---

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| User | user@example.com | password |

---

## Troubleshooting

| Error | Solusi |
|-------|--------|
| `Class 'PDO' not found` (Linux) | `sudo dnf install php-pdo php-mysqlnd` |
| MySQL connection refused (Linux) | `sudo systemctl start mysqld` |
| MySQL tidak connect (Windows XAMPP) | Pastikan MySQL sudah di-Start di XAMPP Control Panel |
| Permission denied storage (Linux) | `sudo chmod -R 775 storage bootstrap/cache` |
| Vite manifest not found | `npm install && npm run build` |
| Unknown database | Buat database terlebih dahulu: `CREATE DATABASE exam_system;` |
