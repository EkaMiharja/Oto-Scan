<?php
/**
 * ============================================
 * Hapus Kendaraan
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';

// Proteksi halaman
requireUserLogin();

$userId = $_SESSION['user_id'];

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(BASE_URL . '/user/kendaraan.php');
}

$vehicleId = $_GET['id'];

// Verifikasi kepemilikan kendaraan sebelum menghapus
$stmt = $pdo->prepare("SELECT id FROM vehicles WHERE id = ? AND user_id = ?");
$stmt->execute([$vehicleId, $userId]);
$vehicle = $stmt->fetch();

if ($vehicle) {
    // Hapus data (Hard Delete sesuai kebutuhan MVP)
    $deleteStmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    if ($deleteStmt->execute([$vehicleId])) {
        setFlash('success', 'Kendaraan berhasil dihapus.');
    } else {
        setFlash('danger', 'Gagal menghapus kendaraan. Silakan coba lagi.');
    }
} else {
    setFlash('danger', 'Kendaraan tidak ditemukan atau Anda tidak memiliki akses.');
}

// Kembali ke halaman daftar kendaraan
redirect(BASE_URL . '/user/kendaraan.php');
