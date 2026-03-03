// Theme Management JavaScript
class ThemeManager {
    constructor() {
        this.storageKey = 'denr-bms-theme';
        this.defaultTheme = 'light';
        
        // Check if we're on the login page - if so, don't initialize theme functionality
        if (this.isLoginPage()) {
            return; // Skip theme initialization on login page
        }
        
        // Get initial theme from window.__initialTheme (set by FOUC prevention script)
        // or fallback to stored/system theme
        this.currentTheme = window.__initialTheme || 
                           this.getStoredTheme() || 
                           this.getSystemTheme() || 
                           this.defaultTheme;
        
        // Initialize after DOM is ready
        this.init();
    }

    isLoginPage() {
        // Check if we're on the login page by URL or by the login page flag
        const isLoginByUrl = window.location.pathname === '/login' || 
                           window.location.pathname.includes('/login') ||
                           window.location.pathname === '/' && !document.querySelector('.sidebar');
        
        const isLoginByFlag = window.__loginPageTheme === 'light';
        
        return isLoginByUrl || isLoginByFlag;
    }

    getSystemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    init() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        // Check if theme is already correctly applied (by FOUC prevention script)
        const currentThemeAttr = document.documentElement.getAttribute('data-theme');
        const expectedTheme = this.currentTheme === 'dark' ? 'dark' : null;
        
        // Only apply theme if it's not already correctly set
        if (currentThemeAttr !== expectedTheme) {
            this.applyTheme(this.currentTheme);
        } else {
            // Theme is already correct, just update UI
            this.updateThemeToggleUI(this.currentTheme);
        }
        
        // Remove no-transition class after a short delay to allow initial render
        setTimeout(() => {
            if (document.body) {
                document.body.classList.remove('no-theme-transition');
            }
            // Also remove the inline style if it was added
            const inlineStyle = document.querySelector('style[data-no-transition]');
            if (inlineStyle) {
                inlineStyle.remove();
            }
        }, 100);
        
        // Watch for system theme changes
        this.watchSystemTheme();
        
        // Initialize theme toggle and set initial UI state
        this.initializeThemeToggle();
        
        // Save theme preference
        this.saveTheme(this.currentTheme);
        
        // Update meta theme-color for mobile browsers
        this.updateMetaThemeColor(this.currentTheme);
        
