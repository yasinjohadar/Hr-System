(function () {
    'use strict';

    function initTaskFilters() {
        const page = document.querySelector('.employee-tasks-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-task-filter]');
        const cards = page.querySelectorAll('.task-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.taskFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const state = card.dataset.filterState;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'active') {
                            show = state === 'in_progress' || state === 'pending' || state === 'in_review' || state === 'on_hold';
                        } else {
                            show = state === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initTaskFilters);
})();
