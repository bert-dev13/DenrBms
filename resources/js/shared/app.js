/**
 * Global utilities and shared functions
 * Used across multiple pages
 */

/**
 * Get patrol years for dropdowns (dynamic, matches Laravel PatrolYearHelper).
 * Returns years in descending order from current year.
 * @param {number} [count=10] Number of years to include
 * @returns {number[]}
 */
window.getPatrolYears = function(count = 10) {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear; year >= currentYear - count + 1; year--) {
        years.push(year);
    }
    return years;
};

window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `app-floating-notice app-floating-notice--${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    requestAnimationFrame(() => {
        requestAnimationFrame(() => notification.classList.add('app-floating-notice--visible'));
    });

    const animMs = 240;
    setTimeout(() => {
        notification.classList.remove('app-floating-notice--visible');
        setTimeout(() => notification.remove(), animMs);
    }, 5000);
};
