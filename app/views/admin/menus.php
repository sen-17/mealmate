<?php
$startRow = $totalMenus > 0 ? $offset + 1 : 0;
$endRow = min($offset + $perPage, $totalMenus);
?>

<div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Master Data Menu</h1>
        <p class="text-muted mb-0">Kelola daftar hidangan yang bisa dijadwalkan ke dalam paket</p>
    </div>
    <button type="button" class="btn btn-mealmate-orange d-inline-flex align-items-center justify-content-center gap-2 flex-shrink-0" onclick="openMenuModal('create')">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Menu</span>
    </button>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex flex-column flex-sm-row flex-wrap align-items-stretch align-items-sm-center gap-2 flex-grow-1">
            <form action="index.php" method="get" class="position-relative flex-grow-1 admin-toolbar-search">
                <input type="hidden" name="page" value="admin-menus">
                <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control ps-5 rounded-3 admin-search-input" placeholder="Cari menu...">
            </form>

            <div class="d-flex align-items-center justify-content-between justify-content-sm-start gap-2 flex-wrap">
                <div class="d-inline-flex align-items-center gap-2">
                    <a href="index.php?page=admin-menus&view=kanban&q=<?= urlencode($search) ?>" class="btn admin-view-pill <?= $viewMode === 'kanban' ? 'active' : '' ?>">Kanban</a>
                    <a href="index.php?page=admin-menus&view=list&q=<?= urlencode($search) ?>" class="btn admin-view-pill <?= $viewMode === 'list' ? 'active' : '' ?>">List</a>
                </div>

                <?php if ($viewMode === 'list'): ?>
                    <button type="button" id="bulkDeleteBtn" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-none" onclick="openBulkDeleteModal()">
                        <i class="bi bi-trash me-1"></i>Hapus Terpilih (<span id="selectedCountLabel">0</span>)
                    </button>
                <?php endif; ?>

                <?php if ($viewMode === 'kanban'): ?>
                    <span class="text-muted small fw-medium d-md-none"><?= $totalMenus ?> menu</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($viewMode === 'kanban'): ?>
            <span class="text-muted small fw-medium d-none d-md-inline flex-shrink-0"><?= $totalMenus ?> menu</span>
        <?php endif; ?>
    </div>

    <?php if (empty($menus)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-fork-knife fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada data menu yang ditemukan.</p>
        </div>
    <?php elseif ($viewMode === 'list'): ?>
        <form id="bulkDeleteForm" action="index.php?page=admin-menus&view=list" method="POST">
            <input type="hidden" name="action" value="bulk_delete">
            <input type="hidden" name="view" value="list">
            <div class="d-md-none d-flex align-items-center gap-2 px-3 py-2 mb-2 rounded-3" style="background-color: #F6F4ED;">
                <input type="checkbox" class="form-check-input admin-checkbox select-all-menus-cb m-0" onchange="toggleSelectAllMenus(this)" aria-label="Pilih Semua Menu">
                <span class="small fw-semibold text-secondary">Pilih Semua Menu</span>
            </div>
            <div class="table-responsive">
                <table class="table admin-table admin-list-table admin-responsive-list mb-0">
                    <thead>
                        <tr>
                            <th style="width: 48px;">
                                <input type="checkbox" class="form-check-input admin-checkbox select-all-menus-cb" id="selectAllMenus" onchange="toggleSelectAllMenus(this)">
                            </th>
                            <th style="width: 240px;">Nama Menu</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menus as $menu): ?>
                            <tr>
                                <td class="admin-list-check-cell">
                                    <input type="checkbox" name="menu_ids[]" value="<?= (int) $menu['menu_id'] ?>" class="form-check-input admin-checkbox menu-row-checkbox" onchange="updateBulkDeleteState()">
                                </td>
                                <td class="fw-semibold text-dark admin-list-name-cell"><?= htmlspecialchars($menu['menu_name']) ?></td>
                                <td class="text-muted admin-list-desc-cell"><?= htmlspecialchars($menu['description'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-menus mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th style="width: 220px;">Nama Menu</th>
                        <th>Deskripsi</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $menu):
                        $hasImage = !empty($menu['image']) && is_file(__DIR__ . '/../../../public/assets/img/' . $menu['image']);
                    ?>
                        <tr>
                            <td class="admin-menu-photo-cell">
                                <?php if ($hasImage): ?>
                                    <img src="./assets/img/<?= htmlspecialchars($menu['image']) ?>" alt="<?= htmlspecialchars($menu['menu_name']) ?>" class="admin-menu-thumb">
                                <?php else: ?>
                                    <div class="admin-menu-icon">
                                        <i class="bi bi-fork-knife"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark admin-menu-title-cell"><?= htmlspecialchars($menu['menu_name']) ?></td>
                            <td class="text-muted admin-menu-desc-cell"><?= htmlspecialchars($menu['description'] ?? '') ?></td>
                            <td class="text-end admin-menu-action-cell">
                                <button type="button" class="btn btn-sm admin-action-btn"
                                    data-id="<?= (int) $menu['menu_id'] ?>"
                                    data-name="<?= htmlspecialchars($menu['menu_name'], ENT_QUOTES) ?>"
                                    data-desc="<?= htmlspecialchars($menu['description'] ?? '', ENT_QUOTES) ?>"
                                    onclick="openMenuModalFromBtn(this)" aria-label="Edit Menu">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm admin-action-btn"
                                    data-id="<?= (int) $menu['menu_id'] ?>"
                                    data-name="<?= htmlspecialchars($menu['menu_name'], ENT_QUOTES) ?>"
                                    onclick="openDeleteMenuModal(this)" aria-label="Hapus Menu">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-2 border-top">
        <span class="text-muted small text-center text-sm-start">
            <?php if ($totalMenus <= $perPage): ?>
                Menampilkan <?= $totalMenus ?> dari <?= $totalMenus ?> menu
            <?php else: ?>
                Menampilkan <?= $startRow ?>-<?= $endRow ?> dari <?= $totalMenus ?> menu
            <?php endif; ?>
        </span>

        <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
            <a href="index.php?page=admin-menus&view=<?= urlencode($viewMode) ?>&q=<?= urlencode($search) ?>&p=<?= max(1, $currentPageNum - 1) ?>"
               class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>"
               aria-label="Halaman Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?page=admin-menus&view=<?= urlencode($viewMode) ?>&q=<?= urlencode($search) ?>&p=<?= $i ?>"
                   class="btn btn-sm admin-page-btn <?= $i === $currentPageNum ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <a href="index.php?page=admin-menus&view=<?= urlencode($viewMode) ?>&q=<?= urlencode($search) ?>&p=<?= min($totalPages, $currentPageNum + 1) ?>"
               class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>"
               aria-label="Halaman Selanjutnya">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="menuFormModal" tabindex="-1" aria-hidden="true" <?= !empty($errors) ? 'data-auto-open="true"' : '' ?>>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-menus&view=<?= urlencode($viewMode) ?>" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" id="menuFormAction" value="<?= htmlspecialchars($old['modal_mode'] ?? 'create') ?>">
                <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
                <input type="hidden" name="menu_id" id="menuFormId" value="<?= (int) ($old['menu_id'] ?? 0) ?>">

                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="menuFormTitle"><?= (($old['modal_mode'] ?? 'create') === 'update') ? 'Ubah Data Menu' : 'Tambah Menu Baru' ?></h5>
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
                        <label for="menu_name" class="form-label fw-medium small">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="menu_name" id="menu_name" maxlength="100"
                               class="form-control <?= !empty($errors['menu_name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['menu_name'] ?? '') ?>"
                               placeholder="Contoh: Ayam Bakar Kecap" required>
                        <?php if (!empty($errors['menu_name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['menu_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium small">Deskripsi Masakan <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control <?= !empty($errors['description']) ? 'is-invalid' : '' ?>"
                                  placeholder="Jelaskan bahan dan cita rasa hidangan..." required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label for="image" class="form-label fw-medium small">Foto Menu (JPG, PNG maks 2 MB)</label>
                        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png"
                               class="form-control <?= !empty($errors['image']) ? 'is-invalid' : '' ?>">
                        <?php if (!empty($errors['image'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-mealmate-green px-4">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-menus&view=<?= urlencode($viewMode) ?>" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
                <input type="hidden" name="menu_id" id="deleteMenuId" value="0">

                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Hapus Menu?</h6>
                    <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus menu <strong id="deleteMenuName"></strong>?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkDeleteMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-2">
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
                <h6 class="fw-bold mb-2">Hapus Menu Terpilih?</h6>
                <p class="text-muted small mb-4">Apakah Anda yakin ingin menghapus <strong id="bulkDeleteCountText">0</strong> menu yang dipilih?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger rounded-3 px-3" onclick="document.getElementById('bulkDeleteForm').submit()">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
