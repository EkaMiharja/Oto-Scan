# Sistem Manajemen Kendaraan Perumahan

Aplikasi web berbasis PHP native untuk mendata kendaraan warga perumahan. Setiap kendaraan yang terdaftar akan memiliki **QR Code** unik yang dapat dipindai untuk menampilkan informasi kendaraan dan pemilik.

## Fitur

### Panel Warga
- **Dashboard** — ringkasan jumlah kendaraan (total, mobil, motor) + 5 kendaraan terbaru
- **CRUD Kendaraan** — tambah, edit, hapus kendaraan milik sendiri
- **QR Code** — otomatis tergenerate saat register kendaraan, bisa di-download
- **Scan Publik** — halaman `/kendaraan/info.php?token=XXX` bisa diakses tanpa login

### Panel Admin (Soft UI Dashboard 3)
- **Dashboard** — 4 kartu statistik gradien (Total Warga, Total Kendaraan, Mobil, Motor) + grafik pertumbuhan bulanan
- **Chart.js** — grafik batang (registrasi kendaraan) dan garis (warga baru) 6 bulan
- **Manajemen Warga** — daftar warga, hapus (dicegah jika masih punya kendaraan)
- **Manajemen Kendaraan** — daftar semua kendaraan, filter berdasarkan warga, tampilkan QR
- **Export Data** — DataTables dengan tombol export Excel & PDF

### Keamanan
- PDO prepared statements (SQL injection)
- Input sanitization (`strip_tags`, `htmlspecialchars`)
- Bcrypt password hashing
- Session-based auth dual-role (warga & admin)
- Verifikasi kepemilikan sebelum edit/hapus kendaraan

## Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | PHP (native, tanpa framework) |
| Database | MySQL via PDO |
| Panel Warga | Bootstrap 5, Font Awesome 6 |
| Panel Admin | Soft UI Dashboard 3 (Creative Tim) |
| QR Code | [goqr.me/api](https://goqr.me/api/) |
| Chart | Chart.js |
| DataTables | jQuery DataTables + export buttons |

## Struktur Database

### `users` — Data warga
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT (PK) | Auto increment |
| nama_lengkap | VARCHAR(150) | Nama lengkap |
| email | VARCHAR(100) | UNIQUE |
| password | VARCHAR(255) | Bcrypt hash |
| tanggal_daftar | DATE | Tanggal daftar |

### `vehicles` — Data kendaraan
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT (PK) | Auto increment |
| user_id | INT (FK) | Relasi ke users(id) ON DELETE CASCADE |
| nomor_plat | VARCHAR(20) | UNIQUE |
| jenis | ENUM('Mobil','Motor') | Jenis kendaraan |
| merek_model | VARCHAR(100) | Merek & model |
| qr_code_path | VARCHAR(255) | URL QR Code |
| scan_token | VARCHAR(64) | Token unik untuk scan publik |

### `admins` — Data administrator
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT (PK) | Auto increment |
| nama | VARCHAR(100) | Nama admin |
| username | VARCHAR(50) | UNIQUE |
| password | VARCHAR(255) | Bcrypt hash |

Default admin: `username: admin`, `password: admin123`

## Instalasi

1. Clone repositori ke folder web server (XAMPP/Laragon/etc)
2. Import `sql_cereate.MD` ke database MySQL
3. Sesuaikan koneksi database di `config/database.php`
4. Akses via browser:
   - **Halaman utama:** `http://localhost/sistem-kendaraan/`
   - **Login admin:** `http://localhost/sistem-kendaraan/admin/login.php`

## Endpoint

| URL | Auth | Fungsi |
|---|---|---|
| `/` | - | Landing page / redirect |
| `/login.php` | - | Login warga |
| `/register.php` | - | Registrasi warga |
| `/user/dashboard.php` | Warga | Dashboard warga |
| `/user/kendaraan.php` | Warga | Daftar kendaraan |
| `/user/tambah-kendaraan.php` | Warga | Tambah kendaraan |
| `/user/edit-kendaraan.php` | Warga | Edit kendaraan |
| `/user/hapus-kendaraan.php` | Warga | Hapus kendaraan |
| `/admin/login.php` | - | Login admin |
| `/admin/index.php` | Admin | Dashboard admin |
| `/admin/penghuni.php` | Admin | Manajemen warga |
| `/admin/kendaraan.php` | Admin | Manajemen kendaraan |
| `/kendaraan/info.php?token=` | Publik | Info kendaraan via QR scan |
