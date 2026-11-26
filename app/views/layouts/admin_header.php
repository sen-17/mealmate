<?php
$adminUser = currentUser();
$currentPage = $_GET['page'] ?? 'admin-dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin - MealMate') ?></title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Kaushan+Script&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-layout d-flex min-vh-100 w-100">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <div class="admin-main flex-grow-1 d-flex flex-column min-w-0">
            <header class="admin-topbar d-flex align-items-center justify-content-between gap-2 gap-sm-3 px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-2 gap-sm-3 flex-grow-1 min-w-0">
                    <button class="btn d-lg-none border-0 p-1 flex-shrink-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas" aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <div class="admin-global-search position-relative flex-grow-1" id="adminGlobalSearchContainer">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="search" id="adminGlobalSearchInput" class="form-control ps-5 rounded-3 border-0 shadow-xs" placeholder="Cari menu, paket, atau pesanan..." autocomplete="off">
                        <div id="adminGlobalSearchDropdown" class="admin-search-dropdown d-none"></div>
                    </div>
                </div>

                <div class="dropdown flex-shrink-0">
                    <button class="btn border-0 d-flex align-items-center gap-2 gap-sm-3 p-0 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="admin-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold text-white">
                            <?= htmlspecialchars(getInitials($adminUser['name'] ?? 'Admin')) ?>
                        </div>
                        <div class="d-none d-md-flex flex-column lh-sm">
                            <span class="fw-semibold text-dark" style="font-size: 14px;"><?= htmlspecialchars($adminUser['name'] ?? 'Admin') ?></span>
                            <span class="text-muted" style="font-size: 12px;"><?= htmlspecialchars($adminUser['role'] ?? 'Admin') ?></span>
                        </div>
                        <i class="bi bi-chevron-down text-muted small d-none d-sm-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                        <li class="d-md-none px-3 py-2 border-bottom">
                            <div class="fw-semibold text-dark small"><?= htmlspecialchars($adminUser['name'] ?? 'Admin') ?></div>
                            <div class="text-muted" style="font-size: 11px;"><?= htmlspecialchars($adminUser['role'] ?? 'Admin') ?></div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?page=home">
                                <i class="bi bi-globe2 me-2 text-success"></i>Lihat Halaman Pelanggan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="index.php?page=logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="admin-content px-3 px-md-4 pb-5 pt-2 flex-grow-1">
                <?php if (!empty($flash)): ?>
                    <div class="flash-toast" id="flash-toast">
                        <?= renderFlash($flash, 'shadow-sm mb-0') ?>
                    </div>
                <?php endif; ?>
