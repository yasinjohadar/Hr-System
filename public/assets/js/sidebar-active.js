(function () {
    'use strict';

    function markSidebarReady() {
        document.documentElement.classList.add('sidebar-nav-ready');
    }

    function normalizePath(href) {
        if (!href || href === 'javascript:void(0);' || href === '#' || href === '') {
            return null;
        }
        try {
            if (href.startsWith('http')) {
                return new URL(href).pathname;
            }
            return href.startsWith('/') ? href : '/' + href;
        } catch (e) {
            return href;
        }
    }

    function pathMatches(currentPath, itemPath) {
        if (!itemPath) {
            return false;
        }
        if (currentPath === itemPath) {
            return true;
        }
        if (itemPath.length > 1 && currentPath.startsWith(itemPath + '/')) {
            return true;
        }
        return false;
    }

    function openParentSlide(item) {
        let menu = item.closest('.slide-menu');

        while (menu) {
            menu.style.display = 'block';

            const parentSlide = menu.closest('.slide.has-sub');
            if (parentSlide) {
                parentSlide.classList.add('open', 'has-active');
            }

            menu = menu.parentElement?.closest('.slide-menu');
        }
    }

    function markActiveLinks() {
        const currentPath = window.location.pathname;

        document.querySelectorAll('.app-sidebar .side-menu__item').forEach(function (item) {
            const href = item.getAttribute('href');
            const itemPath = normalizePath(href);
            if (!itemPath) {
                return;
            }

            if (pathMatches(currentPath, itemPath)) {
                item.classList.add('active');
                openParentSlide(item);
            }
        });
    }

    function scrollToActiveItem() {
        const active = document.querySelector('.app-sidebar .side-menu__item.active');
        const sidebarScroll = document.getElementById('sidebar-scroll');
        if (!active || !sidebarScroll) {
            return;
        }
        const itemTop = active.offsetTop;
        const sidebarHeight = sidebarScroll.clientHeight;
        sidebarScroll.scrollTop = Math.max(0, itemTop - sidebarHeight / 2);
    }

    function init() {
        markActiveLinks();
        scrollToActiveItem();
        markSidebarReady();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
