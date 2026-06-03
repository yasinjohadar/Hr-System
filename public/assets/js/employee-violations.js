(function () {
    'use strict';

    function initViolationFilters() {
        const page = document.querySelector('.employee-violations-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-violation-filter]');
        const cards = page.querySelectorAll('.violation-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.violationFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    const severity = card.dataset.severity;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'pending') {
                            show = status === 'pending' || status === 'investigating';
                        } else if (filter === 'resolved') {
                            show = status === 'dismissed' || status === 'resolved';
                        } else if (filter === 'critical') {
                            show = severity === 'critical';
                        } else {
                            show = status === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initViolationFilters);
})();
