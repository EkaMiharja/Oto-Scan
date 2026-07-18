<?php
/**
 * ============================================
 * Manajemen Kendaraan (Admin)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';
requireAdminLogin();

$pageTitle = 'Manajemen Kendaraan';
$useDataTables = true;

// Aksi Hapus Kendaraan
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    if ($stmt->execute([$id])) {
        setFlash('success', 'Kendaraan berhasil dihapus.');
    } else {
        setFlash('danger', 'Gagal menghapus kendaraan.');
    }
    // Redirect kembali ke URL sebelumnya jika ada parameter filter
    $redirect = BASE_URL . '/admin/kendaraan.php';
    if (isset($_GET['user_id'])) $redirect .= '?user_id=' . $_GET['user_id'];
    redirect($redirect);
}

// Filter berdasarkan user_id (jika admin klik "Lihat Kendaraan" dari halaman penghuni)
$userIdFilter = $_GET['user_id'] ?? null;

if ($userIdFilter) {
    $stmt = $pdo->prepare("
        SELECT v.*, u.nama_lengkap 
        FROM vehicles v 
        JOIN users u ON v.user_id = u.id 
        WHERE v.user_id = ?
        ORDER BY v.id DESC
    ");
    $stmt->execute([$userIdFilter]);
    
    // Ambil nama user untuk judul
    $userStmt = $pdo->prepare("SELECT nama_lengkap FROM users WHERE id = ?");
    $userStmt->execute([$userIdFilter]);
    $filterUserName = $userStmt->fetchColumn();
} else {
    $stmt = $pdo->query("
        SELECT v.*, u.nama_lengkap 
        FROM vehicles v 
        JOIN users u ON v.user_id = u.id 
        ORDER BY v.id DESC
    ");
}

$vehicles = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6>Daftar Kendaraan <?= $userIdFilter && isset($filterUserName) ? "Milik: $filterUserName" : "Keseluruhan" ?></h6>
                <?php if ($userIdFilter): ?>
                    <a href="<?= BASE_URL ?>/admin/kendaraan.php" class="btn btn-sm btn-outline-secondary mb-0">Tampilkan Semua</a>
                <?php endif; ?>
            </div>
            <div class="card-body px-0 pt-0 pb-2 mt-3">
                <div class="table-responsive p-3">
                    <table class="table align-items-center mb-0 table-hover datatable-exportable">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kendaraan & Plat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pemilik</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">QR Code</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Daftar</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($vehicles) > 0): ?>
                                <?php $no = 1; foreach ($vehicles as $v): ?>
                                    <tr>
                                        <td class="align-middle px-4 text-sm">
                                            <?= $no++ ?>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1 align-items-center">
                                                <div>
                                                    <?php if ($v['jenis'] == 'Mobil'): ?>
                                                        <i class="fas fa-car text-info me-3 fs-4"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-motorcycle text-warning me-3 fs-4"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm text-uppercase"><?= htmlspecialchars($v['nomor_plat']) ?></h6>
                                                    <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($v['merek_model']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($v['nama_lengkap']) ?></p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php if ($v['qr_code_path']): ?>
                                                <a href="<?= htmlspecialchars($v['qr_code_path']) ?>" target="_blank">
                                                    <img src="<?= htmlspecialchars($v['qr_code_path']) ?>" alt="QR" class="qr-code-img border-radius-sm shadow-sm" data-toggle="tooltip" title="Klik untuk memperbesar">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-xs text-secondary">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= formatTanggal($v['tanggal_daftar_kendaraan']) ?></span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="?action=delete&id=<?= $v['id'] ?><?= $userIdFilter ? '&user_id='.$userIdFilter : '' ?>" onclick="return confirm('Yakin ingin menghapus kendaraan ini dari sistem?');" class="text-danger font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Hapus Kendaraan">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Belum ada data kendaraan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
