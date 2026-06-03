(function () {
    'use strict';

    function initPolicySectionFilters() {
        const page = document.querySelector('.employee-policies-page');
        if (!page) {
            return;
        }

        const pills = page.querySelectorAll('[data-policy-section]');
        const pendingSection = document.getElementById('policies-pending-section');
        const acknowledgedSection = document.getElementById('policies-acknowledged-section');
        if (!pills.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.policySection;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });

                if (pendingSection) {
                    pendingSection.classList.toggle(
                        'policy-section-hidden',
                        filter === 'acknowledged'
                    );
                }
                if (acknowledgedSection) {
                    acknowledgedSection.classList.toggle(
                        'policy-section-hidden',
                        filter === 'pending'
                    );
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initPolicySectionFilters);
})();
