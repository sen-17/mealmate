function clearModalValidationState(modalEl) {
    if (!modalEl) return;
    modalEl.querySelectorAll('.is-invalid').forEach(function (el) {
        el.classList.remove('is-invalid');
    });
    modalEl.querySelectorAll('.modal-validation-alert').forEach(function (el) {
        el.remove();
    });
}

function openMenuModal(mode, id = 0, name = '', desc = '') {
    var modalEl = document.getElementById('menuFormModal');
    clearModalValidationState(modalEl);
    document.getElementById('menuFormAction').value = mode;
    document.getElementById('menuFormId').value = id;
    document.getElementById('menu_name').value = name;
    document.getElementById('description').value = desc;
    var imgInput = document.getElementById('image');
    if (imgInput) imgInput.value = '';
    document.getElementById('menuFormTitle').textContent = mode === 'update' ? 'Ubah Data Menu' : 'Tambah Menu Baru';
    new bootstrap.Modal(modalEl).show();
}

function openMenuModalFromBtn(btn) {
    openMenuModal('update', btn.dataset.id, btn.dataset.name, btn.dataset.desc);
}

function openDeleteMenuModal(btn) {
    document.getElementById('deleteMenuId').value = btn.dataset.id;
    document.getElementById('deleteMenuName').textContent = btn.dataset.name;
    new bootstrap.Modal(document.getElementById('deleteMenuModal')).show();
}

function toggleSelectAllMenus(masterCheckbox) {
    document.querySelectorAll('.menu-row-checkbox').forEach(function (cb) {
        cb.checked = masterCheckbox.checked;
    });
    updateBulkDeleteState();
}

function updateBulkDeleteState() {
    var checkboxes = document.querySelectorAll('.menu-row-checkbox');
    var checked = document.querySelectorAll('.menu-row-checkbox:checked');
    var btn = document.getElementById('bulkDeleteBtn');
    var countLabel = document.getElementById('selectedCountLabel');
    var selectAll = document.getElementById('selectAllMenus');

    if (countLabel) {
        countLabel.textContent = checked.length;
    }
    if (btn) {
        btn.classList.toggle('d-none', checked.length === 0);
    }
    document.querySelectorAll('.select-all-menus-cb').forEach(function (masterCb) {
        masterCb.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
    });
}

function openBulkDeleteModal() {
    var checked = document.querySelectorAll('.menu-row-checkbox:checked');
    if (checked.length === 0) return;
    document.getElementById('bulkDeleteCountText').textContent = checked.length;
    new bootstrap.Modal(document.getElementById('bulkDeleteMenuModal')).show();
}

function setModalDropdownValue(targetInputId, labelSpanId, val, placeholderText) {
    var hiddenInput = document.getElementById(targetInputId);
    var labelSpan = document.getElementById(labelSpanId);
    var strVal = (val !== undefined && val !== null) ? String(val) : '';
    if (hiddenInput) {
        hiddenInput.value = strVal;
    }

    var matchedLabel = '';
    document.querySelectorAll('.admin-modal-dropdown-item[data-modal-dropdown-target="' + targetInputId + '"]').forEach(function (btn) {
        var isMatch = (strVal !== '' && btn.getAttribute('data-value') === strVal);
        btn.classList.toggle('active', isMatch);
        var checkIcon = btn.querySelector('.admin-modal-check');
        if (checkIcon) {
            checkIcon.classList.toggle('d-none', !isMatch);
        }
        if (isMatch) {
            matchedLabel = btn.getAttribute('data-label') || '';
        }
    });

    if (labelSpan) {
        if (matchedLabel !== '') {
            labelSpan.textContent = matchedLabel;
            labelSpan.classList.remove('text-muted', 'fw-normal');
        } else {
            labelSpan.textContent = placeholderText || 'Pilih';
            labelSpan.classList.add('text-muted', 'fw-normal');
        }
    }
}

