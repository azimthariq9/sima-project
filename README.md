# SIMA — Sistem Informasi Mahasiswa Asing
### Universitas Gunadarma

SIMA (Sistem Informasi Mahasiswa Asing) adalah aplikasi berbasis web yang dibangun dengan framework **Laravel 12** untuk mengelola data dan layanan akademik mahasiswa asing di Universitas Gunadarma. Aplikasi ini mencakup pengelolaan data mahasiswa, dosen, jurusan, kelas, mata kuliah, jadwal perkuliahan, kehadiran, dokumen (imigrasi & kependudukan), pengumuman (announcement), notifikasi, hingga alur login berbasis OTP (One-Time Password) untuk akun tanpa kata sandi.

---

## 1. Daftar Isi

1. [Teknologi yang Digunakan](#2-teknologi-yang-digunakan)
2. [Persyaratan Sistem](#3-persyaratan-sistem)
3. [Struktur Peran Pengguna (Role)](#4-struktur-peran-pengguna-role)
4. [Panduan Instalasi](#5-panduan-instalasi)
5. [Konfigurasi Berkas `.env`](#6-konfigurasi-berkas-env)
6. [Menjalankan Migrasi & Seeder](#7-menjalankan-migrasi--seeder)
7. [Akun Demo Hasil Seeder](#8-akun-demo-hasil-seeder)
8. [Menjalankan Aplikasi](#9-menjalankan-aplikasi)
9. [Perintah Build untuk Produksi](#10-perintah-build-untuk-produksi)
10. [Troubleshooting (Pemecahan Masalah)](#11-troubleshooting-pemecahan-masalah)
11. [Struktur Direktori Penting](#12-struktur-direktori-penting)
12. [Catatan Tambahan](#13-catatan-tambahan)

---

## 2. Teknologi yang Digunakan

| Komponen            | Teknologi / Versi                                   |
|----------------------|-------------------------------------------------------|
| Backend Framework    | Laravel 12 (`laravel/framework: ^12.0`)              |
| Bahasa Pemrograman   | PHP `^8.3.30`                                        |
| Autentikasi          | Laravel Breeze (`laravel/breeze: ^2.3`) + OTP Login  |
| Frontend Build Tool  | Vite `^7.0.7`                                        |
| CSS Framework        | Tailwind CSS `^3.1.0`, Bootstrap `^5.2.3`            |
| Interaktivitas Front | Alpine.js `^3.4.2`, Axios `^1.11.0`                  |
| Basis Data (default) | SQLite (dapat diganti ke PostgreSQL/MySQL)           |
| Notifikasi UI        | php-flasher / @flasher/flasher                       |
| Testing              | Pest PHP `^3.8`                                      |
| Package Manager PHP  | Composer                                             |
| Package Manager JS   | NPM                                                   |

---

## 3. Persyaratan Sistem

Pastikan perangkat/server Anda telah memiliki perangkat lunak berikut sebelum memulai instalasi:

- **PHP** versi **8.3.30 atau lebih tinggi** (di bawah 8.4), dengan ekstensi wajib berikut aktif:
  - `pdo`, `pdo_sqlite` (jika memakai SQLite) atau `pdo_pgsql` (jika memakai PostgreSQL)
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `bcmath`
  - `fileinfo`
  - `curl`
  - `gd` (untuk pemrosesan gambar/dokumen, jika digunakan)
- **Composer** versi 2.x — [https://getcomposer.org](https://getcomposer.org)
- **Node.js** versi 18 LTS atau lebih baru, beserta **NPM**
- **Git** (opsional, untuk kloning repositori)
- Salah satu basis data berikut:
  - **SQLite** (paling mudah, cukup 1 berkas, cocok untuk pengembangan lokal), **atau**
  - **PostgreSQL** versi 13 ke atas (direkomendasikan untuk lingkungan produksi/staging)
- **Web server** (opsional untuk produksi): Nginx atau Apache. Untuk pengembangan lokal, cukup gunakan server bawaan PHP (`php artisan serve`).

---

## 4. Struktur Peran Pengguna (Role)

Aplikasi ini memiliki beberapa peran (role) pengguna yang menentukan hak akses melalui middleware `check.role`:

| Role         | Deskripsi Singkat                                                        |
|--------------|---------------------------------------------------------------------------|
| `kln`        | Kantor Layanan/Kerjasama Internasional — pengelola utama data & pengumuman |
| `dosen`      | Dosen pengampu mata kuliah                                               |
| `mahasiswa`  | Mahasiswa asing (BIPA, kelas TI, SI, dan program KLN lainnya)             |

---

## 5. Panduan Instalasi

Ikuti langkah-langkah berikut **secara berurutan**.

### Langkah 1 — Ekstrak / Kloning Proyek

Jika Anda menerima proyek dalam bentuk berkas `.zip`, ekstrak terlebih dahulu ke direktori kerja Anda, misalnya:

```bash
unzip sima.zip -d sima
cd sima
```

Jika proyek berada di repositori Git:

```bash
git clone <url-repositori-anda> sima
cd sima
```

### Langkah 2 — Instal Dependensi PHP (Composer)

Jalankan perintah berikut dari direktori utama proyek (folder yang berisi `composer.json`):

```bash
composer install
```

Perintah ini akan mengunduh seluruh pustaka PHP yang dibutuhkan (Laravel Framework, Breeze, Pint, Pest, php-flasher, dll.) ke dalam folder `vendor/`.

> **Catatan:** Jika Anda mendapati proyek sudah menyertakan folder `vendor/` bawaan (misalnya hasil kompresi dari komputer sebelumnya), tetap disarankan menjalankan `composer install` ulang di lingkungan baru untuk memastikan kompatibilitas versi PHP dan sistem operasi.

### Langkah 3 — Instal Dependensi JavaScript (NPM)

Masih di direktori yang sama, jalankan:

```bash
npm install
```

Perintah ini akan mengunduh Vite, Tailwind CSS, Bootstrap, Alpine.js, dan paket front-end lainnya ke dalam folder `node_modules/`.

### Langkah 4 — Siapkan Berkas Lingkungan (`.env`)

Salin berkas contoh konfigurasi menjadi berkas `.env` aktif:

```bash
cp .env.example .env
```

- **Windows (Command Prompt):** `copy .env.example .env`
- **Windows (PowerShell):** `Copy-Item .env.example .env`

> **PENTING:** Jika proyek yang Anda terima sudah menyertakan berkas `.env` bawaan dari pengembang sebelumnya, periksa isinya terlebih dahulu. Beberapa distribusi proyek ini memiliki baris konfigurasi yang tertulis **dua kali** (duplikat) di dalam satu berkas `.env`, termasuk `DB_CONNECTION`, `SESSION_DRIVER`, `MAIL_MAILER`, dan variabel lainnya. Baris yang letaknya **paling bawah** akan menimpa (override) baris yang sama di atasnya. Untuk menghindari kebingungan dan kesalahan konfigurasi, **hapus baris-baris yang terduplikasi** sehingga setiap variabel hanya muncul satu kali, lalu sesuaikan nilainya mengikuti panduan pada Bagian 6 di bawah ini.

### Langkah 5 — Generate Application Key

Laravel memerlukan kunci enkripsi unik untuk mengamankan sesi dan data terenkripsi:

```bash
php artisan key:generate
```

Perintah ini akan otomatis mengisi nilai `APP_KEY` pada berkas `.env`.

### Langkah 6 — Konfigurasi Basis Data

Lihat panduan lengkap pada [Bagian 6](#6-konfigurasi-berkas-env) di bawah untuk memilih antara SQLite atau PostgreSQL, lalu lanjutkan ke Langkah 7.

### Langkah 7 — Jalankan Migrasi dan Seeder

Lihat panduan lengkap pada [Bagian 7](#7-menjalankan-migrasi--seeder).

### Langkah 8 — Buat Symbolic Link Storage

Aplikasi ini menyimpan berkas unggahan (dokumen imigrasi, dokumen kependudukan, lampiran pengumuman, dsb.) di `storage/app/public`. Agar berkas tersebut dapat diakses melalui browser, buat tautan simbolik ke folder publik:

```bash
php artisan storage:link
```

Perintah ini akan membuat folder `public/storage` yang menunjuk ke `storage/app/public`.

### Langkah 9 — Build Aset Front-End

Untuk pengembangan (development), jalankan Vite dalam mode watch:

```bash
npm run dev
```

Untuk produksi, kompilasi aset menjadi berkas statis yang teroptimasi:

```bash
npm run build
```

### Langkah 10 — Jalankan Server Aplikasi

Buka terminal baru (biarkan `npm run dev` tetap berjalan jika sedang dalam mode pengembangan), lalu jalankan:

```bash
php artisan serve
```

Aplikasi akan tersedia di:

```
http://127.0.0.1:8000
```

Buka alamat tersebut di peramban (browser) untuk mengakses halaman login SIMA.

---

## 6. Konfigurasi Berkas `.env`

Setelah menyalin `.env.example` menjadi `.env` (lihat Langkah 4), sesuaikan variabel-variabel berikut sesuai lingkungan Anda.

### 6.1 Konfigurasi Umum Aplikasi

```env
APP_NAME="SIMA - Sistem Informasi Mahasiswa Asing"
APP_ENV=local
APP_KEY=          # akan terisi otomatis setelah php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000
```

> Ganti `APP_ENV=local` menjadi `APP_ENV=production` dan `APP_DEBUG=false` saat aplikasi dijalankan di server produksi.

### 6.2 Opsi A — Basis Data SQLite (Paling Mudah, Direkomendasikan untuk Pengembangan Lokal)

Ini adalah konfigurasi **bawaan** (`.env.example`) proyek. Cukup pastikan berkas basis data tersedia:

```env
DB_CONNECTION=sqlite
```

Baris `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` **tidak perlu diisi/diaktifkan** untuk SQLite. Selanjutnya, pastikan berkas basis data fisiknya tersedia:

```bash
# Linux / macOS
touch database/database.sqlite

# Windows (PowerShell)
New-Item database\database.sqlite -ItemType File
```

> Jika proyek sudah menyertakan berkas `database/database.sqlite` bawaan, langkah ini boleh dilewati.

### 6.3 Opsi B — Basis Data PostgreSQL (Direkomendasikan untuk Staging/Produksi)

Jika Anda ingin menggunakan PostgreSQL (sesuai dengan konfigurasi yang pernah digunakan pada proyek ini), buat terlebih dahulu basis data kosong di PostgreSQL:

```sql
CREATE DATABASE sima_db;
```

Kemudian atur variabel berikut pada `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sima_db
DB_USERNAME=postgres
DB_PASSWORD=isi_dengan_kata_sandi_postgresql_anda
```

> **Keamanan:** Jangan pernah menggunakan kata sandi bawaan/contoh dari lingkungan pengembangan lama pada server produksi. Selalu buat kredensial basis data baru dan simpan secara rahasia (jangan diunggah ke repositori publik).

### 6.4 Konfigurasi Sesi, Cache, dan Antrean (Queue)

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Karena driver di atas menggunakan tabel basis data (`sessions`, `cache`, `jobs`), tabel-tabel tersebut akan otomatis tersedia setelah proses migrasi pada Langkah 7 (sudah termasuk dalam migrasi bawaan Laravel).

### 6.5 Konfigurasi Email (Wajib untuk Fitur Login OTP)

Aplikasi ini memiliki fitur **login berbasis OTP** (`app/Http/Controllers/Auth/OtpController.php`) yang mengirimkan kode OTP melalui email untuk akun yang belum memiliki kata sandi. Agar fitur ini berfungsi dengan benar (tidak hanya mencatat log), konfigurasikan SMTP yang valid:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_anda
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email_anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> Untuk pengembangan lokal tanpa perlu mengirim email sungguhan, biarkan `MAIL_MAILER=log`. Kode OTP yang "terkirim" akan dicatat pada berkas `storage/logs/laravel.log` dan dapat dibaca secara manual di sana.

### 6.6 Konfigurasi Vite

```env
VITE_APP_NAME="${APP_NAME}"
```

Tidak perlu diubah kecuali Anda menambahkan variabel lingkungan khusus untuk front-end.

---

## 7. Menjalankan Migrasi & Seeder

### 7.1 Migrasi Struktur Tabel

Setelah basis data terkonfigurasi dengan benar (Bagian 6), jalankan seluruh migrasi untuk membangun struktur tabel:

```bash
php artisan migrate
```

Perintah ini akan membuat seluruh tabel yang dibutuhkan, di antaranya: `users`, `mahasiswa`, `dosen`, `jurusan`, `kelas`, `kelas_mahasiswa`, `matakuliah`, `jadwal`, `jadwal_mahasiswa`, `dokumen`, `dokumen_imigrasi`, `dokumen_kependudukan`, `history_dokumen`, `announcement`, `announcement_files`, `notification`, `log`, `req_dokumen`, `file_detail`, `personal_access_tokens`, beserta tabel bawaan Laravel (`sessions`, `cache`, `jobs`).

> Jika Anda ingin memulai dari basis data yang benar-benar bersih (menghapus seluruh tabel lalu membuatnya ulang), gunakan:
> ```bash
> php artisan migrate:fresh
> ```
> **Perhatian:** perintah ini akan **menghapus seluruh data** yang ada di basis data. Jangan jalankan pada basis data produksi yang sudah berisi data penting.

### 7.2 Mengisi Data Awal (Seeder)

Proyek ini menyediakan dua seeder yang dijalankan secara berurutan melalui `DatabaseSeeder`:

1. `JurusanSeeder` — mengisi data jurusan (KLN, BIPA, dan program studi lainnya).
2. `SimaSeeder` — mengisi data demo: akun KLN, 10 dosen, dan 12 mahasiswa asing beserta relasi kelas dan jadwalnya. Seeder ini bersifat **idempotent** (aman dijalankan berulang kali tanpa menghasilkan data ganda).

Jalankan seluruh seeder sekaligus:

```bash
php artisan db:seed
```

Atau jalankan seeder tertentu saja:

```bash
php artisan db:seed --class=JurusanSeeder
php artisan db:seed --class=SimaSeeder
```

Jika Anda ingin melakukan migrasi ulang **sekaligus** mengisi data demo dalam satu perintah:

```bash
php artisan migrate:fresh --seed
```

---

## 8. Akun Demo Hasil Seeder

Setelah `SimaSeeder` dijalankan, Anda dapat login menggunakan akun-akun demo berikut. **Kata sandi untuk seluruh akun demo adalah `password`.**

| Role         | Contoh Email             | Kata Sandi |
|--------------|---------------------------|------------|
| KLN          | `kln.demo@seed.test`      | `password` |
| Dosen        | `dos.bipa1@seed.test`     | `password` |
| Dosen        | `dos.ti1@seed.test`       | `password` |
| Mahasiswa    | `mhs.bipa1@seed.test`     | `password` |
| Mahasiswa    | `mhs.kln1@seed.test`      | `password` |

> Terdapat total 10 akun dosen (`dos.bipa1–3`, `dos.ti1–3`, `dos.si1–2`, `dos.kln1–2`) dan 12 akun mahasiswa asing (`mhs.bipa1–4`, `mhs.ti1–3`, `mhs.si1–2`, `mhs.kln1–3`) dengan pola email dan kata sandi yang sama seperti contoh di atas.

> **Wajib untuk produksi:** Hapus atau nonaktifkan seluruh akun demo di atas sebelum aplikasi digunakan secara nyata, dan jangan pernah menjalankan `SimaSeeder` pada basis data produksi.

---

## 9. Menjalankan Aplikasi

### 9.1 Mode Pengembangan (Development) — Cara Cepat

Proyek ini menyediakan satu perintah gabungan yang menjalankan server PHP, worker antrean (queue), pencatat log (Pail), dan Vite secara bersamaan:

```bash
composer run dev
```

Perintah ini setara dengan menjalankan empat proses berikut secara paralel:
- `php artisan serve` (server aplikasi)
- `php artisan queue:listen --tries=1 --timeout=0` (pemrosesan antrean/queue)
- `php artisan pail --timeout=0` (pemantauan log secara langsung)
- `npm run dev` (kompilasi aset front-end secara langsung/hot-reload)

### 9.2 Mode Pengembangan — Cara Manual (Terpisah per Terminal)

Jika lebih nyaman menjalankan tiap proses secara terpisah, buka beberapa jendela terminal:

```bash
# Terminal 1 — server aplikasi
php artisan serve

# Terminal 2 — kompilasi aset front-end (mode watch)
npm run dev

# Terminal 3 — pemrosesan antrean (wajib jika ada fitur pengiriman email/notifikasi)
php artisan queue:listen --tries=1 --timeout=0
```

Setelah semua proses berjalan, akses aplikasi melalui:

```
http://127.0.0.1:8000
```

---

## 10. Perintah Build untuk Produksi

Sebelum menempatkan aplikasi ke server produksi, lakukan langkah-langkah berikut secara berurutan:

```bash
# 1. Instal dependensi PHP tanpa paket pengembangan (development)
composer install --optimize-autoloader --no-dev

# 2. Instal dependensi JS
npm install

# 3. Build aset front-end final (menghasilkan folder public/build)
npm run build

# 4. Cache konfigurasi, route, dan view agar performa lebih optimal
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Jalankan migrasi (tanpa seeder demo!)
php artisan migrate --force
```

> Perintah `--force` diperlukan agar migrasi tetap berjalan meskipun `APP_ENV=production`, karena secara default Laravel akan meminta konfirmasi interaktif pada lingkungan produksi.

> Setelah melakukan perubahan pada berkas `.env` di lingkungan produksi yang sudah menjalankan `config:cache`, jalankan `php artisan config:clear` lalu `php artisan config:cache` ulang agar perubahan terbaca.

---

## 11. Troubleshooting (Pemecahan Masalah)

| Masalah | Kemungkinan Penyebab & Solusi |
|---|---|
| `SQLSTATE[HY000] [2002] Connection refused` | Layanan PostgreSQL/MySQL belum berjalan, atau `DB_HOST`/`DB_PORT` pada `.env` salah. Pastikan layanan basis data aktif dan port sesuai. |
| `could not find driver` | Ekstensi PHP untuk basis data belum aktif. Aktifkan `pdo_pgsql` (PostgreSQL) atau `pdo_sqlite` (SQLite) di berkas `php.ini`, lalu restart server. |
| Halaman blank/putih tanpa pesan error | Pastikan `APP_DEBUG=true` saat development untuk melihat pesan error, dan periksa `storage/logs/laravel.log`. |
| `The stream or file "storage/logs/laravel.log" could not be opened` | Folder `storage/` dan `bootstrap/cache/` belum memiliki izin tulis. Jalankan `chmod -R 775 storage bootstrap/cache` (Linux/macOS) dan pastikan kepemilikan folder sesuai dengan pengguna web server. |
| Gambar/dokumen unggahan tidak tampil (404) | Symbolic link belum dibuat. Jalankan `php artisan storage:link`. |
| Tampilan CSS/JS tidak berubah setelah edit | Aset belum di-build ulang. Jalankan `npm run dev` (development) atau `npm run build` (produksi), lalu hapus cache browser (hard refresh: `Ctrl+Shift+R`). |
| Error `Vite manifest not found` | Belum menjalankan `npm run build` (untuk produksi) atau `npm run dev` belum aktif (untuk development). |
| `Class "X" not found` setelah instalasi | Autoload Composer belum diperbarui. Jalankan `composer dump-autoload`. |
| Perubahan `.env` tidak berpengaruh | Konfigurasi sudah ter-cache. Jalankan `php artisan config:clear`. Periksa juga apakah ada baris duplikat pada `.env` (lihat catatan pada Langkah 4). |
| Kode OTP tidak diterima via email | Periksa konfigurasi SMTP pada Bagian 6.5. Jika `MAIL_MAILER=log`, cek isi kode OTP secara manual pada `storage/logs/laravel.log`. |
| `composer install` gagal karena versi PHP | Pastikan versi PHP aktif adalah **8.3.30 ke atas** dan **di bawah 8.4**. Cek dengan `php -v`. |
| Error terkait sertifikat SSL saat `composer install` di Windows | Unduh berkas `cacert.pem` terbaru, lalu tambahkan `curl.cainfo = "C:\path\to\cacert.pem"` pada `php.ini`. |

---

## 12. Struktur Direktori Penting

```
sima/
├── app/
│   ├── Http/Controllers/       # Controller web & API (Auth, Mahasiswa, Dosen, Jadwal, dsb.)
│   ├── Http/Middleware/        # RoleMiddleware (check.role), EnsureProfileCompleted
│   ├── Models/                 # Eloquent model (User, Mahasiswa, Dosen, Jadwal, dsb.)
│   ├── Mail/                   # Mailable, termasuk OtpLoginMail
│   └── Services/                # Kelas layanan/logic bisnis
├── config/                     # Berkas konfigurasi (app, database, mail, filesystems, dsb.)
├── database/
│   ├── migrations/              # Seluruh skema tabel
│   ├── seeders/                 # JurusanSeeder, SimaSeeder, DatabaseSeeder
│   └── database.sqlite          # Berkas basis data SQLite (jika memakai opsi A)
├── public/                      # Titik masuk aplikasi (index.php) & aset hasil build
├── resources/                   # View Blade, CSS, JS sumber (sebelum di-build Vite)
├── routes/
│   ├── web.php                  # Rute halaman web (termasuk login, OTP, dashboard)
│   ├── api.php                  # Rute API
│   └── auth.php                 # Rute autentikasi bawaan Breeze
├── storage/                     # Log, cache, berkas unggahan (app/public)
├── .env                         # Konfigurasi lingkungan (tidak diunggah ke Git)
├── .env.example                 # Contoh/template konfigurasi lingkungan
├── composer.json                # Daftar dependensi PHP
├── package.json                 # Daftar dependensi JavaScript
└── vite.config.js               # Konfigurasi build Vite + Tailwind
```

---

## 13. Catatan Tambahan

- **Autentikasi ganda:** Aplikasi mendukung dua jalur login — login dengan kata sandi biasa (`AuthenticatedSessionController`) dan login berbasis OTP email (`OtpController`) untuk akun yang kolom `is_has_password`-nya bernilai `false`.
- **Berkas `SIMA-Dashboard-Final.html`** yang terdapat di direktori utama proyek merupakan berkas rancangan tampilan (mockup) statis dan **bukan bagian dari alur aplikasi Laravel yang berjalan**; berkas ini dapat dijadikan referensi desain (palet warna navy-gold, tipografi Fraunces & DM Sans) namun tidak perlu dijalankan atau di-deploy.
- **Perbedaan lingkungan:** Konfigurasi bawaan proyek (`.env.example`) menggunakan SQLite agar mudah dijalankan siapa saja tanpa instalasi server basis data tambahan. Jika sebelumnya proyek pernah dijalankan dengan PostgreSQL, sesuaikan kembali sesuai Bagian 6.3 saat memindahkan proyek ke lingkungan baru.
- **Jangan mengunggah berkas `.env` ke repositori publik (GitHub, dsb.)** karena berkas ini dapat memuat kredensial basis data dan kunci aplikasi yang bersifat rahasia. Berkas ini sudah terdaftar pada `.gitignore` secara bawaan.
- Untuk pertanyaan lebih lanjut terkait arsitektur Laravel, silakan merujuk ke dokumentasi resmi: [https://laravel.com/docs/12.x](https://laravel.com/docs/12.x)

---

<p align="center"><i>Dibuat untuk memudahkan proses instalasi dan pengembangan SIMA — Sistem Informasi Mahasiswa Asing, Universitas Gunadarma.</i></p>