        // Listen for page visibility changes
        this.watchPageVisibility();
        
    }

    getStoredTheme() {
        try {
            return localStorage.getItem(this.storageKey);
        } catch (e) {
            console.warn('Unable to access localStorage:', e);
            return null;
        }
    }

    saveTheme(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        try {
            localStorage.setItem(this.storageKey, theme);
            // Also update the global variable for consistency
            window.__initialTheme = theme;
        } catch (e) {
            console.warn('Unable to save theme to localStorage:', e);
        }
    }

    applyTheme(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const html = document.documentElement;
        const body = document.body;
        
        // Check if theme is already applied
        const currentTheme = html.getAttribute('data-theme');
        const expectedTheme = theme === 'dark' ? 'dark' : null;
        
        if (currentTheme === expectedTheme) {
            // Theme is already applied, just update UI
            this.updateThemeToggleUI(theme);
            return;
        }
        
        // Apply new theme immediately (no full-page transition class to avoid lag on heavy pages)
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
            if (body) body.setAttribute('data-theme', 'dark');
        } else {
            html.removeAttribute('data-theme');
            if (body) body.removeAttribute('data-theme');
        }
        
        this.currentTheme = theme;
        
        // Update theme toggle UI
        this.updateThemeToggleUI(theme);
        
        // Update meta theme-color
        this.updateMetaThemeColor(theme);
        
        // Dispatch custom event
        this.dispatchThemeChange(theme);
    }

    toggleTheme() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.saveTheme(newTheme);
    }

    updateThemeToggleUI(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const themeToggle = document.getElementById('theme-toggle');
        
        if (!sunIcon || !moonIcon) {
            console.warn('Theme toggle icons not found');
            return;
        }
        
        try {
            if (theme === 'dark') {
                // Show moon icon, hide sun icon
                sunIcon.classList.add('opacity-0', 'rotate-180');
                sunIcon.classList.remove('opacity-100', 'rotate-0');
                
                moonIcon.classList.add('opacity-100', 'rotate-0');
                moonIcon.classList.remove('opacity-0', '-rotate-180');
                
                // Update aria label
                if (themeToggle) {
                    themeToggle.setAttribute('aria-label', 'Toggle light mode');
                }
            } else {
                // Show sun icon, hide moon icon
                sunIcon.classList.add('opacity-100', 'rotate-0');
                sunIcon.classList.remove('opacity-0', 'rotate-180');
                
                moonIcon.classList.add('opacity-0', '-rotate-180');
                moonIcon.classList.remove('opacity-100', 'rotate-0');
                
                // Update aria label
                if (themeToggle) {
                    themeToggle.setAttribute('aria-label', 'Toggle dark mode');
                }
            }
            
            this.updateTooltip(theme);
        } catch (error) {
            console.error('Error updating theme toggle UI:', error);
        }
    }

    updateTooltip(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const tooltip = document.querySelector('.theme-tooltip');
        if (tooltip) {
            tooltip.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
        }
    }

    updateMetaThemeColor(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }
        
        const color = theme === 'dark' ? '#1e293b' : '#ffffff';
        metaThemeColor.content = color;
    }

    watchSystemTheme() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        if (window.matchMedia) {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Only apply system theme if user hasn't set a preference
            if (!this.getStoredTheme()) {
                const systemTheme = darkModeQuery.matches ? 'dark' : 'light';
                this.applyTheme(systemTheme);
                this.saveTheme(systemTheme);
            }
            
            // Listen for system theme changes
            darkModeQuery.addEventListener('change', (e) => {
                if (!this.getStoredTheme()) {
                    const systemTheme = e.matches ? 'dark' : 'light';
                    this.applyTheme(systemTheme);
                    this.saveTheme(systemTheme);
                }
            });
        }
    }

    watchPageVisibility() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && window.themeManager) {
                // Re-apply theme when page becomes visible to sync theme
                window.themeManager.applyTheme(window.themeManager.getCurrentTheme());
            }
        });
    }

    dispatchThemeChange(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const event = new CustomEvent('themechange', {
            detail: { theme: theme }
        });
        document.dispatchEvent(event);
    }

    initializeThemeToggle() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            // Remove any existing event listeners and onclick attributes
            themeToggle.removeAttribute('onclick');
            
            // Clone and replace to remove all event listeners
            const newThemeToggle = themeToggle.cloneNode(true);
            themeToggle.parentNode.replaceChild(newThemeToggle, themeToggle);
            
            // Add click event listener with error handling
            newThemeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                try {
                    this.toggleTheme();
                } catch (error) {
                    console.error('Error toggling theme:', error);
                    // Fallback: manually toggle theme
                    const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                    this.applyTheme(newTheme);
                    this.saveTheme(newTheme);
                }
            });
            
            // Add keyboard support
            newThemeToggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    try {
                        this.toggleTheme();
                    } catch (error) {
                        console.error('Error toggling theme with keyboard:', error);
                        // Fallback: manually toggle theme
                        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                        this.applyTheme(newTheme);
                        this.saveTheme(newTheme);
                    }
                }
            });
        }
        
        // Initialize the UI based on current theme
        this.updateThemeToggleUI(this.currentTheme);
    }

    // Public methods
    getCurrentTheme() {
        return this.currentTheme;
    }

    setTheme(theme) {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        if (theme === 'light' || theme === 'dark') {
            this.applyTheme(theme);
            this.saveTheme(theme);
        }
    }

    resetToSystemTheme() {
        // Skip if on login page
        if (this.isLoginPage()) {
            return;
        }
        
        try {
            localStorage.removeItem(this.storageKey);
        } catch (e) {
            console.warn('Unable to clear theme storage:', e);
        }
        
        const systemTheme = this.getSystemTheme();
        this.applyTheme(systemTheme);
        this.saveTheme(systemTheme);
    }
}

// Global theme toggle function for backward compatibility
function toggleTheme() {
    if (window.themeManager) {
        window.themeManager.toggleTheme();
    }
}

// Initialize theme manager IMMEDIATELY to prevent flicker
(function() {
    // Check if FOUC prevention was already applied
    const foucPreventionApplied = window.__foucPreventionApplied;
    
    // Get theme before any scripts run
    const storedTheme = (() => {
        try {
            return localStorage.getItem('denr-bms-theme');
        } catch (e) {
            return null;
        }
    })();
    
    const systemTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const initialTheme = window.__initialTheme || storedTheme || systemTheme;
    
    // Check if we're on the login page - if so, force light theme
    const isLoginPage = window.location.pathname === '/login' || 
                       window.location.pathname.includes('/login') ||
                       window.location.pathname === '/' && !document.querySelector('.sidebar');
    
    const finalTheme = isLoginPage ? 'light' : initialTheme;
    
    // Only apply theme if FOUC prevention wasn't applied or failed
    if (!foucPreventionApplied) {
        // Apply theme immediately to prevent FOUC
        if (finalTheme === 'dark' && !isLoginPage) {
            document.documentElement.setAttribute('data-theme', 'dark');
            if (document.body) document.body.setAttribute('data-theme', 'dark');
        }
        
        // Add no-transition class to prevent initial animation
        if (document.body) {
            document.body.classList.add('no-theme-transition');
        }
    } else {
        // FOUC prevention was applied, just ensure consistency
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const expectedTheme = finalTheme === 'dark' ? 'dark' : null;
        
        if (currentTheme !== expectedTheme) {
            // Fix any inconsistency
            if (finalTheme === 'dark' && !isLoginPage) {
                document.documentElement.setAttribute('data-theme', 'dark');
                if (document.body) document.body.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
                if (document.body) document.body.removeAttribute('data-theme');
            }
        }
    }
    
    // Store the initial theme for ThemeManager
    window.__initialTheme = finalTheme;
    
    // Add login page flag if applicable
    if (isLoginPage) {
        window.__loginPageTheme = 'light';
    }
})();

// Initialize theme manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.themeManager = new ThemeManager();
});

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themeManager = new ThemeManager();
    });
} else {
    window.themeManager = new ThemeManager();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeManager;
}