function openScheduleModal(date = '', slot = 'Pagi', menuId = '', origDate = '', origSlot = '') {
    var modalEl = document.getElementById('scheduleFormModal');
    clearModalValidationState(modalEl);
    var dateInput = document.getElementById('schedule_date');
    dateInput.value = date || dateInput.dataset.today || '';
    setModalDropdownValue('schedule_slot', 'scheduleSlotLabel', slot || 'Pagi', 'Pilih Slot Waktu');
    setModalDropdownValue('schedule_menu_id', 'scheduleMenuLabel', menuId || '', 'Pilih Menu');
    var menuSearch = document.getElementById('scheduleMenuDropdownSearch');
    if (menuSearch) {
        menuSearch.value = '';
        document.querySelectorAll('#scheduleMenuDropdownList .admin-modal-dropdown-item').forEach(function (el) {
            el.classList.remove('d-none');
        });
        var emptyEl = document.getElementById('scheduleMenuDropdownEmpty');
        if (emptyEl) emptyEl.classList.add('d-none');
    }
    document.getElementById('scheduleOriginalDate').value = origDate;
    document.getElementById('scheduleOriginalSlot').value = origSlot;
    document.getElementById('scheduleModalTitle').textContent = origDate ? 'Ubah Jadwal Menu' : 'Tambah Jadwal Menu';
    new bootstrap.Modal(modalEl).show();
}

function openScheduleModalFromBtn(btn) {
    openScheduleModal(btn.dataset.date, btn.dataset.slot, btn.dataset.menuId, btn.dataset.date, btn.dataset.slot);
}

function openDeleteScheduleModal(btn) {
    document.getElementById('deleteScheduleDate').value = btn.dataset.date;
    document.getElementById('deleteScheduleSlot').value = btn.dataset.slot;
    document.getElementById('deleteScheduleLabel').textContent = btn.dataset.label;
    new bootstrap.Modal(document.getElementById('deleteScheduleModal')).show();
}

function openPackageModal(mode, id = 0, name = '', duration = '', applicable = '', price = '', desc = '') {
    var modalEl = document.getElementById('packageFormModal');
    clearModalValidationState(modalEl);
    var formAction = document.getElementById('packageFormAction');
    var formId = document.getElementById('packageFormId');
    var nameInput = document.getElementById('package_name');
    var durationInput = document.getElementById('duration_days');
    var priceInput = document.getElementById('price');
    var descInput = document.getElementById('description');
    var imgInput = document.getElementById('image');
    var formTitle = document.getElementById('packageFormTitle');

    if (formAction) formAction.value = mode;
    if (formId) formId.value = id;
    if (nameInput) nameInput.value = name;
    if (durationInput) durationInput.value = duration;
    setModalDropdownValue('applicable_days', 'applicableDaysLabel', applicable || '', 'Pilih Hari');
    if (priceInput) priceInput.value = price;
    if (descInput) descInput.value = desc;
    if (imgInput) imgInput.value = '';
    if (formTitle) formTitle.textContent = mode === 'update' ? 'Ubah Data Paket' : 'Tambah Paket Baru';

    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
}

function openPackageModalFromBtn(btn) {
    openPackageModal(
        'update',
        btn.dataset.id,
        btn.dataset.name,
        btn.dataset.duration,
        btn.dataset.applicable,
        btn.dataset.price,
        btn.dataset.desc
    );
}

function openDeletePackageModal(btn) {
    var idInput = document.getElementById('deletePackageId');
    var nameLabel = document.getElementById('deletePackageName');
    if (idInput) idInput.value = btn.dataset.id;
    if (nameLabel) nameLabel.textContent = btn.dataset.name;

    var modalEl = document.getElementById('deletePackageModal');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
}


function handlePaymentProofError() {
    var img = document.getElementById('paymentProofImg');
    var fallback = document.getElementById('paymentProofFallback');
    if (img) img.classList.add('d-none');
    if (fallback) fallback.classList.remove('d-none');
}

function reloadPaymentProofImage(url) {
    if (!url) return;
    var img = document.getElementById('paymentProofImg');
    var fallback = document.getElementById('paymentProofFallback');
    if (img) {
        img.classList.remove('d-none');
        img.src = url + (url.indexOf('?') === -1 ? '?' : '&') + 't=' + Date.now();
        if (fallback) fallback.classList.add('d-none');
    }
}


// customers
function togglePasswordVisibility(inputId, btnElem) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var icon = btnElem.querySelector('i');
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    if (icon) {
        icon.classList.toggle('bi-eye', show);
        icon.classList.toggle('bi-eye-slash', !show);
    }
}

