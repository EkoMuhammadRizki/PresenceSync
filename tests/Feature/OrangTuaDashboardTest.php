<?php

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Pengaduan;
use App\Models\ParentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function() {
    \Spatie\Permission\Models\Role::create(['name' => 'orang_tua']);
});

test('parent can login using child NIS and default password', function () {
    $parent = User::create([
        'first_name' => 'Bapak',
        'last_name' => 'Subarjo',
        'email' => 'ortu@demo.com',
        'password' => Hash::make('password123'),
    ]);
    $parent->assignRole('orang_tua');

    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);

    $siswa = Siswa::create([
        'user_id'           => User::factory()->create()->id, // child user
        'nama'              => 'Ahmad Anak',
        'jenis_kelamin'     => 'L',
        'is_sekretaris'     => false,
        'kelas_id'          => $kelas->id,
        'nis'               => '10201',
        'nama_orang_tua'    => $parent->name,
        'orang_tua_user_id' => $parent->id
    ]);

    // Attempt login using student NIS
    $response = $this->post(route('login'), [
        'email' => '10201', // child's NIS
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('orangtua.dashboard'));
});

test('parent dashboard renders and profile updates correctly', function () {
    $parent = User::create([
        'first_name' => 'Bapak',
        'last_name' => 'Subarjo',
        'email' => 'ortu@demo.com',
        'password' => Hash::make('password123'),
    ]);
    $parent->assignRole('orang_tua');

    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);

    $siswa = Siswa::create([
        'user_id'           => User::factory()->create()->id,
        'nama'              => 'Ahmad Anak',
        'jenis_kelamin'     => 'L',
        'is_sekretaris'     => false,
        'kelas_id'          => $kelas->id,
        'nis'               => '10201',
        'nama_orang_tua'    => 'Bapak Subarjo',
        'orang_tua_user_id' => $parent->id
    ]);

    // Test Login via Child's NIS
    $loginResponse = $this->post(route('login'), [
        'email' => '10201',
        'password' => 'password123',
    ]);
    $loginResponse->assertRedirect(route('orangtua.dashboard'));

    // Test Access Dashboard
    $response = $this->actingAs($parent)->get(route('orangtua.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Rekapitulasi Kehadiran Anak');
    $response->assertSee('Ahmad Anak');

    // Test Access Complaints Page
    $complaint = Pengaduan::create([
        'siswa_id' => $siswa->id,
        'tanggal' => '2026-07-17',
        'deskripsi' => 'Pengaduan orang tua tentang AC.',
    ]);
    $complaintsResponse = $this->actingAs($parent)->get(route('orangtua.pengaduan'));
    $complaintsResponse->assertStatus(200);
    $complaintsResponse->assertSee('Pengaduan orang tua tentang AC.');

    // Test Update Profile
    $profileResponse = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'nik_ayah' => '3671072212710001',
        'nama_ayah' => 'Bapak Subarjo',
        'pekerjaan_ayah' => 'Wiraswasta',
        'nik_ibu' => '3671072212710002',
        'nama_ibu' => 'Ibu Subarjo',
    ]);
    $profileResponse->assertRedirect();

    // Verify database
    $this->assertDatabaseHas('parent_profiles', [
        'parent_user_id' => $parent->id,
        'nik_ayah' => '3671072212710001',
        'nik_ibu' => '3671072212710002',
    ]);
});

test('parent profile validation rules are enforced', function () {
    $parent = User::create([
        'first_name' => 'Bapak',
        'last_name' => 'Subarjo',
        'email' => 'ortu2@demo.com',
        'password' => Hash::make('password123'),
    ]);
    $parent->assignRole('orang_tua');

    $kelas = Kelas::create([
        'nama' => 'X-1',
        'tingkat' => '10',
        'status' => 'aktif',
    ]);

    $siswa = Siswa::create([
        'user_id'           => User::factory()->create()->id,
        'nama'              => 'Anak Dua',
        'jenis_kelamin'     => 'L',
        'is_sekretaris'     => false,
        'kelas_id'          => $kelas->id,
        'nis'               => '10202',
        'nama_orang_tua'    => 'Bapak Subarjo',
        'orang_tua_user_id' => $parent->id
    ]);

    // 1. NIK with alphabet should fail
    $response = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'nik_ayah' => '123456789012345A', // has letters
    ]);
    $response->assertSessionHasErrors(['nik_ayah']);

    // 2. NIK with 17 digits should fail
    $response = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'nik_ayah' => '12345678901234567', // 17 digits
    ]);
    $response->assertSessionHasErrors(['nik_ayah']);

    // 3. No HP with 14 digits should fail
    $response = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'no_hp_ayah' => '08123456789012', // 14 digits
    ]);
    $response->assertSessionHasErrors(['no_hp_ayah']);

    // 4. Alamat with 501 characters should fail
    $response = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'alamat_ayah' => str_repeat('a', 501), // 501 chars
    ]);
    $response->assertSessionHasErrors(['alamat_ayah']);

    // 5. Nama with numbers should fail
    $response = $this->actingAs($parent)->post(route('orangtua.profil.update'), [
        'nama_ayah' => 'Budi 123',
    ]);
    $response->assertSessionHasErrors(['nama_ayah']);
});
