/*
 * تنفيذ عمليات POST من عناصر واجهة (أزرار، عناصر قوائم منسدلة) مع تأكيد.
 *
 * سبب وجوده: النمط السابق كان <form method="POST"> داخل <li> داخل
 * .dropdown-menu، ويعتمد على form.submit() من admin-confirm.js. البنية
 * كانت صحيحة في HTML المُصيَّر، لكن الطلب كان يخرج GET فعلياً في المتصفح
 * (405 Method Not Allowed على admin/payrolls/{id}/calculate).
 *
 * هذا البديل لا يعتمد على موضع النموذج في الصفحة إطلاقاً: عند التأكيد
 * يُبنى نموذج جديد ويُلحق بـ document.body ثم يُرسل. لا تعشيش، ولا تأثّر
 * بإخفاء القائمة المنسدلة أو بأي معالج نقر آخر.
 *
 * الاستخدام:
 *   <button type="button"
 *           data-post-url="/admin/payrolls/1/calculate"
 *           data-post-confirm="احتساب الكشف؟"        (اختياري — بلا تأكيد إن غاب)
 *           data-post-title="احتساب"                  (اختياري)
 *           data-post-type="info|warning|danger"      (اختياري)
 *           data-post-btn="احتساب"                    (اختياري)
 *           data-post-method="PUT|PATCH|DELETE">      (اختياري — POST افتراضاً)
 */
(function () {
    'use strict';

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function hidden(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    function submitPost(url, spoofedMethod) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.style.display = 'none';

        form.appendChild(hidden('_token', csrfToken()));

        // Laravel لا يقرأ PUT/PATCH/DELETE إلا عبر _method داخل طلب POST
        if (spoofedMethod) {
            form.appendChild(hidden('_method', spoofedMethod.toUpperCase()));
        }

        document.body.appendChild(form);
        form.submit();
    }

    async function confirmed(el) {
        const message = el.getAttribute('data-post-confirm');

        if (!message) {
            return true;
        }

        // نستخدم المودال المركزي إن توفّر، وإلا confirm المتصفح
        if (window.AdminConfirm && typeof window.AdminConfirm.show === 'function') {
            return window.AdminConfirm.show({
                type: el.getAttribute('data-post-type') || 'warning',
                title: el.getAttribute('data-post-title') || undefined,
                html: message,
                hint: el.getAttribute('data-post-hint') || undefined,
                confirmText: el.getAttribute('data-post-btn') || undefined,
            });
        }

        return window.confirm(message.replace(/<[^>]*>/g, ''));
    }

    document.addEventListener('click', async function (event) {
        const el = event.target.closest('[data-post-url]');

        if (!el || el.dataset.postBusy === '1') {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        if (!(await confirmed(el))) {
            return;
        }

        // منع الإرسال المزدوج لو نقر المستخدم مرّتين
        el.dataset.postBusy = '1';
        submitPost(el.getAttribute('data-post-url'), el.getAttribute('data-post-method'));
    });
})();
