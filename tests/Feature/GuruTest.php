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
