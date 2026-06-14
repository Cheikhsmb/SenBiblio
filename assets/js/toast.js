(() => {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        const flashData = document.body.dataset.flash;
        if (!flashData || flashData === 'null') return;

        try {
            const flash = JSON.parse(flashData);
            if (!flash || !flash.text) return;

            const container = document.querySelector('.toast-container');
            if (!container) return;

            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${flash.type === 'danger' ? 'danger' : flash.type === 'warning' ? 'warning' : 'success'} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${flash.text}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                </div>
            `;
            container.appendChild(toastEl);

            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        } catch (e) {
            console.warn('Flash message parse error:', e);
        }
    });
})();