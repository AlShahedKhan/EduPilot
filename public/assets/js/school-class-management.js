(() => {
    'use strict';

    const openModal = (trigger) => {
        if (typeof window.rightModal === 'function') {
            window.rightModal(trigger.dataset.url, trigger.dataset.title);
        }
    };

    const reindexSections = (form) => {
        form.querySelectorAll('[data-section-row]').forEach((row, index) => {
            row.querySelectorAll('input').forEach((input) => {
                if (input.name.includes('[id]')) {
                    input.name = `sections[${index}][id]`;
                } else if (input.name.includes('[name]')) {
                    input.name = `sections[${index}][name]`;
                }
            });
        });
    };

    const showNotice = (form, message) => {
        const notice = form.querySelector('[data-section-notice]');
        if (!notice) return;
        notice.textContent = message;
        notice.hidden = message === '';
    };

    const removeSection = (button) => {
        const form = button.closest('[data-class-form]');
        const rows = form.querySelectorAll('[data-section-row]');

        if (rows.length <= 1) {
            showNotice(form, 'Every class must retain at least one section.');
            return;
        }

        const row = button.closest('[data-section-row]');
        const sectionId = row.dataset.sectionId;
        const removedContainer = form.querySelector('[data-removed-sections]');

        if (sectionId && !window.confirm('Remove this section when the class is saved?')) {
            return;
        }

        if (sectionId && removedContainer) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'removed_section_ids[]';
            hidden.value = sectionId;
            removedContainer.append(hidden);
        }

        row.remove();
        reindexSections(form);
        showNotice(form, '');
    };

    const addSection = (button) => {
        const form = button.closest('[data-class-form]');
        const list = form.querySelector('[data-section-list]');
        const template = form.querySelector('[data-section-template]');
        const index = list.querySelectorAll('[data-section-row]').length;

        list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index)));
        list.querySelector('[data-section-row]:last-child input[name$="[name]"]').focus();
        showNotice(form, '');
    };

    const validateSectionNames = (event) => {
        const form = event.target.closest('[data-class-form]');
        if (!form) return;

        const names = Array.from(form.querySelectorAll('[data-section-row] input[name$="[name]"]'))
            .map((input) => input.value.trim().toLocaleLowerCase());
        const uniqueNames = new Set(names);

        if (names.some((name) => name === '') || uniqueNames.size !== names.length) {
            event.preventDefault();
            showNotice(form, 'Every section needs a unique name.');
        }
    };

    const exportPage = (mode, pdfUrl) => {
        const printable = document.querySelector('[data-class-print-view]');
        if (!printable) return;

        if (mode === 'print') {
            window.print();
            return;
        }

        if (mode === 'pdf' && pdfUrl) window.location.assign(pdfUrl + window.location.search);
    };

    document.addEventListener('click', (event) => {
        const modalTrigger = event.target.closest('[data-class-open-modal]');
        if (modalTrigger) openModal(modalTrigger);

        const addButton = event.target.closest('[data-section-add]');
        if (addButton) addSection(addButton);

        const removeButton = event.target.closest('[data-section-remove]');
        if (removeButton) removeSection(removeButton);

        const exportButton = event.target.closest('[data-class-export]');
        if (exportButton) exportPage(exportButton.dataset.classExport, exportButton.dataset.classPdfUrl);
    });

    document.addEventListener('submit', (event) => {
        const deleteForm = event.target.closest('[data-class-delete-form]');
        if (deleteForm && !window.confirm(`Delete ${deleteForm.dataset.name}? This cannot be undone.`)) {
            event.preventDefault();
            return;
        }

        validateSectionNames(event);
    }, true);
})();
