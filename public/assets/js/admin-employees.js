(function () {
    'use strict';

    const config = window.adminEmployeesConfig || {};

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showEmployeeToast(message, type) {
        const alertClass = type === 'success' ? 'success' : 'danger';
        const toast = document.createElement('div');
        toast.className =
            'alert alert-' + alertClass + ' alert-dismissible fade show position-fixed employee-status-toast';
        toast.style.cssText = 'top: 20px; left: 20px; z-index: 9999; min-width: 280px; max-width: 400px;';
        toast.innerHTML =
            message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
        document.body.appendChild(toast);

        setTimeout(function () {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 4500);
    }

    function updateStatusLabel(switchEl, isActive) {
        const wrap = switchEl.closest('.employee-status-switch-wrap');
        const label = wrap ? wrap.querySelector('.employee-status-label') : null;
        if (label) {
            label.textContent = isActive ? 'مفعّل' : 'معطّل';
        }
    }

    function initializeStatusSwitches() {
        const switches = document.querySelectorAll('.employee-status-switch');
        if (!switches.length) return;

        const csrfToken = getCsrfToken();
        const urlTemplate = config.toggleUrlTemplate || '';

        switches.forEach(function (switchEl) {
            if (switchEl.dataset.toggleBound) return;
            switchEl.dataset.toggleBound = '1';

            switchEl.addEventListener('change', function () {
                const employeeId = this.dataset.employeeId;
                const previousChecked = !this.checked;
                const willBeActive = this.checked;
                const wrap = this.closest('.employee-status-switch-wrap');
                const url = urlTemplate.replace('__ID__', employeeId);

                this.disabled = true;
                if (wrap) wrap.classList.add('is-loading');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ is_active: willBeActive }),
                })
                    .then(function (r) {
                        return r.json().then(function (data) {
                            return { ok: r.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.data.success) {
                            switchEl.checked = !!result.data.is_active;
                            updateStatusLabel(switchEl, result.data.is_active);
                            showEmployeeToast(result.data.message || 'تم تحديث حالة الموظف', 'success');
                        } else {
                            switchEl.checked = previousChecked;
                            showEmployeeToast(result.data.message || 'حدث خطأ', 'error');
                        }
                    })
                    .catch(function () {
                        switchEl.checked = previousChecked;
                        showEmployeeToast('حدث خطأ أثناء تحديث حالة الموظف', 'error');
                    })
                    .finally(function () {
                        switchEl.disabled = false;
                        if (wrap) wrap.classList.remove('is-loading');
                    });
            });
        });
    }

    function setupLoginCodeButtons() {
        const modalEl = document.getElementById('employeeLoginCodeModal');
        if (!modalEl) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const codeInput = document.getElementById('employeeLoginCodeValue');
        const urlInput = document.getElementById('employeeLoginCodeUrl');
        const nameEl = document.getElementById('employeeLoginCodeName');
        const csrfToken = getCsrfToken();
        const urlTemplate = config.loginCodeUrlTemplate || '';

        document.querySelectorAll('.employee-login-code-btn').forEach(function (btn) {
            if (btn.dataset.loginBound) return;
            btn.dataset.loginBound = '1';

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const employeeId = this.dataset.employeeId;
                const employeeName = this.dataset.employeeName;
                if (nameEl) nameEl.textContent = employeeName;

                const url = urlTemplate.replace('__ID__', employeeId);
                this.classList.add('disabled');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({}),
                })
                    .then(function (r) {
                        return r.json();
                    })
                    .then(function (data) {
                        if (data.error) {
                            showEmployeeToast(data.error, 'error');
                            return;
                        }
                        if (codeInput) codeInput.value = data.code || '';
                        if (urlInput) urlInput.value = data.url || '';
                        modal.show();
                    })
                    .catch(function () {
                        showEmployeeToast('حدث خطأ أثناء إنشاء الكود', 'error');
                    })
                    .finally(function () {
                        btn.classList.remove('disabled');
                    });
            });
        });

        const copyCodeBtn = document.getElementById('employeeCopyCodeBtn');
        const copyUrlBtn = document.getElementById('employeeCopyUrlBtn');
        if (copyCodeBtn) {
            copyCodeBtn.addEventListener('click', function () {
                const el = document.getElementById('employeeLoginCodeValue');
                if (el) navigator.clipboard.writeText(el.value);
            });
        }
        if (copyUrlBtn) {
            copyUrlBtn.addEventListener('click', function () {
                const el = document.getElementById('employeeLoginCodeUrl');
                if (el) navigator.clipboard.writeText(el.value);
            });
        }
    }

    function setupFilters() {
        const form = document.getElementById('employees-filter-form');
        const tbody = document.getElementById('employees-table-body');
        const paginationEl = document.getElementById('employees-pagination');
        const totalEl = document.getElementById('employees-total');
        const totalFooterEl = document.getElementById('employees-total-footer');
        const loadingEl = document.getElementById('employees-loading');
        const queryInput = document.getElementById('employees-filter-query');
        const clearBtn = document.getElementById('employees-filter-clear');
        const indexUrl = config.indexUrl;

        if (!form || !tbody || !indexUrl) return;

        const jsonHeaders = {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        };

        function setLoading(on) {
            if (loadingEl) loadingEl.classList.toggle('d-none', !on);
        }

        function updateTotals(total) {
            if (totalEl) totalEl.textContent = total;
            if (totalFooterEl) totalFooterEl.textContent = total;
        }

        function loadEmployees(url) {
            const absoluteUrl = url.startsWith('http') ? url : new URL(url, window.location.origin).href;
            const fetchUrl = absoluteUrl + (absoluteUrl.indexOf('?') >= 0 ? '&' : '?') + 'ajax=1';

            setLoading(true);
            fetch(fetchUrl, {
                method: 'GET',
                headers: jsonHeaders,
                credentials: 'same-origin',
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('Network error');
                    return r.json();
                })
                .then(function (data) {
                    tbody.innerHTML = data.html_rows;
                    if (paginationEl) paginationEl.innerHTML = data.html_pagination;
                    updateTotals(data.total);
                    try {
                        const u = new URL(absoluteUrl);
                        history.pushState({ employeesAjax: true }, '', u.pathname + u.search);
                    } catch (e) {
                        /* ignore */
                    }
                    initializeStatusSwitches();
                    setupLoginCodeButtons();
                })
                .catch(function () {
                    window.location.href = url;
                })
                .finally(function () {
                    setLoading(false);
                });
        }

        function filterUrlPageOne() {
            const params = new URLSearchParams(new FormData(form));
            params.set('page', '1');
            return indexUrl + (params.toString() ? '?' + params.toString() : '');
        }

        if (clearBtn && queryInput) {
            clearBtn.addEventListener('click', function () {
                clearTimeout(queryDebounce);
                queryInput.value = '';
                form.querySelectorAll('select').forEach(function (sel) {
                    sel.selectedIndex = 0;
                });
                loadEmployees(indexUrl);
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadEmployees(filterUrlPageOne());
        });

        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                loadEmployees(filterUrlPageOne());
            });
        });

        let queryDebounce;
        queryInput.addEventListener('input', function () {
            clearTimeout(queryDebounce);
            queryDebounce = setTimeout(function () {
                loadEmployees(filterUrlPageOne());
            }, 380);
        });

        document.addEventListener('click', function (e) {
            const pagLink = e.target.closest('#employees-pagination a[href]');
            if (!pagLink) return;
            const href = pagLink.getAttribute('href');
            if (!href || href === '#') return;
            e.preventDefault();
            loadEmployees(href);
        });

        window.addEventListener('popstate', function () {
            window.location.reload();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeStatusSwitches();
        setupLoginCodeButtons();
        setupFilters();
    });
})();
