document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.getElementById('public-notice-dialog');
    const dialogContent = document.getElementById('public-notice-dialog-content');

    if (!dialog || !dialogContent) {
        return;
    }

    let openingButton = null;

    document.querySelectorAll('.public-notice-open').forEach((button) => {
        button.addEventListener('click', () => {
            const template = document.getElementById(button.dataset.noticeTemplate);

            if (!(template instanceof HTMLTemplateElement)) {
                return;
            }

            openingButton = button;
            dialogContent.replaceChildren(template.content.cloneNode(true));
            document.body.classList.add('overflow-hidden');
            dialog.showModal();
            dialog.querySelector('.public-notice-close')?.focus();
        });
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog || event.target.closest('.public-notice-close')) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => {
        document.body.classList.remove('overflow-hidden');
        openingButton?.focus();
    });
});
