<?php
$activeRatingValue = 0;
$activeRatingLabel = 'Semua Rating';
foreach ($ratingOptions as $opt) {
    if (!empty($opt['selected'])) {
        $activeRatingValue = (int) $opt['value'];
        $activeRatingLabel = (string) $opt['label'];
        break;
    }
}
?>
<div class="mb-4">
    <h1 class="admin-page-title mb-1">Ulasan Pelanggan</h1>
    <p class="text-muted mb-0">Kelola dan pantau semua ulasan dari pelanggan untuk meningkatkan kualitas layanan kami.</p>
</div>

<div class="admin-card p-3 p-md-4">
    <form action="index.php" method="get" id="reviewFilterForm" class="d-flex flex-column flex-md-row align-items-stretch gap-2 mb-4">
        <input type="hidden" name="page" value="admin-reviews">
        <input type="hidden" name="sort" value="<?= escape($sort) ?>">
        <input type="hidden" name="dir" value="<?= escape($dir) ?>">
        <input type="hidden" name="rating" id="reviewRatingInput" value="<?= $activeRatingValue ?>">

        <div class="position-relative flex-grow-1">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="search" name="q" maxlength="100" value="<?= escape($search) ?>"
                   class="form-control ps-5 rounded-3 admin-search-input"
                   placeholder="Cari nama pelanggan, menu, atau komentar..."
                   aria-label="Cari ulasan">
        </div>

        <div class="dropdown admin-filter-dropdown">
            <button
                class="btn admin-filter-trigger w-100 d-flex align-items-center justify-content-between gap-2"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-star text-muted"></i>
                    <span class="text-truncate"><?= escape($activeRatingLabel) ?></span>
                </span>
                <i class="bi bi-chevron-down admin-filter-chevron"></i>
            </button>
            <ul class="dropdown-menu admin-filter-menu dropdown-menu-end w-100 shadow-sm border-0">
                <?php foreach ($ratingOptions as $opt):
                    $isOptSelected = !empty($opt['selected']);
                ?>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item admin-filter-item d-flex align-items-center justify-content-between gap-2 <?= $isOptSelected ? 'active' : '' ?>"
                            data-report-filter-field="reviewRatingInput"
                            data-report-filter-value="<?= (int) $opt['value'] ?>"
                        >
                            <span><?= escape($opt['label']) ?></span>
                            <?php if ($isOptSelected): ?>
                                <i class="bi bi-check2"></i>
                            <?php endif; ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="admin-review-select d-md-none">
            <select class="form-select rounded-3 admin-search-input" aria-label="Urutkan ulasan" onchange="window.location.href = this.value">
                <?php foreach ($sortOptions as $opt): ?>
                    <option value="<?= escape($opt['url']) ?>" <?= $opt['selected'] ? 'selected' : '' ?>>Urutkan: <?= escape($opt['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($isFiltered): ?>
            <a href="<?= escape($resetUrl) ?>" class="btn btn-outline-secondary rounded-3 d-inline-flex align-items-center justify-content-center px-3">
                <i class="bi bi-x-lg me-1"></i>Reset
            </a>
        <?php endif; ?>
    </form>

    <?php if (empty($reviews)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-star fs-1 d-block mb-2"></i>
            <?php if ($isFiltered): ?>
                <p class="mb-1 fw-medium">Tidak ada ulasan yang cocok dengan pencarian atau filter.</p>
                <a href="<?= escape($resetUrl) ?>" class="small">Reset pencarian</a>
            <?php else: ?>
                <p class="mb-0 fw-medium">Belum ada ulasan dari pelanggan.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-reviews mb-0">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th aria-sort="<?= escape($col['aria_sort']) ?>">
                                <a href="<?= escape($col['url']) ?>" class="admin-sort-link <?= $col['active'] ? 'active' : '' ?>">
                                    <span><?= escape($col['label']) ?></span>
                                    <i class="bi <?= escape($col['icon']) ?>"></i>
                                </a>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td class="admin-rev-date-cell">
                                <div class="fw-semibold text-dark"><?= escape($rev['date_text']) ?></div>
                                <div class="text-muted" style="font-size: 11.5px;"><?= escape($rev['time_text']) ?></div>
                            </td>
                            <td class="admin-rev-cust-cell">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="admin-cust-avatar <?= escape($rev['avatar_class']) ?>"><?= escape($rev['initials']) ?></span>
                                    <div class="min-w-0">
                                        <div class="fw-bold admin-cust-name text-truncate"><?= escape($rev['customer_name']) ?></div>
                                        <div class="text-muted" style="font-size: 11.5px;">Pelanggan</div>
                                    </div>
                                </div>
                            </td>
                            <td class="admin-rev-menu-cell">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <span class="fw-semibold text-dark admin-rev-menu-name"><?= escape($rev['menu_name']) ?></span>
                                    <span class="admin-rev-stars d-md-none flex-shrink-0" role="img" aria-label="Rating <?= escape($rev['rating_label']) ?>">
                                        <?= $rev['stars_html'] ?>
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size: 11.5px;"><?= escape($rev['order_code']) ?></div>
                            </td>
                            <td class="admin-rev-rating-cell d-none d-md-table-cell">
                                <span class="admin-rev-stars" role="img" aria-label="Rating <?= escape($rev['rating_label']) ?>"><?= $rev['stars_html'] ?></span>
                            </td>
                            <td class="admin-rev-comment-cell" data-label="Komentar">
                                <?php if ($rev['has_comment']): ?>
                                    <span class="admin-review-comment" title="<?= escape($rev['comment_text']) ?>"><?= escape($rev['comment_text']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-3 border-top">
            <span class="text-muted small text-center text-sm-start"><?= escape($countText) ?></span>

            <?php if ($pagination['show']): ?>
                <nav class="d-flex align-items-center flex-wrap justify-content-center gap-1" aria-label="Paginasi ulasan">
                    <a href="<?= escape($pagination['prev_url']) ?>"
                       class="btn btn-sm admin-page-btn <?= $pagination['has_prev'] ? '' : 'disabled' ?>"
                       aria-label="Halaman Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php foreach ($pagination['pages'] as $pg): ?>
                        <a href="<?= escape($pg['url']) ?>"
                           class="btn btn-sm admin-page-btn <?= $pg['active'] ? 'active' : '' ?>"
                           <?= $pg['active'] ? 'aria-current="page"' : '' ?>>
                            <?= (int) $pg['number'] ?>
                        </a>
                    <?php endforeach; ?>
                    <a href="<?= escape($pagination['next_url']) ?>"
                       class="btn btn-sm admin-page-btn <?= $pagination['has_next'] ? '' : 'disabled' ?>"
                       aria-label="Halaman Selanjutnya">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>