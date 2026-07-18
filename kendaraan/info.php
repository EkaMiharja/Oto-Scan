<?php
/**
 * ============================================
 * Halaman Publik Info Kendaraan (via Scan QR)
 * Tidak memerlukan login — bisa dibuka siapa saja
 * ============================================
 */

require_once '../functions.php';

$token = trim($_GET['token'] ?? '');

// Validasi format token (32 hex chars)
if (empty($token) || strlen($token) !== 32 || !ctype_xdigit($token)) {
    $vehicle = null;
    $owner   = null;
} else {
    // Cari kendaraan berdasarkan token
    $stmt = $pdo->prepare("
        SELECT v.*, u.nama_lengkap 
        FROM vehicles v 
        JOIN users u ON v.user_id = u.id 
        WHERE v.scan_token = ?
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $vehicle = $stmt->fetch();
    $owner   = $vehicle ? $vehicle['nama_lengkap'] : null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $vehicle ? 'Info Kendaraan - ' . htmlspecialchars($vehicle['nomor_plat']) : 'Kendaraan Tidak Ditemukan' ?> | Sistem Kendaraan</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2ff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Dot background pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle, rgba(54,61,157,0.08) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            animation: fadeUp 0.45s cubic-bezier(0.34,1.26,0.64,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(54,61,157,0.12), 0 4px 16px rgba(0,0,0,0.06);
        }

        /* Header */
        .card-header {
            background: linear-gradient(135deg, #363d9d 0%, #6d74d8 100%);
            padding: 28px 24px 24px;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            pointer-events: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 14px;
            backdrop-filter: blur(4px);
        }

        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #4ade80;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(1.35); }
        }

        .plate-number {
            font-size: 34px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .vehicle-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            background: rgba(255,255,255,0.22);
            color: #fff;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Body */
        .card-body { padding: 22px 24px; }

        /* Owner */
        .owner-section {
            display: flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, rgba(54,61,157,0.06), rgba(109,116,216,0.06));
            border: 1px solid rgba(54,61,157,0.12);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .owner-avatar {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #363d9d, #6d74d8);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
        }

        .owner-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #67748e;
        }

        .owner-name {
            font-size: 15px;
            font-weight: 700;
            color: #363d9d;
            margin-top: 2px;
        }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }

        .info-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 13px;
        }

        .info-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #67748e;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #344767;
        }

        /* QR section */
        .qr-section { text-align: center; padding-top: 2px; }

        .qr-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #67748e;
            margin-bottom: 10px;
        }

        .qr-wrapper {
            display: inline-block;
            padding: 10px;
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 14px;
        }

        .qr-wrapper img { display: block; width: 110px; height: 110px; }

        /* Footer */
        .card-footer {
            padding: 14px 24px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .footer-logo {
            font-size: 12px;
            color: #67748e;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .footer-logo img { height: 20px; object-fit: contain; }
        .scan-time { font-size: 11px; color: #adb5bd; margin-top: 5px; }

        /* Not found */
        .not-found { text-align: center; padding: 48px 24px 32px; }
        .not-found-icon { font-size: 60px; color: #d2d6da; margin-bottom: 18px; }
        .not-found h2 { font-size: 18px; font-weight: 700; color: #344767; margin-bottom: 8px; }
        .not-found p { font-size: 13px; color: #67748e; line-height: 1.6; }
    </style>
</head>
<body>

<div class="container">
    <?php if ($vehicle): ?>
    <div class="card">

        <!-- Header -->
        <div class="card-header">
            <div class="status-badge">
                <span class="status-dot"></span>
                Kendaraan Terdaftar
            </div>
            <div class="plate-number"><?= htmlspecialchars($vehicle['nomor_plat']) ?></div>
            <div class="vehicle-type-badge">
                <?php if ($vehicle['jenis'] === 'Mobil'): ?>
                    <i class="fas fa-car"></i> Mobil
                <?php else: ?>
                    <i class="fas fa-motorcycle"></i> Motor
                <?php endif; ?>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body">

            <!-- Pemilik -->
            <div class="owner-section">
                <div class="owner-avatar"><i class="fas fa-user"></i></div>
                <div>
                    <div class="owner-label">Pemilik Kendaraan</div>
                    <div class="owner-name"><?= htmlspecialchars($owner) ?></div>
                </div>
            </div>

            <!-- Info -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Merek & Model</div>
                    <div class="info-value"><?= htmlspecialchars($vehicle['merek_model']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Daftar</div>
                    <div class="info-value"><?= formatTanggal($vehicle['tanggal_daftar_kendaraan']) ?></div>
                </div>
            </div>

            <!-- QR Code -->
            <?php if ($vehicle['qr_code_path']): ?>
            <div class="qr-section">
                <div class="qr-label">QR Code Kendaraan Ini</div>
                <div class="qr-wrapper">
                    <img src="<?= htmlspecialchars($vehicle['qr_code_path']) ?>" alt="QR Code">
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="footer-logo">
                <img src="<?= BASE_URL ?>/assets/logo.png" alt="Logo">
                Sistem Kendaraan Perumahan
            </div>
            <div class="scan-time">Dipindai pada <?= date('d M Y, H:i') ?> WIB</div>
        </div>

    </div>

    <?php else: ?>

    <!-- Not Found -->
    <div class="card">
        <div class="not-found">
            <div class="not-found-icon"><i class="fas fa-qrcode"></i></div>
            <h2>Kendaraan Tidak Ditemukan</h2>
            <p>QR Code tidak valid atau kendaraan sudah tidak terdaftar dalam sistem perumahan ini.</p>
        </div>
        <div class="card-footer">
            <div class="footer-logo">
                <img src="<?= BASE_URL ?>/assets/logo.png" alt="Logo">
                Sistem Kendaraan Perumahan
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

</body>
</html>
