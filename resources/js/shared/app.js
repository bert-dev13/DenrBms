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
    notification.style.cssText = `
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 10000;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        font-weight: 500;
        max-width: 400px;
        color: white;
        transition: opacity 0.3s ease-in-out;
    `;

    if (type === 'success') {
        notification.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#ef4444';
    } else {
        notification.style.backgroundColor = '#3b82f6';
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
};
