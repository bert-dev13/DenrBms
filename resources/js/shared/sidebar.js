/**
 * DENR BMS Sidebar — mobile toggle
 */

class SidebarManager {
    constructor() {
        this.sidebar = null;
        this.sidebarOpen = true;
        this.sidebarCollapsed = false;
        this.isMobile = false;
        this.breakpoints = { tablet: 1024 };
        this.init();
    }

    init() {
        this.sidebar = document.getElementById('sidebar');
        if (!this.sidebar) return;

        this.checkViewport();
        this.adjustLayout();
        this.setupEventListeners();
        this.setupMobileToggle();
    }

    setupEventListeners() {
        window.addEventListener('resize', this.debounce(() => {
            this.checkViewport();
            this.adjustLayout();
        }, 200));

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMobile && this.sidebarOpen) {
                this.closeSidebar();
            }
        });

        document.addEventListener('click', (e) => {
            if (this.isMobile && this.sidebarOpen &&
                !this.sidebar.contains(e.target) &&
                !e.target.closest('.mobile-menu-toggle')) {
                this.closeSidebar();
            }
        });

        const navItems = this.sidebar.querySelectorAll('.sidebar__nav-item');
        navItems.forEach((item) => {
            item.addEventListener('click', () => {
                if (this.isMobile) {
                    setTimeout(() => this.closeSidebar(), 250);
                }
            });
        });
    }

    setupMobileToggle() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const overlay = document.getElementById('sidebar-overlay');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSidebar();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                if (this.isMobile && this.sidebarOpen) {
                    this.closeSidebar();
                }
            });
        }
    }

    checkViewport() {
        this.isMobile = window.innerWidth < this.breakpoints.tablet;
        if (this.isMobile) {
            this.sidebarOpen = false;
            this.sidebarCollapsed = false;
        } else {
            const saved = localStorage.getItem('sidebarOpen');
            const savedCollapsed = localStorage.getItem('sidebarCollapsed');
            this.sidebarOpen = saved !== null ? saved === 'true' : true;
            this.sidebarCollapsed = savedCollapsed !== null ? savedCollapsed === 'true' : false;
        }
    }

    adjustLayout() {
        if (!this.sidebar) return;

        const overlay = document.getElementById('sidebar-overlay');
        const collapseToggle = document.getElementById('sidebar-collapse-toggle');

        document.body.classList.toggle('sidebar-collapsed', !this.isMobile && this.sidebarCollapsed);
        this.sidebar.classList.toggle('sidebar--collapsed', !this.isMobile && this.sidebarCollapsed);
        if (collapseToggle) {
            collapseToggle.setAttribute('aria-label', this.sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            collapseToggle.setAttribute('title', this.sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
        }

        if (this.isMobile) {
            this.sidebar.classList.toggle('open', this.sidebarOpen);
            document.body.classList.toggle('sidebar-open', this.sidebarOpen);
            if (overlay) {
                overlay.classList.toggle('hidden', !this.sidebarOpen);
                overlay.style.setProperty('display', this.sidebarOpen ? 'block' : 'none', 'important');
                overlay.setAttribute('aria-hidden', !this.sidebarOpen);
            }
        } else {
            this.sidebar.classList.remove('open');
            document.body.classList.remove('sidebar-open');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.style.setProperty('display', 'none', 'important');
                overlay.setAttribute('aria-hidden', true);
            }
        }
    }

    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        try {
            localStorage.setItem('sidebarOpen', this.sidebarOpen.toString());
        } catch (e) {}
        this.adjustLayout();
    }

    closeSidebar() {
        this.sidebarOpen = false;
        try {
            localStorage.setItem('sidebarOpen', 'false');
        } catch (e) {}
        this.adjustLayout();
    }

    openSidebar() {
        this.sidebarOpen = true;
        try {
            localStorage.setItem('sidebarOpen', 'true');
        } catch (e) {}
        this.adjustLayout();
    }

    toggleCollapse() {
        if (this.isMobile) return;
        this.sidebarCollapsed = !this.sidebarCollapsed;
        try {
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed.toString());
        } catch (e) {}
        this.adjustLayout();
    }

    debounce(fn, wait) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    isOpen() {
        return this.sidebarOpen;
    }

    isMobileView() {
        return this.isMobile;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.sidebarManager = new SidebarManager();
});

window.toggleSidebar = function () {
    if (window.sidebarManager) {
        window.sidebarManager.toggleSidebar();
    }
};

window.toggleSidebarCollapse = function () {
    if (window.sidebarManager) {
        window.sidebarManager.toggleCollapse();
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SidebarManager;
}
