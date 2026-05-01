/**
 * Settings Page - Profile, password, loading states, password visibility
 */

function initPasswordToggles() {
    document.querySelectorAll('.settings-password-toggle').forEach(function(btn) {
        if (btn.dataset.initialized) return;
        btn.dataset.initialized = '1';
        var wrap = btn.closest('.settings-password-wrap');
        var input = document.getElementById(btn.getAttribute('data-target'));
        if (!wrap || !input) return;

        btn.addEventListener('click', function() {
            var visible = wrap.getAttribute('data-visible') === 'true';
            visible = !visible;
            wrap.setAttribute('data-visible', visible ? 'true' : 'false');
            input.type = visible ? 'text' : 'password';
            btn.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        });
    });
}

function setButtonLoading(btn, loading) {
    if (!btn) return;
    btn.disabled = loading;
    btn.classList.toggle('loading', loading);
}

function initFormSubmit() {
    var profileForm = document.getElementById('settings-profile-form');
    var passwordForm = document.getElementById('settings-password-form');
    var profileBtn = document.getElementById('profile-submit-btn');
    var passwordBtn = document.getElementById('password-submit-btn');

    if (profileForm && profileBtn) {
        profileForm.addEventListener('submit', function() {
            setButtonLoading(profileBtn, true);
        });
    }

    if (passwordForm && passwordBtn) {
        passwordForm.addEventListener('submit', function() {
            setButtonLoading(passwordBtn, true);
        });
    }

    // Re-enable buttons if user comes back (e.g. validation error)
}

function initSuccessToast() {
    var toast = document.getElementById('settings-success-toast');
    if (!toast) return;
    if (typeof window.replaceLucideIcons === 'function') {
        window.replaceLucideIcons(toast);
    }
    requestAnimationFrame(function() {
        toast.style.transform = 'translateX(0)';
    });
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(calc(100% + 2rem))';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3500);
}

document.addEventListener('DOMContentLoaded', function() {
    initPasswordToggles();
    initFormSubmit();
    initSuccessToast();
});
