/**
 * Image Cropper Component
 * jQuery-compatible Cropper.js integration with server-side GD cropping
 *
 * Usage:
 *   var cropper = new ImageCropper({
 *       fileInput: '#photo-input',
 *       cropUrl: '/crop-image',
 *       aspectRatio: 1,
 *       outputWidth: 300,
 *       outputHeight: 300,
 *       previewSelector: '.photo-preview',
 *       onSuccess: function(response) { ... },
 *       onError: function(xhr) { ... }
 *   });
 */
(function (window, $) {
    "use strict";

    if (typeof $ === "undefined") {
        console.error("[ImageCropper] jQuery not loaded!");
        return;
    }

    function ImageCropper(options) {
        var defaults = {
            fileInput: null,
            cropUrl: "/crop-image",
            csrfToken: $('meta[name="csrf-token"]').attr("content"),
            aspectRatio: NaN,
            outputWidth: null,
            outputHeight: null,
            quality: 90,
            outputPath: "assets/uploads/user-images",
            previewSelector: null,
            modalId: "imageCropperModal",
            maxFileSize: 10 * 1024 * 1024,
            allowedTypes: [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/webp",
            ],
            onBeforeCrop: null,
            onSuccess: null,
            onError: null,
            onComplete: null,
        };

        var settings = $.extend({}, defaults, options);
        var cropperInstance = null;

        console.log("[ImageCropper] Initializing", settings);

        if (typeof window.Cropper === "undefined") {
            console.error(
                "[ImageCropper] Cropper.js not loaded! Make sure cropper.min.js is included.",
            );
            return;
        }

        // Create modal
        var modalId = settings.modalId;
        if ($("#" + modalId).length === 0) {
            var modalHtml = `
        <div class="modal fade sa-cropper-modal" id="${modalId}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    
                    <!-- Header -->
                    <div class="modal-header border-bottom bg-light px-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <svg class="text-primary" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h5 class="modal-title sa-cropper-title fw-semibold text-dark fs-5 mb-0">Crop & Edit Image</h5>
                        </div>
                        <button type="button" class="btn-close sa-cropper-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body sa-cropper-body p-4 bg-dark-subtle">
                        <div class="sa-cropper-wrapper position-relative rounded-3 overflow-hidden bg-black d-flex align-items-center justify-content-center" style="min-height: 380px; max-height: 60vh;">
                            <img id="${modalId}-image" src="" class="sa-cropper-img" style="max-width:100%;max-height:60vh;display:block;">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer sa-cropper-footer border-top bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <span class="d-none d-sm-inline">Drag handles to adjust selection</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary sa-cropper-cancel px-4 fw-medium" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary sa-cropper-save-btn px-4 fw-medium d-inline-flex align-items-center gap-2" id="${modalId}-save">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span>Apply Crop</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>`;

            $("body").append(modalHtml);
        }

        var $modalEl = $("#" + modalId);
        var $img = $("#" + modalId + "-image");
        var bsModal = null;

        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            bsModal = new bootstrap.Modal($modalEl[0]);
        } else {
            console.error("[ImageCropper] Bootstrap not loaded!");
        }

        // Bind file input change
        $(document)
            .off("change." + modalId)
            .on("change." + modalId, settings.fileInput, function (e) {
                var file = e.target.files[0];
                if (!file) return;

                console.log(
                    "[ImageCropper] File selected:",
                    file.name,
                    file.type,
                    file.size,
                );

                if (!settings.allowedTypes.includes(file.type)) {
                    if (typeof toastr !== "undefined")
                        toastr.error(
                            "Invalid file type. Please select an image.",
                        );
                    else alert("Invalid file type. Please select an image.");
                    return;
                }

                if (file.size > settings.maxFileSize) {
                    if (typeof toastr !== "undefined")
                        toastr.error("File size exceeds maximum limit.");
                    else alert("File size exceeds maximum limit.");
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (evt) {
                    $img.attr("src", evt.target.result);

                    var img = $img[0];
                    var initFn = function () {
                        if (cropperInstance) cropperInstance.destroy();

                        var opts = {
                            viewMode: 1,
                            dragMode: "move",
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                        };
                        if (!isNaN(settings.aspectRatio))
                            opts.aspectRatio = settings.aspectRatio;

                        try {
                            cropperInstance = new Cropper(img, opts);
                            console.log("[ImageCropper] Cropper initialized, complete:", img.complete, "naturalWidth:", img.naturalWidth);
                        } catch (e) {
                            console.error("[ImageCropper] Cropper init failed:", e);
                        }
                    };

                    // Attach listener BEFORE showing modal
                    $modalEl.one("shown.bs.modal", function () {
                        console.log("[ImageCropper] Modal shown, img.complete:", img.complete, "naturalWidth:", img.naturalWidth);
                        if (img.complete && img.naturalWidth > 0) {
                            initFn();
                        } else {
                            img.onload = initFn;
                        }
                    });

                    if (bsModal) {
                        bsModal.show();
                    } else {
                        $modalEl.modal("show");
                    }
                };
                reader.readAsDataURL(file);
            });

        // Bind save button
        $("#" + modalId + "-save")
            .off("click." + modalId)
            .on("click." + modalId, function () {
                console.log("[ImageCropper] Save clicked, cropperInstance:", !!cropperInstance);
                if (!cropperInstance) {
                    console.error("[ImageCropper] No cropper instance!");
                    if (typeof toastr !== "undefined") toastr.error("Crop not ready. Please wait.");
                    return;
                }

                var $btn = $(this);
                var $spinner = $btn.find(".spinner-border");
                $btn.prop("disabled", true);
                $spinner.removeClass("d-none");

                var canvas = cropperInstance.getCroppedCanvas();
                var data = cropperInstance.getData(true);

                canvas.toBlob(
                    function (blob) {
                        if (!blob) {
                            $btn.prop("disabled", false);
                            $spinner.addClass("d-none");
                            alert("Failed to process image");
                            return;
                        }

                        var formData = new FormData();
                        formData.append("image", blob, "crop.jpg");
                        formData.append("crop_x", Math.round(data.x));
                        formData.append("crop_y", Math.round(data.y));
                        formData.append("crop_width", Math.round(data.width));
                        formData.append("crop_height", Math.round(data.height));

                        if (settings.outputWidth)
                            formData.append(
                                "output_width",
                                settings.outputWidth,
                            );
                        if (settings.outputHeight)
                            formData.append(
                                "output_height",
                                settings.outputHeight,
                            );
                        formData.append("output_path", settings.outputPath);
                        formData.append("quality", settings.quality);
                        formData.append("_token", settings.csrfToken);

                        if (typeof settings.onBeforeCrop === "function") {
                            settings.onBeforeCrop(formData);
                        }

                        console.log(
                            "[ImageCropper] Sending crop request to:",
                            settings.cropUrl,
                        );

                        $.ajax({
                            url: settings.cropUrl,
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $btn.prop("disabled", false);
                                $spinner.addClass("d-none");
                                if (response.success) {
                                    if (typeof toastr !== "undefined")
                                        toastr.success(
                                            "Image cropped successfully!",
                                        );
                                    console.log(
                                        "[ImageCropper] Success:",
                                        response.filename,
                                    );

                                    if (settings.previewSelector) {
                                        $(settings.previewSelector).attr(
                                            "src",
                                            response.url,
                                        );
                                    }

                                    if (
                                        typeof settings.onSuccess === "function"
                                    ) {
                                        settings.onSuccess(response);
                                    }

                                    if (bsModal) bsModal.hide();
                                    else $modalEl.modal("hide");

                                    if (cropperInstance) {
                                        cropperInstance.destroy();
                                        cropperInstance = null;
                                    }
                                } else {
                                    if (typeof toastr !== "undefined")
                                        toastr.error(
                                            response.message || "Crop failed",
                                        );
                                }
                            },
                            error: function (xhr) {
                                $btn.prop("disabled", false);
                                $spinner.addClass("d-none");
                                var msg = "Something went wrong.";
                                if (
                                    xhr.responseJSON &&
                                    xhr.responseJSON.message
                                )
                                    msg = xhr.responseJSON.message;
                                if (typeof toastr !== "undefined")
                                    toastr.error(msg);
                                console.error(
                                    "[ImageCropper] AJAX error:",
                                    xhr,
                                );

                                if (typeof settings.onError === "function") {
                                    settings.onError(xhr);
                                }
                            },
                            complete: function () {
                                if (typeof settings.onComplete === "function") {
                                    settings.onComplete();
                                }
                            },
                        });
                    },
                    "image/jpeg",
                    settings.quality / 100,
                );
            });

        // Cleanup
        $modalEl.on("hidden.bs.modal", function () {
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
        });
    }

    // Expose globally
    window.ImageCropper = ImageCropper;
    console.log("[ImageCropper] Class registered");
})(window, jQuery);
