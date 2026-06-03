(function () {
    'use strict';

    function initSkillFilters() {
        const pills = document.querySelectorAll('.employee-skills-page [data-skill-filter]');
        const cards = document.querySelectorAll('.employee-skills-page .skill-card-item');
        if (!pills.length || !cards.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.skillFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                cards.forEach(function (card) {
                    const level = card.dataset.level;
                    const verified = card.dataset.verified;
                    let show = false;
                    if (filter === 'all') {
                        show = true;
                    } else if (filter === 'verified') {
                        show = verified === '1';
                    } else if (filter === 'pending') {
                        show = verified === '0';
                    } else {
                        show = level === filter;
                    }
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initSkillFilters);
})();
