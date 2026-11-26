<?php
$startRow = $totalOrders > 0 ? $offset + 1 : 0;
$endRow = min($offset + $perPage, $totalOrders);
$bulanSingkat = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$statusTabs = ['Semua', 'Belum Dibayar', 'Menunggu Verifikasi', 'Aktif', 'Selesai', 'Dibatalkan', 'Ditolak'];

$formatTgl = static function (string $dateStr, bool $withTime = false) use ($bulanSingkat): string {
    $dt = new DateTimeImmutable($dateStr);
    $tgl = sprintf('%02d %s %s', (int) $dt->format('d'), $bulanSingkat[(int) $dt->format('n')], $dt->format('Y'));
    return $withTime ? $tgl . ', ' . $dt->format('H:i') : $tgl;
};
?>

<div class="mb-4">
    <h1 class="admin-page-title mb-1">Daftar Pesanan</h1>
    <p class="text-muted mb-0">Kelola semua pesanan pelanggan dan pantau status pembayarannya.</p>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-3">Daftar Pesanan</h5>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php foreach ($statusTabs as $tab):
                    $isActive = ($statusFilter === $tab);
                ?>
                    <a href="index.php?page=admin-orders&status=<?= urlencode($tab) ?>&q=<?= urlencode($search) ?>"
                       class="btn admin-view-pill <?= $isActive ? 'active' : '' ?>">
                        <?= htmlspecialchars($tab) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between justify-content-lg-end gap-3 pt-2 pt-lg-0 border-lg-0">
            <div class="text-lg-end">
                <div class="text-muted small">Total Pesanan</div>
                <div class="fw-bold fs-4 text-dark lh-1"><?= $totalOrders ?></div>
            </div>
            <div class="admin-order-total-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <?php if ($search !== ''): ?>
        <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2 mb-3">
            <span class="small text-muted">Menampilkan hasil pencarian untuk: <strong class="text-dark"><?= htmlspecialchars($search) ?></strong></span>
            <a href="index.php?page=admin-orders&status=<?= urlencode($statusFilter) ?>" class="btn btn-sm btn-link text-decoration-none p-0">Reset</a>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <p class="mb-0 fw-medium">Belum ada pesanan</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-orders mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Paket</th>
                        <th>Tanggal Mulai</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord):
                        $statusBadgeClass = match ($ord['status']) {
                            'Menunggu Verifikasi' => 'admin-status-pending',
                            'Aktif' => 'admin-status-active',
                            'Selesai' => 'admin-status-completed',
                            'Ditolak' => 'admin-status-rejected',
                            'Dibatalkan' => 'admin-status-cancelled',
                            default => 'admin-status-unpaid',
                        };
                    ?>
                        <tr>
                            <td class="admin-ord-id-cell">
                                <div class="fw-bold text-dark"><?= sprintf('#ORD-%03d', (int) $ord['order_id']) ?></div>
                                <div class="text-muted" style="font-size: 11.5px;"><?= htmlspecialchars($formatTgl($ord['created_at'], true)) ?></div>
                            </td>
                            <td class="admin-ord-cust-cell" data-label="Pelanggan">
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div class="text-muted" style="font-size: 11.5px;"><?= htmlspecialchars($ord['customer_phone'] ?? '-') ?></div>
                            </td>
                            <td class="admin-ord-pkg-cell" data-label="Paket">
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['package_name']) ?></div>
                                <div class="text-muted" style="font-size: 11.5px;">(<?= (int) $ord['duration_days'] ?> hari • <?= htmlspecialchars($ord['slot']) ?>)</div>
                            </td>
                            <td class="admin-ord-date-cell" data-label="Tanggal Mulai">
                                <span class="text-dark"><?= htmlspecialchars($formatTgl($ord['start_date'])) ?></span>
                            </td>
                            <td class="admin-ord-price-cell" data-label="Total Harga">
                                <span class="fw-semibold text-dark"><?= formatRupiah((int) $ord['total_price']) ?></span>
                            </td>
                            <td class="admin-ord-status-cell">
                                <span class="admin-status-pill <?= $statusBadgeClass ?>">
                                    <span class="status-dot"></span>
                                    <?= htmlspecialchars($ord['status']) ?>
                                </span>
                            </td>
                            <td class="text-end admin-ord-action-cell">
                                <a href="index.php?page=admin-order-detail&id=<?= (int) $ord['order_id'] ?>" class="btn btn-sm admin-detail-btn">
                                    <span>Detail</span>
                                    <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-3 border-top">
            <span class="text-muted small text-center text-sm-start">
                <?php if ($totalOrders <= $perPage): ?>
                    Menampilkan <?= $totalOrders ?> dari <?= $totalOrders ?> pesanan
                <?php else: ?>
                    Menampilkan <?= $startRow ?>-<?= $endRow ?> dari <?= $totalOrders ?> pesanan
                <?php endif; ?>
            </span>

            <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                <a href="index.php?page=admin-orders&status=<?= urlencode($statusFilter) ?>&q=<?= urlencode($search) ?>&p=<?= max(1, $currentPageNum - 1) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>"
                   aria-label="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=admin-orders&status=<?= urlencode($statusFilter) ?>&q=<?= urlencode($search) ?>&p=<?= $i ?>"
                       class="btn btn-sm admin-page-btn <?= $i === $currentPageNum ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <a href="index.php?page=admin-orders&status=<?= urlencode($statusFilter) ?>&q=<?= urlencode($search) ?>&p=<?= min($totalPages, $currentPageNum + 1) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>"
                   aria-label="Halaman Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
