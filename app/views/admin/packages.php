<?php
$startRow = $totalPackages > 0 ? $offset + 1 : 0;
$endRow = min($offset + $perPage, $totalPackages);
?>

<div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Master Data Paket</h1>
        <p class="text-muted mb-0">Kelola paket langganan catering harian untuk pelanggan.</p>
    </div>
    <button type="button" class="btn btn-mealmate-orange d-inline-flex align-items-center justify-content-center gap-2 flex-shrink-0" onclick="openPackageModal('create')">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Paket</span>
    </button>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
            <form action="index.php" method="get" class="position-relative flex-grow-1 admin-toolbar-search">
                <input type="hidden" name="page" value="admin-packages">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control ps-5 rounded-3 admin-search-input" placeholder="Cari paket...">
            </form>
            <?php if ($search !== ''): ?>
                <a href="index.php?page=admin-packages" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-x-lg me-1"></i>Reset
                </a>
            <?php endif; ?>
        </div>
        <span class="text-muted small fw-medium align-self-end align-self-sm-center flex-shrink-0"><?= $totalPackages ?> paket</span>
    </div>

    <?php if (empty($packages)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada paket langganan yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-packages mb-0">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th style="width: 120px;">Durasi (Hari)</th>
                        <th style="width: 150px;">Hari Berlaku</th>
                        <th style="width: 140px;">Harga</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $pkg):
                        $hasImage = !empty($pkg['image']) && is_file(__DIR__ . '/../../../public/assets/img/' . $pkg['image']);
                        $formattedDays = ($pkg['applicable_days'] === 'Senin-Minggu') ? 'Senin - Minggu' : 'Senin - Jumat';
                    ?>
                        <tr>
                            <td class="admin-pkg-main-cell">
                                <div class="d-flex align-items-start align-items-md-center gap-3">
                                    <?php if ($hasImage): ?>
                                        <img src="./assets/img/<?= htmlspecialchars($pkg['image']) ?>" alt="<?= htmlspecialchars($pkg['package_name']) ?>" class="admin-menu-thumb flex-shrink-0">
                                    <?php else: ?>
                                        <div class="admin-menu-icon flex-shrink-0">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($pkg['package_name']) ?></div>
                                        <div class="text-muted small admin-pkg-desc">
                                            <?= htmlspecialchars($pkg['description'] ?? '') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="admin-pkg-meta-cell" data-label="Durasi">
                                <span class="fw-medium text-dark"><?= (int) $pkg['duration_days'] ?> <span class="d-md-none">Hari</span></span>
                            </td>
                            <td class="admin-pkg-meta-cell" data-label="Hari Berlaku">
                                <span class="fw-medium text-dark"><?= htmlspecialchars($formattedDays) ?></span>
                            </td>
                            <td class="admin-pkg-meta-cell" data-label="Harga">
                                <span class="fw-semibold text-dark"><?= formatRupiah($pkg['price']) ?></span>
                            </td>
                            <td class="text-end admin-pkg-action-cell">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                            title="Ubah Paket"
                                            data-id="<?= (int) $pkg['package_id'] ?>"
                                            data-name="<?= htmlspecialchars($pkg['package_name'], ENT_QUOTES) ?>"
                                            data-duration="<?= (int) $pkg['duration_days'] ?>"
                                            data-applicable="<?= htmlspecialchars($pkg['applicable_days'], ENT_QUOTES) ?>"
                                            data-price="<?= (int) $pkg['price'] ?>"
                                            data-desc="<?= htmlspecialchars($pkg['description'] ?? '', ENT_QUOTES) ?>"
                                            onclick="openPackageModalFromBtn(this)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                            title="Hapus Paket"
                                            data-id="<?= (int) $pkg['package_id'] ?>"
                                            data-name="<?= htmlspecialchars($pkg['package_name'], ENT_QUOTES) ?>"
                                            onclick="openDeletePackageModal(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 mt-4 pt-3 border-top">
            <span class="text-muted small text-center text-sm-start">
                <?php if ($totalPackages <= $perPage): ?>
                    Menampilkan <?= $totalPackages ?> dari <?= $totalPackages ?> paket
                <?php else: ?>
                    Menampilkan <?= $startRow ?>-<?= $endRow ?> dari <?= $totalPackages ?> paket
                <?php endif; ?>
            </span>

            <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                <a href="index.php?page=admin-packages&p=<?= max(1, $currentPageNum - 1) ?>&q=<?= urlencode($search) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>"
                   aria-label="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="index.php?page=admin-packages&p=<?= $p ?>&q=<?= urlencode($search) ?>"
                       class="btn btn-sm admin-page-btn <?= $p === $currentPageNum ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
                <a href="index.php?page=admin-packages&p=<?= min($totalPages, $currentPageNum + 1) ?>&q=<?= urlencode($search) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>"
                   aria-label="Halaman Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="packageFormModal" tabindex="-1" aria-hidden="true" <?= !empty($errors) ? 'data-auto-open="true"' : '' ?>>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-packages" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" id="packageFormAction" value="<?= htmlspecialchars($old['modal_mode'] ?? 'create') ?>">
                <input type="hidden" name="package_id" id="packageFormId" value="<?= (int) ($old['package_id'] ?? 0) ?>">

                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="packageFormTitle"><?= (($old['modal_mode'] ?? 'create') === 'update') ? 'Ubah Data Paket' : 'Tambah Paket Baru' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 px-3 rounded-3 small mb-3 modal-validation-alert" role="alert">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <div><?= htmlspecialchars($errors['general'] ?? 'Mohon periksa kembali input yang ditandai di bawah ini.') ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="package_name" class="form-label fw-medium small">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="package_name" id="package_name" maxlength="100"
                               class="form-control <?= !empty($errors['package_name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['package_name'] ?? '') ?>"
                               placeholder="Contoh: Paket Hemat" required>
                        <?php if (!empty($errors['package_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['package_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="duration_days" class="form-label fw-medium small">Durasi (Hari) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" id="duration_days" min="1" max="365"
                                   class="form-control <?= !empty($errors['duration_days']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($old['duration_days'] ?? '') ?>"
                                   placeholder="Contoh: 5" required>
                            <?php if (!empty($errors['duration_days'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['duration_days']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="applicableDaysTrigger" class="form-label fw-medium small">Hari Berlaku <span class="text-danger">*</span></label>
                            <?php
                            $oldApplicable = (string) ($old['applicable_days'] ?? '');
                            $applicableOptions = [
                                'Senin-Jumat' => 'Senin - Jumat',
                                'Senin-Minggu' => 'Senin - Minggu',
                            ];
                            $currentApplicableLabel = $applicableOptions[$oldApplicable] ?? 'Pilih Hari';
                            ?>
                            <input type="hidden" name="applicable_days" id="applicable_days" value="<?= htmlspecialchars($oldApplicable) ?>">
                            <div class="dropdown w-100">
                                <button type="button"
                                        id="applicableDaysTrigger"
                                        data-placeholder="Pilih Hari"
                                        class="btn admin-filter-trigger admin-modal-dropdown-trigger w-100 d-flex align-items-center justify-content-between gap-2 <?= !empty($errors['applicable_days']) ? 'is-invalid' : '' ?>"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <span id="applicableDaysLabel" class="text-truncate <?= $oldApplicable === '' ? 'text-muted fw-normal' : '' ?>"><?= htmlspecialchars($currentApplicableLabel) ?></span>
                                    <i class="bi bi-chevron-down admin-filter-chevron"></i>
                                </button>
                                <div class="dropdown-menu admin-filter-menu w-100">
                                    <?php foreach ($applicableOptions as $val => $label): ?>
                                        <?php $isAppActive = ($oldApplicable === $val); ?>
                                        <button type="button"
                                                class="dropdown-item admin-filter-item admin-modal-dropdown-item d-flex align-items-center justify-content-between <?= $isAppActive ? 'active' : '' ?>"
                                                data-modal-dropdown-target="applicable_days"
                                                data-modal-dropdown-label="applicableDaysLabel"
                                                data-value="<?= htmlspecialchars($val) ?>"
                                                data-label="<?= htmlspecialchars($label) ?>">
                                            <span><?= htmlspecialchars($label) ?></span>
                                            <i class="bi bi-check2 admin-modal-check <?= $isAppActive ? '' : 'd-none' ?>"></i>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php if (!empty($errors['applicable_days'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['applicable_days']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-medium small">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-light text-muted border-end-0">Rp</span>
                            <input type="number" name="price" id="price" min="1" max="2000000000" step="1000"
                                   class="form-control border-start-0 <?= !empty($errors['price']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($old['price'] ?? '') ?>"
                                   placeholder="125000" required>
                            <?php if (!empty($errors['price'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium small">Deskripsi Paket <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>"
                                  placeholder="Jelaskan keunggulan dan rincian paket langganan..." required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label for="image" class="form-label fw-medium small">Foto Paket (JPG, PNG maks 2 MB)</label>
                        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png"
                               class="form-control <?= !empty($errors['image']) ? 'is-invalid' : '' ?>">
                        <?php if (!empty($errors['image'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-mealmate-green px-4">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePackageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-packages" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="package_id" id="deletePackageId" value="0">

                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Hapus Paket?</h6>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus paket <strong id="deletePackageName"></strong>?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
