<?php
/**
 * ============================================
 * Manajemen Penghuni (Admin)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';
requireAdminLogin();

$pageTitle = 'Manajemen Penghuni';
$useDataTables = true;

// Aksi Hapus Penghuni
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Cek apakah user memiliki kendaraan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE user_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        setFlash('warning', "Gagal menghapus! Penghuni masih memiliki $count kendaraan terdaftar. Hapus kendaraan terlebih dahulu atau gunakan fitur cascade.");
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            setFlash('success', 'Penghuni berhasil dihapus.');
        } else {
            setFlash('danger', 'Gagal menghapus data.');
        }
    }
    redirect(BASE_URL . '/admin/penghuni.php');
}

// Ambil data semua penghuni beserta jumlah kendaraan mereka
$stmt = $pdo->query("
    SELECT u.*, (SELECT COUNT(*) FROM vehicles v WHERE v.user_id = u.id) as total_kendaraan
    FROM users u 
    ORDER BY u.id DESC
");
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6>Daftar Penghuni Perumahan</h6>
                <!-- Tombol tambah penghuni (opsional untuk admin) -->
                <!-- <a href="#" class="btn bg-gradient-primary btn-sm mb-0">Tambah Penghuni</a> -->
            </div>
            <div class="card-body px-0 pt-0 pb-2 mt-3">
                <div class="table-responsive p-3">
                    <table class="table align-items-center mb-0 table-hover datatable-exportable">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama & Email</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Daftar</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jml Kendaraan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php $no = 1; foreach ($users as $u): ?>
                                    <tr>
                                        <td class="align-middle px-4 text-sm">
                                            <?= $no++ ?>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><?= htmlspecialchars($u['nama_lengkap']) ?></h6>
                                                    <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($u['email']) ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold"><?= formatTanggal($u['tanggal_daftar']) ?></span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm <?= $u['total_kendaraan'] > 0 ? 'bg-gradient-success' : 'bg-gradient-secondary' ?>">
                                                <?= $u['total_kendaraan'] ?> Kendaraan
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="kendaraan.php?user_id=<?= $u['id'] ?>" class="btn bg-gradient-info btn-sm mb-0 me-2" data-toggle="tooltip" data-original-title="Lihat Kendaraan">
                                                Lihat
                                            </a>
                                            <a href="?action=delete&id=<?= $u['id'] ?>" onclick="return confirm('Yakin ingin menghapus penghuni ini?');" class="btn bg-gradient-danger btn-sm mb-0" data-toggle="tooltip" data-original-title="Hapus user">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Belum ada data penghuni.</td>
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
