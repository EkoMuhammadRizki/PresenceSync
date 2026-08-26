<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Kompresi dan simpan foto avatar menjadi ukuran kecil (puluhan KB) dengan nama file yang rapi & informatif.
     *
     * @param UploadedFile $file
     * @param string $category ('siswa', 'guru', 'admin')
     * @param string $identifier (NIS siswa, NIP guru, atau ID)
     * @param string $name (Nama lengkap pemilik profil)
     * @param string|null $oldAvatarPath (Path foto lama untuk dihapus otomatis)
     * @param int $maxDimension (default: 600px)
     * @param int $quality (default: 75%)
     * @return string Relative storage path (contoh: 'avatars/siswa/262710138_zavira-ilhami-maolida_1724328900.jpg')
     */
    public static function compressAndSaveNamedAvatar(
        UploadedFile $file,
        string $category,
        string $identifier,
        string $name,
        ?string $oldAvatarPath = null,
        int $maxDimension = 600,
        int $quality = 75
    ): string {
        // Bersihkan foto lama jika ada di storage
        if ($oldAvatarPath && Storage::disk('public')->exists($oldAvatarPath)) {
            Storage::disk('public')->delete($oldAvatarPath);
        }

        // Susun nama file yang bersih, rapi, dan mudah dicari admin
        $cleanIdentifier = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($identifier ?: 'user'));
        $slugName = Str::slug($name, '-');
        if (empty($slugName)) {
            $slugName = 'avatar';
        }
        $timestamp = time();
        $filename = "{$cleanIdentifier}_{$slugName}_{$timestamp}.jpg";
        $directory = "avatars/{$category}";

        $realPath = $file->getRealPath();
        if (empty($realPath) || !file_exists($realPath)) {
            $realPath = $file->getPathname();
        }

        $imageData = null;
        if (!empty($realPath) && file_exists($realPath)) {
            $imageData = @file_get_contents($realPath);
        }

        if (!$imageData) {
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

            // Center-crop ke rasio 1:1 (kotak) agar proporsi wajah tidak gepeng/terdistorsi
            $cropSize = min($origWidth, $origHeight);
            $srcX = (int) max(0, round(($origWidth - $cropSize) / 2));
            $srcY = (int) max(0, round(($origHeight - $cropSize) / 2));

            // Tentukan ukuran akhir (maksimal maxDimension)
            $targetSize = min($cropSize, $maxDimension);

            // Buat canvas baru truecolor kotak 1:1
            $targetImage = imagecreatetruecolor($targetSize, $targetSize);

            // Background putih bersih jika input memiliki transparansi
            $white = imagecolorallocate($targetImage, 255, 255, 255);
            imagefill($targetImage, 0, 0, $white);

            // Resample area tengah (center-cropped) dengan interpolasi halus
            imagecopyresampled(
                $targetImage,
                $srcImage,
                0,
                0,
                $srcX,
                $srcY,
                $targetSize,
                $targetSize,
                $cropSize,
                $cropSize
            );

            // Kompresi ke JPEG kualitas optimal (hanya puluhan KB)
            ob_start();
            imagejpeg($targetImage, null, $quality);
            $compressedData = ob_get_clean();

            imagedestroy($srcImage);
            imagedestroy($targetImage);

            $storagePath = "{$directory}/{$filename}";
            Storage::disk('public')->put($storagePath, $compressedData, 'public');

            return $storagePath;
        }

        // Fallback jika bukan image GD
        $storagePath = "{$directory}/{$filename}";
        Storage::disk('public')->putFileAs($directory, $file, $filename, 'public');
        return $storagePath;
    }
}
