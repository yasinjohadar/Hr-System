(function () {
    'use strict';

    function initCertificateFilters() {
        const page = document.querySelector('.employee-certificates-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-cert-filter]');
        const cards = page.querySelectorAll('.certificate-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.certFilter;
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

    document.addEventListener('DOMContentLoaded', initCertificateFilters);
})();
