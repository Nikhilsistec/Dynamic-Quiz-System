import './bootstrap';

// ── Toast notification system ─────────────────────────────────────────────────
window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');

    const icons = {
        success: `<svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        error: `<svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        warning: `<svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>`,
    };

    const colors = {
        success: 'bg-emerald-50 dark:bg-emerald-900/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200',
        error: 'bg-red-50 dark:bg-red-900/40 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
        warning: 'bg-amber-50 dark:bg-amber-900/40 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
    };

    toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg backdrop-blur-sm ${colors[type] || colors.success} transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `${icons[type] || icons.success}<span class="text-sm font-medium">${message}</span>`;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    });

    // Auto-dismiss after 3s
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

function createToastContainer() {
    const el = document.createElement('div');
    el.id = 'toast-container';
    el.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm w-full pointer-events-none';
    el.style.pointerEvents = 'none';
    document.body.appendChild(el);
    return el;
}

// ── Option management helpers (used in single_choice & multiple_choice partials) ──

window.csrfToken = () =>
    document.querySelector('meta[name=csrf-token]')?.content ?? '';

window.addOption = async function (storeUrl) {
    try {
        const fd = new FormData();
        fd.append('body', 'New option');
        fd.append('_token', window.csrfToken());
        const res  = await fetch(storeUrl, { method: 'POST', body: fd });
        const data = await res.json();

        const list = document.getElementById('options-list');
        const row  = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.dataset.optionId = data.id;
        row.innerHTML = buildOptionRow(data);
        list.appendChild(row);
    } catch (e) {
        showToast('Could not add option. Save the question first.', 'error');
    }
};

window.updateOption = async function (optionId, body, updateUrl) {
    const fd = new FormData();
    fd.append('body', body);
    fd.append('_token', window.csrfToken());
    fd.append('_method', 'PUT');
    try {
        const res = await fetch(updateUrl, { method: 'POST', body: fd });
        if (!res.ok) throw new Error('Save failed');
    } catch (e) {
        showToast('Failed to save option. Please try again.', 'error');
    }
};

window.markCorrect = async function (optionId, updateUrl, isCorrect = true) {
    const fd = new FormData();
    fd.append('is_correct', isCorrect ? '1' : '0');
    fd.append('_token', window.csrfToken());
    fd.append('_method', 'PUT');
    try {
        const res = await fetch(updateUrl, { method: 'POST', body: fd });
        if (!res.ok) throw new Error('Save failed');
    } catch (e) {
        showToast('Failed to update correct answer. Please try again.', 'error');
    }
};

window.deleteOption = async function (optionId, deleteUrl, rowEl) {
    const fd = new FormData();
    fd.append('_token', window.csrfToken());
    fd.append('_method', 'DELETE');
    const res = await fetch(deleteUrl, { method: 'POST', body: fd });
    if (res.ok) rowEl.remove();
};

function buildOptionRow(data) {
    return `
        <input type="checkbox" value="${data.id}"
               class="h-4 w-4 text-green-600 border-gray-300 correct-check"
               onchange="markCorrect(${data.id}, '${data.update_url}', this.checked)">
        <input type="text" value="${data.body ?? ''}" placeholder="Option text"
               class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm option-body"
               onblur="updateOption(${data.id}, this.value, '${data.update_url}')">
        <button type="button"
                onclick="deleteOption(${data.id}, '${data.delete_url}', this.closest('[data-option-id]'))"
                class="text-red-400 hover:text-red-600 text-xs px-2 py-1">✕</button>
    `;
}
