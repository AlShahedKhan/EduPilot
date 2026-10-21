{{-- 
    Image Cropper Partial (Tenant)
    
    Usage:
        @include('partials.image-cropper', [
            'id' => 'admin-photo-cropper',
            'fileInput' => '#photo',
            'preview' => '.photo-preview',
            'aspectRatio' => 1,
            'outputWidth' => 300,
            'outputHeight' => 300,
            'outputPath' => 'assets/uploads/user-images',
            'cropRoute' => 'crop.image',
            'onSuccess' => 'function(response) { ... }',
        ])
    
    Parameters:
        - id: Unique identifier for this cropper instance (default: 'cropper')
        - fileInput: jQuery selector for the file input element
        - preview: jQuery selector for the preview image element (optional)
        - aspectRatio: Crop aspect ratio, NaN for freeform (default: NaN)
        - outputWidth: Output image width in pixels (optional)
        - outputHeight: Output image height in pixels (optional)
        - outputPath: Server path to save cropped image (default: 'assets/uploads/user-images')
        - quality: JPEG quality 1-100 (default: 90)
        - cropRoute: Route name for crop endpoint (default: 'crop.image')
        - onSuccess: JavaScript callback function on success (optional)
--}}

@php
    $cropperId = $id ?? 'cropper';
    $fileInputSelector = $fileInput ?? '#photo';
    $previewSelector = $preview ?? null;
    $aspectRatioVal = $aspectRatio ?? 'NaN';
    $outputWidthVal = $outputWidth ?? 'null';
    $outputHeightVal = $outputHeight ?? 'null';
    $outputPathVal = $outputPath ?? 'assets/uploads/user-images';
    $qualityVal = $quality ?? 90;
    $cropRouteName = $cropRoute ?? 'crop.image';
    $modalId = $cropperId . '-modal';
@endphp

<script src="{{ asset('assets/js/image-cropper.js') }}"></script>
<script>
    "use strict";

    $(function () {
        if (typeof ImageCropper === 'undefined') {
            console.error('[ImageCropper] Class not loaded for ' + '{{ $fileInputSelector }}');
            return;
        }

        new ImageCropper({
            fileInput: '{{ $fileInputSelector }}',
            modalId: '{{ $modalId }}',
            cropUrl: '{{ route($cropRouteName) }}',
            aspectRatio: {{ $aspectRatioVal }},
            outputWidth: {{ $outputWidthVal }},
            outputHeight: {{ $outputHeightVal }},
            outputPath: '{{ $outputPathVal }}',
            quality: {{ $qualityVal }},
            previewSelector: {{ $previewSelector ? "'" . json_encode($previewSelector) . "'" : 'null' }},
            @if(!empty($onBeforeCrop))
            onBeforeCrop: {!! $onBeforeCrop !!},
            @endif
            @if(!empty($onSuccess))
            onSuccess: {!! $onSuccess !!},
            @endif
        });
    });
</script>
