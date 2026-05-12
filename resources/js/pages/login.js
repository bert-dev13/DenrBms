const LOGIN_SUCCESS_TOAST_ID = 'denr-bms-login-success-toast';
const LOGIN_SUCCESS_MESSAGE = 'Login successful! Redirecting...';

/**
 * Single entry point for the post-auth success toast. Synchronous window lock + DOM id
 * guarantee at most one toast per page lifetime (Strict Mode re-bootstrap, double handlers, etc.).
 */
function showLoginSuccessToastOnce() {
    if (typeof window !== 'undefined' && window.__denrBmsLoginSuccessToastLock) {
        return;
    }
    if (typeof window !== 'undefined') {
        window.__denrBmsLoginSuccessToastLock = true;
    }
    if (document.getElementById(LOGIN_SUCCESS_TOAST_ID)) {
        return;
    }

    const root = document.createElement('div');
    root.id = LOGIN_SUCCESS_TOAST_ID;
    root.className = 'login-success-toast';
    root.setAttribute('role', 'status');
    root.setAttribute('aria-live', 'polite');
    root.setAttribute('aria-atomic', 'true');

    const panel = document.createElement('div');
    panel.className = 'login-success-toast__panel';

    const iconWrap = document.createElement('span');
    iconWrap.className = 'login-success-toast__icon';
    iconWrap.setAttribute('aria-hidden', 'true');
    iconWrap.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';

    const text = document.createElement('p');
    text.className = 'login-success-toast__text';
    text.textContent = LOGIN_SUCCESS_MESSAGE;

    panel.appendChild(iconWrap);
    panel.appendChild(text);
    root.appendChild(panel);
    // Attach under <html> so login-page reduced-motion / animation resets do not strip toast transitions.
    document.documentElement.appendChild(root);

    requestAnimationFrame(() => {
        root.classList.add('login-success-toast--visible');
    });
}

function dismissLoginSuccessToastForRedirect(done) {
    const el = document.getElementById(LOGIN_SUCCESS_TOAST_ID);
    if (!el) {
        done();
        return;
    }
    el.classList.remove('login-success-toast--visible');
    el.classList.add('login-success-toast--leaving');
    const ms = window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches ? 0 : 220;
    window.setTimeout(done, ms);
}

class Login {
    constructor() {
        this._listenersAbort = new AbortController();
        this.formData = {
            email: '',
            password: '',
            remember: false
        };
        
        this.errors = {
            email: '',
            password: ''
        };
        
        this.isLoading = false;
        this.showPassword = false;
        /** Delay before redirect after successful login (toast visible ~1–2s) */
        this.successRedirectDelayMs = 1600;

        this.init();
    }
    
    init() {
        this._submitInFlight = false;
        this.setupEventListeners();
        this.render();
    }

