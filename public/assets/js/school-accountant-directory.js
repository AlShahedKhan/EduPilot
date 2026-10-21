(function (window, document, $) {
    "use strict";

    var pendingForm = null;

    function initializeAccountantForm(root) {
        var formRoot = root.matches && root.matches("[data-school-accountant-form]")
            ? root
            : root.querySelector && root.querySelector("[data-school-accountant-form]");

        if (!formRoot || formRoot.dataset.initialized === "true") {
            return;
        }

        formRoot.dataset.initialized = "true";

        if ($ && $.fn.select2) {
            $(formRoot).find(".js-accountant-select").each(function () {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({ dropdownParent: $(this).closest(".modal") });
                }
            });
        }

        if ($ && $.fn.daterangepicker) {
            $(formRoot).find(".js-accountant-date").each(function () {
                if (!$(this).data("daterangepicker")) {
                    $(this).daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        minYear: 1901,
                        maxDate: new Date(),
                        locale: { format: "MM/DD/YYYY" },
                    });
                }
            });
        }

        formRoot.querySelectorAll("[data-accountant-photo-input]").forEach(function (input, index) {
            if (input.dataset.cropInitialized === "true" || typeof window.ImageCropper === "undefined") {
                return;
            }

            input.dataset.cropInitialized = "true";
            var output = formRoot.querySelector(input.dataset.output);

            new window.ImageCropper({
                fileInput: "#" + input.id,
                cropUrl: formRoot.dataset.cropUrl,
                modalId: "schoolAccountantCropper-" + input.id + "-" + index,
                aspectRatio: 1,
                outputWidth: 300,
                outputHeight: 300,
                outputPath: "assets/uploads/user-images",
                maxFileSize: 2 * 1024 * 1024,
                allowedTypes: ["image/jpeg", "image/png", "image/webp"],
                previewSelector: input.dataset.preview,
                onSuccess: function (response) {
                    if (output) {
                        output.value = response.filename;
                    }
                },
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        var directory = document.querySelector("[data-accountant-directory]");

        document.querySelectorAll("[data-school-accountant-form]").forEach(initializeAccountantForm);

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initializeAccountantForm(node);
                    }
                });
            });
        }).observe(document.body, { childList: true, subtree: true });

        if (!directory) {
            return;
        }

        var confirmationElement = document.getElementById("accountantActionConfirmation");
        var confirmationModal = confirmationElement && window.bootstrap
            ? window.bootstrap.Modal.getOrCreateInstance(confirmationElement)
            : null;

        directory.addEventListener("click", function (event) {
            var modalButton = event.target.closest("[data-accountant-modal]");
            var confirmationButton = event.target.closest("[data-confirm-form]");
            var exportButton = event.target.closest("[data-accountant-export]");

            if (modalButton) {
                var modalFunction = modalButton.dataset.accountantModal === "large" ? window.largeModal : window.rightModal;

                if (typeof modalFunction === "function") {
                    modalFunction(modalButton.dataset.modalUrl, modalButton.dataset.modalTitle);
                }

                return;
            }

            if (confirmationButton) {
                pendingForm = document.getElementById(confirmationButton.dataset.confirmForm);
                confirmationElement.querySelector("[data-confirm-message]").textContent = confirmationButton.dataset.confirmText || "";

                if (confirmationModal) {
                    confirmationModal.show();
                }

                return;
            }

            if (!exportButton) {
                return;
            }

            var exportElement = document.getElementById("accountant-directory-export");

            if (exportButton.dataset.accountantExport === "print") {
                document.body.classList.add("is-printing-accountant-directory");
                window.print();
                return;
            }

            if (typeof window.html2pdf === "function") {
                var clone = exportElement.cloneNode(true);
                clone.removeAttribute("aria-hidden");
                clone.classList.add("is-exporting");
                window.html2pdf().set({
                    margin: 10,
                    filename: "school-accountants-" + new Date().toISOString().slice(0, 10) + ".pdf",
                    image: { type: "jpeg", quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
                }).from(clone).save();
            }
        });

        directory.querySelector("[data-confirm-submit]").addEventListener("click", function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        });

        window.addEventListener("afterprint", function () {
            document.body.classList.remove("is-printing-accountant-directory");
        });
    });
})(window, document, window.jQuery);
