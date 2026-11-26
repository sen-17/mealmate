<div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Kelola Data Pelanggan</h1>
        <p class="text-muted mb-0 small">Akun dengan peran Pelanggan &middot; <?= number_format($totalAllCustomers, 0, ',', '.') ?> terdaftar</p>
    </div>
    <button type="button" class="btn btn-mealmate-orange d-inline-flex align-items-center justify-content-center gap-2 flex-shrink-0" onclick="openCustomerModal('create')">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Pelanggan</span>
    </button>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <form action="index.php" method="get" class="position-relative flex-grow-1 admin-toolbar-search">
            <input type="hidden" name="page" value="admin-customers">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control ps-5 rounded-3 admin-search-input" placeholder="Cari nama, email, atau nomor telepon">
        </form>
        <?php if ($search !== ''): ?>
            <a href="index.php?page=admin-customers" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-x-lg me-1"></i>Reset
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($customers)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-2"></i>
            <?php if ($search !== ''): ?>
                <p class="mb-0 fw-medium">Tidak ada pelanggan yang cocok dengan "<?= htmlspecialchars($search) ?>".</p>
                <p class="small mb-0">Coba kata kunci lain atau reset pencarian.</p>
            <?php else: ?>
                <p class="mb-0 fw-medium">Belum ada pelanggan terdaftar.</p>
                <p class="small mb-0">Klik "Tambah Pelanggan" untuk membuat akun pertama.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-customers mb-0">
                <thead style="border-bottom: 1px solid #dee2e6">
                    <tr>
                        <th style="width: 22%; background: none;">Nama Lengkap</th>
                        <th style="width: 24%; background: none;">Email</th>
                        <th style="width: 150px; background: none;">No. Telepon</th>
                        <th style="background: none;">Alamat</th>
                        <th class="text-end" style="width: 100px; background: none;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $i => $cust):
                        $avatarClass = 'admin-avatar-c' . ($i % 3);
                        $addr = trim((string) ($cust['address'] ?? ''));
                    ?>
                        <tr>
                            <td class="admin-cust-name-cell">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="admin-cust-avatar <?= $avatarClass ?>"><?= htmlspecialchars(getInitials($cust['name'])) ?></span>
                                    <span class="fw-bold admin-cust-name"><?= htmlspecialchars($cust['name']) ?></span>
                                </div>
                            </td>
                            <td class="admin-cust-email-cell" data-label="Email"><?= htmlspecialchars($cust['email']) ?></td>
                            <td class="admin-cust-phone-cell" data-label="Telepon"><?= htmlspecialchars($cust['phone_number'] ?? '-') ?></td>
                            <td class="admin-cust-addr-cell" data-label="Alamat">
                                <span class="admin-cust-addr" title="<?= htmlspecialchars($addr) ?>"><?= $addr !== '' ? htmlspecialchars($addr) : '-' ?></span>
                            </td>
                            <td class="text-end admin-cust-action-cell">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                            title="Ubah Pelanggan" aria-label="Ubah Pelanggan"
                                            data-id="<?= (int) $cust['user_id'] ?>"
                                            data-name="<?= htmlspecialchars($cust['name'], ENT_QUOTES) ?>"
                                            data-email="<?= htmlspecialchars($cust['email'], ENT_QUOTES) ?>"
                                            data-phone="<?= htmlspecialchars($cust['phone_number'] ?? '', ENT_QUOTES) ?>"
                                            data-address="<?= htmlspecialchars($cust['address'] ?? '', ENT_QUOTES) ?>"
                                            onclick="openCustomerModalFromBtn(this)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                            title="Hapus Pelanggan" aria-label="Hapus Pelanggan"
                                            data-id="<?= (int) $cust['user_id'] ?>"
                                            data-name="<?= htmlspecialchars($cust['name'], ENT_QUOTES) ?>"
                                            onclick="openDeleteCustomerModal(this)">
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
                <?php if ($totalCustomers <= $perPage): ?>
                    Menampilkan <?= $totalCustomers ?> dari <?= $totalCustomers ?> pelanggan
                <?php else: ?>
                    Menampilkan <?= $startRow ?>-<?= $endRow ?> dari <?= number_format($totalCustomers, 0, ',', '.') ?> pelanggan
                <?php endif; ?>
            </span>

            <?php if ($totalPages > 1): ?>
                <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                    <a href="index.php?page=admin-customers&q=<?= urlencode($search) ?>&p=<?= max(1, $currentPageNum - 1) ?>"
                       class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>"
                       aria-label="Halaman Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php for ($i = max(1, $currentPageNum - 2); $i <= min($totalPages, $currentPageNum + 2); $i++): ?>
                        <a href="index.php?page=admin-customers&q=<?= urlencode($search) ?>&p=<?= $i ?>"
                           class="btn btn-sm admin-page-btn <?= $i === $currentPageNum ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <a href="index.php?page=admin-customers&q=<?= urlencode($search) ?>&p=<?= min($totalPages, $currentPageNum + 1) ?>"
                       class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>"
                       aria-label="Halaman Selanjutnya">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- modal tambah / ubah -->
