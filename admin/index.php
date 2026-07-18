<?php
/**
 * ============================================
 * Dashboard Admin
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';
requireAdminLogin();

$pageTitle = 'Dashboard';
$useDataTables = true; // Aktifkan DataTables untuk tabel kendaraan di dashboard

// ============================================
// FILTER BULAN & TAHUN
// ============================================
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

// ============================================
// DATA CARD STATISTIK & GROWTH (Bulan Ini vs Bulan Lalu)
// ============================================
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalPenghuni = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM vehicles");
$totalKendaraan = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Mobil'");
$totalMobil = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Motor'");
$totalMotor = $stmt->fetchColumn();

// Hitung Growth (Bulan ini vs Bulan lalu)
// Penghuni
$thisMonthUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(tanggal_daftar) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_daftar) = YEAR(CURRENT_DATE())")->fetchColumn();
$lastMonthUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(tanggal_daftar) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(tanggal_daftar) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)")->fetchColumn();
$userGrowth = $lastMonthUsers > 0 ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100) : ($thisMonthUsers > 0 ? 100 : 0);
$userGrowthText = ($userGrowth >= 0 ? '+' : '') . $userGrowth . '%';

// Kendaraan
$thisMonthVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE())")->fetchColumn();
$lastMonthVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)")->fetchColumn();
$vehicleGrowth = $lastMonthVehicles > 0 ? round((($thisMonthVehicles - $lastMonthVehicles) / $lastMonthVehicles) * 100) : ($thisMonthVehicles > 0 ? 100 : 0);
$vehicleGrowthText = ($vehicleGrowth >= 0 ? '+' : '') . $vehicleGrowth . '%';

// Mobil
$thisMonthMobil = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Mobil' AND MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE())")->fetchColumn();
$lastMonthMobil = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Mobil' AND MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)")->fetchColumn();
$mobilGrowth = $lastMonthMobil > 0 ? round((($thisMonthMobil - $lastMonthMobil) / $lastMonthMobil) * 100) : ($thisMonthMobil > 0 ? 100 : 0);
$mobilGrowthText = ($mobilGrowth >= 0 ? '+' : '') . $mobilGrowth . '%';

// Motor
$thisMonthMotor = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Motor' AND MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE())")->fetchColumn();
$lastMonthMotor = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE jenis = 'Motor' AND MONTH(tanggal_daftar_kendaraan) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(tanggal_daftar_kendaraan) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)")->fetchColumn();
$motorGrowth = $lastMonthMotor > 0 ? round((($thisMonthMotor - $lastMonthMotor) / $lastMonthMotor) * 100) : ($thisMonthMotor > 0 ? 100 : 0);
$motorGrowthText = ($motorGrowth >= 0 ? '+' : '') . $motorGrowth . '%';

$stmt = $pdo->query("
    SELECT v.*, u.nama_lengkap 
    FROM vehicles v 
    JOIN users u ON v.user_id = u.id 
    ORDER BY v.id DESC
");
$recentVehicles = $stmt->fetchAll();

// ============================================
// DATA GRAFIK (CHARTS) - 6 Bulan Terakhir dari yang dipilih
// ============================================
$labels = [];
$dataVehicles = [];
$dataUsers = [];

for ($i = 5; $i >= 0; $i--) {
    $time = mktime(0, 0, 0, $selectedMonth - $i, 1, $selectedYear);
    $m = date('m', $time);
    $y = date('Y', $time);
    $labels[] = date('M Y', $time); // ex: Jul 2026
    
    // Total Kendaraan di bulan tsb
    $stmtV = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE MONTH(tanggal_daftar_kendaraan) = ? AND YEAR(tanggal_daftar_kendaraan) = ?");
    $stmtV->execute([$m, $y]);
    $dataVehicles[] = (int)$stmtV->fetchColumn();
    
    // Total Penghuni Baru di bulan tsb
    $stmtU = $pdo->prepare("SELECT COUNT(*) FROM users WHERE MONTH(tanggal_daftar) = ? AND YEAR(tanggal_daftar) = ?");
    $stmtU->execute([$m, $y]);
    $dataUsers[] = (int)$stmtU->fetchColumn();
}

$labelsJson = json_encode($labels);
$dataVehiclesJson = json_encode($dataVehicles);
$dataUsersJson = json_encode($dataUsers);

// Siapkan script untuk dirender di footer.php (setelah chartjs dipanggil)
$extraScripts = <<<HTML
<script>
    // Inisialisasi Bar Chart (Kendaraan)
    var ctxBars = document.getElementById("chart-bars").getContext("2d");
    new Chart(ctxBars, {
        type: "bar",
        data: {
            labels: $labelsJson,
            datasets: [{
                label: "Kendaraan Terdaftar",
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                backgroundColor: "#fff",
                data: $dataVehiclesJson,
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: {
                    grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
                    ticks: { suggestedMin: 0, beginAtZero: true, padding: 15, stepSize: 1, precision: 0, font: { size: 14, family: "Inter", style: 'normal', lineHeight: 2 }, color: "#fff" },
                },
                x: {
                    grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
                    ticks: { display: true, color: '#fff', font: { size: 10 } },
                },
            },
        },
    });

    // Inisialisasi Line Chart (Penghuni Baru)
    var ctxLine = document.getElementById("chart-line").getContext("2d");
    var gradientStroke1 = ctxLine.createLinearGradient(0, 230, 0, 50);
    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    new Chart(ctxLine, {
        type: "line",
        data: {
            labels: $labelsJson,
            datasets: [{
                label: "Penghuni Baru",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#cb0c9f",
                borderWidth: 3,
                backgroundColor: gradientStroke1,
                fill: true,
                data: $dataUsersJson,
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: {
                    grid: { drawBorder: false, display: true, drawOnChartArea: true, drawTicks: false, borderDash: [5, 5] },
                    ticks: { display: true, padding: 10, color: '#b2b9bf', stepSize: 1, precision: 0, font: { size: 11, family: "Inter", style: 'normal', lineHeight: 2 }, suggestedMin: 0, beginAtZero: true }
                },
                x: {
                    grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false, borderDash: [5, 5] },
                    ticks: { display: true, color: '#b2b9bf', padding: 20, font: { size: 11, family: "Inter", style: 'normal', lineHeight: 2 } },
                },
            },
        },
    });
</script>
HTML;

include 'includes/header.php';
?>

<div class="row">
    <!-- Card Total Penghuni (Highlighted Orange) -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card bg-gradient-warning shadow border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-users text-dark fs-5" aria-hidden="true"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-weight-bolder mb-0"><?= $totalPenghuni ?></h3>
                    <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Total Penghuni</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Total Kendaraan (Dark Card) -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card bg-gradient-dark shadow border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-car text-dark fs-5" aria-hidden="true"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-weight-bolder mb-0"><?= $totalKendaraan ?></h3>
                    <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Total Kendaraan</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Mobil (Dark Card) -->
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card bg-gradient-dark shadow border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-car-side text-dark fs-5" aria-hidden="true"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-weight-bolder mb-0"><?= $totalMobil ?></h3>
                    <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Mobil</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Motor (Dark Card) -->
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-gradient-dark shadow border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-motorcycle text-dark fs-5" aria-hidden="true"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-weight-bolder mb-0"><?= $totalMotor ?></h3>
                    <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Motor</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTER DATA -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card px-3 py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-sm">Filter Grafik (6 Bulan Terakhir dari yang dipilih)</h6>
                <form method="GET" class="d-flex align-items-center mb-0">
                    <select name="month" class="form-select form-select-sm me-2" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
                        <?php 
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        foreach($months as $mNum => $mName): ?>
                            <option value="<?= $mNum ?>" <?= $mNum == $selectedMonth ? 'selected' : '' ?>><?= $mName ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="year" class="form-select form-select-sm" style="width: auto; min-width: 90px;" onchange="this.form.submit()">
                        <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="row mt-4">
    <!-- Bar Chart (Kendaraan) -->
    <div class="col-lg-5 mb-lg-0 mb-4">
        <div class="card z-index-2">
            <div class="card-body p-3">
                <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
                    <div class="chart">
                        <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                    </div>
                </div>
                <h6 class="ms-2 mt-4 mb-0"> Pendaftaran Kendaraan </h6>
                <p class="text-sm ms-2">Statistik kendaraan baru terdaftar</p>
            </div>
        </div>
    </div>
    
    <!-- Line Chart (Penghuni) -->
    <div class="col-lg-7">
        <div class="card z-index-2">
            <div class="card-header pb-0">
                <h6>Pendaftaran Penghuni Baru</h6>
                <p class="text-sm">Statistik penghuni yang mendaftar ke sistem</p>
            </div>
            <div class="card-body p-3">
                <div class="chart">
                    <canvas id="chart-line" class="chart-canvas" height="235"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RECENT VEHICLES ROW -->
<div class="row mt-4">
    <div class="col-lg-12 mb-lg-0 mb-4">
        <div class="card ">
            <div class="card-header pb-0 p-3">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-2">Kendaraan Terdaftar Terbaru</h6>
                    <a href="<?= BASE_URL ?>/admin/kendaraan.php" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table align-items-center mb-0 datatable-exportable">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Pemilik</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kendaraan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nomor Plat</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recentVehicles) > 0): ?>
                            <?php foreach ($recentVehicles as $v): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm"><?= htmlspecialchars($v['nama_lengkap']) ?></h6>
                                            </div>
                                        </div>
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
                                                <h6 class="mb-0 text-sm"><?= htmlspecialchars($v['merek_model']) ?></h6>
                                                <p class="text-xs text-secondary mb-0"><?= $v['jenis'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-secondary text-uppercase"><?= htmlspecialchars($v['nomor_plat']) ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold"><?= formatTanggal($v['tanggal_daftar_kendaraan']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">Belum ada data kendaraan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
