<?php
/**
 * ============================================
 * Sidebar Admin Panel (Soft UI Dashboard)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 text-center" href="<?= BASE_URL ?>/admin/index.php">
            <img src="<?= BASE_URL ?>/assets/logo.png" class="navbar-brand-img h-100" alt="main_logo" style="max-height: 40px;">
        </a>
    </div>
    <hr class="horizontal dark mt-3">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <div class="mx-3 my-2 px-3 py-2 bg-gray-100 border-radius-lg d-flex align-items-center">
            <div class="avatar avatar-sm bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm me-2">
                <?= substr($_SESSION['admin_nama'] ?? 'A', 0, 1) ?>
            </div>
            <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm font-weight-bold">Halo, <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?>!</h6>
                <span class="text-xs text-secondary">Administrator</span>
            </div>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/index.php">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-chart-pie <?= ($currentPage == 'index.php') ? 'text-white' : 'text-dark' ?>"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'vr-dashboard.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/vr-dashboard.php">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-vr-cardboard <?= ($currentPage == 'vr-dashboard.php') ? 'text-white' : 'text-dark' ?>"></i>
                    </div>
                    <span class="nav-link-text ms-1">VR Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Data</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'penghuni.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/penghuni.php">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users <?= ($currentPage == 'penghuni.php') ? 'text-white' : 'text-dark' ?>"></i>
                    </div>
                    <span class="nav-link-text ms-1">Penghuni</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'kendaraan.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/kendaraan.php">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-car <?= ($currentPage == 'kendaraan.php') ? 'text-white' : 'text-dark' ?>"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kendaraan</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Sistem</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>/admin/logout.php">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-danger"></i>
                    </div>
                    <span class="nav-link-text ms-1 text-danger">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
