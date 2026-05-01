class Login {
    constructor() {
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
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.render();
    }
    
    setupEventListeners() {
        // Form submission
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('login-form')) {
                e.preventDefault();
                this.handleSubmit();
            }
        });
        
        // Input changes
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('form-input')) {
                this.handleChange(e);
            }
        });
        
        // Checkbox changes
        document.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                this.handleChange(e);
            }
        });
        
        // Password toggle (use closest for clicks on icon inside button)
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.password-toggle');
            if (toggle) {
                e.preventDefault();
                this.togglePasswordVisibility();
            }
        });
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
        
        this.setLoading(true);
        
        try {
            const form = document.querySelector('.login-form');
            
            // Create FormData manually to ensure all fields are included
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
                // Show success message and redirect
                this.showSuccessMessage('Login successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = result.redirect || '/dashboard';
                }, 1000);
            } else {
                // Handle server-side validation errors
                this.handleServerErrors(result);
            }
        } catch (error) {
            this.showErrorMessage('Network error. Please try again.');
        } finally {
            this.setLoading(false);
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
    
    showSuccessMessage(message) {
        this.showMessage(message, 'success');
    }
    
    showErrorMessage(message) {
        this.showMessage(message, 'error');
    }
    
    showMessage(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;

        // Insert at the beginning of the form
        const form = document.querySelector('.login-form');
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

// Initialize the login component when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.body && document.body.classList.contains('login-page')) {
        requestAnimationFrame(() => {
            document.body.classList.add('login-loaded');
        });
    }
    new Login();
});
