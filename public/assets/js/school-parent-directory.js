(function (window, document, $) {
    "use strict";

    var pendingForm = null;

    function initializeParentForm(root) {
        var formRoot = root.matches && root.matches("[data-school-parent-form]")
            ? root
            : root.querySelector && root.querySelector("[data-school-parent-form]");

        if (!formRoot || formRoot.dataset.initialized === "true") {
            return;
        }

        formRoot.dataset.initialized = "true";

        if ($ && $.fn.select2) {
            $(formRoot).find(".js-parent-select").each(function () {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({ dropdownParent: $(this).closest(".modal") });
                }
            });
        }

        if ($ && $.fn.daterangepicker) {
            $(formRoot).find(".js-parent-date").each(function () {
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

        formRoot.querySelectorAll("[data-parent-photo-input]").forEach(function (input, index) {
            if (input.dataset.cropInitialized === "true" || typeof window.ImageCropper === "undefined") {
                return;
            }

            input.dataset.cropInitialized = "true";
            var output = formRoot.querySelector(input.dataset.output);

            new window.ImageCropper({
                fileInput: "#" + input.id,
                cropUrl: formRoot.dataset.cropUrl,
                modalId: "schoolParentCropper-" + input.id + "-" + index,
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
        var directory = document.querySelector("[data-parent-directory]");

        document.querySelectorAll("[data-school-parent-form]").forEach(initializeParentForm);

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initializeParentForm(node);
                    }
                });
            });
        }).observe(document.body, { childList: true, subtree: true });

        if (!directory) {
            return;
        }

        var confirmationElement = document.getElementById("parentActionConfirmation");
        var confirmationModal = confirmationElement && window.bootstrap
            ? window.bootstrap.Modal.getOrCreateInstance(confirmationElement)
            : null;

        directory.addEventListener("click", function (event) {
            var modalButton = event.target.closest("[data-parent-modal]");
            var confirmationButton = event.target.closest("[data-confirm-form]");
            var exportButton = event.target.closest("[data-parent-export]");

            if (modalButton) {
                var modalFunction = modalButton.dataset.parentModal === "large" ? window.largeModal : window.rightModal;

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

            var exportElement = document.getElementById("parent-directory-export");

            if (exportButton.dataset.parentExport === "print") {
                document.body.classList.add("is-printing-parent-directory");
                window.print();
                return;
            }

            if (typeof window.html2pdf === "function") {
                var clone = exportElement.cloneNode(true);
                clone.removeAttribute("aria-hidden");
                clone.classList.add("is-exporting");
                window.html2pdf().set({
                    margin: 10,
                    filename: "school-parents-" + new Date().toISOString().slice(0, 10) + ".pdf",
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
            document.body.classList.remove("is-printing-parent-directory");
        });
    });
})(window, document, window.jQuery);

(function (document) {
    "use strict";

    function setOptions(select, items, placeholder) {
        select.replaceChildren();
        var emptyOption = document.createElement("option");
        emptyOption.value = "";
        emptyOption.textContent = placeholder;
        select.appendChild(emptyOption);

        items.forEach(function (item) {
            var option = document.createElement("option");
            option.value = String(item.id);
            option.textContent = item.name + (item.email ? " (" + item.email + ")" : "");
            select.appendChild(option);
        });
    }

    function initializeChildAssignment(form) {
        if (!form || form.dataset.assignmentInitialized === "true") {
            return;
        }

        form.dataset.assignmentInitialized = "true";
        var classSelect = form.querySelector("[data-parent-class]");
        var sectionSelect = form.querySelector("[data-parent-section]");
        var studentSelect = form.querySelector("[data-parent-student]");
        var addButton = form.querySelector("[data-parent-add-child]");
        var selectedChildren = form.querySelector("[data-parent-selected-children]");
        var feedback = form.querySelector("[data-parent-child-feedback]");

        function showFeedback(message, isError) {
            feedback.textContent = message;
            feedback.classList.toggle("text-danger", Boolean(isError));
        }

        function requestJson(url) {
            return fetch(url, { credentials: "same-origin", headers: { Accept: "application/json" } }).then(function (response) {
                if (!response.ok) {
                    throw new Error("Request failed");
                }

                return response.json();
            });
        }

        classSelect.addEventListener("change", function () {
            setOptions(sectionSelect, [], "Loading sections...");
            setOptions(studentSelect, [], "Select student");
            sectionSelect.disabled = true;
            studentSelect.disabled = true;
            addButton.disabled = true;

            if (!classSelect.value) {
                setOptions(sectionSelect, [], "Select section");
                return;
            }

            requestJson(form.dataset.sectionsUrl.replace("__CLASS__", classSelect.value)).then(function (sections) {
                setOptions(sectionSelect, sections, sections.length ? "Select section" : "No sections available");
                sectionSelect.disabled = sections.length === 0;
                showFeedback("");
            }).catch(function () {
                setOptions(sectionSelect, [], "Unable to load sections");
                showFeedback("Sections could not be loaded. Please try again.", true);
            });
        });

        sectionSelect.addEventListener("change", function () {
            setOptions(studentSelect, [], "Loading students...");
            studentSelect.disabled = true;
            addButton.disabled = true;

            if (!sectionSelect.value) {
                setOptions(studentSelect, [], "Select student");
                return;
            }

            var url = form.dataset.studentsUrl
                .replace("__CLASS__", classSelect.value)
                .replace("__SECTION__", sectionSelect.value);

            if (form.dataset.parentId) {
                url += (url.indexOf("?") === -1 ? "?" : "&") + "parent_id=" + encodeURIComponent(form.dataset.parentId);
            }

            requestJson(url).then(function (students) {
                setOptions(studentSelect, students, students.length ? "Select student" : "No eligible students");
                studentSelect.disabled = students.length === 0;
                addButton.disabled = students.length === 0;
                showFeedback(students.length ? "" : "No eligible students are available in this section.");
            }).catch(function () {
                setOptions(studentSelect, [], "Unable to load students");
                showFeedback("Students could not be loaded. Please try again.", true);
            });
        });

        addButton.addEventListener("click", function () {
            var option = studentSelect.options[studentSelect.selectedIndex];

            if (!option || !option.value) {
                showFeedback("Select a student first.", true);
                return;
            }

            if (selectedChildren.querySelector('[data-child-id="' + CSS.escape(option.value) + '"]')) {
                showFeedback("That student is already selected.", true);
                return;
            }

            var chip = document.createElement("span");
            chip.className = "school-parent-form__chip";
            chip.dataset.childId = option.value;
            chip.append(document.createTextNode(option.textContent));
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "child_ids[]";
            input.value = option.value;
            chip.appendChild(input);
            var remove = document.createElement("button");
            remove.type = "button";
            remove.dataset.parentRemoveChild = "";
            remove.setAttribute("aria-label", "Remove " + option.textContent);
            remove.innerHTML = '<i class="bi bi-x" aria-hidden="true"></i>';
            chip.appendChild(remove);
            selectedChildren.appendChild(chip);
            studentSelect.value = "";
            showFeedback("Student added.");
        });

        selectedChildren.addEventListener("click", function (event) {
            var remove = event.target.closest("[data-parent-remove-child]");

            if (remove) {
                remove.closest("[data-child-id]").remove();
                showFeedback("Student removed from the selection.");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-school-parent-form]").forEach(initializeChildAssignment);
    });
})(document);
