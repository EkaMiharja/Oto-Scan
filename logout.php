<?php
/**
 * ============================================
 * Halaman Logout Penghuni
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * File ini menangani proses logout penghuni:
 * - Menghapus semua data session
 * - Destroy session
 * - Set flash message logout berhasil
 * - Redirect ke halaman login
 */

// Include functions untuk akses session dan helper
require_once 'functions.php';

// -- Hapus semua data session --
session_unset();

// -- Destroy session sepenuhnya --
session_destroy();

// -- Mulai session baru untuk flash message --
session_start();
setFlash('success', 'Anda berhasil logout.');

// -- Redirect ke halaman login --
redirect(BASE_URL . '/login.php');
