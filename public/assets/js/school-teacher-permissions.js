(function (window, document) {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        var page = document.querySelector("[data-permission-page]");

        if (!page) {
            return;
        }

        var classSelect = page.querySelector("[data-permission-class]");
        var sectionSelect = page.querySelector("[data-permission-section]");
        var permissionForm = page.querySelector("[data-permission-form]");
        var dirty = false;

        function replaceOptions(items, placeholder) {
            sectionSelect.replaceChildren();
            var empty = document.createElement("option");
            empty.value = "";
            empty.textContent = placeholder;
            sectionSelect.appendChild(empty);

            items.forEach(function (item) {
                var option = document.createElement("option");
                option.value = String(item.id);
                option.textContent = item.name;
                sectionSelect.appendChild(option);
            });
        }

        classSelect.addEventListener("change", function () {
            sectionSelect.disabled = true;

            if (!classSelect.value) {
                replaceOptions([], "Select section");
                return;
            }

            replaceOptions([], "Loading sections...");
            fetch(page.dataset.sectionsUrl.replace("__CLASS__", classSelect.value), {
                credentials: "same-origin",
                headers: { Accept: "application/json" },
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error("Unable to load sections");
                }

                return response.json();
            }).then(function (sections) {
                replaceOptions(sections, sections.length ? "Select section" : "No sections available");
                sectionSelect.disabled = sections.length === 0;
            }).catch(function () {
                replaceOptions([], "Unable to load sections");
            });
        });

        if (!permissionForm) {
            return;
        }

        function valueInput(teacherId, permission) {
            return permissionForm.querySelector('[data-permission-value="' + teacherId + ":" + permission + '"]');
        }

        function toggles(teacherId, permission) {
            return permissionForm.querySelectorAll('[data-permission-toggle][data-teacher-id="' + teacherId + '"][data-permission="' + permission + '"]');
        }

        function setPermission(teacherId, permission, checked, disabled) {
            var hidden = valueInput(teacherId, permission);

            if (hidden) {
                hidden.value = checked ? "1" : "0";
            }

            toggles(teacherId, permission).forEach(function (toggle) {
                toggle.checked = checked;
                if (typeof disabled === "boolean") {
                    toggle.disabled = disabled;
                }
            });
        }

        permissionForm.addEventListener("change", function (event) {
            var toggle = event.target.closest("[data-permission-toggle]");

            if (!toggle) {
                return;
            }

            var teacherId = toggle.dataset.teacherId;
            var permission = toggle.dataset.permission;
            setPermission(teacherId, permission, toggle.checked);

            if (permission === "class_access") {
                setPermission(teacherId, "marks", toggle.checked ? Boolean(valueInput(teacherId, "marks").value === "1") : false, !toggle.checked);
                setPermission(teacherId, "attendance", toggle.checked ? Boolean(valueInput(teacherId, "attendance").value === "1") : false, !toggle.checked);
            }

            dirty = true;
        });

        permissionForm.addEventListener("submit", function () {
            dirty = false;
        });

        window.addEventListener("beforeunload", function (event) {
            if (!dirty) {
                return;
            }

            event.preventDefault();
            event.returnValue = "";
        });
    });
})(window, document);