    setupEventListeners() {
        const signal = this._listenersAbort.signal;
        const form = document.querySelector('.login-form');
        if (form) {
            this._boundFormSubmit = (e) => {
                e.preventDefault();
                this.handleSubmit();
            };
            form.addEventListener('submit', this._boundFormSubmit, { signal });
        }

        // Input changes
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('form-input')) {
                this.handleChange(e);
            }
        }, { signal });
        
        // Checkbox changes
        document.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                this.handleChange(e);
            }
        }, { signal });
        
        // Password toggle (use closest for clicks on icon inside button)
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.password-toggle');
            if (toggle) {
                e.preventDefault();
                this.togglePasswordVisibility();
            }
        }, { signal });
    }

    /**
     * Detach listeners so a second Login bootstrap (duplicate script eval / HMR) cannot stack handlers.
     */
    destroy() {
        this._listenersAbort.abort();
    }
    
    handleChange(e) {
        const { name, value, type, checked } = e.target;
        const finalValue = type === 'checkbox' ? checked : (value || '').trim();
        this.formData[name] = finalValue;
        
        // Update filled class for floating label
        if (value !== undefined && type !== 'checkbox') {
            e.target.classList.toggle('filled', !!finalValue);
        }
        
        if (this.errors[name]) {
            this.errors[name] = '';
            this.updateErrorDisplay(name, '');
        }
    }
    
    validateForm() {
        const newErrors = { email: '', password: '' };
        let isValid = true;
        const emailValue = (this.formData.email || '').trim();
        const passwordValue = this.formData.password || '';
        
        if (!emailValue) {
            newErrors.email = 'Email address is required';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
            newErrors.email = 'Please enter a valid email address';
            isValid = false;
        }
        
        if (!passwordValue) {
            newErrors.password = 'Password is required';
            isValid = false;
        } else if (passwordValue.length < 6) {
            newErrors.password = 'Password must be at least 6 characters long';
            isValid = false;
        }
        
        this.errors = newErrors;
        Object.keys(newErrors).forEach(key => this.updateErrorDisplay(key, newErrors[key]));
        return isValid;
    }
    
    updateErrorDisplay(fieldName, error) {
        const input = document.querySelector(`[name="${fieldName}"]`);
        const formGroup = input?.closest('.form-group');
        const errorElement = formGroup?.querySelector('.error-message');
        
        if (errorElement) errorElement.textContent = error || '';
        if (input) {
            input.classList.toggle('error', !!error);
            input.setAttribute('aria-invalid', !!error);
        }
    }
    
    async handleSubmit() {
        // Get fresh form data from DOM as fallback
        const emailInput = document.querySelector('#email');
        const passwordInput = document.querySelector('#password');
        const rememberCheckbox = document.querySelector('#remember');
        
        // Update form data with current input values
        if (emailInput) this.formData.email = emailInput.value.trim();
        if (passwordInput) this.formData.password = passwordInput.value;
        if (rememberCheckbox) this.formData.remember = rememberCheckbox.checked;
        
        if (!this.validateForm()) {
            return;
        }

        if (this._submitInFlight || window.__denrBmsLoginSubmitInFlight) {
            return;
        }
        this._submitInFlight = true;
        window.__denrBmsLoginSubmitInFlight = true;

        // Disable immediately after validation so double-clicks cannot enqueue duplicate requests.
        this.setLoading(true);

        let willRedirect = false;

        try {
            const form = document.querySelector('.login-form');
            if (!form) {
                return;
            }
            let formData = new FormData();

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.set('_token', csrfToken);

            // Add form fields
            formData.set('email', this.formData.email);
            formData.set('password', this.formData.password);
            if (this.formData.remember) {
                formData.set('remember', '1');
            }

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                willRedirect = true;
                showLoginSuccessToastOnce();
                await new Promise((resolve) => setTimeout(resolve, this.successRedirectDelayMs));
                const target = result.redirect || '/dashboard';
                dismissLoginSuccessToastForRedirect(() => {
                    window.location.href = target;
                });
                return;
            }

            // Handle server-side validation errors
            this.handleServerErrors(result);
        } catch (error) {
            this.showErrorMessage('Network error. Please try again.');
        } finally {
            if (!willRedirect) {
                this._submitInFlight = false;
                window.__denrBmsLoginSubmitInFlight = false;
                this.setLoading(false);
            }
        }
    }
    
    handleServerErrors(result) {
        // Clear existing errors
        this.errors = { email: '', password: '' };
        Object.keys(this.errors).forEach(key => {
            this.updateErrorDisplay(key, '');
        });

        if (typeof result.message === 'string') {
            this.showErrorMessage(result.message);
        }
        
        if (result.errors && typeof result.errors === 'object') {
            Object.keys(result.errors).forEach(key => {
                if (this.errors.hasOwnProperty(key)) {
                    const errorMessages = Array.isArray(result.errors[key]) 
                        ? result.errors[key] 
                        : [result.errors[key]];
                    this.errors[key] = errorMessages[0];
                    this.updateErrorDisplay(key, this.errors[key]);
                }
            });
        }
    }
    
    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }

    showMessage(message, type) {
        if (type === 'success') {
            return;
        }

        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;

        // Insert at the beginning of the form
        const form = document.querySelector('.login-form');
        if (!form) {
            return;
        }
        form.insertBefore(alert, form.firstChild);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
    
    togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
        const passwordInput = document.querySelector('#password');
        const toggleBtn = document.querySelector('.password-toggle');
        
        if (passwordInput) {
            passwordInput.type = this.showPassword ? 'text' : 'password';
        }
        
        if (toggleBtn) {
            toggleBtn.classList.toggle('password-visible', this.showPassword);
            toggleBtn.setAttribute('aria-pressed', this.showPassword);
            if (typeof window.replaceLucideIcons === 'function') window.replaceLucideIcons();
        }
    }
    
    setLoading(isLoading) {
        this.isLoading = isLoading;
        const submitButton = document.querySelector('.login-button');
        const buttonText = submitButton?.querySelector('.login-button-text');
        const inputs = document.querySelectorAll('.form-input, .password-toggle');
        
        if (submitButton) {
            submitButton.disabled = isLoading;
            submitButton.classList.toggle('loading', isLoading);
            if (buttonText) buttonText.textContent = isLoading ? 'Signing In...' : 'Sign In';
        }
        
        inputs.forEach(input => { input.disabled = isLoading; });
    }
    
    render() {
        // The HTML is already in the DOM, we just need to initialize the state
        this.updateFormFromData();
    }
    
    updateFormFromData() {
        const emailInput = document.querySelector('#email');
        const passwordInput = document.querySelector('#password');
        const rememberCheckbox = document.querySelector('#remember');
        
        if (emailInput) {
            this.formData.email = emailInput.value.trim();
            emailInput.classList.toggle('filled', !!this.formData.email);
        }
        if (passwordInput) this.formData.password = passwordInput.value;
        if (rememberCheckbox) this.formData.remember = rememberCheckbox.checked;
    }
}

function bootstrapLoginPage() {
    if (!document.body || !document.body.classList.contains('login-page')) {
        return;
    }

    if (window.__denrBmsLoginControllerInstance?.destroy) {
        window.__denrBmsLoginControllerInstance.destroy();
    }
    window.__denrBmsLoginControllerInstance = new Login();

    requestAnimationFrame(() => {
        document.body.classList.add('login-loaded');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapLoginPage);
} else {
    bootstrapLoginPage();
}
