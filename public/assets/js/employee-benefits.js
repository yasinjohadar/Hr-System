(function () {
    'use strict';

    function initBenefitFilters() {
        const page = document.querySelector('.employee-benefits-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-benefit-filter]');
        const cards = page.querySelectorAll('.benefit-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.benefitFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    const show = filter === 'all' || status === filter;
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initBenefitFilters);
})();
