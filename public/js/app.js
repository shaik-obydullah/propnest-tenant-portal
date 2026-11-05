/* PropNest portal interactions */
(function () {
    'use strict';

    const qs = (selector, scope) => (scope || document).querySelector(selector);
    const qsa = (selector, scope) => Array.from((scope || document).querySelectorAll(selector));

    /* -----------------------------------------
       Sidebar (mobile)
       ----------------------------------------- */
    const sidebar = qs('#sidebar');
    const scrim = qs('.sidebar-scrim');

    const setSidebar = (open) => {
        if (!sidebar) return;

        sidebar.classList.toggle('is-open', open);
        if (scrim) scrim.classList.toggle('is-visible', open);
        document.body.style.overflow = open ? 'hidden' : '';

        qsa('[data-sidebar-open]').forEach((btn) => btn.setAttribute('aria-expanded', String(open)));
    };

    qsa('[data-sidebar-open]').forEach((btn) => {
        btn.setAttribute('aria-expanded', 'false');
        btn.addEventListener('click', () => setSidebar(true));
    });

    qsa('[data-sidebar-close]').forEach((btn) => {
        btn.addEventListener('click', () => setSidebar(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) {
            setSidebar(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) setSidebar(false);
    });

    /* -----------------------------------------
       Flash messages
       ----------------------------------------- */
    qsa('[data-flash-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const flash = btn.closest('[data-flash]');
            if (!flash) return;
            flash.style.transition = 'opacity .18s ease, transform .18s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-4px)';
            window.setTimeout(() => flash.remove(), 190);
        });
    });

    qsa('[data-flash]').forEach((flash) => {
        window.setTimeout(() => {
            if (!document.body.contains(flash)) return;
            flash.style.transition = 'opacity .3s ease, transform .3s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-4px)';
            window.setTimeout(() => flash.remove(), 320);
        }, 6500);
    });

    /* -----------------------------------------
       Demo credential buttons
       ----------------------------------------- */
    qsa('[data-demo-email]').forEach((button) => {
        button.addEventListener('click', () => {
            const email = button.getAttribute('data-demo-email') || '';
            const password = button.getAttribute('data-demo-password') || 'password';
            const emailInput = qs('#email');
            const passwordInput = qs('#password');

            if (!emailInput || !passwordInput) return;

            emailInput.value = email;
            passwordInput.value = password;

            [emailInput, passwordInput].forEach((input) => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.classList.add('is-filled');
                window.setTimeout(() => input.classList.remove('is-filled'), 900);
            });

            passwordInput.focus();
            passwordInput.setSelectionRange(password.length, password.length);
        });
    });

    /* -----------------------------------------
       Upload preview
       ----------------------------------------- */
    qsa('[data-upload-input]').forEach((input) => {
        const zone = input.closest('.upload-zone');
        const target = zone ? qs('[data-upload-name]', zone) : null;

        input.addEventListener('change', () => {
            if (!target) return;

            const file = input.files && input.files[0];
            if (!file) {
                target.textContent = '';
                return;
            }

            const size = file.size >= 1024 * 1024
                ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';

            target.textContent = file.name + ' · ' + size;
            zone.classList.add('has-file');
        });

        ['dragenter', 'dragover'].forEach((type) => {
            input.addEventListener(type, () => zone && zone.classList.add('is-dragging'));
        });

        ['dragleave', 'drop'].forEach((type) => {
            input.addEventListener(type, () => zone && zone.classList.remove('is-dragging'));
        });
    });

    /* -----------------------------------------
       Print buttons
       ----------------------------------------- */
    qsa('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    /* -----------------------------------------
       Sticky header state (marketing site)
       ----------------------------------------- */
    const header = qs('[data-header]');
    if (header) {
        const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    /* -----------------------------------------
       Confirm destructive submits
       ----------------------------------------- */
    qsa('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Are you sure?';
            if (!window.confirm(message)) event.preventDefault();
        });
    });

    /* -----------------------------------------
       Auto-submit filter selects
       ----------------------------------------- */
    qsa('[data-auto-submit]').forEach((select) => {
        select.addEventListener('change', () => {
            if (select.form) select.form.submit();
        });
    });
})();
