(() => {
    'use strict';

    const page = document.querySelector('[data-academic-page]');
    if (!page) return;

    const exportPage = (button) => {
        if (button.dataset.export === 'print') {
            window.print();
            return;
        }
        if (button.dataset.export === 'pdf' && button.dataset.pdfUrl) {
            window.location.assign(button.dataset.pdfUrl + window.location.search);
        }
    };

    const loadDependentOptions = async (select) => {
        const target = document.querySelector(select.dataset.target);
        if (!target) return;
        target.innerHTML = `<option value="">${target.dataset.placeholder || 'Select'}</option>`;
        if (!select.value) return;
        const response = await fetch(select.dataset.url.replace('__CLASS__', select.value), { headers: { Accept: 'application/json' } });
        if (!response.ok) return;
        const rows = await response.json();
        rows.forEach((row) => target.add(new Option(row.name, row.id)));
    };

    document.addEventListener('click', (event) => {
        const modal = event.target.closest('[data-academic-modal]');
        if (modal && typeof window.rightModal === 'function') window.rightModal(modal.dataset.url, modal.dataset.title);
        const exportButton = event.target.closest('[data-academic-export]');
        if (exportButton) exportPage(exportButton);
    });

    document.addEventListener('change', (event) => {
        const select = event.target.closest('[data-dependent-class]');
        if (select) loadDependentOptions(select);
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-academic-delete]');
        if (form && !window.confirm(`Delete ${form.dataset.name}? This cannot be undone.`)) event.preventDefault();
    }, true);
})();
