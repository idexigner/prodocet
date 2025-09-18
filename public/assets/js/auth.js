/**
 * PRODOCET LMS - Authentication JavaScript
 * Author: Assistant
 * Description: JavaScript functionality for login and signup forms
 */

// Global variables and configuration
const AUTH_CONFIG = {
    minPasswordLength: 8,
    maxAttempts: 3,
    lockoutTime: 15 * 60 * 1000, // 15 minutes in milliseconds
};

// DOM elements
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const togglePasswordBtn = document.getElementById('togglePassword');
const passwordField = document.getElementById('password');

// Initialize authentication system
document.addEventListener('DOMContentLoaded', function() {
    initializeAuth();
    setupEventListeners();
});

/**
 * Initialize authentication system
 */
function initializeAuth() {
    // Check for stored login attempts
    const attempts = getLoginAttempts();
    if (attempts.count >= AUTH_CONFIG.maxAttempts) {
        const timeLeft = attempts.lockoutTime - Date.now();
        if (timeLeft > 0) {
            showLockoutMessage(Math.ceil(timeLeft / (60 * 1000)));
            return;
        } else {
            // Reset attempts if lockout time has passed
            resetLoginAttempts();
        }
    }

    // Initialize form validation
    initializeFormValidation();
}

/**
 * Setup event listeners for forms and interactions
 */
function setupEventListeners() {
    // Password visibility toggle
    if (togglePasswordBtn && passwordField) {
        togglePasswordBtn.addEventListener('click', togglePasswordVisibility);
    }

    // Forms will submit naturally to Laravel routes

    // Real-time validation
    setupRealTimeValidation();

    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeyboardShortcuts);
}




/**
 * Validate login form
 */
function validateLoginForm(email, password) {
    let isValid = true;
    
    // Email validation
    if (!email || !isValidEmail(email)) {
        showFieldError('email', 'Por favor, ingresa un correo electrónico válido.');
        isValid = false;
    } else {
        clearFieldError('email');
    }
    
    // Password validation
    if (!password || password.length < 6) {
        showFieldError('password', 'La contraseña debe tener al menos 6 caracteres.');
        isValid = false;
    } else {
        clearFieldError('password');
    }
    
    return isValid;
}

/**
 * Validate signup form
 */
function validateSignupForm(data) {
    let isValid = true;
    
    // First name validation
    if (!data.firstName || data.firstName.length < 2) {
        showFieldError('firstName', 'El nombre debe tener al menos 2 caracteres.');
        isValid = false;
    } else {
        clearFieldError('firstName');
    }
    
    // Last name validation
    if (!data.lastName || data.lastName.length < 2) {
        showFieldError('lastName', 'El apellido debe tener al menos 2 caracteres.');
        isValid = false;
    } else {
        clearFieldError('lastName');
    }
    
    // Email validation
    if (!data.email || !isValidEmail(data.email)) {
        showFieldError('signupEmail', 'Por favor, ingresa un correo electrónico válido.');
        isValid = false;
    } else {
        clearFieldError('signupEmail');
    }
    
    // Password validation
    if (!data.password || data.password.length < AUTH_CONFIG.minPasswordLength) {
        showFieldError('signupPassword', `La contraseña debe tener al menos ${AUTH_CONFIG.minPasswordLength} caracteres.`);
        isValid = false;
    } else if (!isStrongPassword(data.password)) {
        showFieldError('signupPassword', 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.');
        isValid = false;
    } else {
        clearFieldError('signupPassword');
    }
    
    // Confirm password validation
    if (data.password !== data.confirmPassword) {
        showFieldError('confirmPassword', 'Las contraseñas no coinciden.');
        isValid = false;
    } else {
        clearFieldError('confirmPassword');
    }
    
    // Role validation
    if (!data.role) {
        showFieldError('role', 'Por favor, selecciona un rol.');
        isValid = false;
    } else {
        clearFieldError('role');
    }
    
    // Terms validation
    if (!data.acceptTerms) {
        showFieldError('acceptTerms', 'Debes aceptar los términos y condiciones.');
        isValid = false;
    } else {
        clearFieldError('acceptTerms');
    }
    
    return isValid;
}

/**
 * Setup real-time validation
 */
function setupRealTimeValidation() {
    // Email validation on blur
    const emailFields = ['email', 'signupEmail'];
    emailFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                if (this.value && !isValidEmail(this.value)) {
                    showFieldError(fieldId, 'Formato de correo electrónico inválido.');
                } else {
                    clearFieldError(fieldId);
                }
            });
        }
    });
    
    // Password strength indicator
    const signupPassword = document.getElementById('signupPassword');
    if (signupPassword) {
        signupPassword.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });
    }
    
    // Confirm password validation
    const confirmPassword = document.getElementById('confirmPassword');
    if (confirmPassword && signupPassword) {
        confirmPassword.addEventListener('input', function() {
            if (this.value && this.value !== signupPassword.value) {
                showFieldError('confirmPassword', 'Las contraseñas no coinciden.');
            } else {
                clearFieldError('confirmPassword');
            }
        });
    }
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility() {
    const passwordField = document.getElementById('password');
    const toggleIcon = togglePasswordBtn.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

/**
 * Show signup form and hide login form
 */
function showSignupForm() {
    loginForm.classList.add('d-none');
    signupForm.classList.remove('d-none');
    
    // Add animation classes
    signupForm.classList.add('fade-in');
    
    // Focus first field
    setTimeout(() => {
        document.getElementById('firstName').focus();
    }, 300);
}

/**
 * Show login form and hide signup form
 */
function showLoginForm() {
    signupForm.classList.add('d-none');
    loginForm.classList.remove('d-none');
    
    // Add animation classes
    loginForm.classList.add('fade-in');
    
    // Focus email field
    setTimeout(() => {
        document.getElementById('email').focus();
    }, 300);
}

/**
 * Show field error
 */
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    // Remove existing feedback
    const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    // Add error message
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    field.parentNode.appendChild(feedback);
}

