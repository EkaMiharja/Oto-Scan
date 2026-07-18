<?php
/**
 * ============================================
 * Header Admin Panel (Soft UI Dashboard)
 * Sistem Manajemen Kendaraan Perumahan
 * ============================================
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/functions.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Admin Panel | Sistem Kendaraan</title>
    
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    
    <!-- Nucleo Icons -->
    <link href="<?= BASE_URL ?>/assets/soft-ui/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>/assets/soft-ui/css/nucleo-svg.css" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= BASE_URL ?>/assets/soft-ui/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
    
    <?php if (isset($useDataTables) && $useDataTables): ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        .dt-buttons .btn {
            padding: 0.5rem 1rem !important;
            font-size: 0.75rem !important;
            margin-bottom: 0.5rem !important;
        }
        div.dataTables_wrapper div.dataTables_filter {
            text-align: right !important;
            margin-bottom: 10px;
        }
        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 15px;
        }
        .dataTables_info {
            font-size: 0.75rem !important;
        }
        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 0;
            padding-top: 0;
        }
        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            justify-content: flex-end;
            margin-bottom: 0;
        }
        .dataTables_wrapper .page-link {
            border-radius: 50% !important;
            margin: 0 3px !important;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            font-size: 0.85rem !important;
            color: #8392ab;
            border: 1px solid #dee2e6;
        }
        .dataTables_wrapper .page-item.active .page-link {
            background-image: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%) !important; /* Soft UI Success gradient or primary */
            background-color: transparent !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 3px 5px -1px rgba(0, 0, 0, 0.09), 0 2px 3px -1px rgba(0, 0, 0, 0.07);
        }
        .dataTables_wrapper .page-item.disabled .page-link {
            background-color: #fff !important;
            border-color: #e9ecef !important;
            opacity: 0.6;
        }
        .dataTables_wrapper .page-item:not(.active) .page-link:hover {
            background-color: #e9ecef !important;
            border-color: #dee2e6 !important;
        }
        .dataTables_wrapper .row.mt-3 {
            align-items: center;
            padding-top: 10px;
        }
    </style>
    <?php endif; ?>
    
    <style>
        .qr-code-img {
            width: 60px;
            height: 60px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px;
        }
    </style>
</head>
<?php $isVR = isset($isVR) ? $isVR : false; ?>
<body class="g-sidenav-show bg-gray-100 <?= $isVR ? 'virtual-reality' : '' ?>">

<?php if ($isVR): ?>
    <!-- VR Layout -->
    <div>
        <?php if (isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') include 'navbar.php'; ?>
    </div>
    <div class="border-radius-xl mt-3 mx-3 position-relative" style="background-image: url('<?= BASE_URL ?>/assets/soft-ui/img/vr-bg.jpg'); background-size: cover;">
        <?php if (isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') include 'sidebar.php'; ?>
        <main class="main-content mt-1 border-radius-lg">
            <div class="section min-vh-85 position-relative transform-scale-0 transform-scale-md-7">
                <div class="container-fluid py-4">
                    <?php renderFlash(); ?>
<?php else: ?>
    <!-- Normal Layout -->
    <?php if (isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') include 'sidebar.php'; ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <?php if (isAdminLoggedIn() && basename($_SERVER['PHP_SELF']) !== 'login.php') include 'navbar.php'; ?>
        <div class="container-fluid py-4">
            <?php renderFlash(); ?>
<?php endif; ?>