<div class="modal fade" id="customerFormModal" tabindex="-1" aria-hidden="true" <?= !empty($errors) ? 'data-auto-open="true"' : '' ?>>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-customers" method="POST" novalidate>
                <input type="hidden" name="action" id="customerFormAction" value="<?= htmlspecialchars($old['modal_mode'] ?? 'create') ?>">
                <input type="hidden" name="user_id" id="customerFormId" value="<?= (int) ($old['user_id'] ?? 0) ?>">

                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="customerFormTitle"><?= (($old['modal_mode'] ?? '') === 'update') ? 'Ubah Data Pelanggan' : 'Tambah Pelanggan Baru' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-medium small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="customer_name"
                               class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                               placeholder="Contoh: Angga Pratama" required>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label fw-medium small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="customer_email"
                               class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                               placeholder="nama@email.com" required>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label fw-medium small">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="tel" name="phone_number" id="customer_phone"
                               class="form-control <?= !empty($errors['phone_number']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>"
                               placeholder="081234567890" required>
                        <?php if (!empty($errors['phone_number'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['phone_number']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="customer_address" class="form-label fw-medium small">Alamat <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea name="address" id="customer_address" rows="3"
                                  class="form-control <?= !empty($errors['address']) ? 'is-invalid' : '' ?>"
                                  placeholder="Alamat lengkap pelanggan"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                        <?php if (!empty($errors['address'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['address']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label for="customer_password" class="form-label fw-medium small">
                            Password <span class="text-danger" id="customerPasswordRequired" <?= (($old['modal_mode'] ?? '') === 'update') ? 'style="display:none"' : '' ?>>*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" name="password" id="customer_password"
                                   class="form-control pe-5 <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                   placeholder="Minimal 8 karakter" autocomplete="new-password">
                            <button type="button" class="btn auth-password-toggle admin-password-toggle" onclick="togglePasswordVisibility('customer_password', this)" aria-label="Tampilkan Password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            <?php if (!empty($errors['password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text" id="customerPasswordHint" <?= (($old['modal_mode'] ?? '') === 'update') ? '' : 'style="display:none"' ?>>Kosongkan jika tidak ingin mengubah password.</div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-mealmate-green px-4">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal hapus -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-customers" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" id="deleteCustomerId" value="0">

                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Hapus Pelanggan?</h6>
                    <p class="text-muted small mb-2">Apakah Anda yakin ingin menghapus akun <strong id="deleteCustomerName"></strong>?</p>
                    <p class="text-muted mb-4" style="font-size: 11.5px;">Pesanan yang belum dibayar atau dibatalkan milik pelanggan ini ikut terhapus. Akun dengan pesanan yang sudah dibayar tidak dapat dihapus.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>