function openCustomerModal(mode, id = 0, name = '', email = '', phone = '', address = '') {
    var isUpdate = mode === 'update';

    document.getElementById('customerFormAction').value = mode;
    document.getElementById('customerFormId').value = id;
    document.getElementById('customer_name').value = name;
    document.getElementById('customer_email').value = email;
    document.getElementById('customer_phone').value = phone;
    document.getElementById('customer_address').value = address;

    var pwd = document.getElementById('customer_password');
    pwd.value = '';
    pwd.type = 'password';
    var toggleIcon = pwd.parentElement.querySelector('.admin-password-toggle i');
    if (toggleIcon) {
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    }

    document.getElementById('customerFormTitle').textContent = isUpdate ? 'Ubah Data Pelanggan' : 'Tambah Pelanggan Baru';
    document.getElementById('customerPasswordRequired').style.display = isUpdate ? 'none' : '';
    document.getElementById('customerPasswordHint').style.display = isUpdate ? '' : 'none';

    new bootstrap.Modal(document.getElementById('customerFormModal')).show();
}

function openCustomerModalFromBtn(btn) {
    openCustomerModal('update', btn.dataset.id, btn.dataset.name, btn.dataset.email, btn.dataset.phone, btn.dataset.address);
}

function openDeleteCustomerModal(btn) {
    document.getElementById('deleteCustomerId').value = btn.dataset.id;
    document.getElementById('deleteCustomerName').textContent = btn.dataset.name;
    new bootstrap.Modal(document.getElementById('deleteCustomerModal')).show();
}

document.addEventListener('submit', function (e) {
    var btn = e.target.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
});

window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
        document.querySelectorAll('button[type="submit"]').forEach(function (b) { b.disabled = false; });
    }
});

