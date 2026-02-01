import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Initialize pagination when DOM is ready
window.initializePagination = function() {
    console.log('Pagination initialized');
};
