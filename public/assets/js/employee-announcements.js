(function () {
    'use strict';

    function initAnnouncementFilters() {
        const page = document.querySelector('.employee-announcements-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-announcement-filter]');
        const cards = page.querySelectorAll('.announcement-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.announcementFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const states = (card.dataset.filterState || '').split(/\s+/);
                    const show = filter === 'all' || states.indexOf(filter) !== -1;
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    function initContentToggle() {
        document.querySelectorAll('.employee-announcements-page .btn-toggle-content').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const content = btn.previousElementSibling;
                if (!content) {
                    return;
                }
                const collapsed = content.classList.toggle('collapsed');
                btn.textContent = collapsed ? 'اقرأ المزيد' : 'عرض أقل';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAnnouncementFilters();
        initContentToggle();
    });
})();
