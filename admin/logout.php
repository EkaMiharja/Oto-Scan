<?php
/**
 * ============================================
 * Logout Admin
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */
require_once '../functions.php';

// Hapus variabel session khusus admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nama']);
unset($_SESSION['admin_username']);

// Note: Kita tidak melakukan session_destroy() agar tidak menghapus session user jika mereka login di browser yang sama
// Kecuali jika tidak ada session lain
if (empty($_SESSION)) {
    session_destroy();
}

// Mulai session kembali jika dihancurkan untuk flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setFlash('success', 'Anda telah logout dari panel admin.');
redirect(BASE_URL . '/admin/login.php');
