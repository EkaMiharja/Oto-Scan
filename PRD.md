# PRD: Sistem Manajemen Kendaraan Perumahan

## Product Overview

Sistem Manajemen Kendaraan Perumahan adalah sebuah website berbasis web yang digunakan untuk mengelola data penghuni dan kendaraan di dalam lingkungan perumahan secara digital.

Sistem ini memungkinkan penghuni mendaftarkan diri dan kendaraannya, serta menghasilkan QR Code untuk setiap kendaraan. Admin perumahan dapat mengelola semua data melalui Admin Panel yang elegan menggunakan template **Soft UI Dashboard 3**.

Tujuan utama sistem ini adalah menciptakan database kendaraan yang terpusat, memudahkan proses identifikasi kendaraan, dan meningkatkan keamanan serta ketertiban di lingkungan perumahan.

## Problem Statement

Banyak perumahan masih menggunakan cara manual (buku tulis, Excel, atau catatan) untuk mencatat data kendaraan penghuni. Hal ini menyebabkan:

- Kesulitan mencari data kendaraan saat dibutuhkan
- Tidak adanya QR Code untuk identifikasi cepat
- Proses pendataan yang berulang dan rawan kesalahan
- Admin kesulitan mengawasi dan melaporkan data kendaraan
- Tidak ada pemisahan akses yang jelas antara penghuni dan pengelola perumahan

## Product Goals

Sistem ini dirancang untuk membantu:

- Penghuni dapat mendaftarkan kendaraannya dengan mudah
- Setiap kendaraan memiliki QR Code sendiri
- Admin memiliki dashboard yang modern dan informatif
- Meningkatkan keamanan dan ketertiban lalu lintas di perumahan
- Memudahkan pencarian dan pengelolaan data kendaraan

## Success Metrics

- Penghuni dapat mendaftarkan kendaraan dan melihat QR Code dalam waktu kurang dari 2 menit
- Admin dapat melihat seluruh data kendaraan dengan cepat melalui dashboard
- Semua kendaraan milik satu penghuni dapat terdaftar dalam satu akun
- Sistem responsif dan mudah digunakan di komputer maupun HP

## Target Users

### Penghuni Perumahan
**Responsibilities:**
- Mendaftarkan diri
- Mendaftarkan kendaraan pribadi (Mobil/Motor)
- Melihat daftar kendaraan dan QR Code miliknya

**Pain Points:**
- Harus datang ke pos jaga untuk urusan data kendaraan
- Tidak memiliki bukti digital kendaraan yang terdaftar

**Goals:**
- Dapat mendaftar dan mengelola kendaraan secara online
- Memiliki QR Code untuk setiap kendaraan

### Admin / Pengelola Perumahan
**Responsibilities:**
- Mengelola data penghuni
- Mengawasi seluruh data kendaraan
- Melihat laporan dan statistik

**Pain Points:**
- Data tersebar dan sulit dikelola
- Proses pendataan masih manual

**Goals:**
- Memiliki dashboard modern (Soft UI Dashboard 3)
- Dapat melakukan CRUD data dengan mudah

## User Problems

- Proses pendataan kendaraan yang masih manual
- Sulitnya melacak kepemilikan kendaraan
- Tidak adanya sistem identifikasi digital (QR Code)
- Admin kesulitan mendapatkan laporan data kendaraan

## Core Features

### Fitur Penghuni (User Panel)
- Registrasi akun penghuni
- Login penghuni
- Tambah/Edit/Hapus Kendaraan
- Generate QR Code otomatis
- Melihat daftar kendaraan beserta QR Code

### Fitur Admin Panel (Soft UI Dashboard 3)
- Login Admin
- Dashboard overview (jumlah penghuni, kendaraan, dll)
- Manajemen Penghuni (CRUD)
- Manajemen Kendaraan (CRUD)
- Melihat QR Code semua kendaraan
- Pencarian dan filter data

## Information Architecture

**User Side**
- Beranda
- Registrasi
- Login
- Dashboard Penghuni
- Tambah Kendaraan
- Daftar Kendaraan Saya

**Admin Side (Soft UI Dashboard)**
- Dashboard
- Data Penghuni
- Data Kendaraan
- Settings

## User Flows

### Flow Penghuni
Registrasi → Login → Dashboard → Tambah Kendaraan → QR Code Muncul → Daftar Kendaraan

### Flow Admin
Login Admin → Dashboard → Kelola Penghuni → Kelola Kendaraan → Lihat QR Code

## Functional Requirements

- Sistem mendukung multiple kendaraan per user
- Generate QR Code otomatis setelah kendaraan disimpan
- Autentikasi terpisah antara user dan admin
- Responsive di berbagai perangkat
- Menggunakan database MySQL
- Admin Panel menggunakan template Soft UI Dashboard 3

## Non Functional Requirements

- Keamanan password (hashing)
- Tampilan modern dan user-friendly
- Performa cepat
- Mudah di-maintenance

## MVP Scope

**Included:**
- Registrasi & Login Penghuni
- CRUD Kendaraan dengan QR Code
- Admin Panel menggunakan Soft UI Dashboard 3
- Manajemen data penghuni dan kendaraan oleh admin

**Not Included (MVP):**
- Scan QR Code oleh security
- Notifikasi
- Laporan PDF/Excel
- Fitur pembayaran iuran

## Database Status
Database sudah dibuat dengan nama **`perumahan_kendaraan`** dan berisi 3 tabel utama:
- `users`
- `vehicles`
- `admins`

## Teknologi
- Backend: PHP
- Database: MySQL
- Admin Template: Soft UI Dashboard 3
- QR Code: Google Chart API / Library QR Code
