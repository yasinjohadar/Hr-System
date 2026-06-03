(function () {
    'use strict';

    function initProjectFilters() {
        const pills = document.querySelectorAll('.employee-projects-page [data-project-filter]');
        const cards = document.querySelectorAll('.employee-projects-page .project-card[data-status]');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function (e) {
                e.preventDefault();
                const filter = pill.dataset.projectFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    card.style.display = filter === 'all' || status === filter ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initProjectFilters);
})();
