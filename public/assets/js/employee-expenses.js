(function () {
    'use strict';

    function initExpenseFilters() {
        const page = document.querySelector('.employee-expenses-page[data-expense-list]');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-expense-filter]');
        const cards = page.querySelectorAll('.expense-request-card');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.expenseFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'approved') {
                            show = status === 'approved' || status === 'paid';
                        } else {
                            show = status === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    function initExpenseCreateForm() {
        const form = document.getElementById('expense-create-form');
        if (!form) {
            return;
        }

        const categorySelect = form.querySelector('[name="expense_category_id"]');
        const amountInput = form.querySelector('[name="amount"]');
        const hintEl = document.getElementById('category-hint');
        const fileInput = form.querySelector('[name="receipt"]');
        const fileDrop = document.getElementById('receipt-drop');
        const fileNameEl = document.getElementById('receipt-file-name');

        function updateCategoryHint() {
            if (!categorySelect || !hintEl) {
                return;
            }
            const opt = categorySelect.options[categorySelect.selectedIndex];
            if (!opt || !opt.value) {
                hintEl.textContent = '';
                hintEl.className = 'category-hint';
                return;
            }
            const parts = [];
            const max = opt.dataset.maxAmount;
            const requiresReceipt = opt.dataset.requiresReceipt === '1';
            if (max) {
                parts.push('الحد الأقصى: ' + max);
            }
            if (requiresReceipt) {
                parts.push('الإيصال مطلوب لهذا التصنيف');
            }
            hintEl.textContent = parts.join(' · ');
            hintEl.className = 'category-hint' + (requiresReceipt ? ' is-warning' : '');
        }

        function validateAmount() {
            if (!categorySelect || !amountInput || !hintEl) {
                return;
            }
            const opt = categorySelect.options[categorySelect.selectedIndex];
            const max = parseFloat(opt?.dataset.maxAmount || '');
            const amount = parseFloat(amountInput.value || '');
            if (max && amount > max) {
                hintEl.textContent = 'المبلغ يتجاوز الحد الأقصى (' + max + ')';
                hintEl.className = 'category-hint is-danger';
            } else {
                updateCategoryHint();
            }
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', updateCategoryHint);
            updateCategoryHint();
        }
        if (amountInput) {
            amountInput.addEventListener('input', validateAmount);
        }

        if (fileInput && fileDrop) {
            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];
                if (file) {
                    fileDrop.classList.add('has-file');
                    if (fileNameEl) {
                        fileNameEl.textContent = file.name;
                    }
                } else {
                    fileDrop.classList.remove('has-file');
                    if (fileNameEl) {
                        fileNameEl.textContent = 'اسحب الملف أو انقر للاختيار';
                    }
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initExpenseFilters();
        initExpenseCreateForm();
    });
})();
