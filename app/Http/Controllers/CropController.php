<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CropController extends Controller
{
    /**
     * Crop an uploaded image using GD library.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function crop(Request $request): JsonResponse
    {
        $request->validate([
            'image'             => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'crop_x'            => 'required|numeric|min:0',
            'crop_y'            => 'required|numeric|min:0',
            'crop_width'        => 'required|numeric|min:1',
            'crop_height'       => 'required|numeric|min:1',
            'output_width'      => 'nullable|integer|min:1|max:2000',
            'output_height'     => 'nullable|integer|min:1|max:2000',
            'output_path'       => ['nullable', Rule::in(['assets/uploads/user-images', 'assets/uploads/logo'])],
            'quality'           => 'nullable|integer|min:1|max:100',
        ]);

        $file       = $request->file('image');
        $cropX      = (int) $request->input('crop_x');
        $cropY      = (int) $request->input('crop_y');
        $cropWidth  = (int) $request->input('crop_width');
        $cropHeight = (int) $request->input('crop_height');
        $outWidth   = (int) ($request->input('output_width') ?? $cropWidth);
        $outHeight  = (int) ($request->input('output_height') ?? $cropHeight);
        $quality   = (int) ($request->input('quality') ?? 90);
        $outputPath = $request->input('output_path', 'assets/uploads/user-images');

        $sourcePath = $file->getRealPath();
        $sourceMime = $file->getMimeType();

        $srcImage = $this->createGdImage($sourcePath, $sourceMime);
        if ($srcImage === false) {
            return response()->json(['success' => false, 'message' => 'Failed to read source image.'], 422);
        }

        $cropped = imagecreatetruecolor($outWidth, $outHeight);
        if ($cropped === false) {
            imagedestroy($srcImage);
            return response()->json(['success' => false, 'message' => 'Failed to create cropped image canvas.'], 422);
        }

        // Preserve transparency for PNG and GIF
        if (in_array($sourceMime, ['image/png', 'image/gif'], true)) {
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);
            $transparent = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
            imagefilledrectangle($cropped, 0, 0, $outWidth - 1, $outHeight - 1, $transparent);
        }

        imagecopyresampled(
            $cropped,
            $srcImage,
            0, 0,
            $cropX, $cropY,
            $outWidth, $outHeight,
            $cropWidth, $cropHeight
        );

        $extension = $this->getExtension($sourceMime);
        $filename  = time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $fullDir   = public_path($outputPath);

        if (! is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }

        $fullPath = $fullDir . '/' . $filename;
        $saved    = $this->saveGdImage($cropped, $fullPath, $sourceMime, $quality);

        imagedestroy($srcImage);
        imagedestroy($cropped);

        if (! $saved) {
            return response()->json(['success' => false, 'message' => 'Failed to save cropped image.'], 500);
        }

        $relativePath = $outputPath . '/' . $filename;

        return response()->json([
            'success'  => true,
            'filename' => $filename,
            'path'     => $relativePath,
            'url'      => asset($relativePath),
        ]);
    }

    /**
     * Create a GD image resource from a file.
     *
     * @return \GdImage|false
     */
    private function createGdImage(string $path, string $mime): \GdImage|false
    {
        return match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/gif'  => imagecreatefromgif($path),
            'image/webp' => imagecreatefromwebp($path),
            default      => false,
        };
    }

    /**
     * Save a GD image resource to disk.
     */
    private function saveGdImage(\GdImage $image, string $path, string $mime, int $quality): bool
    {
        return match ($mime) {
            'image/jpeg' => imagejpeg($image, $path, $quality),
            'image/png'  => imagepng($image, $path, (int) round((100 - $quality) / 10)),
            'image/gif'  => imagegif($image, $path),
            'image/webp' => imagewebp($image, $path, $quality),
            default      => false,
        };
    }

    /**
     * Get file extension from MIME type.
     */
    private function getExtension(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };
    }
}
