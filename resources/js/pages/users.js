const USER_MODAL_ANIM_MS = 240;

document.addEventListener('DOMContentLoaded', () => {
    const createUserModal = document.getElementById('create-user-modal');
    const hasCreateUserErrors = createUserModal?.dataset.hasErrors === '1';
    const usersConfig = document.getElementById('users-page-config');

    const routes = {
        pdf: usersConfig?.dataset.exportPdf || '',
        excel: usersConfig?.dataset.exportExcel || '',
        print: usersConfig?.dataset.exportPrint || '',
    };

    const scheduleToastDismiss = (id, delay = 3200) => {
        const toast = document.getElementById(id);
        if (!toast) return;

        setTimeout(() => {
            toast.classList.add('is-hiding');
            setTimeout(() => toast.remove(), USER_MODAL_ANIM_MS);
        }, delay);
    };

    scheduleToastDismiss('users-success-toast');
    scheduleToastDismiss('users-error-toast');
    scheduleToastDismiss('users-validation-toast', 2600);

    const toggleProtectedArea = (roleElement, targetElement, fieldElementId = 'create-protected-area-field') => {
        if (!roleElement || !targetElement) return;
        const targetField = document.getElementById(fieldElementId);
        const isPaUser = roleElement.value === 'pa_user';
        targetElement.disabled = !isPaUser;
        if (!isPaUser) targetElement.value = '';
        if (targetField) {
            targetField.classList.toggle('hidden', !isPaUser);
        }
    };

    const createRole = document.getElementById('create-role');
    const createProtectedArea = document.getElementById('create-protected-area');
    toggleProtectedArea(createRole, createProtectedArea);
    if (hasCreateUserErrors) {
        showCreateUserModal();
    }
    if (createRole) {
        createRole.addEventListener('change', () => toggleProtectedArea(createRole, createProtectedArea));
    }

    const editRole = document.getElementById('edit-role');
    const editProtectedArea = document.getElementById('edit-protected-area');
    if (editRole && editProtectedArea) {
        editRole.addEventListener('change', () => toggleProtectedArea(editRole, editProtectedArea, 'edit-protected-area-field'));
    }

    const exportButton = document.getElementById('users-export-dropdown-btn');
    const exportDropdown = document.getElementById('users-export-dropdown');
    if (exportButton && exportDropdown) {
        exportButton.addEventListener('click', (event) => {
            event.stopPropagation();
            exportDropdown.classList.toggle('is-open');
        });

        document.addEventListener('click', (event) => {
            if (!exportDropdown.contains(event.target) && !exportButton.contains(event.target)) {
                exportDropdown.classList.remove('is-open');
            }
        });
    }

    window.exportUsers = (format) => {
        const baseUrl = routes[format];
        if (!baseUrl) return;

        const currentParams = new URLSearchParams(window.location.search);
        const query = currentParams.toString();
        const targetUrl = query ? `${baseUrl}?${query}` : baseUrl;

        if (format === 'print') {
            window.open(targetUrl, '_blank', 'noopener');
            return;
        }

        window.location.href = targetUrl;
    };
});

function showCreateUserModal() {
    showModal('create-user-modal', 'create-user-modal-content');
}

function hideCreateUserModal() {
    hideModal('create-user-modal', 'create-user-modal-content');
}

function formatUsernameForDisplay(username) {
    if (!username) return '—';
    if (username.startsWith('@') || username.includes('@')) return username;
    return `@${username}`;
}

function openUserViewModal(button) {
    document.getElementById('view-user-name').textContent = button.dataset.userName || '—';
    document.getElementById('view-user-email').textContent = button.dataset.userEmail || '—';
    document.getElementById('view-user-username').textContent = formatUsernameForDisplay(button.dataset.userUsername);
    document.getElementById('view-user-role').textContent = button.dataset.userRole === 'admin' ? 'Administrator' : 'Protected Area User';
    document.getElementById('view-user-protected-area').textContent = button.dataset.userProtectedArea || '—';
    document.getElementById('view-user-status').textContent = button.dataset.userStatus || '—';
    showModal('view-user-modal', 'view-user-modal-content');
}

