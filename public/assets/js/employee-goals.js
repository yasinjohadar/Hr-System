(function () {
    'use strict';

    function initGoalFilters() {
        const page = document.querySelector('.employee-goals-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-goal-filter]');
        const cards = page.querySelectorAll('.goal-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.goalFilter;
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

    document.addEventListener('DOMContentLoaded', initGoalFilters);
})();
