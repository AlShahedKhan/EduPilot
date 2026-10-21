(() => {
    'use strict';

    const exportPage = (mode, pdfUrl) => {
        const printable = document.querySelector('[data-subject-print-view]');
        if (!printable) return;
        if (mode === 'print') {
            window.print();
            return;
        }
        if (mode === 'pdf' && pdfUrl) window.location.assign(pdfUrl + window.location.search);
    };

    document.addEventListener('click', (event) => {
        const modalTrigger = event.target.closest('[data-subject-open-modal]');
        if (modalTrigger && typeof window.rightModal === 'function') {
            window.rightModal(modalTrigger.dataset.url, modalTrigger.dataset.title);
        }

        const exportButton = event.target.closest('[data-subject-export]');
        if (exportButton) exportPage(exportButton.dataset.subjectExport, exportButton.dataset.subjectPdfUrl);
    });

    document.addEventListener('submit', (event) => {
        const deleteForm = event.target.closest('[data-subject-delete-form]');
        if (deleteForm && !window.confirm(`Delete ${deleteForm.dataset.name}? This cannot be undone.`)) {
            event.preventDefault();
        }
    }, true);
})();
