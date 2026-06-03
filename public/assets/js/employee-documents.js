(function () {
    'use strict';

    function initDocumentFilters() {
        const pills = document.querySelectorAll('.employee-documents-page .filter-pill');
        const cards = document.querySelectorAll('.employee-documents-page .document-card');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.filter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.filterStatus;
                    const expiring = card.dataset.expiring === '1';
                    let show = false;
                    if (filter === 'all') {
                        show = true;
                    } else if (filter === 'expiring') {
                        show = expiring;
                    } else {
                        show = status === filter;
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initDocumentFilters);
})();
