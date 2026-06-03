(function () {
    'use strict';

    function initContractFilters() {
        const page = document.querySelector('.employee-contract-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-contract-filter]');
        const cards = page.querySelectorAll('.history-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.contractFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    let show = filter === 'all' || status === filter;
                    if (!show && filter === 'expired') {
                        show = status === 'expired' || status === 'terminated' || status === 'renewed';
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initContractFilters);
})();
