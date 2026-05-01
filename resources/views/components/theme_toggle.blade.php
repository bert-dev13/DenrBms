<div class="theme-toggle-container">
    <button 
        id="theme-toggle" 
        class="theme-toggle-btn relative inline-flex items-center justify-center p-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none"
        aria-label="Toggle dark mode"
    >
        <!-- Sun Icon (Light Mode) -->
        <span id="sun-icon" class="theme-icon-wrap w-5 h-5 text-yellow-500 transition-all duration-300 opacity-100 rotate-0 inline-flex items-center justify-center pointer-events-none">
            <i data-lucide="sun" class="lucide-icon w-full h-full"></i>
        </span>
        
        <!-- Moon Icon (Dark Mode) - absolute overlay, pointer-events-none when hidden to avoid layout artifacts -->
        <span id="moon-icon" class="theme-icon-wrap w-5 h-5 text-gray-700 dark:text-gray-300 absolute inset-0 m-auto transition-all duration-300 opacity-0 -rotate-180 inline-flex items-center justify-center pointer-events-none">
            <i data-lucide="moon" class="lucide-icon w-full h-full"></i>
        </span>
    </button>
</div>

<script>
// Simple initialization that works with ThemeManager
(function() {
    // Initialize toggle immediately to prevent flicker
    function initializeToggleImmediately() {
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        /* #sun-icon and #moon-icon are wrappers; Lucide replaces inner <i> with SVG */
        const themeToggle = document.getElementById('theme-toggle');
        
        if (!sunIcon || !moonIcon) {
            return false;
        }
        
        // Get the current theme from multiple sources
        const currentTheme = window.__initialTheme || 
                            (document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light') ||
                            (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        try {
            if (currentTheme === 'dark') {
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
            return true;
        } catch (error) {
            console.error('Error initializing theme toggle immediately:', error);
            return false;
        }
    }
    
    // Try to initialize immediately
    if (!initializeToggleImmediately()) {
        // If immediate initialization fails, try again when DOM is ready
        function initializeToggle() {
            if (window.themeManager) {
                window.themeManager.updateThemeToggleUI(window.themeManager.getCurrentTheme());
            } else {
                // Fallback: try to initialize manually
                initializeToggleImmediately();
            }
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeToggle);
        } else {
            initializeToggle();
        }
    }
})();
</script>

<style>
/* Theme Toggle Specific Styles */
.theme-toggle-container {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.theme-toggle-btn {
    min-width: 40px;
    min-height: 40px;
    position: relative;
    border: none;
    background: transparent;
    cursor: pointer;
    outline: none;
}

.theme-toggle-btn:active {
    transform: scale(0.95);
}

/* Ensure consistent positioning */
.theme-toggle-btn svg {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .theme-toggle-btn {
        min-width: 36px;
        min-height: 36px;
    }
    
    .theme-toggle-btn svg {
        width: 1.125rem;
        height: 1.125rem;
    }
}

/* Dark mode transitions will be handled by the main theme CSS */
</style>
