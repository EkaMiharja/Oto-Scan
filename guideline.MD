# GUIDELINE.md - Sistem Manajemen Kendaraan Perumahan

## 1. Informasi Umum Proyek

**Nama Proyek**          : Sistem Manajemen Kendaraan Perumahan  
**Tujuan**               : Mengelola data penghuni dan kendaraan secara digital dengan QR Code  
**Teknologi**            : PHP + MySQL  
**Admin Template**       : Soft UI Dashboard 3 (Creative Tim)  
**Database**             : `perumahan_kendaraan` (sudah dibuat)  
**Status Database**      : 3 tabel utama (`users`, `vehicles`, `admins`)

---

## 2. Design Philosophy

Sistem ini harus terasa **profesional, aman, dan mudah digunakan** oleh penghuni maupun pengelola perumahan.

Prioritas utama:
- Kejelasan informasi
- Kemudahan penggunaan
- Keamanan data
- Konsistensi tampilan
- Kecepatan akses

Interface harus memberikan kesan:
- Terpercaya
- Rapi
- Modern
- Profesional
- User-friendly

---

## 3. Color System

### Primary Color
- **Biru Tua / Navy** → `#1A365D` (keamanan & kepercayaan)

### Secondary Color
- **Hijau** → `#10B981` (sukses, QR Code aktif)

### Accent Colors
- Success: `#10B981`
- Warning: `#F59E0B`
- Danger: `#EF4444`
- Info: `#3B82F6`

### Neutral Colors
- Background: `#F8FAFC`
- Card/Surface: `#FFFFFF`
- Text Dark: `#1E2937`
- Text Muted: `#64748B`
- Border: `#E2E8F0`

---

## 4. Typography

**Font Utama**: Inter (atau sistem sans-serif)

**Hierarchy**:
- **Heading 1** → Judul Halaman (Dashboard)
- **Heading 2** → Nama Section
- **Body** → Teks utama
- **Caption** → Keterangan kecil, tanggal, status

---

## 5. Layout & Components

- **Admin Panel**: Menggunakan **Soft UI Dashboard 3** sepenuhnya
- **User Panel**: Desain sederhana, clean, dan responsif (Bootstrap 5)
- Sidebar navigasi pada admin (persistent)
- Card-based layout untuk daftar kendaraan
- QR Code berukuran sedang dan jelas

---

## 6. User Interface Rules

### QR Code
- Harus jelas dan mudah discan
- Ditampilkan bersama nomor plat
- Bisa di-download

### Tabel Kendaraan
- Kolom: No, Nomor Plat, Jenis, Merek & Model, Tanggal Daftar, QR Code, Aksi

### Status
- Gunakan badge: `Aktif`, `Dihapus`, dll.

### Form Input
- Label jelas
- Validasi sederhana
- Tombol utama berwarna biru navy

---

## 7. User Flows Utama

**Penghuni**:
Registrasi → Login → Dashboard → Tambah Kendaraan → Generate QR Code → Lihat Daftar Kendaraan

**Admin**:
Login → Dashboard Overview → Kelola Penghuni → Kelola Kendaraan → Lihat Semua QR Code

---

## 8. Development Guidelines

### Folder Structure
/sistem-kendaraan/
├── index.php
├── register.php
├── login.php
├── functions.php
├── config/
│   └── database.php
├── user/
├── admin/                  ← Soft UI Dashboard 3
├── assets/
├── qrcodes/                ← Folder untuk simpan gambar QR (jika pakai library lokal)

### Coding Rules
- Gunakan PHP PDO untuk database
- Gunakan prepared statement (keamanan)
- Session untuk autentikasi
- Nama file dan folder lowercase dengan underscore
- Comment kode penting

### Security
- Password di-hash (`password_hash`)
- Gunakan `htmlspecialchars` saat menampilkan data
- Validasi input
- Pisahkan folder admin dan user

---

## 9. Future Improvement

- Fitur scan QR Code oleh security
- Export data ke Excel
- Notifikasi email
- Riwayat perubahan data
- Multi-role admin