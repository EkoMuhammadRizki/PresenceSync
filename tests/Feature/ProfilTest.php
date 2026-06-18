<?php

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('siswa profile page can be loaded', function () {
    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Andi Wijaya',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($user)->get(route('profil-siswa.show', ['id' => $siswa->id]));
    $response->assertStatus(200);
    $response->assertSee('Andi Wijaya');
});

test('guru profile page can be loaded', function () {
    $user = User::factory()->create();
    $guru = Guru::create([
        'user_id' => $user->id,
        'nama'    => 'Siti Aminah',
        'nip'     => '987654',
        'email'   => 'siti@test.com',
    ]);

    $response = $this->actingAs($user)->get(route('profil-guru.show', ['id' => $guru->id]));
    $response->assertStatus(200);
    $response->assertSee('Siti Aminah');
});

test('siswa can only update permitted fields', function () {
    $user = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $user->id,
        'nama'          => 'Andi Wijaya',
        'jenis_kelamin' => 'L',
        'nis'           => '8888',
        'alamat'        => 'Old Address',
    ]);

    // Request update as the siswa
    $response = $this->actingAs($user)->put(route('profil-siswa.update', $siswa->id), [
        'nama'            => 'Hack Name',
        'nis'             => '9999',
        'alamat'          => 'New Address',
        'nama_orang_tua'  => 'Wali Andi',
        'no_hp'           => '0812345',
        'no_hp_orang_tua' => '0854321',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Assert that permitted fields are updated
    $this->assertDatabaseHas('siswas', [
        'id'              => $siswa->id,
        'alamat'          => 'New Address',
        'nama_orang_tua'  => 'Wali Andi',
        'no_hp'           => '0812345',
        'no_hp_orang_tua' => '0854321',
    ]);

    // Assert that restricted fields are NOT updated
    $this->assertDatabaseHas('siswas', [
        'id'   => $siswa->id,
        'nama' => 'Andi Wijaya',
        'nis'  => '8888',
    ]);
});

test('guru or admin can update all fields of a student', function () {
    // Create admin user and assign admin role
    $admin = User::factory()->create();
    
    // Set up roles
    \Spatie\Permission\Models\Role::create(['name' => 'admin']);
    $admin->assignRole('admin');

    $studentUser = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $studentUser->id,
        'nama'          => 'Andi Wijaya',
        'jenis_kelamin' => 'L',
        'nis'           => '8888',
    ]);

    $response = $this->actingAs($admin)->put(route('profil-siswa.update', $siswa->id), [
        'nama'            => 'Andi Baru',
        'nis'             => '9999',
        'jenis_kelamin'   => 'L',
        'alamat'          => 'New Address',
        'nama_orang_tua'  => 'Wali Baru',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Assert that all fields are updated
    $this->assertDatabaseHas('siswas', [
        'id'             => $siswa->id,
        'nama'           => 'Andi Baru',
        'nis'            => '9999',
        'alamat'         => 'New Address',
        'nama_orang_tua' => 'Wali Baru',
    ]);
});

test('siswa cannot update guru profile', function () {
    $studentUser = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $studentUser->id,
        'nama'          => 'Andi Wijaya',
        'jenis_kelamin' => 'L',
    ]);

    $guruUser = User::factory()->create();
    $guru = Guru::create([
        'user_id' => $guruUser->id,
        'nama'    => 'Siti Aminah',
        'nip'     => '987654',
        'email'   => 'siti@test.com',
    ]);

    $response = $this->actingAs($studentUser)->put(route('profil-guru.update', $guru->id), [
        'nama' => 'Siti Hack',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('gurus', [
        'id'   => $guru->id,
        'nama' => 'Siti Aminah',
    ]);
});
