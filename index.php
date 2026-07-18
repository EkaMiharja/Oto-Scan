<?php
/**
 * ============================================
 * Halaman Utama (Landing Page)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * Halaman ini berfungsi sebagai entry point aplikasi.
 * Redirect otomatis berdasarkan status login:
 * - Jika user sudah login → Dashboard User
 * - Jika belum login → Halaman Login
 */

// Include file functions (berisi session, database, helper)
require_once 'functions.php';

// -- Cek status login dan redirect --
if (isUserLoggedIn()) {
    // User sudah login, arahkan ke dashboard
    redirect(BASE_URL . '/user/dashboard.php');
} else {
    // Belum login, arahkan ke halaman login
    redirect(BASE_URL . '/login.php');
}
