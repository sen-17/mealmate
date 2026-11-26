<?php
$currentPreset = $periodInfo['preset'];
$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];

$baseParams = [
    'page' => 'admin-reports',
    'period' => $currentPreset,
    'start_date' => $currentPreset === 'custom' ? $startDate : null,
    'end_date' => $currentPreset === 'custom' ? $endDate : null,
    'status' => $statusFilter !== '' ? $statusFilter : null,
    'package_id' => $packageIdFilter > 0 ? $packageIdFilter : null,
    'q' => $searchQuery !== '' ? $searchQuery : null,
    'sort' => $sort ?? 'date',
    'dir' => $dir ?? 'desc',
];

$exportCsvUrl = 'index.php?' . http_build_query(array_merge($baseParams, ['export' => 'csv']));
$exportExcelUrl = 'index.php?' . http_build_query(array_merge($baseParams, ['export' => 'excel']));

$startRow = $totalTransactions > 0 ? (($currentPageNum - 1) * $perPage) + 1 : 0;
$endRow = min($totalTransactions, $currentPageNum * $perPage);

$statusBadgeMap = [
    'Selesai' => ['class' => 'admin-status-completed', 'label' => 'Selesai'],
    'Aktif' => ['class' => 'admin-status-active', 'label' => 'Aktif'],
    'Menunggu Verifikasi' => ['class' => 'admin-status-pending', 'label' => 'Menunggu Verifikasi'],
    'Belum Dibayar' => ['class' => 'admin-status-unpaid', 'label' => 'Belum Dibayar'],
    'Dibatalkan' => ['class' => 'admin-status-cancelled', 'label' => 'Dibatalkan'],
    'Ditolak' => ['class' => 'admin-status-rejected', 'label' => 'Ditolak'],
];

$statusOptionsList = [
    '' => 'Semua Status',
    'Berhasil' => 'Berhasil (Aktif/Selesai)',
    'Aktif' => 'Aktif',
    'Selesai' => 'Selesai',
    'Menunggu Verifikasi' => 'Menunggu Verifikasi',
    'Belum Dibayar' => 'Belum Dibayar',
    'Dibatalkan' => 'Dibatalkan',
    'Ditolak' => 'Ditolak',
];
$activeStatusLabel = $statusOptionsList[$statusFilter] ?? 'Semua Status';

$activePackageLabel = 'Semua Paket';
foreach ($allPackages as $pkgItem) {
    if ((int) $pkgItem['package_id'] === $packageIdFilter) {
        $activePackageLabel = $pkgItem['package_name'];
        break;
    }
}

$quickDropdownPackages = array_slice($allPackages, 0, 4);
$isFiltered = ($statusFilter !== '' || $packageIdFilter > 0 || $searchQuery !== '' || $sort !== 'date' || $dir !== 'desc');
?>
<div class="d-flex flex-column flex-xl-row align-items-xl-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Laporan Transaksi</h1>
        <p class="admin-page-subtitle mb-0">Riwayat pesanan dan pembayaran pelanggan</p>
    </div>

    <div class="d-flex flex-wrap align-items-end gap-2">
        <form action="index.php" method="GET" class="d-flex flex-wrap align-items-end gap-2">
            <input type="hidden" name="page" value="admin-reports">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
            <?php if ($statusFilter !== ''): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
            <?php endif; ?>
            <?php if ($packageIdFilter > 0): ?>
                <input type="hidden" name="package_id" value="<?= (int) $packageIdFilter ?>">
            <?php endif; ?>
            <?php if ($searchQuery !== ''): ?>
                <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
            <?php endif; ?>

            <div class="<?= $currentPreset === 'custom' ? '' : 'd-none' ?> d-flex flex-wrap align-items-end gap-2" id="reportCustomDateGroup">
                <div>
                    <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control form-control-sm admin-period-date">
                </div>
                <div>
                    <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control form-control-sm admin-period-date">
                </div>
                <button type="submit" class="btn btn-sm admin-btn-primary px-3">Terapkan</button>
            </div>

            <div>
                <select name="period" class="form-select form-select-sm admin-period-select" data-custom-target="reportCustomDateGroup">
                    <option value="12w" <?= $currentPreset === '12w' ? 'selected' : '' ?>>12 minggu terakhir</option>
                    <option value="4w" <?= $currentPreset === '4w' ? 'selected' : '' ?>>4 minggu terakhir</option>
                    <option value="month" <?= $currentPreset === 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                    <option value="all" <?= $currentPreset === 'all' ? 'selected' : '' ?>>Semua Periode</option>
                    <option value="custom" <?= $currentPreset === 'custom' ? 'selected' : '' ?>>Rentang Kustom</option>
                </select>
            </div>
        </form>

        <div class="dropdown">
            <button class="btn btn-sm admin-export-btn dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
                <span>Export to Excel/CSV</span>
            </button>
            <ul class="dropdown-menu admin-filter-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item admin-filter-item d-flex align-items-center gap-2" href="<?= htmlspecialchars($exportExcelUrl) ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i>
                        <span>Export Excel (.xlsx)</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item admin-filter-item d-flex align-items-center gap-2" href="<?= htmlspecialchars($exportCsvUrl) ?>">
                        <i class="bi bi-filetype-csv text-success"></i>
                        <span>Export CSV (.csv)</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Total Transaksi</div>
            <div class="admin-kpi-value"><?= (int) $kpi['total_transactions'] ?></div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Berhasil</div>
            <div class="d-flex align-items-baseline gap-2">
                <span class="admin-kpi-value"><?= (int) $kpi['successful_count'] ?></span>
                <span class="text-muted small fw-medium">&middot; <?= number_format((float) $kpi['success_rate'], 1, ',', '.') ?>%</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="admin-kpi-label mb-1">Total Pendapatan</div>
            <div class="admin-kpi-value"><?= formatRupiah((int) $kpi['total_revenue']) ?></div>
        </div>
    </div>
