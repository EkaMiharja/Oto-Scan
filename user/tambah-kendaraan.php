<?php
/**
 * ============================================
 * Tambah Kendaraan Baru
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';

// Proteksi halaman
requireUserLogin();

$pageTitle = 'Tambah Kendaraan';
$userId = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $nomorPlat = strtoupper(sanitize($_POST['nomor_plat'] ?? ''));
    $jenis = sanitize($_POST['jenis'] ?? '');
    $merekModel = sanitize($_POST['merek_model'] ?? '');
    
    // Validasi
    if (empty($nomorPlat) || empty($jenis) || empty($merekModel)) {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek apakah nomor plat sudah ada
        $stmt = $pdo->prepare("SELECT id FROM vehicles WHERE nomor_plat = ?");
        $stmt->execute([$nomorPlat]);
        
        if ($stmt->fetch()) {
            $error = 'Nomor plat sudah terdaftar dalam sistem.';
        } else {
            // Generate token unik untuk URL scan publik
            $scanToken    = generateScanToken();
            $scanUrl      = BASE_URL . '/kendaraan/info.php?token=' . $scanToken;
            $qrCodePath   = generateQRCodeURL($scanUrl);
            $tanggalDaftar = date('Y-m-d');
            
            // Insert ke database (termasuk scan_token)
            $stmt = $pdo->prepare(
                "INSERT INTO vehicles (user_id, nomor_plat, jenis, merek_model, tanggal_daftar_kendaraan, qr_code_path, scan_token) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            if ($stmt->execute([$userId, $nomorPlat, $jenis, $merekModel, $tanggalDaftar, $qrCodePath, $scanToken])) {
                setFlash('success', 'Kendaraan berhasil ditambahkan.');
                redirect(BASE_URL . '/user/kendaraan.php');
            } else {
                $error = 'Terjadi kesalahan sistem, gagal menyimpan data.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Tambah Kendaraan</h1>
        <p class="text-muted mt-1 mb-0">Daftarkan kendaraan baru Anda ke dalam sistem.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="table-card p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_plat">Nomor Plat</label>
                                <input type="text" class="form-control text-uppercase" id="nomor_plat" name="nomor_plat" 
                                       placeholder="Contoh: B 1234 CD" required value="<?= htmlspecialchars($_POST['nomor_plat'] ?? '') ?>">
                                <small class="text-muted">Masukkan tanpa spasi atau dengan spasi.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis">Jenis Kendaraan</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Mobil" <?= (($_POST['jenis'] ?? '') == 'Mobil') ? 'selected' : '' ?>>Mobil</option>
                                    <option value="Motor" <?= (($_POST['jenis'] ?? '') == 'Motor') ? 'selected' : '' ?>>Motor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="merek_model">Merek & Model</label>
                        <input type="text" class="form-control" id="merek_model" name="merek_model" 
                               placeholder="Contoh: Honda Vario 150 Hitam" required value="<?= htmlspecialchars($_POST['merek_model'] ?? '') ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/user/kendaraan.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Simpan Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
