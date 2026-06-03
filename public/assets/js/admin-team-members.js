(function () {
    const page = document.querySelector('.admin-team-members-page');
    if (!page) return;

    const searchInput = page.querySelector('[data-team-search]');
    const filterPills = page.querySelectorAll('[data-dept-filter]');
    const cards = page.querySelectorAll('[data-employee-card]');
    const emptyFiltered = page.querySelector('[data-empty-filtered]');
    const listWrap = page.querySelector('[data-employee-list]');

    let activeDept = 'all';

    function normalize(text) {
        return (text || '').toLowerCase().trim();
    }

    function applyFilters() {
        const query = normalize(searchInput?.value);
        let visible = 0;

        cards.forEach((card) => {
            const deptId = card.getAttribute('data-department-id') || '';
            const haystack = normalize(card.getAttribute('data-search'));
            const deptMatch = activeDept === 'all' || deptId === activeDept;
            const searchMatch = !query || haystack.includes(query);
            const show = deptMatch && searchMatch;

            card.classList.toggle('is-hidden', !show);
            if (show) visible++;
        });

        if (emptyFiltered) {
            emptyFiltered.classList.toggle('d-none', visible > 0 || cards.length === 0);
        }
        const header = listWrap?.querySelector('.members-table-header');
        if (header) {
            header.classList.toggle('is-hidden', visible === 0 && cards.length > 0);
        }
    }

    searchInput?.addEventListener('input', applyFilters);

    filterPills.forEach((pill) => {
        pill.addEventListener('click', () => {
            filterPills.forEach((p) => p.classList.remove('active'));
            pill.classList.add('active');
            activeDept = pill.getAttribute('data-dept-filter') || 'all';
            applyFilters();
        });
    });
})();
