(function () {
    const sidebar = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('main-wrapper');
    const overlay = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const openBtn = document.getElementById('sidebar-open');
    const STORAGE_KEY = 'pdvsa-sidebar-collapsed';

    function setCollapsed(collapsed) {
        if (!sidebar || !mainWrapper) return;

        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open', !collapsed);
            overlay?.classList.toggle('visible', !collapsed);
        } else {
            sidebar.classList.toggle('collapsed', collapsed);
            mainWrapper.classList.toggle('sidebar-collapsed', collapsed);
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        }
    }

    function isCollapsed() {
        if (window.innerWidth <= 768) {
            return !sidebar?.classList.contains('open');
        }
        return localStorage.getItem(STORAGE_KEY) === '1';
    }

    toggleBtn?.addEventListener('click', function () {
        setCollapsed(!isCollapsed());
    });

    openBtn?.addEventListener('click', function () {
        setCollapsed(false);
    });

    overlay?.addEventListener('click', function () {
        setCollapsed(true);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            overlay?.classList.remove('visible');
            sidebar?.classList.remove('open');
            const collapsed = localStorage.getItem(STORAGE_KEY) === '1';
            sidebar?.classList.toggle('collapsed', collapsed);
            mainWrapper?.classList.toggle('sidebar-collapsed', collapsed);
        } else {
            sidebar?.classList.remove('collapsed');
            mainWrapper?.classList.remove('sidebar-collapsed');
        }
    });

    if (window.innerWidth > 768) {
        setCollapsed(localStorage.getItem(STORAGE_KEY) === '1');
    } else {
        setCollapsed(true);
    }
})();
