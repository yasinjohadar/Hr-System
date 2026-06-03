(function () {
    'use strict';

    function initLeaveFilters() {
        const pills = document.querySelectorAll('.employee-leaves-page .filter-pill');
        const cards = document.querySelectorAll('.employee-leaves-page .leave-request-card');
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
                    const status = card.dataset.status;
                    const show = filter === 'all' || status === filter;
                    card.style.display = show ? '' : 'none';
                });
            });
        });
    }

    function initLeaveDurationPreview() {
        const start = document.getElementById('leave_start_date');
        const end = document.getElementById('leave_end_date');
        const preview = document.getElementById('leaveDurationPreview');
        const daysEl = document.getElementById('leaveDurationDays');
        if (!start || !end || !preview || !daysEl) {
            return;
        }

        function update() {
            if (!start.value || !end.value) {
                preview.classList.remove('is-visible');
                return;
            }
            const s = new Date(start.value + 'T00:00:00');
            const e = new Date(end.value + 'T00:00:00');
            if (e < s) {
                preview.classList.add('is-visible');
                daysEl.textContent = 'تاريخ النهاية يجب أن يكون بعد البداية';
                return;
            }
            const diff = Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1;
            preview.classList.add('is-visible');
            daysEl.textContent = diff + (diff === 1 ? ' يوم' : ' يوم');
        }

        start.addEventListener('change', function () {
            if (start.value) {
                end.min = start.value;
            }
            update();
        });
        end.addEventListener('change', update);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initLeaveFilters();
        initLeaveDurationPreview();
    });
})();
