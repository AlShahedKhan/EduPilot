@extends('superadmin.navigation')

@section('content')
    <div class="sa-logo">
        <div class="sa-logo-head">
            <h4>{{ get_phrase('Logo & Favicon') }}</h4>
            <div class="sa-bc">
                <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a> / {{ get_phrase('Settings') }} /
                {{ get_phrase('Logo & Favicon') }}
            </div>
        </div>

        @if (session('success'))
            <div class="sa-logo-alert sa-logo-alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                    <path d="M22 4L12 14.01l-3-3" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="sa-logo-grid">
            {{-- Logo Card --}}
            <div class="sa-logo-card">
                <div class="sa-logo-card-head">
                    <div class="sa-logo-card-icon ic-logo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="M21 15l-5-5L5 21" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="sa-logo-card-title">{{ get_phrase('Sidebar Logo') }}</h5>
                        <p class="sa-logo-card-sub">{{ get_phrase('Displayed in sidebar & top bar') }}</p>
                    </div>
                </div>
                <div class="sa-logo-card-body">
                    <div class="sa-logo-preview">
                        <img src="{{ asset('assets/uploads/logo/' . $logo) }}" alt="Current Logo" class="logo-preview-img"
                            style="width: 200px; height: auto;">
                    </div>
                    <form method="POST" action="{{ route('superadmin.settings.logo.update') }}"
                        enctype="multipart/form-data" id="logo-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="cropped_logo" id="cropped_logo" value="">
                        <label class="sa-logo-upload">
                            <input type="file" name="logo" accept="image/*" id="logo-input">
                            <div class="sa-logo-upload-box">
                                <div class="sa-logo-upload-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                        <polyline points="17,8 12,3 7,8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="sa-logo-upload-text">{{ get_phrase('Click to upload or drag & drop') }}
                                    </div>
                                    <div class="sa-logo-upload-hint">{{ get_phrase('PNG, JPG, SVG, WEBP — Max 2MB') }}
                                    </div>
                                </div>
                            </div>
                        </label>
                        @error('logo')
                            <div style="color:#dc2626;font-size:11px;margin-top:6px;">{{ $message }}</div>
                        @enderror
                        <div class="sa-logo-submit">
                            <button type="submit" class="sa-logo-btn sa-logo-btn-primary" id="logo-submit-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                    <polyline points="17,8 12,3 7,8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                {{ get_phrase('Upload Logo') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Favicon Card --}}
            <div class="sa-logo-card">
                <div class="sa-logo-card-head">
                    <div class="sa-logo-card-icon ic-fav">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="sa-logo-card-title">{{ get_phrase('Favicon') }}</h5>
                        <p class="sa-logo-card-sub">{{ get_phrase('Browser tab icon') }}</p>
                    </div>
                </div>
                <div class="sa-logo-card-body">
                    <div class="sa-logo-preview sa-logo-preview-favicon">
                        <img src="{{ asset('assets/uploads/logo/' . $favicon) }}" alt="Current Favicon"
                            class="favicon-preview-img" style="width: 64px; height: 64px;">
                    </div>
                    <form method="POST" action="{{ route('superadmin.settings.favicon.update') }}"
                        enctype="multipart/form-data" id="favicon-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="cropped_favicon" id="cropped_favicon" value="">
                        <label class="sa-logo-upload">
                            <input type="file" name="favicon" accept="image/*" id="favicon-input">
                            <div class="sa-logo-upload-box">
                                <div class="sa-logo-upload-icon" style="background:#fef3c7;color:#d97706;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                        <polyline points="17,8 12,3 7,8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="sa-logo-upload-text">{{ get_phrase('Click to upload or drag & drop') }}
                                    </div>
                                    <div class="sa-logo-upload-hint">{{ get_phrase('PNG, ICO, SVG — Max 1MB') }}</div>
                                </div>
                            </div>
                        </label>
                        @error('favicon')
                            <div style="color:#dc2626;font-size:11px;margin-top:6px;">{{ $message }}</div>
                        @enderror
                        <div class="sa-logo-submit">
                            <button type="submit" class="sa-logo-btn sa-logo-btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                    <polyline points="17,8 12,3 7,8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                {{ get_phrase('Upload Favicon') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Cropper Inline Script --}}
    <script>
        "use strict";

        window.setupImageCropper = function(inputId, modalId, imageId, saveBtnId, previewSelector, croppedInputId,
            aspectRatio, options) {
            options = options || {};

            var cropperInstance = null;

            console.log('[ImageCropper] Setup for input:', inputId, 'modal:', modalId);

            // Check dependencies
            if (typeof $ === 'undefined') {
                console.error('[ImageCropper] jQuery not loaded!');
                return;
            }
            if (typeof Cropper === 'undefined') {
                console.error('[ImageCropper] Cropper.js not loaded!');
                return;
            }
            if (typeof bootstrap === 'undefined') {
                console.error('[ImageCropper] Bootstrap not loaded!');
                return;
            }

            // Create modal if it doesn't exist
            if ($('#' + modalId).length === 0) {
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
                            <img id="${imageId}" src="" class="sa-cropper-img" style="max-width:100%;max-height:60vh;display:block;">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer sa-cropper-footer border-top bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <span class="d-none d-sm-inline">Drag handles to adjust selection</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary sa-cropper-cancel px-4 fw-medium" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary sa-cropper-save-btn px-4 fw-medium d-inline-flex align-items-center gap-2" id="${saveBtnId}">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span>Apply Crop</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>`;

                $('body').append(modalHtml);
            }

            var $modalEl = $('#' + modalId);
            var $img = $('#' + imageId);
            var bsModal = new bootstrap.Modal($modalEl[0]);

            console.log('[ImageCropper] Modal created:', modalId);

            // Bind file input change
            $('#' + inputId).off('change.sa-cropper').on('change.sa-cropper', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                console.log('[ImageCropper] File selected:', inputId, file.name);

                var reader = new FileReader();
                reader.onload = function(evt) {
                    $img.attr('src', evt.target.result);

                    var img = $img[0];
                    var initFn = function() {
                        if (cropperInstance) cropperInstance.destroy();
                        var cropperOpts = {
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                        };
                        if (!isNaN(aspectRatio)) cropperOpts.aspectRatio = aspectRatio;

                        try {
                            cropperInstance = new Cropper(img, cropperOpts);
                            console.log('[ImageCropper] Cropper initialized for:', inputId);
                        } catch (e) {
                            console.error('[ImageCropper] Cropper init failed:', e);
                        }
                    };

                    // Attach listener BEFORE showing modal
                    $modalEl.one('shown.bs.modal', function() {
                        console.log('[ImageCropper] Modal shown for:', inputId, 'complete:', img.complete, 'naturalWidth:', img.naturalWidth);
                        if (img.complete && img.naturalWidth > 0) {
                            initFn();
                        } else {
                            img.onload = initFn;
                        }
                    });

                    bsModal.show();
                };
                reader.readAsDataURL(file);
            });

            // Bind save button
            $('#' + saveBtnId).off('click.sa-cropper').on('click.sa-cropper', function() {
                if (!cropperInstance) {
                    console.error('[ImageCropper] No cropper instance!');
                    return;
                }

                var $btn = $(this);
                var $spinner = $btn.find('.spinner-border');
                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                var canvas = cropperInstance.getCroppedCanvas();
                var data = cropperInstance.getData(true);

                canvas.toBlob(function(blob) {
                    if (!blob) {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        alert('Failed to process image');
                        return;
                    }

                    var formData = new FormData();
                    formData.append('image', blob, 'crop.jpg');
                    formData.append('crop_x', Math.round(data.x));
                    formData.append('crop_y', Math.round(data.y));
                    formData.append('crop_width', Math.round(data.width));
                    formData.append('crop_height', Math.round(data.height));

                    if (options.outputWidth) formData.append('output_width', options.outputWidth);
                    if (options.outputHeight) formData.append('output_height', options.outputHeight);
                    formData.append('output_path', options.outputPath || 'assets/uploads/logo');
                    formData.append('quality', options.quality || 90);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    if (typeof options.onBeforeCrop === 'function') {
                        options.onBeforeCrop(formData);
                    }

                    $.ajax({
                        url: options.cropUrl || '{{ route('superadmin.crop.image') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $btn.prop('disabled', false);
                            $spinner.addClass('d-none');
                            if (response.success) {
                                $('#' + croppedInputId).val(response.filename);
                                if (previewSelector) $(previewSelector).attr('src', response
                                    .url);
                                if (typeof toastr !== 'undefined') toastr.success(
                                    'Image cropped successfully!');
                                console.log('[ImageCropper] Success:', response.filename,
                                    'hidden input:', $('#' + croppedInputId).val());

                                // Call onSuccess callback if provided (for auto-submit)
                                if (options.onSuccess && typeof options.onSuccess ===
                                    'function') {
                                    options.onSuccess(response);
                                }
                                bsModal.hide();
                            } else {
                                if (typeof toastr !== 'undefined') toastr.error(response
                                    .message || 'Crop failed');
                            }
                        },
                        error: function(xhr) {
                            $btn.prop('disabled', false);
                            $spinner.addClass('d-none');
                            var msg = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr
                                .responseJSON.message;
                            if (typeof toastr !== 'undefined') toastr.error(msg);
                            console.error('[ImageCropper] AJAX error:', xhr);
                        }
                    });
                }, 'image/jpeg', (options.quality || 90) / 100);
            });

            // Cleanup on modal hide
            $modalEl.on('hidden.bs.modal', function() {
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
            });
        };

        // Initialize when document is ready
        $(function() {
            console.log('[ImageCropper] Document ready, initializing...');

            // Setup logo cropper
            window.setupImageCropper(
                'logo-input',
                'logo-cropper-modal',
                'logo-cropper-image',
                'logo-crop-save',
                '.logo-preview-img',
                'cropped_logo',
                3, {
                    outputWidth: 300,
                    outputHeight: 100,
                    outputPath: 'assets/uploads/logo',
                    cropUrl: '{{ route('superadmin.crop.image') }}',
                    onSuccess: function(response) {
                        console.log('[SA Cropper] Logo cropped, auto-submitting form...');
                        $('#logo-form').submit();
                    }
                }
            );

            // Setup favicon cropper
            window.setupImageCropper(
                'favicon-input',
                'favicon-cropper-modal',
                'favicon-cropper-image',
                'favicon-crop-save',
                '.favicon-preview-img',
                'cropped_favicon',
                1, {
                    outputWidth: 64,
                    outputHeight: 64,
                    outputPath: 'assets/uploads/logo',
                    cropUrl: '{{ route('superadmin.crop.image') }}',
                    onSuccess: function(response) {
                        console.log('[SA Cropper] Favicon cropped, auto-submitting form...');
                        $('#favicon-form').submit();
                    }
                }
            );
        });

        // Form submit handlers
        $(function() {
            $('#logo-form').on('submit', function(e) {
                var croppedLogo = $('#cropped_logo').val();
                console.log('[SA Cropper] Logo form submit, cropped_logo:', croppedLogo);
                if (!croppedLogo) {
                    console.warn('[SA Cropper] No cropped logo - form will submit normally');
                }
            });

            $('#favicon-form').on('submit', function(e) {
                var croppedFavicon = $('#cropped_favicon').val();
                console.log('[SA Cropper] Favicon form submit, cropped_favicon:', croppedFavicon);
                if (!croppedFavicon) {
                    console.warn('[SA Cropper] No cropped favicon - form will submit normally');
                }
            });
        });
    </script>
@endsection
