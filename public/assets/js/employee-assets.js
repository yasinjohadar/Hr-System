(function () {
    'use strict';

    function initAssetFilters() {
        const page = document.querySelector('.employee-assets-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-asset-filter]');
        const cards = page.querySelectorAll('.asset-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.assetFilter;
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

    document.addEventListener('DOMContentLoaded', initAssetFilters);
})();
