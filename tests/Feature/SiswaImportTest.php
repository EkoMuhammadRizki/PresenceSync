<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

test('download template works', function () {
    $user = User::factory()->create();

    // Create a Kelas and a student
    $kelas = Kelas::create([
        'nama'    => 'X-1',
        'tingkat' => 10,
        'status'  => 'aktif',
    ]);
    
    $studentUser = User::factory()->create([
        'email' => 'myteststudent@siswa.presencesync.sch.id'
    ]);
    Siswa::create([
        'user_id'       => $studentUser->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'My Test Student Name',
        'nis'           => '99887',
        'jenis_kelamin' => 'L',
    ]);

    // 1. Check populated download (default)
    $response = $this->actingAs($user)->get(route('siswa.download-template'));
    $response->assertStatus(200);

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();
    $tempFile = tempnam(sys_get_temp_dir(), 'excel_download_test');
    file_put_contents($tempFile, $content);
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
    $rows = $spreadsheet->getActiveSheet()->toArray();
    unlink($tempFile);

    // Verify student info exists in output rows
    $found = false;
    foreach ($rows as $row) {
        if (($row[0] ?? '') === 'myteststudent@siswa.presencesync.sch.id' && ($row[5] ?? '') === 'My Test Student Name') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // 2. Check empty download (?empty=1)
    $responseEmpty = $this->actingAs($user)->get(route('siswa.download-template', ['empty' => 1]));
    $responseEmpty->assertStatus(200);

    ob_start();
    $responseEmpty->sendContent();
    $contentEmpty = ob_get_clean();
    $tempFileEmpty = tempnam(sys_get_temp_dir(), 'excel_download_test_empty');
    file_put_contents($tempFileEmpty, $contentEmpty);
    $spreadsheetEmpty = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFileEmpty);
    $rowsEmpty = $spreadsheetEmpty->getActiveSheet()->toArray();
    unlink($tempFileEmpty);

    expect(count($rowsEmpty))->toBe(1);
    expect($rowsEmpty[0][0])->toBe('email');
    expect($rowsEmpty[0][5])->toBe('nama');
});

test('import students from excel works', function () {
    $user = User::factory()->create();

    // Create a Kelas
    $kelas = Kelas::create([
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

test('import student rejects guru template', function () {
    $user = User::factory()->create();

    // Create a temporary Teacher template Excel file
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Guru');
    $sheet->setCellValue('A1', 'email');
    $sheet->setCellValue('B1', 'password');
    $sheet->setCellValue('C1', 'kata_sandi');
    $sheet->setCellValue('D1', 'jenis_pengguna');
    $sheet->setCellValue('E1', 'nip');
    $sheet->setCellValue('F1', 'nama');
    
    // Add a dummy data row to bypass the empty check
    $sheet->setCellValue('A2', 'teacher@demo.com');
    $sheet->setCellValue('E2', '19920317');
    $sheet->setCellValue('F2', 'Teacher Name');

    $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test_guru_on_siswa');
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    $uploadedFile = new UploadedFile(
        $tempFile,
        'template_import_guru.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($user)->post(route('siswa.import'), [
        'file' => $uploadedFile,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['error']);
    expect(session('errors')->first('error'))->toContain('template Guru');

    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});

test('import teacher rejects student template', function () {
    $user = User::factory()->create();

    // Create a temporary Student template Excel file
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Siswa');
    $sheet->setCellValue('A1', 'email');
    $sheet->setCellValue('B1', 'password');
    $sheet->setCellValue('C1', 'kata_sandi');
    $sheet->setCellValue('D1', 'jenis_pengguna');
    $sheet->setCellValue('E1', 'nis');
    $sheet->setCellValue('F1', 'nama');

    // Add a dummy data row to bypass the empty check
    $sheet->setCellValue('A2', 'student@demo.com');
    $sheet->setCellValue('E2', '12345');
    $sheet->setCellValue('F2', 'Student Name');

    $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test_siswa_on_guru');
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    $uploadedFile = new UploadedFile(
        $tempFile,
        'template_import_siswa.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $response = $this->actingAs($user)->post(route('guru.import'), [
        'file' => $uploadedFile,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['error']);
    expect(session('errors')->first('error'))->toContain('template Siswa');

    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});
