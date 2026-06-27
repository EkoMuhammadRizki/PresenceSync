<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;

test('bulk delete siswa works and deletes associated users', function () {
    $user = User::factory()->create();

    // Create a Kelas
    $kelas = Kelas::create([
        'nama'       => 'X-1',
        'tingkat'    => 10,
        'status'     => 'aktif',
    ]);

    // Create students
    $studentUser1 = User::factory()->create(['email' => 's1@siswa.presencesync.sch.id']);
    $siswa1 = Siswa::create([
        'user_id'       => $studentUser1->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'Student One',
        'nisn'          => '1111111111',
        'nis'           => '11111',
        'jenis_kelamin' => 'L',
    ]);

    $studentUser2 = User::factory()->create(['email' => 's2@siswa.presencesync.sch.id']);
    $siswa2 = Siswa::create([
        'user_id'       => $studentUser2->id,
        'kelas_id'      => $kelas->id,
        'nama'          => 'Student Two',
        'nisn'          => '2222222222',
        'nis'           => '22222',
        'jenis_kelamin' => 'P',
    ]);

    $response = $this->actingAs($user)->post(route('bulk-delete'), [
        'type' => 'siswa',
        'ids'  => [$siswa1->id, $siswa2->id],
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // Assert students are deleted from database
    $this->assertDatabaseMissing('siswas', ['id' => $siswa1->id]);
    $this->assertDatabaseMissing('siswas', ['id' => $siswa2->id]);

    // Assert associated user accounts are deleted from database
    $this->assertDatabaseMissing('users', ['id' => $studentUser1->id]);
    $this->assertDatabaseMissing('users', ['id' => $studentUser2->id]);
});

test('bulk delete guru works', function () {
    $user = User::factory()->create();

    // Create gurus
    $guru1 = Guru::create([
        'nip'    => '123456789012345671',
        'nama'   => 'Guru One',
        'email'  => 'g1@sekolah.sch.id',
        'no_hp'  => '08123456781',
    ]);

    $guru2 = Guru::create([
        'nip'    => '123456789012345672',
        'nama'   => 'Guru Two',
        'email'  => 'g2@sekolah.sch.id',
        'no_hp'  => '08123456782',
    ]);

    $response = $this->actingAs($user)->post(route('bulk-delete'), [
        'type' => 'guru',
        'ids'  => [$guru1->id, $guru2->id],
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // Assert gurus are deleted from database
    $this->assertDatabaseMissing('gurus', ['id' => $guru1->id]);
    $this->assertDatabaseMissing('gurus', ['id' => $guru2->id]);
});

test('bulk delete validation fails with invalid parameters', function () {
    $user = User::factory()->create();

    // Missing fields
    $response1 = $this->actingAs($user)->post(route('bulk-delete'), []);
    $response1->assertStatus(302); // Redirect back with validation error (or 422 if AJAX is detected, but here standard post redirect)

    // Invalid type
    $response2 = $this->actingAs($user)->postJson(route('bulk-delete'), [
        'type' => 'invalid-type',
        'ids'  => [1, 2],
    ]);
    $response2->assertStatus(400);
    $response2->assertJson(['success' => false]);
});
