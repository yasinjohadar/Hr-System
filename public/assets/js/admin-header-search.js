/*
 * بحث الهيدر السريع عن الأشخاص.
 *
 * يعمل على كل حاوية تحمل [data-hr-search] — حالياً مربّع سطح المكتب
 * والمربّع داخل قائمة الجوّال المنسدلة — فلا يوجد تكرار للمنطق بينهما.
 *
 * الخادم: admin.search.people (GlobalSearchController@people) يرجّع
 * الموظفين والمستخدمين حسب صلاحيات المستخدم الحالي.
 *
 * النتائج تُبنى عبر createElement/textContent لا innerHTML: أسماء
 * الأشخاص تأتي من قاعدة البيانات، وبناؤها كنص HTML يفتح باب XSS مخزّن.
 */
(function () {
    'use strict';

    const MIN_CHARS = 2;
    const DEBOUNCE_MS = 250;

    document.querySelectorAll('[data-hr-search]').forEach(setup);

    function setup(root) {
        const input = root.querySelector('.hr-search__input');
        const panel = root.querySelector('.hr-search__panel');
        const url = root.dataset.hrSearchUrl;

        if (!input || !panel || !url) {
            return;
        }

        let timer = null;
        let controller = null;
        let items = [];        // عناصر <a> المعروضة حالياً
        let activeIndex = -1;

        /* —— العرض —— */

        function open() {
            panel.hidden = false;
            root.classList.add('is-open');
            input.setAttribute('aria-expanded', 'true');
        }

        function close() {
            panel.hidden = true;
            root.classList.remove('is-open');
            input.setAttribute('aria-expanded', 'false');
            input.removeAttribute('aria-activedescendant');
            activeIndex = -1;
        }

        function clear() {
            panel.replaceChildren();
            items = [];
            activeIndex = -1;
        }

        function showNote(text, modifier) {
            clear();
            const note = document.createElement('p');
            note.className = 'hr-search__note' + (modifier ? ' hr-search__note--' + modifier : '');
            note.textContent = text;
            panel.appendChild(note);
            open();
        }

        function showLoading() {
            clear();
            const wrap = document.createElement('div');
            wrap.className = 'hr-search__loading';
            for (let i = 0; i < 3; i++) {
                wrap.appendChild(document.createElement('span'));
            }
            panel.appendChild(wrap);
            open();
        }

        /* —— بناء النتائج —— */

        function avatarNode(result) {
            if (result.avatar) {
                const img = document.createElement('img');
                img.className = 'hr-search__avatar';
                img.src = result.avatar;
                img.alt = '';
                img.loading = 'lazy';
                return img;
            }

            const fallback = document.createElement('span');
            fallback.className = 'hr-search__avatar hr-search__avatar--letter';
            fallback.setAttribute('aria-hidden', 'true');
            fallback.textContent = result.initial || '؟';
            return fallback;
        }

        function resultNode(result, index) {
            const a = document.createElement('a');
            a.className = 'hr-search__item';
            a.href = result.url;
            a.id = panel.id + '-opt-' + index;
            a.setAttribute('role', 'option');
            a.setAttribute('aria-selected', 'false');
            a.dataset.type = result.type;

            a.appendChild(avatarNode(result));

            const body = document.createElement('span');
            body.className = 'hr-search__body';

            const title = document.createElement('span');
            title.className = 'hr-search__title';
            title.textContent = result.title || '—';

            const meta = document.createElement('span');
            meta.className = 'hr-search__meta';
            meta.textContent = result.meta || '';

            body.append(title, meta);
            a.appendChild(body);

            const badge = document.createElement('span');
            badge.className = 'hr-search__badge hr-search__badge--' + result.type;
            badge.textContent = result.type_ar || result.type;
            a.appendChild(badge);

            return a;
        }

        function render(results) {
            clear();

            if (results.length === 0) {
                showNote('لا نتائج مطابقة', 'empty');
                return;
            }

            // فاصل مجموعة عند تغيّر النوع (موظفون ثم مستخدمون)
            let lastType = null;
            results.forEach(function (result, index) {
                if (result.type !== lastType) {
                    const head = document.createElement('p');
                    head.className = 'hr-search__group';
                    head.textContent = result.type === 'employee' ? 'الموظفون' : 'المستخدمون';
                    panel.appendChild(head);
                    lastType = result.type;
                }

                const node = resultNode(result, index);
                panel.appendChild(node);
                items.push(node);
            });

            open();
        }

        /* —— الطلب —— */

        function fetchResults(term) {
            if (controller) {
                controller.abort();
            }
            controller = new AbortController();

            fetch(url + '?q=' + encodeURIComponent(term), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
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
                    // تجاهل ردّاً وصل بعد أن غيّر المستخدم النص
                    if (input.value.trim() !== term) {
                        return;
                    }
                    if (data.message && (!data.results || data.results.length === 0)) {
                        showNote(data.message, 'empty');
                        return;
                    }
                    render(data.results || []);
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }
                    showNote('تعذّر تنفيذ البحث. حاول مرة أخرى.', 'error');
                });
        }

        /* —— التنقّل بلوحة المفاتيح —— */

        function setActive(index) {
            if (items.length === 0) {
                return;
            }

            if (activeIndex > -1 && items[activeIndex]) {
                items[activeIndex].classList.remove('is-active');
                items[activeIndex].setAttribute('aria-selected', 'false');
            }

            // التفاف من الطرف إلى الطرف
            activeIndex = (index + items.length) % items.length;

            const current = items[activeIndex];
            current.classList.add('is-active');
            current.setAttribute('aria-selected', 'true');
            current.scrollIntoView({ block: 'nearest' });
            input.setAttribute('aria-activedescendant', current.id);
        }

        /* —— الأحداث —— */

        input.addEventListener('input', function () {
            const term = input.value.trim();
            clearTimeout(timer);

            if (term.length < MIN_CHARS) {
                if (controller) {
                    controller.abort();
                }
                close();
                clear();
                return;
            }

            showLoading();
            timer = setTimeout(function () {
                fetchResults(term);
            }, DEBOUNCE_MS);
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
                return;
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActive(activeIndex + 1);
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActive(activeIndex - 1);
                return;
            }

            if (event.key === 'Enter' && activeIndex > -1 && items[activeIndex]) {
                event.preventDefault();
                window.location.href = items[activeIndex].href;
            }
        });

        // إعادة فتح اللوحة عند العودة إلى حقل يحمل نتائج جاهزة
        input.addEventListener('focus', function () {
            if (items.length > 0) {
                open();
            }
        });

        document.addEventListener('click', function (event) {
            if (!root.contains(event.target)) {
                close();
            }
        });
    }
})();