</div>

<div class="admin-card p-3 p-lg-4">
    <form action="index.php" method="GET" id="reportFilterForm" class="d-flex flex-column flex-md-row align-items-stretch gap-2 mb-4">
        <input type="hidden" name="page" value="admin-reports">
        <input type="hidden" name="period" value="<?= htmlspecialchars($currentPreset) ?>">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <input type="hidden" name="dir" value="<?= htmlspecialchars($dir) ?>">
        <input type="hidden" name="status" id="reportStatusInput" value="<?= htmlspecialchars($statusFilter) ?>">
        <input type="hidden" name="package_id" id="reportPackageIdInput" value="<?= (int) $packageIdFilter ?>">
        <?php if ($currentPreset === 'custom'): ?>
            <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
            <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
        <?php endif; ?>

        <div class="position-relative flex-grow-1">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input
                type="search"
                name="q"
                maxlength="100"
                value="<?= htmlspecialchars($searchQuery) ?>"
                class="form-control ps-5 rounded-3 admin-search-input"
                placeholder="Cari nama pelanggan atau Order ID..."
                aria-label="Cari transaksi"
            >
        </div>

        <div class="dropdown admin-filter-dropdown">
            <button
                class="btn admin-filter-trigger w-100 d-flex align-items-center justify-content-between gap-2"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-funnel text-muted"></i>
                    <span class="text-truncate"><?= htmlspecialchars($activeStatusLabel) ?></span>
                </span>
                <i class="bi bi-chevron-down admin-filter-chevron"></i>
            </button>
            <ul class="dropdown-menu admin-filter-menu w-100 shadow-sm border-0">
                <?php foreach ($statusOptionsList as $stKey => $stLabel):
                    $isStActive = ($statusFilter === $stKey);
                ?>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item admin-filter-item d-flex align-items-center justify-content-between gap-2 <?= $isStActive ? 'active' : '' ?>"
                            data-report-filter-field="reportStatusInput"
                            data-report-filter-value="<?= htmlspecialchars($stKey) ?>"
                        >
                            <span><?= htmlspecialchars($stLabel) ?></span>
                            <?php if ($isStActive): ?>
                                <i class="bi bi-check2"></i>
                            <?php endif; ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="dropdown admin-filter-dropdown">
            <button
                class="btn admin-filter-trigger w-100 d-flex align-items-center justify-content-between gap-2"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-box-seam text-muted"></i>
                    <span class="text-truncate"><?= htmlspecialchars($activePackageLabel) ?></span>
                </span>
                <i class="bi bi-chevron-down admin-filter-chevron"></i>
            </button>
            <ul class="dropdown-menu admin-filter-menu dropdown-menu-end w-100 shadow-sm border-0">
                <li>
                    <button
                        type="button"
                        class="dropdown-item admin-filter-item d-flex align-items-center justify-content-between gap-2 <?= $packageIdFilter === 0 ? 'active' : '' ?>"
                        data-report-filter-field="reportPackageIdInput"
                        data-report-filter-value="0"
                    >
                        <span>Semua Paket</span>
                        <?php if ($packageIdFilter === 0): ?>
                            <i class="bi bi-check2"></i>
                        <?php endif; ?>
                    </button>
                </li>
                <?php foreach ($quickDropdownPackages as $pkg):
                    $pkgId = (int) $pkg['package_id'];
                    $isPkgActive = ($packageIdFilter === $pkgId);
                ?>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item admin-filter-item d-flex align-items-center justify-content-between gap-2 <?= $isPkgActive ? 'active' : '' ?>"
                            data-report-filter-field="reportPackageIdInput"
                            data-report-filter-value="<?= $pkgId ?>"
                        >
                            <span class="text-truncate"><?= htmlspecialchars($pkg['package_name']) ?></span>
                            <?php if ($isPkgActive): ?>
                                <i class="bi bi-check2 flex-shrink-0"></i>
                            <?php endif; ?>
                        </button>
                    </li>
                <?php endforeach; ?>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <button
                        type="button"
                        class="dropdown-item admin-filter-item admin-search-more-item d-flex align-items-center gap-2"
                        data-bs-toggle="modal"
                        data-bs-target="#reportPackageWizardModal"
                    >
                        <i class="bi bi-search"></i>
                        <span>Search More...</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="d-md-none">
            <select class="form-select rounded-3 admin-search-input" aria-label="Urutkan transaksi" onchange="window.location.href = this.value">
                <?php foreach ($sortOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt['url']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>
                        Urutkan: <?= htmlspecialchars($opt['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($isFiltered): ?>
            <a href="index.php?page=admin-reports&period=<?= urlencode($currentPreset) ?>" class="btn btn-outline-secondary rounded-3 d-inline-flex align-items-center justify-content-center px-3">
                <i class="bi bi-x-lg me-1"></i>Reset
            </a>
        <?php endif; ?>
    </form>

    <?php if (empty($transactions)): ?>
        <div class="text-center py-5">
            <div class="admin-empty-icon mx-auto mb-3">
                <i class="bi bi-receipt"></i>
            </div>
            <h5 class="fw-bold mb-1">Tidak ada data pada periode ini</h5>
            <p class="text-muted small mb-0">
                Belum ada riwayat transaksi yang sesuai dengan rentang tanggal atau filter yang dipilih.
            </p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-orders align-middle mb-0">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th <?= $col['width'] !== '' ? 'style="width: ' . htmlspecialchars($col['width']) . ';"' : '' ?> aria-sort="<?= htmlspecialchars($col['aria_sort']) ?>">
                                <a href="<?= htmlspecialchars($col['url']) ?>" class="admin-sort-link <?= $col['active'] ? 'active' : '' ?>">
                                    <span><?= htmlspecialchars($col['label']) ?></span>
                                    <i class="bi <?= htmlspecialchars($col['icon']) ?>"></i>
                                </a>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trx):
                        $ordCode = '#ORD-' . str_pad((string) $trx['order_id'], 3, '0', STR_PAD_LEFT);
                        $badgeInfo = $statusBadgeMap[$trx['status']] ?? ['class' => 'admin-status-unpaid', 'label' => $trx['status']];
                        $trxDate = formatDateIndo(date('Y-m-d', strtotime((string) $trx['created_at'])), true);
                    ?>
                        <tr>
                            <td class="admin-ord-id-cell fw-bold">
                                <a href="index.php?page=admin-order-detail&id=<?= (int) $trx['order_id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($ordCode) ?>
                                </a>
                            </td>
                            <td class="admin-ord-cust-cell" data-label="Nama Pelanggan"><?= htmlspecialchars($trx['customer_name']) ?></td>
                            <td class="admin-ord-date-cell" data-label="Tanggal"><?= htmlspecialchars($trxDate) ?></td>
                            <td class="admin-ord-pkg-cell" data-label="Paket"><?= htmlspecialchars($trx['package_name']) ?></td>
                            <td class="admin-ord-price-cell fw-semibold" data-label="Total Harga"><?= formatRupiah((int) $trx['total_price']) ?></td>
                            <td class="admin-ord-status-cell">
                                <span class="admin-status-pill <?= htmlspecialchars($badgeInfo['class']) ?>">
                                    <span class="status-dot"></span>
                                    <?= htmlspecialchars($badgeInfo['label']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-3 border-top">
            <span class="text-muted small">
                Menampilkan <?= $startRow ?>-<?= $endRow ?> dari <?= $totalTransactions ?> transaksi
            </span>

            <?php if ($totalPages > 1): ?>
                <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                    <?php
                    $prevQuery = http_build_query(array_merge($baseParams, ['p' => max(1, $currentPageNum - 1)]));
                    $nextQuery = http_build_query(array_merge($baseParams, ['p' => min($totalPages, $currentPageNum + 1)]));
                    ?>
                    <a href="index.php?<?= htmlspecialchars($prevQuery) ?>" class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($p = 1; $p <= $totalPages; $p++):
                        $pQuery = http_build_query(array_merge($baseParams, ['p' => $p]));
                    ?>
                        <a href="index.php?<?= htmlspecialchars($pQuery) ?>" class="btn btn-sm admin-page-btn <?= $p === $currentPageNum ? 'active' : '' ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                    <a href="index.php?<?= htmlspecialchars($nextQuery) ?>" class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="reportPackageWizardModal" tabindex="-1" aria-labelledby="reportPackageWizardLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-2 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="reportPackageWizardLabel">Pilih Paket Langganan</h5>
                    <p class="text-muted small mb-0">Cari dan pilih paket untuk memfilter laporan transaksi</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 pt-2 pb-4">
                <div class="position-relative mb-3">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input
                        type="search"
                        id="reportPkgWizardSearch"
                        class="form-control ps-5 rounded-3 admin-search-input"
                        placeholder="Ketik nama paket, durasi hari, hari berlaku, atau harga..."
                        aria-label="Cari paket di daftar"
                    >
                </div>

                <div class="admin-pkg-wizard-list d-flex flex-column gap-2" id="reportPkgWizardList">
                    <button
                        type="button"
                        class="admin-pkg-wizard-item d-flex align-items-center justify-content-between gap-3 text-start <?= $packageIdFilter === 0 ? 'active' : '' ?>"
                        data-report-filter-field="reportPackageIdInput"
                        data-report-filter-value="0"
                        data-pkg-search="semua paket tampilkan seluruh transaksi"
                    >
                        <div class="d-flex align-items-center gap-3 min-w-0">
                            <div class="admin-pkg-wizard-icon">
                                <i class="bi bi-grid"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark">Semua Paket</div>
                                <div class="text-muted small">Tampilkan transaksi dari seluruh paket langganan</div>
                            </div>
                        </div>
                        <?php if ($packageIdFilter === 0): ?>
                            <span class="admin-status-pill admin-status-active flex-shrink-0">
                                <i class="bi bi-check2"></i>Terpilih
                            </span>
                        <?php endif; ?>
                    </button>

                    <?php foreach ($allPackages as $pkg):
                        $pkgId = (int) $pkg['package_id'];
                        $isSelected = ($packageIdFilter === $pkgId);
                        $pkgImg = !empty($pkg['image']) && is_file(__DIR__ . '/../../../public/assets/img/' . $pkg['image'])
                            ? './assets/img/' . $pkg['image']
                            : './assets/img/nasi-lemak.webp';
                        $daysText = str_replace('-', ' - ', (string) $pkg['applicable_days']);
                        $priceText = formatRupiah((int) $pkg['price']);
                        $searchKeywords = strtolower($pkg['package_name'] . ' ' . $pkg['duration_days'] . ' hari ' . $daysText . ' ' . $priceText . ' ' . ($pkg['description'] ?? ''));
                    ?>
                        <button
                            type="button"
                            class="admin-pkg-wizard-item d-flex align-items-center justify-content-between gap-3 text-start <?= $isSelected ? 'active' : '' ?>"
                            data-report-filter-field="reportPackageIdInput"
                            data-report-filter-value="<?= $pkgId ?>"
                            data-pkg-search="<?= htmlspecialchars($searchKeywords) ?>"
                        >
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <img src="<?= htmlspecialchars($pkgImg) ?>" alt="<?= htmlspecialchars($pkg['package_name']) ?>" class="admin-pkg-wizard-thumb flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($pkg['package_name']) ?></span>
                                        <span class="badge rounded-pill bg-light text-dark border fw-medium">
                                            <?= (int) $pkg['duration_days'] ?> hari &bull; <?= htmlspecialchars($daysText) ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small text-truncate"><?= htmlspecialchars($pkg['description'] ?? '') ?></div>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold text-success small"><?= htmlspecialchars($priceText) ?></div>
                                <?php if ($isSelected): ?>
                                    <span class="admin-status-pill admin-status-active mt-1">
                                        <i class="bi bi-check2"></i>Terpilih
                                    </span>
                                <?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div id="reportPkgWizardEmpty" class="text-center py-4 text-muted d-none">
                    <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                    <p class="small mb-0">Tidak ditemukan paket yang cocok dengan kata kunci tersebut.</p>
                </div>
            </div>
        </div>
    </div>
</div>
