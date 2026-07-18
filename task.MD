# TASK.md - Sistem Manajemen Kendaraan Perumahan

## Context

Dokumen ini mendefinisikan urutan pembuatan sistem secara bertahap.  
Tujuannya agar pengembangan dilakukan secara terstruktur dan tidak sekaligus.

Setiap task harus diselesaikan dan dicek sebelum lanjut ke task berikutnya.

Selalu merujuk ke:
- `PRD.md`
- `GUIDELINE.md`

sebelum mengerjakan task.

## Build Rules

- Ikuti PRD dan Guideline dengan ketat
- Jangan menambahkan fitur di luar scope
- Kerjakan secara sequential
- Gunakan PHP + MySQL
- Admin Panel wajib menggunakan **Soft UI Dashboard 3**

---

## Phase 1 — Project Foundation

### Task 1.1: Setup Project Structure
- Buat folder utama project
- Buat folder `config`, `user`, `admin`, `assets`
- Buat file `PRD.md`, `GUIDELINE.md`, `TASK.md`
- Setup `config/database.php`

**Success Criteria:** Struktur folder rapi dan terorganisir.

### Task 1.2: Database & Koneksi
- Import SQL database (sudah ada)
- Buat `functions.php`
- Test koneksi database

**Success Criteria:** Koneksi ke database berhasil.

---

## Phase 2 — Authentication

### Task 2.1: Registrasi Penghuni
- Buat `register.php`
- Validasi input (nama, email, password)
- Simpan data ke tabel `users`

### Task 2.2: Login Penghuni
- Buat `login.php`
- Session management
- Redirect ke dashboard user

### Task 2.3: Login Admin
- Buat `admin/login.php`
- Gunakan desain dari Soft UI Dashboard
- Session admin terpisah

### Task 2.4: Logout
- Buat fungsi logout untuk user & admin

**Success Criteria:** Sistem login & registrasi berjalan lancar.

---

## Phase 3 — User Panel (Penghuni)

### Task 3.1: Dashboard User
- Tampilkan informasi user
- Tampilkan ringkasan jumlah kendaraan

### Task 3.2: CRUD Kendaraan
- Form tambah kendaraan (`tambah-kendaraan.php`)
- Generate QR Code otomatis
- Halaman daftar kendaraan beserta QR Code
- Fitur Edit & Hapus kendaraan

**Success Criteria:** User dapat mendaftarkan banyak kendaraan dan melihat QR Code.

---

## Phase 4 — Admin Panel (Soft UI Dashboard 3)

### Task 4.1: Integrasi Soft UI Dashboard
- Pasang template Soft UI ke folder `admin/`
- Setup halaman login admin

### Task 4.2: Dashboard Admin
- Overview (jumlah penghuni, total kendaraan, dll)
- Statistik sederhana

### Task 4.3: Manajemen Penghuni
- CRUD data users
- Daftar semua penghuni

### Task 4.4: Manajemen Kendaraan
- Lihat semua kendaraan
- Filter berdasarkan penghuni / jenis
- Tampilkan QR Code

**Success Criteria:** Admin dapat mengelola semua data dengan nyaman melalui Soft UI Dashboard.

---

## Phase 5 — Enhancement & Polish

### Task 5.1: Security & Validation
- Input sanitization
- Password hashing
- Proteksi halaman (session check)

### Task 5.2: Responsiveness
- Pastikan mobile friendly
- User Panel & Admin Panel responsif

### Task 5.3: QR Code Improvement
- Tampilan QR Code yang lebih baik
- Fitur download QR Code

### Task 5.4: Final Testing
- Test alur lengkap user & admin
- Perbaikan bug

---

## Definition of Done (DoD)

Proyek dianggap selesai apabila:

- Database berjalan dengan baik
- Fitur registrasi & login berfungsi
- User dapat CRUD kendaraan + QR Code
- Admin Panel menggunakan Soft UI Dashboard 3 dan berfungsi
- Tidak ada error koneksi database
- Interface bersih, rapi, dan user-friendly
- Mengikuti semua guideline yang ada