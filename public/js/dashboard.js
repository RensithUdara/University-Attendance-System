document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('ui-ready');
    hidePageLoader();

    updateTime();
    window.setInterval(updateTime, 60000);
    initTooltips();
    initScrollReveal();
    initNavbarDepth();
    initRipples();
    initPageTransitions();
    initCounters();
    initFormLoading();
    autoDismissAlerts();
});

function updateTime() {
    const currentTimeElement = document.getElementById('currentTime');
    if (!currentTimeElement) return;

    currentTimeElement.textContent = new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
}

function initTooltips() {
    if (!window.bootstrap?.Tooltip) return;

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        window.bootstrap.Tooltip.getOrCreateInstance(element);
    });
}

function initScrollReveal() {
    const animatedElements = document.querySelectorAll(
        '.animate-fade-in-up, .stat-card, .dashboard-card, .course-card, .card.shadow'
    );

    if (!('IntersectionObserver' in window)) {
        animatedElements.forEach((element) => element.classList.add('ui-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    animatedElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(18px)';
        element.style.transition = `opacity 0.55s ease ${Math.min(index * 0.035, 0.28)}s, transform 0.55s ease ${Math.min(index * 0.035, 0.28)}s`;
        observer.observe(element);
    });
}

function initNavbarDepth() {
    const navbar = document.querySelector('.dashboard-navbar');
    if (!navbar) return;

    const update = () => {
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 10);
        navbar.style.boxShadow = window.scrollY > 10
            ? '0 14px 38px rgba(15, 23, 42, 0.12)'
            : '0 10px 35px rgba(15, 23, 42, 0.08)';
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
}

function initRipples() {
    document.querySelectorAll('.btn, .action-card').forEach((element) => {
        element.addEventListener('click', (event) => {
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const ripple = document.createElement('span');

            ripple.className = 'btn-ripple';
            ripple.style.width = `${size}px`;
            ripple.style.height = `${size}px`;
            ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
            element.appendChild(ripple);

            window.setTimeout(() => ripple.remove(), 650);
        });
    });
}

function initPageTransitions() {
    document.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');
            const target = link.getAttribute('target');
            const isBootstrapToggle = link.hasAttribute('data-bs-toggle');

            if (
                !href ||
                href.startsWith('#') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                target === '_blank' ||
                isBootstrapToggle ||
                event.ctrlKey ||
                event.metaKey
            ) {
                return;
            }

            event.preventDefault();
            showPageLoader('Loading page');
            document.body.classList.add('page-leaving');
            window.setTimeout(() => {
                window.location.href = href;
            }, 150);
        });
    });
}

function initCounters() {
    const values = document.querySelectorAll('.stat-value, .h5.font-weight-bold');
    const formatter = new Intl.NumberFormat();

    values.forEach((element) => {
        const text = element.textContent.trim();
        const suffix = text.endsWith('%') ? '%' : '';
        const number = Number.parseFloat(text.replace(/[,%]/g, ''));

        if (!Number.isFinite(number)) return;

        const start = performance.now();
        const duration = 720;

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(number * eased);

            element.textContent = `${formatter.format(current)}${suffix}`;
            if (progress < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
    });
}

function initFormLoading() {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton || !form.checkValidity()) return;

            submitButton.dataset.originalHtml = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitButton.disabled = true;
            showPageLoader('Processing request');
        });
    });
}

function autoDismissAlerts() {
    window.setTimeout(() => {
        document.querySelectorAll('.alert').forEach((alert) => {
            if (window.bootstrap?.Alert) {
                window.bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        });
    }, 5500);
}

function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 90px; right: 20px; z-index: 9999; max-width: 350px;';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `alert alert-${type} animate-slide-in-right mb-2`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'info' ? 'info' : 'exclamation'}-circle me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    toastContainer.appendChild(toast);

    window.setTimeout(() => toast.remove(), 5000);
}

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
