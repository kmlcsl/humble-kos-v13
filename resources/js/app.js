import "./bootstrap";

// App JavaScript untuk Laravel 12 + Vite

// Import Alpine.js (untuk reactive components)
import Alpine from "alpinejs";

// Import Axios (untuk AJAX requests)
import axios from "axios";

// Setup Axios defaults
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Setup CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Custom JavaScript Functions
document.addEventListener("DOMContentLoaded", function () {
    // Initialize tooltips if Bootstrap is loaded
    initTooltips();

    // Initialize smooth scrolling
    initSmoothScrolling();

    // Initialize form validations
    initFormValidations();

    // Register form submission
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const originalText = window.HumbleKos.showLoading(button);

            const formData = new FormData(this);

            window.axios.post(this.action, formData)
                .then(response => {
                    window.HumbleKos.hideLoading(button, originalText);
                    if (response.data.success) {
                        window.HumbleKos.showNotification('Sukses', response.data.message, 'success');
                        if (response.data.redirect) {
                            setTimeout(() => {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        }
                    } else {
                        window.HumbleKos.showNotification('Error', response.data.message || 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(error => {
                    window.HumbleKos.hideLoading(button, originalText);
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        let errorMessages = '';
                        for (const field in errors) {
                            errorMessages += `${errors[field].join('<br>')}<br>`;
                        }
                        window.HumbleKos.showNotification('Validasi Gagal', errorMessages, 'error');
                    } else {
                        console.error('Registration error:', error);
                        window.HumbleKos.showNotification('Error Server', 'Terjadi kesalahan server, silakan coba lagi nanti.', 'error');
                    }
                });
        });
    }

    // Login form submission
    const loginForm = document.querySelector('form[action="' + window.location.origin + '/login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const originalText = window.HumbleKos.showLoading(button);

            const formData = new FormData(this);

            window.axios.post(this.action, formData)
                .then(response => {
                    window.HumbleKos.hideLoading(button, originalText);
                    if (response.data.success) {
                        window.HumbleKos.showNotification('Sukses', response.data.message, 'success');
                        if (response.data.redirect) {
                            setTimeout(() => {
                                window.location.href = response.data.redirect;
                            }, 2000);
                        }
                    } else {
                        window.HumbleKos.showNotification('Error', response.data.message || 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(error => {
                    window.HumbleKos.hideLoading(button, originalText);
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            let errorMessages = '';
                            for (const field in errors) {
                                errorMessages += `${errors[field].join('<br>')}<br>`;
                            }
                            window.HumbleKos.showNotification('Validasi Gagal', errorMessages, 'error');
                        } else if (error.response.status === 401) {
                            window.HumbleKos.showNotification('Login Gagal', error.response.data.message || 'Username atau password salah.', 'error');
                        }
                    } else {
                        console.error('Login error:', error);
                        window.HumbleKos.showNotification('Error Server', 'Terjadi kesalahan server, silakan coba lagi nanti.', 'error');
                    }
                });
        });
    }
});

// Initialize Bootstrap Tooltips
function initTooltips() {
    if (typeof bootstrap !== "undefined") {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Smooth Scrolling
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        const href = anchor.getAttribute('href');
        // Only process if href is not just "#" and is a valid selector
        if (href && href.length > 1 && href !== '#') {
            anchor.addEventListener("click", function (e) {
                const targetId = this.getAttribute("href");
                try {
                    // Validate selector before querying
                    if (targetId && targetId.match(/^#[a-zA-Z][\w-]*$/)) {
                        const target = document.querySelector(targetId);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({
                                behavior: "smooth",
                                block: "start",
                            });
                        }
                    }
                } catch (error) {
                    // Silently ignore invalid selectors
                    console.warn(`Invalid selector: ${targetId}`);
                }
            });
        }
    });
}

// Form Validations
function initFormValidations() {
    const forms = document.querySelectorAll(".needs-validation");
    Array.from(forms).forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add("was-validated");
        });
    });
}

// Utility Functions
window.HumbleKos = {
    // Show loading spinner
    showLoading: function (button) {
        const original = button.innerHTML;
        button.innerHTML = '<span class="loading-spinner"></span> Loading...';
        button.disabled = true;
        return original;
    },

    // Hide loading spinner
    hideLoading: function (button, originalText) {
        button.innerHTML = originalText;
        button.disabled = false;
    },

    // Show notification
    showNotification: function (title, message, type = "success") {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${iconClass}"></i>
            </div>
            <div class="notification-content">
                <h5>${title}</h5>
                <p>${message}</p>
            </div>
            <button class="notification-close">&times;</button>
        `;

        container.appendChild(notification);

        // Trigger the slide in animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Auto-dismiss after 10 seconds
        const timeout = setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                container.removeChild(notification);
            }, 500);
        }, 10000);

        // Dismiss on click
        notification.querySelector('.notification-close').addEventListener('click', () => {
            clearTimeout(timeout);
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (container.contains(notification)) {
                    container.removeChild(notification);
                }
            }, 500);
        });
    },

    // AJAX Form Submit
    submitForm: function (form, callback) {
        const formData = new FormData(form);

        axios
            .post(form.action, formData)
            .then((response) => {
                if (callback) callback(response.data);
            })
            .catch((error) => {
                console.error("Form submission error:", error);
                this.showNotification(
                    "Terjadi kesalahan, silakan coba lagi.",
                    "error"
                );
            });
    },
};

// Export for global use
export { Alpine, axios };
