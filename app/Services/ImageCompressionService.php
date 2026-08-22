<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageCompressionService
{
    /**
     * Kompresi dan simpan foto avatar menjadi ukuran kecil (puluhan KB) dengan kualitas tinggi.
     *
     * @param UploadedFile $file
     * @param string $directory (contoh: 'avatars/siswa/123')
     * @param string $filename (default: 'avatar.jpg')
     * @param int $maxDimension (default: 600px)
     * @param int $quality (default: 75%)
     * @return string Relative storage path
     */
    public static function compressAndSaveAvatar(
        UploadedFile $file,
        string $directory,
        string $filename = 'avatar.jpg',
        int $maxDimension = 600,
        int $quality = 75
    ): string {
        $realPath = $file->getRealPath();
        if (empty($realPath) || !file_exists($realPath)) {
            $realPath = $file->getPathname();
        }

        $imageData = null;
        if (!empty($realPath) && file_exists($realPath)) {
            $imageData = @file_get_contents($realPath);
        }

        if (!$imageData) {
            // Fallback jika path tidak terbaca langsung
            $tempStream = $file->openFile('r');
            $imageData = $tempStream->fread($tempStream->getSize());
        }

        $srcImage = @imagecreatefromstring($imageData);

        if ($srcImage !== false) {
            // Auto-rotate foto jika ada orientation EXIF dari kamera HP
            if (function_exists('exif_read_data') && !empty($realPath) && file_exists($realPath)) {
                try {
                    $exif = @exif_read_data($realPath);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 8:
                                $srcImage = imagerotate($srcImage, 90, 0);
                                break;
                            case 3:
                                $srcImage = imagerotate($srcImage, 180, 0);
                                break;
                            case 6:
                                $srcImage = imagerotate($srcImage, -90, 0);
                                break;
                        }
                    }
                } catch (\Throwable $e) {
                    // Abaikan error EXIF
                }
            }

            $origWidth = imagesx($srcImage);
            $origHeight = imagesy($srcImage);

            // Resize proporsional jika dimensi melebihi batas
            $width = $origWidth;
            $height = $origHeight;

            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio = min($maxDimension / $width, $maxDimension / $height);
                $width = (int) round($width * $ratio);
                $height = (int) round($height * $ratio);
            }

            // Buat canvas baru truecolor
            $targetImage = imagecreatetruecolor($width, $height);

            // Background putih bersih untuk gambar dengan transparansi (PNG/WebP)
            $white = imagecolorallocate($targetImage, 255, 255, 255);
            imagefill($targetImage, 0, 0, $white);

            // Resample dengan interpolasi halus
            imagecopyresampled(
                $targetImage,
                $srcImage,
                0, 0, 0, 0,
                $width,
                $height,
                $origWidth,
                $origHeight
            );

            // Kompresi ke JPEG dengan kualitas optimal (~30KB - 70KB)
            ob_start();
            imagejpeg($targetImage, null, $quality);
            $compressedData = ob_get_clean();

            imagedestroy($srcImage);
            imagedestroy($targetImage);

            $storagePath = trim($directory, '/') . '/' . $filename;
            Storage::disk('public')->put($storagePath, $compressedData, 'public');

            return $storagePath;
        }

        // Fallback jika bukan image yang dikenali GD
        $storagePath = trim($directory, '/') . '/' . $filename;
        Storage::disk('public')->putFileAs(trim($directory, '/'), $file, $filename, 'public');
        return $storagePath;
    }
}
