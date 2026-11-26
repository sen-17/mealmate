<?php
$currentPreset = $periodInfo['preset'];
$startDate = $periodInfo['start_date'];
$endDate = $periodInfo['end_date'];
$hasPeriodData = ($summary['total_orders'] > 0);

$maxPointRevenue = 0;
foreach ($salesTrend as $pt) {
    if ($pt['revenue'] > $maxPointRevenue) {
        $maxPointRevenue = $pt['revenue'];
    }
}
$useMillions = ($maxPointRevenue >= 2000000);
$unitDivisor = $useMillions ? 1000000 : 1000;
$unitLabel = $useMillions ? 'Rp juta' : 'Rp ribu';

$maxScaled = $maxPointRevenue > 0 ? ($maxPointRevenue / $unitDivisor) : 400;
$stepVal = $useMillions ? max(1, (int) ceil($maxScaled / 4)) : max(50, (int) (ceil(($maxScaled / 4) / 50) * 50));
$yMax = $stepVal * 4;

$svgW = 620;
$svgH = 230;
$padLeft = 46;
$padRight = 20;
$padTop = 18;
$padBottom = 38;
$plotW = $svgW - $padLeft - $padRight;
$plotH = $svgH - $padTop - $padBottom;

$pointCount = count($salesTrend);
$coords = [];
foreach ($salesTrend as $idx => $pt) {
    $x = $pointCount > 1
        ? $padLeft + ($idx / ($pointCount - 1)) * $plotW
        : $padLeft + ($plotW / 2);
    $scaledVal = $pt['revenue'] / $unitDivisor;
    $ratio = $yMax > 0 ? min(1, max(0, $scaledVal / $yMax)) : 0;
    $y = $padTop + $plotH - ($ratio * $plotH);
    $coords[] = [
        'x' => round($x, 1),
        'y' => round($y, 1),
        'label' => $pt['label'],
        'revenue' => $pt['revenue'],
        'orders' => $pt['order_count'],
    ];
}

$pointCount = count($coords);
$smoothLinePath = '';
$smoothAreaPath = '';

if ($pointCount > 0) {
    $smoothLinePath = "M {$coords[0]['x']},{$coords[0]['y']}";
    for ($i = 0; $i < $pointCount - 1; $i++) {
        $p0 = $coords[$i];
        $p1 = $coords[$i + 1];
        $midX = round(($p0['x'] + $p1['x']) / 2, 1);
        $smoothLinePath .= " C {$midX},{$p0['y']} {$midX},{$p1['y']} {$p1['x']},{$p1['y']}";
    }

    $firstX = $coords[0]['x'];
    $lastX = $coords[$pointCount - 1]['x'];
    $bottomY = $padTop + $plotH;
    $smoothAreaPath = "{$smoothLinePath} L {$lastX},{$bottomY} L {$firstX},{$bottomY} Z";
}
?>
<div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">
            Selamat datang, <span style="color: #D96B38;">Admin</span>
        </h1>
        <p class="admin-page-subtitle mb-0">Ringkasan katering MealMate - <?= htmlspecialchars($todayLabel) ?></p>
    </div>

    <form action="index.php" method="GET" class="d-flex flex-wrap align-items-end gap-2">
        <input type="hidden" name="page" value="admin-dashboard">
        <div class="<?= $currentPreset === 'custom' ? '' : 'd-none' ?> d-flex flex-wrap align-items-end gap-2" id="dashboardCustomDateGroup">
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
            <select name="period" class="form-select form-select-sm admin-period-select" data-custom-target="dashboardCustomDateGroup">
                <option value="12w" <?= $currentPreset === '12w' ? 'selected' : '' ?>>12 minggu</option>
                <option value="4w" <?= $currentPreset === '4w' ? 'selected' : '' ?>>4 minggu</option>
                <option value="month" <?= $currentPreset === 'month' ? 'selected' : '' ?>>Bulan Ini</option>
                <option value="all" <?= $currentPreset === 'all' ? 'selected' : '' ?>>Semua Periode</option>
                <option value="custom" <?= $currentPreset === 'custom' ? 'selected' : '' ?>>Rentang Kustom</option>
            </select>
        </div>
    </form>
</div>

