(function () {
    const form = document.getElementById('login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-password');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleBtn.setAttribute('aria-label', isHidden ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
            toggleBtn.querySelector('[data-icon-show]').classList.toggle('d-none', isHidden);
            toggleBtn.querySelector('[data-icon-hide]').classList.toggle('d-none', !isHidden);
        });
    }

    if (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('.auth-submit');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'جاري تسجيل الدخول…';
            }
        });
    }

    // —— دخول سريع (يُصيَّر في بيئة local فقط) ——
    // نقرة عادية: تعبئة الحقول وإرسال النموذج. Alt+نقرة: تعبئة بدون إرسال.
    if (form && emailInput && passwordInput) {
        document.querySelectorAll('[data-quick-login] .auth-quick-btn').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                emailInput.value = btn.dataset.quickEmail || '';
                passwordInput.value = btn.dataset.quickPassword || '';
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
                passwordInput.dispatchEvent(new Event('input', { bubbles: true }));

                if (event.altKey) {
                    passwordInput.focus();
                    return;
                }

                btn.classList.add('is-loading');
                form.requestSubmit();
            });
        });
    }
})();
