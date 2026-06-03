(function () {
    const page = document.querySelector('.admin-team-approvals-page');
    if (!page) return;

    const pills = page.querySelectorAll('[data-approval-filter]');
    const cards = page.querySelectorAll('[data-approval-card]');
    const emptyFiltered = page.querySelector('[data-empty-filtered]');

    pills.forEach((pill) => {
        pill.addEventListener('click', () => {
            const filter = pill.getAttribute('data-approval-filter') || 'all';

            pills.forEach((p) => p.classList.remove('active'));
            pill.classList.add('active');

            let visible = 0;
            cards.forEach((card) => {
                const type = card.getAttribute('data-approval-type');
                const show = filter === 'all' || type === filter;
                card.classList.toggle('is-hidden', !show);
                if (show) visible++;
            });

            if (emptyFiltered) {
                emptyFiltered.classList.toggle('d-none', visible > 0 || cards.length === 0);
            }
        });
    });
})();
