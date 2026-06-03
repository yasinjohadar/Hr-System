(function () {
    'use strict';

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

    function isSubmenuToggle(anchor) {
        const href = anchor.getAttribute('href');
        return !href || href === 'javascript:void(0);' || href === '#';
    }

    function openParentSlide(item) {
        const parentSlide = item.closest('.slide.has-sub');
        if (!parentSlide) {
            return;
        }
        parentSlide.classList.add('open', 'has-active');
    }

    function closeSiblingMenus(currentSlide) {
        const parentUl = currentSlide.parentElement;
        if (!parentUl) {
            return;
        }
        parentUl.querySelectorAll(':scope > .slide.has-sub.open').forEach(function (sibling) {
            if (sibling !== currentSlide) {
                sibling.classList.remove('open');
            }
        });
    }

    function initSubmenuToggles() {
        document.addEventListener(
            'click',
            function (e) {
                const toggle = e.target.closest('.app-sidebar .slide.has-sub > .side-menu__item');
                if (!toggle || !isSubmenuToggle(toggle)) {
                    return;
                }

                e.preventDefault();
                e.stopImmediatePropagation();

                const parentSlide = toggle.closest('.slide.has-sub');
                if (!parentSlide) {
                    return;
                }

                const willOpen = !parentSlide.classList.contains('open');

                if (willOpen) {
                    closeSiblingMenus(parentSlide);
                    parentSlide.classList.add('open');
                } else {
                    parentSlide.classList.remove('open');
                }
            },
            true
        );
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
        initSubmenuToggles();
        scrollToActiveItem();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
