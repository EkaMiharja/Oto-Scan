<?php
/**
 * ============================================
 * Dashboard User
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';

// Proteksi halaman
requireUserLogin();

$pageTitle = 'Dashboard';
$userId = $_SESSION['user_id'];

// Ambil data user
$user = getUserById($pdo, $userId);

// Hitung statistik kendaraan
$totalMobil = countUserVehicles($pdo, $userId, 'Mobil');
$totalMotor = countUserVehicles($pdo, $userId, 'Motor');
$totalKendaraan = $totalMobil + $totalMotor;

// Ambil 5 kendaraan terbaru
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$stmt->execute([$userId]);
$recentVehicles = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted mt-1 mb-0">Selamat datang kembali, <?= htmlspecialchars($user['nama_lengkap']) ?>!</p>
    </div>
</div>

<div class="container">
    <?php renderFlash(); ?>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <!-- Total Kendaraan -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-car-side"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $totalKendaraan ?></h3>
                    <p>Total Kendaraan</p>
                </div>
            </div>
        </div>
        
        <!-- Total Mobil -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $totalMobil ?></h3>
                    <p>Mobil Terdaftar</p>
                </div>
            </div>
        </div>

        <!-- Total Motor -->
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div class="stat-details">
                    <h3><?= $totalMotor ?></h3>
                    <p>Motor Terdaftar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kendaraan Terbaru -->
    <div class="table-card">
        <div class="table-card-header">
            <h4>Kendaraan Terbaru Ditambahkan</h4>
            <a href="<?= BASE_URL ?>/user/kendaraan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive mt-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nomor Plat</th>
                        <th>Jenis</th>
                        <th>Merek & Model</th>
                        <th>Tanggal Daftar</th>
                        <th>QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recentVehicles) > 0): ?>
                        <?php foreach ($recentVehicles as $v): ?>
                            <tr class="vehicle-row" style="cursor:pointer;" 
                                data-id="<?= $v['id'] ?>"
                                data-plat="<?= htmlspecialchars($v['nomor_plat']) ?>"
                                data-jenis="<?= htmlspecialchars($v['jenis']) ?>"
                                data-merek="<?= htmlspecialchars($v['merek_model']) ?>"
                                data-tanggal="<?= formatTanggal($v['tanggal_daftar_kendaraan']) ?>"
                                data-qr="<?= $v['qr_code_path'] ? htmlspecialchars($v['qr_code_path']) : '' ?>">
                                <td><strong class="text-uppercase"><?= htmlspecialchars($v['nomor_plat']) ?></strong></td>
                                <td>
                                    <?php if ($v['jenis'] === 'Mobil'): ?>
                                        <span class="badge-mobil"><i class="fas fa-car me-1"></i> Mobil</span>
                                    <?php else: ?>
                                        <span class="badge-motor"><i class="fas fa-motorcycle me-1"></i> Motor</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($v['merek_model']) ?></td>
                                <td><?= formatTanggal($v['tanggal_daftar_kendaraan']) ?></td>
                                <td>
                                    <?php if ($v['qr_code_path']): ?>
                                        <img src="<?= htmlspecialchars($v['qr_code_path']) ?>" alt="QR Code" class="qr-code-img">
                                    <?php else: ?>
                                        <span class="text-muted small">Belum ada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <p class="text-muted mb-0">Belum ada kendaraan yang didaftarkan.</p>
                                <a href="tambah-kendaraan.php" class="btn btn-sm btn-primary-custom mt-2">Daftarkan Sekarang</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Vehicle Detail Modal -->
<div id="vehicleModal" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(15,20,50,0.55); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;
" onclick="closeVehicleModal(event)">
    <div id="vehicleModalCard" style="
        background:#fff; border-radius:1.25rem;
        width:100%; max-width:420px; margin:16px;
        box-shadow:0 24px 60px rgba(0,0,0,0.18);
        transform:scale(0.88) translateY(30px);
        opacity:0; transition:transform 0.3s cubic-bezier(.34,1.56,.64,1), opacity 0.25s ease;
        overflow:hidden;
    " onclick="event.stopPropagation()">
        <!-- Header -->
        <div id="vmHeader" style="
            background:linear-gradient(135deg,#363d9d 0%,#6d74d8 100%);
            padding:24px 24px 20px; position:relative;
        ">
            <button onclick="closeVehicleModal()" style="
                position:absolute; top:14px; right:16px;
                background:rgba(255,255,255,0.2); border:none;
                color:#fff; border-radius:50%; width:30px; height:30px;
                font-size:16px; cursor:pointer; line-height:1;
            ">&times;</button>
            <div style="display:flex;align-items:center;gap:14px;">
                <div id="vmIcon" style="
                    width:52px;height:52px;border-radius:14px;
                    background:rgba(255,255,255,0.2);
                    display:flex;align-items:center;justify-content:center;
                    font-size:24px;color:#fff;
                "></div>
                <div>
                    <div id="vmPlat" style="font-size:22px;font-weight:700;color:#fff;letter-spacing:.05em;"></div>
                    <div id="vmJenisBadge" style="margin-top:4px;"></div>
                </div>
            </div>
        </div>
        <!-- Body -->
        <div style="padding:22px 24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div style="background:#f8f9fa;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#67748e;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Merek & Model</div>
                    <div id="vmMerek" style="font-size:15px;font-weight:600;color:#344767;margin-top:4px;"></div>
                </div>
                <div style="background:#f8f9fa;border-radius:10px;padding:14px;">
                    <div style="font-size:11px;color:#67748e;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Tanggal Daftar</div>
                    <div id="vmTanggal" style="font-size:15px;font-weight:600;color:#344767;margin-top:4px;"></div>
                </div>
            </div>
            <!-- QR Code Section -->
            <div id="vmQrSection" style="text-align:center;">
                <div style="font-size:12px;color:#67748e;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;">QR Code Kendaraan</div>
                <div style="display:inline-block;padding:12px;background:#fff;border:2px solid #e9ecef;border-radius:12px;">
                    <img id="vmQrImg" src="" alt="QR Code" style="width:150px;height:150px;display:block;">
                </div>
                <div style="margin-top:12px;">
                    <a id="vmQrDownload" href="" download style="
                        display:inline-flex;align-items:center;gap:6px;
                        background:linear-gradient(135deg,#363d9d,#6d74d8);
                        color:#fff;text-decoration:none;padding:8px 18px;
                        border-radius:8px;font-size:13px;font-weight:600;
                        transition:opacity 0.2s;
                    " onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-download"></i> Unduh QR Code
                    </a>
                </div>
            </div>
            <div id="vmQrEmpty" style="text-align:center;padding:10px 0;display:none;">
                <i class="fas fa-qrcode" style="font-size:40px;color:#d2d6da;"></i>
                <p style="color:#67748e;margin:8px 0 0;font-size:13px;">QR Code belum tersedia</p>
            </div>
        </div>
    </div>
</div>

<script>
function openVehicleModal(row) {
    const plat   = row.dataset.plat;
    const jenis  = row.dataset.jenis;
    const merek  = row.dataset.merek;
    const tgl    = row.dataset.tanggal;
    const qr     = row.dataset.qr;

    document.getElementById('vmPlat').textContent   = plat.toUpperCase();
    document.getElementById('vmMerek').textContent  = merek;
    document.getElementById('vmTanggal').textContent= tgl;

    const icon = document.getElementById('vmIcon');
    const badge = document.getElementById('vmJenisBadge');
    if (jenis === 'Mobil') {
        icon.innerHTML  = '<i class="fas fa-car"></i>';
        badge.innerHTML = '<span style="background:rgba(255,255,255,0.25);color:#fff;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-car me-1"></i>Mobil</span>';
    } else {
        icon.innerHTML  = '<i class="fas fa-motorcycle"></i>';
        badge.innerHTML = '<span style="background:rgba(255,255,255,0.25);color:#fff;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;"><i class="fas fa-motorcycle me-1"></i>Motor</span>';
    }

    const qrSec   = document.getElementById('vmQrSection');
    const qrEmpty = document.getElementById('vmQrEmpty');
    if (qr) {
        document.getElementById('vmQrImg').src           = qr;
        document.getElementById('vmQrDownload').href     = qr;
        qrSec.style.display   = 'block';
        qrEmpty.style.display = 'none';
    } else {
        qrSec.style.display   = 'none';
        qrEmpty.style.display = 'block';
    }

    const modal = document.getElementById('vehicleModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        const card = document.getElementById('vehicleModalCard');
        card.style.transform = 'scale(1) translateY(0)';
        card.style.opacity   = '1';
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeVehicleModal(e) {
    if (e && e.target !== document.getElementById('vehicleModal')) return;
    const card  = document.getElementById('vehicleModalCard');
    const modal = document.getElementById('vehicleModal');
    card.style.transform = 'scale(0.88) translateY(30px)';
    card.style.opacity   = '0';
    setTimeout(() => { modal.style.display = 'none'; }, 280);
    document.body.style.overflow = '';
}

document.querySelectorAll('.vehicle-row').forEach(row => {
    row.addEventListener('click', () => openVehicleModal(row));
    row.addEventListener('mouseenter', () => row.style.background = '#f0f3ff');
    row.addEventListener('mouseleave', () => row.style.background = '');
});

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeVehicleModal(null); });
</script>
