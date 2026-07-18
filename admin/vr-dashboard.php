<?php
/**
 * ============================================
 * Dashboard VR (Virtual Reality Layout)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */

require_once '../functions.php';
requireAdminLogin();

$pageTitle = 'VR Dashboard';
$isVR = true; // Trigger layout VR di header & footer

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

// ============================================
// DATA GRAFIK (CHARTS) - 6 Bulan Terakhir
// ============================================
$labels = [];
$dataVehicles = [];
$dataUsers = [];

for ($i = 5; $i >= 0; $i--) {
    $time = mktime(0, 0, 0, $selectedMonth - $i, 1, $selectedYear);
    $m = date('m', $time);
    $y = date('Y', $time);
    $labels[] = date('M Y', $time);
    
    $stmtV = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE MONTH(tanggal_daftar_kendaraan) = ? AND YEAR(tanggal_daftar_kendaraan) = ?");
    $stmtV->execute([$m, $y]);
    $dataVehicles[] = (int)$stmtV->fetchColumn();
    
    $stmtU = $pdo->prepare("SELECT COUNT(*) FROM users WHERE MONTH(tanggal_daftar) = ? AND YEAR(tanggal_daftar) = ?");
    $stmtU->execute([$m, $y]);
    $dataUsers[] = (int)$stmtU->fetchColumn();
}

$labelsJson = json_encode($labels);
$dataVehiclesJson = json_encode($dataVehicles);
$dataUsersJson = json_encode($dataUsers);

$extraScripts = <<<HTML
<script>
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
                    ticks: { suggestedMin: 0, beginAtZero: true, padding: 15, font: { size: 14, family: "Inter", style: 'normal', lineHeight: 2 }, color: "#fff" },
                },
                x: {
                    grid: { drawBorder: false, display: false, drawOnChartArea: false, drawTicks: false },
                    ticks: { display: true, color: '#fff', font: { size: 10 } },
                },
            },
        },
    });

    var ctxLine = document.getElementById("chart-line").getContext("2d");
    var gradientStroke1 = ctxLine.createLinearGradient(0, 230, 0, 50);
    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); 

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
                    ticks: { display: true, padding: 10, color: '#b2b9bf', font: { size: 11, family: "Inter", style: 'normal', lineHeight: 2 }, suggestedMin: 0, beginAtZero: true }
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

<div class="row pt-10">
    <div class="col-lg-1 col-md-1 pt-5 pt-lg-0 ms-lg-5 text-center">
        <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-white border-radius-lg p-2 mt-2" data-bs-toggle="tooltip" data-bs-placement="left" title="Standard Dashboard">
            <i class="fas fa-home p-2"></i>
        </a>
    </div>
    
    <div class="col-lg-10 col-md-10">
        <!-- Weather & Welcome -->
        <div class="d-flex mb-4">
            <div class="me-auto text-white">
                <h1 class="display-1 font-weight-bold mt-n4 mb-0 text-white">28°C</h1>
                <h6 class="text-uppercase mb-0 ms-1 text-white opacity-8">Cerah</h6>
            </div>
            <div class="ms-auto">
                <img class="w-50 float-end mt-lg-n4" src="<?= BASE_URL ?>/assets/soft-ui/img/small-logos/icon-sun-cloud.png" alt="image sun">
            </div>
        </div>
        
        <!-- Filter Form in a glass card -->
        <div class="card move-on-hover overflow-hidden mb-4 bg-transparent border">
            <div class="card-body p-3">
                <form method="GET" class="d-flex align-items-center mb-0">
                    <h6 class="text-white mb-0 me-4">Filter Periode:</h6>
                    <select name="month" class="form-select form-select-sm me-2 bg-white" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
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
                    <select name="year" class="form-select form-select-sm bg-white" style="width: auto; min-width: 90px;" onchange="this.form.submit()">
                        <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Content Rows -->
        <div class="row">
            <!-- Stats -->
            <div class="col-lg-4 col-md-4">
                <!-- Card Total Penghuni (Highlighted Orange) -->
                <div class="card bg-gradient-warning shadow border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-users text-dark fs-5" aria-hidden="true"></i>
                            </div>
                            <span class="text-white text-sm font-weight-bolder"><?= $userGrowthText ?></span>
                        </div>
                        <div>
                            <h3 class="text-white font-weight-bolder mb-0"><?= $totalPenghuni ?></h3>
                            <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Total Penghuni</p>
                        </div>
                    </div>
                </div>
                
                <!-- Card Total Kendaraan (Dark Card) -->
                <div class="card bg-gradient-dark shadow border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="icon icon-shape bg-white shadow-sm text-center border-radius-md d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas fa-car text-dark fs-5" aria-hidden="true"></i>
                            </div>
                            <span class="text-white text-sm font-weight-bolder"><?= $vehicleGrowthText ?></span>
                        </div>
                        <div>
                            <h3 class="text-white font-weight-bolder mb-0"><?= $totalKendaraan ?></h3>
                            <p class="text-white text-sm mb-0 opacity-8 font-weight-bold">Total Kendaraan</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="col-lg-8 col-md-8">
                <div class="card mb-4 bg-gradient-dark">
                    <div class="card-body p-3">
                        <h6 class="text-white mb-0">Pendaftaran Kendaraan</h6>
                        <p class="text-sm text-white opacity-8 mb-4">Statistik 6 Bulan Terakhir</p>
                        <div class="chart">
                            <canvas id="chart-bars" class="chart-canvas" height="150"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body p-3">
                        <h6 class="text-dark mb-0">Pendaftaran Penghuni Baru</h6>
                        <p class="text-sm opacity-8 mb-4">Statistik 6 Bulan Terakhir</p>
                        <div class="chart">
                            <canvas id="chart-line" class="chart-canvas" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
