(function () {
    'use strict';

    function initTrainingFilters() {
        const page = document.querySelector('.employee-training-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-training-filter]');
        const cards = page.querySelectorAll('.training-record-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.trainingFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'in_progress') {
                            show = status === 'registered' || status === 'attending';
                        } else {
                            show = status === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initTrainingFilters);
})();
