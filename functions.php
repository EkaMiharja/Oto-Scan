<?php
/**
 * ============================================
 * Functions Helper
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 * 
 * File ini berisi fungsi-fungsi helper yang
 * digunakan di seluruh sistem, termasuk:
 * - Sanitasi input (keamanan)
 * - Session management
 * - Flash messages (notifikasi)
 * - Auth check (user & admin)
 * - QR Code generation
 * - Format tanggal Indonesia
 */

// -- Mulai session jika belum dimulai --
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -- Include koneksi database --
require_once __DIR__ . '/config/database.php';

// -- Auto-detect Base URL aplikasi --
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace('\\', '/', __DIR__);
$baseUrl = str_replace($docRoot, '', $dir);
define('BASE_URL', $baseUrl);


// ============================================
// FUNGSI SANITASI & KEAMANAN
// ============================================

/**
 * Sanitasi input dari user untuk mencegah XSS attack
 * Menghapus spasi, tag HTML, dan encode karakter spesial
 * 
 * @param string $data Input yang akan disanitasi
 * @return string Data yang sudah aman ditampilkan
 */
function sanitize($data) {
    $data = trim($data);                                    // Hapus spasi di awal & akhir
    $data = strip_tags($data);                              // Hapus tag HTML
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');   // Encode karakter spesial
    return $data;
}


// ============================================
// FUNGSI NAVIGASI & REDIRECT
// ============================================

/**
 * Redirect ke halaman tertentu dan hentikan eksekusi
 * 
 * @param string $url URL tujuan redirect
 */
function redirect($url) {
    header("Location: $url");
    exit();
}


// ============================================
// FUNGSI FLASH MESSAGE (Notifikasi)
// ============================================

/**
 * Set flash message untuk ditampilkan di halaman berikutnya
 * Flash message otomatis hilang setelah ditampilkan satu kali
 * 
 * @param string $type Tipe alert Bootstrap (success, danger, warning, info)
 * @param string $message Pesan yang akan ditampilkan
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Ambil flash message dari session
 * Flash message akan dihapus dari session setelah diambil
 * 
 * @return array|null Array [type, message] atau null jika tidak ada
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // Hapus setelah diambil (sekali tampil)
        return $flash;
    }
    return null;
}

/**
 * Render flash message sebagai HTML Bootstrap alert
 * Dipanggil di halaman untuk menampilkan notifikasi otomatis
 */
function renderFlash() {
    $flash = getFlash();
    if ($flash) {
        echo '<div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show" role="alert">';
        echo $flash['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}


// ============================================
// FUNGSI AUTENTIKASI
// ============================================

/**
 * Cek apakah user (penghuni) sudah login
 * 
 * @return bool True jika user sudah login
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah admin sudah login
 * 
 * @return bool True jika admin sudah login
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Proteksi halaman user - redirect ke login jika belum login
 * Panggil fungsi ini di awal setiap halaman yang membutuhkan autentikasi user
 */
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        setFlash('warning', 'Silakan login terlebih dahulu.');
        redirect(BASE_URL . '/login.php');
    }
}

/**
 * Proteksi halaman admin - redirect ke login admin jika belum login
 * Panggil fungsi ini di awal setiap halaman yang membutuhkan autentikasi admin
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        setFlash('warning', 'Silakan login sebagai admin terlebih dahulu.');
        redirect(BASE_URL . '/admin/login.php');
    }
}


// ============================================
// FUNGSI QR CODE
// ============================================

/**
 * Generate URL QR Code menggunakan API goqr.me
 * API ini gratis dan reliable untuk generate QR Code secara online
 * 
 * @param string $data Data yang akan di-encode dalam QR Code
 * @param int $size Ukuran QR Code dalam pixel (default: 300)
 * @return string URL gambar QR Code yang bisa dipakai sebagai src <img>
 */
function generateQRCodeURL($data, $size = 300) {
    return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($data);
}

/**
 * Generate token unik untuk URL scan QR kendaraan.
 * Token ini digunakan sebagai identifier aman di URL publik.
 *
 * @return string Token hex 32-karakter yang aman dan unik
 */
function generateScanToken() {
    return bin2hex(random_bytes(16));
}


// ============================================
// FUNGSI FORMAT DATA
// ============================================

/**
 * Format tanggal ke format Indonesia
 * Contoh output: "14 Juli 2026"
 * 
 * @param string $date Tanggal dalam format database (YYYY-MM-DD)
 * @return string Tanggal dalam format Indonesia yang mudah dibaca
 */
function formatTanggal($date) {
    // Array nama bulan dalam Bahasa Indonesia
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $tanggal = date('j', strtotime($date));              // Tanggal tanpa leading zero
    $bln     = $bulan[(int)date('n', strtotime($date))]; // Nama bulan Indonesia
    $tahun   = date('Y', strtotime($date));              // Tahun 4 digit

    return "$tanggal $bln $tahun";
}


// ============================================
// FUNGSI DATA HELPER
// ============================================

/**
 * Ambil data user berdasarkan ID
 * 
 * @param PDO $pdo Koneksi database
 * @param int $userId ID user yang dicari
 * @return array|false Data user atau false jika tidak ditemukan
 */
function getUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Hitung jumlah kendaraan milik user tertentu
 * Bisa difilter berdasarkan jenis (Mobil/Motor) atau semua jenis
 * 
 * @param PDO $pdo Koneksi database
 * @param int $userId ID user pemilik kendaraan
 * @param string|null $jenis Filter jenis kendaraan (Mobil/Motor), null untuk semua
 * @return int Jumlah kendaraan
 */
function countUserVehicles($pdo, $userId, $jenis = null) {
    if ($jenis) {
        // Hitung berdasarkan jenis tertentu
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ? AND jenis = ?");
        $stmt->execute([$userId, $jenis]);
    } else {
        // Hitung semua kendaraan
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    return $stmt->fetchColumn();
}
