<?php

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store creates a guru and an associated user', function () {
    // Authenticate as a user (e.g. admin)
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->post(route('guru.store'), [
        'email'    => 'test.guru@sekolah.sch.id',
        'password' => 'password123',
        'nama'     => 'Test Guru, S.Pd',
        'nip'      => '1234567890',
        'no_hp'    => '081234567890',
        'alamat'   => 'Jl. Test No. 12',
    ]);

    $response->assertRedirect(route('guru.index'));
    $response->assertSessionHas('success', 'Data guru berhasil ditambahkan.');

    // Assert user created
    $this->assertDatabaseHas('users', [
        'email' => 'test.guru@sekolah.sch.id',
        'first_name' => 'Test',
        'last_name' => 'Guru, S.Pd',
    ]);

    $user = User::where('email', 'test.guru@sekolah.sch.id')->first();

    // Assert guru created and linked
    $this->assertDatabaseHas('gurus', [
        'user_id' => $user->id,
        'nama'    => 'Test Guru, S.Pd',
        'nip'     => '1234567890',
        'email'   => 'test.guru@sekolah.sch.id',
    ]);
});

test('update modifies guru and user information', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $user = User::create([
        'first_name' => 'Old',
        'last_name'  => 'Name',
        'email'      => 'old.guru@sekolah.sch.id',
        'password'   => bcrypt('password123'),
    ]);

    $guru = Guru::create([
        'user_id' => $user->id,
        'nama'    => 'Old Name',
        'nip'     => '11111',
        'email'   => 'old.guru@sekolah.sch.id',
    ]);

    $response = $this->put(route('guru.update', $guru->id), [
        'nama'   => 'New Name',
        'nip'    => '22222',
        'email'  => 'new.guru@sekolah.sch.id',
        'no_hp'  => '089999999',
        'alamat' => 'New Address',
    ]);

    $response->assertRedirect(route('guru.index'));
    $response->assertSessionHas('success', 'Data guru berhasil diperbarui.');

    // Assert Guru updated
    $this->assertDatabaseHas('gurus', [
        'id'     => $guru->id,
        'nama'   => 'New Name',
        'nip'    => '22222',
        'email'  => 'new.guru@sekolah.sch.id',
    ]);

    // Assert User updated
    $this->assertDatabaseHas('users', [
        'id'         => $user->id,
        'first_name' => 'New',
        'last_name'  => 'Name',
        'email'      => 'new.guru@sekolah.sch.id',
    ]);
});

test('destroy deletes guru and user', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $user = User::create([
        'first_name' => 'Guru',
        'last_name'  => 'Delete',
        'email'      => 'delete.guru@sekolah.sch.id',
        'password'   => bcrypt('password123'),
    ]);

    $guru = Guru::create([
        'user_id' => $user->id,
        'nama'    => 'Guru Delete',
        'nip'     => '33333',
        'email'   => 'delete.guru@sekolah.sch.id',
    ]);

    $response = $this->delete(route('guru.destroy', $guru->id));

    $response->assertRedirect(route('guru.index'));
    $response->assertSessionHas('success', 'Data guru berhasil dihapus.');

    $this->assertDatabaseMissing('gurus', ['id' => $guru->id]);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('download template works and lists existing gurus', function () {
    $admin = User::factory()->create();
    
    // Create an existing teacher
    $user = User::factory()->create([
        'email' => 'mytestteacher@sekolah.sch.id'
    ]);
    Guru::create([
        'user_id' => $user->id,
        'nama'    => 'My Test Teacher Name',
        'nip'     => '998877',
        'email'   => 'mytestteacher@sekolah.sch.id',
    ]);

    // 1. Check populated download (default)
    $response = $this->actingAs($admin)->get(route('guru.download-template'));
    $response->assertStatus(200);
    
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();
    $tempFile = tempnam(sys_get_temp_dir(), 'excel_download_test');
    file_put_contents($tempFile, $content);
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);
    $rows = $spreadsheet->getActiveSheet()->toArray();
    unlink($tempFile);

    // Verify existing teacher info exists in output rows
    $found = false;
    foreach ($rows as $row) {
        if (($row[0] ?? '') === 'mytestteacher@sekolah.sch.id' && ($row[5] ?? '') === 'My Test Teacher Name') {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();

    // 2. Check empty download (?empty=1)
    $responseEmpty = $this->actingAs($admin)->get(route('guru.download-template', ['empty' => 1]));
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

test('destroy deletes guru with related kelas and mata_pelajarn by setting guru_id to null', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    $user = User::create([
        'first_name' => 'Guru',
        'last_name'  => 'Relasi',
        'email'      => 'relasi.guru@sekolah.sch.id',
        'password'   => bcrypt('password123'),
    ]);

    $guru = Guru::create([
        'user_id' => $user->id,
        'nama'    => 'Guru Relasi',
        'nip'     => '44444',
        'email'   => 'relasi.guru@sekolah.sch.id',
    ]);

    $kelas = App\Models\Kelas::create([
        'guru_id' => $guru->id,
        'nama'    => 'X-RPL-1',
        'tingkat' => 10,
        'status'  => 'aktif',
    ]);

    $mp = App\Models\MataPelajaran::create([
        'guru_id' => $guru->id,
        'nama'    => 'Matematika',
        'kode'    => 'MTK-10',
    ]);

    $response = $this->delete(route('guru.destroy', $guru->id));

    $response->assertRedirect(route('guru.index'));
    $response->assertSessionHas('success', 'Data guru berhasil dihapus.');

    $this->assertDatabaseMissing('gurus', ['id' => $guru->id]);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);

    $this->assertDatabaseHas('kelas', [
        'id' => $kelas->id,
        'guru_id' => null,
    ]);

    $this->assertDatabaseHas('mata_pelajarans', [
        'id' => $mp->id,
        'guru_id' => null,
    ]);
});

