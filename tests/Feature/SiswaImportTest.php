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
    $sheet->setCellValue('A1', 'email');
    $sheet->setCellValue('B1', 'password');
    $sheet->setCellValue('C1', 'kata_sandi');
    $sheet->setCellValue('D1', 'jenis_pengguna');
    $sheet->setCellValue('E1', 'nis');
    $sheet->setCellValue('F1', 'nama');
    $sheet->setCellValue('G1', 'id_fingerprint');
    $sheet->setCellValue('H1', 'jenis_kelamin');
    $sheet->setCellValue('I1', 'tempat_lahir');
    $sheet->setCellValue('J1', 'tanggal_lahir');
    $sheet->setCellValue('K1', 'alamat');
    $sheet->setCellValue('L1', 'no_hp');
    $sheet->setCellValue('M1', 'no_hp_orang_tua');
    $sheet->setCellValue('N1', 'status');
    $sheet->setCellValue('O1', 'kelas');

    // Row 2: new student
    $sheet->setCellValue('A2', 'test.student@siswa.presencesync.sch.id');
    $sheet->setCellValue('B2', 'password123');
    $sheet->setCellValue('C2', 'password123');
    $sheet->setCellValue('D2', 'siswa');
    $sheet->setCellValue('E2', '98765');
    $sheet->setCellValue('F2', 'Test Student');
    $sheet->setCellValue('G2', 'FP002');
    $sheet->setCellValue('H2', 'L');
    $sheet->setCellValue('I2', 'Bandung');
    $sheet->setCellValue('J2', '2009-08-15');
    $sheet->setCellValue('K2', 'Test Address');
    $sheet->setCellValue('L2', '081234567890');
    $sheet->setCellValue('M2', '081298765432');
    $sheet->setCellValue('N2', 'aktif');
    $sheet->setCellValue('O2', 'X-1');

    // Row 3: duplicate student (we will insert this to database beforehand)
    $sheet->setCellValue('A3', 'duplicate.student@siswa.presencesync.sch.id');
    $sheet->setCellValue('B3', 'password123');
    $sheet->setCellValue('C3', 'password123');
    $sheet->setCellValue('D3', 'siswa');
    $sheet->setCellValue('E3', '10201');
    $sheet->setCellValue('F3', 'Duplicate Student');
    $sheet->setCellValue('G3', 'FP001');
    $sheet->setCellValue('H3', 'P');
    $sheet->setCellValue('I3', 'Jakarta');
    $sheet->setCellValue('J3', '2009-08-15');
    $sheet->setCellValue('K3', 'Dup Address');
    $sheet->setCellValue('L3', '081234567890');
    $sheet->setCellValue('M3', '081298765432');
    $sheet->setCellValue('N3', 'aktif');
    $sheet->setCellValue('O3', 'X-1');

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
        'nis'           => '98765',
        'kelas_id'      => $kelas->id,
        'jenis_kelamin' => 'L',
    ]);

    // Cleanup temp file
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});
