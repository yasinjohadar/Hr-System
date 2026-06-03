(function () {
    'use strict';

    function initMeetingFilters() {
        const page = document.querySelector('.employee-meetings-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-meeting-filter]');
        const cards = page.querySelectorAll('.meeting-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.meetingFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const state = card.dataset.filterState;
                    const show = filter === 'all' || state === filter;
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initMeetingFilters);
})();
