/**
 * AdminConfirm — مودال تأكيد مركزي (بديل confirm/alert)
 */
(function () {
    'use strict';

    const TYPE_ICONS = {
        danger: 'ri-delete-bin-line',
        warning: 'ri-error-warning-line',
        success: 'ri-checkbox-circle-line',
        info: 'ri-information-line',
        primary: 'ri-question-line',
    };

    const TYPE_TITLES = {
        danger: 'تأكيد الحذف',
        warning: 'تنبيه',
        success: 'تأكيد',
        info: 'معلومة',
        primary: 'تأكيد العملية',
    };

    let modalInstance = null;
    let pendingResolve = null;
    let pendingAction = null;
    let settled = false;

    function getModal() {
        return document.getElementById('adminConfirmModal');
    }

    function getBsModal() {
        const el = getModal();
        if (!el || typeof bootstrap === 'undefined') return null;
        if (!modalInstance) {
            modalInstance = bootstrap.Modal.getOrCreateInstance(el, {
                backdrop: true,
                keyboard: true,
            });
        }
        return modalInstance;
    }

    function settle(result) {
        if (pendingResolve) {
            const resolve = pendingResolve;
            pendingResolve = null;
            pendingAction = null;
            resolve(result);
        }
    }

    function applyType(type) {
        const ring = document.getElementById('adminConfirmIconRing');
        const icon = document.getElementById('adminConfirmIcon');
        const okBtn = document.getElementById('adminConfirmOk');
        const safeType = TYPE_ICONS[type] ? type : 'info';

        if (ring) ring.dataset.type = safeType;
        if (icon) icon.className = TYPE_ICONS[safeType];
        if (okBtn) okBtn.dataset.type = safeType;

        return safeType;
    }

    function show(options = {}) {
        const modalEl = getModal();
        const bsModal = getBsModal();

        if (!modalEl || !bsModal) {
            return Promise.resolve(window.confirm(options.message || 'هل أنت متأكد؟'));
        }

        const type = applyType(options.type || 'info');
        const titleEl = document.getElementById('adminConfirmTitle');
        const messageEl = document.getElementById('adminConfirmMessage');
        const hintEl = document.getElementById('adminConfirmHint');
        const cancelTextEl = document.getElementById('adminConfirmCancelText');
        const okTextEl = document.getElementById('adminConfirmOkText');
        const okIconEl = document.getElementById('adminConfirmOkIcon');

        if (titleEl) titleEl.textContent = options.title || TYPE_TITLES[type];
        if (messageEl) {
            if (options.html) {
                messageEl.innerHTML = options.html;
            } else {
                messageEl.textContent = options.message || 'هل أنت متأكد من المتابعة؟';
            }
        }
        if (hintEl) hintEl.textContent = options.hint || '';
        if (cancelTextEl) cancelTextEl.textContent = options.cancelText || 'إلغاء';
        if (okTextEl) okTextEl.textContent = options.confirmText || 'تأكيد';
        if (okIconEl) okIconEl.className = TYPE_ICONS[type] || 'ri-check-line';

        pendingAction = options.onConfirm || null;
        settled = false;

        return new Promise((resolve) => {
            pendingResolve = resolve;
            bsModal.show();
        });
    }

    function confirmDelete(options = {}) {
        return show({
            type: 'danger',
            title: options.title || 'تأكيد الحذف',
            message: options.message || 'هل أنت متأكد من رغبتك في حذف هذا العنصر؟',
            hint: options.hint || 'لا يمكن التراجع عن هذه العملية بعد الحذف.',
            confirmText: options.confirmText || 'حذف',
            onConfirm: () => {
                const form = document.getElementById('adminConfirmDeleteForm');
                if (form && options.url) {
                    form.action = options.url;
                    form.submit();
                }
                if (typeof options.onConfirmed === 'function') {
                    options.onConfirmed();
                }
            },
        });
    }

    function bindModalEvents() {
        const modalEl = getModal();
        const okBtn = document.getElementById('adminConfirmOk');
        if (!modalEl || modalEl.dataset.bound) return;
        modalEl.dataset.bound = '1';

        okBtn?.addEventListener('click', function () {
            if (settled) return;
            settled = true;
            const action = pendingAction;
            getBsModal()?.hide();
            settle(true);
            if (typeof action === 'function') {
                action();
            }
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            if (!settled) {
                settle(false);
            }
            settled = false;
        });
    }

    function initDeleteDelegation() {
        if (document.body.dataset.adminConfirmDeleteBound) return;
        document.body.dataset.adminConfirmDeleteBound = '1';

        document.addEventListener('click', function (e) {
            const button = e.target.closest('[data-delete-url]');
            if (!button) return;
            e.preventDefault();

            confirmDelete({
                url: button.getAttribute('data-delete-url'),
                title: button.getAttribute('data-delete-title') || undefined,
                message: button.getAttribute('data-delete-message') || undefined,
                hint: button.getAttribute('data-delete-hint') || undefined,
                confirmText: button.getAttribute('data-delete-confirm') || undefined,
            });
        });
    }

    function initFormConfirm() {
        if (document.body.dataset.adminConfirmFormBound) return;
        document.body.dataset.adminConfirmFormBound = '1';

        document.addEventListener('submit', async function (e) {
            const form = e.target.closest('form[data-confirm]');
            if (!form || form.dataset.confirmSubmitted === '1') return;

            e.preventDefault();

            const type = form.getAttribute('data-confirm-type') || 'warning';
            const ok = await show({
                type,
                title: form.getAttribute('data-confirm-title') || undefined,
                message: form.getAttribute('data-confirm'),
                hint: form.getAttribute('data-confirm-hint') || undefined,
                confirmText: form.getAttribute('data-confirm-btn') || undefined,
            });

            if (ok) {
                form.dataset.confirmSubmitted = '1';
                form.submit();
            }
        }, true);
    }

    function initClickConfirm() {
        if (document.body.dataset.adminConfirmClickBound) return;
        document.body.dataset.adminConfirmClickBound = '1';

        document.addEventListener('click', async function (e) {
            const el = e.target.closest('[data-confirm-click]');
            if (!el) return;

            e.preventDefault();
            e.stopPropagation();

            const ok = await show({
                type: el.getAttribute('data-confirm-type') || 'warning',
                title: el.getAttribute('data-confirm-title') || undefined,
                message: el.getAttribute('data-confirm-click'),
                hint: el.getAttribute('data-confirm-hint') || undefined,
                confirmText: el.getAttribute('data-confirm-btn') || undefined,
            });

            if (!ok) return;

            if (el.tagName === 'A' && el.href) {
                window.location.href = el.href;
                return;
            }

            const form = el.closest('form');
            if (form) {
                form.requestSubmit();
            }
        });
    }

    function init() {
        bindModalEvents();
        initDeleteDelegation();
        initFormConfirm();
        initClickConfirm();
    }

    window.AdminConfirm = {
        show,
        confirmDelete,
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
