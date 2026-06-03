(function () {
    'use strict';

    function initReviewFilters() {
        const page = document.querySelector('.employee-reviews-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-review-filter]');
        const cards = page.querySelectorAll('.review-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.reviewFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'pending') {
                            show = status === 'draft' || status === 'completed';
                        } else {
                            show = status === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initReviewFilters);
})();
