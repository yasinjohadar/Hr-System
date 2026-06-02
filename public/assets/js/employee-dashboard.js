(function () {
    'use strict';

    const config = window.employeeDashboardConfig || {};
    const TAB_STORAGE_KEY = 'employee_dashboard_tab';
    const root = document.getElementById('employee-dashboard');

    if (!root || typeof ApexCharts === 'undefined') {
        return;
    }

    let attendanceChart = null;
    let isRefreshing = false;

    function getThemeMode() {
        return document.documentElement.getAttribute('data-theme-mode') || 'light';
    }

    function getChartThemeColors() {
        const isDark = getThemeMode() === 'dark';
        return {
            foreColor: isDark ? '#adb5bd' : '#6c757d',
            borderColor: isDark ? '#2d2d4a' : '#e9ecef',
            gridColor: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
        };
    }

    function buildChartOptions(payload) {
        const theme = getChartThemeColors();
        const colors = payload.colors || [];

        return {
            series: payload.series || [],
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
            },
            plotOptions: {
                bar: {
                    distributed: true,
                    borderRadius: 3,
                    columnWidth: '75%',
                },
            },
            colors: colors,
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: payload.categories || [],
                labels: { style: { colors: theme.foreColor } },
                axisBorder: { color: theme.borderColor },
                axisTicks: { color: theme.borderColor },
            },
            yaxis: {
                max: 3,
                tickAmount: 3,
                labels: {
                    formatter: function (val) {
                        if (val === 3) return 'حاضر';
                        if (val === 2) return 'متأخر';
                        if (val === 1) return 'غائب';
                        return '';
                    },
                    style: { colors: theme.foreColor },
                },
            },
            grid: {
                borderColor: theme.gridColor,
            },
            tooltip: {
                theme: getThemeMode(),
                y: {
                    formatter: function (val) {
                        if (val === 3) return 'حاضر';
                        if (val === 2) return 'متأخر';
                        if (val === 1) return 'غائب';
                        return 'بدون سجل';
                    },
                },
            },
        };
    }

    function initAttendanceChart() {
        const el = document.querySelector('#attendance-chart');
        if (!el || !config.attendanceChart) {
            return;
        }

        if (attendanceChart) {
            attendanceChart.destroy();
        }

        attendanceChart = new ApexCharts(el, buildChartOptions(config.attendanceChart));
        attendanceChart.render();
    }

    function updateStats(stats, absentDays, lateDays) {
        if (!stats) return;

        root.querySelectorAll('[data-stat]').forEach(function (el) {
            const key = el.getAttribute('data-stat');
            if (stats[key] !== undefined) {
                el.textContent = stats[key];
            }
        });

        root.querySelectorAll('[data-stat-display="absent"]').forEach(function (el) {
            el.textContent = absentDays ?? 0;
        });
        root.querySelectorAll('[data-stat-display="late"]').forEach(function (el) {
            el.textContent = lateDays ?? 0;
        });
    }

    function updatePayrollKpi(payroll) {
        const amountEl = document.getElementById('kpi-latest-payroll');
        const metaEl = document.getElementById('kpi-payroll-meta');
        if (!amountEl || !metaEl) return;

        if (!payroll) {
            amountEl.textContent = '—';
            metaEl.textContent = 'لا توجد بيانات';
            return;
        }

        amountEl.innerHTML =
            payroll.net_salary +
            ' <small class="fs-14 text-white-50">' +
            payroll.currency +
            '</small>';
        metaEl.textContent = payroll.month_label;
    }

    function updateAttendanceChart(payload) {
        if (!payload) return;

        const monthEl = document.getElementById('attendance-chart-month');
        if (monthEl && payload.monthLabel) {
            monthEl.textContent = payload.monthLabel;
        }

        if (attendanceChart) {
            attendanceChart.updateOptions({
                series: payload.series,
                xaxis: { categories: payload.categories },
                colors: payload.colors,
            });
        } else {
            config.attendanceChart = payload;
            initAttendanceChart();
        }
    }

    function setRefreshing(loading) {
        isRefreshing = loading;
        const btn = document.getElementById('dashboard-refresh-btn');
        const icon = document.getElementById('dashboard-refresh-icon');
        const kpi = document.getElementById('dashboard-kpi-row');
        const att = document.getElementById('dashboard-attendance-section');

        if (btn) btn.disabled = loading;
        if (icon) icon.classList.toggle('ri-refresh-line', !loading);
        if (icon) icon.classList.toggle('ri-loader-4-line', loading);
        if (icon && loading) icon.classList.add('spin');

        [kpi, att].forEach(function (el) {
            if (el) el.classList.toggle('dashboard-refreshing', loading);
        });
    }

    async function refreshWidgets() {
        const widgets = root.querySelectorAll('[data-widget]');
        const activeTab = document.querySelector('#dashboardTabs .nav-link.active');
        const tabKey = activeTab ? activeTab.getAttribute('data-tab-key') : 'overview';

        const widgetMap = {
            overview: ['announcements'],
            attendance: [],
            leaves: ['leaves', 'payroll'],
            activity: ['meetings', 'tasks', 'violations', 'assets'],
        };

        const toRefresh = widgetMap[tabKey] || [];
        const urlTemplate = config.widgetUrlTemplate;

        if (!urlTemplate) return;

        await Promise.all(
            toRefresh.map(async function (name) {
                const container = document.getElementById('widget-' + name);
                if (!container) return;

                const url = urlTemplate.replace('__WIDGET__', name);
                try {
                    const res = await fetch(url, {
                        headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (res.ok) {
                        container.innerHTML = await res.text();
                    }
                } catch (e) {
                    console.warn('Widget refresh failed:', name, e);
                }
            })
        );
    }

    async function refreshDashboard() {
        if (isRefreshing || !config.refreshUrl) return;

        setRefreshing(true);

        try {
            const res = await fetch(config.refreshUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!res.ok) throw new Error('Refresh failed');

            const data = await res.json();

            updateStats(data.stats, data.absentDays, data.lateDays);
            updatePayrollKpi(data.latestPayroll);
            updateAttendanceChart(data.attendanceChart);

            const refreshedEl = document.getElementById('dashboard-last-refreshed');
            if (refreshedEl && data.refreshedAt) {
                refreshedEl.textContent = 'آخر تحديث: ' + data.refreshedAt;
            }

            await refreshWidgets();
        } catch (e) {
            console.error(e);
        } finally {
            setRefreshing(false);
            const icon = document.getElementById('dashboard-refresh-icon');
            if (icon) icon.classList.remove('spin');
        }
    }

    function restoreTab() {
        const saved = localStorage.getItem(TAB_STORAGE_KEY);
        if (!saved) return;

        const btn = document.querySelector('#dashboardTabs [data-tab-key="' + saved + '"]');
        if (btn && typeof bootstrap !== 'undefined') {
            bootstrap.Tab.getOrCreateInstance(btn).show();
        }
    }

    function bindTabs() {
        const tabList = document.getElementById('dashboardTabs');
        if (!tabList) return;

        tabList.addEventListener('shown.bs.tab', function (e) {
            const key = e.target.getAttribute('data-tab-key');
            if (key) localStorage.setItem(TAB_STORAGE_KEY, key);

            if (key === 'attendance' && !attendanceChart) {
                initAttendanceChart();
            }
        });
    }

    function observeTheme() {
        const observer = new MutationObserver(function () {
            if (!attendanceChart || !config.attendanceChart) return;
            attendanceChart.updateOptions(buildChartOptions(config.attendanceChart), false, true);
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme-mode'],
        });
    }

    function injectSpinStyle() {
        if (document.getElementById('dashboard-spin-style')) return;
        const style = document.createElement('style');
        style.id = 'dashboard-spin-style';
        style.textContent = '@keyframes dashboardSpin { to { transform: rotate(360deg); } } .spin { animation: dashboardSpin 0.8s linear infinite; display: inline-block; }';
        document.head.appendChild(style);
    }

    document.addEventListener('DOMContentLoaded', function () {
        injectSpinStyle();
        restoreTab();
        bindTabs();

        const refreshBtn = document.getElementById('dashboard-refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', refreshDashboard);
        }

        const activeTab = document.querySelector('#dashboardTabs .nav-link.active');
        const activeKey = activeTab ? activeTab.getAttribute('data-tab-key') : 'overview';
        if (activeKey === 'attendance') {
            initAttendanceChart();
        }

        observeTheme();
    });
})();