/**
 * Clear field error
 */
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    
    // Remove feedback
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.remove();
    }
}

/**
 * Show loading state on button
 */
function showLoadingState(button, text) {
    button.disabled = true;
    button.classList.add('btn-loading');
    button.setAttribute('data-original-text', button.innerHTML);
    button.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
}

/**
 * Hide loading state on button
 */
function hideLoadingState(button, originalText) {
    button.disabled = false;
    button.classList.remove('btn-loading');
    const original = button.getAttribute('data-original-text');
    button.innerHTML = original || `<i class="fas fa-sign-in-alt me-2"></i>${originalText}`;
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
    showAlert(message, 'success');
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    showAlert(message, 'danger');
}

/**
 * Show alert message
 */
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.style.position = 'fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.style.minWidth = '300px';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

/**
 * Utility function to validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Check if password is strong
 */
function isStrongPassword(password) {
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasMinLength = password.length >= AUTH_CONFIG.minPasswordLength;
    
    return hasUpperCase && hasLowerCase && hasNumbers && hasMinLength;
}

/**
 * Update password strength indicator
 */
function updatePasswordStrength(password) {
    // This would update a visual password strength indicator
    // Implementation depends on UI design
}

/**
 * Handle keyboard shortcuts
 */
function handleKeyboardShortcuts(e) {
    // Alt + L for login form
    if (e.altKey && e.key === 'l') {
        e.preventDefault();
        showLoginForm();
    }
    
    // Alt + S for signup form
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        showSignupForm();
    }
}

/**
 * Simulate user authentication (replace with real API call)
 */
async function authenticateUser(email, password) {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Demo credentials
    const demoUsers = {
        'admin@prodocet.com': { password: 'admin123', role: 'admin', name: 'Administrador' },
        'teacher@prodocet.com': { password: 'teacher123', role: 'teacher', name: 'Profesor' },
        'student@prodocet.com': { password: 'student123', role: 'student', name: 'Estudiante' }
    };
    
    const user = demoUsers[email];
    if (user && user.password === password) {
        return {
            success: true,
            user: {
                id: Math.random().toString(36).substr(2, 9),
                email: email,
                name: user.name,
                role: user.role,
                avatar: 'https://via.placeholder.com/40x40?text=' + user.name.charAt(0)
            }
        };
    } else {
        incrementLoginAttempts();
        return {
            success: false,
            message: 'Credenciales incorrectas. Por favor, verifica tu email y contraseña.'
        };
    }
}

/**
 * Simulate user registration (replace with real API call)
 */
async function createUser(userData) {
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Simulate success (in real app, check if email exists, etc.)
    return {
        success: true,
        user: {
            id: Math.random().toString(36).substr(2, 9),
            email: userData.email,
            name: `${userData.firstName} ${userData.lastName}`,
            role: userData.role
        }
    };
}


/**
 * Login attempts management
 */
function getLoginAttempts() {
    const attempts = localStorage.getItem('login_attempts');
    return attempts ? JSON.parse(attempts) : { count: 0, lockoutTime: 0 };
}

function incrementLoginAttempts() {
    const attempts = getLoginAttempts();
    attempts.count++;
    
    if (attempts.count >= AUTH_CONFIG.maxAttempts) {
        attempts.lockoutTime = Date.now() + AUTH_CONFIG.lockoutTime;
        showLockoutMessage(AUTH_CONFIG.lockoutTime / (60 * 1000));
    }
    
    localStorage.setItem('login_attempts', JSON.stringify(attempts));
}

function resetLoginAttempts() {
    localStorage.removeItem('login_attempts');
}

function handleLoginFailure(message) {
    const attempts = getLoginAttempts();
    const remainingAttempts = AUTH_CONFIG.maxAttempts - attempts.count;
    
    if (remainingAttempts > 0) {
        showErrorMessage(`${message} Te quedan ${remainingAttempts} intentos.`);
    }
}

function showLockoutMessage(minutes) {
    showErrorMessage(`Demasiados intentos fallidos. Cuenta bloqueada por ${minutes} minutos.`);
}

/**
 * Initialize form validation with Bootstrap
 */
function initializeFormValidation() {
    // Add Bootstrap validation classes
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}