function hideUserViewModal() {
    hideModal('view-user-modal', 'view-user-modal-content');
}

function openUserEditModal(button) {
    const form = document.getElementById('edit-user-form');
    const roleField = document.getElementById('edit-role');
    const protectedAreaField = document.getElementById('edit-protected-area');
    const protectedAreaWrap = document.getElementById('edit-protected-area-field');
    if (!form || !roleField || !protectedAreaField || !protectedAreaWrap) return;

    form.action = button.dataset.updateUrl || form.action;
    document.getElementById('edit-name').value = button.dataset.userName || '';
    document.getElementById('edit-email').value = button.dataset.userEmail || '';
    document.getElementById('edit-username').value = button.dataset.userUsername || '';
    document.getElementById('edit-password').value = '';
    roleField.value = button.dataset.userRole || 'admin';
    protectedAreaField.value = button.dataset.userProtectedAreaId || '';
    document.getElementById('edit-is-active').checked = button.dataset.userIsActive === '1';

    const isPaUser = roleField.value === 'pa_user';
    protectedAreaField.disabled = !isPaUser;
    protectedAreaWrap.classList.toggle('hidden', !isPaUser);
    if (!isPaUser) {
        protectedAreaField.value = '';
    }

    showModal('edit-user-modal', 'edit-user-modal-content');
}

function hideUserEditModal() {
    hideModal('edit-user-modal', 'edit-user-modal-content');
}

function openUserDeleteModal(button) {
    const form = document.getElementById('delete-user-form');
    const name = document.getElementById('delete-user-name');
    if (!form || !name) return;

    form.action = button.dataset.deleteUrl || form.action;
    name.textContent = button.dataset.userName || 'this user';
    showModal('delete-user-modal', 'delete-user-modal-content');
}

function hideUserDeleteModal() {
    hideModal('delete-user-modal', 'delete-user-modal-content');
}

function showModal(modalId, contentId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(contentId);
    if (!modal || !content) return;

    if (modal._userModalCloseTimer) {
        clearTimeout(modal._userModalCloseTimer);
        modal._userModalCloseTimer = null;
        modal.classList.remove('user-modal--closing');
    }

    modal.classList.remove('hidden');
    modal.classList.remove('user-modal--closing');
    void modal.offsetWidth;
    requestAnimationFrame(() => {
        modal.classList.add('user-modal--open');
    });
    document.body.style.overflow = 'hidden';
    if (typeof window.lucide !== 'undefined') window.lucide.createIcons();
}

function hideModal(modalId, contentId) {
    const modal = document.getElementById(modalId);
    const content = document.getElementById(contentId);
    if (!modal || !content) return;

    if (!modal.classList.contains('user-modal--open')) {
        return;
    }
    if (modal._userModalCloseTimer) {
        return;
    }

    modal.classList.remove('user-modal--open');
    modal.classList.add('user-modal--closing');

    modal._userModalCloseTimer = setTimeout(() => {
        modal._userModalCloseTimer = null;
        modal.classList.remove('user-modal--closing');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, USER_MODAL_ANIM_MS);
}

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    hideCreateUserModal();
    hideUserViewModal();
    hideUserEditModal();
    hideUserDeleteModal();

    const exportDropdown = document.getElementById('users-export-dropdown');
    if (exportDropdown) {
        exportDropdown.classList.remove('is-open');
    }
});

window.showCreateUserModal = showCreateUserModal;
window.hideCreateUserModal = hideCreateUserModal;
window.openUserViewModal = openUserViewModal;
window.hideUserViewModal = hideUserViewModal;
window.openUserEditModal = openUserEditModal;
window.hideUserEditModal = hideUserEditModal;
window.openUserDeleteModal = openUserDeleteModal;
window.hideUserDeleteModal = hideUserDeleteModal;
