/*
 * لوحة الهيدر الجانبية: نشطون الآن + الإدارة.
 *
 * تُجلب البيانات عبر admin.header-panel.people عند فتح اللوحة فقط
 * (حدث show.bs.offcanvas)، لا مع كل تحميل صفحة — هذه لوحة جانبية
 * اختيارية لا شارة تُحدَّث باستمرار.
 *
 * البناء بـ createElement/textContent لا innerHTML: الأسماء والأدوار
 * تأتي من قاعدة البيانات (أسماء مستخدمين حقيقية)، فبناؤها كنص HTML
 * يفتح باب XSS مخزّن — نفس القرار المتّخذ في admin-header-search.js.
 */
(function () {
    'use strict';

    const panel = document.getElementById('header-sidebar');

    if (!panel) {
        return;
    }

    const peopleUrl = panel.dataset.peopleUrl;
    const activeList = document.getElementById('hp-active-list');
    const managersList = document.getElementById('hp-managers-list');
    const activeCount = document.getElementById('hp-active-count');

    let loaded = false;
    let loading = false;

    function clear(el) {
        el.replaceChildren();
    }

    function showSkeleton(el) {
        clear(el);
        for (let i = 0; i < 3; i++) {
            const row = document.createElement('div');
            row.className = 'hr-panel__skeleton-row';
            row.innerHTML = '<span class="hr-panel__skeleton-avatar"></span>'
                + '<span class="hr-panel__skeleton-lines"><span></span><span></span></span>';
            el.appendChild(row);
        }
    }

    function showMessage(el, text) {
        clear(el);
        const p = document.createElement('p');
        p.className = 'hr-panel__empty';
        p.textContent = text;
        el.appendChild(p);
    }

    function avatarNode(person) {
        let node;

        if (person.avatar) {
            node = document.createElement('img');
            node.src = person.avatar;
            node.alt = '';
            node.loading = 'lazy';
        } else {
            node = document.createElement('span');
            node.textContent = person.initial || '؟';
        }

        node.className = 'hr-panel__avatar' + (person.avatar ? '' : ' hr-panel__avatar--letter');

        const wrap = document.createElement('span');
        wrap.className = 'hr-panel__avatar-wrap';
        wrap.appendChild(node);

        if (person.active) {
            const dot = document.createElement('span');
            dot.className = 'hr-panel__online-dot';
            dot.setAttribute('aria-label', 'نشط الآن');
            wrap.appendChild(dot);
        }

        return wrap;
    }

    function personRow(person) {
        const a = document.createElement('a');
        a.className = 'hr-panel__person';
        a.href = person.url;

        a.appendChild(avatarNode(person));

        const body = document.createElement('span');
        body.className = 'hr-panel__person-body';

        const name = document.createElement('span');
        name.className = 'hr-panel__person-name';
        name.textContent = person.name || '—';

        const subtitle = document.createElement('span');
        subtitle.className = 'hr-panel__person-subtitle';
        subtitle.textContent = person.subtitle || '';

        body.append(name, subtitle);
        a.appendChild(body);

        if (person.active) {
            const badge = document.createElement('span');
            badge.className = 'hr-panel__live-badge';
            badge.textContent = 'الآن';
            a.appendChild(badge);
        }

        return a;
    }

    function render(el, people, emptyText) {
        clear(el);

        if (!people || people.length === 0) {
            showMessage(el, emptyText);
            return;
        }

        people.forEach(function (person) {
            el.appendChild(personRow(person));
        });
    }

    function load() {
        if (loading || !peopleUrl) {
            return;
        }

        loading = true;
        showSkeleton(activeList);
        showSkeleton(managersList);

        fetch(peopleUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                render(activeList, data.active, activeList.dataset.hpEmpty);
                render(managersList, data.managers, managersList.dataset.hpEmpty);

                const count = (data.active || []).length;
                if (activeCount) {
                    activeCount.textContent = String(count);
                    activeCount.hidden = count === 0;
                }

                loaded = true;
            })
            .catch(function () {
                showMessage(activeList, 'تعذّر تحميل البيانات. حاول مرة أخرى.');
                showMessage(managersList, 'تعذّر تحميل البيانات. حاول مرة أخرى.');
            })
            .finally(function () {
                loading = false;
            });
    }

    // إعادة التحميل عند كل فتح — بيانات النشاط الآن تتغيّر بسرعة،
    // والتكلفة زهيدة (استعلامان مجمّعان محدودان بـ 12 صفاً).
    panel.addEventListener('show.bs.offcanvas', load);

    // fallback بلا Bootstrap events (نادراً) — أول ضغط على الزرّ المؤدّي للّوحة
    document.querySelectorAll('[data-bs-target="#header-sidebar"]').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            if (!loaded) {
                load();
            }
        });
    });
})();
