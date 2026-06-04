<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

test('download template works', function () {
    $user = User::factory()->create();

    // Access route as authenticated user
    $response = $this->actingAs($user)->get(route('siswa.download-template'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Disposition', 'attachment; filename="template_import_siswa.xlsx"');
});

test('import students from excel works', function () {
    $user = User::factory()->create();

    // Create a Jurusan
    $jurusan = Jurusan::create([
        'kode' => 'IPA',
        'nama' => 'Ilmu Pengetahuan Alam',
    ]);

    // Create a Kelas
    $kelas = Kelas::create([
        'jurusan_id' => $jurusan->id,
        'nama'       => 'X-1',
        'tingkat'    => 10,
        'status'     => 'aktif',
    ]);

    // Create a temporary Excel file
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nama');
    $sheet->setCellValue('B1', 'NISN');
    $sheet->setCellValue('C1', 'NIS');
    $sheet->setCellValue('D1', 'Kelas');
    $sheet->setCellValue('E1', 'Jenis Kelamin (L/P)');
    $sheet->setCellValue('F1', 'Tanggal Lahir (YYYY-MM-DD)');
    $sheet->setCellValue('G1', 'Alamat');

    // Row 2: new student
    $sheet->setCellValue('A2', 'Test Student');
    $sheet->setCellValue('B2', '1234567890');
    $sheet->setCellValue('C2', '98765');
    $sheet->setCellValue('D2', 'X-1');
    $sheet->setCellValue('E2', 'L');
    $sheet->setCellValue('F2', '2009-08-15');
    $sheet->setCellValue('G2', 'Test Address');

    // Row 3: duplicate student (we will insert this to database beforehand)
    $sheet->setCellValue('A3', 'Duplicate Student');
    $sheet->setCellValue('B3', '0054321098');
    $sheet->setCellValue('C3', '10201');
    $sheet->setCellValue('D3', 'X-1');
    $sheet->setCellValue('E3', 'P');
    $sheet->setCellValue('F3', '2009-08-15');
    $sheet->setCellValue('G3', 'Dup Address');

    // Pre-insert the duplicate student to database
    $dupUser = User::factory()->create([
        'email' => 'duplicate.student@siswa.presencesync.sch.id'
    ]);
    Siswa::create([
        'user_id'       => $dupUser->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'Duplicate Student',
        'nisn'          => '0054321098',
        'nis'           => '10201',
        'jenis_kelamin' => 'P',
        'tanggal_lahir' => '2009-08-15',
        'alamat'        => 'Dup Address',
    ]);

    $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test');
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    // Create UploadedFile
    $uploadedFile = new UploadedFile(
        $tempFile,
        'test_import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($user)->post(route('siswa.import'), [
        'file' => $uploadedFile,
    ]);

    $response->assertRedirect(route('siswa.index'));
    $response->assertSessionHas('import_success');

    $importSuccess = session('import_success');
    expect($importSuccess['success_count'])->toBe(1);
    expect($importSuccess['skip_count'])->toBe(1);

    // Verify student is inserted
    $this->assertDatabaseHas('siswas', [
        'nama'          => 'Test Student',
        'nisn'          => '1234567890',
        'nis'           => '98765',
        'kelas_id'      => $kelas->id,
        'jenis_kelamin' => 'L',
    ]);

    // Cleanup temp file
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});
