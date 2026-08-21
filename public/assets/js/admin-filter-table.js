/*
 * فلترة جداول الإدارة عبر AJOX بلا إعادة تحميل الصفحة.
 *
 * عام ومدفوع بسمات data-* على نموذج الفلترة، فلا يحتاج سكربتاً لكل صفحة:
 *
 *   <form data-filter-table
 *         data-filter-rows="#payrolls-table-body"        (إجباري)
 *         data-filter-pagination="#payrolls-pagination"  (اختياري)
 *         data-filter-meta="#payrolls-meta"              (اختياري — «عرض س إلى ص من ن»)
 *         data-filter-total="#payrolls-total"            (اختياري — عدّاد رقمي)
 *         data-filter-loading="#payrolls-loading"        (اختياري)
 *         data-filter-delay="350">                       (اختياري — تأجيل الكتابة، 350ms افتراضاً)
 *
 * الخادم يرجّع: html_rows، html_pagination، html_meta، total.
 *
 * ملاحظة مهمّة للصفوف المُستبدَلة: أي تفاعل داخل الصفوف يجب أن يكون
 * بتفويض الأحداث على document (كما في admin-post-action.js و
 * admin-confirm.js)، لأن innerHTML يُتلف أي مستمع مربوط على العناصر.
 */
(function () {
    'use strict';

    const DEFAULT_DELAY = 350;

    document.querySelectorAll('form[data-filter-table]').forEach(setup);

    function setup(form) {
        const rowsEl = document.querySelector(form.dataset.filterRows || '');

        if (!rowsEl) {
            return; // بلا حاوية صفوف لا معنى للفلترة الحيّة
        }

        const paginationEl = form.dataset.filterPagination
            ? document.querySelector(form.dataset.filterPagination) : null;
        const metaEl = form.dataset.filterMeta
            ? document.querySelector(form.dataset.filterMeta) : null;
        const totalEl = form.dataset.filterTotal
            ? document.querySelector(form.dataset.filterTotal) : null;
        const loadingEl = form.dataset.filterLoading
            ? document.querySelector(form.dataset.filterLoading) : null;

        const delay = parseInt(form.dataset.filterDelay, 10) || DEFAULT_DELAY;

        let timer = null;
        let controller = null;

        function setLoading(on) {
            if (loadingEl) {
                loadingEl.classList.toggle('d-none', !on);
            }
            rowsEl.style.opacity = on ? '0.55' : '';
        }

        /** يبني رابط الفلترة من حالة النموذج الحالية */
        function currentUrl(page) {
            const params = new URLSearchParams(new FormData(form));

            // أزل الحقول الفارغة حتى يبقى الرابط نظيفاً وقابلاً للمشاركة
            Array.from(params.keys()).forEach(function (key) {
                if (params.get(key) === '') {
                    params.delete(key);
                }
            });

            if (page) {
                params.set('page', page);
            }

            const query = params.toString();

            return form.getAttribute('action') + (query ? '?' + query : '');
        }

        function load(url, pushState) {
            if (controller) {
                controller.abort();
            }
            controller = new AbortController();

            setLoading(true);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                signal: controller.signal,
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    rowsEl.innerHTML = data.html_rows || '';

                    if (paginationEl) {
                        paginationEl.innerHTML = data.html_pagination || '';
                    }
                    if (metaEl && typeof data.html_meta === 'string') {
                        metaEl.innerHTML = data.html_meta;
                    }
                    if (totalEl && typeof data.total !== 'undefined') {
                        totalEl.textContent = data.total;
                    }

                    if (pushState) {
                        // يُبقي الرابط مطابقاً للمعروض، فالتحديث ورجوع
                        // المتصفح والمشاركة تعمل كلها
                        window.history.pushState({ adminFilterTable: true }, '', url);
                    }
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }
                    // الانتقال العادي بديل آمن لو فشل الطلب
                    window.location.href = url;
                })
                .finally(function () {
                    setLoading(false);
                });
        }

        /* —— المشغّلات —— */

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearTimeout(timer);
            load(currentUrl(1), true);
        });

        // القوائم المنسدلة: تطبيق فوري
        form.querySelectorAll('select').forEach(function (select) {
            select.addEventListener('change', function () {
                clearTimeout(timer);
                load(currentUrl(1), true);
            });
        });

        // حقول النص والأرقام: تأجيل حتى يتوقّف المستخدم عن الكتابة
        form.querySelectorAll('input[type="text"], input[type="search"], input[type="number"]').forEach(function (input) {
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    load(currentUrl(1), true);
                }, delay);
            });
        });

        // روابط الترقيم — تفويض لأن المحتوى يُستبدل
        if (paginationEl) {
            paginationEl.addEventListener('click', function (event) {
                const link = event.target.closest('a[href]');

                if (!link) {
                    return;
                }

                const href = link.getAttribute('href');

                if (!href || href === '#') {
                    return;
                }

                event.preventDefault();
                load(href, true);
            });
        }

        // زرّ الرجوع في المتصفح: أعد التحميل الكامل — أبسط من إعادة بناء
        // حالة النموذج من الرابط، وبلا فرق ملموس للمستخدم
        window.addEventListener('popstate', function (event) {
            if (event.state && event.state.adminFilterTable) {
                window.location.reload();
            }
        });
    }
})();
