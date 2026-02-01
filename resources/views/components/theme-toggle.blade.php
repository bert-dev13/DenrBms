<div class="theme-toggle-container relative">
    <button 
        id="theme-toggle" 
        class="theme-toggle-btn relative inline-flex items-center justify-center p-2 rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        aria-label="Toggle dark mode"
    >
        <!-- Sun Icon (Light Mode) -->
        <svg 
            class="w-5 h-5 text-yellow-500 transition-all duration-300 opacity-100 rotate-0" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
            id="sun-icon"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg 
            class="w-5 h-5 text-gray-700 dark:text-gray-300 absolute transition-all duration-300 opacity-0 -rotate-180" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
            id="moon-icon"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>
    
    <!-- Tooltip -->
    <div class="theme-tooltip absolute top-full right-0 mt-2 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded opacity-0 pointer-events-none transition-opacity duration-200 whitespace-nowrap z-50">
        Switch to Dark Mode
    </div>
</div>

<script>
// Simple initialization that works with ThemeManager
(function() {
    // Initialize toggle immediately to prevent flicker
    function initializeToggleImmediately() {
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const themeToggle = document.getElementById('theme-toggle');
        const tooltip = document.querySelector('.theme-tooltip');
        
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
                
                // Update tooltip
                if (tooltip) {
                    tooltip.textContent = 'Switch to Light Mode';
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
                
                // Update tooltip
                if (tooltip) {
                    tooltip.textContent = 'Switch to Dark Mode';
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

.theme-toggle-btn:hover .theme-tooltip {
    opacity: 1;
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

/* Tooltip positioning */
.theme-tooltip {
    white-space: nowrap;
    pointer-events: none;
    transform: translateX(-50%);
    left: 50%;
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
