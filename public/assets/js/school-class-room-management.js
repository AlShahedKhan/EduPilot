(() => {
    'use strict';

    const exportPage = (mode, pdfUrl) => {
        const printable = document.querySelector('[data-room-print-view]');
        if (!printable) return;

        if (mode === 'print') {
            window.print();
            return;
        }

        if (mode === 'pdf' && pdfUrl) window.location.assign(pdfUrl + window.location.search);
    };

    document.addEventListener('click', (event) => {
        const modalTrigger = event.target.closest('[data-room-open-modal]');
        if (modalTrigger && typeof window.rightModal === 'function') {
            window.rightModal(modalTrigger.dataset.url, modalTrigger.dataset.title);
        }

        const exportButton = event.target.closest('[data-room-export]');
        if (exportButton) exportPage(exportButton.dataset.roomExport, exportButton.dataset.roomPdfUrl);
    });

    document.addEventListener('submit', (event) => {
        const deleteForm = event.target.closest('[data-room-delete-form]');
        if (deleteForm && !window.confirm(`Delete ${deleteForm.dataset.name}? This cannot be undone.`)) {
            event.preventDefault();
        }
    }, true);
})();
