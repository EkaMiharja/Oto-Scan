<?php
/**
 * ============================================
 * Edit Kendaraan
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';

// Proteksi halaman
requireUserLogin();

$pageTitle = 'Edit Kendaraan';
$userId = $_SESSION['user_id'];
$error = '';

// Cek parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(BASE_URL . '/user/kendaraan.php');
}

$vehicleId = $_GET['id'];

// Ambil data kendaraan saat ini (pastikan milik user yang sedang login)
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ? AND user_id = ?");
$stmt->execute([$vehicleId, $userId]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    setFlash('danger', 'Kendaraan tidak ditemukan atau Anda tidak memiliki akses.');
    redirect(BASE_URL . '/user/kendaraan.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $nomorPlat = strtoupper(sanitize($_POST['nomor_plat'] ?? ''));
    $jenis = sanitize($_POST['jenis'] ?? '');
    $merekModel = sanitize($_POST['merek_model'] ?? '');
    
    // Validasi
    if (empty($nomorPlat) || empty($jenis) || empty($merekModel)) {
        $error = 'Semua field wajib diisi.';
    } else {
        // Cek plat jika berubah
        if ($nomorPlat !== $vehicle['nomor_plat']) {
            $stmt = $pdo->prepare("SELECT id FROM vehicles WHERE nomor_plat = ?");
            $stmt->execute([$nomorPlat]);
            
            if ($stmt->fetch()) {
                $error = 'Nomor plat sudah terdaftar dalam sistem.';
            }
        }
        
        if (empty($error)) {
            // Pertahankan token lama jika plat tidak berubah; regenerate jika plat berubah
            $scanToken  = $vehicle['scan_token'];
            $qrCodePath = $vehicle['qr_code_path'];

            if ($nomorPlat !== $vehicle['nomor_plat'] || empty($scanToken)) {
                // Buat token baru jika plat berubah atau token belum ada (kendaraan lama)
                $scanToken  = generateScanToken();
                $scanUrl    = BASE_URL . '/kendaraan/info.php?token=' . $scanToken;
                $qrCodePath = generateQRCodeURL($scanUrl);
            }
            
            // Update ke database
            $stmt = $pdo->prepare(
                "UPDATE vehicles SET nomor_plat = ?, jenis = ?, merek_model = ?, qr_code_path = ?, scan_token = ? WHERE id = ? AND user_id = ?"
            );
            
            if ($stmt->execute([$nomorPlat, $jenis, $merekModel, $qrCodePath, $scanToken, $vehicleId, $userId])) {
                setFlash('success', 'Data kendaraan berhasil diperbarui.');
                redirect(BASE_URL . '/user/kendaraan.php');
            } else {
                $error = 'Terjadi kesalahan sistem, gagal mengupdate data.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">Edit Kendaraan</h1>
        <p class="text-muted mt-1 mb-0">Ubah data kendaraan Anda.</p>
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
                                       placeholder="Contoh: B 1234 CD" required value="<?= htmlspecialchars($_POST['nomor_plat'] ?? $vehicle['nomor_plat']) ?>">
                                <small class="text-muted">Jika diubah, QR Code akan di-generate ulang.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis">Jenis Kendaraan</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Mobil" <?= (($_POST['jenis'] ?? $vehicle['jenis']) == 'Mobil') ? 'selected' : '' ?>>Mobil</option>
                                    <option value="Motor" <?= (($_POST['jenis'] ?? $vehicle['jenis']) == 'Motor') ? 'selected' : '' ?>>Motor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="merek_model">Merek & Model</label>
                        <input type="text" class="form-control" id="merek_model" name="merek_model" 
                               placeholder="Contoh: Honda Vario 150 Hitam" required value="<?= htmlspecialchars($_POST['merek_model'] ?? $vehicle['merek_model']) ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/user/kendaraan.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Update Kendaraan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
