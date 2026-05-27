document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('ui-ready');
    hidePageLoader();

    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', () => {
            const passwordInput = document.getElementById('password');
            if (!passwordInput) return;

            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            togglePassword.classList.toggle('fa-eye', !isPassword);
            togglePassword.classList.toggle('fa-eye-slash', isPassword);
        });
    }

    document.querySelectorAll('.floating-input .form-control').forEach((input) => {
        const updateState = () => {
            input.parentElement.classList.toggle('focused', Boolean(input.value) || document.activeElement === input);
        };

        updateState();
        input.addEventListener('focus', updateState);
        input.addEventListener('blur', updateState);
        input.addEventListener('input', () => {
            input.classList.toggle('is-valid', input.value.length > 0 && input.checkValidity());
            if (input.checkValidity()) input.classList.remove('is-invalid');
        });
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton || !form.checkValidity()) return;

            submitButton.classList.add('loading');
            submitButton.disabled = true;
            showPageLoader('Signing you in');
        });
    });

    document.querySelectorAll('.btn, .btn-social, .auth-link').forEach((element) => {
        element.addEventListener('click', (event) => {
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const ripple = document.createElement('span');

            ripple.className = 'ripple';
            ripple.style.width = `${size}px`;
            ripple.style.height = `${size}px`;
            ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
            element.appendChild(ripple);

            window.setTimeout(() => ripple.remove(), 650);
        });
    });

    document.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');
            const target = link.getAttribute('target');

            if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || target === '_blank') {
                return;
            }

            event.preventDefault();
            showPageLoader('Loading page');
            document.body.classList.add('page-leaving');
            window.setTimeout(() => {
                window.location.href = href;
            }, 160);
        });
    });

    window.setTimeout(() => {
        document.querySelectorAll('.alert').forEach((alert) => {
            if (window.bootstrap?.Alert) {
                window.bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5000);
});

window.addEventListener('pageshow', () => {
    document.body.classList.remove('page-leaving');
    hidePageLoader();
});

function showPageLoader(message = 'Loading EduManage') {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    const text = loader.querySelector('.page-loader-text');
    if (text) text.textContent = message;

    loader.classList.remove('is-hidden');
}

function hidePageLoader() {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    window.setTimeout(() => {
        loader.classList.add('is-hidden');
    }, 280);
}

function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `alert alert-${type} glass-card animate-slide-in-right mb-2`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'info' ? 'info' : 'exclamation'}-circle me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    toastContainer.appendChild(toast);

    window.setTimeout(() => toast.remove(), 5000);
}
