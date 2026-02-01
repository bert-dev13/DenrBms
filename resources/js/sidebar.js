/**
 * DENR BMS Sidebar JavaScript
 * Handles sidebar functionality, responsiveness, and interactions
 */

class SidebarManager {
    constructor() {
        this.sidebar = null;
        this.sidebarOpen = true;
        this.isMobile = false;
        this.breakpoints = {
            mobile: 768,
            tablet: 1024,
            desktop: 1025
        };
        
        this.init();
    }

    init() {
        this.sidebar = document.getElementById('sidebar');
        if (!this.sidebar) {
            console.warn('Sidebar element not found');
            return;
        }

        this.checkViewport();
        this.setupEventListeners();
        this.setupAlpineData();
        this.loadUserPreferences();
        this.setupKeyboardNavigation();
        this.setupTooltips();
        this.setupMobileToggle();
    }

    setupEventListeners() {
        // Window resize handler
        window.addEventListener('resize', this.debounce(() => {
            this.checkViewport();
            this.adjustLayout();
        }, 250));

        // Handle escape key for mobile
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMobile && this.sidebarOpen) {
                this.closeSidebar();
            }
        });

        // Handle click outside for mobile
        document.addEventListener('click', (e) => {
            if (this.isMobile && 
                this.sidebarOpen && 
                !this.sidebar.contains(e.target) &&
                !e.target.closest('.mobile-menu-toggle')) {
                this.closeSidebar();
            }
        });

        // Handle navigation item clicks
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                this.handleNavigationClick(e, item);
            });
        });

        // Handle logout form submission
        const logoutForm = this.sidebar.querySelector('.logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', (e) => {
                this.handleLogout(e);
            });
        }

        // Add hover sound effect (optional)
        this.setupHoverEffects();
    }

    setupAlpineData() {
        // Initialize Alpine.js data if available
        if (typeof Alpine !== 'undefined') {
            Alpine.data('sidebar', () => ({
                sidebarOpen: this.sidebarOpen,
                isMobile: this.isMobile,
                toggle() {
                    this.sidebarOpen = !this.sidebarOpen;
                    this.saveUserPreferences();
                    this.adjustLayout();
                },
                close() {
                    this.sidebarOpen = false;
                    this.saveUserPreferences();
                    this.adjustLayout();
                }
            }));
        }
    }

    checkViewport() {
        const width = window.innerWidth;
        this.isMobile = width < this.breakpoints.tablet;
        
        // Auto-close sidebar on mobile
        if (this.isMobile && this.sidebarOpen) {
            this.sidebarOpen = false;
        } else if (!this.isMobile) {
            // Restore desktop preference
            const saved = localStorage.getItem('sidebarOpen');
            this.sidebarOpen = saved !== null ? saved === 'true' : true;
        }
    }

    adjustLayout() {
        if (!this.sidebar) return;

        const mainContent = document.querySelector('main') || document.querySelector('.main-content');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (this.isMobile) {
            // Mobile layout - use CSS classes instead of direct style manipulation
            this.sidebar.classList.toggle('open', this.sidebarOpen);
            if (mainContent) {
                mainContent.style.marginLeft = '';
                mainContent.style.transition = '';
            }
            
            // Handle overlay
            if (overlay) {
                overlay.classList.toggle('hidden', !this.sidebarOpen);
            }
        } else {
            // Desktop layout - let CSS handle the layout
            this.sidebar.classList.remove('open');
            
            // Remove any inline styles that might interfere with CSS
            if (mainContent) {
                mainContent.style.marginLeft = '';
                mainContent.style.transition = '';
            }
            
            // Hide overlay on desktop
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }

        // Update body class for overlay
        document.body.classList.toggle('sidebar-open', this.sidebarOpen && this.isMobile);
    }

    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        this.saveUserPreferences();
        this.adjustLayout();
        
        // Emit custom event
        this.emitEvent('sidebar:toggle', { open: this.sidebarOpen, isMobile: this.isMobile });
    }

    closeSidebar() {
        this.sidebarOpen = false;
        this.saveUserPreferences();
        this.adjustLayout();
        
        // Emit custom event
        this.emitEvent('sidebar:close', { isMobile: this.isMobile });
    }

    openSidebar() {
        this.sidebarOpen = true;
        this.saveUserPreferences();
        this.adjustLayout();
        
        // Emit custom event
        this.emitEvent('sidebar:open', { isMobile: this.isMobile });
    }

    handleNavigationClick(e, item) {
        // Remove active state from all items
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        navItems.forEach(navItem => {
            navItem.classList.remove('bg-blue-600', 'text-white', 'shadow-lg');
            navItem.classList.add('text-slate-300', 'hover:bg-slate-700', 'hover:text-white');
            
            // Remove active indicator
            const indicator = navItem.querySelector('.bg-white.rounded-full');
            if (indicator && indicator !== item.querySelector('.bg-white.rounded-full')) {
                indicator.remove();
            }
        });

        // Add active state to clicked item
        if (!item.classList.contains('bg-blue-600')) {
            item.classList.add('bg-blue-600', 'text-white', 'shadow-lg');
            item.classList.remove('text-slate-300', 'hover:bg-slate-700', 'hover:text-white');
            
            // Add active indicator if not present
            if (!item.querySelector('.bg-white.rounded-full')) {
                const indicator = document.createElement('span');
                indicator.className = 'ml-auto w-2 h-2 bg-white rounded-full';
                item.appendChild(indicator);
            }
        }

        // Add ripple effect
        this.createRippleEffect(item, e);

        // Close sidebar on mobile after navigation
        if (this.isMobile) {
            setTimeout(() => this.closeSidebar(), 300);
        }

        // Emit navigation event
        this.emitEvent('sidebar:navigate', { 
            href: item.href, 
            text: item.querySelector('span')?.textContent?.trim() || item.textContent.trim() 
        });
    }

    handleLogout(e) {
        // Show confirmation on desktop, proceed directly on mobile
        if (!this.isMobile) {
            const confirmed = confirm('Are you sure you want to logout?');
            if (!confirmed) {
                e.preventDefault();
                return;
            }
        }

        // Emit logout event
        this.emitEvent('sidebar:logout', {});
    }

    setupKeyboardNavigation() {
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        let currentIndex = -1;

        this.sidebar.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                // Let default tab behavior work
                return;
            }

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                
                if (e.key === 'ArrowDown') {
                    currentIndex = (currentIndex + 1) % navItems.length;
                } else {
                    currentIndex = currentIndex <= 0 ? navItems.length - 1 : currentIndex - 1;
                }

                navItems[currentIndex]?.focus();
            }

            if (e.key === 'Enter' && document.activeElement.classList.contains('nav-item')) {
                document.activeElement.click();
            }
        });
    }

    setupTooltips() {
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            // Skip if item already has a badge or indicator
            if (item.querySelector('.bg-red-500, .bg-white.rounded-full')) {
                return;
            }
            
            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.className = 'sidebar-tooltip';
            const text = item.querySelector('span')?.textContent?.trim() || item.textContent.trim();
            tooltip.textContent = text;
            item.appendChild(tooltip);

            // Show/hide tooltip on hover when sidebar is collapsed
            item.addEventListener('mouseenter', () => {
                if (this.sidebar.classList.contains('w-20')) {
                    tooltip.style.opacity = '1';
                }
            });

            item.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        });
    }

    saveUserPreferences() {
        try {
            localStorage.setItem('sidebarOpen', this.sidebarOpen.toString());
        } catch (e) {
            console.warn('Could not save sidebar preference:', e);
        }
    }

    loadUserPreferences() {
        try {
            const saved = localStorage.getItem('sidebarOpen');
            if (saved !== null) {
                this.sidebarOpen = saved === 'true';
            }
        } catch (e) {
            console.warn('Could not load sidebar preference:', e);
        }
    }

    emitEvent(eventName, data) {
        const event = new CustomEvent(eventName, { detail: data });
        document.dispatchEvent(event);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Public API methods
    isOpen() {
        return this.sidebarOpen;
    }

    isMobileView() {
        return this.isMobile;
    }

    setSidebarState(open) {
        if (typeof open === 'boolean') {
            this.sidebarOpen = open;
            this.saveUserPreferences();
            this.adjustLayout();
        }
    }

    // Add notification badge to menu item
    addNotification(menuItemText, count = 1) {
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            const text = item.querySelector('span')?.textContent?.trim() || item.textContent.trim();
            if (text.includes(menuItemText)) {
                let badge = item.querySelector('.notification-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'ml-auto px-2 py-1 text-xs bg-red-500 text-white rounded-full';
                    item.appendChild(badge);
                }
                badge.textContent = count > 99 ? '99+' : count.toString();
                badge.style.display = count > 0 ? 'block' : 'none';
            }
        });
    }

    // Remove notification badge
    removeNotification(menuItemText) {
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            const text = item.querySelector('span')?.textContent?.trim() || item.textContent.trim();
            if (text.includes(menuItemText)) {
                const badge = item.querySelector('.notification-badge');
                if (badge) {
                    badge.remove();
                }
            }
        });
    }

    // Create ripple effect on click
    createRippleEffect(element, event) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        // Add ripple styles if not already present
        if (!document.querySelector('#ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: scale(0);
                    animation: ripple-animation 0.6s ease-out;
                    pointer-events: none;
                }
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        element.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }

    // Setup enhanced hover effects
    setupHoverEffects() {
        const navItems = this.sidebar.querySelectorAll('.nav-item');
        
        navItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                // Add subtle glow effect
                item.style.boxShadow = '0 4px 20px rgba(59, 130, 246, 0.2)';
            });
            
            item.addEventListener('mouseleave', () => {
                if (!item.classList.contains('bg-blue-600')) {
                    item.style.boxShadow = '';
                }
            });
        });
    }

    // Setup mobile menu toggle
    setupMobileToggle() {
        const toggleButton = document.getElementById('mobile-menu-toggle');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (toggleButton) {
            toggleButton.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }
    }
}

// Initialize sidebar when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.sidebarManager = new SidebarManager();
});

// Make sidebar manager available globally
window.SidebarManager = SidebarManager;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SidebarManager;
}
