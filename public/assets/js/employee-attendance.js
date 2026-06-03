(function () {
    'use strict';

    function initAttendanceFilters() {
        const pills = document.querySelectorAll('.employee-attendance-page [data-attendance-filter]');
        const rows = document.querySelectorAll('.employee-attendance-page .attendance-row[data-status]');
        if (!pills.length || !rows.length) {
            return;
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                const filter = pill.dataset.attendanceFilter;
                pills.forEach(function (p) {
                    p.classList.toggle('active', p === pill);
                });
                rows.forEach(function (row) {
                    const status = row.dataset.status;
                    row.style.display = filter === 'all' || status === filter ? '' : 'none';
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initAttendanceFilters);
})();
