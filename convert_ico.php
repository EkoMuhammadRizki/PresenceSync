<?php

$pngPath = 'c:/File Eko/PresenceSync/logo/Siap_Logo.png';
$icoPath = 'c:/File Eko/PresenceSync/logo/Siap_Logo.ico';

if (!file_exists($pngPath)) {
    die("File PNG tidak ditemukan: $pngPath\n");
}

$srcImage = imagecreatefrompng($pngPath);
if (!$srcImage) {
    die("Gagal membaca file PNG\n");
}

imagealphablending($srcImage, false);
imagesavealpha($srcImage, true);

$sizes = [256, 128, 64, 48, 32, 16];
$imagesData = [];

foreach ($sizes as $size) {
    $resized = imagecreatetruecolor($size, $size);
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefilledrectangle($resized, 0, 0, $size, $size, $transparent);
    
    imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $size, $size, imagesx($srcImage), imagesy($srcImage));
    
    ob_start();
    imagepng($resized);
    $pngData = ob_get_clean();
    imagedestroy($resized);
    
    $imagesData[] = [
        'width' => $size == 256 ? 0 : $size,
        'height' => $size == 256 ? 0 : $size,
        'data' => $pngData,
        'size' => strlen($pngData),
    ];
}

imagedestroy($srcImage);

// Tulis header ICO
$count = count($imagesData);
$icoHeader = pack('vvv', 0, 1, $count);

$offset = 6 + ($count * 16);
$dirEntries = '';
$dataSection = '';

foreach ($imagesData as $img) {
    $dirEntries .= pack('CCCCvvVV',
        $img['width'],
        $img['height'],
        0, // color count
        0, // reserved
        1, // color planes
        32, // bit depth
        $img['size'],
        $offset
    );
    $dataSection .= $img['data'];
    $offset += $img['size'];
}

$icoContent = $icoHeader . $dirEntries . $dataSection;
file_put_contents($icoPath, $icoContent);

echo "Berhasil membuat file ICO: $icoPath (Ukuran: " . round(filesize($icoPath)/1024, 2) . " KB)\n";
