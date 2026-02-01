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
        
        // Password toggle
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('password-toggle')) {
                e.preventDefault();
                this.togglePasswordVisibility();
            }
        });
    }
    
    handleChange(e) {
        const { name, value, type, checked } = e.target;
        
        // Debug: Log input changes
        console.log('Input changed:', { name, value, type, checked });
        
        // Trim whitespace from text inputs
        const finalValue = type === 'checkbox' ? checked : value.trim();
        this.formData[name] = finalValue;
        
        // Debug: Log updated form data
        console.log('Updated form data:', this.formData);
        
        // Clear error for this field when user starts typing
        if (this.errors[name]) {
            this.errors[name] = '';
            this.updateErrorDisplay(name, '');
        }
    }
    
    validateForm() {
        const newErrors = {
            email: '',
            password: ''
        };
        
        let isValid = true;
        
        // Debug: Log current form data
        console.log('Current form data:', this.formData);
        console.log('Email value:', this.formData.email);
        console.log('Password value:', this.formData.password);
        
        // Email validation
        const emailValue = (this.formData.email || '').trim();
        console.log('Trimmed email value:', emailValue);
        
        if (!emailValue || emailValue === '') {
            newErrors.email = 'Email address is required';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
            newErrors.email = 'Please enter a valid email address';
            isValid = false;
        }
        
        // Password validation
        const passwordValue = this.formData.password || '';
        console.log('Password value:', passwordValue);
        
        if (!passwordValue || passwordValue.trim() === '') {
            newErrors.password = 'Password is required';
            isValid = false;
        } else if (passwordValue.length < 6) {
            newErrors.password = 'Password must be at least 6 characters long';
            isValid = false;
        }
        
        this.errors = newErrors;
        
        // Debug: Log validation results
        console.log('Validation errors:', newErrors);
        console.log('Is valid:', isValid);
        
        // Update error displays
        Object.keys(newErrors).forEach(key => {
            this.updateErrorDisplay(key, newErrors[key]);
        });
        
        return isValid;
    }
    
    updateErrorDisplay(fieldName, error) {
        const input = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = input?.parentElement.querySelector('.error-message');
        
        if (errorElement) {
            errorElement.textContent = error;
        }
        
        if (input) {
            if (error) {
                input.classList.add('error');
            } else {
                input.classList.remove('error');
            }
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
        
        console.log('Form data before validation:', this.formData);
        
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
            
            // Double-check the form data
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
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
            console.error('Login error:', error);
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
        const toggleButton = document.querySelector('.password-toggle');
        
        if (passwordInput) {
            passwordInput.type = this.showPassword ? 'text' : 'password';
        }
        
        if (toggleButton) {
            toggleButton.textContent = this.showPassword ? 'Hide' : 'Show';
        }
    }
    
    setLoading(isLoading) {
        this.isLoading = isLoading;
        const submitButton = document.querySelector('.login-button');
        const inputs = document.querySelectorAll('.form-input, .password-toggle');
        
        if (submitButton) {
            submitButton.disabled = isLoading;
            submitButton.classList.toggle('loading', isLoading);
            submitButton.textContent = isLoading ? 'Signing In...' : 'Sign In';
        }
        
        inputs.forEach(input => {
            input.disabled = isLoading;
        });
    }
    
    render() {
        // The HTML is already in the DOM, we just need to initialize the state
        this.updateFormFromData();
    }
    
    updateFormFromData() {
        // Update form inputs with current data
        const emailInput = document.querySelector('#email');
        const passwordInput = document.querySelector('#password');
        const rememberCheckbox = document.querySelector('#remember');
        
        // Initialize form data from input values (not the other way around)
        if (emailInput) {
            this.formData.email = emailInput.value.trim();
            console.log('Initialized email from input:', this.formData.email);
        }
        if (passwordInput) {
            this.formData.password = passwordInput.value;
            console.log('Initialized password from input:', this.formData.password);
        }
        if (rememberCheckbox) {
            this.formData.remember = rememberCheckbox.checked;
            console.log('Initialized remember from checkbox:', this.formData.remember);
        }
        
        console.log('Final initialized form data:', this.formData);
    }
}

// Initialize the login component when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new Login();
});
