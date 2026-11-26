<?php
$orderCode = '#ORD-' . str_pad((string) $order['order_id'], 3, '0', STR_PAD_LEFT);
$packageImg = !empty($order['package_image']) ? './assets/img/' . $order['package_image'] : './assets/img/home-package-card-placeholder.webp';
$status = $order['status'];
$isUnpaid = ($status === 'Belum Dibayar' && empty($order['payment_id']));

$slotTimeMap = [
    'Pagi' => '06.00 - 09.00 WIB',
    'Siang' => '11.00 - 13.00 WIB',
    'Malam' => '17.00 - 20.00 WIB',
];
$slotTimeText = $slotTimeMap[$order['slot']] ?? '08.00 - 10.00 WIB';

$statusBannerMap = [
    'Belum Dibayar' => [
        'theme' => 'unpaid',
        'icon' => 'bi-wallet2',
        'title' => 'Belum Dibayar',
        'desc' => 'Pesanan Anda berhasil disimpan. Silakan lakukan pembayaran dan unggah bukti transfer agar pesanan dapat diproses.',
    ],
    'Menunggu Verifikasi' => [
        'theme' => 'pending',
        'icon' => 'bi-hourglass-split',
        'title' => 'Menunggu Verifikasi',
        'desc' => 'Bukti pembayaran Anda telah diterima dan sedang diperiksa oleh Admin MealMate.',
    ],
    'Aktif' => [
        'theme' => 'active',
        'icon' => 'bi-truck',
        'title' => 'Aktif',
        'desc' => 'Pembayaran telah terverifikasi. Paket katering Anda sedang dalam masa pengantaran sesuai jadwal slot.',
    ],
    'Selesai' => [
        'theme' => 'completed',
        'icon' => 'bi-check-lg',
        'title' => 'Selesai',
        'desc' => 'Pesanan Anda telah selesai. Terima kasih telah mempercayakan MealMate!',
    ],
    'Ditolak' => [
        'theme' => 'rejected',
        'icon' => 'bi-exclamation-triangle',
        'title' => 'Ditolak',
        'desc' => 'Bukti pembayaran Anda ditolak oleh Admin. Silakan periksa catatan penolakan di bawah dan buat pesanan baru.',
    ],
    'Dibatalkan' => [
        'theme' => 'cancelled',
        'icon' => 'bi-x-lg',
        'title' => 'Dibatalkan',
        'desc' => 'Pesanan ini telah dibatalkan.',
    ],
];
$banner = $statusBannerMap[$status] ?? $statusBannerMap['Belum Dibayar'];
$createdAtTs = strtotime((string) $order['created_at']);
$createdAtFormatted = $createdAtTs ? formatDateIndo(date('Y-m-d', $createdAtTs), true) . ', ' . date('H:i', $createdAtTs) : $order['created_at'];
$backUrl = file_exists(__DIR__ . '/../../controllers/orders.php') ? 'index.php?page=orders' : 'index.php?page=menu#packages-section';
?>
<main class="order-detail-page py-3">
    <div class="container">
        <div class="order-detail-top mb-3">
            <a href="<?= htmlspecialchars($backUrl) ?>" class="order-back-link text-decoration-none d-inline-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i>
                <h1 class="mb-0">Detail Pesanan <?= htmlspecialchars($orderCode) ?></h1>
            </a>
        </div>

        <div class="order-detail-card mx-auto p-3 p-md-4">
            <div class="order-status-banner theme-<?= htmlspecialchars($banner['theme']) ?> p-3 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="status-banner-icon flex-shrink-0">
                        <i class="bi <?= htmlspecialchars($banner['icon']) ?>"></i>
                    </div>
                    <div>
                        <h2 class="status-banner-title mb-1"><?= htmlspecialchars($banner['title']) ?></h2>
                        <p class="status-banner-desc mb-0"><?= htmlspecialchars($banner['desc']) ?></p>
                        <?php if ($status === 'Ditolak' && !empty($order['rejection_reason'])): ?>
                            <div class="mt-2 small text-danger fw-semibold">
                                Alasan Penolakan: "<?= htmlspecialchars($order['rejection_reason']) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="status-banner-date text-md-end flex-shrink-0">
                    <span class="d-block small text-muted">Dipesan Pada</span>
                    <strong class="small"><?= htmlspecialchars($createdAtFormatted) ?></strong>
                </div>
            </div>

            <div class="pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-calendar2-range text-success fs-5"></i>
                    <h3 class="order-subsection-title mb-0">Detail Pesanan</h3>
                </div>

                <div class="d-flex flex-column flex-sm-row align-items-start gap-3">
                    <img
                        src="<?= htmlspecialchars($packageImg) ?>"
                        alt="<?= htmlspecialchars($order['package_name']) ?>"
                        class="order-detail-pkg-img"
                    >
                    <div>
                        <h4 class="checkout-pkg-title mb-1"><?= htmlspecialchars($order['package_name']) ?></h4>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <span class="checkout-pkg-badge"><?= (int) $order['duration_days'] ?> hari</span>
                            <span class="checkout-pkg-badge"><?= htmlspecialchars(str_replace('-', ' - ', $order['applicable_days'])) ?></span>
                        </div>
                        <p class="checkout-pkg-desc mb-0"><?= htmlspecialchars($order['package_description']) ?></p>
                    </div>
                </div>
            </div>

            <div class="row g-3 pb-3 mb-3 border-bottom">
                <div class="col-12 col-md-5 border-md-end">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-clock text-success fs-5 mt-1"></i>
                        <div>
                            <h4 class="order-info-heading mb-1">Jadwal Slot &amp; Periode</h4>
                            <span class="order-slot-pill mb-1 d-inline-flex align-items-center gap-1">
                                <i class="bi bi-sun"></i>
                                <span><?= htmlspecialchars($order['slot']) ?></span>
                            </span>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($slotTimeText) ?></p>
                            <div class="small text-dark">
                                <div><strong>Mulai:</strong> <?= htmlspecialchars(formatDateIndo($order['start_date'])) ?></div>
                                <div><strong>Selesai:</strong> <?= htmlspecialchars(formatDateIndo($order['end_date'])) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-7 ps-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt text-success fs-5 mt-1"></i>
                        <div>
                            <h4 class="order-info-heading mb-1">Alamat Pengantaran</h4>
                            <p class="fw-semibold text-success mb-1"><?= htmlspecialchars($order['customer_name']) ?> (<?= htmlspecialchars($order['customer_phone']) ?>)</p>
                            <p class="order-address-text mb-1"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
                            <?php if (!empty($order['notes'])): ?>
                                <div class="small text-muted bg-light rounded-3 px-2 py-1 border mt-1">
                                    <strong>Catatan:</strong> <?= htmlspecialchars($order['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-payment-box p-3 rounded-4">
                <h3 class="order-subsection-title mb-2">Rincian Pembayaran</h3>

                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span class="text-muted">Subtotal (<?= htmlspecialchars($order['package_name']) ?>)</span>
                    <span class="fw-semibold text-dark"><?= formatRupiah((int) $order['total_price']) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom small">
                    <span class="text-muted">Ongkos Kirim</span>
                    <span class="fw-semibold text-success">Gratis (Rp 0)</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="checkout-total-label">Total Pembayaran</span>
                    <span class="checkout-total-value"><?= formatRupiah((int) $order['total_price']) ?></span>
                </div>

                <?php if ($isUnpaid): ?>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                        <a href="index.php?page=payment&order_id=<?= (int) $order['order_id'] ?>" class="btn checkout-submit-btn flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-upload"></i>
                            <span>Lanjutkan Pembayaran &amp; Unggah Bukti</span>
                        </a>
                        <button
                            type="button"
                            class="btn btn-outline-danger rounded-3 px-4 py-2 fw-semibold"
                            data-bs-toggle="modal"
                            data-bs-target="#cancelOrderModal"
                        >
                            <i class="bi bi-x-circle me-1"></i>Batalkan Pesanan
                        </button>
                    </div>
                <?php elseif ($status === 'Selesai' && !$isReviewed): ?>
                    <div class="mt-3">
                        <a href="index.php?page=review&order_id=<?= (int) $order['order_id'] ?>" class="btn checkout-submit-btn w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-star-fill"></i>
                            <span>Berikan Ulasan</span>
                        </a>
                    </div>
                <?php elseif ($status === 'Ditolak'): ?>
                    <div class="mt-3">
                        <a href="index.php?page=checkout&package_id=<?= (int) $order['package_id'] ?>" class="btn checkout-submit-btn w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Pesan Ulang Paket Ini</span>
                        </a>
                    </div>
                <?php elseif ($status === 'Selesai' && $hasReviews): ?>
                    <div class="mt-3">
                        <a href="index.php?page=review&order_id=<?= (int) $order['order_id'] ?>" class="btn checkout-submit-btn w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-chat-square-heart"></i>
                            <span>Lihat Ulasan Saya</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php if ($isUnpaid): ?>
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="index.php?page=order-detail&id=<?= (int) $order['order_id'] ?>" method="POST">
                <input type="hidden" name="action" value="cancel_order">
                <input type="hidden" name="order_id" value="<?= (int) $order['order_id'] ?>">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="cancelOrderModalLabel">
                        <i class="bi bi-exclamation-octagon me-2"></i>Konfirmasi Pembatalan Pesanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Apakah Anda yakin ingin membatalkan pesanan <strong><?= htmlspecialchars($orderCode) ?></strong> (<?= htmlspecialchars($order['package_name']) ?>)?
                    </p>
                    <div class="mb-2">
                        <label for="cancelReasonInput" class="form-label small fw-semibold text-muted">
                            Alasan Pembatalan (Opsional)
                        </label>
                        <textarea
                            id="cancelReasonInput"
                            name="cancel_reason"
                            rows="2"
                            class="form-control"
                            placeholder="Contoh: Ingin mengganti pilihan paket atau jadwal mulai"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">Ya, Batalkan Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
