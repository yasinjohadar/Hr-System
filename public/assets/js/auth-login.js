(function () {
    const form = document.getElementById('login-form');
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
})();
