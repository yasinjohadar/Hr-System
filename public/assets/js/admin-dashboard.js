(function () {
    'use strict';

    const config = window.adminDashboardConfig || {};
    let attendanceChart = null;

    function getThemeMode() {
        return document.documentElement.getAttribute('data-theme-mode') || 'light';
    }

    function getChartColors() {
        const isDark = getThemeMode() === 'dark';
        return {
            foreColor: isDark ? '#adb5bd' : '#6c757d',
            borderColor: isDark ? '#2d2d4a' : '#e9ecef',
        };
    }

    function buildChartOptions(data) {
        const theme = getChartColors();
        const labels = (data || []).map(function (item) {
            return item.month;
        });

        return {
            series: [
                { name: 'حضور', data: (data || []).map(function (item) { return item.present; }) },
                { name: 'غياب', data: (data || []).map(function (item) { return item.absent; }) },
            ],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit',
                zoom: { enabled: false },
            },
            colors: ['#22c55e', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                },
            },
            xaxis: {
                categories: labels,
                labels: { style: { colors: theme.foreColor } },
                axisBorder: { color: theme.borderColor },
            },
            yaxis: {
                labels: { style: { colors: theme.foreColor } },
                min: 0,
            },
            grid: { borderColor: theme.borderColor },
            legend: {
                position: 'top',
                labels: { colors: theme.foreColor },
            },
            tooltip: { theme: getThemeMode() },
        };
    }

    function initChart() {
        const el = document.querySelector('#admin-attendance-chart');
        if (!el || typeof ApexCharts === 'undefined' || !config.attendanceChart) {
            return;
        }

        if (attendanceChart) {
            attendanceChart.destroy();
        }

        attendanceChart = new ApexCharts(el, buildChartOptions(config.attendanceChart));
        attendanceChart.render();
    }

    function observeTheme() {
        const observer = new MutationObserver(function () {
            if (!attendanceChart || !config.attendanceChart) {
                return;
            }
            attendanceChart.updateOptions(buildChartOptions(config.attendanceChart), false, true);
        });
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme-mode'],
        });
    }

    window.refreshAdminDashboard = function (ev) {
        const btn = ev?.target?.closest('button');
        if (btn) {
            btn.disabled = true;
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.add('ri-loader-4-line', 'spin');
                icon.classList.remove('ri-refresh-line');
            }
        }
        window.location.href = config.refreshUrl || '/admin?refresh=1';
    };

    document.addEventListener('DOMContentLoaded', function () {
        initChart();
        observeTheme();

        if (!document.getElementById('admin-dash-spin')) {
            const style = document.createElement('style');
            style.id = 'admin-dash-spin';
            style.textContent = '@keyframes adminDashSpin { to { transform: rotate(360deg); } } .spin { animation: adminDashSpin 0.8s linear infinite; display: inline-block; }';
            document.head.appendChild(style);
        }
    });
})();
