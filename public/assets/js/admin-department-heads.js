/**
 * منتقي أقسام رئيس القسم
 */
(function () {
    'use strict';

    function initDepartmentPicker(root) {
        const picker = root.querySelector('[data-dept-picker]');
        if (!picker || picker.dataset.deptPickerBound) return;
        picker.dataset.deptPickerBound = '1';

        const searchInput = picker.querySelector('[data-dept-search]');
        const emptyEl = picker.querySelector('[data-dept-empty]');
        const items = () => Array.from(picker.querySelectorAll('[data-dept-item]'));

        function filterItems() {
            const q = (searchInput?.value || '').trim().toLowerCase();
            let visible = 0;

            items().forEach((item) => {
                const name = (item.dataset.name || '').toLowerCase();
                const code = (item.dataset.code || '').toLowerCase();
                const match = !q || name.includes(q) || code.includes(q);
                item.classList.toggle('is-hidden', !match);
                if (match) visible++;
            });

            if (emptyEl) {
                emptyEl.style.display = visible === 0 ? 'block' : 'none';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterItems);
        }

        picker.querySelector('[data-dept-select-all]')?.addEventListener('click', () => {
            items().forEach((item) => {
                if (!item.classList.contains('is-hidden')) {
                    const cb = item.querySelector('.dh-dept-card__checkbox');
                    if (cb) cb.checked = true;
                }
            });
        });

        picker.querySelector('[data-dept-deselect-all]')?.addEventListener('click', () => {
            items().forEach((item) => {
                if (!item.classList.contains('is-hidden')) {
                    const cb = item.querySelector('.dh-dept-card__checkbox');
                    if (cb) cb.checked = false;
                }
            });
        });

        filterItems();
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDepartmentPicker(document);
    });

    window.AdminDepartmentHeads = { initDepartmentPicker };
})();