window.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal[data-auto-open="true"]').forEach(function (modalEl) {
        new bootstrap.Modal(modalEl).show();
    });

    document.querySelectorAll('.admin-period-select').forEach(function (selectEl) {
        selectEl.addEventListener('change', function () {
            var targetId = selectEl.getAttribute('data-custom-target');
            var customGroup = targetId ? document.getElementById(targetId) : null;
            if (selectEl.value === 'custom') {
                if (customGroup) {
                    customGroup.classList.remove('d-none');
                }
            } else {
                if (customGroup) {
                    customGroup.classList.add('d-none');
                }
                if (selectEl.form) {
                    selectEl.form.submit();
                }
            }
        });
    });

    document.querySelectorAll('[data-report-filter-field]').forEach(function (btnEl) {
        btnEl.addEventListener('click', function () {
            var fieldId = btnEl.getAttribute('data-report-filter-field');
            var val = btnEl.getAttribute('data-report-filter-value');
            var hiddenInput = fieldId ? document.getElementById(fieldId) : null;
            if (hiddenInput && hiddenInput.form) {
                hiddenInput.value = val;
                hiddenInput.form.submit();
            }
        });
    });

    document.querySelectorAll('.admin-modal-dropdown-item').forEach(function (btnEl) {
        btnEl.addEventListener('click', function () {
            var targetId = btnEl.getAttribute('data-modal-dropdown-target');
            var labelId = btnEl.getAttribute('data-modal-dropdown-label');
            var val = btnEl.getAttribute('data-value');
            if (targetId && labelId) {
                setModalDropdownValue(targetId, labelId, val, 'Pilih');
                var dropdownWrap = btnEl.closest('.dropdown');
                if (dropdownWrap) {
                    var trigger = dropdownWrap.querySelector('.admin-modal-dropdown-trigger');
                    if (trigger) {
                        trigger.classList.remove('is-invalid');
                    }
                }
            }
        });
    });

    var scheduleMenuSearch = document.getElementById('scheduleMenuDropdownSearch');
    var scheduleMenuList = document.getElementById('scheduleMenuDropdownList');
    var scheduleMenuEmpty = document.getElementById('scheduleMenuDropdownEmpty');
    if (scheduleMenuSearch && scheduleMenuList) {
        var scheduleMenuItems = scheduleMenuList.querySelectorAll('.admin-modal-dropdown-item');
        scheduleMenuSearch.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        scheduleMenuSearch.addEventListener('input', function () {
            var kw = scheduleMenuSearch.value.trim().toLowerCase();
            var visibleCount = 0;
            scheduleMenuItems.forEach(function (itemEl) {
                var hay = (itemEl.getAttribute('data-menu-search') || '').toLowerCase();
                var matched = (kw === '' || hay.indexOf(kw) !== -1);
                itemEl.classList.toggle('d-none', !matched);
                if (matched) {
                    visibleCount++;
                }
            });
            if (scheduleMenuEmpty) {
                scheduleMenuEmpty.classList.toggle('d-none', visibleCount > 0);
            }
        });
    }

    var pkgWizardSearch = document.getElementById('reportPkgWizardSearch');
    var pkgWizardList = document.getElementById('reportPkgWizardList');
    var pkgWizardEmpty = document.getElementById('reportPkgWizardEmpty');
    var pkgWizardModal = document.getElementById('reportPackageWizardModal');

    if (pkgWizardSearch && pkgWizardList) {
        var wizardItems = pkgWizardList.querySelectorAll('.admin-pkg-wizard-item');
        pkgWizardSearch.addEventListener('input', function () {
            var kw = pkgWizardSearch.value.trim().toLowerCase();
            var visibleCount = 0;
            wizardItems.forEach(function (itemEl) {
                var hay = (itemEl.getAttribute('data-pkg-search') || '').toLowerCase();
                var matched = (kw === '' || hay.indexOf(kw) !== -1);
                itemEl.classList.toggle('d-none', !matched);
                if (matched) {
                    visibleCount++;
                }
            });
            if (pkgWizardEmpty) {
                pkgWizardEmpty.classList.toggle('d-none', visibleCount > 0);
            }
        });

        if (pkgWizardModal) {
            pkgWizardModal.addEventListener('shown.bs.modal', function () {
                pkgWizardSearch.focus();
            });
            pkgWizardModal.addEventListener('hidden.bs.modal', function () {
                pkgWizardSearch.value = '';
                wizardItems.forEach(function (itemEl) { itemEl.classList.remove('d-none'); });
                if (pkgWizardEmpty) {
                    pkgWizardEmpty.classList.add('d-none');
                }
            });
        }
    }

    var input = document.getElementById('adminGlobalSearchInput');
    var dropdown = document.getElementById('adminGlobalSearchDropdown');
    var container = document.getElementById('adminGlobalSearchContainer');
    if (!input || !dropdown || !container) return;

    var timer = null;
    var activeReqId = 0;

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function renderResults(groups, query) {
        if (!groups || groups.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-center text-muted small text-truncate">Tidak ditemukan hasil untuk "<strong>' + escapeHtml(query) + '</strong>"</div>';
            dropdown.classList.remove('d-none');
            return;
        }

        var html = '';
        groups.forEach(function (group) {
            html += '<div class="admin-search-group-label">' + escapeHtml(group.label) + '</div>';
            group.items.forEach(function (item) {
                html += '<a href="' + escapeHtml(item.url) + '" class="admin-search-item d-flex align-items-center gap-3 text-decoration-none">' +
                    '<div class="admin-search-item-icon"><i class="bi ' + escapeHtml(item.icon) + '"></i></div>' +
                    '<div class="min-w-0 flex-grow-1 overflow-hidden">' +
                        '<div class="fw-semibold text-dark text-truncate" style="font-size: 13px;">' + escapeHtml(item.title) + '</div>' +
                        '<div class="text-muted text-truncate" style="font-size: 11.5px;">' + escapeHtml(item.subtitle) + '</div>' +
                    '</div>' +
                    '<i class="bi bi-chevron-right text-muted small flex-shrink-0"></i>' +
                '</a>';
            });
        });

        dropdown.innerHTML = html;
        dropdown.classList.remove('d-none');
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var currentReq = ++activeReqId;
        var q = input.value.trim();
        if (q.length < 1) {
            dropdown.classList.add('d-none');
            dropdown.innerHTML = '';
            return;
        }

        timer = setTimeout(function () {
            fetch('index.php?page=admin-search&q=' + encodeURIComponent(q))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (currentReq !== activeReqId) return;
                    renderResults(data.groups || [], q);
                })
                .catch(function () {
                    if (currentReq !== activeReqId) return;
                    dropdown.classList.add('d-none');
                });
        }, 200);
    });

    input.addEventListener('focus', function () {
        if (input.value.trim().length > 0 && dropdown.innerHTML !== '') {
            dropdown.classList.remove('d-none');
        }
    });

    document.addEventListener('click', function (e) {
        if (!container.contains(e.target)) {
            dropdown.classList.add('d-none');
        }
    });
});
