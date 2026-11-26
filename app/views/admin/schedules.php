<?php
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$bulanSingkat = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
?>

<div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="admin-page-title mb-1">Kelola Jadwal Menu</h1>
        <p class="text-muted mb-0">Atur menu harian yang akan disajikan kepada pelanggan sesuai jadwal.</p>
    </div>
    <button type="button" class="btn btn-mealmate-orange d-inline-flex align-items-center justify-content-center gap-2 flex-shrink-0" onclick="openScheduleModal()">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Jadwal</span>
    </button>
</div>

<div class="admin-card p-3 p-md-4">
    <?php if (empty($dates)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar3 fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada jadwal menu yang tersedia.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table admin-table admin-responsive-schedules mb-0">
                <thead>
                    <tr>
                        <th style="width: 175px;">Tanggal</th>
                        <th style="width: 130px;">Slot</th>
                        <th>Menu Disajikan</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dates as $dateStr):
                        $rows = $schedulesByDate[$dateStr] ?? [];
                        $dt = new DateTimeImmutable($dateStr);
                        $tglLabel = sprintf('%02d %s %s', (int) $dt->format('d'), $bulanSingkat[(int) $dt->format('n')], $dt->format('Y'));
                        $hariLabel = $hariIndo[$dt->format('l')] ?? '';
                    ?>
                        <?php foreach ($rows as $idx => $item):
                            $slotClass = match ($item['slot']) {
                                'Pagi' => 'admin-slot-pagi',
                                'Siang' => 'admin-slot-siang',
                                default => 'admin-slot-malam',
                            };
                            $slotIcon = match ($item['slot']) {
                                'Pagi' => 'bi-brightness-high',
                                'Siang' => 'bi-sun',
                                default => 'bi-moon',
                            };
                            $hasImage = !empty($item['image']) && is_file(__DIR__ . '/../../../public/assets/img/' . $item['image']);
                        ?>
                            <tr class="<?= $idx === 0 ? 'schedule-group-first' : 'schedule-group-item' ?>">
                                <td class="admin-schedule-date-cell <?= $idx > 0 ? 'd-none d-md-table-cell' : '' ?>">
                                    <?php if ($idx === 0): ?>
                                        <div class="d-flex align-items-center align-items-md-start gap-2">
                                            <i class="bi bi-calendar3 text-muted mt-md-1"></i>
                                            <div class="d-flex flex-row flex-md-column align-items-center align-items-md-start gap-2 gap-md-0">
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($tglLabel) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($hariLabel) ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="admin-schedule-slot-cell">
                                    <span class="admin-slot-badge <?= $slotClass ?>">
                                        <i class="bi <?= $slotIcon ?>"></i>
                                        <span><?= htmlspecialchars($item['slot']) ?></span>
                                    </span>
                                </td>
                                <td class="admin-schedule-menu-cell">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($hasImage): ?>
                                            <img src="./assets/img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['menu_name']) ?>" class="admin-schedule-thumb flex-shrink-0">
                                        <?php else: ?>
                                            <div class="admin-schedule-placeholder flex-shrink-0"></div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark" style="font-size: 13px;"><?= htmlspecialchars($item['menu_name']) ?></div>
                                            <div class="text-muted" style="font-size: 12px;"><?= htmlspecialchars($item['description'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end admin-schedule-action-cell">
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                        data-date="<?= htmlspecialchars($item['date']) ?>"
                                        data-slot="<?= htmlspecialchars($item['slot']) ?>"
                                        data-menu-id="<?= (int) $item['menu_id'] ?>"
                                        onclick="openScheduleModalFromBtn(this)" aria-label="Edit Jadwal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm admin-action-btn"
                                        data-date="<?= htmlspecialchars($item['date']) ?>"
                                        data-slot="<?= htmlspecialchars($item['slot']) ?>"
                                        data-label="<?= htmlspecialchars($tglLabel . ' - ' . $item['slot'], ENT_QUOTES) ?>"
                                        onclick="openDeleteScheduleModal(this)" aria-label="Hapus Jadwal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-3 mt-2 border-top">
        <span class="text-muted small text-center text-sm-start">Menampilkan <?= count($dates) ?> dari <?= $totalDates ?> tanggal</span>

        <?php if ($totalPages > 1): ?>
            <div class="d-flex align-items-center flex-wrap justify-content-center gap-1">
                <a href="index.php?page=admin-schedules&q=<?= urlencode($search) ?>&p=<?= max(1, $currentPageNum - 1) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum <= 1 ? 'disabled' : '' ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=admin-schedules&q=<?= urlencode($search) ?>&p=<?= $i ?>"
                       class="btn btn-sm admin-page-btn <?= $i === $currentPageNum ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                <a href="index.php?page=admin-schedules&q=<?= urlencode($search) ?>&p=<?= min($totalPages, $currentPageNum + 1) ?>"
                   class="btn btn-sm admin-page-btn <?= $currentPageNum >= $totalPages ? 'disabled' : '' ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="scheduleFormModal" tabindex="-1" aria-hidden="true" <?= !empty($errors) ? 'data-auto-open="true"' : '' ?>>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-schedules" method="POST" novalidate>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="original_date" id="scheduleOriginalDate" value="<?= htmlspecialchars($old['original_date'] ?? '') ?>">
                <input type="hidden" name="original_slot" id="scheduleOriginalSlot" value="<?= htmlspecialchars($old['original_slot'] ?? '') ?>">

                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="scheduleModalTitle"><?= !empty($old['original_date']) ? 'Ubah Jadwal Menu' : 'Atur Jadwal Menu' ?></h5>
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
                        <label for="schedule_date" class="form-label fw-medium small">Tanggal Penyajian <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="schedule_date"
                               data-today="<?= date('Y-m-d') ?>"
                               class="form-control <?= !empty($errors['date']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($old['date'] ?? date('Y-m-d')) ?>" required>
                        <?php if (!empty($errors['date'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['date']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="scheduleSlotTrigger" class="form-label fw-medium small">Slot Waktu <span class="text-danger">*</span></label>
                        <?php $oldSlot = (string) ($old['slot'] ?? 'Pagi'); ?>
                        <input type="hidden" name="slot" id="schedule_slot" value="<?= htmlspecialchars($oldSlot) ?>">
                        <div class="dropdown w-100">
                            <button type="button"
                                    id="scheduleSlotTrigger"
                                    data-placeholder="Pilih Slot Waktu"
                                    class="btn admin-filter-trigger admin-modal-dropdown-trigger w-100 d-flex align-items-center justify-content-between gap-2 <?= !empty($errors['slot']) ? 'is-invalid' : '' ?>"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <span id="scheduleSlotLabel" class="text-truncate"><?= htmlspecialchars($oldSlot) ?></span>
                                <i class="bi bi-chevron-down admin-filter-chevron"></i>
                            </button>
                            <div class="dropdown-menu admin-filter-menu w-100">
                                <?php foreach (['Pagi', 'Siang', 'Malam'] as $s): ?>
                                    <?php $isSlotActive = ($oldSlot === $s); ?>
                                    <button type="button"
                                            class="dropdown-item admin-filter-item admin-modal-dropdown-item d-flex align-items-center justify-content-between <?= $isSlotActive ? 'active' : '' ?>"
                                            data-modal-dropdown-target="schedule_slot"
                                            data-modal-dropdown-label="scheduleSlotLabel"
                                            data-value="<?= htmlspecialchars($s) ?>"
                                            data-label="<?= htmlspecialchars($s) ?>">
                                        <span><?= htmlspecialchars($s) ?></span>
                                        <i class="bi bi-check2 admin-modal-check <?= $isSlotActive ? '' : 'd-none' ?>"></i>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if (!empty($errors['slot'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['slot']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label for="scheduleMenuTrigger" class="form-label fw-medium small">Menu Disajikan <span class="text-danger">*</span></label>
                        <?php
                        $oldMenuId = (int) ($old['menu_id'] ?? 0);
                        $currentMenuLabel = 'Pilih Menu';
                        foreach ($allMenus as $m) {
                            if ((int) $m['menu_id'] === $oldMenuId) {
                                $currentMenuLabel = (string) $m['menu_name'];
                                break;
                            }
                        }
                        ?>
                        <input type="hidden" name="menu_id" id="schedule_menu_id" value="<?= $oldMenuId > 0 ? $oldMenuId : '' ?>">
                        <div class="dropdown w-100">
                            <button type="button"
                                    id="scheduleMenuTrigger"
                                    data-placeholder="Pilih Menu"
                                    class="btn admin-filter-trigger admin-modal-dropdown-trigger w-100 d-flex align-items-center justify-content-between gap-2 <?= !empty($errors['menu_id']) ? 'is-invalid' : '' ?>"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <span id="scheduleMenuLabel" class="text-truncate <?= $oldMenuId <= 0 ? 'text-muted fw-normal' : '' ?>"><?= htmlspecialchars($currentMenuLabel) ?></span>
                                <i class="bi bi-chevron-down admin-filter-chevron"></i>
                            </button>
                            <div class="dropdown-menu admin-filter-menu w-100">
                                <div class="px-2 pt-1 pb-2">
                                    <div class="position-relative">
                                        <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 11px; font-size: 0.8rem;"></i>
                                        <input type="search"
                                               id="scheduleMenuDropdownSearch"
                                               class="form-control form-control-sm ps-4 rounded-3"
                                               placeholder="Cari menu makanan..."
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="admin-modal-menu-list" id="scheduleMenuDropdownList">
                                    <?php foreach ($allMenus as $m): ?>
                                        <?php
                                        $mId = (int) $m['menu_id'];
                                        $mName = (string) $m['menu_name'];
                                        $isMenuActive = ($oldMenuId === $mId);
                                        ?>
                                        <button type="button"
                                                class="dropdown-item admin-filter-item admin-modal-dropdown-item d-flex align-items-center justify-content-between gap-2 <?= $isMenuActive ? 'active' : '' ?>"
                                                data-modal-dropdown-target="schedule_menu_id"
                                                data-modal-dropdown-label="scheduleMenuLabel"
                                                data-value="<?= $mId ?>"
                                                data-label="<?= htmlspecialchars($mName) ?>"
                                                data-menu-search="<?= htmlspecialchars(strtolower($mName)) ?>">
                                            <span class="text-truncate"><?= htmlspecialchars($mName) ?></span>
                                            <i class="bi bi-check2 admin-modal-check flex-shrink-0 <?= $isMenuActive ? '' : 'd-none' ?>"></i>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                                <div id="scheduleMenuDropdownEmpty" class="text-center text-muted small py-3 d-none">
                                    Menu tidak ditemukan.
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($errors['menu_id'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['menu_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-mealmate-green px-4">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <form action="index.php?page=admin-schedules" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="date" id="deleteScheduleDate" value="">
                <input type="hidden" name="slot" id="deleteScheduleSlot" value="">

                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h6 class="fw-bold mb-2">Hapus Jadwal?</h6>
                    <p class="text-muted small mb-4">Hapus jadwal penyajian pada <strong id="deleteScheduleLabel"></strong>?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-3">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
