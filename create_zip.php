<?php

$destZip = 'c:/File Eko/PresenceSync/patch_aturan_jam.zip';

if (file_exists($destZip)) {
    unlink($destZip);
}

$zip = new ZipArchive();
if ($zip->open($destZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Gagal membuat file ZIP\n");
}

$baseDir = 'c:/File Eko/PresenceSync/';

$files = [
    'app/Http/Controllers/Absensi/BulkDeleteController.php',
    'app/Http/Controllers/Absensi/GuruController.php',
    'app/Http/Controllers/Absensi/KelasController.php',
    'app/Http/Controllers/Absensi/PembagianKelasController.php',
    'app/Http/Controllers/Absensi/SiswaController.php',
    'app/Models/Guru.php',
    'resources/views/base/base.blade.php',
    'resources/views/pages/absensi/guru.blade.php',
    'resources/views/pages/absensi/kelas-data.blade.php',
    'resources/views/pages/absensi/pembagian-kelas.blade.php',
    'resources/views/pages/absensi/pembagian-kelas-detail.blade.php',
    'resources/views/pages/absensi/siswa.blade.php',
    'resources/views/profile/partials/profile-header.blade.php',
    'resources/views/profile/partials/profile-info-card.blade.php',
    'resources/views/profile/siswa.blade.php',
    'resources/views/pages/absensi/dashboard.blade.php',
    'resources/views/auth/layout.blade.php',
    'database/migrations/2026_08_21_000001_add_details_to_gurus_table.php',
];

foreach ($files as $file) {
    $fullPath = $baseDir . $file;
    if (file_exists($fullPath)) {
        $zip->addFile($fullPath, $file);
        echo "Menambahkan: $file\n";
    } else {
        echo "File tidak ditemukan: $fullPath\n";
    }
}

$zip->close();

echo "\nBERHASIL: File ZIP berhasil dibuat di: $destZip\n";
echo "Ukuran file: " . round(filesize($destZip) / 1024, 2) . " KB\n";
