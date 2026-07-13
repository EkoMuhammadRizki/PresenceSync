<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// 1. Generate Student template
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template Siswa');
$headers = ['email', 'password', 'kata_sandi', 'jenis_pengguna', 'nis', 'nama', 'id_fingerprint', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_hp', 'no_hp_orang_tua', 'status', 'kelas'];
foreach ($headers as $colIndex => $header) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
    $sheet->setCellValue($colLetter . '1', $header);
}
// Add a dummy row to pass the "empty file" check
$sheet->setCellValue('A2', 'dummy_student@demo.com');
$sheet->setCellValue('E2', '11111');
$sheet->setCellValue('F2', 'Dummy Student');

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/template_siswa.xlsx');

// 2. Generate Teacher template
$spreadsheet2 = new Spreadsheet();
$sheet2 = $spreadsheet2->getActiveSheet();
$sheet2->setTitle('Template Guru');
$headers2 = ['email', 'password', 'kata_sandi', 'jenis_pengguna', 'nip', 'nama', 'id_fingerprint', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_hp', 'no_hp_orang_tua', 'status', 'kelas'];
foreach ($headers2 as $colIndex => $header) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
    $sheet2->setCellValue($colLetter . '1', $header);
}
// Add a dummy row to pass the "empty file" check
$sheet2->setCellValue('A2', 'dummy_teacher@demo.com');
$sheet2->setCellValue('E2', '22222');
$sheet2->setCellValue('F2', 'Dummy Teacher');

$writer2 = new Xlsx($spreadsheet2);
$writer2->save(__DIR__ . '/template_guru.xlsx');

echo "TEMPLATES GENERATED SUCCESSFULLY\n";
