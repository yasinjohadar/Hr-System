(function () {
    'use strict';

    function initTicketFilters() {
        const page = document.querySelector('.employee-tickets-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-ticket-filter]');
        const cards = page.querySelectorAll('.ticket-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.ticketFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const status = card.dataset.status;
                    let show = filter === 'all';
                    if (!show) {
                        if (filter === 'open') {
                            show = status === 'open' || status === 'in_progress';
                        } else {
                            show = status === filter;
                        }
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initTicketFilters);
})();