<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="admin-card admin-kpi-card p-3 p-lg-4 h-100">
            <div class="d-flex align-items-start gap-3">
                <div class="admin-kpi-icon">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="admin-kpi-label">Pendapatan (<?= htmlspecialchars($periodInfo['label']) ?>)</div>
                    <div class="admin-kpi-value"><?= formatRupiah($summary['total_revenue']) ?></div>
                    <div class="admin-kpi-sub text-success fw-semibold">
                        <?php if ($summary['best_package_sold'] > 0): ?>
                            Terlaris: <?= htmlspecialchars($summary['best_package']) ?> (<?= (int) $summary['best_package_sold'] ?> pesanan)
                        <?php else: ?>
                            Belum ada transaksi terbayar pada periode ini
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card admin-kpi-card p-3 p-lg-4 h-100">
            <div class="d-flex align-items-start gap-3">
                <div class="admin-kpi-icon">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="admin-kpi-label">Pesanan Aktif</div>
                    <div class="admin-kpi-value"><?= (int) $summary['total_orders'] ?></div>
                    <div class="admin-kpi-sub text-muted">
                        <?= (int) $summary['current_pending_orders'] ?> pesanan menunggu verifikasi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="admin-card admin-kpi-card p-3 p-lg-4 h-100">
            <div class="d-flex align-items-start gap-3">
                <div class="admin-kpi-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="admin-kpi-label">Langganan aktif</div>
                    <div class="admin-kpi-value"><?= (int) $summary['current_active_subscriptions'] ?></div>
                    <div class="admin-kpi-sub text-success fw-semibold">
                        +<?= (int) $summary['new_active_this_week'] ?> minggu ini
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="admin-card p-3 p-lg-4 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="admin-section-heading mb-0">Tren Penjualan</h2>
                    <p class="admin-section-subtext mb-0">Total tagihan pesanan per minggu (<?= htmlspecialchars($unitLabel) ?>)</p>
                </div>
                <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium">
                    <?= htmlspecialchars(formatDateIndo($startDate, true)) ?> - <?= htmlspecialchars(formatDateIndo($endDate, true)) ?>
                </span>
            </div>

            <?php if (!$hasPeriodData): ?>
                <div class="text-center py-5 my-auto">
                    <div class="admin-empty-icon mx-auto mb-2">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Tidak ada data pada periode ini</h6>
                    <p class="text-muted small mb-0">Belum ada transaksi pesanan yang tercatat pada rentang tanggal yang dipilih.</p>
                </div>
            <?php else: ?>
                <div class="admin-chart-wrapper">
                    <svg viewBox="0 0 <?= $svgW ?> <?= $svgH ?>" class="admin-trend-svg" role="img" aria-label="Grafik Tren Penjualan">
                        <defs>
                            <linearGradient id="salesTrendAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#1F5A3F" stop-opacity="0.22" />
                                <stop offset="100%" stop-color="#1F5A3F" stop-opacity="0.02" />
                            </linearGradient>
                        </defs>

                        <?php for ($step = 0; $step <= 4; $step++):
                            $val = $step * $stepVal;
                            $yPos = round($padTop + $plotH - (($step / 4) * $plotH), 1);
                        ?>
                            <line x1="<?= $padLeft ?>" y1="<?= $yPos ?>" x2="<?= $svgW - $padRight ?>" y2="<?= $yPos ?>" stroke="#EAE6DC" stroke-width="1" />
                            <text x="<?= $padLeft - 10 ?>" y="<?= $yPos + 4 ?>" text-anchor="end" class="admin-chart-axis-text"><?= $val ?></text>
                        <?php endfor; ?>

                        <?php if ($smoothAreaPath !== ''): ?>
                            <path d="<?= htmlspecialchars($smoothAreaPath) ?>" fill="url(#salesTrendAreaGrad)" />
                            <path d="<?= htmlspecialchars($smoothLinePath) ?>" fill="none" stroke="#1F5A3F" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <?php endif; ?>

                        <?php foreach ($coords as $c): ?>
                            <circle cx="<?= $c['x'] ?>" cy="<?= $c['y'] ?>" r="4.5" class="admin-chart-dot" fill="#1F5A3F" stroke="#FFFFFF" stroke-width="2">
                                <title><?= htmlspecialchars($c['label'] . ': ' . formatRupiah($c['revenue']) . ' (' . $c['orders'] . ' pesanan)') ?></title>
                            </circle>
                            <text x="<?= $c['x'] ?>" y="<?= $svgH - 10 ?>" text-anchor="middle" class="admin-chart-axis-text"><?= htmlspecialchars($c['label']) ?></text>
                        <?php endforeach; ?>
                    </svg>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="admin-card p-3 p-lg-4 h-100">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="admin-section-heading mb-0">Ulasan Terbaru</h2>
                    <p class="admin-section-subtext mb-0">
                        <?php if ($reviewsSummary['total_reviews'] > 0): ?>
                            Rata-rata <?= number_format($reviewsSummary['avg_rating'], 1, ',', '.') ?> dari 5
                        <?php else: ?>
                            Belum ada penilaian
                        <?php endif; ?>
                    </p>
                </div>
                <a href="index.php?page=admin-reviews" class="admin-link-sm text-decoration-none flex-shrink-0">Lihat semua</a>
            </div>

            <?php if (empty($reviewsSummary['items'])): ?>
                <div class="text-center py-5">
                    <div class="admin-empty-icon mx-auto mb-2">
                        <i class="bi bi-chat-square-quote"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Belum ada ulasan</h6>
                    <p class="text-muted small mb-0">Ulasan pelanggan untuk pesanan yang telah selesai akan tampil di sini.</p>
                </div>
            <?php else: ?>
                <div class="admin-review-feed d-flex flex-column">
                    <?php foreach ($reviewsSummary['items'] as $idx => $rev): ?>
                        <div class="admin-review-feed-item <?= $idx < count($reviewsSummary['items']) - 1 ? 'pb-3 mb-3 border-bottom' : '' ?>">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <strong class="admin-review-author text-truncate"><?= htmlspecialchars($rev['customer_name']) ?></strong>
                                <div class="admin-review-stars flex-shrink-0">
                                    <?= renderStars((float) $rev['rating']) ?>
                                </div>
                            </div>
                            <p class="admin-review-comment mb-1">
                                <?= htmlspecialchars($rev['comment'] ?: 'Pelanggan memberikan rating bintang tanpa komentar.') ?>
                            </p>
                            <div class="admin-review-meta">
                                <?= htmlspecialchars($rev['package_name']) ?> &bull; <?= htmlspecialchars(formatDateIndo(date('Y-m-d', strtotime((string) $rev['created_at'])), true)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="admin-card p-3 p-lg-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="admin-section-heading mb-0">Menunggu Verifikasi Pembayaran</h2>
            <p class="admin-section-subtext mb-0">Pesanan berstatus Menunggu Verifikasi dengan bukti bayar terunggah</p>
        </div>
        <a href="index.php?page=admin-orders&status=<?= urlencode('Menunggu Verifikasi') ?>" class="admin-link-sm text-decoration-none">
            Lihat semua pesanan
        </a>
    </div>

    <?php if (empty($pendingOrders)): ?>
        <div class="text-center py-4">
            <p class="text-muted small mb-0">Tidak ada pesanan yang sedang menunggu verifikasi pembayaran.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-orders align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 110px;">Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Paket</th>
                        <th style="width: 100px;">Slot</th>
                        <th style="width: 120px;">Mulai</th>
                        <th style="width: 140px;">Total</th>
                        <th style="width: 170px;">Status</th>
                        <th class="text-end" style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingOrders as $ord):
                        $ordCode = '#ORD-' . str_pad((string) $ord['order_id'], 3, '0', STR_PAD_LEFT);
                    ?>
                        <tr>
                            <td class="admin-ord-id-cell fw-bold"><?= htmlspecialchars($ordCode) ?></td>
                            <td class="admin-ord-cust-cell" data-label="Pelanggan"><?= htmlspecialchars($ord['customer_name']) ?></td>
                            <td class="admin-ord-pkg-cell" data-label="Paket"><?= htmlspecialchars($ord['package_name']) ?></td>
                            <td class="admin-ord-date-cell" data-label="Slot"><?= htmlspecialchars($ord['slot']) ?></td>
                            <td class="admin-ord-date-cell" data-label="Mulai"><?= htmlspecialchars(formatDateIndo($ord['start_date'], true)) ?></td>
                            <td class="admin-ord-price-cell fw-semibold" data-label="Total"><?= formatRupiah((int) $ord['total_price']) ?></td>
                            <td class="admin-ord-status-cell">
                                <span class="admin-status-pill admin-status-pending">
                                    <span class="status-dot"></span>Menunggu Verifikasi
                                </span>
                            </td>
                            <td class="text-end admin-ord-action-cell">
                                <a href="index.php?page=admin-order-detail&id=<?= (int) $ord['order_id'] ?>" class="btn btn-sm admin-detail-btn">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
