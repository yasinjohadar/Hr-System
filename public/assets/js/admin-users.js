(function () {
    'use strict';

    const config = window.adminUsersConfig || {};

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showUserToast(message, type) {
        const alertClass = type === 'success' ? 'success' : 'danger';
        const toast = document.createElement('div');
        toast.className =
            'alert alert-' + alertClass + ' alert-dismissible fade show position-fixed user-status-toast';
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
        const wrap = switchEl.closest('.user-status-switch-wrap');
        const label = wrap ? wrap.querySelector('.user-status-label') : null;
        if (label) {
            label.textContent = isActive ? 'مفعّل' : 'معطّل';
        }
    }

    function initializeToggleSwitches() {
        const switches = document.querySelectorAll('.user-status-switch:not([disabled])');
        if (!switches.length) return;

        const csrfToken = getCsrfToken();

        switches.forEach(function (switchEl) {
            if (switchEl.dataset.toggleBound) return;
            switchEl.dataset.toggleBound = '1';

            switchEl.addEventListener('change', function () {
                const userId = this.dataset.userId;
                const previousChecked = !this.checked;
                const willBeActive = this.checked;
                const wrap = this.closest('.user-status-switch-wrap');

                this.disabled = true;
                if (wrap) wrap.classList.add('is-loading');

                fetch('/users/' + userId + '/toggle-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
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
                            showUserToast(result.data.message || 'تم تحديث حالة المستخدم', 'success');
                        } else {
                            switchEl.checked = previousChecked;
                            showUserToast(result.data.message || 'حدث خطأ', 'error');
                        }
                    })
                    .catch(function () {
                        switchEl.checked = previousChecked;
                        showUserToast('حدث خطأ أثناء تحديث حالة المستخدم', 'error');
                    })
                    .finally(function () {
                        switchEl.disabled = false;
                        if (wrap) wrap.classList.remove('is-loading');
                    });
            });
        });
    }

    function setupLoginCodeButtons() {
        const modalEl = document.getElementById('loginCodeModal');
        if (!modalEl) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const codeInput = document.getElementById('loginCodeValue');
        const urlInput = document.getElementById('loginCodeUrl');
        const userNameEl = document.getElementById('loginCodeUserName');
        const csrfToken = getCsrfToken();
        const urlTemplate = config.loginCodeUrlTemplate || '/admin/users/__ID__/login-code';

        document.querySelectorAll('.login-code-btn').forEach(function (btn) {
            if (btn.dataset.loginBound) return;
            btn.dataset.loginBound = '1';
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                userNameEl.textContent = userName;
                this.style.pointerEvents = 'none';
                this.style.opacity = '0.6';

                const url = urlTemplate.replace('__ID__', userId);

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
                    .then(
                        function (data) {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }
                            codeInput.value = data.code || '';
                            urlInput.value = data.url || '';
                            modal.show();
                        }.bind(this)
                    )
                    .catch(function () {
                        alert('حدث خطأ أثناء إنشاء الكود.');
                    })
                    .finally(
                        function () {
                            this.style.pointerEvents = '';
                            this.style.opacity = '';
                        }.bind(this)
                    );
            });
        });
    }

    function setupLiveSearch() {
        const searchInput = document.getElementById('liveSearch');
        const filterActive = document.getElementById('filterActive');
        const filterStatus = document.getElementById('filterStatus');
        const spinner = document.getElementById('searchSpinner');
        const container = document.getElementById('usersTableContainer');
        const searchUrl = config.searchUrl;

        if (!container || !searchUrl) return;

        let searchTimer = null;

        function doSearch() {
            if (spinner) spinner.classList.remove('d-none');
            const params = new URLSearchParams();
            const q = searchInput ? searchInput.value : '';
            if (q) params.append('q', q);
            if (filterActive && filterActive.value) params.append('is_active', filterActive.value);
            if (filterStatus && filterStatus.value) params.append('status', filterStatus.value);

            fetch(searchUrl + '?' + params.toString(), {
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) {
                    return r.text();
                })
                .then(function (html) {
                    container.innerHTML = html;
                    if (spinner) spinner.classList.add('d-none');
                    initializeToggleSwitches();
                    setupLoginCodeButtons();
                })
                .catch(function () {
                    if (spinner) spinner.classList.add('d-none');
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(doSearch, 300);
            });
        }
        if (filterActive) filterActive.addEventListener('change', doSearch);
        if (filterStatus) filterStatus.addEventListener('change', doSearch);
    }

    function setupCopyButtons() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.copy-email-btn');
            if (!btn) return;
            navigator.clipboard.writeText(btn.dataset.email).then(function () {
                const icon = btn.querySelector('i');
                if (!icon) return;
                icon.className = 'ri-check-line text-success';
                setTimeout(function () {
                    icon.className = 'ri-file-copy-line';
                }, 1500);
            });
        });

        const copyCodeBtn = document.getElementById('copyCodeBtn');
        const copyUrlBtn = document.getElementById('copyUrlBtn');
        if (copyCodeBtn) {
            copyCodeBtn.addEventListener('click', function () {
                const el = document.getElementById('loginCodeValue');
                if (el) navigator.clipboard.writeText(el.value);
            });
        }
        if (copyUrlBtn) {
            copyUrlBtn.addEventListener('click', function () {
                const el = document.getElementById('loginCodeUrl');
                if (el) navigator.clipboard.writeText(el.value);
            });
        }
    }

    window.previewUserPhoto = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('photo-preview');
                if (preview) preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        initializeToggleSwitches();
        setupLoginCodeButtons();
        setupLiveSearch();
        setupCopyButtons();
    });
})();
