<?php

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createKelasWithDependencies(): Kelas
{
    $user = User::factory()->create();
    $jurusan = Jurusan::create(['nama' => 'IPA', 'kode' => 'IPA']);
    $guru = Guru::create(['nama' => 'Budi Santoso', 'nip' => '1234', 'email' => 'budi@test.com', 'user_id' => $user->id]);
    return Kelas::create([
        'jurusan_id' => $jurusan->id,
        'guru_id'    => $guru->id,
        'nama'       => 'X-1',
        'tingkat'    => '10',
        'status'     => 'aktif',
    ]);
}

test('pembagian kelas index shows classes with student count', function () {
    $admin = User::factory()->create();
    $kelas = createKelasWithDependencies();

    // Create 3 students in the class
    for ($i = 1; $i <= 3; $i++) {
        $u = User::factory()->create();
        Siswa::create([
            'user_id'       => $u->id,
            'kelas_id'      => $kelas->id,
            'nama'          => 'Siswa ' . $i,
            'jenis_kelamin' => 'L',
        ]);
    }

    $response = $this->actingAs($admin)->get(route('pembagian-kelas.index'));
    $response->assertStatus(200);
    $response->assertSee('X-1');
    $response->assertSee('3 siswa');
});

test('pembagian kelas show displays students in a class', function () {
    $admin = User::factory()->create();
    $kelas = createKelasWithDependencies();

    $u = User::factory()->create();
    Siswa::create([
        'user_id'       => $u->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'Ahmad Subarjo',
        'nis'           => '10201',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($admin)->get(route('pembagian-kelas.show', $kelas->id));
    $response->assertStatus(200);
    $response->assertSee('Ahmad Subarjo');
    $response->assertSee('10201');
});

test('add siswa to kelas via pembagian kelas', function () {
    $admin = User::factory()->create();
    $kelas = createKelasWithDependencies();

    // Create a student without a class
    $u = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $u->id,
        'kelas_id'      => null,
        'nama'          => 'Bela Puspita',
        'jenis_kelamin' => 'P',
    ]);

    $response = $this->actingAs($admin)->post(route('pembagian-kelas.add-siswa', $kelas->id), [
        'siswa_ids' => [$siswa->id],
    ]);

    $response->assertRedirect(route('pembagian-kelas.show', $kelas->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('siswas', [
        'id'       => $siswa->id,
        'kelas_id' => $kelas->id,
    ]);
});

test('remove siswa from kelas via pembagian kelas', function () {
    $admin = User::factory()->create();
    $kelas = createKelasWithDependencies();

    $u = User::factory()->create();
    $siswa = Siswa::create([
        'user_id'       => $u->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'Candra Wijaya',
        'jenis_kelamin' => 'L',
    ]);

    $response = $this->actingAs($admin)->delete(route('pembagian-kelas.remove-siswa', [$kelas->id, $siswa->id]));

    $response->assertRedirect(route('pembagian-kelas.show', $kelas->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('siswas', [
        'id'       => $siswa->id,
        'kelas_id' => null,
    ]);
});

test('add siswa fails with no selection', function () {
    $admin = User::factory()->create();
    $kelas = createKelasWithDependencies();

    $response = $this->actingAs($admin)->post(route('pembagian-kelas.add-siswa', $kelas->id), [
        'siswa_ids' => [],
    ]);

    $response->assertSessionHasErrors('siswa_ids');
});
